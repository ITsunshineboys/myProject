app.controller('withdraw_manage_ctrl',function ($rootScope,$state,_ajax,$scope,$stateParams) {
    let fromState = $rootScope.fromState_name == 'withdraw_detail'
    if(!fromState){
        sessionStorage.removeItem('withdrawDetail')
        sessionStorage.removeItem('isOwnerFlag')
    }
    let withdrawDetail = sessionStorage.getItem('withdrawDetail')
    let isOwnerFlag = sessionStorage.getItem('isOwnerFlag')
    console.log($stateParams)
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
        name:'业主提现管理'
        }
    ]
    $scope.owner_finance_data = JSON.parse(sessionStorage.getItem('owner_finance_data'))
    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
        if(!isOwnerFlag){
            if(withdrawDetail != null){
                let params = JSON.parse(withdrawDetail)
                $scope.params = params
                $scope.keyword = params.keyword
                $scope.Config.currentPage = params.page
            }
        }
    })
    $scope.keyword = ''
    //状态选择
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
            tablePages()
        }
    }
    let tablePages = function () {
        $scope.params.page = $scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/supplier-cash/owner-cashed-list', $scope.params, function (res) {
            console.log(res);
            $scope.withdraw_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.params = {
        time_type: withdrawDetail!=null&&!isOwnerFlag?JSON.parse(withdrawDetail).time_type:$stateParams.time_type,
        time_start: '',
        time_end: '',
        status:withdrawDetail!=null&&!isOwnerFlag?JSON.parse(withdrawDetail).status:$stateParams.status,
        keyword:''
    };
    // $scope.getWithdraw = function (index) {
    //     if(index == 1){
    //         if($scope.params.time_type == 'custom'){
    //             if($scope.params.time_start!=''||$scope.params.time_end!=''){
    //                 $scope.Config.currentPage = 1
    //                 tablePages()
    //             }
    //         }else{
    //             $scope.params.time_start = ''
    //             $scope.params.time_end = ''
    //             $scope.Config.currentPage = 1
    //             tablePages()
    //         }
    //     }else{
    //         $scope.Config.currentPage = 1
    //         tablePages()
    //     }
    //     $scope.keyword = ''
    //     $scope.params.keyword = ''
    // }
    // $scope.$watch('keyword',function (newVal,oldVal) {
    //     if(newVal == ''&&oldVal!=''){
    //         $scope.params.keyword = ''
    //         $scope.Config.currentPage = 1
    //         tablePages()
    //     }
    // })
    $scope.$watch('params',function (newVal,oldVal) {
        if (newVal.page != oldVal.page) {

        } else {
            if(newVal.keyword === oldVal.keyword){
                $scope.keyword = ''
            }
            $scope.Config.currentPage = 1
            tablePages()
        }
        if(newVal.time_type!='custom'){
            newVal.time_start = ''
            newVal.time_end = ''
            return
        }
    },true)
    $scope.inquire = function () {
        if($scope.keyword!=''){
            $scope.params.keyword = $scope.keyword
            $scope.params.time_type = 'all'
            $scope.params.time_start = ''
            $scope.params.time_end = ''
            $scope.params.status = '0'
            $scope.Config.currentPage = 1
            tablePages()
        }else if($scope.params.time_type == 'all'&&
            $scope.params.status==0&&$scope.keyword == ''){
            $scope.params.search = ''
            $scope.Config.currentPage = 1
        }
    }
    //跳转内页保存状态
    $scope.goInner = function () {
        sessionStorage.setItem('withdrawDetail',JSON.stringify($scope.params))
    }
})