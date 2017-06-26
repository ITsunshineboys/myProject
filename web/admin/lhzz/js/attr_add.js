/**
 * Created by xl on 2017/6/26 0026.
 */
//点击普通添加  添加按钮
    var a=0;
    var b=1;
$(".only_add").on("click",function () {
    a++;
    alert(a);
    //for(var i=0; i<a;i++){
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
    var moreNode=$("<li>\
    <img src='images/red.png' alt=''/>\
    <button id='btn_delete' onclick='deletePar()'>-</button>\
    <input type='text' placeholder='1-6个字' class='input_val'/>\
    <input type='text' placeholder='多属性以 , 隔开'/>\
    <select>\
    <option value='1'>无</option>\
    <option value='2'>L</option>\
    <option value='3'>m</option>\
    <option value='4'>kg</option>\
    </select>\
    </li>");
    $('.input_ul').append(moreNode);
});

//点击删除按钮  删除节点
function deletePar (obj) {
    var a=$(obj).parent().remove();
}

//获取数据传给后台
//var app=angular.module('app',[]);
//app.controller("attrAddctrl",function ($scope,$http) {
//    $http({
//        method:"post",
//        url:url+""
//    })
//});

//点击保存时获取所有input 的值
var selete;
var text1;
function add_data () {
    /*for(var i=1; i<b; i++){
     console.log($("#input"+i));
     }*/
    //获取inputd 值
    $('.input_val').each(function (index,item) {
        text1=($(item).val());
        console.log(text1);
    });
    //获取下拉框值
    $('.sel option:selected').each(function (index,item) {
        selete=($(item).text());
        console.log(selete)
    });


}

//$(".add_data").on("click",function () {
//    console.log($(".input_val").val());
//});
//$(".input_val").focus(function (){
//    //var a=$(".input_val").val();
//    //var b=$(".input_string").val();
//    alert(1111);
//    //console.log(b)
//});
//$(function () {
//    console.log(a)
//    console.log(b)
//    $.ajax({
//        method:"post",
//        url:url+"",
//        dataType:"json",
//        data:{
//
//        },
//        success:function (data){
//            alert("11111");
//        },
//        error:function(data){
//            alert(2222);
//        }
//    })
//});
