app.controller('mall_withdraw_manage_ctrl',function ($scope,$stateParams,_ajax,$uibModal,$state,$rootScope) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '商城财务',
            link:-1
        },{
        name:'商家提现管理'
        }
    ]
    $scope.vm = $scope
    $scope.cash_data = JSON.parse(sessionStorage.getItem('mall_finance_data'))
    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
    })
    $scope.keyword = ''
    //状态选择
    $scope.all_status = [
        {num:'0',status:'全部'},
        {num:'1',status:'提现中'},
        {num:'2',status:'已提现'},
        {num:'3',status:'驳回'},
    ]
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
        _ajax.get('/supplier-cash/cash-list-today', $scope.params, function (res) {
            console.log(res);
            $scope.withdraw_list = res.data.list
            $scope.Config.totalItems = res.data.count;
        })
    };
    $scope.params = {
        time_type: $stateParams.time_type,
        status: $stateParams.status,
        time_start: '',
        time_end: '',
        search: ''
    };
    //获取列表数据
    $scope.getMallList = function (index) {
        if(index == 1){
            if($scope.params.time_type == 'custom'){
                if($scope.params.time_start!=''||$scope.params.time_end!=''){
                    $scope.Config.currentPage = 1
                    tablePages()
                }
            }else{
                $scope.params.time_start = ''
                $scope.params.time_end = ''
                $scope.Config.currentPage = 1
                tablePages()
            }
        }else{
            $scope.Config.currentPage = 1
            tablePages()
        }
        $scope.keyword = ''
        $scope.params.search = ''
    }
    $scope.$watch('keyword',function (newVal,oldVal) {
        if(newVal == ''&&oldVal!=''){
            $scope.params.search = ''
            $scope.Config.currentPage = 1
            tablePages()
        }
    })
    $scope.inquire = function () {
        if($scope.keyword!=''){
            $scope.params.search = $scope.keyword
            $scope.params.time_type = 'all'
            $scope.params.time_start = ''
            $scope.params.time_end = ''
            $scope.params.status = '0'
            $scope.Config.currentPage = 1
            tablePages()
        }
    }
})