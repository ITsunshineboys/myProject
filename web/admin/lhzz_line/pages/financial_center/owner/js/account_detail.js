app.controller('account_detail_ctrl', function ($state,$stateParams, $rootScope, $scope, _ajax) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '业主财务',
            link: function () {
                $state.go('owner_finance')
            }
        }, {
            name: '账户管理',
            link: -1
        }, {
            name: '详情'
        }
    ]
    _ajax.get('/supplieraccount/owner-account-detail', {
        user_id: $stateParams.id
    }, function (res) {
        console.log(res)
        $scope.owner_detail = res.data
        sessionStorage.setItem('owner_detail',JSON.stringify($scope.owner_detail))
    })
})