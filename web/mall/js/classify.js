//tab
function smallTab() {
    $(".resumes_content > .tab2").hide();
    $(".resumes").each(function () {
        $(this).next("div").find(".11").show();
        $(this).find("li:first a").addClass("current").css({"color":"red"});
        $('.resumes a[name="22"]').css({"color":"#333"});
        $('.resumes a[name="33"]').css({"color":"#333"});

    });
    $(".content").each(function () {
        $(this).find("div:first").fadeIn();
    });
    $(".resumes a").on("click", function (e) {
        e.preventDefault();
        $(this).parent().parent().next("div").find(".tab2").hide();
        $(this).parent().parent().find("a").removeClass("current");
        if ($(this).attr("class") == "current"&&$(this)==$(".resumes").find("li:first a")) {
            return;
        }
        else {
            if ($(this).attr("name") == 11) {
                $('.resumes a[name="22"]').css({"color":"#333"});
                $('.resumes a[name="33"]').css({"color":"#333"});
                $(this).parent().parent().next("div").find(".11").show();
                $(this).addClass("current").css({"color":"red"});
                $(this).find("img").attr({ src: "images/man_1.png" });
                $($(this).attr("name")).fadeIn();
            }
            else if ($(this).attr("name") == 22) {
                $('.resumes a[name="11"]').css({"color":"#333"});
                $('.resumes a[name="33"]').css({"color":"#333"});
                $(this).parent().parent().next("div").find(".22").show();
                $(this).addClass("current").css({"color":"red"});
                $($(this).attr("name")).fadeIn();
            }
            else if ($(this).attr("name") == 33) {
                $('.resumes a[name="11"]').css({"color":"#333"});
                $('.resumes a[name="22"]').css({"color":"#333"});
                $(this).parent().parent().next("div").find(".33").show();
                $(this).addClass("current").css({"color":"red"});
                $($(this).attr("name")).fadeIn();
            }
        }
    });
}
$(function(){
    smallTab();
});