/**
 * Created by Administrator on 2017/12/13/013.
 */
app.controller('user_verify_detail', ['$rootScope', '$scope', '$state', '$stateParams','_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {
    $scope.id = $stateParams.id;    // 审核id
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.account_click
    }, {
        name: '用户审核',
        link: -1
    },{
        name: '审核'
    }];


    // $scope.params = {
    //     status:2,
    //     remark
    // }


        _ajax.get('/supplieraccount/audit-view', {id:$stateParams.id}, function (res) { //少身份证号字段
            $scope.detail = res.data;
        })

}]);