/**
 * Created by xl on 2017/6/26 0026.
 */
//点击普通添加  添加按钮
    var a=0;
    var b=1;
$(".only_add").on("click",function () {
    a++;
    //alert(a);
    var newNode=$("<li>\
    <img src='images/red.png' alt=''/>\
    <button id='btn_delete' onclick='deletePar(this)'>-</button>\
    <input type='text' placeholder='1-6个字' class='input_val' id='input"+a+"'/>\
    <input type='text' class='input_string'  disabled/>\
    <select class='sel'>\
    <option value='1'>无</option>\
    <option value='2'>L</option>\
    <option value='3'>m</option>\
    <option value='4'>kg</option>\
    </select>\
    </li>");
    b++;
//添加节点
    $(".input_ul").append(newNode);
});

//点击多项添加
$('.more_add').on("click",function () {
    a++;
    var moreNode=$("<li>\
    <img src='images/red.png' alt=''/>\
    <button id='btn_delete' onclick='deletePar(this)'>-</button>\
    <input type='text' placeholder='1-6个字' class='input_val_more' id='input"+a+"'/>\
    <input type='text' class='input_more' placeholder='多属性以 , 隔开' id='input"+a+"' />\
    <select class='sel_more'>\
    <option value='1'>无</option>\
    <option value='2'>L</option>\
    <option value='3'>m</option>\
    <option value='4'>kg</option>\
    </select>\
    </li>");
    b++;
    $('.input_ul').append(moreNode);
});

//点击删除按钮  删除节点
function deletePar (obj) {
    $(obj).parent().remove();
}

//点击保存时获取所有input 的值.并且发起AJAX 请求传值到后台
var selete,text,more,selete_more, text_more;
function add_data () {
    //普通添加获取input值
    $('.input_val').each(function (index,item) {
        text=($(item).val());
        console.log(text);
    });
    //多项添加获取input 的值
    $('.input_val_more').each(function (index,item) {
        text_more=($(item).val());
        console.log(text_more);
    });
    //多项添加 隔开内容获取
    $('.input_more').each(function (index,item) {
        more=($(item).val());
    //var c=$('input_val')
        console.log(more);
    });

    //普通添加获取下拉框值
    $('.sel option:selected').each(function (index,item) {
        selete=($(item).text());
        console.log(selete);
    });
    //多项添加获取下拉框的值
    $('.sel_more option:selected').each(function (index,item) {
        selete_more=($(item).text());
        console.log(selete_more);
    });

    $.ajax({
        method:"post",
        url:url+"",
        dataType:"json",
        data:{
            arrayInput:text,
            arraySelete:selete
        },
        success:function (data){
            alert("11111");
        },
        error:function(data){
            alert(2222);
        }
    })

};

//获取数据传给后台
//var app=angular.module('app',[]);
//app.controller("attrAddctrl",function ($scope,$http) {
//    $http({
//        method:"post",
//        url:url+""
//    })
//});

