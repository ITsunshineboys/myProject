app.controller("indexCtrl", ["$rootScope", "$scope", "_ajax", function ($rootScope, $scope, _ajax) {
    // 微信事宜
    $rootScope.isWxOpen = false;
    _ajax.get('/order/iswxlogin', "", function (res) {
        if (res.code === 200) { // 是微信浏览器打开
            let data = res.data;
            let wxConfig = {
                appId: data.appId,
                timestamp: data.timestamp,
                nonceStr: data.nonceStr,
                signature: data.signature
            };
            sessionStorage.setItem('wxConfig', JSON.stringify(wxConfig));
            wx.config({
                debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: data.appId, // 必填，公众号的唯一标识
                timestamp: data.timestamp, // 必填，生成签名的时间戳
                nonceStr: data.nonceStr, // 必填，生成签名的随机串
                signature: data.signature,// 必填，签名，见附录1
                jsApiList: ["onMenuShareTimeline", "onMenuShareAppMessage", "onMenuShareQQ", "onMenuShareWeibo", "onMenuShareQZone", "chooseWXPay"] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            });
            wx.ready(function () {
                alert(location.host);
                alert(location.pathname);
                alert(location.hash);
                // 分享到朋友圈
                wx.onMenuShareTimeline({
                    title: '', // 分享标题
                    link: window.location.host + window.location.pathname + window.location.hash, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                    imgUrl: '', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                // 分享给朋友
                wx.onMenuShareAppMessage({
                    title: '', // 分享标题
                    desc: '', // 分享描述
                    link: window.location.host + window.location.pathname + window.location.hash, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                    imgUrl: '', // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        alert(window.location.host + window.location.pathname + window.location.hash)
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                // 分享到QQ
                wx.onMenuShareQQ({
                    title: '', // 分享标题
                    desc: '', // 分享描述
                    link: '', // 分享链接
                    imgUrl: '', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                // 腾讯微博
                wx.onMenuShareWeibo({
                    title: '', // 分享标题
                    desc: '', // 分享描述
                    link: '', // 分享链接
                    imgUrl: '', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                // 分享到QQ空间
                wx.onMenuShareQZone({
                    title: '', // 分享标题
                    desc: '', // 分享描述
                    link: '', // 分享链接
                    imgUrl: '', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
            $rootScope.isWxOpen = true;
            if (sessionStorage.getItem("openId") === null) {
                if (getUrlParams('code') === "") {
                    let url = location.href;
                    _ajax.post('/order/find-open-id', {url: url}, function (res) {
                        location.href = res.data
                    })
                } else {
                    let code = getUrlParams('code');
                    _ajax.post('/order/get-open-id', {code: code}, function (res) {
                        let openId = res.data;
                        sessionStorage.setItem('openId', openId);
                    })
                }
            }
        }
    });
}]);