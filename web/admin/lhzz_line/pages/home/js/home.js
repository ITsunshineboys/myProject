app.controller('home', ['$rootScope', '$scope', '_ajax', function ($rootScope, $scope, _ajax) {
    $rootScope.crumbs = [{
        name: '首页',
        icon: 'icon-shouye'
    }];
    _ajax.get('/mall/index-admin-lhzz', {}, function (res) {
        $scope.data = res.data.index_admin_lhzz
    })
}]);