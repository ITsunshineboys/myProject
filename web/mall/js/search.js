/**
 * Created by xl on 2017/6/17 0017.
 */
$(".search").on("click",function () {
    var input_value =$(".search").val();
    if(input_value ==""){
        $(".tab_none").show();
        $(".tab_hidden").hide();
    }else{
        $(".tab_none").hide();
        $(".tab_hidden").show();
    }
});