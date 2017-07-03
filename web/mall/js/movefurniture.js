/**
 * Created by xl on 2017/6/20 0020.
 */
var myapp=angular.module('myapp',[]);
myapp.controller("commentCtrl",function ($scope,$http) {
    $http({
        method: 'post',
        url:url+"mall/categories?pid=6"
    }).then(function successCallback(data) {
        $scope.message = data.data.data.categories;
        //alert(message);
    }, function errorCallback(data) {

        //alert(data);

    });
});
//$(function () {
//    $.ajax({
//        method:"post",
//
//        url:url+"mall/categories?pid=6",
//        dataType:"json",
//        data:{},
//        success:function (data){
//            alert("11111");
//            //console.log(res.data.data.categories)
//        },
//        error:function(data){
//            //console.log(res)
//            alert(2222);
//        }
//    })
//});