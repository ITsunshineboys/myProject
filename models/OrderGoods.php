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

    const  PLATFORM_PRICE_DESC='平台价';
    const  SUPPLIER_PRICE_DESC='供应商采购价格';
    const  COMPANY_PRICE_DESC='装修公司采购价';
    const  MANAGER_PRICE_DESC='项目经理采购价';
    const  DESIGNER_PRICE_DESC='设计师采购价';
    const  WORKER_PRICE_DESC='工人采购价';

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
    public static  function  FindByOrderNoAndSku($order_no,$sku, $select = []){
        $data=self::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->select($select)
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
            if (
                !array_key_exists('order_no',$postData)
                || !array_key_exists('sku',$postData)
            ){
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
                $supplier_accessdetail->role_id=Yii::$app->params['supplierRoleId'];
                $supplier_accessdetail->access_type=UserAccessdetail::ACCESS_TYPE_PAYMENT_GOODS;
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
                $express=Express::find()
                    ->where(
                        [
                            'order_no'=>$postData['order_no'],
                            'sku'=>$postData['sku']
                        ]
                    )
                    ->one();
                if ($express)
                {
                    $express->receive_time=time();
                    if (!$express->save(false))
                    {
                        $tran->rollBack();
                        $code=500;
                        return $code;
                    }
                }
                $code=UserNewsRecord::AddOrderNewRecord(User::findOne($supplier->uid),'订单已收货',Yii::$app->params['supplierRoleId'],"订单号{$postData['order_no']},商品名称{$OrderGoods->goods_name},货款已打至您的账户余额.",$postData['order_no'],$postData['sku'],GoodsOrder::STATUS_DESC_DETAILS);
                if ($code!=200)
                {
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
                $code=200;
                $tran->commit();
                return $code;
            }catch (\Exception $e)
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
    }

    /**
     * 添加样板间操作
     * @param $id
     * @return int
     */
    public  static  function  AddEffect($id)
    {
        $effect=Effect::findOne($id);
        if (!$effect)
        {
           return 200;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $earnest=EffectEarnest::find()
                ->where(['effect_id'=>$id])
                ->one();
            $earnest->status=1;
            if (!$earnest->save(false))
            {
                $tran->rollBack();
                return 500;
            }
            $time=(time()-60*60*6);
            $list=EffectEarnest::find()
                ->where("create_time<={$time}")
                ->andWhere(['status'=>0,'type'=>0,'item'=>0])
                ->all();
            if ($list)
            {
                foreach ($list as &$delList)
                {
                    $effect_id=$delList->effect_id;
                    $res=$delList->delete();
                    if (!$res)
                    {
                        $tran->rollBack();
                        return 500;
                    };
                    $effect=Effect::find()
                        ->where(['id'=>$effect_id])
                        ->one();
                    if ($effect)
                    {
                        $res1=$effect->delete();
                        if (!$res1)
                        {
                            $tran->rollBack();
                            return 500;
                        };
                    }
                    $effect_material=EffectMaterial::find()
                        ->where(['effect_id'=>$effect_id])
                        ->one();
                    if ($effect_material)
                    {
                        $res2=$effect_material->delete();
                        if (!$res2)
                        {
                            $tran->rollBack();
                            return 500;
                        };
                    }
                    $EffectPicture=EffectPicture::find()
                        ->where(['effect_id'=>$effect_id])
                        ->one();
                    if ($EffectPicture)
                    {
                        $res3=$EffectPicture->delete();
                        if (!$res3)
                        {
                            $tran->rollBack();
                            return 500;
                        };
                    }
                }
            }
            $tran->commit();
            return 200;
        }catch (\Exception $e){
            $tran->rollBack();
            return 500;
        }

    }


    /**
     * @param $order_no
     * @param $sku
     * @return array|int
     */
    public  static  function  GetOrderDetails($order_no,$sku)
    {
        //获取订单信息
        $order_information=GoodsOrder::GetOrderInformation($order_no,$sku);
        if (!$order_information)
        {
            $code = 1000;
            return $code;
        }
        //获取商品信息
        $goods_name=$order_information['goods_name'];
        $goods_id=$order_information['goods_id'];
        $order_no=$order_information['order_no'];
        $sku=explode('+',$order_information['sku']);
        //获取商品属性
        $ordergoodsinformation=GoodsOrder::GetOrderGoodsInformation($goods_name,$goods_id,$order_no,$sku);
        if (!$ordergoodsinformation){
            $code = 1000;
            return $code;
        }

        //获取收货详情
        $receive_details['consignee']=$order_information['consignee'];
        $receive_details['consignee_mobile']=$order_information['consignee_mobile'];
        $receive_details['district']=LogisticsDistrict::getdistrict($order_information['district_code']).$order_information['region'];
        $receive_details['region']=$order_information['region'];
        $receive_details['invoice_header']=$order_information['invoice_header'];
        $receive_details['invoice_type']=$order_information['invoice_type'];
        $receive_details['invoice_header_type']=$order_information['invoice_header_type'];
        $receive_details['invoice_content']=$order_information['invoice_content'];
        $receive_details['invoicer_card'] = $order_information['invoicer_card'];
        $receive_details['buyer_message'] = $order_information['buyer_message'];
        switch ($receive_details['invoice_header_type']){
            case 1:
                $receive_details['invoice_header_type']=Invoice::INVOICE_HEADER_TYPE_PERSON;
                break;
            case 2:
                $receive_details['invoice_header_type']=Invoice::INVOICE_HEADER_TYPE_COMPANY;
                break;
        }
        switch ($receive_details['invoice_type']){
            case 1:
                $receive_details['invoice_type']=Invoice::INVOICE_TYPE_ORDINARY;
                break;
            case 2:
                $receive_details['invoice_type']=Invoice::INVOICE_TYPE_ELECT;
                break;
            case 3:
                $receive_details['invoice_type']=Invoice::INVOICE_TYPE_ADD_TAX;
                break;
        }
        $goods_data=[];
        if ($order_information['goods_name']=='+'){
            $goods_data['goods_name']='';
        }else{
            $goods_data['goods_name']=$order_information['goods_name'];
        }
        $goods_data['status']=$order_information['status'];
        $goods_data['order_no']=$order_information['order_no'];
        $goods_data['sku']=$order_information['sku'];
        $goods_data['username']=$order_information['username'];
        $goods_data['amount_order']=$order_information['amount_order'];
        switch ($order_information['role_id'])
        {
            case 7:
                $goods_data['role']=OrderGoods::PLATFORM_PRICE_DESC;
                break;
            case 6:
                $goods_data['role']=OrderGoods::SUPPLIER_PRICE_DESC;
                break;
            case 5:
                $goods_data['role']=OrderGoods::COMPANY_PRICE_DESC;
                break;
            case 4:
                $goods_data['role']=OrderGoods::MANAGER_PRICE_DESC;
                break;
            case 3:
                $goods_data['role']=OrderGoods::DESIGNER_PRICE_DESC;
                break;
            case 2:
                $goods_data['role']=OrderGoods::WORKER_PRICE_DESC;
                break;
        }

        $goods_data['goods_price']=$order_information['goods_price'];
        $goods_data['goods_number']=$order_information['goods_number'];
        $goods_data['freight']=$order_information['freight'];
        $goods_data['return_insurance']=$order_information['return_insurance'];
        $goods_data['supplier_price']=$order_information['supplier_price'];
        $goods_data['market_price']=$order_information['market_price'];
        $goods_data['shipping_type']=$order_information['shipping_type'];
        $goods_data['shipping_way']=$order_information['shipping_way'];
        $express=Express::find()->where(['order_no'=>$order_no])->andWhere(['sku'=>$sku])->one();
        $goods_data['send_time']=$express?date('Y-m-d H:i',$express->create_time):0;
        $goods_data['complete_time']=$order_information['complete_time'];
        if ($order_information['shipping_type']==1){
            $goods_data['shipping_way']=Express::SEND_TO_HOME;
            $goods_data['send_time']=$express?date('Y-m-d H:i',$express->create_time):0;
        }
        $goods_data['pay_name']=$order_information['pay_name'];
        if ($order_information['status']==GoodsOrder::ORDER_TYPE_DESC_UNPAID){
            $goods_data['pay_term']=$order_information['pay_term'];
        }else{
            $goods_data['pay_term']=0;
        }
        if (!$order_information['paytime']==0){
            $goods_data['paytime']=$order_information['paytime'];
        }
        //1:无平台介入  2：有平台进入
        if (!OrderPlatForm::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one())
        {
            $is_platform=1;
        }else{
            $is_platform=2;
        }
        //1: 无退款  2：有退款
        if (!OrderRefund::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one())
        {
            $is_refund=1;
        }else{
            $is_refund=2;
        }
        $goods_data['create_time']=$order_information['create_time'];
        $data=[
            'goods_data'=>$goods_data,
            'goods_value'=>$ordergoodsinformation,
            'receive_details'=>$receive_details,
            'is_unusual'=>$order_information['is_unusual'],
            'is_platform'=>$is_platform,
            'is_refund'=>$is_refund
        ];
        return $data;
    }




    
}