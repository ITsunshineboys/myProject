<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;
use yii\db\ActiveRecord;
use Yii;

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
            $supplier=Supplier::find()->where(['id'=>$GoodsOrder->supplier_id])->one();
            $role_number=$supplier->shop_no;
            $transaction_no=GoodsOrder::SetTransactionNo($role_number);
            $tran = Yii::$app->db->beginTransaction();
            try{
                $OrderGoods->shipping_status=2;
                $OrderGoods->order_status=1;
                $res=$OrderGoods->save(false);
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
                $supplier_accessdetail=new UserAccessdetail();
                $supplier_accessdetail->uid=$supplier->uid;
                $supplier_accessdetail->role_id=6;
                $supplier_accessdetail->access_type=6;
                $supplier_accessdetail->access_money=($OrderGoods->freight+$OrderGoods->supplier_price*$OrderGoods->goods_number);
                $supplier_accessdetail->order_no=$postData['order_no'];
                $supplier_accessdetail->sku=$postData['sku'];
                $supplier_accessdetail->create_time=time();
                $supplier_accessdetail->transaction_no=$transaction_no;
                $res2=$supplier_accessdetail->save(false);
                if (!$res2)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
                $supplier->availableamount+=($OrderGoods->freight+$OrderGoods->supplier_price*$OrderGoods->goods_number);
                $supplier->balance+=$OrderGoods->freight+$OrderGoods->supplier_price*$OrderGoods->goods_number;
                $res3=$supplier->save(false);
                if (!$res3)
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