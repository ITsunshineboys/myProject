<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;
use yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\data\Pagination;
use app\services\StringService;
use app\services\SmValidationService;
use app\services\ModelService;
class GoodsOrder extends ActiveRecord
{

    const PAY_STATUS_PAID = 1;
    const PAY_STATUS_DESC_UNPAID = '待付款';
    const SHIPPING_STATUS_DESC_UNSHIPPED='待发货';
    const SHIPPING_STATUS_DESC_SHIPPED='已发货';
    const ORDER_STATUS_CANCEL=2;
    const UNUSUAL_STATUS_REFUND=1;
    const REFUND_HANDLE_STATUS_AGREE=1;
    const REFUND_HANDLE_STATUS_DISAGREE=2;
    const PAGE_SIZE_DEFAULT = 12;
    const ORDER_TYPE_DESC_ALL='全部';
    const ORDER_TYPE_DESC_UNPAID='待付款';
    const ORDER_TYPE_DESC_UNSHIPPED='待发货';
    const ORDER_TYPE_DESC_UNRECEIVED='待收货';
    const ORDER_TYPE_DESC_COMPLETED='已完成';
    const ORDER_TYPE_DESC_CANCEL='已取消';
    const ORDER_TYPE_DESC_CUSTOMER_SERVICE='售后';
    const ORDER_TYPE_DESC_UNCOMMENT='待评论';
    const ORDER_TYPE_DESC_APPLYREFUND='申请退款';
    const ORDER_TYPE_APPLYREFUND='apply_refund';
    const ORDER_TYPE_ALL='all';
    const ORDER_TYPE_UNPAID='unpaid';
    const ORDER_TYPE_UNSHIPPED='unshipped';
    const ORDER_TYPE_SHIPPED='shipped';
    const ORDER_TYPE_UNRECEIVED='unreceived';
    const ORDER_TYPE_COMPLETED='completed';
    const ORDER_TYPE_CANCEL='cancel';
    const ORDER_TYPE_CUSTOMER_SERVICE='customer_service';
    const ORDER_TYPE_UNCOMMENT='uncomment';
    const ORDER_TYPE_LIST=[
        self::ORDER_TYPE_DESC_ALL=>self::ORDER_TYPE_ALL,
        self::ORDER_TYPE_DESC_UNPAID=>self::ORDER_TYPE_UNPAID,
        self::ORDER_TYPE_DESC_UNSHIPPED=>self::ORDER_TYPE_UNSHIPPED,
        self::ORDER_TYPE_DESC_UNRECEIVED=>self::ORDER_TYPE_UNRECEIVED,
        self::ORDER_TYPE_DESC_COMPLETED=>self::ORDER_TYPE_COMPLETED,
        self::ORDER_TYPE_DESC_CANCEL=>self::ORDER_TYPE_CANCEL,
        self::ORDER_TYPE_DESC_CUSTOMER_SERVICE=>self::ORDER_TYPE_CUSTOMER_SERVICE,
        self::ORDER_TYPE_DESC_UNCOMMENT=>self::ORDER_TYPE_UNCOMMENT
    ];
    const FIELDS_ORDERLIST_ADMIN = [
        'a.order_no',
        'a.id',
        'z.customer_service',
        'a.pay_status',
        'a.address_id',
        'z.order_status',
        'a.create_time',
        'a.user_id',
        'z.shipping_status',
        'a.amount_order',
        'z.goods_name',
        'z.goods_price',
        'z.goods_number',
        'z.is_unusual',
        'z.market_price',
        'z.supplier_price',
        'z.sku',
        'z.order_id',
        'z.comment_id',
        'a.order_refer',
        'z.freight',
        'a.return_insurance',
        'a.consignee',
        'a.consignee_mobile',
        'a.order_refer',
        'a.role_id',
        'z.shipping_type',
        'u.mobile'
    ];
    const FIELDS_USERORDER_ADMIN = [
        'a.supplier_id',
        'a.order_no',
        'z.customer_service',
        'a.pay_status',
        'z.order_status',
        'a.create_time',
        'a.user_id',
        'z.shipping_status',
        'a.amount_order',
        'a.pay_name',
        'a.buyer_message',
        'a.order_refer',
        'a.paytime',
        'z.goods_name',
        'z.goods_price',
        'z.goods_number',
        'z.is_unusual',
        'z.market_price',
        'z.supplier_price',
        'z.sku',
        'z.order_id',
        'z.comment_id',
        'z.freight',
        'a.return_insurance',
        'z.cover_image',
        'z.shipping_type',
        'a.role_id'
    ];
    const REMIND_SEND_GOODS='remind_send_goods_';
    const PLAT_MONEY='platform_price';
    const WORKER_MONEY='platform_price';
    const MANAGER_MONEY='purchase_price_manager';
    const DESIGNER_MONEY='purchase_price_designer';
    const COMPANEY_MONEY='purchase_price_decoration_company';
    const SUPPLIER_MONEY='supplier_price';
    const STATUS_DESC_DETAILS=1;


    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_order';
    }
    /**
     * Get total amount order
     *
     * @param int $startTime start time
     * @param int $endTime end time
     * @param int $supplierId supplier id default 0
     * @return int
     */
    public static function totalAmountOrder($startTime, $endTime, $supplierId = 0)
    {
        $query = self::find()
            ->select('sum(amount_order) as total_amount_order')
            ->where(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime]);
        $supplierId > 0 && $query->andWhere(['supplier_id' => $supplierId]);
        return (int)$query->asArray()->all()[0]['total_amount_order'];
    }
    /**
     * Get total order number
     *
     * @param int $startTime start time
     * @param int $endTime end time
     * @param int $supplierId supplier id default 0
     * @return int
     */
    public static function totalOrderNumber($startTime, $endTime, $supplierId = 0)
    {
        $query = self::find()
            ->where(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime]);
        $supplierId > 0 && $query->andWhere(['supplier_id' => $supplierId]);

        return (int)$query->count();
    }

    /**
     * @param $order_no
     * @return array|null|ActiveRecord
     */
    public  static  function  FindByOrderNo($order_no)
    {
        $data=self::find()
            ->where(['order_no'=>$order_no])
            ->one();
        return $data;
    }


    /**
     * 支付宝线下商城数据库操作
     * @param $arr
     * @param $post
     * @return bool
     */
    public static function Alipaylinenotifydatabase($arr,$post)
    {
        $goods_id=$arr[0];
        $goods_num=$arr[1];
        $address_id=$arr[2];
        $pay_name=$arr[3];
        $invoice_id=$arr[4];
        $supplier_id=$arr[5];
        $freight=$arr[6];
        $return_insurance=$arr[7];
        $buyer_message=$arr[8];
        $goods=(new Query())
            ->from(Goods::tableName().' as a')
            ->where(['a.id'=>$goods_id])
            ->leftJoin(LogisticsTemplate::tableName().' as b','b.id=a.logistics_template_id')
            ->one();
        if (($freight*100+$return_insurance*100+$goods['platform_price']*$goods_num)!=$post['total_amount']*100){
            return false;
        }
        $post['total_amount']=$freight*100+$return_insurance*100+$goods['platform_price']*$goods_num;
        $address=Addressadd::findOne($address_id);
        $invoice=Invoice::findOne($invoice_id);
        if (! $address  || !$invoice){
            return false;
        }
        $time=time();
        $tran = Yii::$app->db->beginTransaction();
        try{
            $goods_order=new self();
            $goods_order->order_no=$post['out_trade_no'];
            $goods_order->amount_order=$post['total_amount'];
            $goods_order->supplier_id=$goods['supplier_id'];
            $goods_order->invoice_id=$invoice_id;
            $goods_order->pay_status=1;
            $goods_order->create_time=strtotime($post['gmt_create']);
            $goods_order->paytime=strtotime($post['gmt_payment']);
            $goods_order->order_refer=1;
            $goods_order->return_insurance=$return_insurance*100;
            $goods_order->pay_name=$pay_name;
            $goods_order->buyer_message=$buyer_message;
            $goods_order->consignee=$address->consignee;
            $goods_order->district_code=$address->district;
            $goods_order->region=$address->region;
            $goods_order->consignee_mobile=$address->mobile;
            $goods_order->invoice_type=$invoice->invoice_type;
            $goods_order->invoice_header_type=$invoice->invoice_header_type;
            $goods_order->invoicer_card=$invoice->invoicer_card;
            $goods_order->invoice_header=$invoice->invoice_header;
            $goods_order->invoice_content=$invoice->invoice_content;
            $res1=$goods_order->save(false);
            if (!$res1){
                $tran->rollBack();
                return false;
            }
            $OrderGoods=new OrderGoods();
            $OrderGoods->order_no=$post['out_trade_no'];
            $OrderGoods->goods_id=$goods['id'];
            $OrderGoods->goods_number=$goods_num;
            $OrderGoods->create_time=strtotime($post['gmt_create']);
            $OrderGoods->goods_name=$goods['title'];
            $OrderGoods->goods_price=$goods['platform_price'];
            $OrderGoods->sku=$goods['sku'];
            $OrderGoods->market_price=$goods['market_price'];
            $OrderGoods->supplier_price=$goods['supplier_price'];
            $OrderGoods->shipping_type=$goods['delivery_method'];
            $OrderGoods->cover_image=$goods['cover_image'];
            $OrderGoods->order_status=0;
            $OrderGoods->shipping_status=0;
            $OrderGoods->customer_service=0;
            $OrderGoods->is_unusual=0;
            $OrderGoods->freight=$freight*100;
            $res2=$OrderGoods->save(false);

            if (!$res2){
                $tran->rollBack();
                return false;
            }
            $time=time();
            $month=date('Ym',$time);
            $supplier=Supplier::find()
                ->where(['id'=>$goods['supplier_id']])
                ->one();
            $supplier->sales_volumn_month=$supplier->sales_volumn_month+$goods_num;
            $supplier->sales_amount_month=$supplier->sales_amount_month+$goods['platform_price']*$goods_num;
            $supplier->month=$month;
            $res3=$supplier->save(false);
            if (!$res3){
                $tran->rollBack();
                return false;
            }
            $date=date('Ymd',time());
            $GoodsStat=GoodsStat::find()
                ->where(['supplier_id'=>$goods['supplier_id']])
                ->andWhere(['create_date'=>$date])
                ->one();
            if (!$GoodsStat)
            {
                $GoodsStat=new GoodsStat();
                $GoodsStat->supplier_id=$goods['supplier_id'];
                $GoodsStat->sold_number=$goods_num;
                $GoodsStat->amount_sold=$post['total_amount'];
                $GoodsStat->create_date=$date;
                if (!$GoodsStat->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
            }else{
                $GoodsStat->sold_number+=$goods_num;
                $GoodsStat->amount_sold+=$post['total_amount'];
                if (!$GoodsStat->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
            }
            $Goods=Goods::findOne($goods_id);
            $Goods->left_number-=$goods_num;
            $Goods->sold_number+=$goods_num;
            if (!$Goods->save(false))
            {
                $tran->rollBack();
                return false;
            }
            $tran->commit();
        }catch (\Exception $e) {
            $tran->rollBack();
            return false;
        }
        $sms['mobile']=$address->mobile;
        $sms['type']='gotOrder';
        $sms['goods_title']=$goods['title'];
        $sms['order_no']=$post['out_trade_no'];
        $sms['recipient']=$address->consignee;
        $sms['phone_number']=$address->mobile;
        new SmValidationService($sms);
        return true;
    }


    /**
     * 微信线下商城数据库操作
     * @param $arr
     * @param $msg
     * @return bool
     */
    public static function  Wxpaylinenotifydatabase($arr,$msg)
    {
        $goods_id=$arr[0];
        $goods_num=$arr[1];
        $address_id=$arr[2];
        $pay_name=$arr[3];
        $invoice_id=$arr[4];
        $supplier_id=$arr[5];
        $freight=$arr[6];
        $return_insurance=$arr[7];
        $order_no=$arr[8];
        $buyer_message=$arr[9];
//        $client_ip=StringService::getClientIP();
        $goods=(new Query())
            ->from(Goods::tableName().' as a')
            ->where(['a.id'=>$goods_id])
            ->leftJoin(LogisticsTemplate::tableName().' as b','b.id=a.logistics_template_id')
            ->one();
//        +$return_insurance*100
         if (($freight*100+$goods['platform_price']*$goods_num)!=$msg['total_fee']){
             return false;
         }
        $address=Addressadd::findOne($address_id);
        $invoice=Invoice::findOne($invoice_id);
        if (! $address  || !$invoice){
            return false;
        }
        $time=time();
        $tran = Yii::$app->db->beginTransaction();
        try{

            $goods_order=new self();
            $goods_order->order_no=$order_no;
            $goods_order->amount_order=$msg['total_fee'];
            $goods_order->supplier_id=$goods['supplier_id'];
            $goods_order->invoice_id=$invoice_id;
            $goods_order->address_id=$address_id;
            $goods_order->pay_status=1;
            $goods_order->create_time=$time;
            $goods_order->paytime=$time;
            $goods_order->order_refer=1;
            $goods_order->return_insurance=$return_insurance*100;
            $goods_order->pay_name=$pay_name;
            $goods_order->buyer_message=$buyer_message;
            $goods_order->consignee=$address->consignee;
            $goods_order->district_code=$address->district;
            $goods_order->region=$address->region;
            $goods_order->consignee_mobile=$address->mobile;
            $goods_order->invoice_type=$invoice->invoice_type;
            $goods_order->invoice_header_type=$invoice->invoice_header_type;
            $goods_order->invoicer_card=$invoice->invoicer_card;
            $goods_order->invoice_header=$invoice->invoice_header;
            $goods_order->invoice_content=$invoice->invoice_content;
            $res1=$goods_order->save(false);
            if (!$res1){
                $tran->rollBack();
                return false;
            }
            //关闭
            $OrderGoods=new OrderGoods();
            $OrderGoods->order_no=$order_no;
            $OrderGoods->goods_id=$goods['id'];
            $OrderGoods->goods_number=$goods_num;
            $OrderGoods->create_time=time();
            $OrderGoods->goods_name=$goods['title'];
            $OrderGoods->goods_price=$goods['platform_price'];
            $OrderGoods->sku=$goods['sku'];
            $OrderGoods->market_price=$goods['market_price'];
            $OrderGoods->supplier_price=$goods['supplier_price'];
            $OrderGoods->shipping_type=$goods['delivery_method'];
            $OrderGoods->cover_image=$goods['cover_image'];
            $OrderGoods->order_status=0;
            $OrderGoods->shipping_status=0;
            $OrderGoods->customer_service=0;
            $OrderGoods->is_unusual=0;
            $OrderGoods->freight=$freight*100;
            $res2=$OrderGoods->save(false);
            if (!$res2){
                $tran->rollBack();
                return false;
            }
            $time=time();
            $month=date('Ym',$time);
            $supplier=Supplier::find()
                ->where(['id'=>$goods['supplier_id']])
                ->one();
            $supplier->sales_volumn_month=$supplier->sales_volumn_month+$goods_num;
            $supplier->sales_amount_month=$supplier->sales_amount_month+$goods['platform_price']*$goods_num;
            $supplier->month=$month;
            $res3=$supplier->save(false);
            if (!$res3){
                $tran->rollBack();
                return false;
            }
            $date=date('Ymd',time());
            $GoodsStat=GoodsStat::find()
                ->where(['supplier_id'=>$goods['supplier_id']])
                ->andWhere(['create_date'=>$date])
                ->one();
            if (!$GoodsStat)
            {
                $GoodsStat=new GoodsStat();
                $GoodsStat->supplier_id=$goods['supplier_id'];
                $GoodsStat->sold_number=$goods_num;
                $GoodsStat->amount_sold=$msg['total_fee'];
                $GoodsStat->create_date=$date;
                if (!$GoodsStat->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
            }else{
                $GoodsStat->sold_number+=$goods_num;
                $GoodsStat->amount_sold+=$msg['total_fee'];
                if (!$GoodsStat->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
            }
            $Goods=Goods::findOne($goods_id);
            $Goods->left_number-=$goods_num;
            $Goods->sold_number+=$goods_num;
            if (!$Goods->save(false))
            {
                $tran->rollBack();
                return false;
            }
            $tran->commit();
        }catch (\Exception $e) {
            $tran->rollBack();
            return false;
        }
        $sms['mobile']=$address->mobile;
        $sms['type']='gotOrder';
        $sms['goods_title']=$goods['title'];
        $sms['order_no']=$order_no;
        $sms['recipient']=$address->consignee;
        $sms['phone_number']=$address->mobile;
        new SmValidationService($sms);
//        $supplier=Supplier::findOne($supplier_id);
//        $supplier_user=User::findOne($supplier->uid);
//        $title='新订单';
//        $role_id=6;
//        $content="订单号{$order_no},{$goods['title']}";
//        $sku=$goods['sku'];
//        $code=UserNewsRecord::AddOrderNewRecord($supplier_user, $title, $role_id, $content, $order_no, $sku, 1);
//        if (!$code==200)
//        {
//            return false;
//        }
        return true;
    }

    /**
     * @param array $where
     * @param array $select
     * @param int $page
     * @param int $size
     * @param $sort_time
     * @param $sort_money
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $sort_time,$sort_money,$type)
    {

        $sort='';
        if ($sort_time=='' && $sort_money==2)
        {
            $sort="  (z.goods_price*z.goods_number+z.freight) desc";
        }
        if (!$sort_time && $sort_money==1)
        {
            $sort="  (z.goods_price*z.goods_number+z.freight) asc";
        }
        if (!$sort_money && $sort_time==2)
        {
            $sort='a.create_time desc';
        }
        if (!$sort_money && $sort_time==1)
        {
            $sort='a.create_time asc';
        }
        if (!$sort)
        {
            $sort=' a.create_time desc';
        }
        $offset = ($page - 1) * $size;
        $OrderList = (new Query())
            ->from(self::tableName().' AS a')
            ->leftJoin(OrderGoods::tableName().' AS z','z.order_no = a.order_no')
            ->leftJoin(User::tableName(). ' AS u','u.id=a.user_id')
            ->select($select)
            ->where($where)
            ->offset($offset)
            ->orderBy($sort)
            ->limit($size)
            ->all();
        $arr=self::getorderstatus($OrderList);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            $arr[$k]['handle']='';
            switch ($type)
            {
                case 'supplier':
                    if($arr[$k]['status']=='待发货')
                    {
                        $arr[$k]['handle']='发货';
                    }
                    break;
                case 'lhzz':
                    if($arr[$k]['status']=='待发货' || $arr[$k]['status']=='售后中'|| $arr[$k]['status']=='售后结束' || $arr[$k]['status']=='待收货' || $arr[$k]['status']=='已完成')
                    {
                        $arr[$k]['handle']='平台介入';
                    }
                    break;
            }
//            $arr[$k]['amount_order']=sprintf('%.2f', (float)$arr[$k]['amount_order']*0.01);
            $arr[$k]['amount_order']=sprintf('%.2f', (float)$arr[$k]['goods_price']*0.01*$arr[$k]['goods_number']+$arr[$k]['freight']*0.01);
            $arr[$k]['goods_price']=sprintf('%.2f', (float)$arr[$k]['goods_price']*0.01*$arr[$k]['goods_number']);
            $arr[$k]['market_price']=sprintf('%.2f', (float)$arr[$k]['market_price']*0.01*$arr[$k]['goods_number']);
            $arr[$k]['supplier_price']=sprintf('%.2f', (float)$arr[$k]['supplier_price']*0.01*$arr[$k]['goods_number']);
            switch ($arr[$k]['order_refer'])
            {
                case 1:
                    $arr[$k]['mobile']=$arr[$k]['consignee_mobile'];
                    break;
                case 2:
                    $arr[$k]['mobile']=User::find()
                        ->select('mobile')
                        ->where(['id'=>$arr[$k]['user_id']])
                        ->one()
                        ->mobile;
                    break;
            }
            switch ($arr[$k]['role_id'])
            {
                case 7:
                    $arr[$k]['role_id']='平台价';
                    break;
                case 6:
                    $arr[$k]['role_id']='供应商采购价格';
                    break;
                case 5:
                    $arr[$k]['role_id']='装修公司采购价';
                    break;
                case 4:
                    $arr[$k]['role_id']='项目经理采购价';
                    break;
                case 3:
                    $arr[$k]['role_id']='设计师采购价';
                    break;
                case 2:
                    $arr[$k]['role_id']='工人采购价';
                    break;
            }
            unset($arr[$k]['consignee_mobile']);
            $amount_order[$k]  = $arr[$k]['amount_order'];
            $create_time[$k]  = $arr[$k]['create_time'];
        }

        if ($arr){

            $count=(new Query())
                ->from(self::tableName().' AS a')
                ->leftJoin(OrderGoods::tableName().' AS z','z.order_no = a.order_no')
                ->leftJoin(User::tableName(). ' AS u','u.id=a.user_id')
                ->select($select)
                ->where($where)->count();
            $total_page=ceil($count/$size);
            //            $data=array_slice($arr, ($page-1)*$size,$size);
            return [
                'total_page' =>$total_page,
                'count'=>$count,
                'details' => $arr
            ];
        }else{
            return [
                'total_page' =>0,
                'count'=>0,
                'details' => []
            ];
        }

    }

    
    /**
     * @param $goods_id
     * @param $goods_num
     * @return array|bool
     */
    public static  function Getlinegoodsdata($goods_id,$goods_num){
        $array  =(new Query())
            ->from(Goods::tableName().' AS a')
            ->select('a.supplier_id,a.title,a.subtitle,b.shop_name,c.name,a.logistics_template_id,a.platform_price,a.market_price,a.cover_image,b.icon,c.name,a.sku')
            ->leftJoin(Supplier::tableName().' AS b', 'b.id = a.supplier_id')
            ->leftJoin(GoodsBrand::tableName().' AS c','c.id = a.brand_id')->where(['a.id' =>$goods_id])
            ->where(['a.id'=>$goods_id])
            ->one();
        if(!$array)
        {
            $code=1000;
            return $code;
        }
        $logistics_template=(new Query())
            ->from(LogisticsTemplate::tableName())
            ->select('supplier_id,delivery_method,delivery_cost_default,delivery_number_default,delivery_cost_delta,delivery_number_delta,status')
            ->where(['status'=>1,'id'=>$array['logistics_template_id']])
            ->one();
        if ($logistics_template['delivery_method']==1){
            $array['freight']=0;
        }else{
            if ($goods_num<=$logistics_template['delivery_number_default'])
            {
                $array['freight']=$logistics_template['delivery_cost_default']*0.01;
            }else{
                if ($logistics_template['delivery_number_delta']==0)
                {
                    $logistics_template['delivery_number_delta']=1;
                }
                $array['freight']=$logistics_template['delivery_cost_default']*0.01+$logistics_template['delivery_cost_delta']*0.01*ceil(($goods_num-$logistics_template['delivery_number_default'])/$logistics_template['delivery_number_delta']);
            }
        }
        $array['goods_num']=$goods_num;
        $array['return_insurance']=0.00;
        $array['platform_price']=self::switchMoney($array['platform_price']*0.01);
        $array['market_price']=self::switchMoney($array['market_price']*0.01);
        $array['freight']=self::switchMoney($array['freight']);
        $array['allCost']=self::switchMoney($array['platform_price']*$goods_num+$array['freight']);
        return $array;
    }



    /**
     * @param $order_no
     * @param $sku
     * @return array|null
     */
    public static function Getorderinformation($order_no,$sku){
        $select='a.pay_name,
               z.order_status,
               z.customer_service,
               z.shipping_status,
               a.pay_status,
               a.create_time,
               a.user_id,
               a.address_id,
               z.goods_name,
               a.amount_order,
               z.goods_number,
               z.freight,
               a.order_no,
               a.create_time,
               a.paytime,
               a.user_id,
               a.address_id,
               a.return_insurance,
               z.goods_id,
               z.goods_attr_id,
               z.sku,
               a.address_id,
               a.invoice_id,
               supplier_price,
               z.market_price,
               b.waybillnumber,
               b.waybillname,
               z.shipping_type,
               z.order_id,
               z.goods_price,
               a.order_refer,
               a.buyer_message,
               z.comment_id,
               a.consignee,
               a.district_code,
               a.region,
               a.consignee_mobile,
               a.invoice_type,
               a.invoice_header_type,
               a.invoice_header,
               a.invoicer_card,
               a.invoice_content,
               z.cover_image,
               a.role_id,
               z.is_unusual';
        $array=self::getorderlist()
            ->leftJoin(Express::tableName().' AS b','b.order_no =a.order_no and b.sku=z.sku')
            ->select($select)
            ->where(['a.order_no'=>$order_no,'z.sku'=>$sku])
            ->all();
        $arr=self::getorderstatus($array);
        if(!$arr){
            return null;
        }
        $output=array();
        $goods_num=0;
        foreach($arr as $k=>$v){
            $output['amount_order']=self::switchMoney(($arr[$k]['goods_price']*$arr[$k]['goods_number']+$arr[$k]['freight'])*0.01);
            $output['return_insurance']=self::switchMoney($arr[$k]['return_insurance']);
            $output['freight']=self::switchMoney($arr[$k]['freight']*0.01);
            $output['address_id']=$arr[$k]['address_id'];
            $output['invoice_id']=$arr[$k]['invoice_id'];
            $output['role_id']=$arr[$k]['role_id'];
            $output['goods_number']=$arr[$k]['goods_number'];
            $output['goods_price']=self::switchMoney($arr[$k]['goods_price']*0.01*$arr[$k]['goods_number']);
            $output['supplier_price']=self::switchMoney($arr[$k]['supplier_price']*0.01*$arr[$k]['goods_number']);
            $output['market_price']=self::switchMoney($arr[$k]['market_price']*0.01*$arr[$k]['goods_number']);
            $output['order_no']=$arr[$k]['order_no'];
            $output['buyer_message']=$arr[$k]['buyer_message'];
            $output['create_time']=$arr[$k]['create_time'];
            if ($arr[$k]['complete_time']==0){
                $output['complete_time']=$arr[$k]['complete_time'];
            }else{
                $output['complete_time']=date('Y-m-d H:i',$arr[$k]['complete_time']);
            }
            $output['pay_name']=$arr[$k]['pay_name'];
            $output['paytime']=date('Y-m-d H:i:s',$arr[$k]['paytime']);
            $output['status']=$arr[$k]['status'];
            $output['goods_attr_id']=$arr[$k]['goods_attr_id'];
            $output['order_no']=$arr[$k]['order_no'];
            $output['goods_id']=$arr[$k]['goods_id'];
            $output['sku']=$arr[$k]['sku'];
            $output['goods_name']=$arr[$k]['goods_name'];
            $output['waybillnumber']=$arr[$k]['waybillnumber'];
            $output['waybillname']=$arr[$k]['waybillname'];

            if (!empty($output['waybillnumber']) && !empty($output['waybillnumber']))
            {
                $output['shipping_way']=$output['waybillname'].'('.$output['waybillnumber'].')';
            }else{
                $output['shipping_way']='';
            }
            $output['shipping_type']=$arr[$k]['shipping_type'];
            $output['consignee']=$arr[$k]['consignee'];
            $output['consignee_mobile']=$arr[$k]['consignee_mobile'];
            $output['district_code']=$arr[$k]['district_code'];
            $output['region']=$arr[$k]['region'];
            $output['invoice_type']=$arr[$k]['invoice_type'];
            $output['invoice_header_type']=$arr[$k]['invoice_header_type'];
            $output['invoice_header']=$arr[$k]['invoice_header'];
            $output['invoicer_card']=$arr[$k]['invoicer_card'];
            $output['invoice_content']=$arr[$k]['invoice_content'];
            $output['cover_image']=$arr[$k]['cover_image'];
            $output['is_unusual']=$arr[$k]['is_unusual'];
            $user=User::find()
                ->where(['id'=>$arr[$k]['user_id']])
                ->asArray()
                ->one();
            $output['username']=$user['nickname'];

            if (empty($output['username'])){
                if ($arr[$k]['order_refer']==1)
                {
                    $output['username']='线下店购买用户';
                    $output['role']='平台';
                }else{
                    $output['username']=(new Query())
                        ->from('user_address')
                        ->select('consignee')
                        ->where(['id'=>$arr[$k]['address_id']])
                        ->one()['consignee'];
                }
            }else{
                $output['role']=(new Query())
                    ->from(UserRole::tableName().' as a')
                    ->select('b.name')
                    ->leftJoin(Role::tableName().' as b','a.role_id=b.id')
                    ->where(['a.user_id'=>$arr[$k]['user_id']])
                    ->one()['name'];
                if (!$output['role']){
                    $output['role']='平台';
                }
            }
            $goods_num+=$arr[$k]['goods_number'];
        }
        $output['goods_num']=$goods_num;
        if ($output['status']=='待付款'){
            $time=time();
            $pay_term=(strtotime($output['create_time'])+24*60*60);
            if (($pay_term-$time)<0){
                $res=Yii::$app->db->createCommand()->update(OrderGoods::tableName(), ['order_status' => 2],'order_no='.$output['order_no'].' and sku='.$output['sku'])->execute();
                $output['pay_term']=0;
                $output['status']='已取消';
            }else{
                $output['pay_term']=$pay_term-$time;
            }
        }
        if ($output['status']=='已取消'){
            $output['pay_term']=0;
        }
        return $output;
    }

    /**
     * 获取配送方式
     * @param $order_no
     * @param $sku
     * @return array|bool
     */
    public  static  function  findshipping_type($order_no,$sku)
    {
        $data=OrderGoods::find()
            ->select('shipping_type')
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->asArray()
            ->one()['shipping_type'];
        if (!$data){
            $data=0;
        }
        return $data;
    }

    /**
     * 去发货
     * @param $sku
     * @param $order_no
     * @param $waybillnumber
     * @param $shipping_type
     * @return int
     */
    public static function Supplierdelivery($sku,$order_no,$waybillnumber,$shipping_type){
        $create_time=time();
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        $trans = \Yii::$app->db->beginTransaction();
        try {
            if($shipping_type==1){
                $OrderGoods->shipping_type=1;
                $OrderGoods->shipping_status=1;
                $res1=$OrderGoods->save(false);
                if(!$res1)
                {
                    $trans->rollBack();
                }
                $express=Express::find()
                    ->where(['sku'=>$sku,'order_no'=>$order_no])
                    ->one();
                if ($express){
                    $express->waybillname='送货上门';
                    $express->create_time=$create_time;
                    $res2 = $express->save(false);
                }else{
                    $express1=new Express();
                    $express1->sku=$sku;
                    $express1->order_no=$order_no;
                    $express1->waybillname='送货上门';
                    $express1->create_time=$create_time;
                    $res2=$express1->save(false);
                }
                if (!$res2)
                {
                    $trans->rollBack();
                }
            }else
            {

                $waybillname=(new Express())->GetExpressName($waybillnumber);
                if (!$waybillname)
                {
                    $waybillname='未知';
                }
                $express=Express::find()
                    ->where(['sku'=>$sku,'order_no'=>$order_no])
                    ->one();
                $OrderGoods->shipping_type=0;
                $OrderGoods->shipping_status=1;
                $res1=$OrderGoods->save(false);
                if (!$res1)
                {
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
                if ($express){
                    $express->waybillname=$waybillname;
                    $express->waybillnumber=$waybillnumber;
                    $express->create_time=$create_time;
                    $res2= $express->save(false);
                    if (!$res2)
                    {
                        $code=500;
                        $trans->rollBack();
                        return $code;
                    }
                }else{
                    $express1=new Express();
                    $express1->sku=$sku;
                    $express1->order_no=$order_no;
                    $express1->waybillname=$waybillname;
                    $express1->waybillnumber=$waybillnumber;
                    $express1->create_time=$create_time;
                    $res2=$express1->save(false);
                    if (!$res2)
                    {
                        $code=500;
                        $trans->rollBack();
                        return $code;
                    }
                }
            }
            $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
            if($GoodsOrder->order_refer==1)
            {
                if ($shipping_type==1)
                {
                    $sms['mobile']=$GoodsOrder->consignee_mobile;
                    $sms['type']='homeDelivery';
                    $sms['goods_title']=$OrderGoods->goods_name;
                    $sms['order_no']=$GoodsOrder->order_no;
                    $sms['recipient']=$GoodsOrder->consignee;
                    $sms['phone_number']=$GoodsOrder->consignee_mobile;
                }else{
                    $sms['mobile']=$GoodsOrder->consignee_mobile;
                    $sms['type']='courierDelivery';
                    $sms['goods_title']=$OrderGoods->goods_name;
                    $sms['order_no']=$GoodsOrder->order_no;
                    $sms['recipient']=$GoodsOrder->consignee;
                    $sms['phone_number']=$GoodsOrder->consignee_mobile;
                    if (!$waybillname)
                    {
                        $waybillname='未知';
                    }
                    $sms['express']=$waybillname;
                    $sms['tracking_no']=$waybillnumber;
                    new SmValidationService($sms);
                }
            }else{
                $user=User::find()->where(['id'=>$GoodsOrder->user_id])->one();

                $content = "订单号{$order_no},{$OrderGoods->goods_name}";
                $record=new UserNewsRecord();
                $record->uid=$user->id;
                $record->role_id=$GoodsOrder->role_id;
                $record->title='您的订单已发货';
                $record->content=$content;
                $record->send_time=time();
                $record->order_no=$order_no;
                $record->sku=$sku;
                if (!$record->save(false))
                {
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
                $registration_id=User::find()
                    ->where(['id'=>GoodsOrder::find()
                        ->select('user_id')
                        ->where(['order_no'=>$order_no])
                        ->asArray()
                        ->one()['user_id']
                    ])
                    ->one()->registration_id;
                $push=new Jpush();
                $extras = [];//推送附加字段的类型
                $m_time = '86400';//离线保留时间
                $receive = ['registration_id'=>[$registration_id]];//设备的id标识
                $title='订单发货了!';
                $content = '你的订单已经发货,点击详情查看!';
                $result = $push->push($receive,$title,$content,$extras, $m_time);
                if (!$result)
                {
                    $code=1000;
                    $trans->rollBack();
                    return $code;
                }
            }
        } catch (Exception $e) {
            $trans->rollBack();
            $code=500;
            return $code;
        }
        $trans->commit();
        $code=200;
        return $code;
    }

    /**
     * 获取商品信息
     * @param $goods_name
     * @param $goods_id
     * @param $goods_attr_id
     * @param $order_no
     * @param $sku
     * @return array
     */
    public  static function Getordergoodsinformation($goods_name,$goods_id,$goods_attr_id,$order_no,$sku){
        $goods=[];
        $goods['goods_name']=$goods_name;
        $goods['goods_id']=$goods_id;
        $attr_id=explode(',',$goods_attr_id);
        $goods['attr']=[];
        foreach($attr_id AS $key =>$val){
            $goods['attr'][]=(object)GoodsAttr::find()
                ->select('name,value,unit')
                ->where(['goods_id'=>$goods['goods_id']])
                ->all();
        }
        return $goods;
    }

    /**
     * 平台介入
     * @param $order_no
     * @param $handle_type
     * @param $reason
     * @return Exception|\Exception|int
     */
    public static  function Platformadd($order_no,$handle_type,$reason,$sku){
        $OrderPlatForm=OrderPlatForm::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->one();
        if ($OrderPlatForm){
            $code=1035;
            return $code;
        }
        switch ($handle_type){
            case  1:
                $code=OrderPlatForm::platformHandleCloseOrderRefundToUser($order_no,$handle_type,$reason,$sku);
                break;
            case  2:
                $code=OrderPlatForm::platformhandle2($order_no,$handle_type,$reason,$sku);
                break;
            case  3:
                $code=OrderPlatForm::platformHandReturnGoods($order_no,$handle_type,$reason,$sku);
                break;
            case  4:
                $code=OrderPlatForm::platformHandReturnGoods($order_no,$handle_type,$reason,$sku);
                break;
            case  5:
                $code=OrderPlatForm::platformHandReturnGoods($order_no,$handle_type,$reason,$sku);
                break;
            case  6:
                $code=OrderPlatForm::platformHandReturnGoods($order_no,$handle_type,$reason,$sku);
                break;
            case  7:
                $code=OrderPlatForm::platformHandReturnGoods($order_no,$handle_type,$reason,$sku);
                break;
        }
        return $code;
    }

    /**
     * 获取平台介入信息
     * @param $order_no
     * @param $sku
     * @return array|bool
     */
    public static  function Getplatformdetail($order_no,$sku){
        $res=(new Query())
            ->from(OrderPlatForm::tableName())
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->one();
        if (!$res){
            return null;
        }
        $res['handle_time']=date('Y-m-d H:i',$res['creat_time']);
        unset($res['creat_time']);
        $res['refund_result']=self::Getplatformresult($res['refund_result']);
        $res['refund_type']=self::Getplatformtype($res['refund_type']);
        $res['refund_time']=date('Y-m-d H:i',$res['refund_time']);
        return $res;
    }

    /**
     * check is correct money
     * 判断是否是正确的金额
     * @param $goods_id
     * @param $total_amount
     * @param $goods_num
     * @param $return_insurance
     * @param $freight
     * @return bool
     */
    public static  function judge_order_money($goods_id,$total_amount,$goods_num,$return_insurance,$freight)
    {
        $goods=Goods::find()->select('platform_price,market_price,supplier_price')->where(['id'=>$goods_id])->asArray()->one();
        $money=$goods['platform_price']*$goods_num+$return_insurance*100+($freight*100);
        $total=$total_amount;
        if ($money*0.01 != $total)
        {
            return false;
        }else
        {
            return true;
        }
    }

    /**
     * @param $refund_type
     * @return string
     */
    public  static  function Getplatformtype($refund_type)
    {
        switch ($refund_type)
        {
            case 1:
                $res='退至顾客钱包 ';
                break;
            case 2:
                $res='线下自行退款';
                break;
            case 3:
                $res='退至支付宝';
                break;
        }
        return $res;
    }

    /**
     * @param $result
     * @return string
     */
    public  static  function Getplatformresult($result){
        switch ($result){
            case 1:
                $res='退款中';
                break;
            case 2:
                $res='成功';
                break;
            case 3:
                $res='失败';
                break;
        }
        return $res;
    }

    /**获取平台介入类型
     * @param $handle
     * @return string
     */
    public  static  function Gethanndletype($handle){
        switch ($handle){
            case 0:
                $res=null;
                break;
            case 1:
                $res='关闭订单退款';
                break;
            case 2:
                $res='关闭订单线下退款';
                break;
            case 3:
                $res='退货';
                break;
            case 4:
                $res='换货';
                break;
            case 5:
                $res='上门维修';
                break;
            case 6:
                $res='上门退货';
                break;
            case 7:
                $res='上门换货';
                break;
        }
        return $res;

    }
    /**
     * 获取后台订单状态
     * @param $data
     * @return mixed
     */
    public static function  getorderstatus($data)
    {
        foreach ($data as $k =>$v){
            $data[$k]['create_time']=date('Y-m-d H:i',$data[$k]['create_time']);
            switch ($data[$k]['order_refer']){
                case 1:
                    $data[$k]['user_name']='线下店购买用户';
                    break;
                case 2:
                    $data[$k]['user_name']=User::find()
                        ->select('nickname')
                        ->where(['id'=>$data[$k]['user_id']])
                        ->asArray()
                        ->one()['nickname'];
                    break;
            }
            if ($data[$k]['pay_status']==0 && $data[$k]['order_status']==0){
                $data[$k]['status']='待付款';
            }else{
                switch ($data[$k]['order_status']){
                    case 0:
                        switch ($data[$k]['shipping_status']){
                            case 0:
                                $data[$k]['status']='待发货';
                                break;
                            case 1:
                                $data[$k]['status']='待收货';
                                break;
                            case 2:
                                $data[$k]['status']='已完成';
                                break;
                        }
                        break;
                    case 1:
                        switch($data[$k]['customer_service']){
                            case 0:
                                $data[$k]['status']='已完成';
                                break;
                            case 1:
                                $data[$k]['status']='售后中';
                                break;
                            case 2:
                                $data[$k]['status']='售后结束';
                                break;
                        }
                        break;
                    case 2:
                        $data[$k]['status']='已取消';
                        break;
                }
            }
            $data[$k]['send_time']=0;
            $data[$k]['complete_time']=0;
            $data[$k]['RemainingTime']=0;
            if ($data[$k]['status']=='待收货'){
                $express=Express::find()
                    ->where(['order_no'=>$data[$k]['order_no'],'sku'=>$data[$k]['sku']])
                    ->one();
                if ($express)
                {
                    $data[$k]['send_time']=$express->create_time;
                    $data[$k]['RemainingTime']=Express::findRemainingTime($express);
                    if ($data[$k]['RemainingTime']<=0){
                        $data[$k]['complete_time']=$express->receive_time;
                        $data[$k]['status']='已完成';
                        $data[$k]['is_unusual']=0;
                        $supplier_id[$k]=self::find()
                            ->select('supplier_id')
                            ->where(['order_no'=>$data[$k]['order_no']])
                            ->asArray()
                            ->one()['supplier_id'];
                        $money[$k]=($data[$k]['freight']+$data[$k]['supplier_price']*$data[$k]['goods_number']);
                        $res[$k]=self::changeOrderStatus($data[$k]['order_no'],$data[$k]['sku'],$supplier_id[$k],$money[$k]);
                        if (!$res || $res==false){
                            return false;
                        }
                    }
                }
            };
            if ($data[$k]['status']=='已完成')
            {
                $express=Express::find()
                    ->where(['order_no'=>$data[$k]['order_no'],'sku'=>$data[$k]['sku']])
                    ->one();
                if (!$express){
                    $data[$k]['send_time']=0;
                    $data[$k]['RemainingTime']=0;
                    $data[$k]['complete_time']=0;
                }else{
                    $data[$k]['send_time']=$express->create_time;
                    $data[$k]['RemainingTime']=Express::findRemainingTime($express);
                    if ($data[$k]['RemainingTime']<0)
                    {
                        $data[$k]['RemainingTime']=0;
                    }
                    $data[$k]['complete_time']=$express->receive_time;
                }
                $data[$k]['is_unusual']=0;
            };
            $data[$k]['comment_grade']=GoodsComment::findCommentGrade($data[$k]['comment_id']);
            $data[$k]['pay_term']=0;
            if ($data[$k]['status']=='待付款'){
                $time=time();
                $pay_term=(strtotime($data[$k]['create_time'])+24*60*60);
                if (($pay_term-$time)<=0){
                    $res=Yii::$app->db
                        ->createCommand()
                        ->update(OrderGoods::tableName(), ['order_status' => 2],'order_no='.$data[$k]['order_no'].' and sku='.$data[$k]['sku'])
                        ->execute();
                    $data[$k]['status']='已取消';
                }else{
                    $data[$k]['pay_term']=$pay_term-$time;
                }
            }
            if ($data[$k]['status']=='已取消')
            {
                $express=Express::find()
                    ->where(['order_no'=>$data[$k]['order_no'],'sku'=>$data[$k]['sku']])
                    ->one();
                if ($express)
                {
                    $data[$k]['complete_time']=$express->receive_time;
                }else{
                    $data[$k]['complete_time']=0;
                }
            }
            unset($data[$k]['customer_service']);
            unset($data[$k]['pay_status']);
            unset($data[$k]['order_status']);
            unset($data[$k]['shipping_status']);
        }
        return $data;
    }

    /**
     * @param $order_no
     * @param $sku
     * @param $supplier_id
     * @param $money
     * @return bool
     */
    public static  function  changeOrderStatus($order_no,$sku,$supplier_id,$money){
        $trans = \Yii::$app->db->beginTransaction();
        $supplier=Supplier::find()
            ->where(['id'=>$supplier_id])
            ->one();
        $time=time();
        $role_number=$supplier->shop_no;
        $transaction_no= self::SetTransactionNo($role_number);
        $orderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        if (!$orderGoods)
        {
            return false;
        }
        try {
            $orderGoods->order_status=1;
            $orderGoods->shipping_status=2;
            $res1=$orderGoods->save(false);
            if (!$res1)
            {
                $trans->rollBack();
                return false;
            }
            $supplier_accessdetail=new UserAccessdetail();
            $supplier_accessdetail->uid=$supplier->uid;
            $supplier_accessdetail->role_id=6;
            $supplier_accessdetail->access_type=6;
            $supplier_accessdetail->access_money=($orderGoods->freight+$orderGoods->supplier_price*$orderGoods->goods_number);
            $supplier_accessdetail->order_no=$order_no;
            $supplier_accessdetail->sku=$sku;
            $supplier_accessdetail->create_time=time();
            $supplier_accessdetail->transaction_no=$transaction_no;
            $res2=$supplier_accessdetail->save(false);
            if (!$res2){
                $trans->rollBack();
                return false;
            }
            $supplier->balance=$supplier->balance+$money;
            $supplier->availableamount=$supplier->availableamount+$money;
            if (!$supplier->save(false))
            {
                $trans->rollBack();
                return false;
            };
            $express=Express::find()
                ->where(['order_no'=>$order_no,'sku'=>$sku])
                ->one();
            if ($express)
            {
                $express->receive_time=time();
                if (!$express->save(false))
                {
                    $trans->rollBack();
                    return false;
                }
            }

            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            return false;
        }
    }

    /**
     * @return $this
     */
    public static function getorderlist()
    {
        $getorderlist  =(new Query())
            ->from(self::tableName().' AS a')
            ->leftJoin(OrderGoods::tableName().' AS z','z.order_no = a.order_no');
        return $getorderlist;
    }




    /**
     * @param $sort_money
     * @param $sort_time
     * @return string
     */
    public static  function sort_lhzz_order($sort_money,$sort_time){
        if ($sort_time==1 && $sort_money==1){
            $sort='a.create_time asc,z.goods_price asc';
        }else if ($sort_time==1 && $sort_money==2){
            $sort='a.create_time asc';
        }
        else if ($sort_time==2 && $sort_money==1){
            $sort='z.goods_price asc';
        }
        else if ($sort_time==2 && $sort_money==2){
            $sort='a.create_time desc,z.goods_price desc';
        }else
        {
            $sort='a.create_time desc,z.goods_price desc';
        }
        return $sort;
    }

    /**
     * Get supplier sales volumn
     *
     * @param int $supplierId supplier id
     * @param string $timeType
     * @return int
     */
    public static function supplierSalesVolumn($supplierId, $timeType)
    {
        list($startTime, $endTime) = StringService::startEndDate($timeType, true);

        $retKeyName = 'sales_volumn';
        $query = new Query;
        $query
            ->select('sum(og.goods_number) as ' . $retKeyName)
            ->from(self::tableName() .' as t')
            ->leftJoin(OrderGoods::tableName() . ' as og', 'og.order_id = t.id')
            ->where(['t.supplier_id' => $supplierId, 't.pay_status' => self::PAY_STATUS_PAID]);
        if ($startTime + $endTime > 0) {
            $query
                ->andWhere(['>=', 't.create_time', $startTime])
                ->andWhere(['<=', 't.create_time', $endTime]);
        }

        return (int)$query->one()[$retKeyName];
    }

    /**
     * Get supplier sales volumn
     *
     * @param int $supplierId supplier id
     * @param string $timeType
     * @return int
     */
    public static function supplierSalesAmount($supplierId, $timeType)
    {
        list($startTime, $endTime) = StringService::startEndDate($timeType, true);

        $retKeyName = 'sales_amount';
        $query = new Query;
        $query
            ->select('sum(og.goods_number * og.goods_price) as ' . $retKeyName)
            ->from(self::tableName() .' as t')
            ->leftJoin(OrderGoods::tableName() . ' as og', 'og.order_id = t.id')
            ->where(['t.supplier_id' => $supplierId, 't.pay_status' => self::PAY_STATUS_PAID]);
        if ($startTime + $endTime > 0) {
            $query
                ->andWhere(['>=', 't.create_time', $startTime])
                ->andWhere(['<=', 't.create_time', $endTime]);
        }

        return (int)$query->one()[$retKeyName];
    }


    /**user apply refund
     * @param $order_no
     * @param $sku
     * @param $apply_reason
     * @param $user
     * @return int
     */
    public static function  applyRefund($order_no,$sku,$apply_reason,$user,$supplier_user)
    {
        $code=self::CheckJurisdiction($order_no,$sku,$user);
        if ($code !=200){
            return $code;
        }
        $refunds=OrderRefund::find()
            ->select('id')
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->andWhere('handle = 0')
            ->one();
        $time=time();
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $order=OrderGoods::find()
                ->where(['order_no'=>$order_no])
                ->andWhere(['sku'=>$sku])
                ->one();
            switch ($order->shipping_status)
            {
                case 0:
                    $shipping_status='unshipped';
                    break;
                case 1:
                    $shipping_status='unreceived';
                    break;
            }
            $order->is_unusual=self::UNUSUAL_STATUS_REFUND;
            $res2=$order->save(false);
            if (!$res2){
                $code=500;
                $trans->rollBack();
                return $code;
            }
            $title='申请取消订单';
            $content="订单号{$order_no},{$order->goods_name}";
            $code=UserNewsRecord::AddOrderNewRecord($supplier_user,$title,6,$content,$order_no,$sku,self::STATUS_DESC_DETAILS);
            if (!$code==200)
            {
                $trans->rollBack();
                return $code;
            }
            if ($refunds){
                $code=1031;
                $trans->rollBack();
                return $code;
            }else{
                $order_refund=new OrderRefund();
                $order_refund->order_no=$order_no;
                $order_refund->sku=$sku;
                $order_refund->apply_reason=$apply_reason;
                $order_refund->create_time=$time;
                $order_refund->order_type=$shipping_status;
                $res=$order_refund->save(false);
                if (!$res){
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
                $trans->commit();
                $code=200;
                return $code;
            }
        } catch (\Exception $e) {
            $trans->rollBack();
            $code=500;
            return $code;
        }
    }


    /**
     * @param $order_no
     * @param $sku
     * @param $handle
     * @param $handle_reason
     * @return int
     */
    public  static  function RefundHandle($order_no,$sku,$handle,$handle_reason,$user,$supplier)
    {
        if ($handle ==self::REFUND_HANDLE_STATUS_AGREE)
        {
            $code=self::AgreeRefundHandle($order_no,$sku,$handle,$handle_reason,$user,$supplier);
            return $code;
        }
        if ($handle ==self::REFUND_HANDLE_STATUS_DISAGREE){
            $code=self::disAgreeRefundHandle($order_no,$sku,$handle,$handle_reason,$user,$supplier);
            return $code;
        }
    }


    /**
     *
     * ordfer_refund 表字段status不启用
     * @param $order_no
     * @param $sku
     * @param $handle
     * @param $handle_reason
     * @param $user
     * @param $supplier
     * @return int
     */
    public static function  disAgreeRefundHandle($order_no,$sku,$handle,$handle_reason,$user,$supplier)
    {
        $tran = Yii::$app->db->beginTransaction();
        $time=time();
        try{

            $order_goodslist=OrderGoods::find()->where(['order_no'=>$order_no,'sku'=>$sku])->one();
            $order_goodslist->is_unusual=0;
            $res1=$order_goodslist->save(false);
            if (!$res1){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $order_refund=OrderRefund::find()
                ->where(['order_no'=>$order_no,'sku'=>$sku])
                ->one();
            $order_refund->handle=$handle;
            $order_refund->handle_reason=$handle_reason;
            $order_refund->handle_time=$time;
            $res2=$order_refund->save(false);
            if(!$res2){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }

    /**
     * @param $order_no
     * @param $sku
     * @param $handle
     * @param $handle_reason
     * @param $user
     * @param $supplier
     * @return int
     */
    public static function AgreeRefundHandle($order_no,$sku,$handle,$handle_reason,$user,$supplier)
    {
        $time=time();
        $role_number=$supplier->shop_no;
        $transaction_no=GoodsOrder::SetTransactionNo($role_number);
        $tran = Yii::$app->db->beginTransaction();
        try{
            $order_goodslist=OrderGoods::find()
                ->where(['order_no'=>$order_no,'sku'=>$sku])
                ->one();
            $order_goodslist->order_status=self::ORDER_STATUS_CANCEL;
            $res1=$order_goodslist->save();
            if (!$res1){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $supplier->balance=$supplier->balance-$order_goodslist->freight-$order_goodslist->supplier_price*$order_goodslist->goods_number;
            $supplier->availableamount=$supplier->availableamount-$order_goodslist->freight-$order_goodslist->supplier_price*$order_goodslist->goods_number;
            $res2=$supplier->save(false);
            if (!$res2){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $supplier_accessdetail=new UserAccessdetail();
            $supplier_accessdetail->uid=$user->id;
            $supplier_accessdetail->role_id=6;
            $supplier_accessdetail->access_type=2;
            $supplier_accessdetail->access_money=$order_goodslist->freight+$order_goodslist->supplier_price*$order_goodslist->goods_number;
            $supplier_accessdetail->order_no=$order_no;
            $supplier_accessdetail->sku=$sku;
            $supplier_accessdetail->create_time=$time;
            $supplier_accessdetail->transaction_no=$transaction_no;
            $res3=$supplier_accessdetail->save(false);
            if (!$res3){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $order_refund=OrderRefund::find()
                ->where(['order_no'=>$order_no,'sku'=>$sku,'handle'=>0])
                ->one();
            $order_refund->handle=1;
            $order_refund->handle_reason='';
            $order_refund->handle_time=$time;
            $res4=$order_refund->save(false);
            if (!$res4){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }

    /** check user Jurisdiction
     * @param $order_no
     * @param $user
     * @return int
     */
    public static  function CheckJurisdiction($order_no,$sku,$user)
    {
        $order=GoodsOrder::find()
            ->select('user_id')
            ->where(['order_no'=>$order_no])
            ->one();
        $order_goodslist=OrderGoods::find()
            ->select('id,order_status')
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->one();
        if ($order_goodslist['order_status']!=0)
        {
            $code=1034;
            return $code;
        }
        if (!$order || !$order_goodslist)
        {
            $code=1000;
            return $code;
        }
        if ($order->user_id == $user->id){
            $code= 200;
        }
        else{
            $code=1034;
        }
        return $code;
    }

    /**
     *find order type
     * @param $type
     * @return string
     */
    public static function GetTypeWhere($type){
        switch ($type){
            case self::ORDER_TYPE_ALL:
                $where='';
                break;
            case self::ORDER_TYPE_UNPAID:
                $where='a.pay_status=0 and z.order_status=0';
                break;
            case self::ORDER_TYPE_UNSHIPPED:
                $where='a.pay_status=1 and z.order_status=0 and z.shipping_status=0';
                break;
            case self::ORDER_TYPE_UNRECEIVED:
                $where='a.pay_status=1 and z.order_status=0 and z.shipping_status=1';
                break;
            case 'shipped':
                $where='a.pay_status=1 and z.order_status=0 and z.shipping_status=1';
                break;
            case self::ORDER_TYPE_COMPLETED:
                $where='a.pay_status=1 and z.order_status=1 and z.shipping_status=2  and z.customer_service=0';
                break;
            case  self::ORDER_TYPE_CANCEL:
                $where='z.order_status=2';
                break;
            case self::ORDER_TYPE_CUSTOMER_SERVICE:
                $where='z.order_status=1 and z.customer_service!=0';
                break;
            case self::ORDER_TYPE_UNCOMMENT:
                $where='a.pay_status=1 and z.order_status=1 and z.shipping_status=2   and  z.comment_id=0';
                break;
        }
        return $where;
    }
    /**
     * 余额支付
     * @param $postData
     * @param $user
     * @return int
     */
    public  static  function  orderBalanceSub($postData,$user){
        $orders=explode(',',$postData['list']);
        if(!is_array($orders))
        {
            $code=1000;
            return $code;
        }
        if ($postData['total_amount'] > $user->availableamount){
            $code=1033;
            return $code;
        }
        $orderAmount=self::CalculationCost($orders);
        if ($postData['total_amount']*100  != $orderAmount){
            $code=1000;
            return $code;
        };
        $tran = Yii::$app->db->beginTransaction();
        try{

            $role=Role::GetRoleByRoleId($user->last_role_id_app,$user);

            switch ($user->last_role_id_app)
            {
                case 2:
                    $role_number=$role->worker_type_id;
                    break;
                case 3:
                    $role_number=$role->decoration_company_id;
                    break;
                case 4:
                    $role_number=$role->decoration_company_id;
                    break;
                case 5:
                    $role_number=$role->id;
                    break;
                case 6:
                    $role_number=$role->shop_no;
                    break;
                case 7:
                    $role_number=$role->aite_cube_no;
                    break;
            }

            $access=new UserAccessdetail();
            $access->uid=$user->id;
            $access->role_id=$user->last_role_id_app;
            $access->access_type=7;
            $access->access_money=$postData['total_amount']*100;
            $access->create_time=time();
            $access->transaction_no=GoodsOrder::SetTransactionNo($role_number);
            $access->order_no=$orders;
            $res3=$access->save(false);
            if ( !$res3){
                $tran->rollBack();
                $code=500;
                return $code;
            }

            foreach ($orders as $k =>$v){
                $GoodsOrder=self::find()
                    ->where(['order_no'=>$orders[$k]])
                    ->one();
                $OrderGoods=OrderGoods::find()
                    ->where(['order_no'=>$orders[$k]])
                    ->asArray()
                    ->all();
                foreach ($OrderGoods as &$Goods)
                {
                    if ($Goods['order_status']!=0)
                    {
                        $code=1000;
                        return $code;
                    }
                    $date=date('Ymd',time());
                    $GoodsStat=GoodsStat::find()
                        ->where(['supplier_id'=>$GoodsOrder->supplier_id])
                        ->andWhere(['create_date'=>$date])
                        ->one();
                    if (!$GoodsStat)
                    {
                        $GoodsStat=new GoodsStat();
                        $GoodsStat->supplier_id=$GoodsOrder->supplier_id;
                        $GoodsStat->sold_number=(int)$Goods['goods_number'];
                        $GoodsStat->amount_sold=$GoodsOrder->amount_order;
                        $GoodsStat->create_date=$date;
                        if (!$GoodsStat->save(false))
                        {
                            $code=500;
                            $tran->rollBack();
                            return $code;
                        }
                    }else{
                        $GoodsStat->sold_number+=(int)$Goods['goods_number'];
                        $GoodsStat->amount_sold+=$GoodsOrder->amount_order;
                        $GoodsStat->create_date=$date;
                        if (!$GoodsStat->save(false))
                        {
                            $code=500;
                            $tran->rollBack();
                            return $code;
                        }
                    }
                }
                if ( !$GoodsOrder|| $GoodsOrder ->pay_status!=0)
                {
                    $code=1000;
                    $tran->rollBack();
                    return $code;
                }
                $order_money=$GoodsOrder->amount_order;
                $GoodsOrder->pay_status=1;
                $res=$GoodsOrder->save(false);
                if ($user->last_role_id_app==0)
                {
                    $user->last_role_id_app=7;
                    $user=User::find()
                        ->where(['id'=>$user->id])
                        ->one();
                }else{
                    if ($user->last_role_id_app==7)
                    {
                        $user->last_role_id_app=7;
                        $user=User::find()
                            ->where(['id'=>$user->id])
                            ->one();
                    }else{
                        $user=Role::CheckUserRole($user->last_role_id_app)
                            ->where(['uid'=>$user->id])
                            ->one();
                    }
                }
                $user->balance=($user->balance-$order_money);
                $user->availableamount=($user->availableamount-$order_money);
                $res2=$user->save(false);
                if (!$res || !$res2){
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
            }

            $tran->commit();
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }


        $code=200;
        return $code;
    }



    /**
     * @param $orders
     * @return array|int
     */
    public  static  function CalculationCost($orders)
    {
        $orderAmount=0;
        foreach ($orders as $k =>$v){
            $GoodsOrder=GoodsOrder::find()
                ->where(['order_no'=>$orders[$k]])
                ->asArray()
                ->all();
            foreach ($GoodsOrder as $key =>$val){
                $orderAmount+=$GoodsOrder[$key]['amount_order'];
            }
        }
        return $orderAmount;
    }
    /**分页数据
     * @param array $where
     * @param array $select
     * @param int $page
     * @param int $size
     * @param $type
     * @param $user
     * @return array
     */
    public  static  function paginationByUserorderlist($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $type,$user,$role)
    {
        $OrderList = (new Query())
            ->from(self::tableName().' AS a')
            ->leftJoin(OrderGoods::tableName().' AS z','z.order_no = a.order_no')
            ->select($select)
            ->where($where)
            ->all();
        $arr=self::getorderstatus($OrderList);
        $arr=self::findOrderData($arr,$user,$role);
        if ($type=='all' || $type=='unpaid')
        {
            switch ($role){
                case 'supplier':
                    $where='pay_status=0  and supplier_id='.Supplier::find()->select('id')->where(['uid'=>$user->id])->one()->id;
                    break;
                case 'user':
                    $where='pay_status=0  and user_id='.$user->id;
                    break;
            }
            $GoodsOrder=self::find()
                ->select('user_id,role_id,order_no,create_time,pay_status,amount_order,pay_name,buyer_message,order_refer,paytime,supplier_id')
                ->where($where)
                ->asArray()
                ->all();
            foreach ($GoodsOrder AS $k =>$v){
                $GoodsOrder[$k]['amount_order']=sprintf('%.2f', (float) $GoodsOrder[$k]['amount_order']*0.01);
                $GoodsOrder[$k]['create_time']=date('Y-m-d H:i',$GoodsOrder[$k]['create_time']);
                $GoodsOrder[$k]['paytime']=date('Y-m-d H:i',$GoodsOrder[$k]['paytime']);
                $GoodsOrder[$k]['user_name']=$user->nickname;
                $GoodsOrder[$k]['status']='待付款';
                $GoodsOrder[$k]['comment_grade']='';
                $GoodsOrder[$k]['handle']='';
                $sup=Supplier::findOne($GoodsOrder[$k]['supplier_id']);
                $GoodsOrder[$k]['shop_name']=$sup->shop_name;
                if ($role=='user')
                {
                    $GoodsOrder[$k]['uid']=$sup->uid;
                    $GoodsOrder[$k]['to_role_id']=6;
                }else{
                    $GoodsOrder[$k]['uid']=$GoodsOrder[$k]['user_id'];
                    $GoodsOrder[$k]['to_role_id']=$GoodsOrder[$k]['role_id'];
                }

                $GoodsOrder[$k]['list']=OrderGoods::find()
                    ->where(['order_no'=>$GoodsOrder[$k]['order_no']])
                    ->andWhere(['order_status' =>0])
                    ->select('goods_name,goods_price,goods_number,market_price,supplier_price,sku,freight,cover_image,order_status,shipping_type')
                    ->asArray()
                    ->all();
                if($GoodsOrder[$k]['list']==[])
                {
                    unset($GoodsOrder[$k]);
                }else
                {
                    foreach ($GoodsOrder[$k]['list'] as $key =>$val){
                        $GoodsOrder[$k]['list'][$key]['freight']=self::switchMoney($GoodsOrder[$k]['list'][$key]['freight']*0.01);
                        $GoodsOrder[$k]['list'][$key]['goods_price']=self::switchMoney($GoodsOrder[$k]['list'][$key]['goods_price']*0.01);
                        $GoodsOrder[$k]['list'][$key]['market_price']=self::switchMoney($GoodsOrder[$k]['list'][$key]['market_price']*0.01);
                        $GoodsOrder[$k]['list'][$key]['supplier_price']=self::switchMoney($GoodsOrder[$k]['list'][$key]['supplier_price']*0.01);
                        $GoodsOrder[$k]['list'][$key]['unusual']='无异常';
                    }
                    unset($GoodsOrder[$k]['pay_status']);
                    unset($GoodsOrder[$k]['supplier_id']);
                    $arr[]=$GoodsOrder[$k];
                }
            }
        }
        foreach ($arr as $key => $row)
        {
            $arr[$key]['type']=$type;
            $arr[$key]['role']=$role;
            if ($user->last_role_id_app==0)
            {
                $user->last_role_id_app=7;
                $arr[$key]['availableamount']=self::switchMoney($user->availableamount*0.01);
            }else{
                if ($user->last_role_id_app==7)
                {
                    $user->last_role_id_app=7;
                    $arr[$key]['availableamount']=self::switchMoney($user->availableamount*0.01);
                }else{
                    $arr[$key]['availableamount']=self::switchMoney(Role::CheckUserRole($user->last_role_id_app)->where(['uid'=>$user->id])->one()->availableamount*0.01);
                }
            }
            $goods=Goods::find()->where(['sku'=>$arr[$key]['list'][0]['sku']])->one();
            if($goods->after_sale_services=='0' )
            {

                $arr[$key]['is_support_after_sale']=0;
            }else{
                $arr[$key]['is_support_after_sale']=1;
            }
            // if ($arr[$key]['status']!='completed' && $arr[$key]['status']!='uncomment')
            // {
            //     $arr[$key]['is_support_after_sale']=0;
            // }
            $create_time[$key]  = $arr[$key]['create_time'];
        }
        $arr=self::switchStatus($arr,$role);
        if ($arr){
            array_multisort($create_time, SORT_DESC, $arr);
            $count=count($arr);
            $total_page=ceil($count/$size);
            $data=array_slice($arr, ($page-1)*$size,$size);
            return [
                'total_page' =>$total_page,
                'count'=>$count,
                'details' => $data
            ];
        }else{
            return [
                'total_page' =>0,
                'count'=>0,
                'details' => []
            ];
        }
    }
    /**
     * @param $arr
     * @return mixed
     */
    public static function  findOrderData($arr,$user,$role)
    {
        foreach ($arr as $k=>$v){
            if ($arr[$k]['status']=='待付款'){
                unset($arr[$k]);
            }
        }
        foreach ($arr AS $k =>$v){
            $arr[$k]['paytime']=date('Y-m-d H:i',$arr[$k]['paytime']);
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='待发货' || $arr[$k]['status']=='售后中'|| $arr[$k]['status']=='售后结束' || $arr[$k]['status']=='待收货' || $arr[$k]['status']=='已完成'){
                $arr[$k]['handle']='平台介入';
            }
            if ($arr[$k]['status']=='已完成'){
                if (!$arr[$k]['comment_id']){
                    $arr[$k]['status']=self::ORDER_TYPE_DESC_UNCOMMENT;
                }
            }
            $arr[$k]['amount_order']=self::switchMoney(($arr[$k]['goods_price']*$arr[$k]['goods_number']+$arr[$k]['freight'])*0.01);
            $arr[$k]['goods_price']=self::switchMoney($arr[$k]['goods_price']*0.01);
            $arr[$k]['market_price']=self::switchMoney($arr[$k]['market_price']*0.01);
            $arr[$k]['supplier_price']=self::switchMoney($arr[$k]['supplier_price']*0.01);
            $arr[$k]['freight']=self::switchMoney($arr[$k]['freight']*0.01);
            $supplier=Supplier::find()->where(['id'=>$arr[$k]['supplier_id']])->one();
            $arr[$k]['shop_name']=$supplier->shop_name;
            if ($role=='user')
            {

                $arr[$k]['uid']=$supplier->uid;
                $arr[$k]['to_role_id']=6;

            }else
            {

                $arr[$k]['uid']= $arr[$k]['user_id'];
                $arr[$k]['to_role_id']=$arr[$k]['role_id'];
            }
            $arr_list=[];
            $arr_list['goods_name']=$arr[$k]['goods_name'];
            $arr_list['goods_price']=$arr[$k]['goods_price'];
            $arr_list['goods_number']=$arr[$k]['goods_number'];
            $arr_list['market_price']=$arr[$k]['market_price'];
            $arr_list['supplier_price']=$arr[$k]['supplier_price'];
            $arr_list['sku']=$arr[$k]['sku'];
            $arr_list['freight']=$arr[$k]['freight'];
            $arr_list['cover_image']=$arr[$k]['cover_image'];
            $arr_list['unusual']=$arr[$k]['unusual'];
            $arr_list['shipping_type']=$arr[$k]['shipping_type'];
            unset($arr[$k]['goods_name']);
            unset($arr[$k]['goods_price']);
            unset($arr[$k]['goods_number']);
            unset($arr[$k]['market_price']);
            unset($arr[$k]['supplier_price']);
            unset($arr[$k]['sku']);
            unset($arr[$k]['freight']);
            unset($arr[$k]['cover_image']);
            unset($arr[$k]['order_id']);
            unset($arr[$k]['is_unusual']);
            unset($arr[$k]['comment_id']);
            unset($arr[$k]['return_insurance']);
            unset($arr[$k]['send_time']);
            unset($arr[$k]['complete_time']);
            unset($arr[$k]['RemainingTime']);
            unset($arr[$k]['supplier_id']);
            unset($arr[$k]['role_id']);
            $arr[$k]['list']=[$arr_list];
        }
        return $arr;
    }



    /**
     * @param $data
     * @return string
     */
    public  static  function  switchMoney($data)
    {
        $data=sprintf('%.2f', (float)$data);
        return $data;
    }

    /**
     * @param $arr
     * @return mixed
     */
    public static  function switchStatus($arr,$role)
    {
        foreach ($arr as $k =>$v)
        {
            switch ($arr[$k]['status'])
            {
                case self::PAY_STATUS_DESC_UNPAID:
                    $arr[$k]['status']=self::ORDER_TYPE_UNPAID;
                    break;
                case self::SHIPPING_STATUS_DESC_UNSHIPPED:
                    $arr[$k]['status']=self::ORDER_TYPE_UNSHIPPED;
                    if ( $arr[$k]['unusual']=='申请退款')
                    {
                        $arr[$k]['status']=self::ORDER_TYPE_UNSHIPPED.'_'.self::ORDER_TYPE_APPLYREFUND;
                    }
                    break;
                case  self::ORDER_TYPE_DESC_UNRECEIVED:
                    if ($role=='supplier')
                    {
                        $arr[$k]['status']='shipped';
                        if ($arr[$k]['unusual']=='申请退款'){
                            $arr[$k]['status']='shipped'.'_'.self::ORDER_TYPE_APPLYREFUND;
                        }
                    }else{
                        $arr[$k]['status']=self::ORDER_TYPE_UNRECEIVED;
                        if ($arr[$k]['unusual']=='申请退款'){
                            $arr[$k]['status']=self::ORDER_TYPE_UNRECEIVED.'_'.self::ORDER_TYPE_APPLYREFUND;
                        }
                    }
                    break;
                case  self::ORDER_TYPE_DESC_CANCEL:
                    $arr[$k]['status']=self::ORDER_TYPE_CANCEL;
                    break;
                case  '售后中':
                    $arr[$k]['status']='after_saled';
                    break;
                case  '售后结束':
                    $arr[$k]['status']='after_sale_completed';
                    break;
                case self::ORDER_TYPE_DESC_COMPLETED:
                    $arr[$k]['status']=self::ORDER_TYPE_COMPLETED;
                    break;
                case self::ORDER_TYPE_DESC_UNCOMMENT:
                    $arr[$k]['status']=self::ORDER_TYPE_UNCOMMENT;
                    break;
            }
            unset($arr[$k]['unusual']);
        }
        return $arr;
    }


    /**获取订单详情信息
     * @param $postData
     * @param $user
     * @return array|mixed|null
     */
    public  static  function  FindUserOrderDetails($postData,$user)
    {
        $array=self::getorderlist()
            ->leftJoin(Express::tableName().' AS b','b.order_no =a.order_no and b.sku=z.sku')
            ->select('
           a.pay_name,
           a.supplier_id,
           z.order_status,
           z.customer_service,
           z.shipping_status,
           a.pay_status,
           a.create_time,
           a.user_id,
           a.address_id,
           z.goods_name,
           a.amount_order,
           z.goods_number,
           z.freight,
           a.order_no,
           a.create_time,
           a.paytime,
           a.user_id,
           a.role_id,
           a.address_id,
           a.return_insurance,
           z.goods_id,
           z.goods_attr_id,
           z.sku,
           a.address_id,
           a.invoice_id,
           supplier_price,
           z.market_price,
           b.waybillnumber,
           b.waybillname,
           z.shipping_type,
           z.order_id,
           z.goods_price,
           a.order_refer,
           a.buyer_message,
           z.comment_id,
           a.consignee,
           a.district_code,
           a.region,
           a.consignee_mobile,
           a.invoice_type,
           a.invoice_header_type,
           a.invoice_header,
           a.invoicer_card,
           a.invoice_content,
           z.cover_image,
           z.is_unusual');
        if($postData==''){
            $array=[];
        }else{
            $array=$array
                ->where(['a.order_no'=>$postData['order_no']])
                ->all();
        };
        if(!$array){
            return null;
        }
        $arr=self::getorderstatus($array);
        if(!$arr){
            return null;
        }
        return $arr;
    }

    /**获取订单详情信息2
     * @param array $arr
     * @param $user
     * @return mixed
     */
    public static  function GetOrderDetailsData($arr=[],$user)
    {
        $list=[];
        $supplier_price=0;
        $market_price=0;
        $amount_order=0;
        $goods_num=0;
        $freight=0;
        if($arr)
        {
            $arr=self::SwitchStatus_desc($arr,$user);
            foreach ($arr as $k =>$v){
                $amount_order+=($arr[$k]['goods_price']*$arr[$k]['goods_number'])*0.01;
                $supplier_price+=$arr[$k]['supplier_price']*0.01;
                $market_price+=$arr[$k]['market_price']*0.01;
                $freight+=$arr[$k]['freight']*0.01;
                $arr[$k]['return_insurance']=self::switchMoney($arr[$k]['return_insurance']*0.01);
                $arr[$k]['goods_price']=self::switchMoney($arr[$k]['goods_price']*0.01);
                // switch ($arr[$k]['shipping_type']){
                //     case 0:
                //         $arr[$k]['shipping_type']='快递物流';
                //         break;
                //     case 1:
                //         $arr[$k]['shipping_type']='送货上门';
                //         break;
                // }
                $list[$k]['return_insurance']=sprintf('%.2f', (float)$arr[$k]['return_insurance']*0.01);
                $list[$k]['goods_price']=$arr[$k]['goods_price'];
                if ($arr[$k]['send_time']==0){
                    $list[$k]['send_time']=$arr[$k]['send_time'];
                }else{
                    $list[$k]['send_time']=date('Y-m-d H:i',$arr[$k]['send_time']);
                }
                if ($arr[$k]['complete_time']==0){
                    $list[$k]['complete_time']=$arr[$k]['complete_time'];
                }else{
                    $list[$k]['complete_time']=date('Y-m-d H:i',$arr[$k]['complete_time']);
                }
                if ($arr[$k]['RemainingTime']<=0){
                    $list[$k]['automatic_receive_time']=0;
                }else{
                    $list[$k]['automatic_receive_time']=$arr[$k]['RemainingTime'];
                }
                $list[$k]['goods_attr_id']=$arr[$k]['goods_attr_id'];
                $list[$k]['goods_id']=$arr[$k]['goods_id'];
                $list[$k]['sku']=$arr[$k]['sku'];
                $list[$k]['goods_name']=$arr[$k]['goods_name'];
                $list[$k]['waybillnumber']=$arr[$k]['waybillnumber'];
                $list[$k]['waybillname']=$arr[$k]['waybillname'];
                $list[$k]['shipping_type']=$arr[$k]['shipping_type'];
                $list[$k]['username']=$user->nickname;
                if (empty($list[$k]['username'])) {
                    $list['username'] = $list['consignee'];
                }
                $list[$k]['comment_grade']=$arr[$k]['comment_grade'];
                $list[$k]['cover_image']=$arr[$k]['cover_image'];
                $list[$k]['goods_number']=$arr[$k]['goods_number'];
                $refund=OrderRefund::find()
                    ->where(['order_no'=>$arr[$k]['order_no'],'sku'=>$arr[$k]['sku']])
                    ->asArray()
                    ->all();
                if (!$refund)
                {
                    $list[$k]['refund_status']=0;
                    $list[$k]['apply_refund_time']=0;
                    $list[$k]['apply_refund_reason']='';
                }else{
                    $list[$k]['refund_status']=2;
                    $list[$k]['apply_refund_time']=0;
                    $list[$k]['apply_refund_reason']='';
                    foreach ($refund as &$refundList)
                    {
                        if ($refundList['handle']==0){
                            $list[$k]['refund_status']=1;
                            $list[$k]['apply_refund_time']=date('Y-m-d H:i',$refundList['create_time']);
                            $list[$k]['apply_refund_reason']=$refundList['apply_reason'];
                        }
                    }
                    $after_sale=OrderAfterSale::find()
                        ->where(['order_no'=>$arr[$k]['order_no'],'sku'=>$arr[$k]['sku']])
                        ->asArray()
                        ->all();
                    if (!$after_sale)
                    {
                        $list[$k]['aftersale_status']=0;
                        $list[$k]['aftersale_type']='';
                        $list[$k]['apply_aftersale_time']=0;
                        $list[$k]['apply_aftersale_reason']='';
                    }else
                    {
                        $list[$k]['aftersale_status']=2;
                        $list[$k]['aftersale_type']='';
                        $list[$k]['apply_aftersale_time']=0;
                        $list[$k]['apply_aftersale_reason']='';
                        foreach ($after_sale as &$afterSale)
                        {
                            if ($afterSale['supplier_handle']==0)
                            {
                                $list[$k]['aftersale_status']=1;                          $list[$k]['aftersale_type']=OrderAfterSale::AFTER_SALE_SERVICES[$afterSale['type']];
                                $list[$k]['apply_aftersale_time']=$afterSale['create_time'];                               $list[$k]['apply_aftersale_reason']=$afterSale['description'];
                            }
                        }
                    }
                }
            }
            $output['order_no']=$arr[0]['order_no'];
            $output['status_code']=$arr[0]['status_code'];
            $output['status_desc']=$arr[0]['status_desc'];
            $output['buyer_message']=$arr[0]['buyer_message'];
            $output['pay_name']=$arr[0]['pay_name'];
            $output['create_time']=$arr[0]['create_time'];
            $output['paytime']=date('Y-m-d H:i',$arr[0]['paytime']);
            $output['pay_term']=$arr[0]['pay_term'];
            $output['freight']=GoodsOrder::switchMoney($freight);
            $output['original_price']=GoodsOrder::switchMoney($market_price*$arr[0]['goods_number']);
            $output['discount_price']=GoodsOrder::switchMoney($amount_order);
            $output['amount_order']=GoodsOrder::switchMoney($freight+$amount_order);
            $output['consignee']=$arr[0]['consignee'];
            $output['district']=LogisticsDistrict::getdistrict($arr[0]['district_code']).$arr[0]['region'];
            $output['invoice_information']=$arr[0]['invoice_content'].'-'.$arr[0]['invoice_header'];
            $output['invoicer_card']=$arr[0]['invoicer_card'];
            $output['consignee_mobile']=$arr[0]['consignee_mobile'];
            $output['invoice_header_type']=$arr[0]['invoice_header_type'];
            if($user->last_role_id_app==6)
            {
                $output['uid']=$arr[0]['user_id'];
                $output['to_role_id']=$arr[0]['role_id'];
            }else{
                $output['uid']=Supplier::find()
                    ->select('uid')
                    ->where(['id'=>$arr[0]['supplier_id']])
                    ->asArray()
                    ->one()['uid'];
                $output['to_role_id']=6;
            }
            $output['list']=$list;
            return $output;
        }else{
            $arr=[];
            return $arr;
        }
    }
    /**
     * 设置平台角色
     * @param array $output
     * @param $arr
     * @return array
     */
    public function SetUnpaidContinuedTime($output=[],$arr){
        if (empty($output['username'])){
            $output['username']=(new Query())
                ->from('user_address')
                ->where(['id'=>$arr['address_id']])
                ->one()['consignee'];
            $output['role']='平台';
        }else{
            $output['role']=(new Query())
                ->from(UserRole::tableName().' as a')
                ->select('b.name')
                ->leftJoin(Role::tableName().' as b','a.role_id=b.id')
                ->where(['a.user_id'=>$arr['user_id']])
                ->one()['name'];
            if (!$output['role']){
                $output['role']='平台';
            }
        }
        return $output;
    }



    /**
     * 设置订单号
     * set order_no
     * @return string
     */
    public static function SetOrderNo(){
        do {
            $code=date('md',time()).'1'.rand(10000,99999);
        } while ( $code==GoodsOrder::find()->select('order_no')->where(['order_no'=>$code])->asArray()->one()['order_no']);
        return $code;
    }


    /**
     * 设置交易单号
     * set  transaction no
     * @param $supplier_id
     * @return string
     */
    public  static  function  SetTransactionNo($role_number)
    {
        $rand=rand(10000,99999);
        $time=time();
        $month=date('m',$time);
        $day=date('d',$time);
        do{
            $transaction_no=$month.$day.$role_number.$rand;
        }while ( $transaction_no==UserCashregister::find()->select('transaction_no')->where(['transaction_no'=>$transaction_no])->asArray()->one()['transaction_no'] || $transaction_no==UserAccessdetail::find() ->select('transaction_no')->where(['transaction_no'=>$transaction_no])->asArray()->one()['transaction_no']);
        return $transaction_no;
    }

    /**
     * 切换状态
     * @param $arr
     * @param $user
     * @return mixed
     */
    public  static  function  SwitchStatus_desc($arr,$user)
    {
        foreach ($arr as $k =>$v)
        {
            switch ($arr[$k]['status'])
            {
                case self::PAY_STATUS_DESC_UNPAID:
                    $arr[$k]['status_code']=self::ORDER_TYPE_UNPAID;
                    $arr[$k]['status_desc']=self::PAY_STATUS_DESC_UNPAID;
                    break;
                case self::SHIPPING_STATUS_DESC_UNSHIPPED:
                    $arr[$k]['status_code']=self::ORDER_TYPE_UNSHIPPED;
                    $arr[$k]['status_desc']=self::SHIPPING_STATUS_DESC_UNSHIPPED;
                    if ( $arr[$k]['is_unusual']==1){
                        $arr[$k]['status_code']=self::ORDER_TYPE_UNSHIPPED.'_'.self::ORDER_TYPE_APPLYREFUND;
                        $arr[$k]['status_desc']=self::SHIPPING_STATUS_DESC_UNSHIPPED.'_'.self::ORDER_TYPE_DESC_APPLYREFUND;
                    }
                    break;
                case  self::ORDER_TYPE_DESC_UNRECEIVED:
                    $arr[$k]['status_code']=self::ORDER_TYPE_UNRECEIVED;
                    $arr[$k]['status_desc']=self::ORDER_TYPE_DESC_UNRECEIVED;
                    if ($arr[$k]['is_unusual']==1){
                        $arr[$k]['status_code']=self::ORDER_TYPE_UNRECEIVED.'_'.self::ORDER_TYPE_APPLYREFUND;
                        $arr[$k]['status_desc']=self::ORDER_TYPE_DESC_UNRECEIVED.'_申请退款';
                    }
                    if ($user->last_role_id_app==6)
                    {
                        $arr[$k]['status_code']=self::ORDER_TYPE_SHIPPED;
                        $arr[$k]['status_desc']=self::SHIPPING_STATUS_DESC_SHIPPED;
                        if ($arr[$k]['is_unusual']==1){
                            $arr[$k]['status_code']=self::ORDER_TYPE_SHIPPED.'_'.self::ORDER_TYPE_APPLYREFUND;
                            $arr[$k]['status_desc']=self::SHIPPING_STATUS_DESC_SHIPPED.'_'.self::ORDER_TYPE_DESC_APPLYREFUND;
                        }
                    }
                    break;
                case  self::ORDER_TYPE_DESC_CANCEL:
                    $arr[$k]['status_code']=self::ORDER_TYPE_CANCEL;
                    $arr[$k]['status_desc']=self::ORDER_TYPE_DESC_CANCEL;
                    break;
                case  '售后中':
                    $arr[$k]['status_code']='after_saled';
                    $arr[$k]['status_desc']='售后中';
                    break;
                case  '售后结束':
                    $arr[$k]['status_code']='after_sale_completed';
                    $arr[$k]['status_desc']='售后完成';
                    break;
                case self::ORDER_TYPE_DESC_COMPLETED:
                    $arr[$k]['status_code']=self::ORDER_TYPE_COMPLETED;
                    $arr[$k]['status_desc']=self::ORDER_TYPE_DESC_COMPLETED;
                    if (!$arr[$k]['comment_id'])
                    {
                        $arr[$k]['status_code']=self::ORDER_TYPE_UNCOMMENT;
                        $arr[$k]['status_desc']=self::ORDER_TYPE_DESC_UNCOMMENT;
                    }
                    break;
                case self::ORDER_TYPE_DESC_UNCOMMENT:
                    $arr[$k]['status_code']=self::ORDER_TYPE_UNCOMMENT;
                    $arr[$k]['status_desc']=self::ORDER_TYPE_DESC_UNCOMMENT;
                    break;
            }
            unset($arr[$k]['unusual']);
        }
        return $arr;
    }

    /**
     * 获取角色购买商品价格
     * @param $role
     * @return string
     */
    public static function  GetRoleMoney($role)
    {
        switch ($role)
        {
            case 2:
                //工人
                $data=self::WORKER_MONEY;
                break;
            case 3:
                //设计师
                $data=self::DESIGNER_MONEY;
                break;
            case 4:
                //项目经理
                $data=self::MANAGER_MONEY;
                break;
            case 5:
                //装修公司
                $data=self::COMPANEY_MONEY;
                break;
            case 6:
                //供应商
                $data=self::SUPPLIER_MONEY;
                break;
            case 7:
                //业主
                $data=self::PLAT_MONEY;
                break;
        }
        return $data;
    }

    /**
     * @param $user
     * @param $address_id
     * @param $suppliers
     * @param $total_amount
     * @param $pay_way
     * @return array|int
     */
    public static  function AppBuy($user,$address_id,$suppliers,$total_amount,$pay_way)
    {
        //1:余额支付  2：支付宝app支付  3：微信APP支付
        switch ($pay_way)
        {
            case 1:
                $pay_name='余额支付';
                break;
            case 2:
                $pay_name='支付宝app支付';
                break;
            case 3:
                $pay_name='微信APP支付';
                break;
        }
        $address=Addressadd::findOne($address_id);
        if (!$address)
        {
            $code=1000;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        $role_money=self::GetRoleMoney($user->last_role_id_app);
        $time=time();
        try{
            $total=0;
            foreach ($suppliers as  &$supplier)
            {
                if (
                    !array_key_exists('freight',$supplier)
                    || !array_key_exists('buyer_message',$supplier)
                    || !array_key_exists('invoice_type',$supplier)
                    || !array_key_exists('invoice_header_type',$supplier)
                    || !array_key_exists('invoice_header',$supplier)
                    || !array_key_exists('invoicer_card',$supplier)
                    || !array_key_exists('invoice_content',$supplier)
                    || !array_key_exists('supplier_id',$supplier)
                    || !array_key_exists('goods',$supplier)
                )
                {

                    $tran->rollBack();
                    $code=1000;
                    return $code;
                }
                $order_no=GoodsOrder::SetOrderNo();
                $money=0;
                $count=count($supplier['goods']);
                if ($count==0)
                {
                    $count=1;
                }
                $freight=$supplier['freight']/$count;
                foreach ($supplier['goods'] as &$goods)
                {
                    if (
                        !array_key_exists('goods_id',$goods)
                        || !array_key_exists('goods_num',$goods))
                    {
                        $tran->rollBack();
                        $code=1000;
                        return $code;
                    }
                    $Goods=Goods::find()
                        ->where(['id'=>$goods['goods_id']])
                        ->asArray()
                        ->one();
                    $OrderGoods=new OrderGoods();
                    $OrderGoods->order_no=$order_no;
                    $OrderGoods->goods_number=$goods['goods_num'];
                    $OrderGoods->goods_name=$Goods['title'];
                    $OrderGoods->goods_price=$Goods["{$role_money}"];
                    $OrderGoods->sku=$Goods['sku'];
                    $OrderGoods->market_price=$Goods['market_price'];
                    $OrderGoods->supplier_price=$Goods['supplier_price'];
                    $OrderGoods->order_status=0;
                    $OrderGoods->shipping_status=0;
                    $OrderGoods->customer_service=0;
                    $OrderGoods->is_unusual=0;
                    $OrderGoods->freight=$freight;
                    $OrderGoods->cover_image=$Goods['cover_image'];
                    $OrderGoods->create_time=$time;
                    if (!$OrderGoods->save(false))
                    {
                        $tran->rollBack();
                        $code=500;
                        return $code;
                    }
                    $money+=($Goods["{$role_money}"]*$goods['goods_num']);
                }
                $total+=($supplier['freight']*100+$money);
                $GoodsOrder=new GoodsOrder();
                $GoodsOrder->order_no=$order_no;
                $GoodsOrder->amount_order=$supplier['freight']*100+$money;
                $GoodsOrder->supplier_id=$supplier['supplier_id'];
                $GoodsOrder->pay_status=0;
                $GoodsOrder->user_id=$user->id;
                $GoodsOrder->pay_name=$pay_name;
                $GoodsOrder->create_time=$time;
                $GoodsOrder->order_refer=GoodsOrder::REFUND_HANDLE_STATUS_DISAGREE;
                $GoodsOrder->return_insurance=0;
                $GoodsOrder->role_id=$user->last_role_id_app;
                $GoodsOrder->buyer_message=$supplier['buyer_message'];
                $GoodsOrder->consignee=$address->consignee;
                $GoodsOrder->district_code=$address->district;
                $GoodsOrder->region=$address->region;
                $GoodsOrder->consignee_mobile=$address->mobile;
//                $invoice=Invoice::findOne($supplier['invoice_id']);
//                $GoodsOrder->invoice_type=$invoice->invoice_type;
//                $GoodsOrder->invoice_header_type=$invoice->invoice_header_type;
//                $GoodsOrder->invoice_header=$invoice->invoice_header;
//                $GoodsOrder->invoicer_card=$invoice->invoicer_card;
//                $GoodsOrder->invoice_content=$invoice->invoice_content;
                $GoodsOrder->invoice_type=$supplier['invoice_type'];
                $GoodsOrder->invoice_header_type=$supplier['invoice_header_type'];
                $GoodsOrder->invoice_header=$supplier['invoice_header'];
                $GoodsOrder->invoicer_card=$supplier['invoicer_card'];
                $GoodsOrder->invoice_content=$supplier['invoice_content'];
                if (!$GoodsOrder->save(false))
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
                $orders[]=$order_no;
            }
             if ($total!=$total_amount*100)
             {
                 $code=1000;
                 $tran->rollBack();
                 return $code;
             }
            $tran->commit();
            return $orders;
        }catch (yii\db\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }


        /**
         * 余额购买
         * @param $total_amount
         * @param array $suppliers
         * @param $pay_pwd
         * @return int
         */
      public static function  BalanceBuy($total_amount,$pay_pwd,$orders=[])
      {
          $user = Yii::$app->user->identity;
          if (Yii::$app->getSecurity()->validatePassword($pay_pwd,$user->pay_password)==false)
          {
              $code=1055;
              return $code;
          }


      }


        /**
         * @param $goods
         * @return string
         */
      public  static  function  CalculationFreight($goods)
      {
          foreach ($goods as $one){
              if (!is_array($one))
              {
                  $one=(array)$one;
              }
              if ($one['goods_num'] != 0 || $one['goods_num'] !=null){
                  $goods_ [] = $one;
              }
          }
          foreach ($goods_ as  $k =>$v)
          {
              $Good[$k]=LogisticsTemplate::find()
                  ->where(['id'=>Goods::find()
                      ->select('logistics_template_id')
                      ->where(['id'=>$goods_[$k]['goods_id']])
                      ->one()->logistics_template_id])
                  ->asArray()
                  ->one();
              $Good[$k]['goods_id']=$goods_[$k]['goods_id'];
              $Good[$k]['goods_num']=$goods_[$k]['goods_num'];
          }
          $templates=[];
          foreach ($Good as &$wuliu){
              if (!in_array($wuliu['id'],$templates))
              {

                  $templates[]=$wuliu['id'];
              };
          }
          foreach ($templates as &$list)
          {
              $costs[]['id']=$list;
          }
          foreach ($costs as &$cost)
          {
              $cost['goods_num']=0;
              foreach ($Good as &$list)
              {
                  if ($list['id']==$cost['id'])
                  {

                      $cost['goods_num']+=$list['goods_num'];
                  }
              }
          }
          $freight=0;
          foreach ($costs as &$cost)
          {
              $logistics_template=LogisticsTemplate::find()
                  ->where(['id'=>$cost['id']])
                  ->asArray()
                  ->one();
              if ($logistics_template['delivery_number_default']>=$cost['goods_num'])
              {
                  $freight+=$logistics_template['delivery_cost_default'];
              }else{
                  if ($logistics_template['delivery_number_delta']==0)
                  {
                      $logistics_template['delivery_number_delta']=1;
                  }
                  $addnum=ceil(($cost['num']-$logistics_template['delivery_number_default'])/$logistics_template['delivery_number_delta']);
                  $money=$logistics_template['delivery_cost_default']+$addnum*$logistics_template['delivery_cost_delta'];
                  $freight+=$money;
              }
          }

//            foreach ($costs as &$cost)
//            {
//                foreach ($Good as &$list)
//                {
//                    if ($list['id']==$cost['id'])
//                    {
//                        $cost['goods'][]=[
//                            'goods_id'=>$list['goods_id'],
//                            'num'=>$list['num']
//                        ];
//                    }
//                }
//            }

          return GoodsOrder::switchMoney($freight*0.01);
      }




}