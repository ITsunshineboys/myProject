/**
 * Created by Administrator on 2017/8/9 0009.
 */
var merchant_details = angular.module("merchant_details", [])
    .controller("merchant_details_ctrl", function ($rootScope,$scope, $http, $stateParams) {
        $rootScope.crumbs = [{
            name: '商城管理',
            icon: 'icon-shangchengguanli',
            link: 'merchant_index'
        }, {
            name: '商家管理',
            link:'store_mag',
        },{
            name:'商家详情',
            link: 'store_detail',
            params:{authorize_flag:true,store:$stateParams.store}
        },{
            name:'品牌授权详情'
        }];

        $scope.store =  $stateParams.store;
        $scope.authorizedata = $stateParams.itemdetail;
    })