/**
 * Created by Administrator on 2017/12/13/013.
 */
app.controller('account_user_verify_fail', ['$rootScope', '$scope', '$state', '$stateParams','_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {
    /*请求参数*/
    $scope.params = {
        time_type: 'all',               // 时间类型
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
        page: 1,                        // 当前页数
        keyword: '',                    // 关键字查询
        status: 1,                      // 审核状态
        sort: 2                         //排序规则 默认按审核时间降序排列
    }

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tableList();
        }
    }

    // 时间筛选器
    $scope.$watch('params.time_type', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if (value == 'all' && $scope.params.keyword != '') {
            return
        }
        if (value != 'custom') {
            $scope.keyword = '';                // 关键字查询
            $scope.params.start_time = '';     // 自定义开始时间
            $scope.params.end_time = '';       // 自定义结束时间
            $scope.params.sort = 2;            //审核时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });

    //自定义时间筛选
    // 开始时间
    $scope.$watch('params.start_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.end_time != '') {
            $scope.keyword = '';        // 关键字查询
            $scope.params.sort = 2;     //审核时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });

    // 结束时间
    $scope.$watch('params.end_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.start_time != '') {
            $scope.keyword = '';        // 关键字查询
            $scope.params.sort = 2;     //审核时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });

    // 查询
    $scope.search = function () {
        $scope.params.time_type = 'all';   // 时间类型
        $scope.params.start_time = '';     // 自定义开始时间
        $scope.params.end_time = '';       // 自定义结束时间
        $scope.params.sort = 2;            //审核时间排序
        $scope.pageConfig.currentPage = 1;
        tableList();
    };

    // 审核时间排序
    $scope.sortTime = function () {
        $scope.params.sort = $scope.params.sort == 2 ? 1 : 2;
        $scope.pageConfig.currentPage = 1;
        tableList();
    };


    /*列表数据获取*/
    function tableList() {
        $scope.params.keyword = $scope.keyword;
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/supplieraccount/owner-audit-list', $scope.params, function (res) {
            $scope.pageConfig.totalItems = res.data.count;
            $scope.listdata = res.data.list;
        })
    }
}]);