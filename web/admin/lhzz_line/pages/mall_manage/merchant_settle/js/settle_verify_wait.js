/**
 * Created by Administrator on 2017/12/12/012.
 */
app.controller('settle_verify_wait', ['$rootScope', '$scope', '$state', '$stateParams', function ($rootScope, $scope, $state, $stateParams) {
    $scope.id = $stateParams.id;    // 商家ID
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家入驻审核'
    }];


    $scope.params = {


    }

    $scope.storetype_arr = [{storetype: "全部", id: -1}, {storetype: "旗舰店", id: 0}, {
        storetype: "专卖店", id: 1}, {storetype: "专营店", id: 2},{storetype: "自营店", id: 3}] //店铺类型

}]);