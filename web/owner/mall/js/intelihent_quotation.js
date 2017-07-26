/**
 * Created by xl on 2017/6/29 0029.
 */
angular.module('app',[])
    .controller("Intelligent_quotation",function ($scope,$http) {
        $scope.nowSeries ='齐家';
        $scope.nowStyle = '现代简约';
        $http({
            method:"get",
            url:url+"owner/series-and-style"
        }).then(function successCallback (resp) {
            $scope.message = resp.data.data.show.stairs_details;
            $scope.style = resp.data.data.show.series;
            $scope.me=resp.data.data.show.style;
            console.log($scope.me);
        },function errorCallback () {

        });
        //切换系列
        $scope.toggleSeries = function (item) {
            $scope.nowSeries = item;
        };

        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.nowStyle = item;
        }
    });


//输入框获取焦点跳转页面
$(".search_a").blur(function () {

});
