//        $("#p").text(GetQueryString("txt"));
$("#p").text(decodeURI(GetQueryString('txt')));
console.log("url上带的值=="+decodeURI(GetQueryString('txt')));
//tab
//tab页面的动态初始化
function int(obj,now_class){
    if(now_class==null||now_class==0){
        now_class=11;
    }
    var my_class=String("."+now_class);
    obj.next("div").find(my_class).show();
    obj.find(".categorys_li").find('a[name="'+now_class+'"]').addClass("current");

}
//点击的样式变化函数
function  Curve(obj,name){
    var mykind=String("."+name);
    console.log("mykind=="+mykind)
    if (obj.attr("name") == name) {
        obj.parent().parent().find("a").removeClass("current");
        obj.parent().parent().next("div").find(mykind).show();
        obj.addClass("current");
        $(obj.attr("name")).fadeIn();
    }
}
function smallTab() {

    $(".categorys_content > .tab2").hide();
    $(".categorys").each(function () {
        var obj=$(this);
        var my_class=decodeURI(GetQueryString('txt'));
        int(obj,my_class);
        //var aa="."+"11";
        //var cc=22;
        //$(this).next("div").find(aa).show();
        //$(this).find(".categorys_li").find('a[name="'+cc+'"]').addClass("current");

    });
    $(".content").each(function () {
        $(this).find("div:first").fadeIn();
    });
    $(".categorys a").on("click", function (e) {

        e.preventDefault();
        $(this).parent().parent().next("div").find(".tab2").hide();
        $(this).parent().parent().find("a").removeClass("current");
        if ($(this).attr("class") == "current"&&$(this)==$(".categorys").find("li:first a")) {
            return;
        }
        else {
            var obj=$(this);
            var myname=$(this).attr("name");
            //console.log("myname==="+myname)
            Curve(obj, myname)
        }
    });
}
$(function(){
    smallTab();
});
app.controller("classify",function($http,$scope){

})