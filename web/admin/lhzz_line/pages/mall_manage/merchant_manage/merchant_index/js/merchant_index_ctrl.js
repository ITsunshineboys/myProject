/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*商城管理 首页*/

var mall_mag = angular.module("mallmagModule",[]);
mall_mag.controller("mall_mag",function ($scope,$http,$rootScope,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    },{
        name:'商城数据'
    }];

    _ajax.get('/mall/index-admin',{},function (res) {
        $scope.result = res.data.index_admin;
    })
});