<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Json;
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
        $res=Yii::$app->db->createCommand()->update('express', ['waybillnumber' =>$waybillnumber,'waybillname'=>$waybillname], "sku=".$sku." and order_no=".$order_no)->execute();
        return $res;
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
        } else {
            $result = $this->getcontent("http://www.kuaidi100.com/query?type=".$keywords."&postid=".$order);
            $data   = json_decode($result, true);
            return $data;
        }
    }

    public static  function Findexresslist($order_no,$sku)
    {
        $waybill=Express::find()->select('waybillnumber,waybillname,create_time')->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        if (!$waybill){
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => '物流信息不存在'
            ]);
        }else {
            $arr = array(
                'time' => date('Y-m-d H:i:s', $waybill['create_time']),
                'ftime' => date('Y-m-d H:i:s', $waybill['create_time']),
                'context' => '卖家已发货'
            );
            $waybillnumber = $waybill['waybillnumber'];
            $model = new Express();
            $result = $model->getorder($waybillnumber);
            $data = self::Expresslist($result, $arr);
        }
        return $data;
    }

    public static  function Findexpresslist_sendtohome($order_no,$sku){
        $order=(new Query())->from(ORDER_GOODSLIST)->select('order_status,shipping_status')->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        $express=Express::find()->select('create_time,receive_time')->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        switch ($order['shipping_status']){
            case 1:
                $arr[] =[
                    'time' => date('Y-m-d H:i:s', $express['create_time']),
                    'ftime' => date('Y-m-d H:i:s', $express['create_time']),
                    'context' => '您的商品已由工作人员派出，请注意查收'
                ];
                break;
            case 2:
                $arr[]=
                    ['time' => date('Y-m-d H:i:s', $express['receive_time']),
                        'ftime' => date('Y-m-d H:i:s', $express['receive_time']),
                        'context' => '您的商品已签收 感谢使用 期待再次为您服务!'
                    ];
                $arr[]=
                    ['time' => date('Y-m-d H:i:s', $express['create_time']),
                    'ftime' => date('Y-m-d H:i:s', $express['create_time']),
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
}