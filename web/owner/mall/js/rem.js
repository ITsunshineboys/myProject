// 手机网页使用 rem 计算；
//如果页面的宽度超过了640px，那么页面中html的font-size恒为100px，
// 否则，页面中html的font-size的大小为：20 * (当前页面宽度 / 640)
(function (doc, win) {
    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function () {
            var clientWidth = docEl.clientWidth;
            if (!clientWidth)
                return;
            docEl.style.fontSize = 20 * (clientWidth / 640) + 'px';};
             if (!doc.addEventListener)
                 return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);