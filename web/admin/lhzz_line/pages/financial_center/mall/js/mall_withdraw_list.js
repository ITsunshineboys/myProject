app.controller('mall_withdraw_list_ctrl',function ($state,$stateParams,_ajax,$scope,$rootScope,$uibModal) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '商城财务',
            link: function () {
                $state.go('mall_finance')
            }
        }, {
            name: '账户管理',
            link: function () {
                $state.go('mall_account')
            }
        }, {
            name: '详情',
            link: -1
        }, {
            name: '提现列表'
        }
    ]
    $scope.supplier_id = $stateParams.id
    $scope.params = {
        time_type: '',
        start_time: '',
        end_time: '',
        supplier_id: $stateParams.id
    };
    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
        $scope.params.time_type = $scope.time_types[0].value
        tablePages()
    })
    /*分页配置*/
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
        _ajax.get('/supplieraccount/cashed-list', $scope.params, function (res) {
            console.log(res)
            $scope.withdraw_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.getWithdrawList = function () {
        if($scope.parmas.time_type!=''){
            if($scope.params.time_type == 'custom'){
                if($scope.params.start_time!=''||$scope.params.end_time!=''){
                    tablePages()
                }
            }else{
                tablePages()
            }
        }
    }
})