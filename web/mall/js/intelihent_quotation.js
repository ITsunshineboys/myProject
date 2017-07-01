/**
 * Created by xl on 2017/6/29 0029.
 */
$(".list_dis ul li img").on("click",function () {
   var a= $(this).attr("class");
    console.log(a);
    var b =b;
    if(a == b){
        //$(this).addClass("sel").siblings("b").removeClass("sel");
        alert(1)
    }else{
        $(this).addClass("b").siblings("sel").removeClass("b");
    }
    //if(){
    //
    //}

    //$(this).addClass("blue").siblings("black").removeClass("blue");
});
