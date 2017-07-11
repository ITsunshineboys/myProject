/**
 * Created by xl on 2017/6/28 0028.
 */

  //angular  调用数据接口获取数据渲染到页面
var app = angular.module('app', []);
app.controller("stageCtrl", function ($scope,$http) {
    $http({
        method:"post",
        url:url+""
    }).then( function success (resp) {
        $scope.message=resp
    },function error (resp) {

    })
});