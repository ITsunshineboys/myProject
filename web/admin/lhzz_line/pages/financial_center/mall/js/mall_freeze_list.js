app.controller('mall_freeze_list_ctrl',function ($scope,$rootScope,$state,$stateParams,_ajax,$uibModal) {
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
            name: '冻结余额列表'
        }
    ]
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
        _ajax.get('/supplieraccount/freeze-list', $scope.params, function (res) {
            console.log(res);
            $scope.freeze_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.getFreezeList = function () {
        if($scope.parmas.time_type!=''){
            if($scope.params.time_type == 'custom'){
                if($scope.params.start_time!=''||$scope.params.end_time!=''){
                    $scope.Config.currentPage = 1
                    tablePages()
                }
            }else{
                $scope.Config.currentPage = 1
                tablePages()
            }
        }
    }
    //解冻
    $scope.removeFreeze = function (item) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '是否确认解冻？'
            $scope.save_freeze = function () {
                $uibModalInstance.close()
                _ajax.get('/supplieraccount/account-thaw', {
                    freeze_id: item.id
                }, function (res) {
                    console.log(res)
                    tablePages()
                })
            }
            $scope.cancel_freeze = function () {
                $uibModalInstance.close()
            }
        }
        $uibModal.open({
            templateUrl: 'pages/financial_center/mall/cur_modal.html',
            controller: all_modal
        })
    }
    //显示备注模态框
    $scope.showRemark = function (item) {
        $scope.cur_item = item
    }
})