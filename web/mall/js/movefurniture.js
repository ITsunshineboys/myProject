/**
 * Created by xl on 2017/6/20 0020.
 */
var myapp=angular.module('myapp',[]);
myapp.controller("commentCtrl",function ($scope,$http) {
    $http({
        method: 'post',
        url:url+"/categories"
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
//        url:"http://local.test.cdlhzz.cn/mall/categories",
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