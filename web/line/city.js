//重置节点个数

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
function jsApiCall(){
    WeixinJSBridge.invoke('getBrandWCPayRequest',".$jsApiParameters.",function(res){
            WeixinJSBridge.log(res.err_msg);
            alert(res.err_code+res.err_desc+res.err_msg);
        });
}
