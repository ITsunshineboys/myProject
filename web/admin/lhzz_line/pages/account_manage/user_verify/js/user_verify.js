/**
 * Created by Administrator on 2017/12/13/013.
 */
app.controller('account_user_verify', ['$rootScope', '$scope', '$state', '$stateParams', function ($rootScope, $scope, $state, $stateParams) {
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-zhanghuguanli',
        link: $rootScope.account_click
    }, {
        name: '用户审核'
    }];
}]);