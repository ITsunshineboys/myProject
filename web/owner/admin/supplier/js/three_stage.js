/**
 * Created by xl on 2017/6/28 0028.
 */

  //angular  �������ݽӿڻ�ȡ������Ⱦ��ҳ��
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