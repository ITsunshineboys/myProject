/**
 * Created by Administrator on 2017/12/13/013.
 */
app.controller('user_verify_detail', ['$rootScope', '$scope', '$state', '$stateParams', '_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.account_click
    }, {
        name: '用户审核',
        link: -1
    }, {
        name: '审核'
    }];


   /*待审核状态的参数*/
    $scope.params = {
        id: $stateParams.id, // 审核id
        status: '2',         // 默认审核通过
        remark: ''           // 审核备注
    }

    // 审核详情
    _ajax.get('/supplieraccount/audit-view', {id: $stateParams.id}, function (res) {
        $scope.detail = res.data;
    })

    // 待审核状态 确认审核
    $scope.verifyHandle = function () {
        _ajax.post('/supplieraccount/owner-do-audit', $scope.params, function (res) {
            $('#handle-success').modal('show');
        })
    }

    // 返回
    $scope.backPage = function () {
        history.go(-1);
    }
}]);