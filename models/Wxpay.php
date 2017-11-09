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


    const  EFFECT_NOTIFY_URL='http://test.cdlhzz.cn/order/wxpayeffect_earnstnotify';
    const  LINEPAY_NOTIFY_URL='http://test.cdlhzz.cn/order/orderlinewxpaynotify';
    const  EFFECT_BODY='样板间申请费';
    const  NO_LOGIN_CACHE_FREFIX='no_login_cachce_prefix_';
    const  ACCESS_TOKEN='access_token';
    const  TICKET='ticket';
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
        $input->SetTotal_fee(1);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("goods");
        $input->SetNotify_url(self::LINEPAY_NOTIFY_URL);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
//        return $jsApiParameters;
        echo "<script type='text/javascript'>if (typeof WeixinJSBridge == 'undefined'){if( document.addEventListener ){document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);}else if (document.attachEvent){document.attachEvent('WeixinJSBridgeReady', jsApiCall);document.attachEvent('onWeixinJSBridgeReady', jsApiCall);}}else{jsApiCall();}//调用微信JS api 支付
 function jsApiCall(){ WeixinJSBridge.invoke('getBrandWCPayRequest',".$jsApiParameters.",function(res){if(res.err_msg == 'get_brand_wcpay_request:cancel'){window.location.href='http://test.cdlhzz.cn/line/#!/order_commodity';};if(res.err_msg == 'get_brand_wcpay_request:ok'){window.location.href='http://test.cdlhzz.cn/line/#!/pay_success';};if(res.err_msg == 'get_brand_wcpay_request:fail'){window.location.href='http://test.cdlhzz.cn/line/#!/order_commodity';};});}
</script>";
        exit;
    }
        /**
         * 样板间申请支付定金
         * @param $effect_id
         * @param $name
         * @param $phone
         * @param $money
         * @return \app\services\json数据，可直接填入js函数作为参数1
         */
       public static  function effect_earnstsubmit($id,$openId)
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
            $input = new WxPayUnifiedOrder();
            $attach=$id;
            $total_amount=0.01;
            $input->SetBody(self::EFFECT_BODY);
            $input->SetAttach($attach);
            $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
            $input->SetTotal_fee($total_amount*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("goods");
            $input->SetNotify_url("http://".$_SERVER['SERVER_NAME']."/order/orderlinewxpaynotify");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            return  $jsApiParameters;
        }


   public  function WxBuy(){
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
        $openId=$tools->GetOpenid();
       var_dump($openId);exit;
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $attach='123';
        $input->SetBody('test');
        $input->SetAttach($attach);
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee(1);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("goods");
        $input->SetNotify_url(self::LINEPAY_NOTIFY_URL);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        var_dump($jsApiParameters);exit;
//        return $jsApiParameters;
        echo "<script type='text/javascript'>if (typeof WeixinJSBridge == 'undefined'){if( document.addEventListener ){document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);}else if (document.attachEvent){document.attachEvent('WeixinJSBridgeReady', jsApiCall);document.attachEvent('onWeixinJSBridgeReady', jsApiCall);}}else{jsApiCall();}//调用微信JS api 支付
 function jsApiCall(){ WeixinJSBridge.invoke('getBrandWCPayRequest',".$jsApiParameters.",function(res){if(res.err_msg == 'get_brand_wcpay_request:cancel'){window.location.href='http://test.cdlhzz.cn/line/#!/order_commodity';};if(res.err_msg == 'get_brand_wcpay_request:ok'){window.location.href='http://test.cdlhzz.cn/line/#!/pay_success';};if(res.err_msg == 'get_brand_wcpay_request:fail'){window.location.href='http://test.cdlhzz.cn/line/#!/order_commodity';};});}
</script>";
        exit;
    }

       public static function Wxpay(){
            ini_set('date.timezone','Asia/Shanghai');
            //打印输出数组信息
            function printf_info($data)
            {
                foreach($data as $key=>$value){
                    echo "<font color='#00ff55;'>$key</font> : $value <br/>";
                }
            }
            //①、获取用户openid
            $tools = new PayService();
            $openId = $tools->GetOpenid();

            //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody("test");
            $input->SetAttach("test");
            $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
            $input->SetTotal_fee("1");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url("http://test.cdlhzz.cn/example/notify.php");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            echo "<script type='text/javascript'>if (typeof WeixinJSBridge == 'undefined'){if( document.addEventListener ){document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);}else if (document.attachEvent){document.attachEvent('WeixinJSBridgeReady', jsApiCall);document.attachEvent('onWeixinJSBridgeReady', jsApiCall);}}else{jsApiCall();}//调用微信JS api 支付
 function jsApiCall(){ WeixinJSBridge.invoke('getBrandWCPayRequest',".$jsApiParameters.",function(res){WeixinJSBridge.log(res.err_msg);alert(res.err_code+res.err_desc+res.err_msg);});}
</script>";
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


       /**
         * @return array
         */
        public  static  function  GetWxJsSign()
        {
            $cache = Yii::$app->cache;
            $data = $cache->get(self::ACCESS_TOKEN);
            if ($data)
            {
                $access_token=$data;
            }else{
                $sendUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx9814aafe9b6b847f&secret=4560eeb7b386701ddc7085827f65e40e';
                $content =self::curl($sendUrl,false,0); //请求发送短信
                if($content){
                    $result = json_decode($content,true);
                    $access_token=$result['access_token'];
                    $data = $cache->set(self::ACCESS_TOKEN,$access_token,7200);
                }else{
                    //返回内容异常，以下可根据业务逻辑自行修改
                    echo "请求发送短信失败";
                }
            }
            $ticket=$cache->get(self::TICKET);
            if (!$ticket)
            {
                 $sendUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
                $content =self::curl($sendUrl,false,0); //请求发送短信
                if($content){
                    $result = json_decode($content,true);
                    if ($result['expires_in']==7200)
                    {
                        $ticket=$result['ticket'];
                    }else{
                        $ticket=$result['ticket'];
                    }
                     $data = $cache->set(self::TICKET,$ticket,7200);
                }
            }
            $noncestr=WxPayApi::getNonceStr();
            $timestamp=time();
            // $url='https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115';
           $url=$_SERVER['HTTP_REFERER'];
            $appid=WxPayConfig::APPID;
            $str="jsapi_ticket=".$ticket."&noncestr=".$noncestr.'&timestamp='.$timestamp.'&url='.$url;
            $sign=sha1($str);
            return [
                'appId'=>$appid,
                'timestamp'=>$timestamp,
                'nonceStr'=>$noncestr,
                'signature'=>$sign
            ];
        }

    public static   function curl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }
}