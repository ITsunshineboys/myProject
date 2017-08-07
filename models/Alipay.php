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
            $payResponse = new AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
            return ;
    }

     public function  Alipaylinesubmit($out_trade_no,$subject,$total_amount,$body,$goods_id, $goods_num,$districtcode,$pay_name,$invoice_id){

        $config=(new AlipayConfig())->alipayconfig();

        //超时时间
        $timeout_express="1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo(201787165140215);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        // $payRequestBuilder->setGoods_type(0);
        // $payRequestBuilder->setGoods_id($goods_id);
        // $payRequestBuilder->setGoods_num($goods_num);
        // $payRequestBuilder->setDstrictcode($districtcode);
        // $payRequestBuilder->setPay_name($pay_name);
        // $payRequestBuilder->setInvoice_id($invoice_id);
        $payResponse = new AlipayTradeService($config);
        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        return ;

    }



}