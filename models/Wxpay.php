<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\base\WxPayException;
use vendor\wxpay\lib\WxPayJsApiPay;
use vendor\wxpay\lib\WxPayConfig;
use vendor\wxpay\lib\WxPayUnifiedOrder;
use vendor\wxpay\lib\WxPayApi;
use vendor\wxpay\lib\WxPayOrderQuery;
use vendor\wxpay\lib\log;
use vendor\wxpay\lib\CLogFileHandler;
use app\services\PayService;
use yii\db\ActiveRecord;


class Wxpay  extends ActiveRecord
{


    const  EFFECT_NOTIFY_URL='http://common.cdlhzz.cn/order/wxpayeffect_earnstnotify';
    const  LINEPAY_NOTIFY_URL='http://common.cdlhzz.cn/order/orderlinewxpaynotify';
    const  EFFECT_BODY='样板间申请费';
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_order';
    }
  /**
     *无登录-微信公众号支付接口
     */
    public function Wxlineapipay($orders,$openid){
        ini_set('date.timezone','Asia/Shanghai');
        //打印输出数组信息
        function printf_info($data)
        {
            foreach($data as $key=>$value){
                echo "<font color='#00ff55;'>$key</font> : $value <br/>";
            }
        }
        //、获取用户openid
        $tools = new PayService();
        $openId = $openid;
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $attach=$orders['goods_id'].'&'.$orders['goods_num'].'&'.$orders['address_id'].'&'.$orders['pay_name'].'&'.$orders['invoice_id'].'&'.$orders['supplier_id'].'&'.$orders['freight'].'&'.$orders['return_insurance'].'&'.$orders['order_no'].'&'.$orders['buyer_message'];
        $input->SetBody($orders['body']);
        $input->SetAttach($attach);
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($orders['order_price']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("goods");
        $input->SetNotify_url(self::LINEPAY_NOTIFY_URL);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        return $jsApiParameters;
    }
        /**
         * 样板间申请支付定金
         * @param $effect_id
         * @param $name
         * @param $phone
         * @param $money
         * @return \app\services\json数据，可直接填入js函数作为参数1
         */
        public static  function effect_earnstsubmit($effect_id,$name,$phone,$money)
        {
            ini_set('date.timezone','Asia/Shanghai');
            //打印输出数组信息
            function printf_info($data)
            {
                foreach($data as $key=>$value){
                    echo "<font color='#00ff55;'>$key</font> : $value <br/>";
                }
            }
            //、获取用户openid
            $tools = new PayService();
            $openId = $tools->GetOpenid();
            $input = new WxPayUnifiedOrder();
            $attach=$effect_id.'&'.$name.'&'.$phone;
            $input->SetBody('样板间申请费');
            $input->SetAttach($attach);
            $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
            $input->SetTotal_fee($money*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("goods");
            $input->SetNotify_url(self::EFFECT_NOTIFY_URL);
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $editAddress = $tools->GetEditAddressParameters();
            return $jsApiParameters;
        }


        public function Wxpay(){
            ini_set('date.timezone','Asia/Shanghai');
            //打印输出数组信息
            function printf_info($data)
            {
                foreach($data as $key=>$value){
                    echo "<font color='#00ff55;'>$key</font> : $value <br/>";
                }
            }
            //、获取用户openid
            $tools = new PayService();
            $openId = $tools->GetOpenid();
            //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody("test");
            $input->SetAttach("1");
            $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
            $input->SetTotal_fee("1");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url('http://common.cdlhzz.cn/order/orderlinewxpaynotify');
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $editAddress = $tools->GetEditAddressParameters();
            echo $jsApiParameters;
        }

        private static function Queryorder($transaction_id)
        {
            $input = new WxPayOrderQuery();
            $input->SetTransaction_id($transaction_id);
            $result = WxPayApi::orderQuery($input);
            if(array_key_exists("return_code", $result)
                && array_key_exists("result_code", $result)
                && $result["return_code"] == "SUCCESS"
                && $result["result_code"] == "SUCCESS")
            {
                return true;
            }
            return false;
        }

    //重写回调处理函数
    public static function NotifyProcess($data)
    {

        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            return false;
        }
        //查询订单，判断订单真实性
        if(!self::Queryorder($data["transaction_id"])){
            return false;
        }
        return true;
    }
}