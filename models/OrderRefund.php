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


}