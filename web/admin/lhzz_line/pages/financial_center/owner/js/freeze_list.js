app.controller('freeze_list_ctrl',function ($uibModal,$state,$stateParams,$scope,$rootScope,_ajax) {
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
            link: function () {
                $state.go('finance_account')
            }
        }, {
            name: '详情',
            link: -1
        }, {
            name: '冻结列表'
        }
    ]
    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
        $scope.params.time_type = $scope.time_types[0].value
    })
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
        _ajax.get('/supplieraccount/owner-freeze-list', $scope.params, function (res) {
            console.log(res);
            $scope.freeze_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.params = {
        time_type:'' ,
        time_start: '',
        time_end: '',
        user_id:$stateParams.user_id
    };
    $scope.getFreezeList = function () {
        if($scope.params.use_id!=''&&$scope.params.time_type!=''){
            if($scope.params.time_type == 'custom'){
                if($scope.params.time_start!=''||$scope.params.time_end!=''){
                    $scope.Config.currentPage = 1
                    tablePages()
                }
            }else{
                $scope.Config.currentPage = 1
                tablePages()
            }
        }
    }
    $scope.removeFreeze = function (item) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '是否确认解冻'

            $scope.common_house = function () {
                $uibModalInstance.close()
                _ajax.get('/supplieraccount/owner-freeze-taw',{
                    freeze_id:item.id
                },function (res) {
                    console.log(res)
                    tablePages()
                })
            }
            $scope.cancel = function () {
                $uibModalInstance.close()
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        $uibModal.open({
            templateUrl: 'pages/financial_center/owner/cur_modal.html',
            controller: all_modal
        })
    }
    //展示冻结原因
    $scope.getFreezeReason = function (item) {
        $scope.cur_item = item
    }
})