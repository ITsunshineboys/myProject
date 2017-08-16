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

   public  static function  effect_earnstsubmit($effect_id,$name,$phone,$out_trade_no)
    {
        $config=(new Alipayconfig())->alipayconfig();
        $str=$effect_id.'&'.$name.'&'.$phone;
        $total_amount=0.01;
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
        $result=$payResponse->wapPay($payRequestBuilder,'http://test.cdlhzz.cn:888/order/alipayeffect_earnstnotify','http://test.cdlhzz.cn:888/line/effect_earnstsuccess_pay');
    }

    
    public function Alipaylinenotify(){
        $config=(new AlipayConfig())->alipayconfig();
        $alipaySevice = new AlipayTradeService($config);
        return $alipaySevice;
    }


}