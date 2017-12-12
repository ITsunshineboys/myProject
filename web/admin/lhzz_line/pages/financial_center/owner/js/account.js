app.controller('account_ctrl',function ($state,$rootScope,$scope,_ajax) {
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
    $scope.vm = $scope
    $scope.keyword = ''
    $scope.all_status = [
        {num:'-1',status:'全部'},
        {num:'0',status:'已关闭'},
        {num:'1',status:'正常'}
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
            $scope.account_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.params = {
        status:$scope.all_status[0].num,
        keyword:''
    }
    $scope.getAccount = function () {
        $scope.Config.currentPage = 1
        $scope.params.keyword = ''
        $scope.keyword = ''
        tablePages()
    }
    $scope.getAccountList = function () {
        $scope.Config.currentPage = 1
        if($scope.keyword!=''){
            $scope.params.keyword = $scope.keyword
            $scope.params.status = $scope.all_status[0].num
            tablePages()
        }
    }
    $scope.$watch('keyword',function (newVal,oldVal) {
        if(newVal == ''&&oldVal!=''){
            $scope.Config.currentPage = 1
            $scope.params.keyword = ''
            tablePages()
        }
    })
})