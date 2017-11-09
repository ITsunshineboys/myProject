app.controller("indexCtrl", ["$rootScope", "$scope", "_ajax", function ($rootScope, $scope, _ajax) {
    // 微信事宜
    $rootScope.isWxOpen = false;
    _ajax.get(baseUrl + '/order/iswxlogin', "", function (res) {
        if (res.code === 200) { // 是微信浏览器打开
            let data = res.data;
            let wxSharConfig = {
                appId: data.appId,
                timestamp: data.timestamp,
                nonceStr: data.nonceStr,
                signature: data.signature
            };
            sessionStorage.setItem('wxSharConfig', JSON.stringify(wxSharConfig));

            $rootScope.isWxOpen = true;
            if (getUrlParams('code') === "" && sessionStorage.getItem("code") === null) {
                let url = location.href;
                _ajax.post(baseUrl + '/order/find-open-id', {url: url}, function (res) {
                    location.href = res.data
                })
            } else {
                let code = getUrlParams('code');
                sessionStorage.setItem('code', code);
                _ajax.post(baseUrl + '/order/get-open-id', {code: code}, function (res) {
                    console.log(res);
                })
            }
        }
    });
}]);