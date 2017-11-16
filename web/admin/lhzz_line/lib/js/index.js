let index = angular.module("index_module",[]);
index.controller("index_ctrl",function ($rootScope,$http) {
    $rootScope.baseUrl = baseUrl;
});