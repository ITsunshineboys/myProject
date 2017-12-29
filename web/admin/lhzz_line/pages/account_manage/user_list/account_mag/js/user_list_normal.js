/**
 * Created by Administrator on 2017/11/27/027.
 */
app.controller('account_user_list_normal', ['$scope', '$stateParams', '_ajax', '$state', function ($scope, $stateParams, _ajax, $state) {
    /*默认参数*/
    $scope.params = {
        status: 1, //正常
        page: 1,  //当前页数
        time_type:'all',
        'sort[]': 'id:3', //排序规则 默认按注册时间降序排列
        start_time:'',
        end_time:'',
        keyword:'' //魔方号或昵称
    }
    let single_id;


    /*全选ID数组*/
    $scope.table = {
        roles: [],
        keyword:''
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
            $scope.table.roles = [];
            tableList();
        }
    }


    // 时间排序
    $scope.sortTime = function () {
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'id:3' ? 'id:4' : 'id:3';
        $scope.table.roles.length = 0;
        $scope.pageConfig.currentPage = 1;
        tableList();
    }


    // 时间筛选器
    $scope.$watch('params.time_type', function (value, oldValue) {
        $scope.table.keyword = '';
        $scope.table.roles.length = 0;
        if (value == oldValue) {
            return
        }
        if (value == 'all' && $scope.params.keyword != '') {
            return
        }
        if (value != 'custom') {
            $scope.params.start_time = '';     // 自定义开始时间
            $scope.params.end_time = '';       // 自定义结束时间
            $scope.params['sort[]'] = 'id:3';  //注册时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });



    /*搜索*/
    $scope.search = () => {
        $scope.table.roles.length = 0;
        $scope.params.time_type = 'all';   // 时间类型
        $scope.params.start_time = '';     // 自定义开始时间
        $scope.params.end_time = '';       // 自定义结束时间
        $scope.params['sort[]'] = 'id:3';  //注册时间排序
        $scope.pageConfig.currentPage = 1;
        tableList()
    }


    //自定义时间筛选
    // 开始时间
    $scope.$watch('params.start_time', function (value, oldValue) {
        $scope.table.keyword = '';
        $scope.table.roles.length = 0;
        if (value == oldValue) {
            return;
        }
        if ($scope.params.end_time != '') {
            $scope.params['sort[]'] = 'id:3';  //注册时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });

    // 结束时间
    $scope.$watch('params.end_time', function (value, oldValue) {
        $scope.table.keyword = '';
        $scope.table.roles.length = 0;
        if (value == oldValue) {
            return
        }
        if ($scope.params.start_time != '') {
            $scope.params['sort[]'] = 'id:3';  //注册时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });


    $scope.closeModal = (obj) => {
        single_id = obj;
    }


    // 单个关闭
    $scope.singleClose = () => {
        let data = {user_id: +single_id, remark: $scope.singleclose_reason};
        _ajax.post('/mall/user-status-toggle', data, function (response) {
            $scope.pageConfig.currentPage = 1;
            $scope.params.time_type = 'all';
            $scope.params['sort[]'] = 'id:3';  //注册时间排序
            $scope.params.start_time = '';
            $scope.params.end_time = '';
            $scope.table.roles.length = 0;
            $scope.singleclose_reason = '';
            tableList();
        });
    };


    // 批量关闭
    $scope.batchClose = () => {
        $scope.table.roles.length? $("#batch-modal").modal("show"):$("#warning-modal").modal("show");
    }


    // 确认批量关闭
    $scope.sureBatchClose = function () {
        let user_ids = $scope.table.roles.join(',');
        let data = {user_ids:user_ids,remark:$scope.batchclose_reason}
        _ajax.post('/mall/user-disable-batch',data,function (response) {
            $scope.pageConfig.currentPage = 1;
            $scope.params.time_type = 'all';
            $scope.params['sort[]'] = 'id:3';  //注册时间排序
            $scope.params.start_time = '';
            $scope.params.end_time = '';
            $scope.table.roles.length = 0;
            tableList();
        });
    };

    //取消关闭
    $scope.cancelClose = () => {
        $scope.batchclose_reason = '';
        $scope.singleclose_reason = '';
    }

    //查看跳转
    $scope.checkAccount = (item) => {
        sessionStorage.setItem('account_detail',JSON.stringify(item));
        $state.go('account_mag_detail');
    }


    //列表数据
    function tableList() {
        $scope.params.keyword = $scope.table.keyword;
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/mall/user-list',$scope.params,function (res) {
            $scope.pageConfig.totalItems = res.data.user_list.total;
            $scope.listdata = res.data.user_list.details;
        })
    }
}]);

