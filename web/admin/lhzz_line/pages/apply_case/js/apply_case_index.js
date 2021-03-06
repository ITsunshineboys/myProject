app.controller('apply_case_ctrl', function ($rootScope,$scope, _ajax, $state) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '装修申请',
            icon: 'icon-yangbanjian',
        }
    ]
    //时间类型
    _ajax.get('/site/time-types', {}, function (res) {
        console.log(res)
        $scope.time_types = res.data.time_types
        $scope.params.time_type = $scope.time_types[0].value
    })
    $scope.vm = $scope
    $scope.keyword = ''
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
        _ajax.get('/effect/effect-list', $scope.params, function (res) {
            console.log(res);
            $scope.total_data = {
                all_apply:res.data.all_apply,
                all_earnest:res.data.all_earnest,
                today_apply:res.data.today_apply,
                today_earnest:res.data.today_earnest
            }
            $scope.case_list = res.data['0'].list
            $scope.Config.totalItems = res.data['0'].count
        })
    };
    $scope.params = {
        keyword: '',
        time_type: '',
        start_time: '',
        end_time: ''
    }
    $scope.getCase = function () {
        if($scope.params.time_type!=''){
            if($scope.params.time_type == 'custom'){
                if($scope.params.start_time!=''||$scope.params.end_time!=''){
                    $scope.Config.currentPage = 1
                    tablePages()
                }
            }else{
                $scope.params.start_time = ''
                $scope.params.end_time = ''
                $scope.Config.currentPage = 1
                tablePages()
            }
        }
        $scope.keyword = ''
        $scope.params.keyword = ''
    }
    $scope.$watch('keyword',function (newVal,oldVal) {
        if(newVal==''&&oldVal!=''){
            $scope.Config.currentPage = 1
            $scope.params.keyword = ''
            tablePages()
        }
    })
    $scope.inquire = function () {
        if($scope.keyword!=''){
            $scope.Config.currentPage = 1
            $scope.params.keyword = $scope.keyword
            $scope.params.end_time = ''
            $scope.params.start_time = ''
            $scope.params.time_type = $scope.time_types[0].value
            tablePages()
        }
    }
    //获取备注
    $scope.getRemark = function (item) {
        $scope.cur_remark = angular.copy(item)
    }
    //编辑备注
    $scope.editRemark = function () {
        _ajax.post('/effect/effect-view',{
            id:$scope.cur_remark.id,
            remark:$scope.cur_remark.remark
        },function (res) {
            console.log(res)
            $scope.Config.currentPage = 1
            tablePages()
        })
    }
})