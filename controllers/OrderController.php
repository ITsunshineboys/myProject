<?php

namespace app\controllers;
use app\models\GoodsAttr;
use app\models\OrderAfterSaleImage;
use app\models\OrderGoodsAttr;
use app\models\OrderGoodsBrand;
use app\models\OrderGoodsDescription;
use app\models\OrderGoodsImage;
use app\models\OrderLogisticsDistrict;
use app\models\OrderLogisticsTemplate;
use app\models\OrderSeries;
use app\models\OrderStyle;
use app\models\UserAddress;
use app\services\ModelService;
use Yii;
use app\models\OrderPlatForm;
use app\models\CommentImage;
use app\models\CommentReply;
use app\models\Effect;
use app\models\EffectEarnest;
use app\models\EffectMaterial;
use app\models\EffectPicture;
use app\models\GoodsComment;
use app\models\GoodsBrand;
use app\models\GoodsStat;
use app\models\Jpush;
use app\models\GoodsCategory;
use app\models\Role;
use app\models\ShippingCart;
use app\models\DeletedGoodsComment;
use app\models\LogisticsTemplate;
use app\models\UploadForm;
use app\models\OrderAfterSale;
use app\models\OrderGoods;
use app\models\OrderRefund;
use app\models\UserAccessdetail;
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
use app\models\UserNewsRecord;
use app\services\PayService;
use app\services\StringService;
use app\services\FileService;
use app\services\ExceptionHandleService;
use yii\db\Query;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use app\services\AuthService;
use yii\web\UploadedFile;

class OrderController extends Controller
{
    const WXPAY_LINE_GOODS='线下店商城';
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'getsupplierorderdetails',
        'expressupdate',
        'supplierdelivery',
        'getplatformdetail',
        'getorderdetailsall',
        'platformhandlesubmit',
        'find-order-list',
        'find-supplier-order-list',
        'find-unusual-list',
        'find-unusual-list-lhzz',
        'get-comment',
        'comment-reply',
        'supplier-after-sale-handle',
        'refund-handle',
        'supplier-delete-comment',
        'delete-comment-list',
        'delete-comment-details',
        'goods-view',
        'find-refund-detail',
        'after-sale-supplier-send-man',
        'after-sale-supplier-confirm',
        'after-sale-delivery',
        'find-shipping-cart-list',
        'after-sale-detail-admin',
//        'get-order-num',
        'close-order'
    ];
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AuthService::className(),
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
                    'after-sale-delivery' =>['post',],
                    'after-sale-supplier-send-man' =>['post',],
                    'after-sale-supplier-confirm' =>['post',],
                    'supplier-after-sale-handle' =>['post',],
                    'platformhandlesubmit'=>['post',],
                    'supplierdelivery'=>['post',],
                    'expressupdate'=>['post',],
                    'refund-handle'=>['post',],
                    'comment-reply'=>['post',],
                    'supplier-delete-comment'=>['post',],
                    'close-order'=>['post',],
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
        $code=trim($request->get('code',''));
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
     * 无登录app-添加收货地址（旧）
     * @return string
     */
    public function actionAdduseraddress()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $consignee = trim($request->post('consignee',''),'');
            $mobile= trim($request->post('mobile',''),'');
            $districtCode=trim($request->post('districtcode',''),'');
            $region=trim($request->post('region',''));
            if (!$districtCode || !$region  || !$mobile || !$consignee ) {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }else{
                $data=UserAddress::InsertAddress($mobile,$consignee,$region,$districtCode);
                if (!$data){
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }else
                {
                    return Json::encode([
                        'code' => 200,
                        'msg' => 'ok',
                        'data'=>[
                            'address_id'=>$data
                        ]
                    ]);
                }
            }
        }else
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => 0
            ]);
        }
    }

    /**
     * 无登录app-确认订单页面-获取收货地址(旧)
     * @return string
     */
    public function actionGetaddress(){
        $request = Yii::$app->request;
        $address_id=$request ->get('address_id');
        $user_address=UserAddress::GetAddress($address_id);
        if ($user_address){
            return Json::encode([
                'code' => 200,
                'msg'  => 'ok',
                'data' => $user_address
            ]);
        }else{
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * 无登录app-添加收货地址(新)
     * @return string
     */
    public function actionAddLineReceiveAddress()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $consignee = trim($request->post('consignee',''),'');
            $mobile= trim($request->post('mobile',''),'');
            $districtCode=trim($request->post('district_code',''),'');
            $region=trim($request->post('region',''));
            if (!$districtCode || !$region  || !$mobile || !$consignee ) {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }else{
                $data=UserAddress::InsertAddress($mobile,$consignee,$region,$districtCode);
                if (!$data){
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }else
                {
                    return Json::encode([
                        'code' => 200,
                        'msg' => 'ok',
                        'data'=>[
                            'address_id'=>$data
                        ]
                    ]);
                }
            }
        }else
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => 0
            ]);
        }
    }

    /**
     * 无登录app-确认订单页面-获取收货地址(新)
     * @return string
     */
    public  function  actionGetLineReceiveAddress()
    {
        $request = Yii::$app->request;
        $address_id=$request ->get('address_id');
        $user_address=UserAddress::GetAddress($address_id);
        if (!$user_address){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'data' => $user_address
        ]);
    }

    /**
     * 无登录app-添加发票信息(新)
     * @return string
     */
    public function actionAddLineOrderInvoice(){
        $request = \Yii::$app->request;
        $invoice_type        = trim($request->post('invoice_type'));
        $invoice_header_type = trim($request->post('invoice_header_type'));
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
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $res=Invoice::AddInvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content,$invoicer_card);
        if ($res)
        {
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg'  =>'ok',
                'data' =>[
                    'invoice_id'=>$res
                ]
            ]);
        }else{
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }
    /**
     * 无登录app-获取发票信息(新)
     * @return string
     */
    public function  actionGetLineOrderInvoiceData()
    {
        $request = \Yii::$app->request;
        $invoice_id= trim($request->get('invoice_id'));
        if (!$invoice_id)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $model = new Invoice();
        $data=$model->GetLineInvoice($invoice_id);
        if (!$data){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'data' => $data
        ]);
    }
    /**
     * 无登录app-添加发票信息
     * @return string
     */
    public function actionOrderinvoicelineadd(){
        $request = \Yii::$app->request;
        $invoice_type        = trim($request->post('invoice_type'));
        $invoice_header_type = trim($request->post('invoice_header_type'));
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
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $res=Invoice::AddInvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content,$invoicer_card);
        if ($res)
        {
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg'  =>'ok',
                'data' =>[
                    'invoice_id'=>$res
                ]
            ]);
        }else{
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * 无登录app-获取发票信息
     * @return string
     */
    public function  actionGetinvoicelinedata()
    {
        $request = \Yii::$app->request;
        $invoice_id= trim($request->get('invoice_id'));
        if (!$invoice_id)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $model = new Invoice();
        $data=$model->GetLineInvoice($invoice_id);
        if ($data){
            return Json::encode([
                'code' => 200,
                'msg'  => 'ok',
                'data' => $data
            ]);
        }else{
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * 线下店app-获取商品信息(新)
     * @return string
     */
    public function actionGetLineGoodsInfo(){
        $request = Yii::$app->request;
        $goods_id=$request->get('goods_id');
        $goods_num=$request->get('goods_num');
        if (!$goods_id || !$goods_num)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=GoodsOrder::GetLineGoodsData($goods_id,$goods_num);
        if (is_numeric($data))
        {
            $code=$data;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }else{
            return Json::encode([
                'code' => 200,
                'msg'  =>'ok',
                'data'=>$data
            ]);
        }
    }
    /**
     * 线下店app-获取商品信息（旧）
     * @return string
     */
    public function actionGetgoodsdata(){
        $request = Yii::$app->request;
        $goods_id=trim($request->post('goods_id'));
        $goods_num=trim($request->post('goods_num'));
        if (!$goods_id || !$goods_num){
            $goods_id=$request->get('goods_id');
            $goods_num=$request->get('goods_num');
            if (!$goods_id || !$goods_num)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $data=GoodsOrder::GetLineGoodsData($goods_id,$goods_num);
       if (is_numeric($data))
       {
           $code=$data;
           return Json::encode([
               'code' => $code,
               'msg'  => Yii::$app->params['errorCodes'][$code]
           ]);
       }else{
           return Json::encode([
               'code' => 200,
               'msg'  =>'ok',
               'data'=>$data
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
            echo Json::encode([
                'code' => 201,
                'msg' =>'非微信打开',
            ]);
        } else {
            // 微信浏览器，允许访问
            echo Json::encode([
                'code' => 200,
                'msg' =>'微信内打开',
                'data'=>Wxpay::GetWxJsSign()
            ]);
//            Yii::$app->runAction('order/test-open-id');
        }

    }
    /**
     * 智能报价-样板间支付定金提交
     * @return string
     */
    public function actionEffectEarnstAlipaySub(){
        $request = \Yii::$app->request;
        $post=$request->post();
        $code=1000;
        $phone  = trim($request->post('phone', ''), '');
        if (!preg_match('/^[1][3,5,7,8]\d{9}$/', $phone)) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $out_trade_no =GoodsOrder::SetOrderNo();
        $res=Alipay::EffectEarnestSubmit($post,$phone,$out_trade_no);
        if (!$res)
        {
            $code=1000;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=200;
        return json_encode([
            'code' => $code,
            'msg' => 'ok'
        ]);
    }
    /**
     * 样板间支付订单异步返回
     */
    public function actionAliPayEffectEarnestNotify()
    {
        $post=Yii::$app->request->post();
        $model=new Alipay();
        $alipaySevice=$model->Alipaylinenotify();
        $result = $alipaySevice->check($post);
        if ($result){
            if ($post['trade_status'] == 'TRADE_SUCCESS') {
                $id=urldecode($post['passback_params']);
                // if ($post['total_amount'] !=89){
                //     exit;
                // }
                $effect=Effect::findOne($id);
                if (!$effect)
                {
                    echo 'sucess';
                    exit;
                }
                $tran = Yii::$app->db->beginTransaction();
                try{
                    $earnst=EffectEarnest::find()
                        ->where(['effect_id'=>$id])
                        ->one();
                    $earnst->status=1;
                    if (!$earnst->save(false))
                    {
                        echo 'fail';
                        exit;
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
                                return false;
                            };
                            $effect=Effect::find()->where(['id'=>$effect_id])->one();
                            if ($effect)
                            {
                                $res1=$effect->delete();
                                if (!$res1)
                                {
                                    $tran->rollBack();
                                    echo 'fail';
                                    exit;
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
                                    echo 'fail';
                                    exit;
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
                                    echo 'fail';
                                    exit;
                                };
                            }
                        }
                    }
                }catch (Exception $e){
                    $tran->rollBack();
                    echo 'fail';
                    exit;
                }
                $tran->commit();
                echo 'sucess';
            }
        }else{
            //验证失败
            echo "fail";    //请不要修改或删除
        }
    }


    /**
     * 线下店商城支付宝支付提交订单
     */
    public function actionOrderLineAliPay(){
        $request=Yii::$app->request;
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no =GoodsOrder::SetOrderNo();
        //付款金额，必填
        $total_amount =$request->post('order_price');
        $goods_id=$request->post('goods_id');
        $goods_num=$request->post('goods_num');
        $address_id=$request->post('address_id');
        $pay_name=PayService::ALI_PAY;
        $invoice_id=$request->post('invoice_id');
        $freight=$request->post('freight');
        $buyer_message=$request->post('buyer_message','');
        //商品描述，可空
        $body = $request->post('body','无');
        $code=1000;
        if (
            !$total_amount
            ||!$goods_id
            ||!$goods_num
            ||!$address_id
        ){
            return Json::encode([
                'code' =>  $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $Goods=Goods::findOne($goods_id);
        if (!$Goods)
        {
            return Json::encode([
                'code' =>  $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        //若发票未填-添加发票操作
        $invoice=Invoice::findOne($invoice_id);
        if (!$invoice)
        {
            $address=UserAddress::findOne($address_id);
            $in=new Invoice();
            $in->invoice_type=1;
            $in->invoice_header_type=1;
            $in->invoice_header=$address->consignee;
            $in->invoice_content='明细';
            $res=$in->save(false);
            if (!$res)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $invoice_id=$in->id;
        }
        if (!$freight)
        {
            $freight=0;
        }
        $return_insurance=0;
        //判断金额是否正确
        $money=$Goods->platform_price*$goods_num+$return_insurance*100+($freight*100);
        if ($money*0.01 != $total_amount)
        {
            $code=1000;
            return Json::encode([
                'code' =>  $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $res=Alipay::AliPayLineSubmit($out_trade_no,$Goods->title,$total_amount,$body,$goods_id, $goods_num,$address_id,$pay_name,$invoice_id,$Goods->supplier_id,$freight,$return_insurance,$buyer_message);
        if ($res)
        {
            $code=200;
            return Json::encode([
                'code' =>  $code,
                'msg'  =>'ok'
            ]);
        }
    }

    /**
     * 线下店商城支付宝支付提交订单
     */
    public function actionAlipaylinesubmit(){
        $request=Yii::$app->request;
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no =GoodsOrder::SetOrderNo();
        $subject=trim($request->post('goods_name'),' ');
        //付款金额，必填
        $total_amount =trim($request->post('order_price'),' ');
        $goods_id=trim($request->post('goods_id'),' ');
        $goods_num=trim($request->post('goods_num'),' ');
        $address_id=trim($request->post('address_id'),' ');
        $pay_name='线上支付-支付宝支付';
        $invoice_id=trim($request->post('invoice_id'),' ');
        $supplier_id=trim($request->post('supplier_id'),' ');
        $freight=trim($request->post('freight'));
        $return_insurance=trim($request->post('return_insurance'),' ');
        $buyer_message=trim($request->post('buyer_message',''));
        //商品描述，可空
        $body = trim($request->post('body'),' ');
        if (!$subject||!$total_amount||!$goods_id ||!$goods_num||!$address_id||! $invoice_id||!$supplier_id ){
            $c=1000;
            return Json::encode([
                'code' =>  $c,
                'msg'  => Yii::$app->params['errorCodes'][$c]
            ]);
        }

        //若发票未填-添加发票操作
        $invoice=Invoice::findOne($invoice_id);
        if (!$invoice)
        {
            $address=UserAddress::findOne($address_id);
            $in=new Invoice();
            $in->invoice_type=1;
            $in->invoice_header_type=1;
            $in->invoice_header=$address->consignee;
            $in->invoice_content='明细';
            $res=$in->save(false);
            if (!$res)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $invoice_id=$in->id;
        }
        if (!$freight)
        {
            $freight=0;
        }
        $return_insurance=0;
        //判断金额是否正确
        $code=GoodsOrder::judge_order_money($goods_id,$total_amount,$goods_num,$return_insurance,$freight);
        if ($code==1000)
        {
            return Json::encode([
                'code' =>  $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $res=Alipay::AliPayLineSubmit($out_trade_no,$subject,$total_amount,$body,$goods_id, $goods_num,$address_id,$pay_name,$invoice_id,$supplier_id,$freight,$return_insurance,$buyer_message);
        if ($res)
        {
            $c=200;
            return Json::encode([
                'code' =>  $c,
                'msg'  =>'ok'
            ]);
        }
    }
    /**
     * 支付宝线下店商城异步返回操作-购买回调
     */
    public function actionAliPayLineNotify(){
        $post=Yii::$app->request->post();
        $model=new Alipay();
        $alipaySevice=$model->Alipaylinenotify();
        $result = $alipaySevice->check($post);
        if ($result){
            if ($post['trade_status'] == 'TRADE_SUCCESS'){
                $arr=explode('&',$post['passback_params']);
                $order_no=$post['out_trade_no'];
                $order=GoodsOrder::find()
                    ->select('order_no')
                    ->where(['order_no'=>$order_no])
                    ->asArray()
                    ->one();
                if ($order){
                    echo "success";
                    exit;
                }
                $res=GoodsOrder::AliPayLineNotifyDataBase($arr,$post);
                if ($res==true){
                    echo "success";     //请不要修改或删除
                }else{
                    echo "fail";
                }
            }
        }else{
            //验证失败
            echo "fail";  //请不要修改或删除
        }
    }

     /**
     * wxpay effect sub
     * 微信样板间支付
     * @return string
     */
    public function actionWxpayEffectEarnstSub(){
        $request = \Yii::$app->request;
        $post=$request->post();
        $code=1000;
        $phone  = $request->post('phone', '');
        if (!preg_match('/^[1][3,5,7,8]\d{9}$/', $phone)) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
//        $out_trade_no =GoodsOrder::SetOrderNo();
        $id=Effect::addneweffect($post);
        if (!$id)
        {
            $code=1000;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $id=1;
        $openId=$request->post('wxpayCode', '');
        if (!$openId)
        {
            $code=1000;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $res=Wxpay::EffectEarnestSubmit($id,$openId);
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
        $subject=trim($request->get('goods_name'));
        //付款金额，必填
        $total_amount =trim($request->get('order_price'));
        $goods_id=trim($request->get('goods_id'));
        $goods_num=trim($request->get('goods_num'));
        $address_id=trim($request->get('address_id'));
        $pay_name='线上支付-微信支付';
        $invoice_id=trim($request->get('invoice_id'));
        $supplier_id=trim($request->get('supplier_id'));
        $freight=trim($request->get('freight'));
        $return_insurance=trim($request->get('return_insurance'));
        $buyer_message=trim($request->get('buyer_message',''));
        if (!$total_amount || !$goods_id || !$goods_num || !$address_id || !$pay_name ||! $invoice_id || !$supplier_id )
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$freight)
        {
            $freight=0;
        }
        $order_no =GoodsOrder::SetOrderNo();
        //商品描述，可空
        $body =$subject;
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
            'buyer_message'=>$buyer_message,
            'total_amount'=>$total_amount
        );
        $url=(new PayService())->GetOrderOpenid($orders);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$url
        ]);
    }
     /**
     * 获取openID2-微信
     * @return string
     */
    public function  actionWxLinePay()
    {
        $orders=array(
            'address_id'=> Yii::$app->session['address_id'],
            'invoice_id'=> Yii::$app->session['invoice_id'],
            'goods_id'=> Yii::$app->session['goods_id'],
            'goods_num'=> Yii::$app->session['goods_num'],
            'order_price'=> Yii::$app->session['order_price'],
            'goods_name'=> Yii::$app->session['goods_name'],
            'pay_name'=> Yii::$app->session['pay_name'],
            'supplier_id'=> Yii::$app->session['supplier_id'],
            'freight'=> Yii::$app->session['freight'],
            'return_insurance'=> Yii::$app->session['return_insurance'],
            'body'=> Yii::$app->session['body'],
            'order_no'=> Yii::$app->session['order_no'],
            'buyer_message'=> Yii::$app->session['buyer_message'],
            'total_amount'=> Yii::$app->session['total_amount']
        );
            if (! Yii::$app->session['address_id']
                || !Yii::$app->session['goods_id']
                || !Yii::$app->session['goods_num']
                || !Yii::$app->session['order_price']
                || !Yii::$app->session['pay_name']
                || !Yii::$app->session['supplier_id']
                || !Yii::$app->session['freight']
                || !Yii::$app->session['order_no']
                || !Yii::$app->session['total_amount']
            )
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $address=UserAddress::findOne(Yii::$app->session['address_id']);
            {
                if (!$address)
                {
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg'  => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }
        $invoice=Invoice::findOne(Yii::$app->session['invoice_id']);
        if (!$invoice)
        {
            $address=UserAddress::findOne(Yii::$app->session['address_id']);
            $in=new Invoice();
            $in->invoice_type=1;
            $in->invoice_header_type=1;
            $in->invoice_header=$address->consignee;
            $in->invoice_content='明细';
            $res=$in->save(false);
            if (!$res)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $orders['invoice_id']=$in->id;
        }
        $openid=(new PayService())->GetOpenid();
        $model=new Wxpay();
        $data=$model->WxLineApiPay($orders,$openid);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    /**
     * 微信公众号样板间申请定金异步返回
     * wxpay notify action
     * wxpay nityfy apply Deposit database
     * @return bool
     */
    public function actionWxPayEffectEarnestNotify(){
        //获取通知的数据
        $xml = file_get_contents("php://input");;
        $data=json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $arr=Json::decode($data);
        if ($arr['result_code']=='SUCCESS')
        {
            $transaction_id=$arr['transaction_id'];
            //查询订单
            $result = Wxpay::Queryorder($transaction_id);
            if (!$result)
            {
                return false;
            }
           // if ($arr['total_fee']!=8900)
           // {
           //     return false;
           // }
            $id=$arr['attach'];
            $tran = Yii::$app->db->beginTransaction();
            try{
                $earnst=EffectEarnest::find()
                    ->where(['effect_id'=>$id])
                    ->one();
                $earnst->status=1;
                if (!$earnst->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
                $time=(time()-60*60*6);
                $list=EffectEarnest::find()
                    ->where("  create_time < {$time} ")
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
                            return false;
                        };
                        $effect=Effect::find()->where(['id'=>$effect_id])->one();
                        if ($effect)
                        {
                            $res1=$effect->delete();
                            if (!$res1)
                            {
                                $tran->rollBack();
                                return false;
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
                                return false;
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
                                return false;
                            }
                        }
                    }
                }
            }catch (Exception $e){
                $tran->rollBack();
                return false;
            }
            $tran->commit();
            return true;
        }else{
            return false;
        }
    }
    /**
     *微信线下支付异步操作
     */
    public function actionOrderLineWxPayNotify(){
        //获取通知的数据
        $xml = file_get_contents("php://input");
        $data=json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $msg=Json::decode($data);
        if ($msg['result_code']=='SUCCESS')
        {
            $transaction_id=$msg['transaction_id'];
            $result = Wxpay::Queryorder($transaction_id);
            if (!$result)
            {
                return false;
            }
            $arr=explode('&',$msg['attach']);
            $order=GoodsOrder::find()
                ->select('order_no')
                ->where(['order_no'=>$arr[8]])
                ->asArray()
                ->one();
            if ($order){
                return true;
            }
            $result=GoodsOrder::WxPayLineNotifyDataBase($arr,$msg);
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
     * 获取订单状态
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
     * 大后台订单列表
     * find order list by admin user
     * @return string
     */
    public function actionFindOrderList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=403;
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
            ){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($startTime==$endTime){
                list($startTime, $endTime) =ModelService::timeDeal($startTime);
            }else{
                $endTime && $endTime .= ' 23:59:59';
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
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
                $where .="  CONCAT(z.order_no,z.goods_name,a.consignee_mobile) like '%{$keyword}%'";
//                        a.consignee_mobile,u.mobile
            }
        }else{
            if($keyword){
                $where .=" and  CONCAT(z.order_no,z.goods_name,a.consignee_mobile) like '%{$keyword}%'";
            }
        }

//            if ($timeType=='today')
//            {
//                $startTime=date('Y-m-d',time());
//                $endTime=date('Y-m-d',time()+24*60*60);
//            }
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
        }else{
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
        $paginationData = GoodsOrder::pagination($where, GoodsOrder::FIELDS_ORDERLIST_ADMIN, $page, $size,$sort_time,$sort_money,'lhzz');
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
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));

        if(!$order_no|| !$sku){
            $order_no=trim($request->get('order_no',''));
            $sku=trim($request->get('sku',''));
            if (!$order_no || !$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        //获取订单信息
        $order_information=GoodsOrder::GetOrderInformation($order_no,$sku);
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
        $order_no=$order_information['order_no'];
        $sku=explode('+',$order_information['sku']);
        $ordergoodsinformation=GoodsOrder::GetOrderGoodsInformation($goods_name,$goods_id,$order_no,$sku);
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
        $address=UserAddress::find()->where(['id'=>$address_id])->asArray()->one();
        if (!$address){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => '收货地址不存在'
            ]);
        }
        $address['district']=LogisticsDistrict::getdistrict($address['district']);
        $invoice=Invoice::find()->where(['id'=>$invoice_id])->asArray()->one();
        if (!$invoice){
            $code = 1000;
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
        $order_information['invoice_type']=$invoice['invoice_type'];
        $receive_details['invoice_header_type']=$invoice['invoice_header_type'];
        $receive_details['invoice_content']=$invoice['invoice_content'];
        $receive_details['invoicer_card'] = $invoice['invoicer_card'];
        $receive_details['buyer_message']=$order_information['buyer_message'];
        switch ($invoice['invoice_header_type']){
            case 1:
                $receive_details['invoice_header_type']='个人';
                break;
            case 2:
                $receive_details['invoice_header_type']='公司';
                break;
        }
        switch ($receive_details['invoice_type']){
            case 1:
                $receive_details['invoice_type']='普通发票';
                break;
            case 2:
                $receive_details['invoice_type']='电子发票';
                break;
            case 3:
                $receive_details['invoice_type']='普通增值税发票';
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
        if ($order_information['status']==GoodsOrder::ORDER_TYPE_DESC_UNPAID){
            $goods_data['pay_term']=$order_information['pay_term'];
        }else{
            $goods_data['pay_term']=0;
        }
        if (!$order_information['paytime']==0){
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
        $lhzz=self::LhzzIdentity($user);
        if (!is_numeric($lhzz)){
            return $lhzz;
        }
        $request    = Yii::$app->request;
        $order_no   = trim($request->post('order_no',''));
        $sku        = trim($request->post('sku',''));
        $handle_type= trim($request->post('handle_type',''));
        $reason     = trim($request->post('reason',''));
        if (!$order_no || !$handle_type  || !$sku){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$reason)
        {
            $reason='';
        }
        $code=GoodsOrder::PlatformAdd($order_no,$handle_type,$reason,$sku);
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
     * 判断收货地址是否在指定区域内(新)
     * @return string
     */
    public function actionJudgeAddress(){
        $request=Yii::$app->request;
        $district_code=trim($request->get('district_code',''));
        $goods_id=trim($request->get('goods_id',''));
        if (!$district_code || !$goods_id) {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $template=Goods::find()
            ->select('logistics_template_id')
            ->where(['id'=>$goods_id])
            ->asArray()
            ->one();
        if (!$template)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=LogisticsDistrict::isApply($district_code,$template['logistics_template_id']);
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
     * 判断收货地址是否在指定区域内(旧)
     * @return string
     */
    public function actionJudegaddress(){
        $request=Yii::$app->request;
        $districtcode=trim($request->post('districtcode',''));
        $goods_id=trim($request->post('goods_id',''));
        if (!$districtcode || !$goods_id){
            $districtcode=trim($request->get('districtcode',''));
            $goods_id=trim($request->get('goods_id',''));
            if (!$districtcode || !$goods_id) {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        $template_id=Goods::find()
            ->select('logistics_template_id')
            ->where(['id'=>$goods_id])
            ->asArray()
            ->one()
        ['logistics_template_id'];
        $data=LogisticsDistrict::isApply($districtcode,$template_id);
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
        $supplier=Supplier::find()
            ->where(['uid' => $user->id])
            ->one();
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

        if($keyword){
            if ($type=='all')
            {
                $where ="  CONCAT(z.order_no,z.goods_name) like '%{$keyword}%'";
            }else{
                $where ="  CONCAT(z.order_no,z.goods_name) like '%{$keyword}%' and  ".GoodsOrder::GetTypeWhere($type);
            }
            $where.=" and a.supplier_id={$supplier->id}";
        }else{
            if ($type=='all')
            {
                $where=" a.supplier_id={$supplier->id}";
            }else{
                $where=GoodsOrder::GetTypeWhere($type);
                $where.=" and a.supplier_id={$supplier->id}";
            }
        }
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($startTime==$endTime){
                list($startTime, $endTime) =ModelService::timeDeal($startTime);
            }else{
                $endTime && $endTime .= ' 23:59:59';
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
        }

//        if ($timeType=='today')
//        {
//            $startTime=date('Y-m-d',time());
//            $endTime=date('Y-m-d',time()+24*60*60);
//        }
                $where .=" and supplier_id={$supplier->id}";
                if ($startTime) {
                    $startTime = (int)strtotime($startTime);
                    $startTime && $where .= " and   a.create_time >= {$startTime}";
                }
                if ($endTime) {
                    $endTime = (int)strtotime($endTime);
                    $endTime && $where .= " and a.create_time <= {$endTime}";
                }
        $sort_money=trim($request->get('sort_money'));
        $sort_time=trim($request->get('sort_time'));
        $paginationData = GoodsOrder::pagination($where, GoodsOrder::FIELDS_ORDERLIST_ADMIN, $page, $size,$sort_time,$sort_money,'supplier');
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
            $order_no=trim($request->get('order_no',''));
            $sku=trim($request->get('sku',''));
            if (!$order_no || !$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        //获取订单信息
        $order_information=GoodsOrder::GetOrderInformation($order_no,$sku);
        if (!$order_information)
        {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
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
            return Json::encode([
               'code' => $code,
               'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
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
                $receive_details['invoice_header_type']='个人';
                break;
            case 2:
                $receive_details['invoice_header_type']='公司';
                break;
        }
        switch ($receive_details['invoice_type']){
            case 1:
                $receive_details['invoice_type']='普通发票';
                break;
            case 2:
                $receive_details['invoice_type']='电子发票';
                break;
            case 3:
                $receive_details['invoice_type']='普通增值税发票';
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
        $goods_data['sku']=$order_information['sku'];
        $goods_data['username']=$order_information['username'];
        $goods_data['amount_order']=$order_information['amount_order'];
        switch ($order_information['role_id'])
        {
            case 7:
                $goods_data['role']='平台价';
                break;
            case 6:
                $goods_data['role']='供应商采购价';
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
        $goods_data['shipping_type']=$order_information['shipping_type'];
        $goods_data['shipping_way']=$order_information['shipping_way'];
        $express=Express::find()->where(['order_no'=>$order_no])->andWhere(['sku'=>$sku])->one();
        $goods_data['send_time']=$express?date('Y-m-d H:i',$express->create_time):0;
        $goods_data['complete_time']=$order_information['complete_time'];
        if ($order_information['shipping_type']==1){
            $goods_data['shipping_way']='送货上门';
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
//                $is_unusual=0;
                $is_refund=1;
          }else{
//                $is_unusual=1;
                $is_refund=2;
          }
          $goods_data['create_time']=$order_information['create_time'];
            $data=array(
                'goods_data'=>$goods_data,
                'goods_value'=>$ordergoodsinformation,
                'receive_details'=>$receive_details,
                'is_unusual'=>$order_information['is_unusual'],
                'is_platform'=>$is_platform,
                'is_refund'=>$is_refund
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
        $code=1000;
        if ($shipping_type!=1){
            if (!$sku|| !$waybillnumber || !$order_no) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            $name=(new  Express())->GetExpressName($waybillnumber);
            if(!$name)
            {
                return Json::encode([
                    'code' => $code,
                    'msg' =>'快递单号错误',
                ]);
            }
        }

        if (!OrderGoods::FindByOrderNoAndSku($order_no,$sku))
        {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $res=GoodsOrder::SupplierDelivery($sku,$order_no,$waybillnumber,$shipping_type);
        if ($res==200){
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
            ]);
        }else{
            $code = $res;
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
        $sku = trim($request->post('sku', ''));
        $order_no = trim($request->post('order_no', ''));
        $waybillname = trim($request->post('waybillname', ''));
        $waybillnumber = trim($request->post('waybillnumber', ''));
        if (!$sku || !$waybillname || !$waybillnumber || !$order_no) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $model = new  Express();
        $waybillname=(new Express())->GetExpressName($waybillnumber);
        if (!$waybillname)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => '快递单号错误',
            ]);
        }
        $res = $model->Expressadd($sku, $waybillname, $waybillnumber, $order_no);
        if ($res) {
            return Json::encode([
                'code' => 200,
                'msg' => '添加成功',
                'data'=>$order_no
            ]);
        }else {
            return Json::encode([
                'code' => 1000,
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
        $waybillnumber= trim($request->post('waybillnumber', ''));
        $order_no= trim($request->post('order_no', ''));
        $sku=trim($request->post('sku', ''));
        $data=Express::find()
            ->select('waybillnumber,waybillname')
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$data || !$waybillnumber){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $waybillname=(new Express())->GetExpressName($waybillnumber);
        if (!$waybillname)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => '快递单号错误',
            ]);
        }
        $code=Express::ExpressUpdate($waybillnumber,$waybillname,$sku,$order_no);
        if ($code==200){
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok',
                'data'=>[
                    'shipping_way'=>$waybillname.'('.$waybillnumber.')'
                ]
            ]);
        }else{
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
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
            $order_no=trim($request->get('order_no',''));
            $sku=trim($request->get('sku',''));
            if (!$order_no  || !$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
        if($sku =='-1')
        {
            $shipping_type=0;
            $express=Express::find()
                ->select('waybillnumber,waybillname,create_time')
                ->where(['waybillnumber'=>$order_no])
                ->asArray()
                ->one();
            if (!$express)
            {
                $code=200;
                $arr[]=[
                    'time'=>date('Y-m-d H:i',time()),
                    'context'=>'无物流信息'
                ];
                return Json::encode([
                    'code' => $code,
                    'msg' =>'ok',
                    'data'=> [
                        'list'=>$arr,
                        'shipping_type'=>$shipping_type,
                        'waybillname'=>'暂无物流信息',
                        'waybillnumber'=>'0',
                        'order_no'=>'',
                        'mobile'=>''
                    ]
                ]);
            }
            $list=Express::FindExpressList($order_no,$sku);
            if (is_numeric($list))
            {
                $code=$list;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            $waybillname= $express['waybillname'];
            return Json::encode([
                'code' => 200,
                'msg' =>'ok',
                'data' => [
                    'list'=>$list,
                    'shipping_type'=>$shipping_type,
                    'waybillname'=>$waybillname,
                    'waybillnumber'=>$express['waybillnumber'],
                    'order_no'=>$order_no,
                    'mobile'=>''
                ],
            ]);
        }else
        {
            $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
            if (!$GoodsOrder)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            $shipping_type=GoodsOrder::findShippingType($order_no,$sku);
            $express=Express::find()
                ->select('waybillnumber,waybillname,create_time')
                ->where(['order_no'=>$order_no,'sku'=>$sku])
                ->asArray()
                ->one();
            if (!$express)
            {
                $code=200;
                $arr[]=[
                    'time'=>date('Y-m-d H:i',time()),
                    'context'=>'无物流信息'
                ];
                return Json::encode([
                    'code' => $code,
                    'msg' =>'ok',
                    'data'=> [
                        'list'=>$arr,
                        'shipping_type'=>$shipping_type,
                        'waybillname'=>'暂无物流信息',
                        'waybillnumber'=>'0',
                        'order_no'=>$order_no,
                        'mobile'=>$GoodsOrder->consignee_mobile
                    ]
                ]);
            }
            switch ($shipping_type){
                case 0:
                    $list=Express::FindExpressList($order_no,$sku);
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
                    $list=Express::FindExpressListSendToHome($order_no,$sku);
                    break;
            }
            if ($shipping_type==1)
            {
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
                    'waybillnumber'=>$express['waybillnumber'],
                    'order_no'=>$order_no,
                    'mobile'=>$GoodsOrder->consignee_mobile
                ],
            ]);
        }
    }
    /**
     * @return string
     */
    public  function  actionAfterFindExpress()
    {
        $request=Yii::$app->request;
        $waybillnumber=trim($request->get('waybillnumber',''));
        if (!$waybillnumber)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $express=Express::find()
        ->select('waybillnumber,waybillname,create_time')
        ->where(['waybillnumber'=>$waybillnumber])
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
        $list=Express::FindExpressList($waybillnumber,'-1');
        if (is_numeric($list))
        {
            $code=$list;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $waybillname= $express['waybillname'];
        return Json::encode([
            'code' => 200,
            'msg' =>'ok',
            'data' => [
                'list'=>$list,
                'waybillname'=>$waybillname,
                'waybillnumber'=>$express['waybillnumber']
            ],
        ]);
    }
    /**
     * @return string
     */
    public function actionGetplatformdetail(){
        $request=Yii::$app->request;
        $order_no=trim($request->post('order_no',''));
        $sku=trim($request->post('sku',''));
        if (!$sku || !$order_no){
            $order_no=trim($request->get('order_no',''));
            $sku=trim($request->get('sku',''));
            if (!$order_no || !$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
        $data=GoodsOrder::GetPlatFormDetail($order_no,$sku);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }
    //判断用户是否登陆
    public  static function userIdentity()
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
    /**
     * @param $user
     * @return mixed|string
     */
    public static function LhzzIdentity($user)
    {
        $lhzz=Lhzz::find()
            ->select('id')
            ->where(['uid'=>$user])
            ->one();
        if (!$lhzz){
            $code = 1010;
            return Json::encode([
                'code' => 1052,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return $lhzz['id'];
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
        $supplier=Supplier::find()
            ->select('uid')
            ->where(['id'=>$GoodsOrder->supplier_id])
            ->one();
        $supplier_user=User::find()
            ->where(['id'=>$supplier->uid])
            ->one();
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
                $content = "订单号{$order_no},{$OrderGoods[0]->goods_name}";
                    try {
                        $goods->order_status=2;
                        $res=$goods->save(false);
                        if (!$res){
                            $code=500;
                            $trans->rollBack();
                            return Json::encode([
                                'code' => $code,
                                'msg' => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }
                        $record=new UserNewsRecord();
                        $record->uid=$supplier_user->id;
                        $record->role_id=6;
                        $record->title='已取消订单';
                        $record->content=$content;
                        $record->send_time=time();
                        $record->order_no=$order_no;
                        $record->sku=$sku;
                         if (!$record->save(false))
                         {
                             $trans->rollBack();
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
                $registration_id=$supplier_user->registration_id;
                $push=new Jpush();
                $extras =[
                    'role_id'=>6,
                    'order_no'=>$order_no,
                    'sku'=>$sku,
                    'type'=>GoodsOrder::STATUS_DESC_DETAILS,
                ];
                //推送附加字段的类型
                $m_time = '86400';//离线保留时间
                $receive = ['registration_id'=>[$registration_id]];//设备的id标识
                $title='已取消订单';
                $result = $push->push($receive,$title,$content,$extras, $m_time);
                if (!$result)
                {
                    $code=1000;
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
           $code=GoodsOrder::applyRefund($order_no,$sku,$apply_reason,$user,$supplier_user);
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
        $arr=OrderRefund::SetRefundParameter($order_refund);
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
        $order_no=$request->post('order_no','');
        $sku=$request->post('sku','');
        $handle_reason=$request->post('handle_reason','');
        $handle=$request->post('handle','');
        if (!$order_no  || ! $sku || !$handle)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if ($handle==2)
        {

            if (!$handle_reason)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }

        $supplier=Supplier::find()
            ->where(['uid'=>$user->id])
            ->one();
        $order=GoodsOrder::find()
            ->select('id')
            ->where(['order_no'=>$order_no,'supplier_id'=>$supplier->id])
            ->one();
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
            $order_no=trim($request->get('order_no',''));
            $sku=trim($request->get('sku',''));
            if (!$order_no || ! $sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
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
                if ($type==GoodsOrder::ORDER_TYPE_ALL)
                {
                    $where ="a.user_id={$user->id} and role_id={$user->last_role_id_app}";
                }else{
                    $where=GoodsOrder::GetTypeWhere($type);
                    $where .= " and a.user_id={$user->id}  and role_id={$user->last_role_id_app}  and order_refer = 2";
                }
                break;
            case 'supplier':
                $supplier=Supplier::find()
                    ->where(['uid'=>$user->id])
                    ->one();
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
                    $where .=" and a.supplier_id={$supplier->id}  and a.order_refer = 2";
                }
                break;
        }
        if ($type=='all')
        {
            $where.=' and z.customer_service=0';
        }
        $sort=' a.create_time  desc';
        $paginationData = GoodsOrder::paginationByUserOrderList($where, GoodsOrder::FIELDS_USERORDER_ADMIN, $page, $size,$type,$user,$role);
        if (is_numeric($paginationData))
        {
            $code=$paginationData;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$paginationData
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
        if ($user->pay_password=='')
        {
            $code=1081;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (Yii::$app->getSecurity()->validatePassword($postData['pay_password'],$user->pay_password)==false)
        {
            $code=1055;
            return Json::encode([
                'code'=>$code,
                'msg'=>Yii::$app->params['errorCodes'][$code],
                'data'=>$user->mobile
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
     * 获取订单详情
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
            $postData = Yii::$app->request->get();
            if(!array_key_exists('order_no', $postData)){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
         if(
             array_key_exists('sku', $postData)
             || !$postData['sku']==0
         )
         {
            $record=UserNewsRecord::find()
                ->where(['order_no'=>$postData['order_no']])
                ->andWhere(['sku'=>$postData['sku']])
                ->all();
            foreach ($record as &$rec)
            {
                if ($rec)
                {
                    $rec->status=1;
                    $rec->save(false);
                }
            }
        }else{
            $record=UserNewsRecord::find()
                ->where(['order_no'=>$postData['order_no']])
                ->all();
            foreach ($record as &$rec)
            {
                if ($rec)
                {
                    $rec->status=1;
                    $rec->save(false);
                }
            }
        }
         $arr=GoodsOrder::FindUserOrderDetails($postData);
         if($arr)
         {
             $data=GoodsOrder::GetOrderDetailsData($arr,$user);
         }else
         {
             $data=[];
         }
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    /**
     * 用户去评论
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
        if (!$uploadsData ==1000){
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
     * 获取订单评论
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
        if (
            !array_key_exists('order_no',$postData)
            || ! array_key_exists('sku',$postData))
        {
            $postData=yii::$app->request->get();
            if (!array_key_exists('order_no',$postData)||
            !array_key_exists('sku',$postData))
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
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
            $comment['score']=GoodsComment::DESC_SCORE_GOOD;
        }else if (2< $comment['score'] && $comment['score']<= 6 )
        {
            $comment['score']=GoodsComment::DESC_SCORE_MEDIUM;
        }else{
            $comment['score']=GoodsComment::DESC_SCORE_POOR;
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
     * 评论回复操作
     * @return string
     */
    public function  actionCommentReply()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
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
        if (!$postData)
        {
            if(
                !array_key_exists('order_no', $postData)
                || !array_key_exists('sku', $postData)
            ){
                $postData = Yii::$app->request->get();
            }
        }
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
        $postData = \Yii::$app->request->get();
        if (!isset($postData['order_no'])
            ||!isset($postData['sku']))
        {
            $postData=Yii::$app->request->post();
        }
//        $file=Yii::$app->request->post('file');
        $uploadsData=FileService::uploadMore();
        if (!$uploadsData ==1000){
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
        if (!$code ==200){
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
     * 订单售后详情--大后台，商家后台
     * @return string
     */
    public function actionAfterSaleDetailAdmin()
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
        $order_no=trim($request->get('order_no',''));
        $sku=trim($request->get('sku',''));
        if(!$order_no || !$sku){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $role=Supplier::tableName();
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
        if (!array_key_exists($OrderAfterSale->type,OrderAfterSale::AFTER_SALE_SERVICES))
        {
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
                $data=OrderAfterSale::findHandleAfterSaleAgree($OrderAfterSale,$role);
                break;
            case 2:
                $data=OrderAfterSale::findHandleAfterSaleDisagree($OrderAfterSale,$role);
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
        $postData = Yii::$app->request->get();
        $after_sale_detail=OrderAfterSale::GetAfterSaleData($postData,$user);
        if (is_numeric($after_sale_detail)){
            $code=$after_sale_detail;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
                if ($OrderGoods->customer_service==2)
                {
                    $state='over';
                }else
                {
                    $state='in';
                }
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>[
                'after_sale_detail'=>$after_sale_detail,
                'after_sale_progress'=>$data,
                'state'=>$state
            ]
        ]);
    }
     /**售后详情
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
        $role=trim($request->post('role','user'));
        if(!$order_no || !$sku){
            $order_no=trim($request->get('order_no',''));
            $sku=trim($request->get('sku',''));
            $role=trim($request->get('role','user'));
            if (!$order_no || !$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        if (!$role)
        {
            $role='user';
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
        if (!array_key_exists($OrderAfterSale->type,OrderAfterSale::AFTER_SALE_SERVICES))
        {
            $code=500;
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
                $data=OrderAfterSale::findHandleAfterSaleAgree($OrderAfterSale,$role);
                break;
            case 2:
                $data=OrderAfterSale::findHandleAfterSaleDisagree($OrderAfterSale,$role);
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
    /**
     * 售后详情 -- 商家派出人员
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
        $worker_name=trim($request->post('worker_name',''));
        $worker_mobile=trim($request->post('worker_mobile',''));
        if(!$order_no || !$sku || !$worker_name  || !$worker_mobile){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
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
        if (!$code ==200){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=OrderAfterSale::SupplierSendMan($OrderAfterSale,$worker_mobile,$worker_name);
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
        $type=trim($request->post('type',''));
        if (!$type)
        {
            $type='';
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
        if (!$code ==200){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=OrderAfterSale::SupplierConfirm($OrderAfterSale,$type);
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
     * 订单详情 -- 用户确认
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
        if (!$code ==200){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $type=trim($request->post('type',''));
        if (!$type)
        {
            $type='';
        }
        $code=OrderAfterSale::userConfirm($OrderAfterSale,$type);
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
     * 添加测试数据
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
     * 商家获取异常状态
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
            $order_no=trim($request->get('order_no',''));
            $sku=trim($request->get('sku',''));
            if (!$order_no || !$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        if (!$GoodsOrder )
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
            $data[]=[
                'order_type'=>'unshipped',
                'list'=>$arr1
            ];
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
            $data[]=[
                'order_type'=>'unreceived',
                'list'=>$arr2
            ];
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
            $order_no=trim($request->get('order_no',''));
            $sku=trim($request->get('sku',''));
            if (!$order_no || !$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
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
                    $arr1[] =
                    [
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
         $orders=explode(',',$postData['list']);
        if (!is_array($orders))
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $orderAmount=GoodsOrder::CalculationCost($orders);
        if ( !$postData['total_amount']*100 == $orderAmount){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        };
       $data=Alipay::OrderAppPay($orderAmount,$postData['list']);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }
    /**
     * 支付宝APP支付付款数据库操作--异步返回
     */
    public  function  actionAppOrderPayDatabase()
    {
        $post=Yii::$app->request->post();
        $model=new Alipay();
        $alipaySevice=$model->Alipaylinenotify();
        $result = $alipaySevice->check($post);
        if ($result){
            if ($post['trade_status'] == 'TRADE_SUCCESS'){
                $orders=explode(',',urldecode($post['passback_params']));
                $total_amount=$post['total_amount'];
                $orderAmount=GoodsOrder::CalculationCost($orders);
//                    if (!$total_amount*100==$orderAmount)
//                    {
//                        echo 'fail';
//                        exit;
//                    }
                $tran = Yii::$app->db->beginTransaction();
                try{
                    $Ord= GoodsOrder::find()
                        ->where(['order_no'=>$orders[0]])
                        ->one();
                    $role_id=$Ord->role_id;
                    $user=User::findOne($Ord->user_id);
                    $role=Role::GetRoleByRoleId($role_id,$user);
                    switch ($role_id)
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
                    $transaction_no=GoodsOrder::SetTransactionNo($role_number);
                    foreach ($orders as $k =>$v){
                        $GoodsOrder=GoodsOrder::find()
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
                               echo 'fail';
                               exit;
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
                                $GoodsStat->sold_number=$Goods['goods_number'];
                                $GoodsStat->amount_sold=$GoodsOrder->amount_order;
                                $GoodsStat->create_date=$date;
                                if ($GoodsStat->save(false))
                                {
                                    $code=500;
                                    $tran->rollBack();
                                    return $code;
                                }
                            }else{
                                $GoodsStat->sold_number+=$Goods['goods_number'];
                                $GoodsStat->amount_sold+=$GoodsOrder->amount_order;
                                if ($GoodsStat->save(false))
                                {
                                    $code=500;
                                    $tran->rollBack();
                                    return $code;
                                }
                            }
                        }
                        if ( !$GoodsOrder|| $GoodsOrder ->pay_status!=0)
                        {
                            echo 'fail';
                            exit;
                        }
//                            $role_id=$GoodsOrder->role_id;
//                            $user=User::find()->where(['id'=>$GoodsOrder->user_id])->one();
                            $GoodsOrder->pay_status=1;
                            $GoodsOrder->pay_name=PayService::ALI_APP_PAY;
                            $res=$GoodsOrder->save(false);
                            if (!$res)
                            {
                                $tran->rollBack();
                                echo 'fail';
                                die;
                            }
                        $access=new UserAccessdetail();
                        $access->uid=$user->id;
                        $access->role_id=$role_id;
                        $access->access_type=7;
                        $access->access_money=$GoodsOrder['amount_order'];
                        $access->create_time=time();
                        $access->order_no=$orders[$k];
                        $access->transaction_no=$transaction_no;
                        $res3=$access->save(false);
                        if ( !$res3){
                            $tran->rollBack();
                            $code=500;
                            return $code;
                        }
                    }
                    $tran->commit();
                }catch (Exception $e){
                    $tran->rollBack();
                    echo 'fail';
                    die;
                }
                echo 'success';
            }
        }else{
            //验证失败
            echo "fail";    //请不要修改或删除
        }
    }
    /**
     * 获取订单数量
     * @return string
     */
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
          $supplier_id=Supplier::find()
              ->where(['uid'=>$user->id])
              ->one()
              ->id;

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
            ->where("g.pay_status=1 and o.order_status=1 and shipping_status=2  and g.supplier_id={$supplier_id} and o.customer_service=0 ")
            ->count();
        $canceled=(new Query())
            ->from(GoodsOrder::tableName().' as g')
            ->select('g.id')
            ->leftJoin(OrderGoods::tableName().' as o','g.order_no=o.order_no')
            ->where("o.order_status=2 and o.customer_service=0  and g.supplier_id={$supplier_id}")
            ->count();
        $customer_service=(new Query())
            ->from(GoodsOrder::tableName().' as g')
            ->select('g.id')
            ->leftJoin(OrderGoods::tableName().' as o','g.order_no=o.order_no')
            ->where("o.order_status=1  and  o.customer_service!=0  and g.supplier_id={$supplier_id}")
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
                'canceled'=>$canceled,
                'customer_service'=>$customer_service
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
            $order_no=$request->get('order_no','');
            $sku=$request->get('sku','');
            if (!$order_no || !$sku)
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        if (!$OrderGoods || !$GoodsOrder)
        {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()
            ->select('uid')
            ->where(['id'=>$GoodsOrder->supplier_id])
            ->one();
        $arr=explode(',',$OrderGoods->after_sale_services);
        $data=[];
        foreach ($arr as $k =>$v)
        {
            if ($arr[$k]==0 ||$arr[$k]==1 )
            {
                unset($arr[$k]);
            }
            else
            {
                $value=OrderAfterSale::GOODS_AFTER_SALE_SERVICES[$arr[$k]];
                $name=array_search($value,OrderAfterSale::AFTER_SALE_SERVICES);
                $data[]=[
                    'name'=>$name,
                    'value'=>$value
                ];
            }
        }
        if ($data==[])
        {
            $code=1044;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
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
                    'goods_price'=>StringService::formatPrice($OrderGoods->goods_price*0.01)
                ],
                'after_sale'=>$data,
                'user'=>[
                    'uid'=>$supplier->uid,
                    'to_role_id'=>Yii::$app->params['supplierRoleId']
                ]
            ]
        ]);
    }
    /**
     * 删除评论操作
     * @return string
     */
    public  function  actionSupplierDeleteComment()
    {
        $order_no=Yii::$app->request->post('order_no','');
        $sku=Yii::$app->request->post('sku','');
        $code=1000;
        if (!$sku ||! $order_no)
        {
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $lhzz=Lhzz::find()->where(['uid'=>$user->id])->one();
        if (!$lhzz)
        {
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        if (!$OrderGoods)
        {
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$OrderGoods->comment_id)
        {
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $comment=GoodsComment::find()->where(['id'=>$OrderGoods->comment_id])->one();
        if (!$comment)
        {
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $delete=new DeletedGoodsComment();
            $delete->uid=$comment->uid;
            $delete->role_id=$comment->role_id;
            $delete->name=$comment->name;
            $delete->icon=$comment->icon;
            $delete->content=$comment->content;
            $delete->score=$comment->score;
            $delete->goods_id=$comment->goods_id;
            $delete->store_service_score=$comment->store_service_score;
            $delete->shipping_score=$comment->shipping_score;
            $delete->logistics_speed_score=$comment->logistics_speed_score;
            $delete->is_anonymous=$comment->is_anonymous;
            $delete->create_time=time();
            $delete->comment_time=$comment->create_time;
            $delete->handle_uid=$user->id;
            $delete->order_no=$OrderGoods->order_no;
            $delete->sku=$OrderGoods->sku;
            $delete->comment_id=$comment->id;
            $res1=$delete->save(false);
            if (!$res1)
            {
                $tran->rollBack();
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $res2=$comment->delete();
            if (!$res2)
            {
                $tran->rollBack();
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $OrderGoods->comment_id=0;
            $res3=$OrderGoods->save(false);
            if (!$res3)
            {
                $tran->rollBack();
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
     * 已删除评论列表
     * @return string
     */
    public  function  actionDeleteCommentList()
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
        $page=trim($request->get('page',1));
        $size=trim($request->get('size',DeletedGoodsComment::PAGE_SIZE_DEFAULT));
        $supplier_id=trim($request->get('supplier_id', ''));
        if (!$supplier_id)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $keyword = trim($request->get('keyword', ''));
        $timeType = trim($request->get('time_type', ''));
        $where="g.supplier_id={$supplier_id}";
        if ($keyword)
        {
            $where .=" and   CONCAT(d.order_no,o.goods_name) like '%{$keyword}%'";
//                $where .=" and d.order_no like '%{$keyword}%' or  o.goods_name like '%{$keyword}%'";
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
        }
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= " and  d.create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = (int)strtotime($endTime);
            $endTime && $where .= "   and  d.create_time <= {$endTime}";
        }
        $paginationData = DeletedGoodsComment::pagination($where, DeletedGoodsComment::FIELDS_COMMENT_ADMIN, $page, $size);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);
    }
    /**
     * 删除评论详情
     * @return string
     */
    public function  actionDeleteCommentDetails()
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
        $order_no=$request->post('order_no','');
        $sku=$request->post('sku','');
        if (!$order_no || !$sku)
        {
            $order_no=$request->get('order_no','');
            $sku=$request->get('sku','');
            if(!$order_no || !$sku)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $OrderGoods=OrderGoods::find()
            ->select('goods_name,sku')
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->asArray()
            ->one();
        if (!$OrderGoods)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $comment=DeletedGoodsComment::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
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
            $comment['score']=GoodsComment::DESC_SCORE_GOOD;
        }else if (2< $comment['score'] && $comment['score']<= 6 )
        {
            $comment['score']=GoodsComment::DESC_SCORE_MEDIUM;
        }else{
            $comment['score']=GoodsComment::DESC_SCORE_POOR;
        }
        $comment['create_time']=date('Y-m-d H:i',0);
            $comment['image']=CommentImage::find()
                ->select('image')
                ->where(['comment_id'=>$comment['comment_id']])
                ->all();
            $reply=CommentReply::find()
                ->select('content')
                ->where(['comment_id'=>$comment['comment_id']])
                ->asArray()
                ->one();
            if ($reply)
            {
                $comment['reply']=$reply['content'];
            }else{
                $comment['reply']='';
            }
        $comment['goods_name']=$OrderGoods['goods_name'];
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$comment
        ]);

    }
    /**
     * 订单详情-商品详情
     * @return string
     */
    public  function  actionGoodsView()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $order_no=Yii::$app->request->post('order_no','');
        $sku=Yii::$app->request->post('sku','');
        $code=1000;
        if (!$sku ||! $order_no)
        {
            $order_no=Yii::$app->request->get('order_no','');
            $sku=Yii::$app->request->get('sku','');
            if (!$order_no || !$sku)
            {
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        if (!$OrderGoods)
        {
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $Goods=Goods::find()
            ->where(['sku'=>$OrderGoods->sku])
            ->one();
        if (!$Goods)
        {
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $three_category=GoodsCategory::find()
            ->select('path,title,parent_title')
            ->where(['id'=>$OrderGoods->category_id])
            ->one();

        if (!$three_category)
        {
            $three_category=GoodsCategory::find()
                ->select('path,title,parent_title')
                ->where(['id'=>$Goods->category_id])
                ->one();
        }

        $category_arr=explode(',',$three_category->path);
        $first_category=GoodsCategory::find()
            ->select('path,title,parent_title')
            ->where(['id'=>$category_arr[0]])
            ->one();
        $category=$first_category->title.'-'.$three_category->parent_title.'-'.$three_category->title;
        $brand=OrderGoodsBrand::find()->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        if (!$brand)
        {
            $brand=GoodsBrand::findOne($Goods->brand_id);
        }
        $serie=OrderSeries::find()->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        if ($serie)
        {
            $series=$serie->series;
        }else{
            $series='';
        }
        $sty=OrderStyle::find()->where(['order_no'=>$order_no,'sku'=>$sku])->one();
        if ($sty)
        {
            $style= $sty->style;
        }else
        {
            $style='';
        }
        $attr=OrderGoodsAttr::find()
            ->select('name,value,unit')
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->asArray()
            ->all();
        $goods_image=OrderGoodsImage::find()
            ->select('image')
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->asArray()
            ->all();
        $market_price=$OrderGoods->market_price;
        $supplier_price=$OrderGoods->supplier_price;
        $platform_price=$OrderGoods->platform_price;
        $left_number=$Goods->left_number;
        $purchase_price_decoration_company=$OrderGoods->purchase_price_decoration_company;
        $purchase_price_manager=$OrderGoods->purchase_price_manager;
        $purchase_price_designer=$OrderGoods->purchase_price_designer;
        $logisticsTemplate=OrderLogisticsTemplate::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->asArray()
            ->one();
        if (!$logisticsTemplate)
        {
            $logisticsTemplate=LogisticsTemplate::find()
                ->where(['id'=>$Goods->logistics_template_id])
                ->asArray()
                ->one();
        }
        $logisticsTemplate['delivery_cost_default']=StringService::formatPrice($logisticsTemplate['delivery_cost_default']*0.01);
        $logisticsTemplate['delivery_cost_delta']=StringService::formatPrice($logisticsTemplate['delivery_cost_delta']*0.01);
        $logisticsDistrict=OrderLogisticsDistrict::find()
            ->select('district_name')
            ->where(['order_template_id'=>$logisticsTemplate['id']])
            ->asArray()
            ->all();
        $after_sale=explode(',',$OrderGoods->after_sale_services);
        $guarantee=[];
        $after=[];
        foreach ($after_sale as &$afterSale)
        {
            if ($afterSale==0)
            {
                $guarantee[]='提供发票';
            }
            if ($afterSale==1)
            {
                $guarantee[]='上门安装';
            }
            if ($afterSale==2)
            {
                $after[]='上门维修';
            }
            if ($afterSale==3)
            {
                $after[]='上门退货';
            }
            if ($afterSale==4)
            {
                $after[]='上门换货';
            }
            if ($afterSale==5)
            {
                $after[]='退货';
            }
            if ($afterSale==6)
            {
                $after[]='换货';
            }
        }
        $qrcode='/'.UploadForm::DIR_PUBLIC . '/' . Goods::GOODS_QR_PREFIX . $Goods->id . '.png';
        $descriptionList=OrderGoodsDescription::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$descriptionList)
        {
            $description=$Goods->description;
        }else{
            $description=$descriptionList->description;
        }
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>[
                'category'=>$category,
                'goods_name'=>$OrderGoods->goods_name,
                'subtitle'=>$OrderGoods->subtitle,
                'brand'=>$brand->name,
                'series'=>$series,
                'style'=>$style,
                'goods_attr'=>$attr,
                'cover_image'=>$OrderGoods->cover_image,
                'goods_image'=>$goods_image,
                'market_price'=> StringService::formatPrice($market_price*0.01),
                'supplier_price'=> StringService::formatPrice($supplier_price*0.01),
                'platform_price'=> StringService::formatPrice($platform_price*0.01),
                'left_number'=>$left_number,
                'purchase_price_decoration_company'=> StringService::formatPrice($purchase_price_decoration_company*0.01),
                'purchase_price_manager'=> StringService::formatPrice($purchase_price_manager*0.01),
                'purchase_price_designer'=>StringService::formatPrice($purchase_price_designer*0.01),
                'logisticsTemplate'=>$logisticsTemplate,
                'logisticsDistrict'=>$logisticsDistrict,
                'guarantee'=>$guarantee,
                'after'=>$after,
                'qrcode'=>$qrcode,
                'description'=>$description
            ]
        ]);
    }
    /**
     * 售后发货
     * @return string
     */
    public function  actionAfterSaleDelivery()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = 1000;
        $request = Yii::$app->request;
        $waybillnumber=$request->post('waybillnumber');
        $order_no=$request->post('order_no');
        $sku=$request->post('sku');
        $role=$request->post('role');
        if (!$role)
        {
            $role='user';
        }
        if (!$waybillnumber || !$order_no || !$sku)
        {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $waybillname=(new Express())->GetExpressName($waybillnumber);
        if (!$waybillname)
        {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $orderAfterSale=OrderAfterSale::find()
            ->where(['order_no'=>$order_no,'sku'=>$sku])
            ->one();
        if (!$orderAfterSale)
        {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $tran = Yii::$app->db->beginTransaction();
        $time=time();
        try{
            $express=new Express();
            $express->waybillnumber=$waybillnumber;
            $express->waybillname=$waybillname;
            $express->create_time=$time;
            if (!$express->save(false))
            {
                $tran->rollBack();
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            };
            switch ($role)
            {
                case 'user':
                    $orderAfterSale->buyer_express_id=$express->id;
                    break;
                case 'supplier':
                    $orderAfterSale->supplier_express_id=$express->id;
                    break;
            }
            if (!$orderAfterSale->save(false))
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            };
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
     * 获取openID1-微信
     * @return string
     */
    public function actionGetOpenId()
    {
            $tools = new PayService();
            $code=Yii::$app->request->post('code','');
            $openid = $tools->getOpenidFromMp($code);

            return Json::encode([
                'code' => 200,
                'msg'  => 'ok',
                'data'=>$openid
            ]);

    }
    /**
     * @return string
     */
    public function  actionFindOpenId()
     {
          $url=Yii::$app->request->post('url','');
          if(!$url)
          {
               $code=1000;
               return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
               ]);
          }
          $tools = new PayService();
          if (!isset($_GET['code'])){
               //触发微信返回code码
               $baseUrl = urlencode($url);
               $url = $tools->__CreateOauthUrlForCode($baseUrl);
               $code=200;
               return Json::encode([
                    'code' => $code,
                    'msg'  => 'ok',
                    'data' =>$url
               ]);
          } else {
                //获取code码，以获取openid
                $code = $_GET['code'];
                $openid = $tools->getOpenidFromMp($code);
                $code=200;
                return Json::encode([
                     'code' => $code,
                     'msg'  => 'ok',
                     'data' =>$openid
                ]);
         }
     }
    public  function  actionReturnUrl()
    {
     $tools = new PayService();
     $code=Yii::$app->request->get('code');
     $openid = $tools->getOpenidFromMp($code);
     Yii::$app->session['openId']=$openid;
     echo $openid;
    }
    public  function  actionRetOpenId()
    {
        echo Yii::$app->session['openId'];exit;
    }
    public  function  actionTestOpenId2()
    {
        $tools = new PayService();
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url=$http_type."ac.cdlhzz.cn/order/return-url";
        $baseUrl = urlencode($url);
        $urls = $tools->__CreateOauthUrlForCode1($baseUrl);
        file_get_contents($urls);
    }
    public  function  actionTestOpenId()
    {
        $tools = new PayService();
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $url=$http_type."ac.cdlhzz.cn/order/return-url";
            $baseUrl = urlencode($url);
            $urls = $tools->__CreateOauthUrlForCode1($baseUrl);
//            $this->redirect($urls);
            header("Location: {$urls}");


    }
    /**
     * 提醒发货接口
     * @return string
     */
    public function actionRemindSendGoods()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $order_no=Yii::$app->request->post('order_no','');
        $sku=Yii::$app->request->post('sku','');
        $code=1000;
        if (!$sku ||  !$order_no)
        {
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        if (!$GoodsOrder || !$OrderGoods)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $cache = Yii::$app->cache;
        $data = $cache->get(GoodsOrder::REMIND_SEND_GOODS.$user->id.$order_no);
        if (!$data)
        {
            $cacheData=GoodsOrder::REMIND_SEND_GOODS.$user->id;
            $end_time=strtotime(date('Y-m-d',time()+23*60*60+59*60))-time();
            $res= $cache->set(GoodsOrder::REMIND_SEND_GOODS.$user->id.$order_no,$cacheData,$end_time);
            if (!$res)
            {
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $tran = Yii::$app->db->beginTransaction();
            try{
                $supplier=Supplier::find()
                    ->where(['id'=>$GoodsOrder->supplier_id])
                    ->one();
                $supplier_user=User::find()
                    ->where(['id'=>$supplier->uid])
                    ->one();
                $content = "订单号{$order_no},{$OrderGoods->goods_name}...";
                $record=new UserNewsRecord();
                $record->uid=$supplier_user->id;
                $record->role_id=6;
                $record->title='请尽快发货';
                $record->content=$content;
                $record->send_time=time();
                $record->order_no=$order_no;
                $record->sku=$sku;
                if (!$record->save(false))
                {
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $tran->commit();
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $registration_id=$supplier_user->registration_id;
            $push=new Jpush();
            $extras = [
                'role_id'=>6,
                'order_no'=>$order_no,
                'sku'=>$sku,
                'type'=>GoodsOrder::STATUS_DESC_DETAILS,
            ];
            //推送附加字段的类型
            $m_time = '86400';//离线保留时间
            $receive = ['registration_id'=>[$registration_id]];//设备的id标识
            $title='请尽快发货';
            $result = $push->push($receive,$title,$content,$extras, $m_time);
            if (!$result)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }
            return Json::encode([
                'code' =>  200,
                'msg'  => '提醒发货',
                'data' =>$end_time
            ]);
        }else{
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg'  =>'你已经提醒过发货了'
            ]);
        }
    }
    /**
     * 删除购物车商品
     * @return string
     */
    public  function  actionDelShippingCartGoods()
    {
        $request=Yii::$app->request;
        $orders=$request->post('orders');
        if (!$orders)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
//        $lists=ShippingCart::find()
//            ->where(['uid'=>$user->id,'role_id'=>$user->last_role_id_app])
//            ->asArray()
//            ->all();
//        foreach ($lists as &$list)
//        {
//            $carts[]=$list['id'];
//        }
        $andWhere=['uid'=>$user->id,'role_id'=>$user->last_role_id_app];
        $code=ShippingCart::DelShippingCartData($orders,$andWhere);
        if ($code==200)
        {
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
     * app购买商品
     * @return string
     */
    public function actionAppBuyGoods()
    {
        $user = Yii::$app->user->identity;
        if (!$user)
        {
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $suppliers=Json::decode($request->post('suppliers'));
        $total_amount=$request->post('total_amount');
        $address_id=$request->post('address_id');
        $pay_way=$request->post('pay_way');
        if(!$suppliers  ||  !$total_amount || !$address_id || !$pay_way)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $orders=GoodsOrder::AppBuy($user,$address_id,$suppliers,$total_amount,$pay_way);
        if ($orders==500 || $orders==1000)
        {
            $code=$orders;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg'  =>'ok',
            'data' =>$orders
        ]);
    }
    /**
     * 测试收货
     * @return int|string
     */
    public function  actionTestConfirmReceipt()
    {
        $request=Yii::$app->request;
        $order_no=$request->post('order_no');
        $sku=$request->post('sku');
        $orderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        $goodsOrder=GoodsOrder::find()
            ->where(['order_no'=>$order_no])
            ->one();
        $supplier=Supplier::findOne($goodsOrder->supplier_id);
        if (!$goodsOrder || !$supplier )
        {
            $code=1000;
            return Json::encode(
                [
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $orderGoods->order_status=1;
            $orderGoods->shipping_status=2;
            $res=$orderGoods->save(false);
            if (!$res)
            {
                $tran->rollBack();
            }
            $transaction_no=GoodsOrder::SetTransactionNo($supplier->shop_no);
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
            if (!$res2)
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $supplier->availableamount+=($orderGoods->freight+$orderGoods->supplier_price*$orderGoods->goods_number);
            $supplier->balance+=$orderGoods->freight+$orderGoods->supplier_price*$orderGoods->goods_number;
            $res3=$supplier->save(false);
            if (!$res3)
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $express=Express::find()
                ->where(['order_no'=>$order_no,'sku'=>$sku])
                ->one();
            if ($express)
            {
                $express->receive_time=time();
                if (!$express->save(false))
                {
                    $tran->rollBack();
                }
            }
            $tran->commit();
            $code=200;
            return Json::encode([
                'code' => $code,
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
     * 去付款-微信app支付
     * @return string
     */
    public  function actionAppOrderWxPay()
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
        $orders=explode(',',$postData['list']);
        if (!is_array($orders))
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $orderAmount=123;
//        $orderAmount=GoodsOrder::CalculationCost($orders);
//        if ($postData['total_amount']*100  != $orderAmount){
//            $code=1000;
//            return Json::encode([
//                'code' => $code,
//                'msg' => Yii::$app->params['errorCodes'][$code]
//            ]);
//        };
        $data=Wxpay::OrderAppPay($orderAmount,$postData['list']);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }
    /**
     * @return bool
     */
    public  function  actionWxNotifyDatabase()
    {
        //获取通知的数据
        $xml = file_get_contents("php://input");
        $data=json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $msg=Json::decode($data);
        if ($msg['result_code']=='SUCCESS'){
            $transaction_id=$msg['transaction_id'];
            $result = Wxpay::QueryApporder($transaction_id);
            if (!$result)
            {
                return false;
            }
            $orders= explode(',',base64_decode($msg['attach']));
            $total_amount=$msg['total_fee'];
//            $orderAmount=GoodsOrder::CalculationCost($orders);
//                    if (!$total_amount==$orderAmount)
//                    {
//                        return false;
//                    }
            $tran = Yii::$app->db->beginTransaction();
            try{
                $Ord= GoodsOrder::find()
                    ->where(['order_no'=>$orders[0]])
                    ->one();
                $role_id=$Ord->role_id;
                $user=User::findOne($Ord->user_id);
                $role=Role::GetRoleByRoleId($role_id,$user);
                switch ($role_id)
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
                $transaction_no=GoodsOrder::SetTransactionNo($role_number);
                foreach ($orders as $k =>$v){
                    $GoodsOrder=GoodsOrder::find()
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
                           return false;
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
                            $GoodsStat->sold_number=$Goods['goods_number'];
                            $GoodsStat->amount_sold=$GoodsOrder->amount_order;
                            $GoodsStat->create_date=$date;
                            if ($GoodsStat->save(false))
                            {
                                $code=500;
                                $tran->rollBack();
                                return false;
                            }
                        }else{
                            $GoodsStat->sold_number+=$Goods['goods_number'];
                            $GoodsStat->amount_sold+=$GoodsOrder->amount_order;
                            if ($GoodsStat->save(false))
                            {
                                $code=500;
                                $tran->rollBack();
                                return false;
                            }
                        }
                    }
                    if ( !$GoodsOrder|| $GoodsOrder ->pay_status!=0)
                    {
                        return false;
                    }
                    $GoodsOrder->pay_status=1;
                    $GoodsOrder->pay_name=PayService::WE_CHAT_APP_PAY;
                    $res=$GoodsOrder->save(false);
                    if (!$res)
                    {
                        $tran->rollBack();
                        return false;
                    }
                    $access=new UserAccessdetail();
                    $access->uid=$user->id;
                    $access->role_id=$role_id;
                    $access->access_type=7;
                    $access->access_money=$GoodsOrder['amount_order'];
                    $access->create_time=time();
                    $access->order_no=$orders[$k];
                    $access->transaction_no=$transaction_no;
                    $res3=$access->save(false);
                    if ( !$res3){
                        $tran->rollBack();
                        return false;
                    }
                }


                $tran->commit();
            }catch (Exception $e){
                $tran->rollBack();
                return false;
            }
            return true;
        }
    }
    /**
     * 获取购物车列表
     * @return string
     */
    public function  actionFindShippingCartList()
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
        if (!$request->isGet)
        {
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=ShippingCart::ShippingList($user);
        if (is_numeric($data))
        {
            $code=$data;
            return Json::encode([
                'code'=>$code,
                'msg'=>Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    /**
     * 添加购物车-app端
     * @return string
     */
    public  function  actionAddShippingCart()
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
        $goods_id=$request->post('goods_id');
        $goods_num=$request->post('goods_num');
        if (!$goods_id  || !$goods_num)
        {
            $code=1000;
            return Json::encode(
                [
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }
        $Goods=Goods::findOne($goods_id);
        if (!$Goods)
        {
            $code=1000;
            return Json::encode(
                [
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }
        $supplier=Supplier::findOne($Goods->supplier_id);
        if (!$supplier)
        {
            $code=1000;
            return Json::encode(
                [
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $shippingCart=ShippingCart::find()
                ->where([
                    'goods_id'=>$goods_id,
                    'uid'=>$user->id,
                    'role_id'=>$user->last_role_id_app])
                ->one();
            if (!$shippingCart)
            {
                $shippingCart=new ShippingCart();
                $shippingCart->goods_id=$goods_id;
                $shippingCart->uid=$user->id;
                $shippingCart->role_id=$user->last_role_id_app;
                $shippingCart->goods_num=$goods_num;
                $shippingCart->create_time=time();
                if (!$shippingCart->save(false))
                {
                    $tran->rollBack();
                }
            }else{
                $shippingCart->goods_num+=$goods_num;
                if (!$shippingCart->save(false))
                {
                    $tran->rollBack();
                }
            }
            $tran->commit();
            $code=200;
            return Json::encode([
                'code' => $code,
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
     * 计算运费
     * @return string
     */
    public function actionCalculationFreight()
    {
        $goods=Yii::$app->request->post('goods');
        foreach ($goods as $one){
            if( !array_key_exists('goods_id',$one)
            ||!array_key_exists('num',$one))
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            if (empty($one['num']))
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            if ($one['num'] != 0 || $one['num'] !=null){
                $goods_ [] = $one;
            }else{
                unset($one);
            }
        }
        foreach ($goods_ as  $k =>$v)
        {
            $goodsData=Goods::findOne($goods_[$k]['goods_id']);
            if (!$goodsData)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $Good[$k]=LogisticsTemplate::find()
                ->where(['id'=>$goodsData->logistics_template_id])
                ->asArray()
                ->one();
            $Good[$k]['goods_id']=$goods_[$k]['goods_id'];
            $Good[$k]['num']=$goods_[$k]['num'];
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
            $cost['num']=0;
            foreach ($Good as &$list)
            {
                if ($list['id']==$cost['id'])
                {

                    $cost['num']+=$list['num'];
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
            if ($logistics_template['delivery_number_default']>=$cost['num'])
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

        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=> StringService::formatPrice($freight*0.01)
        ]);
    }
    /**
     * 订单详情页-获取商品信息
     * @return string
     */
    public function  actionFindAppGoodsData()
    {
        $data=file_get_contents("php://input");
        $arr=(array)json_decode($data);
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $goods=$arr['goods'];
        if (!$goods)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $all_money=0;
        if (!$goods)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        foreach ($goods as &$good)
        {
            $Good=Goods::findOne($good->goods_id);
            if (!$Good)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $Good=$Good->toArray();
            $Good['goods_num']=$good->goods_num;
            $Goods[]=$Good;
        }
        if (!$Goods)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $goods_price=GoodsOrder::GetRoleMoney($user->last_role_id_app);
        $supplier_ids=[];
        foreach ($Goods as &$Good)
        {
            if (!in_array($Good['supplier_id'],$supplier_ids))
            {
                $supplier_ids[]=$Good['supplier_id'];
            }
        }
        $data=[];
        $market_price=0;

        foreach ($supplier_ids as &$supplier_id)
        {
            $sup_goods=[];
            $discount_price=0;
            foreach ($Goods as &$Good)
            {
                if ($Good['supplier_id']==$supplier_id)
                {
                    $sup_goods[]=[
                        'goods_id'=>$Good['id'],
                        'goods_name'=>$Good['title'],
                        'subtitle'=>$Good['subtitle'],
                        'cover_image'=>$Good['cover_image'],
                        'goods_num'=>$Good['goods_num'],
                        'goods_price'=> StringService::formatPrice($Good["{$goods_price}"]*0.01)
                    ];
                    $market_price+=($Good["market_price"]*$Good['goods_num']);
                    $discount_price+=($Good["{$goods_price}"]*$Good['goods_num']);
                }
            }

            $sup_freight=GoodsOrder::CalculationFreight($sup_goods);
            $supplier=Supplier::findOne($supplier_id);
            if (!$supplier)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $data[]=
            [
                'supplier_id'=>$supplier_id,
                'shop_name'=>$supplier->shop_name,
                'freight'=> StringService::formatPrice($sup_freight*0.01),
                'market_price'=> StringService::formatPrice($market_price*0.01),
                'discount_price'=> StringService::formatPrice($discount_price*0.01),
                'require_payment'=> StringService::formatPrice(($discount_price+$sup_freight)*0.01),
                'goods'=>$sup_goods
            ];
            $all_money+=$discount_price;
        }

        $freight=GoodsOrder::CalculationFreight($goods);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>[
                'list'=>$data,
                'freight'=> StringService::formatPrice($freight*0.01),
                'all_money'=> StringService::formatPrice(($all_money+$freight)*0.01),
                'availableamount'=> StringService::formatPrice($user->availableamount*0.01)
            ]
        ]);
    }
    /**
     * 清空失效商品
     * @return string
     */
    public function actionDelInvalidGoods()
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
            ->asArray()
            ->all();
        foreach ($lists as &$list)
        {
           $Good=Goods::findOne($list['goods_id']);
           if ($Good)
           {
               if(!$Good->status==2)
               {
                   $Goods[]=$Good->id;
               }
           }
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            foreach ($Goods as &$cart)
            {
                $ca=ShippingCart::find()
                    ->where(['goods_id'=>$cart])
                    ->andWhere(['uid'=>$user->id,'role_id'=>$user->last_role_id_app])
                    ->one();
                if (!$ca)
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $res=$ca->delete();
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }
            $tran->commit();
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }
    /**
     * 获取平台介入操作状态
     * @return string
     */
    public function  actionFindOrderAfterHandelStatus()
    {
        $request=Yii::$app->request;
        $order_no=$request->get('order_no');
        $sku=$request->get('sku');
        if(!$order_no || !$sku)
        {
            $code=1000;
            return Json::encode(
                [
                    'code'=>$code,
                    'msg' =>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }

        $GoodsOrder=GoodsOrder::FindByOrderNo($order_no);
        if (!$GoodsOrder)
        {
            $code=1000;
            return Json::encode(
                [
                    'code'=>$code,
                    'msg' =>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        if (!$OrderGoods)
        {
            $code=1000;
            return Json::encode(
                [
                    'code'=>$code,
                    'msg' =>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }

        switch ($GoodsOrder->order_refer)
        {
            case 1:
                //     待付款
                $operation[]=[
                    'name'=>'关闭订单，线下退款',
                    'value'=>2
                ];
                break;
            case 2:
                //     待付款
                $operation[]=[
                    'name'=>'关闭订单，退款',
                    'value'=>1
                ];
                break;
        }
        if ($GoodsOrder->pay_status==0 && $OrderGoods->order_status==0)
        {

            $code=200;
            return Json::encode(
                [
                    'code'=>$code,
                    'msg'=>'ok',
                    'data'=>$operation
                ]
            );
        }else
        {
            switch ($OrderGoods->order_status){
                case 0:
                    $code=200;
                    return Json::encode(
                        [
                            'code'=>$code,
                            'msg'=>'ok',
                            'data'=>$operation
                        ]
                    );
                    break;
                case 1:
                    if ($GoodsOrder->order_refer==2)
                    {
                        switch($OrderGoods->customer_service){
                            case 0:
                                $code=200;
                                $after=explode(',',$OrderGoods->after_sale_services);
                                $data=[];
                                foreach ($after as &$afterList)
                                {
                                    if ($afterList!=0 && $afterList !=1)
                                    {$data[]=['name'=>OrderAfterSale::GOODS_AFTER_SALE_SERVICES[$afterList],
                                            'value'=>array_search(OrderAfterSale::GOODS_AFTER_SALE_SERVICES[$afterList],OrderPlatForm::PLATFORM_HANDLE_TYPE)];
                                    }
                                }
                                return Json::encode(
                                    [
                                        'code'=>$code,
                                        'msg'=>'ok',
                                        'data'=>$data
                                    ]
                                );
                                break;
                            case 1:
                                $code=200;
                                return Json::encode(
                                    [
                                        'code'=>$code,
                                        'msg'=>'ok',
                                        'data'=>$operation
                                    ]
                                );
                                break;
                            case 2:
//                                $code=200;
//                                $orderAfterSale=OrderAfterSale::find()
//                                    ->select('type')
//                                    ->where(['order_no'=>$order_no])
//                                    ->andWhere(['sku'=>$sku])
//                                    ->one();
//                                //1. 退货  2.换货  3.上门维修  4. 上门换货   5.上门退货
//                                switch ($orderAfterSale->type)
//                                {
//                                    case 1:
//                                        $data[]=[
//                                            'name'=>'退货',
//                                            'value'=>3,
//                                        ];
//                                        break;
//                                    case 2:
//                                        $data[]=[
//                                            'name'=>'换货',
//                                            'value'=>4,
//                                        ];
//                                        break;
//                                    case 3:
//                                        $data[]=[
//                                            'name'=>'上门维修',
//                                            'value'=>5,
//                                        ];
//                                        break;
//                                    case 4:
//                                        $data[]=[
//                                            'name'=>'上门换货',
//                                            'value'=>7,
//                                        ];
//                                        break;
//                                    case 5:
//                                        $data[]=[
//                                            'name'=>'上门退货',
//                                            'value'=>6,
//                                        ];
//                                        break;
//                                    case 6:
//                                        break;
//                                }
                                $code=200;
                                $after=explode(',',$OrderGoods->after_sale_services);
                                $data=[];
                                foreach ($after as &$afterList)
                                {
                                    if ($afterList!=0 && $afterList !=1)
                                    {$data[]=['name'=>OrderAfterSale::GOODS_AFTER_SALE_SERVICES[$afterList],
                                        'value'=>array_search(OrderAfterSale::GOODS_AFTER_SALE_SERVICES[$afterList],OrderPlatForm::PLATFORM_HANDLE_TYPE)];
                                    }
                                }
                                return Json::encode(
                                    [
                                        'code'=>$code,
                                        'msg'=>'ok',
                                        'data'=>$data
                                    ]
                                );
//                            $data[$k]['status']='售后完成';
                                break;
                        }
                    }else{
                        $code=200;
                        return Json::encode(
                            [
                                'code'=>$code,
                                'msg'=>'ok',
                                'data'=>$operation
                            ]
                        );
                    }

                    break;
                case 2:
//                    $data[$k]['status']='已取消';
                    $code=200;
                    return Json::encode(
                        [
                            'code'=>$code,
                            'msg'=>'ok',
                            'data'=>[]
                        ]
                    );
                    break;
            }
        }
    }
    /**
     * 关闭订单操作
     * @return string
     */
    public function actionCloseOrder()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $order_no=$request->post('order_no');
        $sku=$request->post('sku');
        $reason=$request->post('reason','');
        $code=1000;
        if(!$order_no || !$sku )
        {

            return Json::encode(
                [
                    'code'=>$code,
                    'msg' =>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);
        if (!$OrderGoods)
        {
            return Json::encode(
                [
                    'code'=>$code,
                    'msg' =>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }
        //1:售后中  2：售后完成
        if ($OrderGoods->customer_service==2  || $OrderGoods->order_status==2)
        {
            return Json::encode(
                [
                    'code'=>$code,
                    'msg' =>Yii::$app->params['errorCodes'][$code]
                ]
            );
        }
        //关闭订单操作
        $code=OrderAfterSale::CloseOrder($order_no,$sku,$reason);
        return Json::encode(
            [
                'code'=>$code,
                'msg' =>$code==200?'ok':Yii::$app->params['errorCodes'][$code],
            ]
        );

    }
    /**
     * 测试接口-获取商品
     * @return string
     */
    public  function  actionFindSupplierGoods()
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
        $Goods=Goods::find()
            ->select('id,sku,title')
            ->where(['supplier_id'=>$supplier->id])
            ->all();
        $code=200;
        return Json::encode(
            [
                'code'=>$code,
                'msg'=>'ok',
                'data'=>$Goods
            ]
        );
    }
    /**
     * 获取默认地址
     * @return string
     */
    public function  actionFindDefaultAddress()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        //default=1000
        $addressList = UserAddress::find()
            ->where(['uid' => $user->id])
            ->andWhere(['default'=>1])
            ->asArray()
            ->one();
        if (!$addressList)
        {
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>[
                    'id'=>1,
                    'uid'=>1,
                    'consignee'=>'',
                    'zipcode'=>'',
                    'mobile'=>'',
                    'district'=>'',
                    'addresstoken'=>'',
                    'default'=>1000,
                    'district_code'=>''
                ]
            ]);
        }
            $addressList['district_code'] = $addressList['district'];
            $addressList['district'] = LogisticsDistrict::getdistrict($addressList['district']);

        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$addressList
        ]);
    }
}