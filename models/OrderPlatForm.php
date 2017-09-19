<?php

namespace app\models;
use app\admin\controller\Users;
use Yii;
use yii\db\Exception;
use yii\db\ActiveRecord;
use yii\db\query;

class OrderPlatForm extends ActiveRecord
{


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
                $res=$OrderPlatForm->save();
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
                $res2=$OrderGoods->save();
                if (!$res2){
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
            $res=$OrderPlatForm->save();
            if (!$res){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $OrderGoods=OrderGoods::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
            $OrderGoods->order_status=2;
            $res2=$OrderGoods->save();
            if (!$res2){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            if ($OrderGoods->shiping_status==0){
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
                ->select(['id'])
                ->where(['order_no'=>$order_no])
                ->one();
            $supplier=Supplier::find()
                ->where(['id'=>$GoodsOrder->supplier_id])
                ->one();

            $supplier->balance=$supplier->balance-$reduce_money;
            $supplier->availableamount=$supplier->availableamount-$reduce_money;
            $res4=$supplier->save();
            if (!$res4){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $transaction_no=ModelService::SetTransactionNo($supplier->id);
            $supplier_accessdetail=new UserAccessdetail();
            $supplier_accessdetail->uid=$user->id;
            $supplier_accessdetail->role_id=6;
            $supplier_accessdetail->access_type=4;
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
