app.controller('class', ['$rootScope', '$scope', '$state', '$stateParams', '_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '分类管理',
    }];
}]);
