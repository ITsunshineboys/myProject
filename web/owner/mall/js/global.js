const baseUrl = "http://test.cdlhzz.cn";
// const baseUrl = "";

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