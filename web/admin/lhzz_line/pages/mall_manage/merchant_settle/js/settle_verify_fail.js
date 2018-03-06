/**
 * Created by tiger on 2017/12/12/012.
 */
app.controller('settle_verify_fail', ['$rootScope', '$scope', '$state', '$stateParams', '_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {

    let fromState = $rootScope.fromState_name === 'verify_detail';
    if (!fromState) {
        sessionStorage.removeItem('saveStatus');
        sessionStorage.removeItem('isOperation')
    }

    $scope.params = {
        time_type: 'all',               // 时间类型
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
        page: 1,                        // 当前页数
        keyword: '',                    // 关键字查询
        review_status: 1,               // 审核状态 - 审核不通过
        'sort[]': 'review_apply_time:3' //排序规则 默认按申请时间降序排列
    }

    $scope.table = {
        keyword : '' // 输入框值
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

    /*排序按钮样式控制*/
    $scope.sortStyleFunc = () => {
        return $scope.params['sort[]'].split(':')[1]
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
            $scope.table.keyword = '';                // 关键字查询
            $scope.params.keyword = '';
            $scope.params.start_time = '';     // 自定义开始时间
            $scope.params.end_time = '';       // 自定义结束时间
            $scope.params['sort[]'] = 'review_apply_time:3'; //申请时间排序
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
            $scope.params['sort[]'] = 'review_apply_time:3'; //申请时间排序
            $scope.pageConfig.currentPage = 1;
            tableList()
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
            $scope.params['sort[]'] = 'review_apply_time:3'; //申请时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });

    // 查询
    $scope.search = function () {
        $scope.params.time_type = 'all';   // 时间类型
        $scope.params.start_time = '';     // 自定义开始时间
        $scope.params.end_time = '';       // 自定义结束时间
        $scope.params['sort[]'] = 'review_apply_time:3'; //申请时间排序
        $scope.params.keyword = $scope.table.keyword;
        $scope.pageConfig.currentPage = 1;
        tableList();
    };

    //时间排序
    $scope.sortTime = function () {
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'review_apply_time:3' ? 'review_apply_time:4' : 'review_apply_time:3';
        $scope.pageConfig.currentPage = 1;
        tableList();
    };


    //显示审核备注
    $scope.tempRemark = function (obj) {
        $scope.remark = obj;
    }

    /*列表数据获取*/
    function tableList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/supplier/supplier-be-audited-list', $scope.params, function (res) {
            $scope.pageConfig.totalItems = res.data.list.count;
            $scope.listdata = res.data.list.list;
        })
    }

    $scope.saveStatus = saveParams

    // 缓存当前页面状态参数
    function saveParams() {
        let temp = JSON.stringify($scope.params);
        sessionStorage.setItem('saveStatus', temp)
    }


    // $scope.storetype_arr = [{storetype: "全部", id: -1}, {storetype: "旗舰店", id: 0}, {
    //     storetype: "专卖店", id: 1}, {storetype: "专营店", id: 2},{storetype: "自营店", id: 3}] //店铺类型

}]);