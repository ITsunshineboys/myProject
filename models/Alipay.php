<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use vendor\Alipay\AlipayTradeWapPayContentBuilder;
use vendor\Alipay\AlipayConfig;
use app\services\AlipayTradeService;

class Alipay extends  ActiveRecord
{



    public function Alipay($out_trade_no,$subject,$total_amount,$body){

        $config=(new AlipayConfig())->alipayconfig();
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

    public function  Alipaylinesubmit($out_trade_no,$subject,$total_amount,$body,$goods_id, $goods_num,$address_id,$pay_name,$invoice_id,$supplier_id,$freight,$return_insurance){
        $config=(new AlipayConfig())->alipayconfig();
        $str=$goods_id.'&'.$goods_num.'&'.$address_id.'&'.$pay_name.'&'.$invoice_id.'&'.$supplier_id.'&'.$freight.'&'.$return_insurance;
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
        $config=(new AlipayConfig())->alipayconfig();
        $str=$effect_id.'&'.$name.'&'.$phone;
        $total_amount=89;
        $passback_params=urlencode($str);
        $notify_url='http://test.cdlhzz.cn:888/order/alipayeffect_earnstnotify';
        $return_url='http://test.cdlhzz.cn:888/line/effect_earnstsuccess_pay';
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
        $result=$payResponse->wapPay($payRequestBuilder,$notify_url,$return_url);
    }
    public function Alipaylinenotify(){
        $config=(new AlipayConfig())->alipayconfig();
        $alipaySevice = new AlipayTradeService($config);
        return $alipaySevice;
    }


}