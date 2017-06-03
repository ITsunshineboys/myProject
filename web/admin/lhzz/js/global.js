//var url="http://local.test.cdlhzz.cn/";
var url="http://test.cdlhzz.cn:888/";
var app=angular.module("app",[]);
//导航栏
$(function(){
    $(".nav_box dd").hide();
    //初始该显示的dd
    $(".nav_box .dl_on>dd").show();
    $(".nav_box dt").click(function(){
        $(".nav_box dt").css({"background-color":"#F5F7FA","color":"#ABABAB"});
        $(this).css({"background-color": " #E6E9F0","color":"#5677FC"});
        $(this).parent().find('dd').removeClass("menu_chioce");
        $(this).parent().parent().find('dd').find("a").removeClass("dd_on");
        $(".menu_chioce").slideUp();
        $(this).parent().find('dd').slideToggle();
        $(this).parent().find('dd').addClass("menu_chioce");
        $(this).parent().find('dd').click(function(){
            $(this).parent().find('dd').find("a").removeClass("dd_on");
            $(this).find("a").addClass("dd_on");
        })
    });


});
/*
//右边内容自适应
var browser_width1=$(document).width()-$(".nav_box").width();
$(".my_container").css("width",browser_width1);
$(".header_box").css("width",browser_width1);
//浏览器大小变化的监听
$(window).resize(function() {
    browser_width1=$(document).width()-$(".nav_box").width();
    console.log("$(document).width()="+$(document).width())
    console.log("browser_width1="+browser_width1)
    $(".my_container").css("width",browser_width1);
    $(".header_box").css("width",browser_width1);
});*/
