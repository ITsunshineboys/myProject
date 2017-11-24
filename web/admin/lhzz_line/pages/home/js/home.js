app.controller('home', ['$rootScope', '$scope', '_ajax', function ($rootScope, $scope, _ajax) {
    sessionStorage.removeItem('finance_menu');
    sessionStorage.removeItem('mall_menu');
    sessionStorage.removeItem('mall_dd_menu');
    sessionStorage.removeItem('finance_dd_menu');
    sessionStorage.removeItem('other_menu');
    $rootScope.crumbs = [{
        name: '首页',
        icon: 'icon-shouye'
    }];
    _ajax.get('/mall/index-admin-lhzz', {}, function (res) {
        $scope.data = res.data.index_admin_lhzz
    })
}]);