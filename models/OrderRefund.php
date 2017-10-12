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

  /**
     * @param $order_no
     * @param $sku
     * @return array|ActiveRecord[]
     */
    public static  function  findByOrderNoAndSku($order_no,$sku)
    {
        $data=self::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->all();
        return $data?$data:[];
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

    //  /**
    //  * @param $order_no
    //  * @param $sku
    //  * @return array|int
    //  */
    // public  static  function  FindRefundDetail($order_no,$sku)
    // {
    //     $OrderGoods=OrderGoods::find()
    //         ->where(['order_no'=>$order_no,'sku'=>$sku])
    //         ->asArray()
    //         ->all();
    //     if (!$OrderGoods)
    //     {
    //         $code=1000;
    //         return $code;
    //     }
    //     //退款详情
    //     $order_refund=self::find()
    //         ->select('create_time,handle_time,refund_time,apply_reason,handle_reason,handle')
    //         ->where(['order_no'=>$order_no,'sku'=>$sku])
    //         ->asArray()
    //         ->all();
    //     foreach ($order_refund  as &$orderRefund)
    //     {
    //         $orderRefund['create_time']=date('Y-m-d H:i',$orderRefund['create_time']);
    //         if ($orderRefund['handle']!=0){
    //             $orderRefund['handle_time']=date('Y-m-d H:i',$orderRefund['handle_time']);
    //         }
    //         if ($orderRefund['handle']==1){
    //             $orderRefund['refund_time']=date('Y-m-d H:i',$orderRefund['refund_time']);
    //         }
    //     }
    //     if (!$order_refund)
    //     {
    //        $code=1000;
    //        return $code;
    //     }
    //     $OrderPlatform=OrderPlatForm::find()
    //         ->where(['order_no'=>$order_no,'sku'=>$sku])
    //         ->one();
    //     if (!$OrderPlatform)
    //     {
    //         $OrderPlatform=[];
    //     }
    //     $OrderPlatform->creat_time=date('Y-m-d H:i',$OrderPlatform->creat_time);
    //     $OrderPlatform->refund_time=date('Y-m-d H:i',$OrderPlatform->refund_time);
    //     return ['data'=>$order_refund,'platform'=>$OrderPlatform];
    // }
    // 
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
        $unusualList=OrderRefund::findByOrderNoAndSku($order_no,$sku);
        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        if (!$GoodsOrder || !$unusualList)
        {
            $code=1000;
           return $code;
        }
        switch ($GoodsOrder->order_refer)
        {
            case 1:
                $refund_type='线下已退款';
                break;
            case 2:
                $refund_type='已退至顾客钱包';
                break;
        }

        foreach ($unusualList as $k =>$v)
        {
            $unusualList[$k]=$unusualList[$k]->toArray();
            if($unusualList[$k]['create_time'])
            {
                $unusualList[$k]['create_time']=date('Y-m-d H:i',$unusualList[$k]['create_time']);
            }
            if ($unusualList[$k]['refund_time'])
            {
                $unusualList[$k]['refund_time']=date('Y-m-d H:i',$unusualList[$k]['refund_time']);
            }
            if ($unusualList[$k]['handle_time'])
            {
                $unusualList[$k]['handle_time']=date('Y-m-d H:i',$unusualList[$k]['handle_time']);
            }
            if ($unusualList[$k]['handle']==0)
            {
                $arr[]=[
                    'type'=>'取消原因',
                    'value'=>$unusualList[$k]['apply_reason'],
                    'content'=>'',
                    'time'=>$unusualList[$k]['create_time'],
                    'stage'=>$unusualList[$k]['order_type']
                ];
            }else{
                $arr[]=[
                    'type'=>'取消原因',
                    'value'=>$unusualList[$k]['apply_reason'],
                    'content'=>'',
                    'time'=>$unusualList[$k]['create_time'],
                    'stage'=>$unusualList[$k]['order_type']
                ];
                switch ($unusualList[$k]['handle'])
                {
                    case 1:
                        $type='同意';
                        $data_code[$k]['reason']='';
                        $data_code[$k]['complete_time']=$unusualList[$k]['refund_time'];
                        $result='成功';
                        break;
                    case 2:
                        $type='驳回';
                        $data_code[$k]['reason']=$unusualList[$k]['handle_reason'];
                        $data_code[$k]['complete_time']=$unusualList[$k]['handle_time'];
                        $result='失败';
                        break;
                }
                $arr[]=[
                    'type'=>'商家反馈',
                    'value'=>$type,
                    'content'=>$data_code[$k]['reason'],
                    'time'=>$unusualList[$k]['handle_time'],
                    'stage'=>$unusualList[$k]['order_type']
                ];
                $arr[]=[
                    'type'=>'退款结果',
                    'value'=>$result,
                    'content'=>'',
                    'time'=>$data_code[$k]['complete_time'],
                    'stage'=>$unusualList[$k]['order_type']
                ];
                if ($unusualList[$k]['handle']==1){
                    $arr[]=[
                        'type'=>'退款去向',
                        'value'=>$refund_type,
                        'content'=>'',
                        'time'=>$data_code[$k]['complete_time'],
                        'stage'=>$unusualList[$k]['order_type']
                    ];
                }
            }
            $data[$k]['order_type']=$unusualList[$k]['order_type'];
            $data[$k]['list']=$arr;
        }

        $OrderPlatform=OrderPlatForm::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$OrderPlatform)
        {
            $OrderPlatform=[];
        }else{

            $OrderPlatform->creat_time=date('Y-m-d H:i',$OrderPlatform->creat_time);
            $OrderPlatform->refund_time=date('Y-m-d H:i',$OrderPlatform->refund_time);
            $arr[]=[
                'type'=>'选择操作',
                'value'=>'关闭订单，退款',
                'content'=>$OrderPlatform->reasons,
                'time'=>$OrderPlatform->creat_time,
                'stage'=>''
            ];
            $arr[]=[
                'type'=>'退款结果',
                'value'=>'成功',
                'content'=>'',
                'time'=>$OrderPlatform->refund_time,
                'stage'=>''
            ];
        }

        return ['data'=>$data,'platform'=>$OrderPlatform];
    }



}