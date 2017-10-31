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

   /**
     * 样板间提交定金
     * @param $effect_id
     * @param $name
     * @param $phone
     * @param $out_trade_no
     */
    public  static function  effect_earnstsubmit($post,$phone,$out_trade_no)
    {
        $notify_url='http://test.cdlhzz.cn:888/order/alipayeffect_earnstnotify';
        $return_url='http://test.cdlhzz.cn:888/line/effect_earnstsuccess_pay';
        $config=(new Alipayconfig())->alipayconfig($notify_url,$return_url);
        $str=$post['province_code'].'&'.$post['city_code'].'&'.$post['district_code'].'&'.$post['bedroom'].'&'.$post['toilet'].'&'.$post['kitchen'].'&'.$post['sittingRoom_diningRoom'].'&'.$post['window'].'&'.$post['high'].'&'.$post['street'].'&'.$post['series'].'&'.$post['style'].'&'.$post['stairway'].'&'.$post['stair_id'].'&'.$post['toponymy'].'&'.$post['particulars'].'&'.$post['area'].'&'.$post['phone'].'&'.$post['name'].'&'.$post['requirement'];
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
        $time=time();
        $out_trade_no=date('Y',$time).date('m',$time).date('d',$time).date('H',$time).date('i',$time).date('s',$time);
        $notify_url='http://test.cdlhzz.cn:888/order/app-order-pay-database';
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
        $notify_url='http://test.cdlhzz.cn:888/withdrawals/ali-pay-user-recharge-database';
        $return_url='';
        $config=(new Alipayconfig())->alipayconfig($notify_url,$return_url);
        $passback_params=urlencode($user->last_role_id_app.','.$user->id);
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