//var url="http://test.cdlhzz.cn:888/";
var url="http://local.test.cdlhzz.cn/";
 //var url="http://local.test.cdlhzz.cn/";
var app=angular.module("app",[]);
//获取url中的？号后面的内容
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    //获取url中"?"符后的字符串并正则匹配
    var context = "";
    if (r != null)
        context = r[2];
    reg = null;
    r = null;
    return context == null || context == "" || context == "undefined" ? "" : context;
}