<?php

namespace app\models;

use function GuzzleHttp\Psr7\str;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Json;
use yii\db\Exception;
class Express extends ActiveRecord
{


    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'express';
    }

    /**
     * @param $num
     * @return array|null|ActiveRecord
     */
    public  static  function  findByWayBillNumber($num)
    {
        $data=self::find()
            ->where(['waybillnumber'=>$num])
            ->one();
        return $data;
    }
    /**
     * 添加快递单号
     * @param $sku
     * @param $waybillname
     * @param $waybillnumber
     * @param $order_no
     * @return int
     */
    public function Expressadd($waybillnumber,$waybillname,$sku,$order_no){
        $express=self::find()->select('waybillnumber')->where(['sku'=>$sku,'order_no'=>$order_no])->one();
        if ($express){
            return false;
        }
        $create_time=time();
        $res=Yii::$app->db->createCommand()->insert('express',[
            'sku'    => $sku,
            'order_no' =>$order_no,
            'waybillname'      =>$waybillname,
            'waybillnumber'  =>$waybillnumber,
            'create_time'=>$create_time,
        ])->execute();
        return $res;

    }


    /**
     * 修改快递单号
     * @param $waybillnumber
     * @param $waybillname
     * @param $sku
     * @param $order_no
     * @return int
     */
    public static function Expressupdate($waybillnumber,$waybillname,$sku,$order_no){
        $tran = Yii::$app->db->beginTransaction();
        try{
            $express=self::find()
                ->where(['order_no'=>$order_no,'sku'=>$sku])
                ->one();
            if (!$express)
            {
                $code=1000;
                return $code;
            }
            $express->waybillnumber=$waybillnumber;
            $express->waybillname=$waybillname;
            $res=$express->save(false);
            if (!$res)
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $code=200;
            $tran->commit();
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }

    /*
       * 网页内容获取方法
      */
    private function getcontent($url)
    {
        if (function_exists("file_get_contents")) {
            $file_contents = file_get_contents($url);
        } else {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        return $file_contents;
    }

    /*
     * 获取对应名称和对应传值的方法
    */
    private function expressname($order)
    {
        $name   = json_decode($this->getcontent("http://www.kuaidi100.com/autonumber/auto?num=".$order), true);
        if (empty($name)){
            return false;
        }
        $result = $name[0]['comCode'];
        if (empty($result)) {
            return false;
        } else
        {
            return $result;
        }
    }

    /*
     * 返回$data array      快递数组查询失败返回false
     * @param $order        快递的单号
     * $data['ischeck'] ==1 已经签收
     * $data['data']        快递实时查询的状态 array
    */
    public function getorder($order)
    {
        $keywords = $this->expressname($order);
        if (!$keywords) {
            return false;
        }else {
            $result = $this->getcontent("http://www.kuaidi100.com/query?type=".$keywords."&postid=".$order);
            $data   = json_decode($result, true);
            return $data;
        }
    }

    /**
     * @param $order_no
     * @param $sku
     * @return int
     */
    public static  function Findexresslist($order_no,$sku)
    {
         if ($sku=='-1')
        {
            $waybill=Express::find()
                ->select('waybillnumber,waybillname,create_time')
                ->where(['waybillnumber'=>$order_no])
                ->asArray()
                ->one();
        }else{
            $waybill=Express::find()
                ->select('waybillnumber,waybillname,create_time')
                ->where(['order_no'=>$order_no,'sku'=>$sku])
                ->asArray()
                ->one();
        }
        if (!$waybill){
            $code = 1000;
            return $code;
        }else {
            $arr = array(
                'time' => date('Y-m-d H:i:s', $waybill['create_time']),
                'context' => '卖家已发货'
            );
            $waybillnumber = $waybill['waybillnumber'];
            $model = new Express();
            $result = $model->getorder($waybillnumber);
            $data = self::Expresslist($result, $arr);
        }
        foreach ($data['data'] as &$datum)
        {
            $datum['time']=date('Y-m-d H:i:s',strtotime($datum['time']));
            unset($datum['ftime']);
            unset($datum['location']);
        }
        return $data['data'];
    }

   /**
     * @param $order_no
     * @param $sku
     * @return array
     */
    public static  function Findexpresslist_sendtohome($order_no,$sku){
        $order=(new Query())->from(OrderGoods::tableName())
            ->select('order_status,shipping_status')
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        $express=Express::find()
            ->select('create_time,receive_time')
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->asArray()
            ->one();
        switch ($order['shipping_status']){
            case 1:
                $arr[] =[
                    'time' => date('Y-m-d H:i:s', $express['create_time']),
                    'context' => '您的商品已由工作人员派出，请注意查收'
                ];
                break;
            case 2:
                $arr[]=
                    [
                        'time' => date('Y-m-d H:i:s', $express['receive_time']),
                        'context' => '您的商品已签收 感谢使用 期待再次为您服务!'
                    ];
                $arr[]=
                    [
                    'time' => date('Y-m-d H:i:s', $express['create_time']),
                    'context' => '您的商品已由工作人员派出，请注意查收'
                ];
                break;
        }
        return $arr;

    }

    /**
     * 获取物流信息
     * @param $data
     * @param $arr
     * @return mixed
     */
    public static function Expresslist($data,$arr){
        $count=count($data['data']);
        $data['data'][$count]=$arr;
        return $data;
    }


    /**
     * 获取银行卡
     * @return bool|mixed
     */
    public function actionGetbank($cardnum){
        $sendUrl = 'http://api.avatardata.cn/Bank/Query'; //URL
        $smsConf = array(
            'key'=>'fc79d5cf0de64f9bb60759045e5977d0',
            'cardnum'=>$cardnum,
            'dtype'=>'JSON',
            'format'=>true
        );
        $content =$this->datacurl($sendUrl,$smsConf,1);
      return $content;
    }

    private function datacurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }


        /**获取收获剩余时间
         * @param $express
         * @return int
         */
      public  static  function  findRemainingTime($express)
      {
          $time=time();

          $RemainingTime=$express->create_time+15*24*60*60-$time;
          return $RemainingTime;
      }


        /**
         * 通过快递公司编码获取公司名称
         * @return array
         */
      public  static function  findExpressByCode()
      {
          return [
              'youzhengguonei'=>'邮政包裹/平邮',
              'youzhengguoji' =>'国际包裹',
              'ems'=>'EMS',
              'emsguoji'=>'EMS-国际件',
              'emsinten'=>'EMS-国际件-英文结果',
              'CN'=>'万国邮联',
              'bjemstckj'=>'北京EMS',
              'shunfeng'=>'顺丰',
              'shentong'=>'申通',
              'yuantong'=>'圆通',
              'zhongtong'=>'中通',
              'huitongkuaidi'=>'汇通',
              'baishiwuliu'=>'百世汇通',
              'yunda'=>'韵达',
              'zhaijisong'=>'宅急送',
              'tiantian'=>'天天',
              'debangwuliu'=>'德邦',
              'guotongkuaidi'=>'国通',
              'zengyisudi'=>'增益',
              'suer'=>'速尔',
              'ztky'=>'中铁物流',
              'zhongtiewuliu'=>'中铁快运',
              'ganzhongnengda'=>'能达',
              'youshuwuliu'=>'优速',
              'quanfengkuaidi'=>'全峰',
              'jd'=>'京东',
          ];
      }

    /**
     * 获取快递公司名称
     * @param $waybillnumber
     * @return mixed
     */
      public  function  GetExpressName($waybillnumber)
      {
          $name = json_decode($this->getcontent("http://www.kuaidi100.com/autonumber/auto?num=".$waybillnumber), true);
          if (!$name)
          {
              return null;
          }
         $data= self::findExpressByCode();
         if (!array_key_exists($name[0]['comCode'],$data))
         {
             return null;
         }
         return $data[$name[0]['comCode']];
      }
}