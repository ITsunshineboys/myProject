/**
 * Created by xl on 2017/8/10 0010.
 */
app.controller('id_info', ['$state', '$scope', '$stateParams', '$http', '$rootScope', function ($state, $scope, $stateParams, $http, $rootScope) {
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-zhanghuguanli',
        link: $rootScope.account_click
    },{
        name: '账户管理详情',
        link: -1
    },{
        name: '身份认证',
    }];

    $scope.id = $stateParams.id;
    $scope.account_detail = JSON.parse(sessionStorage.getItem('account_detail'));
    $scope.backPage = () => {
        history.go(-1);
    }
}])