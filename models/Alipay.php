<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use vendor\alipay\AlipayTradeWapPayContentBuilder;
use vendor\alipay\Alipayconfig;
use app\services\AlipayTradeService;

class Alipay extends  ActiveRecord
{



    public function Alipay($out_trade_no,$subject,$total_amount,$body){
        $notify_url="http://test.cdlhzz.cn:888/order/alipaylinenotify";
        $return_url="http://test.cdlhzz.cn:888/line/success_pay";
        $config=(new AlipayConfig())->alipayconfig($notify_url,$return_url);
        //超时时间
        $timeout_express="1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payRequestBuilder->setGoods_type(0);
        $payResponse = new AlipayTradeService($config);
        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        return ;
//        }
    }

    public function  Alipaylinesubmit($out_trade_no,$subject,$total_amount,$body,$goods_id, $goods_num,$address_id,$pay_name,$invoice_id,$supplier_id,$freight,$return_insurance,$buyer_message){
        $notify_url='http://test.cdlhzz.cn:888/order/alipayeffect_earnstnotify';
        $return_url='http://test.cdlhzz.cn:888/line/effect_earnstsuccess_pay';
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
        return ;
    }

    public  static function  effect_earnstsubmit($effect_id,$name,$phone,$out_trade_no)
    {
        $notify_url='http://test.cdlhzz.cn:888/order/alipayeffect_earnstnotify';
        $return_url='http://test.cdlhzz.cn:888/line/effect_earnstsuccess_pay';
        $config=(new Alipayconfig())->alipayconfig($notify_url,$return_url);
        $str=$effect_id.'&'.$name.'&'.$phone;
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
    public function Alipaylinenotify(){
        $notify_url="http://test.cdlhzz.cn:888/order/alipaylinenotify";
        $return_url="http://test.cdlhzz.cn:888/line/success_pay";
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
        $notify_url='http://test.cdlhzz.cn:888/order/alipayeffect_earnstnotify';
        $return_url='http://test.cdlhzz.cn:888/line/effect_earnstsuccess_pay';
        $config=(new Alipayconfig())->alipayconfig($notify_url,$return_url);
        $str=Json::encode($orders);
        $passback_params=urlencode($str);
        //超时时间
        $timeout_express="1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody('此订单包含一条或多条商品数据');
        $payRequestBuilder->setSubject('艾特魔方商城订单');
        $payRequestBuilder->setOutTradeNo($orders[0]);
        $payRequestBuilder->setTotalAmount($orderAmount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payRequestBuilder->setPassback_params($passback_params);
        $payResponse = new AlipayTradeService($config);
//        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        $result=$payResponse->appPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        return $result;
    }

}