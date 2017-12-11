app.controller('account_ctrl',function ($scope,_ajax) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '业主财务',
            link:function () {
                $state.go('owner_finance')
            }
        },{
            name:'账户管理'
        }
    ]
    $scope.all_status = [
        {num:'0',status:'全部'},
        {num:'1',status:'提现中'},
        {num:'2',status:'已提现'},
        {num:'3',status:'驳回'},
    ]
    //分页配置
    $scope.Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    }
    let tablePages = function () {
        $scope.params.page = $scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/supplieraccount/owner-account-list', $scope.params, function (res) {
            console.log(res);
            // $scope.withdraw_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.params = {
        status:$scope.all_status[0].num,
        keyword:''
    }
})