/**
 * Created by xl on 2017/6/30 0030.
 */

var app = angular.module("app",[]);
app.controller("searchCtrl", function ($scope,$http) {
    $http({
        method:"post",
        url:url+""
    }).then(function success () {

    },function (){

    })
});

//获取焦点，获取输入的内容  显示数据
$(".input_data").keyup(function () {
    //data 为接口的数据
    var data;
    if(data.length>0){
        $(".have_data").css("display","black")
    }else{
        $(".no_data").css("display","black")
    }

});