/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*商城管理 首页*/

var mall_mag = angular.module("mallmagModule",[]);
mall_mag.controller("mall_mag",function ($scope,$http) {
    $http({
        method:"get",
        url:baseUrl+"/mall/index-admin",
    }).then(function (res) {
        $scope.result = res.data.data.index_admin;
    })
});