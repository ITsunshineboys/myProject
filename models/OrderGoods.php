<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class OrderGoods extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'order_goodslist';
    }

    /**
     * @param $order_no
     * @param $sku
     * @return array|null|ActiveRecord
     */
      public static  function  FindByOrderNoAndSku($order_no,$sku){
        $data=self::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->one();
        return $data;
    }

    /**
     * @param array $postData
     * @param $user
     * @return int
     */
    public static  function  UserConfirmReceipt($postData=[],$user)
    {
            if (!array_key_exists('order_no',$postData) || !array_key_exists('sku',$postData)){
                $code=1000;
                return $code;
            }
            $GoodsOrder=GoodsOrder::FindByOrderNo($postData['order_no']);
            $OrderGoods=self::FindByOrderNoAndSku($postData['order_no'],$postData['sku']);
            if (!$OrderGoods
                || $user->id !=$GoodsOrder->user_id
                || $OrderGoods->shipping_status!=1)
            {
                $code=1034;
                return $code;
            }
            $tran = Yii::$app->db->beginTransaction();
            try{
                $OrderGoods->shipping_status=2;
                $OrderGoods->order_status=1;
                $res=$OrderGoods->save();
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
                $code=200;
                $tran->commit();
                return $code;
            }catch (Exception $e)
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }



    }
}