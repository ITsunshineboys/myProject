app.controller('commodity', ['$rootScope', '$scope', '$state', '$stateParams', function ($rootScope, $scope, $state, $stateParams) {
    $scope.id = $stateParams.id;    // 商家ID
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家管理',
        link: 'store_mag',
    },{
        name: '商品管理',
    }];
}]);