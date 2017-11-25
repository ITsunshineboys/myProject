// const baseUrl = "http://ac.cdlhzz.cn";
const baseUrl = "";

/**
 * 获取 url 地址中的参数
 * @param name  参数名称
 * @returns {string}
 */
function getUrlParams(name) {
    let reg = new RegExp("(\\?|\\&)" + name + "=([^\\&]+)", "i");
    let params = location.href.match(reg);
    let context = "";
    if (params !== null) {
        context = params[2];
    }
    return context;
}

function wxConfig (url) {
    // 分享到朋友圈
    wx.onMenuShareTimeline({
        title: '', // 分享标题
        link: window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
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
        title: 'Demo', // 分享标题
        desc: url, // 分享描述
        link: url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: '', // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    // 分享到QQ
    wx.onMenuShareQQ({
        title: '', // 分享标题
        desc: '', // 分享描述
        link: window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash, // 分享链接
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
        link: window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash, // 分享链接
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
        link: window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash, // 分享链接
        imgUrl: '', // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
}