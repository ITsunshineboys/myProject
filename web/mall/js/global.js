//var url="http://test.cdlhzz.cn:888/mall/";
//var url="http://local.test.cdlhzz.cn/mall/";
var app=angular.module("app",[]);
//��ȡurl�еģ��ź��������
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    //��ȡurl��"?"������ַ���������ƥ��
    var context = "";
    if (r != null)
        context = r[2];
    reg = null;
    r = null;
    return context == null || context == "" || context == "undefined" ? "" : context;
}