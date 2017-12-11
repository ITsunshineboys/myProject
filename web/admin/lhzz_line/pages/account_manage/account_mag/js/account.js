/**
 * Created by Administrator on 2017/11/27/027.
 */
app.controller('account', ['$rootScope', '$scope', '$state', '$stateParams', '_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-zhanghuguanli'
    }];
}]);