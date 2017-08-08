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
        $out_trade_no='0807145952';
            $config=(new AlipayConfig())->alipayconfig();
            //超时时间
            $timeout_express="1m";
            $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);
              $payRequestBuilder->setGoods_type(1);
        $payRequestBuilder->setGoods_id(1);
        $payRequestBuilder->setGoods_num(1);
        $payRequestBuilder->setDstrictcode(111);
        $payRequestBuilder->setPay_name('1');
        $payRequestBuilder->setInvoice_id(1);
            $payResponse = new AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
            return ;
    }


   public function  Alipaylinesubmit($out_trade_no,$subject,$total_amount,$body,$goods_id, $goods_num,$address_id,$pay_name,$invoice_id){

        $config=(new AlipayConfig())->alipayconfig();

          $str=$goods_id.'&'.$goods_num.'&'.$address_id.'&'.$pay_name.'&'.$invoice_id;
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

       public function Alipaylinenotify(){
        $config=(new AlipayConfig())->alipayconfig();
        $alipaySevice = new AlipayTradeService($config);
        return $alipaySevice;
        }




}