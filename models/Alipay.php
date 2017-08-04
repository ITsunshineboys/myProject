<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use Flc\Alipay\AlipayTradeWapPayContentBuilder;
use Flc\Alipay\AlipayConfig;
use app\services\AlipayTradeService;
class Alipay extends  ActiveRecord
{

//    /**
//     *支付宝网页支付接口
//     */
//    public function Aliapipay(){
//
//        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'service/AlipayTradeService.php';
//        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'buildermodel/AlipayTradeWapPayContentBuilder.php';
//        require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../config.php';
//        if (!empty($_POST['WIDout_trade_no'])&& trim($_POST['WIDout_trade_no'])!=""){
//            //商户订单号，商户网站订单系统中唯一订单号，必填
//            $out_trade_no = $_POST['WIDout_trade_no'];
//
//            //订单名称，必填
//            $subject = $_POST['WIDsubject'];
//
//            //付款金额，必填
//            $total_amount = $_POST['WIDtotal_amount'];
//
//            //商品描述，可空
//            $body = $_POST['WIDbody'];
//
//            //超时时间
//            $timeout_express="1m";
//
//            $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
//            $payRequestBuilder->setBody($body);
//            $payRequestBuilder->setSubject($subject);
//            $payRequestBuilder->setOutTradeNo($out_trade_no);
//            $payRequestBuilder->setTotalAmount($total_amount);
//            $payRequestBuilder->setTimeExpress($timeout_express);
//
//            $payResponse = new AlipayTradeService($config);
//            $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
//
//            return ;
//        }
//    }

    public function Alipay($out_trade_no,$subject,$total_amount,$body){
//        if (!empty($_POST['WIDout_trade_no'])&& trim($_POST['WIDout_trade_no'])!=""){
            $config=Alipayconfig::alipayconfig();
            //商户订单号，商户网站订单系统中唯一订单号，必填
//            $out_trade_no = $_POST['WIDout_trade_no'];
//
//            //订单名称，必填
//            $subject = $_POST['WIDsubject'];
//
//            //付款金额，必填
//            $total_amount = $_POST['WIDtotal_amount'];
//
//            //商品描述，可空
//            $body = $_POST['WIDbody'];
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
//        }
    }


}