/**
 * Created by xl on 2017/8/10 0010.
 */
app.controller('operation_record', ['$state', '$scope', '$stateParams', '$http', '$rootScope', '_ajax', function ($state, $scope, $stateParams, $http, $rootScope, _ajax) {
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-zhanghuguanli',
        link: $rootScope.account_click
    }, {
        name: '账户详情',
        link: -1
    }, {
        name: '过往操作记录'
    }];

    /*请求参数*/
    $scope.params = {
        user_id: $stateParams.user_id,
        page: 1,
        "sort[]": "id:3",
    };

    /*排序按钮样式控制*/
    $scope.sortStyleFunc = () => {
        return $scope.params['sort[]'].split(':')[1]
    }

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tableList();
        }
    };

    // 时间排序
    $scope.sortTime = function () {
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'id:3' ? 'id:4' : 'id:3';
        $scope.pageConfig.currentPage = 1;
        tableList();
    }


    // 账户过往操作记录
    function tableList () {
        $scope.params.page = $scope.pageConfig.currentPage;//点击页数，传对应的参数
        _ajax.get('/mall/reset-user-status-logs', $scope.params, function (res) {
            $scope.record = res.data.reset_user_status_logs.details;
            $scope.pageConfig.totalItems = res.data.reset_user_status_logs.total;
        })
    };


    // 展示关闭原因
    $scope.showReason = function (reason) {
        $scope.reason = reason;
    }
}])
