let banner_history= angular.module("banner_history_module", []);
banner_history.controller("banner_history_ctrl", function ($rootScope,$scope, $http,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: 'merchant_index'
    }, {
        name: 'APP推荐位-banner管理',
        link: 'banner_recommend',
    }, {
        name: '历史数据统计'
    }];
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
    $scope.params = {
        page: 1,                        // 当前页数
        district_code: '510100',               // 时间类型
        type:0,
        time_type: 'all',                    // 关键字查询
        start_time: '',                 // 自定义开始时间
        end_time: ''                   // 自定义结束时间
    };
    let tablePages=function () {
        $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/mall/recommend-history',$scope.params,function (res) {
            console.log(res);
            $scope.recommendList = res.data.recommend_history.details
            $scope.Config.totalItems = res.data.recommend_history.total;
        })
    };
    $http.get(baseUrl+'/site/time-types').then(function (response) {
        $scope.time = response.data.data.time_types;
        $scope.params.time_type = response.data.data.time_types[0].value;
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


