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

//��ȡ���㣬��ȡ���������  ��ʾ����
$(".input_data").keyup(function () {
    //data Ϊ�ӿڵ�����
    var data;
    if(data.length>0){
        $(".have_data").css("display","black")
    }else{
        $(".no_data").css("display","black")
    }

});