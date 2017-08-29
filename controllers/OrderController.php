<?php

namespace app\controllers;
use app\models\Addressadd;
use app\models\Supplieramountmanage;
use app\models\Wxpay;
use app\models\EffectEarnst;
use app\models\User;
use app\models\Alipay;
use app\models\GoodsOrder;
use app\models\Invoice;
use app\models\Express;
use app\models\Goods;
use app\models\Supplier;
use app\models\LogisticsDistrict;
use app\models\Lhzz;
use app\models\UserRole;
use app\services\SmValidationService;
use app\services\AlipayTradeService;
use app\services\ExceptionHandleService;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;


class OrderController extends Controller
{


    const WXPAY_LINE_GOODS='线下店商城';
 

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

    // public function actionSetuserrole()
    // {
    //     $mobile=18208142446;
    //     $user=User::find()->where(['mobile'=>$mobile])->asArray()->one();
    //     if (!$user) {
    //         echo 2;
    //         exit;
    //     }
    //     $user_id=$user['id'];
    //     $review_status=2;
    //     $time=time();

    //     $res= \Yii::$app->db->createCommand()->insert('user_role',[
    //                     'user_id'    =>$user_id,
    //                     'review_status' =>2,
    //                     'review_apply_time'      =>$time,
    //                     'review_time'=>$time,
    //                     'role_id'=>6
    //                 ])->execute();
    //     if($res){
    //         echo 1;
    //     }
    // }

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
                $res=Addressadd::insertaddress($mobile,$consignee,$region,$districtcode);
                if ($res==true){
                    return Json::encode([
                        'code' => 200,
                        'msg' => 'ok',
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
        $user_address=Addressadd::getaddress($addresstoken);
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
        $request = \Yii::$app->request;
        $invoice_type        = trim(htmlspecialchars($request->post('invoice_type')));

        $invoice_header_type = 1;
        $invoice_header      = trim(htmlspecialchars($request->post('invoice_header')));
        $invoice_content     = trim(htmlspecialchars($request->post('invoice_content')));
        if (!$invoice_type||!$invoice_header||!$invoice_content )
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $invoicer_card =trim(htmlspecialchars($request->post('invoicer_card')));
        if ($invoicer_card){
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
        $res=Invoice::addinvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content,$invoicer_card);
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
            if (!$goods_id || !$goods_num){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
            $data=GoodsOrder::getlinegoodsdata($goods_id,$goods_num);
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
     * 智能报价-样板间支付定金提交
     * @return string
     */
    public function actionEffectEarnstAlipaySub(){
        $request=Yii::$app->request;
        $effect_id = trim($request->post('effect_id', ''), '');
        $name = trim($request->post('name', ''), '');
        $phone = trim($request->post('phone', ''), '');
        if (!preg_match('/^[1][3,5,7,8]\d{9}$/', $phone)) {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $out_trade_no =self::Setorder_no();
        Alipay::effect_earnstsubmit($effect_id,$name,$phone,$out_trade_no);
    }

      public function actionGetEffectlist(){
        $effect=EffectEarnst::find()
            ->asArray()
            ->all();
        foreach ($effect as $k  =>$v)
        {
            $effect[$k]['create_time']=date('Y-m-d H:i',$effect[$k]['create_time']);
        }
        return Json::encode([
            'code' =>  200,
            'msg'  => 'ok',
            'data' => $effect
        ]);
    }

    /**
     * 样板间支付订单异步返回
     */
    public function actionAlipayeffect_earnstnotify()
    {
        $post=Yii::$app->request->post();
        $model=new Alipay();
        $alipaySevice=$model->Alipaylinenotify();
        $result = $alipaySevice->check($post);
        if ($result){
            if ($post['trade_status'] == 'TRADE_SUCCESS') {
                $arr=explode('&',$post['passback_params']);
                // if ($post['total_amount'] !=89){
                //     exit;
                // }
                $res=GoodsOrder::Alipayeffect_earnstnotifydatabase($arr,$post);
                if ($res){
                    echo "success";
                }
            }
        }else{
            //验证失败
            echo "fail";    //请不要修改或删除
        }
    }

    /**
     * 线下店商城支付宝支付提交订单
     */
    public function actionAlipaylinesubmit(){
        $request=Yii::$app->request;
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no =self::Setorder_no();
        $subject=trim($request->post('goods_name'),' ');
        //付款金额，必填
        $total_amount =trim($request->post('order_price'),' ');
        $goods_id=trim($request->post('goods_id'),' ');
        $goods_num=trim($request->post('goods_num'),' ');
        $address_id=trim($request->post('address_id'),' ');
        $pay_name='线上支付-支付宝支付';
        $invoice_id=trim($request->post('invoice_id'),' ');
        $supplier_id=trim($request->post('supplier_id'),' ');
        $freight=trim($request->post('freight'),' ');
        $return_insurance=trim($request->post('return_insurance'),' ');
        $buyer_message=trim($request->post('buyer_message','0'));
        //商品描述，可空
        $body = trim($request->post('body'),' ');
        if (!$subject||!$total_amount||!$goods_id ||!$goods_num||!$address_id||! $invoice_id||!$supplier_id||!$freight ){
            $c=1000;
            return Json::encode([
                'code' =>  $c,
                'msg'  => Yii::$app->params['errorCodes'][$c],
                'data' => null
            ]);
        }
        $iscorrect_money=GoodsOrder::judge_order_money($goods_id,$total_amount,$goods_num,$return_insurance,$freight);
        if ($iscorrect_money!=true)
        {
            $c=1000;
            return Json::encode([
                'code' =>  $c,
                'msg'  => Yii::$app->params['errorCodes'][$c]
            ]);
        }
        $model=new Alipay();
        $res=$model->Alipaylinesubmit($out_trade_no,$subject,$total_amount,$body,$goods_id, $goods_num,$address_id,$pay_name,$invoice_id,$supplier_id,$freight,$return_insurance,$buyer_message);
    }

    /**
     * 支付宝线下店商城异步返回操作
     */
    public function actionAlipaylinenotify(){
        $post=Yii::$app->request->post();
        $model=new Alipay();
        $alipaySevice=$model->Alipaylinenotify();
        $result = $alipaySevice->check($post);
        if ($result){
            if ($post['trade_status'] == 'TRADE_SUCCESS') {
                $arr=explode('&',$post['passback_params']);
                $order_no=$post['out_trade_no'];
                $order=GoodsOrder::find()->select('order_no')->where(['order_no'=>$order_no])->asArray()->one();
                if ($order){
                    exit;
                }
                $res=GoodsOrder::Alipaylinenotifydatabase($arr,$post);
                if ($res==true){
                    echo "success";     //请不要修改或删除
                }else{
                    echo "fail";
                }
            }
        }else{
            //验证失败
            echo "fail";    //请不要修改或删除
        }
    }

    public function actionAlipaygetnotify(){
        $data=(new \yii\db\Query())->from('alipayreturntest')->all();
        var_dump($data);
    }

     /**
     * wxpay effect sub
     * 微信样板间支付
     * @return string
     */
    public function actionWxpayEffectEarnstSub(){
        $request=Yii::$app->request;
        $effect_id = trim($request->post('effect_id', ''), '');
        $name = trim($request->post('name', ''), '');
        $phone = trim($request->post('phone', ''), '');
        $money=0.01;
        if (!preg_match('/^[1][3,5,7,8]\d{9}$/', $phone)) {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        if ( !$name  ||!$phone || !$effect_id){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $res=Wxpay::effect_earnstsubmit($effect_id,$name,$phone,$money);
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'data' => $res
        ]);
    }
   /**
     *提交订单-线下店商城-微信支付
     */
    public function  actionLineplaceorder(){
        $request=Yii::$app->request;
        $subject=trim(htmlspecialchars($request->post('goods_name')),' ');
        //付款金额，必填
        $total_amount =trim(htmlspecialchars($request->post('order_price')),' ');
        $goods_id=trim(htmlspecialchars($request->post('goods_id')),' ');
        $goods_num=trim(htmlspecialchars($request->post('goods_num')),' ');
        $address_id=trim(htmlspecialchars($request->post('address_id')),' ');
        $pay_name='线上支付-微信支付';
        $invoice_id=trim(htmlspecialchars($request->post('invoice_id')),' ');
        $supplier_id=trim(htmlspecialchars($request->post('supplier_id')),' ');
        $freight=trim(htmlspecialchars($request->post('freight')),' ');
        $return_insurance=trim(htmlspecialchars($request->post('return_insurance')),'0');
        $buyer_message=trim($request->post('buyer_message','0'));
        if (!$total_amount || !$goods_id || !$goods_num || !$address_id || !$pay_name || $invoice_id || !$supplier_id || !$freight )
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $order_no =self::Setorder_no();
        //商品描述，可空
        $body =self::WXPAY_LINE_GOODS.'-'.$subject;
        $orders=array(
            'address_id'=>$address_id,
            'invoice_id'=>$invoice_id,
            'goods_id'=>$goods_id,
            'goods_num'=>$goods_num,
            'order_price'=>$total_amount,
            'goods_name'=>$subject,
            'pay_name'=>$pay_name,
            'supplier_id'=>$supplier_id,
            'freight'=>$freight,
            'return_insurance'=>$return_insurance,
            'body'=>$body,
            'order_no'=>$order_no,
            'buyer_message'=>$buyer_message
        );
        $model=new Wxpay();
        $data=$model->Wxlineapipay($orders);
        return Json::encode([
            'code' => 200,
            'msg' =>'ok',
            'data'=>$data
        ]);
    }
    /**
     * 微信公众号样板间申请定金异步返回
     * wxpay notify action
     * wxpay nityfy apply Deposit database
     * @return bool
     */
    public function actionWxpayeffect_earnstnotify(){
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $res=(new wxpay())->Orderlinewxpaynotify($msg);
        if ($res==true){
            $arr=explode('&',$msg['attach']);
//             if ($msg['total_fee'] !=8900){
//                    exit;
//             }
            $result=GoodsOrder::Wxpayeffect_earnstnotify($arr,$msg);
            if ($result==true){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     *微信线下支付异步操作
     */
    public function actionOrderlinewxpaynotify(){
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $res=Wxpay::NotifyProcess($msg);
        if ($res==true){
            $msg= Yii::$app->request->post();
            $arr=explode('&',$msg['attach']);
            $order=GoodsOrder::find()->select('order_no')->where(['order_no'=>$arr[8]])->asArray()->one();
            if ($order){
                return true;
            }
            $result=GoodsOrder::Wxpaylinenotifydatabase($arr,$msg);
            if ($result==true){
              return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=12;
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
        $lhzz=Lhzz::find()->where(['uid' => $user->id])->one()['id'];
        if (!$lhzz){
             $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
          $data=GoodsOrder::Getallorderdata($page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
          return Json::encode([
              'code'=>200,
              'msg' =>'ok',
              'data' => $data
          ]);
    }

    /**
     * 大后台搜索界面
     * @return string
     */
    public function actionOrder_search(){
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$search){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        if (!$page_size){
            $page_size=15;
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
        $lhzz=Lhzz::find()->where(['uid' => $user->id])->one()['id'];
        if (!$lhzz){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=GoodsOrder::Getallorderdata($page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
        $lhzz=Lhzz::find()->where(['uid' => $user->id])->one()['id'];
        if (!$lhzz){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=GoodsOrder::Getallunpaidorderdata($page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=12;
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
        $lhzz=Lhzz::find()->where(['uid' => $user->id])->one()['id'];
        if (!$lhzz){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=GoodsOrder::Getallunshippedorderdata($page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
        $lhzz=Lhzz::find()->where(['uid' => $user->id])->one()['id'];
        if (!$lhzz){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=GoodsOrder::Getallunreceivedorderdata($page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
        $lhzz=Lhzz::find()->where(['uid' => $user->id])->one()['id'];
        if (!$lhzz){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=GoodsOrder::Getallcompeletedorderdata($page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
        $lhzz=Lhzz::find()->where(['uid' => $user->id])->one()['id'];
        if (!$lhzz){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=GoodsOrder::Getallcanceledorderdata($page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
        $lhzz=Lhzz::find()->where(['uid' => $user->id])->one()['id'];
        if (!$lhzz){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=GoodsOrder::Getallcustomerserviceorderdata($page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $order_no=trim(htmlspecialchars($request->post('order_no','')),'');
        if(!$order_no){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        //获取订单信息
        $order_information=(new GoodsOrder())->Getorderinformation($order_no);
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
        $receive_details['invoicer_card'] = $invoice['invoicer_card'];
        switch ($invoice['invoice_header_type']){
            case 1:
                $receive_details['invoice_header_type']='个人';
                break;
            case 2:
                $receive_details['invoice_header_type']='公司';
                break;
        }
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
        $goods_data['goods_price_type']=$order_information['role'].'价';
        $goods_data['goods_price']=$order_information['goods_price'];
        $goods_data['freight']=$order_information['freight'];
        $goods_data['return_insurance']=$order_information['return_insurance'];
        $goods_data['supplier_price']=$order_information['supplier_price'];
        $goods_data['market_price']=$order_information['market_price'];
        $goods_data['shipping_way']=$order_information['waybillname'].'('.$order_information['waybillnumber'].')';
        if ($order_information['shipping_type']==1){
            $goods_data['shipping_way']='送货上门';
        }
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
     * 订单平台介入-操作
     * @return int|string
     */
    public function actionPlatformhandlesubmit(){
        $user = self::userIdentity();
        if (!is_numeric($user)) {
            return $user;
        }
        $lhzz=self::lhzzidentity($user);
        if (!is_numeric($lhzz)){
            return $lhzz;
        }
        $request=Yii::$app->request;
        $order_no=trim(htmlspecialchars($request->post('order_no','')),'');
        $sku=trim(htmlspecialchars($request->post('sku','')),'');
        $handle_type=trim(htmlspecialchars($request->post('handle_type','')),'');
        $reason=trim(htmlspecialchars($request->post('handle_type','')),'');
        if (!$order_no || !$handle_type || !$reason || !$sku){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $res=GoodsOrder::Platformadd($order_no,$handle_type,$reason,$sku);
        if ($res){
            return Json::encode([
                'code' => 200,
                'msg' => 'ok'
            ]);
        }
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
        $data=GoodsOrder::Businessgetallorderlist($supplier_id,$page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
        $data=GoodsOrder::Businessgetunpaidorder($supplier_id,$page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
            $data=GoodsOrder::Businessgetnotshippedorder($supplier_id,$page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
        $data=GoodsOrder::Businessgetnotreceivedorder($supplier_id,$page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
            $data=GoodsOrder::Businessgetcompletedorder($supplier_id,$page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
            $data=GoodsOrder::Businessgetcanceledorder($supplier_id,$page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
        $page_size=trim(htmlspecialchars($request->get('page_size','')),'');
        $time_type=trim(htmlspecialchars($request->post('time_type','')),'');
        if (!$time_type){
            $time_type='all';
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');
        $search=trim(htmlspecialchars($request->post('search','')),'');
        $sort_money=trim(htmlspecialchars($request->post('sort_money','')),'');
        $sort_time=trim(htmlspecialchars($request->post('sort_time','')),'');
        if ($time_type=='custom'){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$page_size){
            $page_size=15;
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
            $data=GoodsOrder::Businessgetcustomerserviceorder($supplier_id,$page_size,$page,$time_type,$time_start,$time_end,$search,$sort_money,$sort_time);
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
            $order_no=trim(htmlspecialchars($request->post('order_no','')),'');
            if(!$order_no){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            //获取订单信息
            $order_information=(new GoodsOrder())->Getorderinformation($order_no);
            if (!$order_information) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            //获取商品信息W
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
            $receive_details['invoicer_card'] = $invoice['invoicer_card'];
            switch ($invoice['invoice_header_type']){
                case 1:
                    $receive_details['invoice_header_type']='个人';
                    break;
                case 2:
                    $receive_details['invoice_header_type']='公司';
                    break;
            }
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
              $goods_data['shipping_way']=$order_information['waybillname'].'('.$order_information['waybillnumber'].')';
              if ($order_information['shipping_type']==1){
                  $goods_data['shipping_way']='送货上门';
              }
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
     * 去发货
     * @return string
     */
    public function actionSupplierdelivery(){
        $request = Yii::$app->request;
        $sku = trim(htmlspecialchars($request->post('sku', '')), '');
        $order_no = trim(htmlspecialchars($request->post('order_no', '')), '');
        $waybillname = trim(htmlspecialchars($request->post('waybillname', '')), '');
        $waybillnumber = trim(htmlspecialchars($request->post('waybillnumber', '')), '');
        $shipping_type = trim(htmlspecialchars($request->post('shipping_type', '')), '');

        if ($shipping_type!=1){
            if (!$sku || !$waybillname || !$waybillnumber || !$order_no) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
        $res=GoodsOrder::Supplierdelivery($sku,$order_no,$waybillname,$waybillnumber,$shipping_type);
        if ($res==true){
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
            ]);
        }else{
            $code = 1051;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
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
    public function actionTesteffecf(){
        $data=(new Query())->from('effect_earnst')->all();
        return Json::encode([
            'code' => 200,
            'data' =>  $data,
        ]);
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
        $shipping_type=GoodsOrder::findshipping_type($order_no,$sku);
        switch ($shipping_type){
            case 0:
                $data=Express::Findexresslist($order_no,$sku);
                break;
            case 1:
                $data=Express::Findexpresslist_sendtohome($order_no,$sku);
                break;
        }
        return Json::encode($data);


    }

    /**
     * @return string
     */
    public function actionGetplatformdetail(){
        $request=Yii::$app->request;
        $order_no=trim(htmlspecialchars($request->post('order_no','')),'');
        $sku=trim(htmlspecialchars($request->post('sku','')),'');
        if (!$sku || !$order_no){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $data=GoodsOrder::Getplatformdetail($order_no,$sku);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }
    //判断用户是否登陆
    private function userIdentity()
    {
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => 1052,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return $user->getId();
    }

    private function lhzzidentity($user)
    {
        $lhzz=Lhzz::find()->select('id')->where(['uid'=>$user])->one();
        if (!$lhzz){
            $code = 1010;
            return Json::encode([
                'code' => 1052,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return $lhzz['id'];
    }

    private function Setorder_no(){
        do {
            $code=date('md',time()).'1'.rand(10000,99999);
        } while ( $code==GoodsOrder::find()->select('order_no')->where(['order_no'=>$code])->asArray()->one()['order_no']);
        return $code;
    }


     /**
     * user apply refund
     * @return string
     */
    public  function  actionUserCancelOrder(){
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => 1052,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        $apply_reason=trim($request->post('apply_reason',''));
        if(!$order_no ||!$sku || !$apply_reason){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=GoodsOrder::applyRefund($order_no,$sku,$apply_reason,$user);
           if ($code ==200){
               return Json::encode([
                   'code' => $code,
                   'msg' => 'ok'
               ]);
           }else{
               return Json::encode([
                   'code' => $code,
                   'msg' => \Yii::$app->params['errorCodes'][$code]
               ]);
           }
    }

    /**
     * get refund list
     * by order_no and sku
     * @return string
     */
    public  function  actionGetOrderRefundList()
    {
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=yii::$app->request;
        $order_no=$request->post('order_no','');
        $sku=$request->post('sku','');
        if (!$order_no  || ! $sku)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $order_refund=OrderRefund::find()
            ->select('order_no,sku,handle,apply_reason,create_time,handle_time,refund_time,handle_reason')
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->asArray()
            ->all();
        $arr=OrderRefund::SetRefundparameter($order_refund);
        $code=200;
        return  Json::encode([
                'code'=>$code,
                'msg'=>'ok',
                'data'=>$arr
        ]);
    }

    /**
     * @return string
     */
    public function  actionRefundhandle(){
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=yii::$app->request;
        $order_no=$request->post('order_no','');
        $sku=$request->post('sku','');
        $handle_reason=$request->post('$handle_reason
','');
        $handle=$request->post('handle','');

        if (!$order_no  || ! $sku || !$handle)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
        $order=GoodsOrder::find()->select('id')->where(['order_no'=>$order_no,'supplier_id'=>$supplier->id])->one();
        if (!$supplier || !$order){
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $order_refund=OrderRefund::find()
            ->select('order_no,sku,handle,apply_reason,create_time,handle_time,refund_time,handle_reason')
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->andWhere(['handle'=>GoodsOrder::REFUND_HANDLE_STATUS_AGREE])
            ->asArray()
            ->one();
        if ($order_refund)
        {
            $code=1032;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=GoodsOrder::RefundHandle($order_no,$sku,$handle,$handle_reason,$user,$supplier);
        if ($code ==200){
            return Json::encode([
                'code' => $code,
                'msg' => 'ok',
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


}