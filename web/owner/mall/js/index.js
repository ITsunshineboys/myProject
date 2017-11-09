app.controller("indexCtrl", ["$rootScope", "$scope", "_ajax", function ($rootScope, $scope, _ajax) {
    sessionStorage.removeItem("huxingParams");
    sessionStorage.removeItem("backman");
    sessionStorage.removeItem("roomPicture");
    sessionStorage.removeItem("worker");
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

            if (sessionStorage.getItem("openId") === null) {
                if (getUrlParams('code') === "") {
                    let url = location.href;
                    _ajax.post(baseUrl + '/order/find-open-id', {url: url}, function (res) {
                        location.href = res.data
                    })
                } else {
                    let code = getUrlParams('code');
                    _ajax.post(baseUrl + '/order/get-open-id', {code: code}, function (res) {
                        let openId = res.data.openid;
                        sessionStorage.setItem('openId', openId);
                    })
                }
            }
        }
    });
}]);