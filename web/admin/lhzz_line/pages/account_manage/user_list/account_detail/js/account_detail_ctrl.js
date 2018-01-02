/**
 * Created by hulingfangzi on 2017/7/27.
 */
app.controller('account_mag_detail', ['$stateParams', '$rootScope', '$scope', '$http', '$state', function ($stateParams, $rootScope, $scope, $http, $state) {
    $scope.new_num = $stateParams.new_num;
    $scope.account_detail = JSON.parse(sessionStorage.getItem('account_detail'));
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-zhanghuguanli',
        link: $rootScope.account_click
    }, {
        name: '账户详情',
    }];

    // 实名认证跳转页
    $scope.jumpPage = function () {
        if ($scope.account_detail.review_status_desc == '通过') {
            $state.go("id_info", {id: $scope.account_detail.id})
        }
    }
}])
