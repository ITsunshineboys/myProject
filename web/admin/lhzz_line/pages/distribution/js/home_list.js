app.controller('home_list_ctrl',function ($scope,$rootScope,$uibModal,$state,$stateParams,_ajax) {
   //面包屑
    $rootScope.crumbs = [
        {
            name:'分销',
            icon:'icon-fenxiao',
        }
    ]
    $scope.vm = $scope
    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
        // $scope.params.time_type = $scope.time_types[0].value
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
    let tablePages=function () {
        $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/distribution/getdistributionlist',$scope.params,function (res) {
            console.log(res);
            $scope.distribution_list = res.data.list
            $scope.nowday_add = res.data.nowday_add
            $scope.total_add = res.data.total_add
            $scope.Config.totalItems = res.data.count;
        })
    };
    $scope.params = {
        time_type:'all',
        start_time:'',
        end_time:'',
        keyword:''
    };
    $scope.getDistribution = function () {
        $scope.Config.currentPage = 1
        $scope.params.keyword = ''
        $scope.keyword = ''
        if($scope.params.time_type == 'custom'){
            if($scope.params.start_time!=''||$scope.params.end_time!=''){
                tablePages()
            }
        }else{
            $scope.params.start_time = ''
            $scope.params.end_time = ''
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
    $scope.inquire = function () {
        if($scope.keyword != ''){
            $scope.params = {
                time_type:'all',
                start_time:'',
                end_time:'',
                keyword:$scope.keyword
            };
            $scope.Config.currentPage = 1
            tablePages()
        }
    }
})