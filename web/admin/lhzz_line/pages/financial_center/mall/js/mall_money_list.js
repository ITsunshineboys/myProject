app.controller('mall_money_list_ctrl',function ($scope,$rootScope,$state,$stateParams,$uibModal,_ajax) {
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
            name: '收支明细'
        }
    ]
    $scope.params = {
        time_type: 'all',
        uid:'',
        keyword:'',
        sort_time:'2',
        type:'0',
        time_start:'',
        time_end:''
    };
    $scope.keyword = ''
    $scope.vm = $scope
    $scope.params.uid = $stateParams.id
    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
        $scope.params.time_type = $scope.time_types[0].value
        // tablePages()
    })
    $scope.all_type = [
        {num:'0',str:'全部'},
        {num:'1',str:'充值'},
        {num:'2',str:'扣款'},
        {num:'3',str:'已提现'},
        {num:'4',str:'提现中'},
        {num:'5',str:'驳回'},
        {num:'6',str:'货款'}
    ]
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
        _ajax.get('/supplieraccount/supplier-access-detail-list', $scope.params, function (res) {
            console.log(res)
            $scope.money_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.changeMoneyList = function (index) {
        if($scope.params.uid != ''){
            $scope.Config.currentPage = 1
            if(index == 1){
                if($scope.params.time_type != 'custom'){
                    $scope.params.time_start = ''
                    $scope.params.time_end = ''
                    tablePages()
                }else{
                    if($scope.params.time_start!=''||$scope.params.time_end!=''){
                        tablePages()
                    }
                }
                $scope.keyword = ''
                $scope.params.keyword = ''
            }else if(index == 2){
                $scope.params.sort_time = $scope.params.sort_time==1?2:1
                tablePages()
            }else{
                tablePages()
                $scope.keyword = ''
                $scope.params.keyword = ''
            }
        }
    }
    $scope.$watch('keyword',function (newVal,oldVal) {
        if(newVal == ''&&oldVal!=''&&$scope.params.uid!=''){
            $scope.params.keyword = newVal
            tablePages()
        }
    })
    //关键词查询
    $scope.inquire = function () {
        if($scope.keyword!=''){
            $scope.params.keyword = $scope.keyword
            $scope.params.time_type = 'all'
            $scope.params.type = '0'
            $scope.params.time_start = ''
            $scope.params.time_end = ''
            tablePages()
        }
    }
    // 查看货款详情
    $scope.getMoneyDetail = function (item) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.money_detail = item
            _ajax.get('/withdrawals/admin-user-access-detail',{
                transaction_no:item.transaction_no
            },function (res) {
                console.log(res)
                $scope.mall_detail = res.data
            })
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(item.access_type == '货款'||item.access_type == '充值' ||item.access_type == '扣款'){
            $uibModal.open({
                templateUrl: 'pages/financial_center/mall/money_detail_modal.html',
                controller: all_modal
            })
        }else{
            $state.go('mall_withdraw_manage_detail',{transaction_no: item.transaction_no})
        }
    }
})