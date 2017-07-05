/**
 * Created by xl on 2017/7/4 0004.
 */
$(".edit").on("click",function () {
    $(".del").toggleClass("del_two");
    $(".wall_money").toggleClass("wall_money_sec");
});

//点击删除节点
$(".del").on("click",function () {
    var that=this;
    $('#myModal_del').modal("show");
    $(".del_btn").on("click",function () {
        //console.log(a);
        $(that).parent().remove();
        $('#myModal_del').modal("hide");
    })
});
