/**
 * Created by Administrator on 2017/12/12/012.
 */
app.controller('settle_verify', ['$rootScope', '$scope', '$state', '$stateParams', function ($rootScope, $scope, $state, $stateParams) {
    $scope.id = $stateParams.id;    // 商家ID
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家入驻审核'
    }];
}]);