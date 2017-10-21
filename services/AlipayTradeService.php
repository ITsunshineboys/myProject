<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 9:34 AM
 */

namespace app\services;
use Yii;
use yii\base\Exception;
use vendor\alipay\AlipayTradeWapPayRequest;
use vendor\alipay\AopClient;
use vendor\alipay\AlipayTradeFastpayRefundQueryRequest;
use vendor\alipay\alipaydatadataservicebilldownloadurlqueryRequest;
use vendor\alipay\AlipayTradeCloseRequest;
use vendor\alipay\AlipayTradeRefundRequest;
use vendor\alipay\AlipayTradeQueryRequest;
use vendor\alipay\AlipayTradeAppPayRequest;
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../vendor/alipay/AopSdk.php';
require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../vendor/alipay/config.php';

class AlipayTradeService {

    //支付宝网关地址
    public $gateway_url = "https://openapi.alipay.com/gateway.do";
    //支付宝公钥
    public $alipay_public_key;
    //商户私钥
    public $private_key;
    //应用id
    public $appid;

    //编码格式
    public $charset = "UTF-8";

    public $token = NULL;

    //返回数据格式
    public $format = "json";

    //签名方式
    public $signtype = "RSA";

    function __construct($alipay_config){
        $this->gateway_url = $alipay_config['gatewayUrl'];
        $this->appid = $alipay_config['app_id'];
        $this->private_key = $alipay_config['merchant_private_key'];
        $this->alipay_public_key = $alipay_config['alipay_public_key'];
        $this->charset = $alipay_config['charset'];
        $this->signtype=$alipay_config['sign_type'];

        if(empty($this->appid)||trim($this->appid)==""){
            throw new Exception("appid should not be NULL!");
        }
        if(empty($this->private_key)||trim($this->private_key)==""){
            throw new Exception("private_key should not be NULL!");
        }
        if(empty($this->alipay_public_key)||trim($this->alipay_public_key)==""){
            throw new Exception("alipay_public_key should not be NULL!");
        }
        if(empty($this->charset)||trim($this->charset)==""){
            throw new Exception("charset should not be NULL!");
        }
        if(empty($this->gateway_url)||trim($this->gateway_url)==""){
            throw new Exception("gateway_url should not be NULL!");
        }

    }
    function AlipayWapPayService($alipay_config) {
        $this->__construct($alipay_config);
    }

        /**
     * alipay.trade.wap.pay
     * @param $builder 业务参数，使用buildmodel中的对象生成。
     * @param $return_url 同步跳转地址，公网可访问
     * @param $notify_url 异步通知地址，公网可以访问
     * @return $response 支付宝返回的信息
     */
    function appPay($builder,$return_url,$notify_url) {
        $aop=new AopClient();
        $aop->gatewayUrl = $this->gateway_url;
        $aop->appId = $this->appid;
        $aop->rsaPrivateKey ="MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDV8sawR4B7D3BE
RjUE7bbkYCAHAiI+gtf+RnwHFgxeKcgIBWT83Q/HxPblK1xKaAbl8ZQHyPQKDxHQ
gdgeLZRJX95xu8+BRg5cPQ7wuzPgVK+lnw6JQshZuE47r9Yk1Nn8hp1ILTgoYrIN
dzYkxFQUXK7mxA9QHMJ6KhjMp3AAfaoGHe2ejY31ZJnpZkD0gmf0V7HUgxrWnBzx
XZfUoS/as0qQD6lDS0haRpHUN6GN0weLq9It2+qAtJonQI12u21B9m0OpSlzME6+
zlagZOeZm9HYZEXIcxEQn97BzYLmeImHwNcCmZUp7lJ+haLTTLK6C5vkI/rcP5Fh
w/afb7LZAgMBAAECggEAJMgXNokkYoO19lbnmJBRqBOKjgnk30BZZ+KwQfudZrbi
J9bdlMdghiSrmZyJwlT+Epwkqe33Xp9WnbUk9ZM6ch7kPENgmkX0BBGPNv3IHmIQ
zHV650gu75XLBWHme/CIBW0TLyxvAiptl/kNuuZx2gpRG2VLiKdZG+2O0+hZTgv8
IoI7I5Vd4OuwNgsQcrkmBA66ra/nL71/PpUTUA/H+EVQHDOcWuhPWrOpyB5qgbEh
PYIE8Cv01VyMTZQQl9lXu16R6AIIQJMEBuoNEjKKEDZnj+5b1OBIyd1DKza4SuOY
Ok/Pf3XQHd1soBsh7ALExM0GkfKLiOCzgyP1yIJ6MQKBgQDx6HA2KhZ60+0VdkXF
g+YRxQJMamLuKnB7SgbcbE1D0lgYC80Ci2a6NsU7tj1qzJVZD2i95cekekc6HraN
sv7uto2YjMOUj+5JaaBHGRX80p4hoX1OV8O/6VQnRD6X+N5zc0LpNTq+GoWTXFAO
COrEFdBVkQxros6np8auuLINFQKBgQDiaWCb2lXp+d+lT4oIqDf7otqjrxgWr76u
/T2PMF0CgLRBgD67BPK0YNRTdrMDm194Nm8OsBOdUBSt2cU6umY7OmbHaCOxxnya
7kvk2joTaJLbt80u8Vo70Jz3k4JupuVhuJs+XZMzCwL9ZoPJelHrMpt9mx2ddgKu
+RkjiWVntQKBgHqleTbM8ebGSb6G32r7q4q40qiBi7TqiWrIpl/c/PN00rsmSIOQ
v72LhUX36H2cSeAEgvs8Y5LNeA1Mgwumg7/tiC6et447xvkrd1p0qoQrGIc+g/RT
ZOiI2OSrofyU7dukxXjNRwIZGj4TIHdtJ/HQiXbIen6b5aB03LO66nLdAoGAapP0
OClE/Aj1sBmK/pxBDiERDrAjhLph5nmq+rZncgAn2hWGcf7YSPSzvj5H8lC3Vh05
lg5ojUrfER9L6mNIMGVDcGajNtIaYcv+ydG4ON6nEuqomYpfUXzYBurXIUNLAv8l
hOwDmKl8VNBnBPahjTebXjsga7jbdoYnwUSyCWkCgYAwoFvVMB2jNE0u/UGqFIGI
L0GevRVNaVlch2DdC6PTBLT0h2IZeagZwnnrlHJRgujHF9DxC80AXz1PWADDPz+r
Lcle+izQ0XIx7A5LOg2oSgBz3DYaqlYCqdusSa2zOswHflVfQqtmA1MinlMAdWDq
HjgB2RoiyDxqCgSbpgAOUA==";
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey ="MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAitSci0DShwM4TpRrl8wRNw+SL26aoy3i/TU/6XuiKUXVX1NwVCxnmI8zriv4ZQEZamVy8K4rrI38slqAnKRNbDLfMJ1DnbKZR4z5LTrV4vn1nfHcUEfu8mRP30zN6nbH/8PbsU1+6uaU0l5hyemstC7oP0zv2Vo1ZLkMHDNipVnxT8nlCRUJGAQJDZF+5KD6VBhrrdf/+IfXgWBl0JTZG0r2BW1SPNmGstol3dY3yGjyVZXlHZBV0w27A36jF30Eg4Nz6FKl2DV9XrUMbjGJmvVJ8Ej+Tu6uyRvO7DRbrDE+huSYRbxAc3c18E6J9aWDtMSBeaX5MH0Ih7ioMtnN5wIDAQAB";
//        $biz_content=$builder->getBizContent();
        $biz_content=$builder->getBizContentApp();
        $request = new AlipayTradeAppPayRequest();
//SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent =$biz_content;
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        return  htmlspecialchars($response);

    }
    /**
     * alipay.trade.wap.pay
     * @param $builder 业务参数，使用buildmodel中的对象生成。
     * @param $return_url 同步跳转地址，公网可访问
     * @param $notify_url 异步通知地址，公网可以访问
     * @return $response 支付宝返回的信息
     */
    function wapPay($builder,$return_url,$notify_url) {

        $biz_content=$builder->getBizContent();
        //打印业务参数
        // $this->writeLog($biz_content);

        $request = new AlipayTradeWapPayRequest();

        $request->setNotifyUrl($notify_url);
        $request->setReturnUrl($return_url);
        $request->setBizContent ( $biz_content );
        // 首先调用支付api
        $response = $this->aopclientRequestExecute ($request,true);
        // $response = $response->alipay_trade_wap_pay_response;
        return $response;
    }

    function aopclientRequestExecute($request,$ispage=false) {

        $aop = new AopClient ();
        $aop->gatewayUrl = $this->gateway_url;
        $aop->appId = $this->appid;
        $aop->rsaPrivateKey =  $this->private_key;
        $aop->alipayrsaPublicKey = $this->alipay_public_key;
        $aop->apiVersion ="1.0";
        $aop->postCharset = $this->charset;
        $aop->format= $this->format;
        $aop->signType=$this->signtype;
        // 开启页面信息输出
        $aop->debugInfo=true;
        if($ispage)
        {
            $result = $aop->pageExecute($request,"post");
            echo $result;
        }
        else
        {
            $result = $aop->Execute($request);
        }

        //打开后，将报文写入log文件
        // $this->writeLog("response: ".var_export($result,true));
        return $result;
    }

    /**
     * alipay.trade.query (统一收单线下交易查询)
     * @param $builder 业务参数，使用buildmodel中的对象生成。
     * @return $response 支付宝返回的信息
     */
    function Query($builder){
        $biz_content=$builder->getBizContent();
        //打印业务参数
        // $this->writeLog($biz_content);
        $request = new AlipayTradeQueryRequest();
        $request->setBizContent ( $biz_content );
        // 首先调用支付api
        $response = $this->aopclientRequestExecute ($request);
        $response = $response->alipay_trade_query_response;
        var_dump($response);
        return $response;
    }

    /**
     * alipay.trade.refund (统一收单交易退款接口)
     * @param $builder 业务参数，使用buildmodel中的对象生成。
     * @return $response 支付宝返回的信息
     */
    function Refund($builder){
        $biz_content=$builder->getBizContent();
        //打印业务参数
        // $this->writeLog($biz_content);
        $request = new AlipayTradeRefundRequest();
        $request->setBizContent ( $biz_content );

        // 首先调用支付api
        $response = $this->aopclientRequestExecute ($request);
        $response = $response->alipay_trade_refund_response;
        var_dump($response);
        return $response;
    }

    /**
     * alipay.trade.close (统一收单交易关闭接口)
     * @param $builder 业务参数，使用buildmodel中的对象生成。
     * @return $response 支付宝返回的信息
     */
    function Close($builder){
        $biz_content=$builder->getBizContent();
        //打印业务参数
        // $this->writeLog($biz_content);
        $request = new AlipayTradeCloseRequest();
        $request->setBizContent ( $biz_content );

        // 首先调用支付api
        $response = $this->aopclientRequestExecute ($request);
        $response = $response->alipay_trade_close_response;
        var_dump($response);
        return $response;
    }

    /**
     * 退款查询   alipay.trade.fastpay.refund.query (统一收单交易退款查询)
     * @param $builder 业务参数，使用buildmodel中的对象生成。
     * @return $response 支付宝返回的信息
     */
    function refundQuery($builder){
        $biz_content=$builder->getBizContent();
        //打印业务参数
        // $this->writeLog($biz_content);
        $request = new AlipayTradeFastpayRefundQueryRequest();
        $request->setBizContent ( $biz_content );

        // 首先调用支付api
        $response = $this->aopclientRequestExecute ($request);
        var_dump($response);
        return $response;
    }
    /**
     * alipay.data.dataservice.bill.downloadurl.query (查询对账单下载地址)
     * @param $builder 业务参数，使用buildmodel中的对象生成。
     * @return $response 支付宝返回的信息
     */
    function downloadurlQuery($builder){
        $biz_content=$builder->getBizContent();
        //打印业务参数
        // $this->writeLog($biz_content);
        $request = new alipaydatadataservicebilldownloadurlqueryRequest();
        $request->setBizContent ( $biz_content );

        // 首先调用支付api
        $response = $this->aopclientRequestExecute ($request);
        $response = $response->alipay_data_dataservice_bill_downloadurl_query_response;
        var_dump($response);
        return $response;
    }

    /**
     * 验签方法
     * @param $arr 验签支付宝返回的信息，使用支付宝公钥。
     * @return boolean
     */
    function check($arr){
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = $this->alipay_public_key;
        $result = $aop->rsaCheckV1($arr, $this->alipay_public_key, $this->signtype);
        return $result;
    }

    // //请确保项目文件有可写权限，不然打印不了日志。
    // function writeLog($text) {
    //     // $text=iconv("GBK", "UTF-8//IGNORE", $text);
    //     //$text = characet ( $text );
    //     file_put_contents ( dirname ( __FILE__ ).DIRECTORY_SEPARATOR."./../../log.txt", date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
    // }


    /** *利用google api生成二维码图片
     * $content：二维码内容参数
     * $size：生成二维码的尺寸，宽度和高度的值
     * $lev：可选参数，纠错等级
     * $margin：生成的二维码离边框的距离
     */
    function create_erweima($content, $size = '200', $lev = 'L', $margin= '0') {
        $content = urlencode($content);
        $image = '<img src="http://chart.apis.google.com/chart?chs='.$size.'x'.$size.'&amp;cht=qr&chld='.$lev.'|'.$margin.'&amp;chl='.$content.'"  widht="'.$size.'" height="'.$size.'" />';
        return $image;
    }
}

?>