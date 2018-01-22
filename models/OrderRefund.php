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
    const  UNUSUAL_DESC='无异常';
    const REFUND_FAIL='退款失败';
    const HANDLE_AGREE=1;
    const HANDLE_DISAGREE=2;
    const HANDLE_UN_HANDLE=0;

    const REFUND_TYPE_LINE=1;
    const REFUND_TYPE_TO_WALLET=2;
    const REFUND_TYPE_DESC=[
        self::REFUND_TYPE_LINE=>'线下已退款',
        self::REFUND_TYPE_TO_WALLET=>'已退至顾客钱包'
    ];
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

    public  static  function  SetRefundParameter($order_refund)
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
        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        if (!$GoodsOrder )
        {
            $code=1000;
           return $code;
        }


        switch ($GoodsOrder->order_refer)
        {
            case self::REFUND_TYPE_LINE:
                $refund_type=self::REFUND_TYPE_DESC[self::REFUND_TYPE_LINE];
                break;
            case self::REFUND_TYPE_TO_WALLET:
                $refund_type=self::REFUND_TYPE_DESC[self::REFUND_TYPE_TO_WALLET];
                break;
        }
        $data=[];
        $unshipped=OrderRefund::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku,'order_type'=>GoodsOrder::ORDER_TYPE_UNSHIPPED])
            ->asArray()
            ->one();
        if ($unshipped)
        {
            if($unshipped['create_time'])
            {
                $unshipped['create_time']=date('Y-m-d H:i',$unshipped['create_time']);
            }
            if ($unshipped['refund_time'])
            {
                $unshipped['refund_time']=date('Y-m-d H:i',$unshipped['refund_time']);
            }
            if ($unshipped['handle_time'])
            {
                $unshipped['handle_time']=date('Y-m-d H:i',$unshipped['handle_time']);
            }
            if ($unshipped['handle']==0)
            {
                $arr1[]=[
                    'type'=>'取消原因',
                    'value'=>$unshipped['apply_reason'],
                    'content'=>'',
                    'time'=>$unshipped['create_time'],
                    'stage'=>$unshipped['order_type'],
                    'status'=>'in'
                ];
            }else {
                $arr1[] = [
                    'type' => '取消原因',
                    'value' => $unshipped['apply_reason'],
                    'content' => '',
                    'time' => $unshipped['create_time'],
                    'stage' => $unshipped['order_type'],
                    'status'=>''
                ];
                switch ($unshipped['handle']) {
                    case 1:
                        $type = '同意';
                        $reason = '';
                        $complete_time = $unshipped['refund_time'];
                        $result = '成功';
                        break;
                    case 2:
                        $type = '驳回';
                        $reason = $unshipped['handle_reason'];
                        $complete_time = $unshipped['handle_time'];
                        $result = '失败';
                        break;
                }
                $arr1[] = [
                    'type' => '商家反馈',
                    'value' => $type,
                    'content' => $reason,
                    'time' => $unshipped['handle_time'],
                    'stage' => $unshipped['order_type'],
                    'status'=>''
                ];
                $arr1[] = [
                    'type' => '退款结果',
                    'value' => $result,
                    'content' => '',
                    'time' => $complete_time,
                    'stage' => $unshipped['order_type'],
                    'status'=>''
                ];
                if ($unshipped['handle'] == 1) {
                    $arr1[] = [
                        'type' => '退款去向',
                        'value' => $refund_type,
                        'content' => '',
                        'time' => $complete_time,
                        'stage' => $unshipped['order_type'],
                        'status'=>'over'
                    ];
                }
            }
            $data[]=[
                'order_type'=>'退款详情-待发货',
                'list'=>$arr1
            ];
        }
        $unreceived=OrderRefund::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku,'order_type'=>GoodsOrder::ORDER_TYPE_UNRECEIVED])
            ->asArray()
            ->one();
        if ($unreceived)
        {
            if($unreceived['create_time'])
            {
                $unreceived['create_time']=date('Y-m-d H:i',$unreceived['create_time']);
            }
            if ($unreceived['refund_time'])
            {
                $unreceived['refund_time']=date('Y-m-d H:i',$unreceived['refund_time']);
            }
            if ($unreceived['handle_time'])
            {
                $unreceived['handle_time']=date('Y-m-d H:i',$unreceived['handle_time']);
            }
            if ($unreceived['handle']==0)
            {
                $arr2[]=[
                    'type'=>'取消原因',
                    'value'=>$unreceived['apply_reason'],
                    'content'=>'',
                    'time'=>$unreceived['create_time'],
                    'stage'=>$unreceived['order_type'],
                    'status'=>'in'
                ];
            }else {
                $arr2[] = [
                    'type' => '取消原因',
                    'value' => $unreceived['apply_reason'],
                    'content' => '',
                    'time' => $unreceived['create_time'],
                    'stage' => $unreceived['order_type'],
                    'status'=>''
                ];
                switch ($unreceived['handle']) {
                    case 1:
                        $type = '同意';
                        $reason = '';
                        $complete_time = $unreceived['refund_time'];
                        $result = '成功';
                        break;
                    case 2:
                        $type = '驳回';
                        $reason = $unreceived['handle_reason'];
                        $complete_time = $unreceived['handle_time'];
                        $result = '失败';
                        break;
                }
                $arr2[] = [
                    'type' => '商家反馈',
                    'value' => $type,
                    'content' => $reason,
                    'time' => $unreceived['handle_time'],
                    'stage' => $unreceived['order_type'],
                    'status'=>''
                ];
                $arr2[] = [
                    'type' => '退款结果',
                    'value' => $result,
                    'content' => '',
                    'time' => $complete_time,
                    'stage' => $unreceived['order_type'],
                    'status'=>'in'
                ];
                if ($unreceived['handle'] == 1) {
                    $arr2[] = [
                        'type' => '退款去向',
                        'value' => $refund_type,
                        'content' => '',
                        'time' => $complete_time,
                        'stage' => $unreceived['order_type'],
                        'status'=>'over'
                    ];
                }
            }
            $data[]=[
                'order_type'=>'退款详情-待收货',
                'list'=>$arr2
            ];
        }
        $OrderPlatform=OrderPlatForm::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$OrderPlatform)
        {
            $platform=[];
        }else{
            $OrderPlatform->creat_time=date('Y-m-d H:i',$OrderPlatform->creat_time);
            $OrderPlatform->refund_time=date('Y-m-d H:i',$OrderPlatform->refund_time);
            $arrp[]=[
                'type'=>'选择操作',
                'value'=>'关闭订单，退款',
                'content'=>$OrderPlatform->reasons,
                'time'=>$OrderPlatform->creat_time,
                'stage'=>'',
                'status'=>''
            ];
            $arrp[]=[
                'type'=>'退款结果',
                'value'=>'成功',
                'content'=>'',
                'time'=>$OrderPlatform->refund_time,
                'stage'=>'',
                'status'=>'over'
            ];
//            $platform=self::object2array(['list'=>$arrp]);
            $platform=(array)$arrp;
        }

        return ['data'=>$data,'platform'=>$platform];
    }


    /**
     * @param $object
     * @return mixed
     */
    public  static  function object2array($object)
    {
        $object =  json_decode( json_encode( $object),true);
        return  $object;
    }

    /**
     * 减少销量，减少销售额，增加库存.减少商品销量
     * @param $supplier_id
     * @param $goods_num
     * @param $goods_price
     * @param $freight
     * @param $sku
     * @return int
     */
    public  static  function  ReduceSold($supplier_id,$goods_num,$goods_price,$freight,$sku)
    {
        $trans = \Yii::$app->db->beginTransaction();
        try {
            //减少销量，减少销售额，增加库存.减少商品销量
            $date=date('Ymd',time());
            $GoodsStat=GoodsStat::find()
                ->where(['supplier_id'=>$supplier_id])
                ->andWhere(['create_date'=>$date])
                ->one();
            if (!$GoodsStat)
            {
                $GoodsStat=new GoodsStat();
                $GoodsStat->supplier_id=$supplier_id;
                $GoodsStat->sold_number=$goods_num;
                $GoodsStat->amount_sold=(($goods_price*$goods_num)+$freight);
                $GoodsStat->create_date=$date;
                if (!$GoodsStat->save(false))
                {
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
            }else{
                $GoodsStat->sold_number-=$goods_num;
                $GoodsStat->amount_sold-=(($goods_price*$goods_num)+$freight);
                if (!$GoodsStat->save(false))
                {
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
            }
            $Goods=Goods::find()->where(['sku'=>$sku])->one();
            $Goods->left_number+=$goods_num;
            $Goods->sold_number-=$goods_num;
            if (!$Goods->save(false))
            {
                $code=500;
                $trans->rollBack();
                return $code;
            }
            $trans->commit();
            $code=200;
            return $code;
        } catch (\Exception $e) {
            $code=500;
            $trans->rollBack();
            return $code;
        }
    }




}