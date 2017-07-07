/**
 * Created by xl on 2017/6/29 0029.
 */
angular.module('app',[])
    .controller("Intelligent_quotation",function ($scope,$http) {
        $http({
            method:"post",
            url:url+"/mall/owner/series-and-style"
        }).then(function successCallback (resp) {
            $scope.message = resp.data.show;
            console.log($scope.message);
        },function errorCallback () {

        })
    });


//输入框获取焦点跳转页面
$(".search_a").blur(function () {

});
