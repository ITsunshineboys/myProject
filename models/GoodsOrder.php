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
use yii\data\Pagination;
use app\models\LogisticsDistrict;
use app\services\StringService;


class GoodsOrder extends ActiveRecord
{
    public $goods_id;
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
      * 获取商品信息-线下店商城
     * @param $goods_id
     * @param $goods_num
     * @param $goods_attr
     * @return array|bool
     */
    public  function Getlinegoodsdata($goods_id, $goods_num, $goods_attr){
            $query=new \yii\db\Query();
            $array  = $query->from('goods AS a')->select('a.supplier_id,a.title,a.subtitle,b.nickname,c.name,a.market_price,a.logistics_template_id,a.platform_price,a.cover_image,b.icon,c.name,a.is_unusual')->leftJoin('supplier AS b', 'b.id = a.supplier_id')->leftJoin('goods_brand AS c','c.id = a.brand_id')->where(['a.id' =>$goods_id])->one();
                $logistics_template=(new \yii\db\Query())->from('logistics_template')->select('supplier_id,delivery_method,delivery_cost_default,delivery_number_default,delivery_cost_delta,delivery_number_delta,status')->where(['status'=>1,'id'=>$array['logistics_template_id']])->one();
            if ($logistics_template['delivery_method']==1){
                $array['freight']=0;
            }else{
                $array['freight']=$logistics_template['delivery_cost_default']*0.01*$logistics_template['delivery_number_default']+$logistics_template['delivery_cost_delta']*0.01*$logistics_template['delivery_number_delta'];
            }
            $array['cover_image']=$_SERVER['SERVER_NAME'].'/'.$array['cover_image'];
            $array['goods_attr']=$goods_attr;
            $array['goods_num']=$goods_num;
            $array['present_price']=$goods_num*0.01*$array['platform_price'];
            $array['original_price']=$goods_num*0.01*$array['platform_price'];
            unset($array['platform_price']);
            unset($array['market_price']);
            $array['return_insurance']=0;
            return $array;
        }

    /**
     * 获取商品列表-大后台获取全部订单
     * @param $pagesize
     * @param $page
     * @param $time_id
     * @param $time_start
     * @param $time_end
     * @return array
     */
    public function Getallorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend){
        if ($blend==0){
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id');
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where($this->Timehandle($time_id,$time_start,$time_end));
            }

        }else{
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where($this->ordercondition($blend));
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where($this->Timehandle($time_id,$time_start,$time_end))->andwhere($this->ordercondition($blend));
            }
        }

            $count = $array->count();
            $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $pagesize,'pageSizeParam'=>false]);
            $data=$array->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
            $arr=$this->getorderstatus($data);
            foreach ($arr AS $k =>$v){
                $arr[$k]['handle']='';
                if ($arr[$k]['is_unusual']==1){
                    $arr[$k]['unusual']='申请退款';
                }else if ($arr[$k]['is_unusual']==0){
                    $arr[$k]['unusual']='无异常';
                }else if($arr[$k]['is_unusual']==2){
                    $arr[$k]['unusual']='退款失败';
                }
                if($arr[$k]['status']=='未发货' || $arr[$k]['status']=='售后中'|| $arr[$k]['status']=='售后结束' || $arr[$k]['status']=='待收货' ){
                    $arr[$k]['handle']='平台介入';
                }
            }
            $data=$this->page($count,$pagesize,$page,$arr);
            return $data;
        }
    /**
         *
         * 获取商品列表-大后台获取待付款全部订单
         */
    public function Getallunpaidorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend){
        if ($blend==0){
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>0,'z.order_status'=>0]);
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>0,'z.order_status'=>0])->andwhere($this->Timehandle($time_id,$time_start,$time_end));
            }

        }else{
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>0,'z.order_status'=>0])->andwhere($this->ordercondition($blend));
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>0,'z.order_status'=>0])->andwhere($this->Timehandle($time_id,$time_start,$time_end))->andwhere($this->ordercondition($blend));
            }
        }
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货' || $arr[$k]['status']=='售后中'|| $arr[$k]['status']=='售后结束' || $arr[$k]['status']=='待收货' ){
                $arr[$k]['handle']='平台介入';
            }
        }
        $data=$this->page($count,$pagesize,$page,$arr);
        return $data;
        }
        /**
         *
         * 获取商品列表-大后台获取待发货全部订单
         */
    public function Getallunshippedorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend){
        if ($blend==0){
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>0]);
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>0])->andwhere($this->Timehandle($time_id,$time_start,$time_end));
            }

        }else{
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>0])->andwhere($this->ordercondition($blend));
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>0])->andwhere($this->Timehandle($time_id,$time_start,$time_end))->andwhere($this->ordercondition($blend));
            }
        }
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货' || $arr[$k]['status']=='售后中'|| $arr[$k]['status']=='售后结束' || $arr[$k]['status']=='待收货' ){
                $arr[$k]['handle']='平台介入';
            }
        }
        $data=$this->page($count,$pagesize,$page,$arr);
        return $data;
        }
    /**
     *
     * 获取商品列表-大后台获取待收货全部订单
     */
    public function Getallunreceivedorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend){
        if ($blend==0){
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>1]);
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>1])->andwhere($this->Timehandle($time_id,$time_start,$time_end));
            }

        }else{
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>1])->andwhere($this->ordercondition($blend));
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>1])->andwhere($this->Timehandle($time_id,$time_start,$time_end))->andwhere($this->ordercondition($blend));
            }

        }

        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货' || $arr[$k]['status']=='售后中'|| $arr[$k]['status']=='售后结束' || $arr[$k]['status']=='待收货' ){
                $arr[$k]['handle']='平台介入';
            }
        }
        $data=$this->page($count,$pagesize,$page,$arr);
        return $data;
    }
    /**
     * 获取大后台已完成全部订单
     */
    public function Getallcompeletedorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend){
        if ($blend==0){
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>1,'z.shipping_status'=>1,'z.customer_service'=>0]);
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>1,'z.shipping_status'=>1,'z.customer_service'=>0])->andwhere($this->Timehandle($time_id,$time_start,$time_end));
            }

        }else{
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>1,'z.shipping_status'=>1,'z.customer_service'=>0])->andwhere($this->ordercondition($blend));
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['a.pay_status'=>1,'z.order_status'=>1,'z.shipping_status'=>1,'z.customer_service'=>0])->andwhere($this->Timehandle($time_id,$time_start,$time_end))->andwhere($this->ordercondition($blend));
            }

        }
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货' || $arr[$k]['status']=='售后中'|| $arr[$k]['status']=='售后结束' || $arr[$k]['status']=='待收货' ){
                $arr[$k]['handle']='平台介入';
            }
        }
        $data=$this->page($count,$pagesize,$page,$arr);
        return $data;
    }
    /**
     * 获取大后台已取消订单
     */
    public function  Getallcanceledorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend){
        if ($blend==0){
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['z.order_status'=>2]);
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['z.order_status'=>2])->andwhere($this->Timehandle($time_id,$time_start,$time_end));
            }

        }else{
            if ($time_id==0){
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['z.order_status'=>2])->andwhere($this->ordercondition($blend));
            }else{
                $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id,z.comment_id')->where(['z.order_status'=>2])->andwhere($this->Timehandle($time_id,$time_start,$time_end))->andwhere($this->ordercondition($blend));
            }

        }
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货' || $arr[$k]['status']=='售后中'|| $arr[$k]['status']=='售后结束' || $arr[$k]['status']=='待收货' ){
                $arr[$k]['handle']='平台介入';
            }
        }
        $data=$this->page($count,$pagesize,$page,$arr);
        return $data;
    }
    /**
     *大后台订单详情-获取订单详情
     */
    public function Getorderdetailsall($order_id){
        $array= $this->getorderlist()->leftJoin('goods AS b','b.id = z.goods_id')->leftJoin('user_address AS c','a.address_id=c.id')->leftJoin('invoice AS d','a.invoice_id= d.id')->select('a.order_no,a.supplier_id,a.order_status,a.pay_status,a.paytime,a.shipping_status,a.customer_service,a.user_id,a.create_time,a.order_refer,a.comment,b.supplier_price,b.platform_price,b.market_price,b.purchase_price_decoration_company,b.logistics_template_id,b.purchase_price_designer,a.return_insurance,a.freight,c.address_name,c.consignee,c.mobile,c.district,c.region,d.invoice_header,d.invoice_content')->where(['a.id'=>$order_id]);
        $count=$array->count();
        $pagination = new Pagination(['totalCount'=> $count ,'pageSize'=>15,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)->limit($pagination->limit)->all();
        foreach ($data as $k => $v){
            $model=new LogisticsDistrict();
            $data[$k]['district']=$model->getdistrict($data[$k]['district']);
            $data[$k]['paytime']=StringService::timeconversion($data[$k]['paytime']);
            $data[$k]['create_time']= StringService::timeconversion($data[$k]['create_time']);
        }
        return $data;
    }
    /**
     *
     * 获取大后台售后订单
     */
    public function Getallcustomerserviceorderdata(){
        $query=new \yii\db\Query();
        $array  = $query->from('goods_order AS a')->select('a.id,a.order_status,a.customer_service,a.pay_status,a.address_id,a.order_status,a.create_time,a.user_id,a.shipping_status,a.amount_order,b.title')->leftJoin('goods AS b','b.id = a.goods_id')->where(['a.customer_service'=>0]);
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' =>15,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        return $arr;
    }
    /**
     * 获取商家后台全部订单
     */
//    public function Getbusinessorderlist(){
//        $query=new \yii\db\Query();
//        $array  = $query->from('goods_order AS a')->select('a.id,a.order_status,a.customer_service,a.pay_status,a.address_id,a.order_status,a.create_time,a.user_id,a.shipping_status,a.amount_order,b.title')->leftJoin('goods AS b','b.id = a.goods_id');
//        $count = $array->count();
//        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => 1,'pageSizeParam'=>false]);
//        $data=$array->offset($pagination->offset)
//            ->limit($pagination->limit)
//            ->all();
//        $arr=$this->getorderstatus($data);
//        return $arr;
//    }
    /**
     * 获取商家后台全部订单
     * @param $supplier_id
     * @return mixed
     */
    public function Businessgetallorderlist($supplier_id,$pagesize){
        $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id')->where(['a.supplier_id'=>$supplier_id]);
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货'){
                $arr[$k]['handle']='去发货';
            }
            if ($arr[$k]['status']=='售后中'){
                $arr[$k]['handle']='售后处理';
            }
        }
        $totalpage=ceil($count/$pagesize);
        $sd=array(
            'orderlist'=>$arr,
            'totalpage'=>$totalpage,
            'count'=>$count,
            'page'=>$pagination->getPage()
        );
        return $sd;
    }


    /**
     *  获取商家后台待付款
     * @param $supplier_id
     * @param $pagesize
     * @return array
     */
    public function Businessgetunpaidorder($supplier_id,$pagesize){
        $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id')->where(['a.supplier_id'=>$supplier_id,'a.pay_status'=>0,'z.order_status'=>0]);
        $count=$array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' =>$pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();


        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货'){
                $arr[$k]['handle']='去发货';
            }
            if ($arr[$k]['status']=='售后中'){
                $arr[$k]['handle']='售后处理';
            }
        }
        $totalpage=ceil($count/$pagesize);
        $sd=array(
            'orderlist'=>$arr,
            'totalpage'=>$totalpage,
            'count'=>$count
            );
        return $sd;
    }

    /**
     *
     * 获取商家后台未发货订单
     * @param $supplier_id
     * @param $pagesize
     * @return array
     */
    public  function Businessgetnotshippedorder($supplier_id,$pagesize){
        $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id')->where(['a.supplier_id'=>$supplier_id,'a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>0]);
        $count=$array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' =>$pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货'){
                $arr[$k]['handle']='去发货';
            }
            if ($arr[$k]['status']=='售后中'){
                $arr[$k]['handle']='售后处理';
            }
        }
        $totalpage=ceil($count/$pagesize);
        $sd=array(
            'orderlist'=>$arr,
            'totalpage'=>$totalpage,
            'count'=>$count
        );
        return $sd;
    }


    /**
     * 商家后台待收货订单
     * @param $supplier_id
     * @param $pagesize
     * @return array
     */
    public function  Businessgetnotreceivedorder($supplier_id,$pagesize){
        $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id')->where(['a.supplier_id'=>$supplier_id,'a.pay_status'=>1,'z.order_status'=>0,'z.shipping_status'=>1]);
        $count=$array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' =>$pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货'){
                $arr[$k]['handle']='去发货';
            }
            if ($arr[$k]['status']=='售后中'){
                $arr[$k]['handle']='售后处理';
            }
        }
        $totalpage=ceil($count/$pagesize);
        $sd=array(
            'orderlist'=>$arr,
            'totalpage'=>$totalpage,
            'count'=>$count
        );
        return $sd;
    }


    /**
     *
     * 获取商家后台已完成列表
     * @param $supplier_id
     * @param $pagesize
     * @return array
     *
     */
    public function Businessgetcompletedorder($supplier_id,$pagesize){
        $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id')->where(['a.supplier_id'=>$supplier_id,'a.pay_status'=>1,'z.order_status'=>1,'z.shipping_status'=>1,'z.customer_service'=>0]);
        $count=$array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' =>$pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货'){
                $arr[$k]['handle']='去发货';
            }
            if ($arr[$k]['status']=='售后中'){
                $arr[$k]['handle']='售后处理';
            }
        }
        $totalpage=ceil($count/$pagesize);
        $sd=array(
            'orderlist'=>$arr,
            'totalpage'=>$totalpage,
            'count'=>$count
        );
        return $sd;
    }


    /**
     * 获取商家后台已取消
     * @param $supplier_id
     * @param $pagesize
     * @return array
     */
    public function Businessgetcanceledorder($supplier_id,$pagesize){
        $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id')->where(['a.supplier_id'=>$supplier_id,'z.order_status'=>2]);
        $count=$array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' =>$pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货'){
                $arr[$k]['handle']='去发货';
            }
            if ($arr[$k]['status']=='售后中'){
                $arr[$k]['handle']='售后处理';
            }
        }
        $totalpage=ceil($count/$pagesize);
        $sd=array(
            'orderlist'=>$arr,
            'totalpage'=>$totalpage,
            'count'=>$count
        );
        return $sd;
    }


    /**
     * 获取售后服务订单
     * @param $supplier_id
     * @param $pagesize
     * @return array
     */
    public function  Businessgetcustomerserviceorder($supplier_id,$pagesize){
        $array=$this->getorderlist()->select('a.order_no,a.id,z.customer_service,a.pay_status,a.address_id,z.order_status,a.create_time,a.user_id,z.shipping_status,a.amount_order,z.goods_name,z.goods_price,z.goods_number,z.is_unusual,z.market_price,z.supplier_price,z.sku,z.order_id')->where(['a.supplier_id'=>$supplier_id,'z.order_status'=>1])->andwhere('z.customer_service!=0');
        $count=$array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' =>$pagesize,'pageSizeParam'=>false]);
        $data=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $arr=$this->getorderstatus($data);
        foreach ($arr AS $k =>$v){
            $arr[$k]['handle']='';
            if ($arr[$k]['is_unusual']==1){
                $arr[$k]['unusual']='申请退款';
            }else if ($arr[$k]['is_unusual']==0){
                $arr[$k]['unusual']='无异常';
            }else if($arr[$k]['is_unusual']==2){
                $arr[$k]['unusual']='退款失败';
            }
            if($arr[$k]['status']=='未发货'){
                $arr[$k]['handle']='去发货';
            }
            if ($arr[$k]['status']=='售后中'){
                $arr[$k]['handle']='售后处理';
            }
        }
        $totalpage=ceil($count/$pagesize);
        $sd=array(
            'orderlist'=>$arr,
            'totalpage'=>$totalpage,
            'count'=>$count
        );
        return $sd;
    }



    /**
     * 获取订单详情订单信息
     * @param $order_id
     * @return array|null|ActiveRecord
     */
    public function Getorderinformation($order_id,$goodsid){
        $array=$this->getorderlist()->leftJoin('express AS b','b.order_no =a.order_no and b.sku=z.sku')->select('a.pay_name,z.order_status,z.customer_service,z.shipping_status,a.pay_status,a.create_time,a.user_id,a.address_id,z.goods_name,a.amount_order,z.goods_number,z.freight,a.order_no,a.create_time,a.paytime,a.user_id,a.address_id,a.return_insurance,z.goods_id,z.goods_attr_id,z.sku,a.address_id,a.invoice_id,supplier_price,z.market_price,b.waybillnumber,b.waybillname,z.order_id')->where(['a.id'=>$order_id,'z.goods_id'=>$goodsid])->all();
        $arr=$this->getorderstatus($array);
        $output=array();
        $goods_num=0;
        foreach($arr as $k=>$v){
            $arr[$k]['amount_order']= $arr[$k]['amount_order']*0.01;
            $arr[$k]['freight']= $arr[$k]['freight']*0.01;
            $arr[$k]['supplier_price']= $arr[$k]['supplier_price']*0.01;
            $arr[$k]['market_price']= $arr[$k]['market_price']*0.01;
            $arr[$k]['return_insurance']= $arr[$k]['return_insurance']*0.01;
            $arr[$k]['goods_price']=($arr[$k]['amount_order']-$arr[$k]['freight']-$arr[$k]['return_insurance']);
            $output['amount_order']=$arr[$k]['amount_order'];
            $output['return_insurance']=$arr[$k]['return_insurance'];
            $output['freight']=$arr[$k]['freight'];
            $output['address_id']=$arr[$k]['address_id'];
            $output['invoice_id']=$arr[$k]['invoice_id'];
            $output['goods_price']=$arr[$k]['goods_price'];
            $output['supplier_price']=$arr[$k]['supplier_price'];
            $output['market_price']=$arr[$k]['market_price'];
            $output['order_no']=$arr[$k]['order_no'];
            $output['create_time']=$arr[$k]['create_time'];
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
            $output['username']=(new \yii\db\Query())->from('user')->where(['id'=>$arr[$k]['user_id']])->one()['nickname'];
            if (empty($output['username'])){
                $output['username']=(new \yii\db\Query())->from('user_address')->where(['id'=>$arr[$k]['address_id']])->one()['consignee'];
            }
            $goods_num+=$arr[$k]['goods_number'];
        }
        $output['goods_num']=$goods_num;

        if ($output['status']=='未付款'){
            $time=time();
            $pay_term=(strtotime($output['create_time'])+1800);
            if (($pay_term-$time)<0){
                $res=Yii::$app->db->createCommand()->update('order_goodslist', ['order_status' => 2],'order_no='.$output['order_no'].' and sku='.$output['sku'])->execute();
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


    public function Getordergoodsinformation($goods_name,$goods_id,$goods_attr_id,$order_no,$sku){
        $goods=array();

            $goods['goods_name']=$goods_name;
            $goods['goods_id']=$goods_id;
            $attr_id=explode(',',$goods_attr_id);
            $goods['attr']=array();
            foreach($attr_id AS $key =>$val){
                $goods['attr'][$key]=(new \yii\db\Query())->select('name,value')->from('goods_attr')->where(['id'=>$attr_id[$key]])->one();

            }


        return $goods;
    }
    /**
     * 获取后台订单状态
     * @param $data
     * @return mixed
     */
    private function getorderstatus($data)
    {
        foreach ($data as $k =>$v){
            $data[$k]['create_time']=date('Y-m-d H:i',$data[$k]['create_time']);
            switch ($data[$k]['order_status']){
                case 0:
                    switch ($data[$k]['pay_status'])
                    {
                        case  0:
                            $data[$k]['status']='未付款';
                            break;
                        case  1:
                            switch ($data[$k]['shipping_status']){
                                case 0:
                                    $data[$k]['status']='未发货';
                                    break;
                                case 1:
                                    $data[$k]['status']='待收货';
                                    break;
                                case 2:
                                            $data[$k]['status']='已完成';
                                            break;
                            }
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
            $received=array();
           if ($data[$k]['status']=='待收货'){
               $waybillnumber=Express::find()->select('waybillnumber')->where(['order_no'=>$data[$k]['order_no'],'sku'=>$data[$k]['sku']])->one()['waybillnumber'];
               $received[$k]['model']=(new Express())->getorder($waybillnumber);
               if ($received[$k]['model']['ischeck']==1){
                   $data[$k]['status']='已完成';
                   $res=Yii::$app->db->createCommand()->update('order_goodslist', ['order_status' =>1,'shipping_status'=>2],'order_id='.$data[$k]['order_id'].' and sku='.$data[$k]['sku'])->execute();
               }
           };
            //获取用户
            $user=array();
            $user[$k]=(new \yii\db\Query())->from('user')->select('id,username')->where(['id'=>$data[$k]['user_id']])->one();
            if ($user[$k]){
                $data[$k]['username']=$user[$k]['username'];
            }else{
                $data[$k]['username']=(new \yii\db\Query())->from('user_address')->select('id,consignee')->where(['id'=>$data[$k]['address_id']])->one()['consignee'];
            }
            unset($data[$k]['customer_service']);
            unset($data[$k]['pay_status']);
            unset($data[$k]['order_status']);
            unset($data[$k]['shipping_status']);
        }

        return $data;
    }

    private function getorderlist()
    {
        $query=new \yii\db\Query();
        $getorderlist  = $query->from('goods_order AS a')->leftJoin('order_goodslist AS z','z.order_id = a.id');
        return $getorderlist;
    }
    private  function page($count,$pagesize,$page,$arr){
        $totalpage=ceil($count/$pagesize);
        if ($page>$totalpage){
           $sd= array(
                'orderlist'=>'',
                'totalpage'=>$totalpage,
                'count'=>$count,
                'page'=>$page
            );
        }else{
            $sd=array(
                'orderlist'=>$arr,
                'totalpage'=>$totalpage,
                'count'=>$count,
                'page'=>$page
            );
        }
        return $sd;
    }


    private  function Timehandle($time_id,$time_start,$time_end){
        if ($time_id==0){
           return null;
        }else if ($time_id==1){
         $data='DATE(FROM_UNIXTIME(a.create_time))=CURDATE()';
            return $data;
        }else if ($time_id==2){
            $data="DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(FROM_UNIXTIME(a.create_time))";
            return $data;
        }else if ($time_id==3){
            $data="DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= DATE(FROM_UNIXTIME(a.create_time))";
            return $data;
        }else if($time_id==4){
            $data="DATE_SUB(CURDATE(), INTERVAL 365 DAY) <= DATE(FROM_UNIXTIME(a.create_time))";
            return $data;
        }else if ($time_id==5){
            $data="a.create_time>='".strtotime($time_start)."' and a.create_time<= '".strtotime($time_end)."'";
            return $data;
        }
    }

    private function ordercondition($blend){
        $ismobile = preg_match('/^0?1[3|4|5|8][0-9]\d{8}$/',$blend);
        $isorderno= preg_match('/\b\d{10}\b/', $blend);
        $ischina= preg_match('/[\x80-\xff]{6,30}/', $blend);
        if ($isorderno==1){
            $data="a.order_no='".$blend."'";
            return $data;
        }
        if ($ischina==1){
            $data="z.goods_name='".$blend."'";
            return $data;
        }

    }



}