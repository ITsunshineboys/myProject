/**
 * Created by Administrator on 2017/12/13/013.
 */
app.controller('verify_detail', ['$rootScope', '$scope', '$state', '$stateParams', function ($rootScope, $scope, $state, $stateParams) {
    $scope.id = $stateParams.id;    // 商家ID
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家入驻审核',
        link: -1
    },{
        name: '审核'
    }];
}]);