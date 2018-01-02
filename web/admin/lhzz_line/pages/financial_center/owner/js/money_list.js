app.controller('money_list_ctrl',function ($uibModal,$rootScope,$stateParams,_ajax,$state,$scope) {
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
            name: '收支明细'
        }
    ]
    $scope.keyword = ''
    $scope.vm = $scope
    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
        $scope.params.time_type = $scope.time_types[0].value
    })
    //明细状态
    _ajax.get('/supplieraccount/owner-access-status',{},function (res) {
        console.log(res)
        $scope.all_status = res.data
        $scope.params.type = $scope.all_status[0].status
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
        _ajax.get('/supplieraccount/owner-access-detail-list', $scope.params, function (res) {
            console.log(res);
            $scope.money_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.params = {
        time_type:'' ,
        start_time: '',
        end_time: '',
        user_id:$stateParams.user_id,
        keyword:'',
        type:'',
        sort_time:2
    };
    $scope.getMoneyList = function (index) {
        $scope.Config.currentPage = 1
        if($scope.params.time_type!=''&&$scope.params.user_id!=''){
            if(index == 1){
                if($scope.params.time_type == 'custom'){
                    if($scope.params.end_time!=''||$scope.params.start_time!=''){
                        tablePages()
                    }
                } else {
                    tablePages()
                }
            }else if(index == 2){
                $scope.params.sort_time = $scope.params.sort_time==1?2:1
                tablePages()
            }else{
                tablePages()
            }
        }
        $scope.params.keyword = ''
        $scope.keyword = ''
    }
    $scope.inquire = function () {
        if($scope.keyword!=''){
            $scope.Config.currentPage = 1
            $scope.params.time_type = $scope.time_types[0].value
            $scope.params.start_time = ''
            $scope.params.end_time = ''
            $scope.params.status = $scope.all_status[0].status
            $scope.params.keyword = $scope.keyword
            tablePages()

        }
    }
    $scope.$watch('keyword',function (newVal,oldVal) {
        if(newVal==''&&oldVal!=''){
            $scope.Config.currentPage = 1
            $scope.params.keyword = ''
            tablePages()
        }
    })
    // 查看货款详情
    $scope.getMoneyDetail = function (item) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.money_detail = item
            _ajax.get('/withdrawals/admin-user-access-detail',{
                transaction_no:item.transaction_no
            },function (res) {
                console.log(res)
                $scope.owner_detail = res.data
            })
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(item.access_type == '使用'||item.access_type == '充值' ||item.access_type == '退款'){
            $uibModal.open({
                templateUrl: 'pages/financial_center/owner/money_detail_modal.html',
                controller: all_modal
            })
        }else{
           $state.go('withdraw_detail',{transaction_no:item.transaction_no})
            sessionStorage.setItem('index',1)
        }
    }
})