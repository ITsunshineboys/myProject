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