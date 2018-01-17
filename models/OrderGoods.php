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
use yii\db\Query;

class OrderGoods extends ActiveRecord
{

    const  PLATFORM_PRICE_DESC='平台价';
    const  SUPPLIER_PRICE_DESC='供应商采购价格';
    const  COMPANY_PRICE_DESC='装修公司采购价';
    const  MANAGER_PRICE_DESC='项目经理采购价';
    const  DESIGNER_PRICE_DESC='设计师采购价';
    const  WORKER_PRICE_DESC='工人采购价';


    const STATUS_TYPE_UNPAID=1;
    const STATUS_TYPE_UN_SHIPPED=2;
    const STATUS_TYPE_UN_SHIPPED_REFUND=3;
    const STATUS_TYPE_SHIPPED=4;
    const STATUS_TYPE_SHIPPED_REFUND=5;
    const STATUS_TYPE_UN_RECEIVED=6;
    const STATUS_TYPE_UN_RECEIVED_REFUND=7;
    const STATUS_TYPE_CANCEL=8;
    const STATUS_TYPE_CUSTOMER_SERVICE_IN=9;
    const STATUS_TYPE_CUSTOMER_SERVICE_OVER=10;
    const STATUS_TYPE_COMPLETED=11;
    const STATUS_TYPE_CUSTOMER_UNCOMMENT=12;
    const UNUSUAL_STATUS_UN=0;
    const UNUSUAL_STATUS_HAVE=1;
    const UNUSUAL_STATUS_NO=2;

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
     * @return array
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
        if (!$ordergoodsinformation)
        {
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
        if ($order_information['goods_name']=='+')
        {
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


    /**
     * @param $order_no
     * @param $goods_num
     * @param $time
     * @param $goods
     * @param $order_status
     * @param $shipping_status
     * @param $customer_service
     * @param $is_unusual
     * @param $freight
     * @return int
     */
    public  static  function  AddNewOrderData($order_no,$goods_num,$time,$goods,$order_status,$shipping_status,$customer_service,$is_unusual,$freight)
    {
        $LogisTemp=LogisticsTemplate::find()
            ->select('delivery_method')
            ->where(['id'=>$goods['logistics_template_id']])
            ->one();
        $tran = Yii::$app->db->beginTransaction();
        try {
            $OrderGoods=new self();
            $OrderGoods->order_no=$order_no;
            $OrderGoods->goods_id=$goods['id'];
            $OrderGoods->goods_number=$goods_num;
            $OrderGoods->create_time=$time;
            $OrderGoods->goods_name=$goods['title'];
            $OrderGoods->goods_price=$goods['platform_price'];
            $OrderGoods->sku=$goods['sku'];
            $OrderGoods->market_price=$goods['market_price'];
            $OrderGoods->supplier_price=$goods['supplier_price'];
            $OrderGoods->shipping_type=$LogisTemp->delivery_method;
            $OrderGoods->cover_image=$goods['cover_image'];
            $OrderGoods->order_status=$order_status;
            $OrderGoods->shipping_status=$shipping_status;
            $OrderGoods->customer_service=$customer_service;
            $OrderGoods->is_unusual=$is_unusual;
            $OrderGoods->freight=$freight;
            $OrderGoods->category_id=$goods['category_id'];
            $OrderGoods->after_sale_services=$goods['after_sale_services'];
            $OrderGoods->platform_price=$goods['platform_price'];
            $OrderGoods->purchase_price_decoration_company=$goods['purchase_price_decoration_company'];
            $OrderGoods->purchase_price_manager=$goods['purchase_price_manager'];
            $OrderGoods->purchase_price_designer=$goods['purchase_price_designer'];
            $OrderGoods->subtitle=$goods['subtitle'];
            $res1=$OrderGoods->save(false);
            if (!$res1) {
                $tran->rollBack();
                return 500;
            }
            $tran->commit();
            return 200;
        }catch (\Exception $e) {
            $tran->rollBack();
            return 500;
        }
    }


    /**
     * @param $user
     * @return array
     */
    public  static  function  FindOrderNumBer($user)
    {
        if ($user->last_role_id_app==6)
        {
            $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
            $data = (new Query())
                ->select("(SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.supplier_id={$supplier->id}) as all_order ,
            (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=0 and o.order_status=0  and g.supplier_id={$supplier->id}) as unpaid,
            (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=1 and o.order_status=0 and shipping_status=0  and g.supplier_id={$supplier->id}) as unshipped,
            (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=1 and o.order_status=0 and shipping_status=1  and g.supplier_id={$supplier->id}) as unreceived,
            (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=1 and o.order_status=1 and shipping_status=2  and g.supplier_id={$supplier->id} and o.customer_service=0)  as completed,
            (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=1 and o.order_status=1 and shipping_status=2  and g.supplier_id={$supplier->id} and o.customer_service=0 and o.comment_id=0 )  as uncomment,
            (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where o.order_status=2 and o.customer_service=0  and g.supplier_id={$supplier->id}   )as canceled,
            (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where o.order_status=1  and  o.customer_service!=0  and g.supplier_id={$supplier->id} )  as customer_service" )
                ->one();
        }else
        {
            $data=(new Query())->select("
               (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.user_id={$user->id} and g.role_id = {$user->last_role_id_app}) as all_order ,
               (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=0 and o.order_status=0  and  g.user_id={$user->id} and g.role_id = {$user->last_role_id_app}) as unpaid ,
               (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=1 and o.order_status=0 and shipping_status=0  and  g.user_id={$user->id} and g.role_id = {$user->last_role_id_app})  as unshipped ,
               (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=1 and o.order_status=0 and shipping_status=1  and  g.user_id={$user->id} and g.role_id = {$user->last_role_id_app})  as unreceived ,
               (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=1 and o.order_status=1 and shipping_status=2  and  g.user_id={$user->id} and g.role_id = {$user->last_role_id_app} and o.customer_service=0)  as completed ,
               (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where g.pay_status=1 and o.order_status=1 and shipping_status=2  and g.user_id={$user->id} and g.role_id = {$user->last_role_id_app} and o.customer_service=0 and o.comment_id=0)   as uncomment ,
               (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where o.order_status=2 and o.customer_service=0  and  g.user_id={$user->id} and g.role_id = {$user->last_role_id_app} ) as canceled ,
               (SELECT count(*)  FROM  " .GoodsOrder::tableName()." as g "." LEFT JOIN  ".OrderGoods::tableName()." as o on g.order_no=o.order_no where o.order_status=1  and  o.customer_service!=0  and  g.user_id={$user->id} and g.role_id = {$user->last_role_id_app})  as customer_service ")->one();
        }

        $userNews=UserNewsRecord::find()
            ->where(['uid'=>$user->id])
            ->andWhere(['role_id'=>$user->last_role_id_app])
            ->andWhere(['status'=>0])
            ->count();

        return [
                'all'=>$data['all_order']>99?99:$data['all_order'],
                'unpaid'=>$data['unpaid']>99?99:$data['unpaid'],
                'unshipped'=>$data['unshipped']>99?99:$data['unshipped'],
                'unreceiveed'=>$data['unreceived']>99?99:$data['unreceived'],
                'completed'=>$data['completed']>99?99:$data['completed'],
                'canceled'=>$data['canceled']>99?99:$data['canceled'],
                'customer_service'=>$data['customer_service']>99?99:$data['customer_service'],
                'have_read_news'=>$userNews>0?1:2,
                'uncomment'=>$data['uncomment']>99?99:$data['uncomment']
            ];
    }



    
}