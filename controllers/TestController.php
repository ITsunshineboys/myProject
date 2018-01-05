<?php

namespace app\controllers;

use app\models\Bank;
use app\models\Carousel;
use app\models\Express;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsOrder;
use app\models\Invoice;
use app\models\LogisticsTemplate;
use app\models\OrderAfterSale;
use app\models\OrderGoods;
use app\models\OrderGoodsAttr;
use app\models\OrderPlatForm;
use app\models\ShippingCart;
use app\models\Supplier;
use app\models\User;
use app\models\UserAddress;
use app\models\UserRole;
use app\services\ExceptionHandleService;
use app\services\StringService;
use Symfony\Component\Yaml\Tests\B;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

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
        Yii::$app->trigger(Yii::$app->params['events']['async']);
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

    /**
     * 验证银行卡测试时
     */
    public function actionVerificationBank()
    {
       $bank_card='13121564684864546123';
       $id_card='511302199112131914';
       $id_name='何友志';
       $bank=Bank::find()->where(['bank_card'=>$bank_card])->one();
       if ($bank)
       {
           $code=$id_card==$bank_card->id_card?200:1000;

       }else
       {
           $tran=Yii::$app->db->transaction;
           $url='';
           try{
                $result=StringService::httpGet($url);
                if ($result)
                {

                    $code=200;
                }else
                {
                    $code=1000;
                }
               $bank=new Bank();
               $bank->bank_card=$result['bank_card'];
               $bank->id_card=$result['id_card'];
               $bank->id_name=$result['id_name'];
               if (!$bank->save(false))
               {
                   $tran->rollBack();
               }
               $tran->commit();

           }catch (\Exception $e){
               $tran->rollBack();
               $code=500;
               return Json::encode([
                   'code' => $code,
                   'msg'  => Yii::$app->params['errorCodes'][$code]
               ]);
           }
       }
        return Json::encode([
            'code' => $code,
            'msg'  => 200?'ok':Yii::$app->params['errorCodes'][$code]
        ]);



    }

    /**
     * Test wxa
     */
    public function actionWx()
    {
        return $this->render('wx');
    }


    public  function  actionReturnPost()
    {
        $data =Yii::$app->request->post();
        echo json_encode($data);
    }


    public  function  actionUpData()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $lists=ShippingCart::find()
            ->where(['uid'=>$user->id,'role_id'=>$user->last_role_id_app])
            ->all();
        foreach ($lists as &$list)
        {
            $list->delete();
        }
    }

    public  function  actionDelInvalidData()
    {
        $GoodsOrder=GoodsOrder::find()->all();
        foreach ($GoodsOrder as &$list)
        {
            $supplier=Supplier::findOne($list->supplier_id);
            if (!$supplier)
            {
                $OrderGoods=OrderGoods::find()->where(['order_no'=>$list->order_no])->all();
                foreach ($OrderGoods as &$orderGoods)
                {
                    $res1=$orderGoods->delete();
                    if (!$res1)
                    {
                        echo 2;
                    }
                }
                $res=$list->delete();
                if (!$res)
                {
                    echo 2;
                }
            }
        }
        echo 1;
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
     * @return string
     */
    public  function  actionBalanceDelete()
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
        $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
        $supplier->balance=0;
        $supplier->availableamount=0;
        $supplier->save(false);
    }

    /**
     * 获取支付测试数据
     * @return string
     */
    public function actionAliPayGetNotify(){
        $data=(new Query())->from('alipayreturntest')->all();
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 测试接口
     * @return int
     */
    public  function  actionPlatformUp()
    {
        $request    = Yii::$app->request;
        $order_no   = trim($request->post('order_no',''));
        $sku        = trim($request->post('sku',''));
        $handle_type= trim($request->post('handle_type',''));

        $OrderPlatForm=OrderPlatForm::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->one();
        $OrderPlatForm->handle=$handle_type;
        $res=$OrderPlatForm->save(false);
        if (!$res){
            $code=500;
            return $code;
        }
    }

    public  function  actionTestData()
    {
//        $request    = Yii::$app->request;
//        $order_no   = trim($request->post('order_no',''));
//        $sku        = trim($request->post('sku',''));
//        $time=trim($request->post('time',''));
//        $express=Express::find()->where(['order_no'=>$order_no,'sku'=>$sku])->one();
//        if ($express)
//        {
//            $express->receive_time=strtotime($time);
//            $express->save(false);
//        }else
//        {
//            echo 2;
//        };

        $user=User::find()->all();
        return Json::encode($user);

    }

    public  function actionSendData(){
        $requestData= "{'OrderCode':'','ShipperCode':'STO','LogisticCode':'3345244122453'}";

        //商户ID：1297184
       // API key：0cdb787d-0542-4bef-bd2e-02826d7e52d4
        $datas = array(
            'EBusinessID' => '1297184',
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = Express::encrypt($requestData, '0cdb787d-0542-4bef-bd2e-02826d7e52d4');
        $result=Express::sendPost('http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx', $datas);
        var_dump($result);die;
        //根据公司业务处理返回的信息......

    }


     /**
     * 添加测试订单数据
     * @return string
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
        $user=User::find()->where(['mobile'=>$mobile])->one();
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

    public  static  function  actionTest()
    {
            echo 1;
    }
    public  static  function  actionTest1()
    {
       echo  StringService::getClientIP();
        echo  Yii::$app->session['PHPSESSID'];die;

    }





}
