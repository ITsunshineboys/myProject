<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

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
}