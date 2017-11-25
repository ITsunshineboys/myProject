<?php

namespace app\models;
use Yii;
use Yii\db\Exception;
use Yii\db\ActiveRecord;
use yii\db\query;
use app\services\ModelService;

class OrderPlatForm extends ActiveRecord
{

    //平台介入表结构creat_time为创建时间  !important
    const PLATFORM_HANDLE_TYPE=[
        1 => '关闭订单退款',
        2 => '关闭订单线下退款',
        3 => '退货',
        4 => '换货',
        5 => '上门维修',
        6 => '上门退货',
        7 => '上门换货'
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'order_platform_handle';
    }

    /**
     * 关闭订单，线下退款
     * @param $order_no
     * @param $handle_type
     * @param $reason
     * @return Exception|\Exception|int
     */
    public static  function  platformhandle2($order_no,$handle_type,$reason,$sku)
    {
            $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
            if (!$GoodsOrder || $GoodsOrder->order_refer!=1)
            {
                $code=1034;
                return $code;
            }
            $supplier=Supplier::findOne($GoodsOrder->supplier_id);
            $time=time();
            $trans = \Yii::$app->db->beginTransaction();
            try {
                $OrderPlatForm=new self;
                $OrderPlatForm->handle=$handle_type;
                $OrderPlatForm->reasons=$reason;
                $OrderPlatForm->creat_time=$time;
                $OrderPlatForm->refund_result=2;
                $OrderPlatForm->refund_type=2;
                $OrderPlatForm->refund_time=$time;
                $res=$OrderPlatForm->save(false);
                if (!$res){
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
                $OrderGoods=OrderGoods::find()
                    ->where(['order_no'=>$order_no])
                    ->andWhere(['sku'=>$sku])
                    ->one();
                $OrderGoods->order_status=2;
                $res2=$OrderGoods->save(false);
                if (!$res2){
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }

                $UserAccessDetail=new UserAccessdetail();
                $UserAccessDetail->uid=$supplier->uid;
                $UserAccessDetail->role_id=6;
                $UserAccessDetail->access_type=2;
                $UserAccessDetail->access_money=($OrderGoods->supplier_price*$OrderGoods->goods_number);
                $UserAccessDetail->create_time=time();
                $UserAccessDetail->order_no=$order_no;
                $UserAccessDetail->sku=$sku;
                $UserAccessDetail->transaction_no=GoodsOrder::SetTransactionNo($GoodsOrder->consignee_mobile);
                if (!$UserAccessDetail->save(false))
                {
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
                $supplier->availableamount-=($OrderGoods->supplier_price*$OrderGoods->goods_number);
                $supplier->balance-=($OrderGoods->supplier_price*$OrderGoods->goods_number);
                if (!$supplier->save(false))
                {
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
                //减少销量，减少销售额，增加库存.减少商品销量
                $date=date('Ymd',time());
                $GoodsStat=GoodsStat::find()
                    ->where(['supplier_id'=>$supplier->id])
                    ->andWhere(['create_date'=>$date])
                    ->one();
                if (!$GoodsStat)
                {
                    $GoodsStat=new GoodsStat();
                    $GoodsStat->supplier_id=$supplier->id;
                    $GoodsStat->sold_number=$OrderGoods->goods_number;
                    $GoodsStat->amount_sold=(($OrderGoods->goods_price*$OrderGoods->goods_number)+$OrderGoods->freight);
                    $GoodsStat->create_date=$date;
                    if (!$GoodsStat->save(false))
                    {
                        $code=500;
                        $trans->rollBack();
                        return $code;
                    }
                }else{

                    $GoodsStat->sold_number-=$OrderGoods->goods_number;
                    $GoodsStat->amount_sold-=(($OrderGoods->goods_price*$OrderGoods->goods_number)+$OrderGoods->freight);
                    if (!$GoodsStat->save(false))
                    {
                        $code=500;
                        $trans->rollBack();
                        return $code;
                    }
                }
                $Goods=Goods::find()->where(['sku'=>$sku])->one();
                $Goods->left_number+=$OrderGoods->goods_number;
                $Goods->sold_number-=$OrderGoods->goods_number;
                if (!$Goods->save(false))
                {
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
                $trans->commit();
                $code=200;
                return $code;
            } catch (Exception $e) {
                $code=500;
                $trans->rollBack();
                return $code;
            }
    }

    /**
     * @param $order_no
     * @param $handle_type
     * @param $reason
     * @param $sku
     * @return int
     */
    public static function platformHandleCloseOrderRefundToUser($order_no,$handle_type,$reason,$sku)
    {
        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        if (!$GoodsOrder || $GoodsOrder->order_refer!=2)
        {
            $code=1000;
            return $code;
        }
        $time=time();
        $tran=\Yii::$app->db->beginTransaction();
        try{
            $OrderPlatForm=new self;
            $OrderPlatForm->handle=$handle_type;
            $OrderPlatForm->reasons=$reason;
            $OrderPlatForm->creat_time=$time;
            $OrderPlatForm->refund_result=2;
            $OrderPlatForm->refund_type=1;
            $OrderPlatForm->refund_time=$time;
            $OrderPlatForm->order_no=$order_no;
            $OrderPlatForm->sku=$sku;
            $res=$OrderPlatForm->save(false);
            if (!$res){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
            if (!$OrderGoods)
            {
                $code=1000;
                $tran->rollBack();
                return $code;
            }
            $OrderGoods->order_status=2;
            $res2=$OrderGoods->save(false);
            if (!$res2){
                $code=500;

                $tran->rollBack();
                return $code;
            }
            if ($OrderGoods->shipping_status==0){
                $refund_money=$OrderGoods->goods_price*$OrderGoods->goods_number+$OrderGoods->freight;
                $reduce_money=$OrderGoods->supplier_price*$OrderGoods->goods_number+$OrderGoods->freight;
            }else{
                $refund_money=$OrderGoods->goods_price*$OrderGoods->goods_number;
                $reduce_money=$OrderGoods->supplier_price*$OrderGoods->goods_number;
            }
            $users = \Yii::$app->user->identity;
            $user=User::find()->where(['id'=>$users->id])->one();
            $user->balance=$user->balance+$refund_money;
            $res3=$user->save(false);
            if (!$res3){

                $code=500;
                $tran->rollBack();
                return $code;

            }
            $GoodsOrder=GoodsOrder::find()
                ->select(['supplier_id'])
                ->where(['order_no'=>$order_no])
                ->one();
            $supplier=Supplier::find()
                ->where(['id'=>$GoodsOrder->supplier_id])
                ->one();

            $supplier->balance=$supplier->balance-$reduce_money;
            $supplier->availableamount=$supplier->availableamount-$reduce_money;
            $res4=$supplier->save(false);
            if (!$res4){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $transaction_no=GoodsOrder::SetTransactionNo($supplier->id);
            $supplier_accessdetail=new UserAccessdetail();
            $supplier_accessdetail->uid=$user->id;
            $supplier_accessdetail->role_id=6;
            $supplier_accessdetail->access_type=2;
            $supplier_accessdetail->access_money=$reduce_money;
            $supplier_accessdetail->order_no=$order_no;
            $supplier_accessdetail->sku=$sku;
            $supplier_accessdetail->create_time=time();
            $supplier_accessdetail->transaction_no=$transaction_no;
            $res5=$supplier_accessdetail->save(false);
            if (!$res5){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch(Exception $e){
            $code=500;
            $tran->rollBack();
            return $code;
        }
    }
    /**
     * @param $order_no
     * @param $handle_type
     * @param $reason
     * @param $sku
     * @return int
     */
    public  static  function  platformHandReturnGoods($order_no,$handle_type,$reason,$sku)
    {
        $time=time();
        $tran=\Yii::$app->db->beginTransaction();
        try{
            $OrderPlatForm=new self;
            $OrderPlatForm->handle=$handle_type;
            $OrderPlatForm->reasons=$reason;
            $OrderPlatForm->creat_time=$time;
            $OrderPlatForm->refund_result=2;
            $OrderPlatForm->refund_time=$time;
            $res=$OrderPlatForm->save();
            if (!$res){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch(Exception $e){
            $code=500;
            $tran->rollBack();
            return $code;
        }
    }

}
