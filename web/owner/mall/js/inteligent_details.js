/**
 * Created by xl on 2017/6/19 0019.
 */
//获取本地json 数据
var myApp = angular.module("myApp",[]);
myApp.controller("comment_controller",function($scope, $http,$filter){
    $http({
        method: 'get',
        url: "commodity.json"
    }).then(function successCallback(response) {
        $scope.message = response.data.data.code;
        //category_goods
        //$scope.myFilter={
        //    limit:4
        //};
        alert(message);

        //$scope.returnMore=function(a,b){
        //    return a>=b
        //}


    }, function errorCallback(response) {
        // 请求失败执行代码
        alert(222);

    });
});