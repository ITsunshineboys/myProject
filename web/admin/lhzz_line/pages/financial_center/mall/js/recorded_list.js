app.controller('recorded_list_ctrl',function ($scope,$rootScope,$state,$stateParams,$uibModal,_ajax) {
    let fromState = $rootScope.fromState_name === 'order_details'
    if(!fromState){
        sessionStorage.removeItem('mallRecordedDetail')
    }
    let mallRecordedDetail = sessionStorage.getItem('mallRecordedDetail')
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '商城财务',
            link: -1
        }, {
            name: '入账详情'
        }
    ]
    $scope.vm = $scope
    $scope.params = {
        time_type:mallRecordedDetail!=null?JSON.parse(mallRecordedDetail).time_type:'today',
        time_start: '',
        time_end: '',
        search: ''
    };
    $scope.all_cash = JSON.parse(sessionStorage.getItem('mall_finance_data'))
    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
        if(mallRecordedDetail != null){
            let params = JSON.parse(mallRecordedDetail)
            $scope.params = params
            $scope.keyword = params.search
            $scope.Config.currentPage = params.page
        }
        // $scope.params.time_type = $scope.time_types[0].value
        // tablePages()
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
        _ajax.get('/supplier-cash/order-list-today', $scope.params, function (res) {
            console.log(res);
            $scope.recorded_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    // $scope.getRecordList = function () {
    //     if($scope.params.time_type!=''){
    //         if($scope.params.time_type == 'custom'){
    //             if($scope.params.time_start!=''||$scope.params.time_end!=''){
    //                 $scope.Config.currentPage = 1
    //                 tablePages()
    //             }
    //         }else{
    //             $scope.params.time_start = ''
    //             $scope.params.time_end = ''
    //             $scope.keyword = ''
    //             $scope.params.search = ''
    //             $scope.Config.currentPage = 1
    //             tablePages()
    //         }
    //     }
    // }
    // $scope.$watch('keyword',function (newVal,oldVal) {
    //     if(newVal==''&&oldVal!=''&&$scope.params.time_type!=''){
    //         $scope.Config.currentPage = 1
    //         $scope.params.search = ''
    //         tablePages()
    //     }
    // })
    $scope.$watch('params', function (newVal, oldVal) {
        if (newVal.page != oldVal.page) {

        } else {
            if(newVal.search === oldVal.search){
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
    }, true)
    $scope.inquire = function () {
        if($scope.keyword!=''){
            $scope.params = {
                time_type: $scope.time_types[0].value,
                time_start: '',
                time_end: '',
                search: $scope.keyword
            };
            $scope.Config.currentPage = 1
            tablePages()
        }else if($scope.params.time_type == 'all'&&$scope.keyword == ''){
            $scope.params.search = ''
            $scope.Config.currentPage = 1
        }
    }
    //跳转页面保存数据
    $scope.goInner = function () {
        sessionStorage.setItem('mallRecordedDetail',JSON.stringify($scope.params))
    }
})