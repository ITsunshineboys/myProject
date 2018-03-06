app.controller('account_ctrl',function ($state,$rootScope,$scope,_ajax) {
    let fromState = $rootScope.fromState_name == 'account_detail'||$rootScope.fromState_name == 'freeze_money'
    ||$rootScope.fromState_name == 'freeze_list' ||$rootScope.fromState_name == 'money_list'
    if(!fromState){
        sessionStorage.removeItem('financeAccount')
    }
    let financeAccount = sessionStorage.getItem('financeAccount')
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
    if(financeAccount != null){
        let params = JSON.parse(financeAccount)
        $scope.params = params
        $scope.keyword = params.keyword
        $scope.Config.currentPage = params.page
    }
    // $scope.getAccount = function () {
    //     $scope.Config.currentPage = 1
    //     $scope.params.keyword = ''
    //     $scope.keyword = ''
    //     tablePages()
    // }
    $scope.$watch('params',function (newVal,oldVal) {
        if (newVal.page != oldVal.page) {

        } else {
            // if(newVal.keyword === oldVal.keyword){
            //     $scope.keyword = ''
            // }
            if(newVal.status != oldVal.status){
                $scope.Config.currentPage = 1
            }
            tablePages()
        }
    },true)
    $scope.getAccountList = function () {
        if($scope.keyword!=''){
            $scope.params.keyword = $scope.keyword
            $scope.params.status = $scope.all_status[0].num
            $scope.Config.currentPage = 1
            tablePages()
        }else if ($scope.params.status == 0&&$scope.keyword == ''){
            $scope.params.keyword = ''
            $scope.Config.currentPage = 1
        }
    }
    // $scope.$watch('keyword',function (newVal,oldVal) {
    //     if(newVal == ''&&oldVal!=''){
    //         $scope.Config.currentPage = 1
    //         $scope.params.keyword = ''
    //         tablePages()
    //     }
    // })
    //跳转内页保存状态
    $scope.goInner = function () {
        sessionStorage.setItem('financeAccount',JSON.stringify($scope.params))
    }
})