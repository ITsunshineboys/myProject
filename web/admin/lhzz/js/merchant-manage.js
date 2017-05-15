//导航栏
//$(".nav_box dt").css({"background-color":"#F8F8F8"});
$(".nav_box dt img").attr("src","images/select1.png");
$(function(){
    $(".nav_box dd").hide();
    //初始该显示的dd
    $(".nav_box .mall>dd").show();
    $(".nav_box dt").click(function(){
        $(".nav_box dt").css({"background-color":"#F5F7FA","color":"#ABABAB"});
        $(this).css({"background-color": "#E6E9F0","color":"#5677FC"});
        $(this).parent().find('dd').removeClass("menu_chioce");
        $(this).parent().parent().find('dd').find("a").removeClass("dd_on");
        $(".nav_box dt img").attr("src","images/select1.png");
        $(this).parent().find('img').attr("src","images/select2.png");
        $(".menu_chioce").slideUp();
        $(this).parent().find('dd').slideToggle();
        $(this).parent().find('dd').addClass("menu_chioce");
        $(this).parent().find('dd').click(function(){
            $(this).parent().find('dd').find("a").removeClass("dd_on");
            $(this).find("a").addClass("dd_on");
        })
    });
})