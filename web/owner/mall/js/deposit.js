app.controller('depositCtrl', ['$rootScope', '$scope', '$state', '_ajax', function ($rootScope, $scope, $state, _ajax) {
    let payParams = JSON.parse(sessionStorage.getItem('payParams'));
    console.log(payParams, '请求参数');
    $scope.isShow = false;
    $scope.isPay = false;
    $scope.user = {
        name: '',
        phone: ''
    };
    $scope.$watch('user.name', function (n, o) {
        $scope.isPay = n !== "" && $scope.user.phone !== "";
    });
    $scope.$watch('user.phone', function (n, o) {
        $scope.isPay = n !== "" && $scope.user.name !== "";
    });
    $scope.payMoney = function () {
        let reg = /(13|14|15|17|18)[0-9]{9}/;
        if (!reg.test($scope.user.phone)) {
            $scope.isShow = true;
            return false;
        }
        payParams.name = $scope.user.name;
        payParams.phone = $scope.user.phone;
        // $state.go('pay_success');
        if ($rootScope.isWxOpen) {
            payParams.wxpayCode = sessionStorage.getItem('openId');
            _ajax.post('/order/wxpay-effect-earnst-sub', payParams, function (res) {
                let data = res.data;
                function onBridgeReady(){
                    WeixinJSBridge.invoke(
                        'getBrandWCPayRequest', {
                            "appId": data.appId,     //公众号名称，由商户传入
                            "timeStamp": data.timestamp,         //时间戳，自1970年以来的秒数
                            "nonceStr": data.nonceStr, //随机串
                            "package": data.package,
                            "signType": data.signType,         //微信签名方式：
                            "paySign": data.paySign //微信签名
                        },
                        function(res){
                            if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                                $state.go('pay_success');
                            }     // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。
                        }
                    );
                }

                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                    }
                }else{
                    onBridgeReady();
                }
            })
        } else {
            _ajax.post('/order/effect-earnst-alipay-sub', payParams, function (res) {
                angular.element('body').append(res);
            })
        }
    }
}]);