let index_recommend_history = angular.module("index_recommend_history_module", []);
index_recommend_history.controller("index_recommend_history_ctrl", function ($scope, $http) {
    $scope.myng=$scope;
    //POST请求的响应头
    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    $scope.recommendList=[];
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
        $http.get(baseUrl+'/mall/recommend-history',{
            params:$scope.params
        }).then(function (res) {
            console.log(res);
            $scope.recommendList = res.data.data.recommend_history.details
            $scope.Config.totalItems = res.data.data.recommend_history.total;
        },function (err) {
            console.log(err);
        })
    };
    $scope.params = {
        page: 1,                        // 当前页数
        district_code: '510100',               // 时间类型
        type:2,
        time_type: 'all',                    // 关键字查询
        start_time: '',                 // 自定义开始时间
        end_time: ''                   // 自定义结束时间
    };

    // $scope.selectValue = '全部时间'
    $http.get('http://test.cdlhzz.cn:888/site/time-types').then(function (response) {
        $scope.time = response.data.data.time_types;
        $scope.selectValue = response.data.data.time_types[0].value;
    }, function (error) {
        console.log(error)
    });
    //监听时间类型
    $scope.type_change=function () {
        $scope.Config.currentPage = 1; //页数跳转到第一页
        tablePages();
    }
    //监听开始和结束时间
    $scope.time_change=function () {
        $scope.Config.currentPage = 1; //页数跳转到第一页
        tablePages();
    }
});


