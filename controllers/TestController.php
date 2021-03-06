<?php

namespace app\controllers;

use app\models\Bank;
use app\models\Carousel;
use app\models\ChatRecord;
use app\models\Express;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsBrand;
use app\models\GoodsComment;
use app\models\GoodsOrder;
use app\models\GoodsStat;
use app\models\GoodsStyle;
use app\models\Invoice;
use app\models\LineSupplier;
use app\models\LineSupplierGoods;
use app\models\LogisticsDistrict;
use app\models\LogisticsTemplate;
use app\models\OrderAfterSale;
use app\models\OrderGoods;
use app\models\OrderGoodsAttr;
use app\models\OrderGoodsBrand;
use app\models\OrderGoodsDescription;
use app\models\OrderGoodsImage;
use app\models\OrderLogisticsDistrict;
use app\models\OrderLogisticsTemplate;
use app\models\OrderPlatForm;
use app\models\OrderRefund;
use app\models\OrderSeries;
use app\models\OrderStyle;
use app\models\ShippingCart;
use app\models\Supplier;
use app\models\User;
use app\models\UserAddress;
use app\models\UserBankInfo;
use app\models\UserNewsRecord;
use app\models\UserRole;
use app\services\BasisDecorationService;
use app\services\ExceptionHandleService;
use app\services\SmValidationService;
use app\services\StringService;
use Symfony\Component\Yaml\Tests\B;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ServerErrorHttpException;

class TestController extends Controller
{
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'cache-delete',
        'cache-delete-all',
//        'reset-mobile-pwd',
        'goods-qr-gen',
        'register-user',
        'upload',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    $code = 403;
                    new ExceptionHandleService($code);
                    exit;
                },
                'only' => self::ACCESS_LOGGED_IN_USER,
                'rules' => [
                    [
                        'actions' => self::ACCESS_LOGGED_IN_USER,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cache-delete' => ['post',],
                    'cache-delete-all' => ['post',],
                    'reset-mobile-pwd' => ['post',],
                    'register-user' => ['post',],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Delete cache action.
     *
     * @return string
     */
    public function actionCacheDelete()
    {
        $key = trim(Yii::$app->request->post('key', ''));
        return Yii::$app->cache->delete($key);
    }

    /**
     * Delete all cache action.
     *
     * @return string
     */
    public function actionCacheDeleteAll()
    {
        return Yii::$app->cache->flush();
    }

    /**
     * Reset user's new mobile and new password
     *
     * @return bool
     */
    public function actionResetMobilePwd()
    {
        $mobile = Yii::$app->request->post('mobile');
        $newMobile = Yii::$app->request->post('new_mobile');
        $pwd = Yii::$app->request->post('pwd');
        return User::resetMobileAndPwdByMobile($mobile, $newMobile, $pwd);
    }

    /**
     * Generate goods qr code image
     */
    public function actionGoodsQrGen()
    {
        $id = (int)Yii::$app->request->get('id', 0);
        if ($id > 0) {
            $goods = Goods::findOne($id);
            $goods && $goods->generateQrCodeImage();
        }
    }

    /**
     * Register user
     *
     * @return string
     */
    public function actionRegisterUser()
    {
        $res = User::register(Yii::$app->request->post(), false);
        echo is_array($res) ? 'ok' : 'failed';
        is_array($res) && Yii::$app->trigger(Yii::$app->params['events']['async']);
    }

    /**
     * Upload test
     *
     * @return string
     */
    public function actionUpload()
    {
        return $this->render('upload');
    }

    /**
     * Login test
     *
     * @return string
     */
    public function actionLogin()
    {
        return $this->render('login');
    }


    public  function  actionReturnPost()
    {
        $data =Yii::$app->request->post();
        echo json_encode($data);
    }

    /**
     * @return string
     */
    public  function  actionBalanceAdd()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user=User::findOne($user->id);
        $user->balance=100000000;
        $user->availableamount=100000000;
        $user->save(false);
    }

    /**
     * 添加测试订单数据
     * @return bool|string
     * @throws Exception
     */
    public function actionAddTestOrderData(){
        $request=Yii::$app->request;
        $order_no=GoodsOrder::SetOrderNo();
        $goods_id=$request->post('goods_id');
        $goods_num=$request->post('goods_num');
        $mobile=$request->post('mobile');
        $address_id=$request->post('address_id');
        $invoice_id=$request->post('invoice_id');
        $goods=Goods::find()
            ->where(['id'=>$goods_id])
            ->one();
        if (!$goods){
            $c=1000;
            return Json::encode([
                'code' =>  $c,
                'msg'  =>'商品不存在'
            ]);
        }
        $supplier=Supplier::findOne($goods->supplier_id);
        if (!$supplier)
        {
            $c=1000;
            return Json::encode([
                'code' =>  $c,
                'msg'  =>'商家不存在'
            ]);
        }
        $LogisticsTemplate=LogisticsTemplate::findOne($goods->logistics_template_id);
        $Goods[]=[
            'goods_id'=>$goods_id,
            'goods_num'=>$goods_num
        ];
        if ($LogisticsTemplate->delivery_method==0)
        {
            $freight=GoodsOrder::CalculationFreightTest($Goods);
        }
        else
        {
            $freight=0;
        }
        $address=UserAddress::find()
            ->where(['id'=>$address_id])
            ->one();
        $invoice=Invoice::find()
            ->where(['id'=>$invoice_id])
            ->one();
        if (!$address  || !$invoice)
        {
            $c=1000;
            return Json::encode([
                'code' =>  $c,
                'msg'  =>'收货地址ID 或 发票ID 错误'
            ]);
        }
        $user=User::find()
            ->where(['mobile'=>$mobile])
            ->one();
        $amount_order=$goods->platform_price*$goods_num+$freight;
        $tran = Yii::$app->db->beginTransaction();
        $time=time();
        try{
            $GoodsOrder= new GoodsOrder();
            $GoodsOrder->order_no=$order_no;
            $GoodsOrder->amount_order=$amount_order;
            $GoodsOrder->pay_status=0;
            $GoodsOrder->create_time=$time;
            $GoodsOrder->paytime=$time;
            $GoodsOrder->pay_name='支付宝支付';
            $GoodsOrder->order_refer=2;
            $GoodsOrder->return_insurance=0;
            $GoodsOrder->role_id=7;
            $GoodsOrder->supplier_id=$goods->supplier_id;
            $GoodsOrder->user_id=$user->id;
            $GoodsOrder->buyer_message='请发快递给我';
            $GoodsOrder->consignee=$address->consignee;
            $GoodsOrder->district_code=$address->district;
            $GoodsOrder->region=$address->region;
            $GoodsOrder->consignee_mobile=$address->mobile;
            $GoodsOrder->invoice_type=$invoice->invoice_type;
            $GoodsOrder->invoice_header_type=$invoice->invoice_header_type;
            $GoodsOrder->invoicer_card=$invoice->invoicer_card;
            $GoodsOrder->invoice_header=$invoice->invoice_header;
            $GoodsOrder->invoice_content=$invoice->invoice_content;
            $res1=$GoodsOrder->save(false);
            $OrderGoods=new  OrderGoods();
            $OrderGoods->order_no=$order_no;
            $OrderGoods->goods_id=$goods->id;
            $OrderGoods->goods_number=$goods_num;
            $OrderGoods->goods_attr_id=12;
            $OrderGoods->create_time=$time;
            $OrderGoods->goods_name=$goods->title;
            $OrderGoods->goods_price=$goods->platform_price;
            $OrderGoods->sku=$goods->sku;
            $OrderGoods->market_price=$goods->market_price;
            $OrderGoods->supplier_price=$goods->supplier_price;
            $OrderGoods->shipping_type=$LogisticsTemplate->delivery_method;
            $OrderGoods->order_status=0;
            $OrderGoods->shipping_status=0;
            $OrderGoods->customer_service=0;
            $OrderGoods->is_unusual=0;
            $OrderGoods->freight=$freight;
            $OrderGoods->cover_image=$goods->cover_image;
            $OrderGoods->after_sale_services=$goods->after_sale_services;
            $OrderGoods->subtitle=$goods->subtitle;
            $OrderGoods->category_id=$goods->category_id;
            $OrderGoods->purchase_price_decoration_company=$goods->purchase_price_decoration_company;
            $OrderGoods->purchase_price_manager=$goods->purchase_price_manager;
            $OrderGoods->purchase_price_designer=$goods->purchase_price_designer;
            $OrderGoods->platform_price=$goods->platform_price;
            $res2= $OrderGoods->save(false);
            if (!$res1  || !$res2)
            {
                $tran->rollBack();
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $goodsAttr=GoodsAttr::find()
                ->where(['goods_id'=>$goods->id])
                ->all();
            foreach ($goodsAttr as &$attrs)
            {
                $OrderAttr=new OrderGoodsAttr();
                $OrderAttr->order_no=$order_no;
                $OrderAttr->sku=$goods->sku;
                $OrderAttr->name=$attrs->name;
                $OrderAttr->value=$attrs->value;
                $OrderAttr->unit=$attrs->unit;
                $OrderAttr->addition_type=$attrs->addition_type;
                $OrderAttr->goods_id=$attrs->goods_id;
                if (!$OrderAttr->save(false))
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg'  => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }
            $code=OrderStyle::AddNewData($goods->style_id,$order_no,$goods->sku);
            if ($code!=200)
            {
                $tran->rollBack();
                return false;
            }
            $code=OrderSeries::AddNewData($goods->series_id,$order_no,$goods->sku);
            if ($code!=200)
            {
                $tran->rollBack();
                return false;
            }
            $code=OrderGoodsImage::AddNewData($goods->id,$order_no,$goods->sku);
            if ($code!=200)
            {
                $tran->rollBack();
                return false;
            }
            $GoodsBrand=GoodsBrand::findOne($goods->brand_id);
            if ($GoodsBrand)
            {
                $orderGoodsBrand=new OrderGoodsBrand();
                $orderGoodsBrand->order_no=$order_no;
                $orderGoodsBrand->sku=$goods->sku;
                $orderGoodsBrand->name=$GoodsBrand->name;
                $orderGoodsBrand->logo=$GoodsBrand->logo;
                $orderGoodsBrand->certificate=$GoodsBrand->certificate;
                if (!$orderGoodsBrand->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
            }
            $month=date('Ym',$time);
            $Supplier=Supplier::find()
                ->where(['id'=>$goods->supplier_id])
                ->one();
            $Supplier->sales_volumn_month=$Supplier->sales_volumn_month+$goods_num;
            $Supplier->sales_amount_month=$Supplier->sales_amount_month+$goods->toArray()['platform_price']*$goods_num;
            $Supplier->month=$month;
            if (!$Supplier->save(false))
            {
                $tran->rollBack();
                return false;
            }
            $LogisticTemp=LogisticsTemplate::find()->where(['id'=>$goods->logistics_template_id])->asArray()->one();
            if ($LogisticTemp)
            {
                $orderLogisticTemp=new  OrderLogisticsTemplate();
                $orderLogisticTemp->order_no=$order_no;
                $orderLogisticTemp->sku=$goods->sku;
                $orderLogisticTemp->name=$LogisticTemp['name'];
                $orderLogisticTemp->delivery_method=$LogisticTemp['delivery_method'];
                $orderLogisticTemp->delivery_cost_default=$LogisticTemp['delivery_cost_default'];
                $orderLogisticTemp->delivery_number_default=$LogisticTemp['delivery_number_default'];
                $orderLogisticTemp->delivery_cost_delta=$LogisticTemp['delivery_cost_delta'];
                $orderLogisticTemp->delivery_number_delta=$LogisticTemp['delivery_number_delta'];
                if (!$orderLogisticTemp->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
                $LogisticDis=LogisticsDistrict::find()
                    ->where(['template_id'=>$goods->logistics_template_id])
                    ->all();
                if ($LogisticDis)
                {
                    foreach ($LogisticDis as  &$dis)
                    {
                        $OrderLogisticDis=new OrderLogisticsDistrict();
                        $OrderLogisticDis->order_template_id=$orderLogisticTemp->id;
                        $OrderLogisticDis->district_code=$dis->district_code;
                        $OrderLogisticDis->district_name=$dis->district_name;
                        if (!$OrderLogisticDis->save(false))
                        {
                            $tran->rollBack();
                            return false;
                        }
                    }
                }
            }
            $date=date('Ymd',time());
            $GoodsStat=GoodsStat::find()
                ->where(['supplier_id'=>$goods->supplier_id])
                ->andWhere(['create_date'=>$date])
                ->one();
            if (!$GoodsStat)
            {
                $GoodsStat=new GoodsStat();
                $GoodsStat->supplier_id=$goods->supplier_id;
                $GoodsStat->sold_number=$goods_num;
                $GoodsStat->amount_sold=$amount_order;
                $GoodsStat->create_date=$date;
                if (!$GoodsStat->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
            }else
            {
                $GoodsStat->sold_number+=$goods_num;
                $GoodsStat->amount_sold+=$amount_order;
                if (!$GoodsStat->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
            }
            if ($goods->left_number<$goods_num)
            {
                $tran->rollBack();
                return false;
            }
            $goods->left_number-=$goods_num;
            $goods->sold_number+=$goods_num;
            if (!$goods->save(false))
            {
                $tran->rollBack();
                return false;
            }
            $orderGoodsdescription=new OrderGoodsDescription();
            $orderGoodsdescription->order_no=$order_no;
            $orderGoodsdescription->sku=$goods->sku;
            $orderGoodsdescription->description=$goods->description;
            if (!$orderGoodsdescription->save(false))
            {
                $tran->rollBack();
                return false;
            }
            $tran->commit();
            return Json::encode([
                'code' =>  200,
                'msg'  => 'ok'
            ]);
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * Handle goods style
     *
     * @return string
     * @throws Exception
     */
    public function actionHandleGoodsStyle()
    {
        $goodsIds = GoodsStyle::find()->select(['goods_id'])->asArray()->column();
        $where = [
            'and',
            ['>', 'style_id', 0],
            ['not in', 'id', $goodsIds]
        ];
        $goodsIdsStyleIds = Goods::find()->select(['id', 'style_id'])->where($where)->asArray()->all();
        
        $tran = Yii::$app->db->beginTransaction();
        foreach ($goodsIdsStyleIds as $row) {
            $gy = new GoodsStyle;
            $gy->goods_id = $row['id'];
            $gy->style_id = $row['style_id'];
            if (!$gy->save(false)) {
                $tran->rollBack();
                return 'failed_' . $row['id'];
            }
        }
        $tran->commit();
        return 'ok';
    }

    public function actionUserView()
    {
        $id = (int)Yii::$app->request->get('id', 0);
        if ($id) {
            $data = User::findOne($id);
            return json_encode($data->attributes);
        }
    }

}
