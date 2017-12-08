<?php

namespace app\models;
use app\services\ModelService;
use Yii;
use yii\db\ActiveRecord;
use vendor\alipay\AlipayTradeWapPayContentBuilder;
use vendor\alipay\Alipayconfig;
use app\services\AlipayTradeService;
use yii\helpers\Json;

class Alipay extends  ActiveRecord
{

    const ALIPAY_LINPAY_NOTIFY='order/alipaylinenotify';
    const LINE_PAY_SUCCESS='line/#!/pay_success';
    const EFFECT_NOTIFY='order/alipayeffect_earnstnotify';
    const EFFECT_SUCCESS='owner/mall/index.html#!/pay_success';


    /**
     *  支付宝线下支付
     * @param $out_trade_no
     * @param $subject
     * @param $total_amount
     * @param $body
     * @param $goods_id
     * @param $goods_num
     * @param $address_id
     * @param $pay_name
     * @param $invoice_id
     * @param $supplier_id
     * @param $freight
     * @param $return_insurance
     * @param $buyer_message
     * @return bool|mixed|\SimpleXMLElement|string|\vendor\alipay\提交表单HTML文本
     */
    public static function  Alipaylinesubmit($out_trade_no,$subject,$total_amount,$body,$goods_id, $goods_num,$address_id,$pay_name,$invoice_id,$supplier_id,$freight,$return_insurance,$buyer_message){
//        $notify_url=Yii::$app->request->hostInfo.'/'.self::ALIPAY_LINPAY_NOTIFY;
        $notify_url="https://".$_SERVER["SERVER_NAME"].'/'.self::ALIPAY_LINPAY_NOTIFY;
        $return_url=Yii::$app->request->hostInfo.'/'.self::LINE_PAY_SUCCESS;
        $config=(new Alipayconfig())->alipayconfig($notify_url,$return_url);
        $str=$goods_id.'&'.$goods_num.'&'.$address_id.'&'.$pay_name.'&'.$invoice_id.'&'.$supplier_id.'&'.$freight.'&'.$return_insurance.'&'.$buyer_message;
        $passback_params=urlencode($str);
        //超时时间
        $timeout_express="1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payRequestBuilder->setPassback_params($passback_params);
        $payResponse = new AlipayTradeService($config);
        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        return $result;
    }

    /**
     * 样板间提交定金
     * @param $post
     * @param $phone
     * @param $out_trade_no
     * @return bool
     */
    public  static function  EffectEarnestSubmit($post,$phone,$out_trade_no)
    {
//        $notify_url=Yii::$app->request->hostInfo."/".self::EFFECT_NOTIFY;
        $notify_url="https://".$_SERVER["SERVER_NAME"].'/'.self::EFFECT_NOTIFY;
        $return_url=Yii::$app->request->hostInfo."/".self::EFFECT_SUCCESS;
        $config=(new Alipayconfig())->alipayconfig($notify_url,$return_url);
        $id=Effect::addneweffect($post);
        if (!$id)
        {
            return false;
        }
        $str=$id;
        $total_amount=0.01;
        $passback_params=urlencode($str);
        //超时时间
        $timeout_express="1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody('此笔订单为样板间定金');
        $payRequestBuilder->setSubject('样板间申请费');
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payRequestBuilder->setPassback_params($passback_params);
        $payResponse = new AlipayTradeService($config);
        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
    }

    /**
     * @return AlipayTradeService
     */
    public function Alipaylinenotify(){
        $notify_url=Yii::$app->request->hostInfo."/order/alipaylinenotify";
        $return_url=Yii::$app->request->hostInfo."/line/success_pay";
        $config=(new AlipayConfig())->alipayconfig($notify_url,$return_url);
        $alipaySevice = new AlipayTradeService($config);
        return $alipaySevice;
    }


   /**
     * 去付款-支付宝支付
     * @param $orderAmount
     * @param array $orders
     * @return string
     */
    public static  function OrderAppPay($orderAmount,$orders=[])
    {
        $time=time();
        $out_trade_no=date('Y',$time).date('m',$time).date('d',$time).date('H',$time).date('i',$time).date('s',$time);
//        $notify_url=Yii::$app->request->hostInfo."/order/app-order-pay-database";
        $notify_url= "https://".$_SERVER["SERVER_NAME"]."/order/app-order-pay-database";
        $return_url='';
        $config=(new Alipayconfig())->alipayconfig($notify_url,$return_url);
        $passback_params=urlencode($orders);
        //超时时间
        $timeout_express="1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody('此订单包含一条或多条商品数据');
        $payRequestBuilder->setSubject('艾特魔方商城订单');
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount(0.01);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payRequestBuilder->setPassback_params($passback_params);
        $payResponse = new AlipayTradeService($config);
//        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        $result=$payResponse->appPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        return $result;
    }


        /**
     * app充值
     * @param $money
     * @param $user
     * @return string
     */
    public  static  function  UserRecharge($money,$user)
    {
        $time=time();
        $out_trade_no=GoodsOrder::SetTransactionNo($user->aite_cube_no);
//        $notify_url=Yii::$app->request->hostInfo."/withdrawals/ali-pay-user-recharge-database";
        $return_url='';
        $notify_url="https://".$_SERVER["SERVER_NAME"]."/withdrawals/ali-pay-user-recharge-database";
        $config=(new Alipayconfig())->alipayconfig($notify_url,$return_url);
        $passback_params=base64_encode($user->last_role_id_app.','.$user->id);
        //超时时间
        $timeout_express="1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody('此订单包含一条或多条商品数据');
        $payRequestBuilder->setSubject('艾特魔方商城充值');
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($money);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payRequestBuilder->setPassback_params($passback_params);
        $payResponse = new AlipayTradeService($config);
        $result=$payResponse->appPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        return $result;
    }

}