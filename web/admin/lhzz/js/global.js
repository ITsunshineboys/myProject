var url="http://local.test.cdlhzz.cn/";
//var url="http://test.cdlhzz.cn:888/";
var app=angular.module("app",[]);
function zishiy(){
    var browser_width1=window.innerWidth-$(".nav_box").width();
    $(".my_container").css("width",browser_width1);
    //浏览器大小变化的监听
    $(window).resize(function() {
        var browser_width1=window.innerWidth-$(".nav_box").width();
        $(".my_container").css("width",browser_width1);
    });

}
zishiy();
