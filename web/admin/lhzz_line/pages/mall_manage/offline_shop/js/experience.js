app.controller('offline_shop', ['$rootScope', '$scope', function ($rootScope, $scope) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '线下体验店'
    }];
}]);