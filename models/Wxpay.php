<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\base\WxPayException;
use vendor\wxpay\lib\WxPayJsApiPay;
use vendor\wxpay\lib\WxPayConfig;
use vendor\wxpay\lib\WxPayUnifiedOrder;
use vendor\wxpay\lib\WxPayApi;
use vendor\wxpay\lib\log;
use vendor\wxpay\lib\CLogFileHandler;
use app\services\PayService;
use yii\db\ActiveRecord;

class Wxpay  extends ActiveRecord
{


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
    public function Wxlineapipay($orders,$local){
            ini_set('date.timezone','Asia/Shanghai');

            //打印输出数组信息
            function printf_info($data)
            {
                foreach($data as $key=>$value){
                    echo "<font color='#00ff55;'>$key</font> : $value <br/>";
                }
            }
            //、获取用户openid
            $tools = new JsApiPay();
            $openId = $tools->GetOpenid();
            $money=$orders['goods_price']*$orders['goods_num']*100;
            $attach=$orders['invoice_id']."+".$orders['goods_id']."+".$orders['goods_num']."+".$orders['goods_attr']."+".$orders['paymentmethod'];
            //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody("test");
            $input->SetAttach("$attach");
            $input->SetOut_trade_no(self::MCHID.date("YmdHis"));
            $input->SetTotal_fee("$money");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $url=$local."xxxx/notify.php";
            $input->SetNotify_url($url);
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $editAddress = $tools->GetEditAddressParameters();
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
            // //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody("test");
            $input->SetAttach("1");
            $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
            $input->SetTotal_fee("1");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url('http://www.cdlhzz.cn/');
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $editAddress = $tools->GetEditAddressParameters();
            echo "
 <script type='text/javascript'>
   
        if (typeof WeixinJSBridge == 'undefined'){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
           
    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
           ".$jsApiParameters.",
            function(res){
                WeixinJSBridge.log(res.err_msg);
                alert(res.err_code+res.err_desc+res.err_msg);
            }
        );
    }
</script>
    ";
            // return $openId;
        }
}