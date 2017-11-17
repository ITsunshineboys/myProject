;
let brand_details= angular.module("brand_details_module",[]);
brand_details.controller("brand_details_ctrl",function ($rootScope,$scope,$stateParams) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: 'merchant_index'
    }, {
        name: '品牌管理',
        link: 'brand_index',
        params:{check_flag:true}
    }, {
        name: '品牌详情'
    }];
  $scope.item=$stateParams.item;//点击传入的数据
  console.log($scope.item)
});