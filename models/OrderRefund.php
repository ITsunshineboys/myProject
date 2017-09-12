<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class OrderRefund extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'order_refund';
    }


    public  static  function  SetRefundparameter($order_refund)
    {
        foreach ($order_refund as $k =>$v)
        {

            $order_refund[$k]['create_time']=date('Y-m-d H:i',$order_refund[$k]['create_time']);
            $order_refund[$k]['handle_time']=date('Y-m-d H:i',$order_refund[$k]['handle_time']);
            $order_refund[$k]['refund_time']=date('Y-m-d H:i',$order_refund[$k]['refund_time']);
            if ($order_refund[$k]['handle']==0){
                unset( $order_refund[$k]['handle_time']);
                unset( $order_refund[$k]['refund_time']);
            }
        }
        return $order_refund;
    }

     /**
     * @param $order_no
     * @param $sku
     * @return array|int
     */
    public  static  function  FindRefundDetail($order_no,$sku)
    {
        $OrderGoods=OrderGoods::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->asArray()
            ->all();
        if (!$OrderGoods)
        {
            $code=1000;
            return $code;
        }
        //退款详情
        $order_refund=self::find()
            ->select('create_time,handle_time,refund_time,apply_reason,handle_reason,handle')
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->asArray()
            ->all();
        foreach ($order_refund  as &$orderRefund)
        {
            $orderRefund['create_time']=date('Y-m-d H:i',$orderRefund['create_time']);
            if ($orderRefund['handle']!=0){
                $orderRefund['handle_time']=date('Y-m-d H:i',$orderRefund['handle_time']);
            }
            if ($orderRefund['handle']==1){
                $orderRefund['refund_time']=date('Y-m-d H:i',$orderRefund['refund_time']);
            }
        }
        if (!$order_refund)
        {
           $code=1000;
           return $code;
        }
        $OrderPlatform=OrderPlatForm::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$OrderPlatform)
        {
            $OrderPlatform=[];
        }
        $OrderPlatform->creat_time=date('Y-m-d H:i',$OrderPlatform->creat_time);
        $OrderPlatform->refund_time=date('Y-m-d H:i',$OrderPlatform->refund_time);
        return ['data'=>$order_refund,'platform'=>$OrderPlatform];
    }


}