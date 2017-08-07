<?php

namespace app\controllers;
use app\models\Addressadd;
use app\models\Supplieramountmanage;
use app\models\Wxpay;
use app\models\User;
use app\models\Alipay;
use app\models\GoodsOrder;
use app\models\Invoice;
use app\models\Express;
use app\models\Goods;
use app\models\Supplier;
use app\models\LogisticsDistrict;
use app\services\SmValidationService;
use app\services\AlipayTradeService;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;


class OrderController extends Controller
{


    public function init(){
        parent::init();
    }


    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'logout',
        'roles',
        'reset-password',
        'roles-status',
        'time-types',
        'upload',
        'upload-delete',
        'review-statuses',
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
                    'logout' => ['post',],
                    'reset-password' => ['post',],
                    'upload' => ['post',],
                    'upload-delete' => ['post',]
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
     * 获取库存
     * @return string
     */
    public function actionLinestocknum(){
        $request = Yii::$app->request;
        if ($request->isPost) {
            $goods_id = trim($request->post('goods_id',''),'');
            $data=Goods::find()->select('left_number')->where(['id'=>$goods_id])->one();
            return Json::encode([
                'code' => 200,
                'msg' => '返回库存量',
                'data' => ['number'=>$data['left_number']]
            ]);
        }else{
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => '请求方式错误',
                'data' => 0
            ]);
        }

    }

    /**
     * 获取商品id
     * @return string
     */
    public function actionLineGobuy(){
        $request = Yii::$app->request;
        if ($request->isPost) {
            $goods_id = trim($request->post('goods_id',''),'');
            $goods_num = trim($request->post('goods_num',''),'');
        }else{
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => 0
            ]);
        }
    }

    /**
     * 获取省份
     * @return string
     */
    public function actionGetprovince(){
        $data=Yii::$app->params['districts'];
        return Json::encode($data[0][86]);
    }

    /**
     * 获取城市
     * @return string
     */
    public  function  actionGetcity(){
        $request=Yii::$app->request;
        $code=trim(htmlspecialchars($request->post('code','')),'');
        if (!$code){
            $c=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$c]
            ]);
        }
        $data=Yii::$app->params['districts'];
        return Json::encode($data[0][$code]);
    }

    /**
     * 无登录app-添加收货地址
     * @return string
     */
    public function actionAdduseraddress()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $consignee = trim($request->post('consignee',''),'');
            $mobile= trim($request->post('mobile',''),'');
            $districtcode=trim($request->post('districtcode',''),'');
            $region=trim($request->post('region',''));
            if (!$districtcode || !$region  || !$mobile || !$consignee ) {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }else{
                $Addressadd = new Addressadd();
                $res=$Addressadd->insertaddress($mobile,$consignee,$region,$districtcode);
                if ($res==true){
                    return Json::encode([
                        'code' => 200,
                        'msg' => '添加收货地址成功',
                        'data' => '添加收货地址成功'
                    ]);
                }else
                {
                    $code=1051;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                        'data' => '添加收货地址失败'
                    ]);
                }
            }
        }else {
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => 0
            ]);
        }
    }

    /**
     * 无登录app-确认订单页面-获取收货地址
     * @return string
     */
    public function actionGetaddress(){
        $session = Yii::$app->session;
        $addresstoken=$session['addresstoken'];
        $model = new Addressadd();
        $user_address=$model->getaddress($addresstoken);
        if ($user_address){
            return Json::encode([
                'code' => 200,
                'msg'  => 'ok',
                'data' => $user_address
            ]);
        }else{
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }
    /**
     * 无登录app-添加发票信息
     * @return string
     */
    public function actionOrderinvoicelineadd(){
        $request = \Yii::$app->request->post();
        $invoice_type        = trim(htmlspecialchars($request['invoice_type']),' ');
        $invoice_header_type = 1;
        $invoice_header      = trim(htmlspecialchars($request['invoice_header']),' ');
        $invoice_content     = trim(htmlspecialchars($request['invoice_content']),' ');
        $invoicer_card = trim(htmlspecialchars($request['invoicer_card']),' ');
        if (!empty($invoicer_card)){
            $isMatched = preg_match('/^[0-9A-Z?]{18}$/', $invoicer_card, $matches);
            if ($isMatched==false){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        $model = new Invoice();
        $res=$model->addinvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content,$invoicer_card);
        if ($res['code']==200){
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data' => '添加发票成功'
            ]);
        }else{
            $code=$res['code'];
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }

    /**
     * 无登录app-获取商品信息
     * @return string
     */
    public function actionGetgoodsdata(){
        $request = Yii::$app->request;
        if ($request->isPost) {
            $goods_id=trim(htmlspecialchars($request->post('goods_id')),' ');
            $goods_num=trim(htmlspecialchars($request->post('goods_num')),' ');
            $model=new GoodsOrder();
            $data=$model->getlinegoodsdata($goods_id,$goods_num);
            if (!$goods_id || !$goods_num){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
            if ($data){
                return Json::encode([
                    'code' => 200,
                    'msg'  => 'ok',
                    'data' => $data
                ]);
            }
        }else{
            $code=1050;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }
    /**
     * 无登录app-获取发票信息
     * @return string
     */
    public function  actionGetinvoicelinedata()
    {
        $session = Yii::$app->session;
        $invoicetoken=$session['invoicetoken'];
        $model = new Invoice();
        $data=$model->getlineinvoice($invoicetoken);
        if ($data){
            return Json::encode([
                'code' => 200,
                'msg'  => 'ok',
                'data' => $data
            ]);
        }else{
            $code=1050;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }

    /**
     * 判断是否是微信登录
     */
    public function  actionIswxlogin(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            // 非微信浏览器禁止浏览
            return Json::encode([
                'code' => 201,
                'msg' =>'非微信打开',
            ]);
        } else {
            // 微信浏览器，允许访问

            return Json::encode([
                'code' => 200,
                'msg' =>'微信内打开',
            ]);
        }
    }

    /**
     * 线下店商城支付宝支付提交订单
     */
    public function actionAlipaylinesubmit(){
        $request=Yii::$app->request;
        //商户订单号，商户网站订单系统中唯一订单号，必填

            do {
                $code=date('md',time()).'1'.rand(10000,99999);
            } while ( $code==GoodsOrder::find()->select('order_no')->where(['order_no'=>$code])->asArray()->one()['order_no']);
        $out_trade_no = $code;
        $subject=trim(htmlspecialchars($request->post('goods_name')),' ');
        //付款金额，必填
        $total_amount =trim(htmlspecialchars($request->post('order_price')),' ');
        $goods_id=trim(htmlspecialchars($request->post('goods_id')),' ');
        $goods_num=trim(htmlspecialchars($request->post('goods_num')),' ');
        $districtcode=trim(htmlspecialchars($request->post('districtcode')),' ');
        $pay_name=trim(htmlspecialchars($request->post('pay_name')),' ');
        $invoice_id=trim(htmlspecialchars($request->post('invoice_id')),' ');
        //商品描述，可空
        $body = trim(htmlspecialchars($request->post('body')),' ');
        $model=new Alipay();
        $res=$model->Alipaylinesubmit($out_trade_no,$subject,$total_amount,$body,$goods_id, $goods_num,$districtcode,$pay_name,$invoice_id);
    }


    public function actionAlipaylinenotify(){
        $post=Yii::$app->request->post();
        $model=new Alipay();
        $alipaySevice=$model->Alipaylinenotify();
        $result = $alipaySevice->check($post);
        if($result){
            $content=json_encode($post);
            $res=Yii::$app->db->createCommand()->insert('alipayreturntest',[
                'content'      => $content,
            ])->execute();//验证成功/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//            //请在这里加上商户的业务逻辑程序代
//
//            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
//            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
//            //商户订单号
//            $out_trade_no = $_POST['out_trade_no'];
//
//            //支付宝交易号
//
//            $trade_no = $_POST['trade_no'];
//
//            //交易状态
//            $trade_status = $_POST['trade_status'];
//            if($_POST['trade_status'] == 'TRADE_FINISHED') {
//
//                //判断该笔订单是否在商户网站中已经做过处理
//                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
//                //如果有做过处理，不执行商户的业务程序
//
//                //注意：
//                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
//            }
//            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
//                //判断该笔订单是否在商户网站中已经做过处理
//                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
//                //如果有做过处理，不执行商户的业务程序
//                //注意：
//                //付款完成后，支付宝系统发送该交易状态通知
//            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";		//请不要修改或删除
        }else{
            //验证失败
            echo "fail";	//请不要修改或删除
        }
    }
    public function actionAlipaygetnotify(){
       $data=(new \yii\db\Query())->from('alipayreturntest')->one()['content'];

        echo $data;

    }
    /**
     * 快递查询类-物流跟踪接口
     *
     */
    public function  actionExpress(){

        $order="3933267921506";
        $express = new Express();
        $result  = $express -> getorder($order);
        return Json::encode($result);
    }

    /**
     * 大后台获取全部订单列表
     * @copyright        艾特魔方
     * @license
     * @lastmodify       2017-7-19
     */
    public function actionGetallorderlist(){
          $request = Yii::$app->request;
          $page=trim(htmlspecialchars($request->get('page','')),'');
          $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
          $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
              if (!$time_id){
                  $time_id=0;
              }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
            if ($time_id==5){
                if (!$time_start || !$time_end){
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg'  => Yii::$app->params['errorCodes'][$code],
                        'data' => null
                    ]);
                }
            }
            if (!$pagesize){
                $pagesize=15;
            }
            if (!$page){
                $page=1;
            }
          $model=new GoodsOrder();
          $data=$model->Getallorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend);
          return Json::encode([
              'code'=>200,
              'msg' =>'ok',
              'data' => $data
          ]);
    }

    /**
     * 大后台获取待付款订单
     * @return string
     */
    public function actionGetallunpaidorder(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        $model=new GoodsOrder();
        $data=$model->Getallunpaidorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 大后台获取待发货订单
     * @return string
     */
    public function actionGetallunshippedorder(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        $model=new GoodsOrder();
        $data=$model->Getallunshippedorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 大后台获取待收货订单
     * @return string
     */
    public function actionGetallunreceivedorder(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        $model=new GoodsOrder();
        $data=$model->Getallunreceivedorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 大后台获取已完成订单
     * @return string
     */
    public function actionGetallcompeleteorder(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        $model=new GoodsOrder();
        $data=$model->Getallcompeletedorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 大后台获取已取消订单
     * @return string
     */
    public function  actionGetallcanceledorder(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        $model=new GoodsOrder();
        $data=$model->Getallcanceledorderdata($pagesize,$page,$time_id,$time_start,$time_end,$blend);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 大后台获取售后订单
     * @return string
     */
    public function actionGetallcustomerservicedorder(){
        $model=new GoodsOrder();
        $data=$model->Getallcustomerserviceorderdata();
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     *大后台之查看订单详情
     */
    public function actionGetorderdetailsall(){
        $request=Yii::$app->request;
//        $goods_id=trim(htmlspecialchars($request->get('goods_id','')),'');
//        $order_id=trim(htmlspecialchars($request->get('order_id','')),'');
        $goodsid=1;
        $order_id=1;
        //获取订单信息
        $order_information=(new GoodsOrder())->Getorderinformation($order_id,$goodsid);
        if (!$order_information) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        //获取商品信息
        $goods_name=$order_information['goods_name'];
        $goods_id=$order_information['goods_id'];
        $goods_attr_id=$order_information['goods_attr_id'];
        $order_no=$order_information['order_no'];
        $sku=explode('+',$order_information['sku']);
        $ordergoodsinformation=(new GoodsOrder())->Getordergoodsinformation($goods_name,$goods_id,$goods_attr_id,$order_no,$sku);
        if (!$ordergoodsinformation){
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        //获取收货详情
        $address_id=$order_information['address_id'];
        $invoice_id=$order_information['invoice_id'];
        $address=Addressadd::find()->where(['id'=>$address_id])->asArray()->one();
        if (!$address){
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => '收货地址不存在'
            ]);
        }
        $model=new LogisticsDistrict();
        $address['district']=$model->getdistrict($address['district']);
        $invoice=Invoice::find()->where(['id'=>$invoice_id])->asArray()->one();
        if (!$invoice){
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => '发票信息为空'
            ]);
        }
        $receive_details['consignee']=$address['consignee'];
        $receive_details['mobile']=$address['mobile'];
        $receive_details['district']=$address['district'];
        $receive_details['region']=$address['region'];
        $receive_details['invoice_header']=$invoice['invoice_header'];
        $receive_details['invoice_header_type']=$invoice['invoice_header_type'];
        $receive_details['invoice_content']=$invoice['invoice_content'];
        $goods_data=array();
        if ($order_information['goods_name']=='+'){
            $goods_data['goods_name']='';
        }else{
            $goods_data['goods_name']=$order_information['goods_name'];
        }
        $goods_data['status']=$order_information['status'];
        $goods_data['order_no']=$order_information['order_no'];
        $goods_data['username']=$order_information['username'];
        $goods_data['amount_order']=$order_information['amount_order'];
        $goods_data['goods_price']=$order_information['goods_price'];
        $goods_data['freight']=$order_information['freight'];
        $goods_data['return_insurance']=$order_information['return_insurance'];
        $goods_data['supplier_price']=$order_information['supplier_price'];
        $goods_data['market_price']=$order_information['market_price'];
        $goods_data['goods_num']=$order_information['goods_num'];
        $goods_data['waybillnumber']=$order_information['waybillnumber'];
        $goods_data['waybillname']=$order_information['waybillname'];
        if ($order_information['status']=='未付款'){
            $goods_data['pay_term']=$order_information['pay_term'];
        }else{
            $goods_data['pay_term']=0;
        }
        if ($order_information['paytime']!=0){
            $goods_data['paytime']=$order_information['paytime'];
        }
        $goods_data['create_time']=$order_information['create_time'];
        $data=array(
            'goods_data'=>$goods_data,
            'goods_value'=>$ordergoodsinformation,
            'receive_details'=>$receive_details
        );
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data' =>$data
        ]);
    }

    /**
     * 判断收货地址是否在指定区域内
     * @return string
     */
    public function actionJudegaddress(){
        $request=Yii::$app->request;
        $districtcode=trim(htmlspecialchars($request->post('districtcode','')),'');
        $goods_id=trim(htmlspecialchars($request->post('goods_id','')),'');
        if (!$districtcode || !$goods_id){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $template_id=Goods::find()->select('logistics_template_id')->where(['id'=>$goods_id])->asArray()->one()['logistics_template_id'];
        $model=new LogisticsDistrict();
        $data=$model->is_apply($districtcode,$template_id);
        if ($data==200){
            return Json::encode([
                'code' => 200,
                'msg' =>'收货地址正常',
            ]);
        }else{
            return Json::encode([
                'code' => $data,
                'msg' => '收货地址异常'
            ]);
        }
    }


    /**
     * 商家后台-获取全部订单信息
     * @return string
     */
    public function actionBusinessgetallorderlist(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
        $data=(new GoodsOrder())->Businessgetallorderlist($supplier_id,$pagesize,$page,$time_id,$time_start,$time_end,$blend);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }


    /**
     * 获取待付款订单列表_商家后台
     * @return string
     */
    public function actionBusinessgetunpaidorder(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
        $data=(new GoodsOrder())->Businessgetunpaidorder($supplier_id,$pagesize,$page,$time_id,$time_start,$time_end,$blend);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }

    /**
     * 商家后台-获取待发货订单
     * @return string
     */
    public function actionBusinessgetnotshippedorder(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $blend=trim(htmlspecialchars($request->post('blend','')),'');
        if (!$blend){
            $blend=0;
        }
        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
            $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
            $data=(new GoodsOrder())->Businessgetnotshippedorder($supplier_id,$pagesize,$page,$time_id,$time_start,$time_end,$blend);
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$data
            ]);
    }

    /**
     * 商家后台-获取待收货列表
     * @return string
     */
    public function actionBusinessgetnotreceivedorder(){
    $request = Yii::$app->request;
    $page=trim(htmlspecialchars($request->get('page','')),'');
    $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
    $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
    if (!$time_id){
        $time_id=0;
    }
    $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
    $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
    $blend=trim(htmlspecialchars($request->post('blend','')),'');
    if (!$blend){
        $blend=0;
    }
    if ($time_id==5){
        if (!$time_start || !$time_end){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }
    if (!$pagesize){
        $pagesize=15;
    }
    if (!$page){
        $page=1;
    }
    $user = Yii::$app->user->identity;
    if (!$user){
        $code=1052;
        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code]
        ]);
    }
    $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
            $model=new GoodsOrder();
            $data=$model->Businessgetnotreceivedorder($supplier_id,$pagesize,$page,$time_id,$time_start,$time_end,$blend);
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$data
            ]);

    }

    /**
     * 获取商家后台已完成订单列表
     * @return string
     */
    public function actionBusinessgetcompletedorder(){
    $request = Yii::$app->request;
    $page=trim(htmlspecialchars($request->get('page','')),'');
    $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
    $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
    if (!$time_id){
        $time_id=0;
    }
    $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
    $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
    $blend=trim(htmlspecialchars($request->post('blend','')),'');
    if (!$blend){
        $blend=0;
    }
    if ($time_id==5){
        if (!$time_start || !$time_end){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }
    if (!$pagesize){
        $pagesize=15;
    }
    if (!$page){
        $page=1;
    }
    $user = Yii::$app->user->identity;
    if (!$user){
        $code=1052;
        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code]
        ]);
    }
            $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
            $model=new GoodsOrder();
            $data=$model->Businessgetcompletedorder($supplier_id,$pagesize,$page,$time_id,$time_start,$time_end,$blend);
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$data
            ]);

    }

    /**
     * 获取商家后台已取消订单
     * @return string
     */
    public function actionBusinessgetcanceledorder(){
            $request = Yii::$app->request;
            $page=trim(htmlspecialchars($request->get('page','')),'');
            $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
            $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
            if (!$time_id){
                $time_id=0;
            }
                $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
            $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
            $blend=trim(htmlspecialchars($request->post('blend','')),'');
            if (!$blend){
                $blend=0;
            }
            if ($time_id==5){
                if (!$time_start || !$time_end){
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg'  => Yii::$app->params['errorCodes'][$code],
                        'data' => null
                    ]);
                }
            }
            if (!$pagesize){
                $pagesize=15;
            }
            if (!$page){
                $page=1;
            }
            $user = Yii::$app->user->identity;
            if (!$user){
                $code=1052;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
            $data=(new GoodsOrder())->Businessgetcanceledorder($supplier_id,$pagesize,$page,$time_id,$time_start,$time_end,$blend);
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$data
            ]);

    }


    /**
     * 获取已完成售后处理订单
     * @return string
     */
    public function actionBusinessgetcustomerserviceorder(){
    $request = Yii::$app->request;
    $page=trim(htmlspecialchars($request->get('page','')),'');
    $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
    $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
    if (!$time_id){
        $time_id=0;
    }
    $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
    $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
    $blend=trim(htmlspecialchars($request->post('blend','')),'');
    if (!$blend){
        $blend=0;
    }
    if ($time_id==5){
        if (!$time_start || !$time_end){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }
    if (!$pagesize){
        $pagesize=15;
    }
    if (!$page){
        $page=1;
    }
    $user = Yii::$app->user->identity;
    if (!$user){
        $code=1052;
        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code]
        ]);
    }
            $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
            $data=(new GoodsOrder())->Businessgetcustomerserviceorder($supplier_id,$pagesize,$page,$time_id,$time_start,$time_end,$blend);
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$data
            ]);
    }

    /**
     * 商家后台获取订单详情
     * @return string
     */
    public function actionGetsupplierorderdetails(){
        $request=Yii::$app->request;
//        $goods_id=trim(htmlspecialchars($request->get('goods_id','')),'');
//        $order_id=trim(htmlspecialchars($request->get('order_id','')),'');
            $goodsid=1;
            $order_id=1;
            //获取订单信息
            $order_information=(new GoodsOrder())->Getorderinformation($order_id,$goodsid);
            if (!$order_information) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            //获取商品信息
            $goods_name=$order_information['goods_name'];
            $goods_id=$order_information['goods_id'];
            $goods_attr_id=$order_information['goods_attr_id'];
            $order_no=$order_information['order_no'];
            $sku=explode('+',$order_information['sku']);
            $ordergoodsinformation=(new GoodsOrder())->Getordergoodsinformation($goods_name,$goods_id,$goods_attr_id,$order_no,$sku);
            if (!$ordergoodsinformation){
                $code = 500;
               return Json::encode([
                   'code' => $code,
                   'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            //获取收货详情
            $address_id=$order_information['address_id'];
            $invoice_id=$order_information['invoice_id'];
            $address=Addressadd::find()->where(['id'=>$address_id])->asArray()->one();
            if (!$address){
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => '收货地址不存在'
                ]);
            }

            $model=new LogisticsDistrict();
            $address['district']=$model->getdistrict($address['district']);
            $invoice=Invoice::find()->where(['id'=>$invoice_id])->asArray()->one();
            if (!$invoice){
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => '发票信息为空'
                ]);
            }
            $receive_details['consignee']=$address['consignee'];
            $receive_details['mobile']=$address['mobile'];
            $receive_details['district']=$address['district'];
            $receive_details['region']=$address['region'];
            $receive_details['invoice_header']=$invoice['invoice_header'];
            $receive_details['invoice_header_type']=$invoice['invoice_header_type'];
            $receive_details['invoice_content']=$invoice['invoice_content'];

            $goods_data=array();

              if ($order_information['goods_name']=='+'){
                  $goods_data['goods_name']='';
              }else{
                  $goods_data['goods_name']=$order_information['goods_name'];
              }
              $goods_data['status']=$order_information['status'];
              $goods_data['order_no']=$order_information['order_no'];
              $goods_data['username']=$order_information['username'];
              $goods_data['money_paid']=$order_information['money_paid'];
              $goods_data['goods_price']=$order_information['goods_price'];
              $goods_data['freight']=$order_information['freight'];
              $goods_data['return_insurance']=$order_information['return_insurance'];
              $goods_data['supplier_price']=$order_information['supplier_price'];
              $goods_data['market_price']=$order_information['market_price'];
              $goods_data['goods_num']=$order_information['goods_num'];
              $goods_data['waybillnumber']=$order_information['waybillnumber'];
              $goods_data['waybillname']=$order_information['waybillname'];
              if ($order_information['status']=='未付款'){
                  $goods_data['pay_term']=$order_information['pay_term'];
              }else{
                  $goods_data['pay_term']=0;
              }
              if ($order_information['paytime']!=0){
                  $goods_data['paytime']=$order_information['paytime'];
              }
              $goods_data['create_time']=$order_information['create_time'];
                $data=array(
                    'goods_data'=>$goods_data,
                    'goods_value'=>$ordergoodsinformation,
                    'receive_details'=>$receive_details
                );
                $code = 200;
                return Json::encode([
                    'code' => $code,
                    'msg' => 'ok',
                    'data' =>$data
                ]);
    }
    /**
     * 添加快递单号
     * @return string
     */
    public function actionExpressadd()
    {
        $request = Yii::$app->request;
        $sku = trim(htmlspecialchars($request->post('sku', '')), '');
        $order_no = trim(htmlspecialchars($request->post('order_no', '')), '');
        $waybillname = trim(htmlspecialchars($request->post('waybillname', '')), '');
        $waybillnumber = trim(htmlspecialchars($request->post('waybillnumber', '')), '');
        if (!$sku || !$waybillname || !$waybillnumber || !$order_no) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $model = new  Express();
        $res = $model->Expressadd($sku, $waybillname, $waybillnumber, $order_no);
        if ($res) {
            return Json::encode([
                'code' => 200,
                'msg' => '添加成功',
                'data'=>$order_no
            ]);
        }else {
            return Json::encode([
                'code' => 500,
                'msg' => '快递单号已存在',
            ]);
        }
    }

    /**
     * 修改快递单号
     * @return string
     */
    public function actionExpressupdate(){
        $request = Yii::$app->request;
        $waybillname= trim(htmlspecialchars($request->post('waybillname', '')), '');
        $waybillnumber= trim(htmlspecialchars($request->post('waybillnumber', '')), '');
        $order_no= trim(htmlspecialchars($request->post('order_no', '')), '');
        $sku=trim(htmlspecialchars($request->post('sku', '')), '');
        $data=Express::find()->select('waybillnumber,waybillname')->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        if (!$data || !$waybillnumber || !$waybillname){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $res=Express::Expressupdate($waybillnumber,$waybillname,$sku,$order_no);

        if ($res){
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok',
            ]);
        }else{
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => '修改失败，参数未做任何修改',
            ]);
        }


    }

    /**
     * 获取物流信息
     * @return string
     */
    public function actionGetexpress(){
        $request=Yii::$app->request;
        $order_no=trim(htmlspecialchars($request->post('order_no','')),'');
        $sku=trim(htmlspecialchars($request->post('sku','')),'');
        if (!$order_no  || !$sku) {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $waybill=Express::find()->select('waybillnumber,waybillname,create_time')->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        if (!$waybill){
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => '物流信息不存在'
            ]);
        }else{
            $arr=array(
                'time'=>date('Y-m-d H:i:s',$waybill['create_time']),
                'ftime'=>date('Y-m-d H:i:s',$waybill['create_time']),
                'context'=>'卖家已发货'
            );
        $waybillnumber=$waybill['waybillnumber'];
        $model=new Express();
        $result=$model->getorder($waybillnumber);
        $data=Express::Expresslist($result,$arr);
        return Json::encode([$data]);
        }
    }



    public function actionAsd(){
        $a=rand(10000,99999);
        echo $a;
    }


    /**
     * 测试支付宝
     */
    public function actionAlipay(){
        $post=Yii::$app->request->post();
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $post['WIDout_trade_no'];

        //订单名称，必填
        $subject = $post['WIDsubject'];

        //付款金额，必填
        $total_amount = $post['WIDtotal_amount'];

        //商品描述，可空
        $body = $post['WIDbody'];
        $model=new Alipay();
        $res=$model->Alipay($out_trade_no,$subject,$total_amount,$body);
    }

    /**
     *提交订单-线下店商城-微信支付
     */
    public function  actionLineplaceorder(){
        $request=Yii::$app->request;
        $address_id=trim(htmlspecialchars($request->post('address_id','')),'');
        $invoice_id=trim(htmlspecialchars($request->post('invoice_id','')),'');
        $goods_id=1;
//        $goods_id=trim(htmlspecialchars($request->post('goods_id','')),'');
        $goods_num=trim(htmlspecialchars($request->post('goods_id','')),'');
        $goods_attr=trim(htmlspecialchars($request->post('goods_attr','')),'');
        $paymentmethod=trim(htmlspecialchars($request->post('paymentmethod','')),'');
        $goods_price=trim(htmlspecialchars($request->post('paymentmethod','')),'');
        $orders=array(
            'address_id'=>$address_id,
            'invoice_id'=>$invoice_id,
            'goods_id'=>$goods_id,
            'goods_num'=>$goods_num,
            'goods_attr'=>$goods_attr,
            'paymentmethod'=>$paymentmethod,
            'goods_price'=>$goods_price
        );
//        $goods=Goods::find()->select('title,subtitle')->where(['id' =>$goods_id])->one();
//        $invoice=Invoice::find()->select('')->where(['id' =>$goods_id])->one();
        $local=$_SERVER['SERVER_NAME'];
        $model=new Wxpay();
        $model->Wxlineapipay($orders,$local);
    }

    public function actionTestwxpay(){
        $model=new Wxpay();
        $res=$model->Wxpay();
        echo $res;
    }



}