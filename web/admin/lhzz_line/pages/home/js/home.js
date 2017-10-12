app.controller('home', ['$scope', '_ajax', function ($scope, _ajax) {
    _ajax.get('/mall/index-admin-lhzz',{}, function (res) {
        $scope.data = res.data.index_admin_lhzz
    })
}]);