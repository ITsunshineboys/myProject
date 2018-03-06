/**
 * Created by Administrator on 2017/12/13/013.
 */
app.controller('account_user_verify_pass', ['$rootScope', '$scope', '$state', '$stateParams', '_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {
    let fromState = $rootScope.fromState_name === 'user_verify_detail';
    if (!fromState) {
        sessionStorage.removeItem('saveStatus');
        sessionStorage.removeItem('isOperation')
    }

    /*请求参数*/
    $scope.params = {
        time_type: 'all',               // 时间类型
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
        page: 1,                        // 当前页数
        keyword: '',                    // 关键字查询
        status: 2,                      // 审核状态
        sort: 2                         //排序规则 默认按审核时间降序排列
    }

    $scope.table = {
        keyword: '' // 输入框值
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

    let isOperation = sessionStorage.getItem('isOperation');
    if (isOperation === null) {     // 判断详情是否是操作数据后跳转到当前页面的
        let saveTempStatus = sessionStorage.getItem('saveStatus');
        if (saveTempStatus !== null) {      // 判断是否保存参数状态
            saveTempStatus = JSON.parse(saveTempStatus);
            $scope.params = saveTempStatus;
            $scope.table.keyword = saveTempStatus.keyword;
            $scope.pageConfig.currentPage = saveTempStatus.page
        }
    }

    // 时间筛选器
    $scope.$watch('params.time_type', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if (value == 'all' && $scope.table.keyword != '') {
            return
        }
        if (value != 'custom') {
            $scope.table.keyword = '';         // 关键字查询
            $scope.params.keyword = '';
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
            $scope.table.keyword = '';        // 关键字查询
            $scope.params.keyword = '';
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
            $scope.table.keyword = '';        // 关键字查询
            $scope.params.keyword = '';
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
        $scope.params.keyword = $scope.table.keyword;
        tableList();
    };

    // 审核时间排序
    $scope.sortTime = function () {
        $scope.params.sort = $scope.params.sort == 2 ? 1 : 2;
        $scope.pageConfig.currentPage = 1;
        tableList();
    };


    // 列表数据获取
    function tableList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/supplieraccount/owner-audit-list', $scope.params, function (res) {
            $scope.pageConfig.totalItems = res.data.count;
            $scope.listdata = res.data.list;
        })
    }

    $scope.saveStatus = saveParams

    // 缓存当前页面状态参数
    function saveParams() {
        let temp = JSON.stringify($scope.params);
        sessionStorage.setItem('saveStatus', temp)
    }

}]);