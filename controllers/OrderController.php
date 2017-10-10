<?php

namespace app\controllers;
use app\models\Addressadd;
use app\models\CommentImage;
use app\models\CommentReply;
use app\models\EffectEarnst;
use app\models\GoodsComment;
use app\models\OrderAfterSale;
use app\models\OrderGoods;
use app\models\OrderRefund;
use app\models\UserRole;
use app\models\Wxpay;
use app\models\User;
use app\models\Alipay;
use app\models\GoodsOrder;
use app\models\Invoice;
use app\models\Express;
use app\models\Goods;
use app\models\Supplier;
use app\models\LogisticsDistrict;
use app\models\Lhzz;
use app\services\StringService;
use app\services\FileService;
use app\services\ExceptionHandleService;
use yii\db\Query;
use yii\db\Exception;
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
                $code=Addressadd::insertaddress($mobile,$consignee,$region,$districtcode);
                if ($code==200){
                    return Json::encode([
                        'code' => 200,
                        'msg' => 'ok'
                    ]);
                }else
                {
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
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
        $invoice_type        = trim($request->post('invoice_type'));
        $invoice_header_type = 1;
        $invoice_header      = trim($request->post('invoice_header'));
        $invoice_content     = trim($request->post('invoice_content'));
        if (!$invoice_type||!$invoice_header||!$invoice_content )
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $invoicer_card =trim($request->post('invoicer_card'));
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
            ]);
        }else{
            $code=$res['code'];
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
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
            $goods_id=trim($request->post('goods_id'));
            $goods_num=trim($request->post('goods_num'));
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
                'msg'  => Yii::$app->params['errorCodes'][$code]
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
        // $effect_id = trim($request->post('effect_id', ''), '');
        // $name = trim($request->post('name', ''), '');
        // $phone = trim($request->post('phone', ''), '');
        // $money=0.01;
        // if (!preg_match('/^[1][3,5,7,8]\d{9}$/', $phone)) {
        //     $code=1000;
        //     return Json::encode([
        //         'code' => $code,
        //         'msg'  => Yii::$app->params['errorCodes'][$code],
        //         'data' => null
        //     ]);
        // }
        // if ( !$name  ||!$phone || !$effect_id){
        //     $code=1000;
        //     return Json::encode([
        //         'code' => $code,
        //         'msg'  => Yii::$app->params['errorCodes'][$code]
        //     ]);
        // }\
        $money=0.01;
        $effect_id=1;
        $name='何友志';
        $phone='13880414513';
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
     * 大后台搜索界面
     * @return string
     */
    public function actionOrder_search(){
        $user = Yii::$app->user->identity;
        if (!$user || !$user->checklogin()){
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
        $request = Yii::$app->request;
        $page=trim($request->get('page',1));
        $size=trim($request->get('size',GoodsOrder::PAGE_SIZE_DEFAULT));
        $keyword = trim($request->get('keyword', ''));
        $timeType = trim(Yii::$app->request->get('time_type', ''));
        $where='';
        if($keyword){
            $where .=" and z.order_no like '%{$keyword}%' or  z.goods_nam like '%{$keyword}%'";
        }
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
            $startTime = explode(' ', $startTime)[0];
            $endTime = explode(' ', $endTime)[0];
        }

        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = (int)strtotime($endTime);
            $endTime && $where .= " and create_time <= {$endTime}";
        }
        $sort_money=trim($request->post('sort_money',''));
        $sort_time=trim($request->post('sort_time',''));
        $sort=GoodsOrder::sort_lhzz_order($sort_money,$sort_time);
        $paginationData = GoodsOrder::pagination($where, GoodsOrder::FIELDS_ORDERLIST_ADMIN, $page, $size,$sort);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);
    }


 /**
     * find order type
     * @return string
     */
    public function  actionFindOrderType(){
        $order_type_list=GoodsOrder::ORDER_TYPE_LIST;
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$order_type_list
        ]);
    }
   /**
     * find order list by admin user
     * @return string
     */
    public function actionFindOrderList(){
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
        $request = Yii::$app->request;
        $page=trim($request->get('page',1));
        $size=trim($request->get('size',GoodsOrder::PAGE_SIZE_DEFAULT));
        $keyword = trim($request->get('keyword', ''));
        $timeType = trim($request->get('time_type', ''));
        $type=trim($request->get('type','all'));
        $supplier_id=trim($request->get('supplier_id'));
        $where=GoodsOrder::GetTypeWhere($type);



            if ($timeType == 'custom') {
                $startTime = trim(Yii::$app->request->get('start_time', ''));
                $endTime = trim(Yii::$app->request->get('end_time', ''));
                if (($startTime && !StringService::checkDate($startTime))
                    || ($endTime && !StringService::checkDate($endTime))
                ) {
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }
            }else{
                list($startTime, $endTime) = StringService::startEndDate($timeType);
                $startTime = explode(' ', $startTime)[0];
                $endTime = explode(' ', $endTime)[0];
            }
        if($type=='all')
        {
            if($supplier_id)
            {
                if(!is_numeric($supplier_id))
                {
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $where .=" a.supplier_id={$supplier_id}";
            }
        }else{
            if($supplier_id)
            {
                if(!is_numeric($supplier_id))
                {
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $where .=" and a.supplier_id={$supplier_id}";
            }
        }
        if ($type=='all' && !$supplier_id)
        {
                if($keyword){
                    $where .="  z.order_no like '%{$keyword}%' or  z.goods_name like '%{$keyword}%'";
                }
        }else{
            if($keyword){
                $where .=" and z.order_no like '%{$keyword}%' or  z.goods_name like '%{$keyword}%'";
            }
        }
            if ($type=='all' && !$supplier_id )
            {
                if ($keyword)
                {
                    if ($startTime) {
                        $startTime = (int)strtotime($startTime);
                        $startTime && $where .= " and   a.create_time >= {$startTime}";
                    }
                    if ($endTime) {
                        $endTime = (int)strtotime($endTime);
                        $endTime && $where .= " and a.create_time <= {$endTime}";
                    }
                }else{
                    if ($startTime) {
                        $startTime = (int)strtotime($startTime);
                        $startTime && $where .= "a.create_time >= {$startTime}";
                    }
                    if ($endTime) {
                        $endTime = (int)strtotime($endTime);
                        $endTime && $where .= " and a.create_time <= {$endTime}";
                    }
                }
            }
            else
                {
                if ($startTime) {
                    $startTime = (int)strtotime($startTime);
                    $startTime && $where .= " and   a.create_time >= {$startTime}";
                }
                if ($endTime) {
                    $endTime = (int)strtotime($endTime);
                    $endTime && $where .= " and a.create_time <= {$endTime}";
                }
           }


        $sort_money=trim($request->get('sort_money'));
        $sort_time=trim($request->get('sort_time'));

        $paginationData = GoodsOrder::pagination($where, GoodsOrder::FIELDS_ORDERLIST_ADMIN, $page, $size,$sort_time,$sort_money);
        $code=200;
        return Json::encode([
             'code'=>$code,
            'msg'=>'ok',
            'data'=>$paginationData
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
        if ($order_information['status']=='待付款'){
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
        $request    =Yii::$app->request;
        $order_no   =trim($request->post('order_no',''));
        $sku        =trim($request->post('sku',''));
        $handle_type=trim($request->post('handle_type',''));
        $reason     =trim($request->post('handle_type',''));
        if (!$order_no || !$handle_type || !$reason || !$sku){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=GoodsOrder::Platformadd($order_no,$handle_type,$reason,$sku);
        if ($code==200){
            return Json::encode([
                'code' => 200,
                'msg' => 'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
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
     * supplier order list
     * @return string
     */
    public  function actionFindSupplierOrderList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()->where(['uid' => $user->id])->one()['id'];
        if (!$supplier){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $page=trim($request->get('page',1));
        $size=trim($request->get('size',GoodsOrder::PAGE_SIZE_DEFAULT));
        $keyword = trim($request->get('keyword', ''));
        $timeType = trim($request->get('time_type', ''));
        $type=trim($request->get('type','all'));
        $supplier_id=trim($request->get('supplier_id'));
        $where=GoodsOrder::GetTypeWhere($type);
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
            $startTime = explode(' ', $startTime)[0];
            $endTime = explode(' ', $endTime)[0];
        }
        if($type=='all')
        {
            if($supplier_id)
            {
                if(!is_numeric($supplier_id))
                {
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $where .=" a.supplier_id={$supplier_id}";
            }
        }else{
            if($supplier_id)
            {
                if(!is_numeric($supplier_id))
                {
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $where .=" and a.supplier_id={$supplier_id}";
            }
        }
        if ($type=='all' && !$supplier_id)
        {
            if($keyword){
                $where .="  z.order_no like '%{$keyword}%' or  z.goods_name like '%{$keyword}%'";
            }
        }else{
            if($keyword){
                $where .=" and z.order_no like '%{$keyword}%' or  z.goods_name like '%{$keyword}%'";
            }
        }
        if ($type=='all' && !$supplier_id )
        {
            if ($keyword)
            {
                if ($startTime) {
                    $startTime = (int)strtotime($startTime);
                    $startTime && $where .= " and   a.create_time >= {$startTime}";
                }
                if ($endTime) {
                    $endTime = (int)strtotime($endTime);
                    $endTime && $where .= " and a.create_time <= {$endTime}";
                }
            }else{
                if ($startTime) {
                    $startTime = (int)strtotime($startTime);
                    $startTime && $where .= "a.create_time >= {$startTime}";
                }
                if ($endTime) {
                    $endTime = (int)strtotime($endTime);
                    $endTime && $where .= " and a.create_time <= {$endTime}";
                }
            }
        }
        else
        {
            if ($startTime) {
                $startTime = (int)strtotime($startTime);
                $startTime && $where .= " and   a.create_time >= {$startTime}";
            }
            if ($endTime) {
                $endTime = (int)strtotime($endTime);
                $endTime && $where .= " and a.create_time <= {$endTime}";
            }
        }
        $where.=" and a.supplier_id={$suplier->id}";
        $sort_money=trim($request->get('sort_money'));
        $sort_time=trim($request->get('sort_time'));

        $paginationData = GoodsOrder::pagination($where, GoodsOrder::FIELDS_ORDERLIST_ADMIN, $page, $size,$sort_time,$sort_money);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);
    }


     /**
     * 商家后台获取订单详情
     * @return string
     */
    public function actionGetsupplierorderdetails(){
            $request=Yii::$app->request;
            $order_no=trim($request->post('order_no',''));
            $sku=trim($request->post('sku',''));
            if(!$order_no || !$sku){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            //获取订单信息
            $order_information=GoodsOrder::Getorderinformation($order_no,$sku);
            if (!$order_information) {
                $code = 1000;
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
            $ordergoodsinformation=GoodsOrder::Getordergoodsinformation($goods_name,$goods_id,$goods_attr_id,$order_no,$sku);
            if (!$ordergoodsinformation){
                $code = 1000;
                return Json::encode([
                   'code' => $code,
                   'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            //获取收货详情
            $receive_details['consignee']=$order_information['consignee'];
            $receive_details['consignee_mobile']=$order_information['consignee_mobile'];
            $receive_details['district']=LogisticsDistrict::getdistrict($order_information['district_code']);
            $receive_details['region']=$order_information['region'];
            $receive_details['invoice_header']=$order_information['invoice_header'];
            $receive_details['invoice_header_type']=$order_information['invoice_header_type'];
            $receive_details['invoice_content']=$order_information['invoice_content'];
            $receive_details['invoicer_card'] = $order_information['invoicer_card'];
            $receive_details['buyer_message'] = $order_information['buyer_message'];
            switch ($order_information['invoice_header_type']){
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
                 switch ($order_information['role_id'])
                {
                    case 7:
                        $goods_data['role']='平台采购价';
                        break;
                    case 6:
                        $goods_data['role']='供应商采购价格';
                        break;
                    case 5:
                        $goods_data['role']='装修公司采购价';
                        break;
                    case 4:
                        $goods_data['role']='项目经理采购价';
                        break;
                    case 3:
                        $goods_data['role']='设计师采购价';
                        break;
                    case 2:
                        $goods_data['role']='工人采购价';
                        break;
                }
              $goods_data['goods_price']=$order_information['goods_price'];
              $goods_data['goods_number']=$order_information['goods_number'];
              $goods_data['freight']=$order_information['freight'];
              $goods_data['return_insurance']=$order_information['return_insurance'];
              $goods_data['supplier_price']=$order_information['supplier_price'];
              $goods_data['market_price']=$order_information['market_price'];
              $goods_data['shipping_way']=$order_information['shipping_way'];
              $goods_data['shipping_type']=$order_information['shipping_type'];
              $goods_data['complete_time']=$order_information['complete_time'];
              if ($order_information['shipping_type']==1){
                  $goods_data['shipping_way']='送货上门';
              }
              $goods_data['pay_name']=$order_information['pay_name'];
              if ($order_information['status']=='待付款'){
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
                    'receive_details'=>$receive_details,
                    'is_unusual'=>$order_information['is_unusual']
                );
              $code = 200;
              return Json::encode([
                    'code' => $code,
                    'msg' => 'ok',
                    'data' =>$data
              ]);
    }


    /**
     * 去发货--商家后台
     * @return string
     */
    public function actionSupplierdelivery(){
        $request = Yii::$app->request;
        $sku = trim($request->post('sku', ''), '');
        $order_no = (string)trim($request->post('order_no', ''), '');
        $waybillnumber = trim($request->post('waybillnumber', ''), '');
        $shipping_type = trim($request->post('shipping_type', '0'), '');
        if ($shipping_type!=1){
            if (!$sku  || !$waybillnumber || !$order_no) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
        $res=GoodsOrder::Supplierdelivery($sku,$order_no,$waybillnumber,$shipping_type);
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
        $waybillname= trim($request->post('waybillname', ''));
        $waybillnumber= trim($request->post('waybillnumber', ''));
        $order_no= trim($request->post('order_no', ''));
        $sku=trim($request->post('sku', ''));
        $data=Express::find()->select('waybillnumber,waybillname')->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        if (!$data || !$waybillnumber || !$waybillname){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $code=Express::Expressupdate($waybillnumber,$waybillname,$sku,$order_no);
        if ($code==200){
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok',
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
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
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        if (!$order_no  || !$sku) {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $shipping_type=GoodsOrder::findshipping_type($order_no,$sku);
        $express=Express::find()
            ->select('waybillnumber,waybillname,create_time')
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->asArray()
            ->one();
        if (!$express)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        switch ($shipping_type){
            case 0:
                $list=Express::Findexresslist($order_no,$sku);
                if (is_numeric($list))
                {
                    $code=$list;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }
                break;
            case 1:
                $list=Express::Findexpresslist_sendtohome($order_no,$sku);
                break;
        }
        if ($shipping_type==1)
        {
            $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
            $supplier=Supplier::find()
                ->select('nickname')
                ->where(['id'=>$GoodsOrder->supplier_id])
                ->one();
            $waybillname=$supplier->nickname;
        }else{
            $waybillname= $express['waybillname'];
        }
        return Json::encode([
            'code' => 200,
            'msg' =>'ok',
            'data' => [
                'list'=>$list,
                'shipping_type'=>$shipping_type,
                'waybillname'=>$waybillname,
                'waybillnumber'=>$express->waybillnumber
            ],
        ]);
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
        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);

        if ($GoodsOrder->pay_status==0)
        {
            $OrderGoods=OrderGoods::find()
                ->where(['order_no'=>$order_no])
                ->all();
            foreach ($OrderGoods as &$goods)
            {
                if ($goods->order_status ==2)
                {
                    $code=403;
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }

                    $trans = \Yii::$app->db->beginTransaction();
                    try {
                        $goods->order_status=2;
                        $res=$goods->save(false);
                        if (!$res){
                            $code=500;
                            return Json::encode([
                                'code' => $code,
                                'msg' => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }
                        $trans->commit();
                    } catch (Exception $e) {
                        $trans->rollBack();
                        $code=500;
                        return Json::encode([
                            'code' => $code,
                            'msg' => \Yii::$app->params['errorCodes'][$code]
                        ]);
                    }

                $code=200;
                return Json::encode([
                    'code' => $code,
                    'msg' => 'ok'
                ]);
            }
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

   /**退款处理
     * @return string
     */
    public function  actionRefundHandle(){
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=yii::$app->request;
        $order_no=$request->post('order_no');
        $sku=$request->post('sku');
        $handle_reason=$request->post('handle_reason
','');
        $handle=$request->post('handle');
        if (!$order_no  || ! $sku || !$handle)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        // $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
        $order=GoodsOrder::find()->where(['order_no'=>$order_no])
            ->asArray()->one();
        $supplier=Supplier::find()->where(['id'=>$order['supplier_id']])->one();
        $u=User::find()->where(['id'=>$supplier->uid])->asArray()->one();
        var_dump($u);exit;
        $order=GoodsOrder::find()->select('id')->where(['order_no'=>$order_no,'supplier_id'=>$supplier->id])->one();
        if (!$supplier ){
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if(!$order){
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
        if (!$handle_reason)
        {
            $handle_reason='';
        }
        $code=GoodsOrder::RefundHandle($order_no,$sku,$handle,$handle_reason,$user,$supplier);
        if ($code ==200){
            return Json::encode([
                'code' => $code,
                'msg'  => 'ok',
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    
     /**获取退款详情
     * @return string
     */
    public function  actionFindRefundDetail()
    {
        $user=Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        if (!$order_no || ! $sku)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data= OrderRefund::FindRefundDetail($order_no,$sku);
        if (is_numeric($data))
        {
            $code=$data;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' =>$data
        ]);
    }

    /**
     * app端  用户获取订单列表
     * @return string
     */
    public function  actionFindOrder(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $type=$request->get('type','all');
        $page=$request->get('page','1');
        $size=$request->get('size',GoodsOrder::PAGE_SIZE_DEFAULT);
        $role=$request->get('role','user');
        switch ($role){
            case 'user':
                if ($type==GoodsOrder::ORDER_TYPE_ALL){
                    $where ="a.user_id={$user->id} and role_id={$user->last_role_id_app}";
                }else{
                    $where=GoodsOrder::GetTypeWhere($type);
                    $where .=" and a.user_id={$user->id}  and role_id={$user->last_role_id_app}  and order_refer = 2";
                }
                break;
            case 'supplier':
                $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
                if(!$supplier)
                {
                    $code=1010;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                if ($type==GoodsOrder::ORDER_TYPE_ALL){
                    $where ="a.supplier_id={$supplier->id}";
                }else{
                    $where=GoodsOrder::GetTypeWhere($type);
                    $where .=" and a.supplier_id={$supplier->id}  and order_refer = 2";
                }
                break;
        }
        $sort=' a.create_time  desc';
        $paginationData = GoodsOrder::paginationByUserorderlist($where, GoodsOrder::FIELDS_USERORDER_ADMIN, $page, $size,$type,$user,$role);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);
    }


    /**
     * app端  商家获取订单列表
     * @return string
     */
    public function  actionFindSupplierOrder(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $type=$request->get('type','all');
        $page=$request->get('page','1');
        $size=$request->get('size',GoodsOrder::PAGE_SIZE_DEFAULT);
        $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
        if(!$supplier)
        {
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if ($type==GoodsOrder::ORDER_TYPE_ALL){
            $where ="a.supplier_id={$supplier->id}";
        }else{
            $where=GoodsOrder::GetTypeWhere($type);
            $where .=" and a.supplier_id={$supplier->id}  and order_refer = 2";
        }
        $sort=' a.create_time  desc';
        $paginationData = GoodsOrder::paginationByUserorderlist($where, GoodsOrder::FIELDS_USERORDER_ADMIN, $page, $size,$sort,$user);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);
    }

    /**
     * @return string
     */
    public function actionGetordergoodslist(){
        $data= OrderGoods::find()->asArray()->all();
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>['balance'=>$data]
        ]);
    }

   /**
     * 余额支付
     * @return string
     */
    public  function  actionBalancePay(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData = Yii::$app->request->post();
        if(!array_key_exists('pay_password', $postData)){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (Yii::$app->getSecurity()->validatePassword($postData['pay_password'],$user->pay_password)==false){
            $code=1055;
            return Json::encode([
                'code'=>$code,
                'msg'=>Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=GoodsOrder::orderBalanceSub($postData,$user);
        if ($code==200){
            return Json::encode([
                'code'=>$code,
                'msg'=>'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     *获取订单详情
     * @return string
     */
    public function  actionUserOrderDetails(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData = Yii::$app->request->post();
        if(!array_key_exists('order_no', $postData)){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $arr=GoodsOrder::FindUserOrderDetails($postData,$user);
        $data=GoodsOrder::GetOrderDetailsData($arr,$user);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }

   /**
     * user add comment
     * @return string
     */
    public function actionCommentSub(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData=yii::$app->request->get();
        $uploadsData=FileService::uploadMore();
        if ($uploadsData !=1000){
            if (is_numeric($uploadsData)){
                $code=$uploadsData;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }

        $code=GoodsComment::addComment($postData,$user,$uploadsData);
        if($code==200)
        {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


   /**
     * get order comment
     * @return int|string
     */
    public function  actionGetComment(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData=yii::$app->request->post();
        if (!array_key_exists('order_no',$postData) || ! array_key_exists('sku',$postData)){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $order=OrderGoods::find()
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->one();
        if (!$order){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $comment=GoodsComment::find()
            ->where(['id'=>$order['comment_id']])
            ->asArray()
            ->one();
         if(!$comment)
        {
            $code=200;
            return Json::encode([
                'code'=>$code,
                'msg'=>'ok',
                'data'=>[]
            ]);
        }

         if (6 <$comment['score'] && $comment['score']<= 10 )
        {
            $comment['score']='好评';
        }else if (2< $comment['score'] && $comment['score']<= 6 )
        {
            $comment['score']='中评';
        }else{
            $comment['score']='差评';
        }
        $comment['create_time']=date('Y-m-d H:i',0);

         if ($comment){
            $comment['image']=CommentImage::find()
                ->select('image')
                ->where(['comment_id'=>$order['comment_id']])
                ->all();
            $reply=CommentReply::find()
                ->select('content')
                ->where(['comment_id'=>$order['comment_id']])
                ->asArray()
                ->one();
            if ($reply)
            {
                $comment['reply']=$reply['content'];
            }else{
                $comment['reply']='';
            }

        }
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$comment
        ]);
    }


    /**
     * @return string
     */
    public function  actionCommentReply()
    {
        $user = Yii::$app->user->identity;
        if (!$user->checklogin()){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData=yii::$app->request->post();
        $code=CommentReply::CommentReplyAction($postData);
        if ($code==200)
        {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


    /**
     * @return string
     */
    public  function  actionSupplierFindAfterSaleData()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData = Yii::$app->request->post();
        $data=OrderAfterSale::FindAfterSaleData($postData,$user);
        if (is_numeric($data)){
            $code=$data;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
  
    /**
     *用户申请售后
     * @return string
     */
    public  function  actionApplyAfterSale()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $postData = Yii::$app->request->get();
        $uploadsData=FileService::uploadMore();
        if ($uploadsData !=1000){
            if (is_numeric($uploadsData)){
                $code=$uploadsData;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $code=OrderAfterSale::UserApplyAfterSale($postData,$user,$uploadsData);
        if($code==200){
            return Json::encode([
                'code'=>$code,
                'msg'=>'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


    /**
     * 商家售后操作-- 同意  or  驳回 
     * @return string
     */
    public function  actionSupplierAfterSaleHandle(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData = Yii::$app->request->post();
        $code=Supplier::CheckOrderJurisdiction($user,$postData);
        if ($code !=200){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=OrderAfterSale::SupplierAfterSaleHandle($postData);
        if($code==200){
            return Json::encode([
                'code'=>$code,
                'msg'=>'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * @return array|string
     */
    public  function   actionUserAfterSaleDetail(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        if(!$order_no || !$sku){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $OrderAfterSale=OrderAfterSale::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$OrderAfterSale){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        switch ($OrderAfterSale->supplier_handle){
            case 0:
                $data=OrderAfterSale::findUnhandleAfterSale($OrderAfterSale);
                break;
            case 1:
                $data=OrderAfterSale::findHandleAfterSaleAgree($OrderAfterSale);
                break;
            case 2:
                $data=OrderAfterSale::findHandleAfterSaleDisagree($OrderAfterSale);
                break;
        }
        if (is_numeric($data)){
            $code=$data;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }

    /**售后详情 -- 商家派出人员
     * 上门服务
     * @return string
     */
    public function actionAfterSaleSupplierSendMan()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        if(!$order_no || !$sku){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $OrderAfterSale=OrderAfterSale::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$OrderAfterSale){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData['order_no']=$order_no;
        $code=Supplier::CheckOrderJurisdiction($user,$postData);
        if ($code !=200){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=OrderAfterSale::SupplierSendMan($OrderAfterSale);
        if ($code==200){
            return Json::encode([
                'code'=>$code,
                'msg'=>'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**售后详情 -- 商家确认
     * 上门服务
     * @return string
     */
    public function actionAfterSaleSupplierConfirm()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        if(!$order_no || !$sku){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $OrderAfterSale=OrderAfterSale::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$OrderAfterSale){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData['order_no']=$order_no;
        $code=Supplier::CheckOrderJurisdiction($user,$postData);
        if ($code !=200){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=OrderAfterSale::SupplierConfirm($OrderAfterSale);
        if ($code==200){
            return Json::encode([
                'code'=>$code,
                'msg'=>'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


    /**订单详情 -- 用户确认
     * 上门服务
     * @return string
     */
    public function actionAfterSaleUserConfirm()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        if(!$order_no || !$sku){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $OrderAfterSale=OrderAfterSale::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$OrderAfterSale){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData['order_no']=$order_no;
        $code=User::CheckOrderJurisdiction($user,$postData);
        if ($code !=200){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $code=OrderAfterSale::userConfirm($OrderAfterSale);
        if ($code==200){
            return Json::encode([
                'code'=>$code,
                'msg'=>'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }
     /**
     * @return string
     */
    public function actionAddTestOrderData(){
        $request=Yii::$app->request;
        $order_no=self::Setorder_no();
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
        $freight=1000;
        $address=Addressadd::find()
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
            $res1=$GoodsOrder->save();
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
            $OrderGoods->shipping_type=0;
            $OrderGoods->order_status=0;
            $OrderGoods->shipping_status=0;
            $OrderGoods->customer_service=0;
            $OrderGoods->is_unusual=0;
            $OrderGoods->freight=$freight;
            $OrderGoods->cover_image=$goods->cover_image;
            $res2= $OrderGoods->save();
            if (!$res1  || !$res2)
            {
                $tran->rollBack();
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $tran->commit();
            return Json::encode([
                'code' =>  200,
                'msg'  => 'ok'
            ]);
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

     /**
     * 用户确认收货
     * @return string
     */
    public  function  actionUserConfirmReceipt()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData=Yii::$app->request->post();
        $code=OrderGoods::UserConfirmReceipt($postData,$user);
        if ($code==200)
        {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     *商家后台获取异常状态
     * @return string
     */
    public  function  actionFindUnusualList()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        if (!$order_no || !$sku)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $unusualList=OrderRefund::findByOrderNoAndSku($order_no,$sku);
        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        if (!$GoodsOrder || !$unusualList)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
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
                        $reason='';
                        $complete_time=$unusualList[$k]['refund_time'];
                        $result='成功';
                        break;
                    case 2:
                        $type='驳回';
                        $reason=$unusualList[$k]['handle_reason'];
                        $complete_time=$unusualList[$k]['handle_time'];
                        $result='失败';
                        break;
                }
                $arr[]=[
                    'type'=>'商家反馈',
                    'value'=>$type,
                    'content'=>$reason,
                    'time'=>$unusualList[$k]['handle_time'],
                    'stage'=>$unusualList[$k]['order_type']
                ];
                $arr[]=[
                    'type'=>'退款结果',
                    'value'=>$result,
                    'content'=>'',
                    'time'=>$complete_time,
                    'stage'=>$unusualList[$k]['order_type']
                ];
                if ($unusualList[$k]['handle']==1){
                    $arr[]=[
                        'type'=>'退款去向',
                        'value'=>$refund_type,
                        'content'=>'',
                        'time'=>$complete_time,
                        'stage'=>$unusualList[$k]['order_type']
                    ];
                }
            }
            $data[$k]['order_type']=$unusualList[$k]['order_type'];
            $data[$k]['list']=$arr;
        }
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }
    /**
     * 大后台获取异常信息
     * @return string
     */
    public  function actionFindUnusualListLhzz()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));

        if (!$order_no || !$sku)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        if (!$GoodsOrder)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
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
                    'stage'=>$unshipped['order_type']
                ];
            }else {
                $arr1[] = [
                    'type' => '取消原因',
                    'value' => $unshipped['apply_reason'],
                    'content' => '',
                    'time' => $unshipped['create_time'],
                    'stage' => $unshipped['order_type']
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
                    'stage' => $unshipped['order_type']
                ];
                $arr1[] = [
                    'type' => '退款结果',
                    'value' => $result,
                    'content' => '',
                    'time' => $complete_time,
                    'stage' => $unshipped['order_type']
                ];
                if ($unshipped['handle'] == 1) {
                    $arr1[] = [
                        'type' => '退款去向',
                        'value' => $refund_type,
                        'content' => '',
                        'time' => $complete_time,
                        'stage' => $unshipped['order_type']
                    ];
                }
            }
            $data[]=$arr1;
        }else{
            $data[]=[];
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
                    'stage'=>$unreceived['order_type']
                ];
            }else {
                $arr2[] = [
                    'type' => '取消原因',
                    'value' => $unreceived['apply_reason'],
                    'content' => '',
                    'time' => $unreceived['create_time'],
                    'stage' => $unreceived['order_type']
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
                    'stage' => $unreceived['order_type']
                ];
                $arr2[] = [
                    'type' => '退款结果',
                    'value' => $result,
                    'content' => '',
                    'time' => $complete_time,
                    'stage' => $unreceived['order_type']
                ];
                if ($unreceived['handle'] == 1) {
                    $arr2[] = [
                        'type' => '退款去向',
                        'value' => $refund_type,
                        'content' => '',
                        'time' => $complete_time,
                        'stage' => $unreceived['order_type']
                    ];
                }
            }
            $data[]=$arr2;
        }else{
            $data[]=[];
        }

        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }



    /**
     * @return string
     */
    public  function  actionAddBalance()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $money=trim($request->post('money',''));
        $user=User::find()->where(['id'=>$user->id])->one();
        $user->balance+=$money*100;
        $user->availableamount+=$money*100;
        $res=$user->save(false);
        if ($res)
        {
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok',
            ]);
        }

    }

   /**
     * 测试专用
     * @return string
     */
    public  function  actionAddTestRole()
    {
        $mobile=Yii::$app->request->post('mobile','');
        $role_id=Yii::$app->request->post('role_id','');
        if (!$mobile || !$role_id)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user=User::find()->where(['mobile'=>$mobile])->one();
        if (!$user)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $time=time();
        $userRole=UserRole::find()->where(['user_id'=>$user->id,'role_id'=>7])->one();
        if (!$userRole)
        {
            $role=new UserRole();
            $role->role_id=$role_id;
            $role->user_id=$user->id;
            $role->review_status=2;
            $role->reviewer_uid=7;
            $role->review_apply_time=$time;
            $role->review_time=$time;
            $res1=$role->save(false);
            if (!$res1)
            {
                $code=1051;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' =>'ok'
            ]);
        }

    }


    public  function actionFindExpressData()
    {
        $data=Express::find()->asArray()->all();
        $code=200;
        return Json::encode([
            'data' => $data
        ]);
    }

      /**
         * @return string
         */
        public function  actionUpdateTestGoods()
        {
            $sku=Yii::$app->request->post('sku','');
            if (!$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $after_sale_services='0,1,2,3,4,5,6';
            $tran = Yii::$app->db->beginTransaction();
            try{
                $Goods=Goods::findBySku($sku);
                $Goods->after_sale_services=$after_sale_services;
                $res=$Goods->save(false);
                if (!$res)
                {
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg'  => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $tran->commit();
                return Json::encode([
                    'code' =>  200,
                    'msg'  => 'ok'
                ]);
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }

        }
     /**
     * @return string
     */
    public  function  actionAddSupplierBalance()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()
            ->where(['uid'=>$user->id])
            ->one();
        if (!$supplier)
        {
            $code=1034;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $money=trim($request->post('money',''));
        if (!$money)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier->balance+=$money*100;
        $supplier->availableamount+=$money*100;
        $res=$supplier->save(false);
        if ($res)
        {
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok',
            ]);
        }
    }

    /**
     * 去付款支付宝app支付
     * @return string
     */
    public  function actionAppOrderAliPay()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData = Yii::$app->request->post();
        if (!array_key_exists('list',$postData)
         || !array_key_exists('total_amount',$postData))
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $orders=explode($postData['list'],',');
        if (!is_array($orders))
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $orderAmount=GoodsOrder::CalculationCost($orders);
        if ($postData['total_amount']*100  != $orderAmount){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        };
        $data=Alipay::OrderAppPay($orderAmount,$orders);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }

   public  function  actionGetOrderNum()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id=Yii::$app->request->get('supplier_id');
        if (!$supplier_id)
        {
          $supplier_id=Supplier::find()->where(['uid'=>$user->id])->one()->id;

        }

        $all=(new Query())
            ->from(GoodsOrder::tableName().' as g')
            ->select('g.id')
            ->leftJoin(OrderGoods::tableName().' as o','g.order_no=o.order_no')
            ->where(" g.supplier_id={$supplier_id} ")
            ->count();
//        $role_id=$user->last_role_id_app;
        //Get 待付款订单  and g.role_id={$role_id}
        $unpaid=(new Query())
            ->from(GoodsOrder::tableName().' as g')
            ->select('g.id')
            ->leftJoin(OrderGoods::tableName().' as o','g.order_no=o.order_no')
            ->where("g.pay_status=0 and o.order_status=0  and g.supplier_id={$supplier_id} ")
            ->count();
        $unshipped=(new Query())
            ->from(GoodsOrder::tableName().' as g')
            ->select('g.id')
            ->leftJoin(OrderGoods::tableName().' as o','g.order_no=o.order_no')
            ->where("g.pay_status=1 and o.order_status=0 and shipping_status=0  and g.supplier_id={$supplier_id} ")
            ->count();
        $unreceiveed=(new Query())
            ->from(GoodsOrder::tableName().' as g')
            ->select('g.id')
            ->leftJoin(OrderGoods::tableName().' as o','g.order_no=o.order_no')
            ->where("g.pay_status=1 and o.order_status=0 and shipping_status=1  and g.supplier_id={$supplier_id} ")
            ->count();
        $completed=(new Query())
            ->from(GoodsOrder::tableName().' as g')
            ->select('g.id')
            ->leftJoin(OrderGoods::tableName().' as o','g.order_no=o.order_no')
            ->where("g.pay_status=1 and o.order_status=1 and shipping_status=2  and g.supplier_id={$supplier_id} ")
            ->count();
        $canceled=(new Query())
            ->from(GoodsOrder::tableName().' as g')
            ->select('g.id')
            ->leftJoin(OrderGoods::tableName().' as o','g.order_no=o.order_no')
            ->where("o.order_status=2  and g.supplier_id={$supplier_id}")
            ->count();
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' =>'ok',
            'data'=>[
                'all'=>$all,
                'unpaid'=>$unpaid,
                'unshipped'=>$unshipped,
                'unreceiveed'=>$unreceiveed,
                'completed'=>$completed,
                'canceled'=>$canceled
            ]
        ]);
    }


   /**
     * 用户申请售后详情
     * @return string
     */
    public function actionApplyAfterDetails()
    {
            $user = Yii::$app->user->identity;
            if (!$user){
                $code=1052;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $request=Yii::$app->request;
            $code=1000;
            $order_no=$request->post('order_no','');
            $sku=$request->post('sku','');
            if (!$order_no || !$sku)
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
            $Goods=Goods::findBySku($sku,'after_sale_services');
            if (!$OrderGoods || !$Goods)
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $arr=explode(',',$Goods->after_sale_services);
            foreach ($arr as $k =>$v)
            {
                if ($arr[$k]==0)
                {
                    unset($arr[$k]);
                }else{
                    $value=OrderAfterSale::GOODS_AFTER_SALE_SERVICES[$arr[$k]];
                    $name=array_search($value,OrderAfterSale::AFTER_SALE_SERVICES);
                    $data[]=[
                        'name'=>$name,
                        'value'=>$value
                    ];
                }
            }
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' =>'ok',
                'data'=>[
                    'goods'=>[
                        'goods_name'=>$OrderGoods->goods_name,
                        'goods_number'=>$OrderGoods->goods_number,
                        'cover_image'=>$OrderGoods->cover_image,
                        'goods_price'=>$OrderGoods->goods_price
                    ],
                    'after_sale'=>$data
                ]
            ]);
        }


}