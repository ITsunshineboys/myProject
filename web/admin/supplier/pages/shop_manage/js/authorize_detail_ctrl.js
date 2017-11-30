/**
 * Created by Administrator on 2017/9/21/021.
 */
let authorizedetail = angular.module("authorizedetailModule", []);
authorizedetail.controller("authorizedetail_ctrl", function ($scope,$rootScope) {
    $rootScope.crumbs = [{
        name: '店铺管理',
        icon: 'icon-dianpuguanli',
        link: -1
    },{
        name: '品牌授权详情'
    }];

    let data = sessionStorage.getItem('authorizeInfo');
    if (data !== null) {
        $scope.data = JSON.parse(data);
    }
});