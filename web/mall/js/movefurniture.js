/**
 * Created by xl on 2017/6/20 0020.
 */
var myapp=angular.module('myapp',[]);
myapp.controller("commentCtrl",function ($scope,$http) {
    $http({
        method: 'get',
        url:url+"mall/categories"
    }).then(function successCallback(resp) {
        $scope.message = resp.data.data.categories;
        //$scope.url_img = resp.data.data.categories.icon;
        //console.log($scope.url_img);
        //console.log(resp);

    }, function errorCallback(data) {

        alert(22);

    });
});
//$(function () {
//    $.ajax({
//        method:"get",
//        url:url+"categories",
//        dataType:"json",
//        data:{},
//        success:function (resp){
//            alert("11111");
//            //console.1log(res.data.data.categories)
//        },
//        error:function(resp){
//            //console.log(res)
//            alert(222);
//        }
//    })
//});