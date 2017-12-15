app.controller('order_completed',['$scope', '$stateParams', '_ajax', function ($scope, $stateParams, _ajax) {
// 筛选器
    $scope.orderFilter = {
        orderNum: true,     // 订单编号
        goodsNum: false,    // 商品编号
        goodsName: true,    // 商品名称
        orderMoney: true,   // 订单金额
        orderTime: true,    // 下单时间
        user: true,         // 用户
        phone: false,       // 绑定手机
        orderState: true,   // 订单状态
        exception: false,    // 异常
        comments: true,     // 评论
        details: true,      // 详情
        operation: false     // 操作
    };

    // 请求参数
    $scope.params = {
        supplier_id: $stateParams.id,   // 商家ID
        page: 1,                        // 当前页数
        time_type: 'all',               // 时间类型
        keyword: '',                    // 关键字查询
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
        sort_money: '',                  // 订单金额排序
        sort_time: 2,                  // 下单时间排序
        type: 'completed'               // 订单类型
    };

    // 分页配置
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            orderList();
        }
    };

    // 时间筛选器
    $scope.$watch('params.time_type', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if (value == 'all' && $scope.params.keyword != '') {
            return
        }
        if (value != 'custom') {
            $scope.params.keyword = '';        // 关键字查询
            $scope.params.start_time = '';     // 自定义开始时间
            $scope.params.end_time = '';       // 自定义结束时间
            $scope.params.sort_money = '';      // 订单金额排序
            $scope.params.sort_time = 2;      // 下单时间排序
            $scope.pageConfig.currentPage = 1;
            orderList();
        }
    });

    // 查询事件
    $scope.search = function () {
        $scope.params.time_type = 'all';   // 时间类型
        $scope.params.start_time = '';     // 自定义开始时间
        $scope.params.end_time = '';       // 自定义结束时间
        $scope.params.sort_money = '';      // 订单金额排序
        $scope.params.sort_time = 2;      // 下单时间排序
        $scope.pageConfig.currentPage = 1;
        orderList()
    };

    //自定义时间筛选
    // 开始时间
    $scope.$watch('params.start_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.end_time != '') {
            $scope.params.keyword = '';        // 关键字查询
            $scope.params.sort_money = '';      // 订单金额排序
            $scope.params.sort_time = 2;      // 下单时间排序
            $scope.pageConfig.currentPage = 1;
            orderList()
        }
    });
    // 结束时间
    $scope.$watch('params.end_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.start_time != '') {
            $scope.params.keyword = '';        // 关键字查询
            $scope.params.sort_money = '';      // 订单金额排序
            $scope.params.sort_time = 2;      // 下单时间排序
            $scope.pageConfig.currentPage = 1;
            orderList()
        }
    });

    // 订单金额排序
    $scope.sortMoney = function () {
        $scope.params.sort_money = $scope.params.sort_money == 2 ? 1 : 2;
        $scope.params.sort_time = '';      // 下单时间排序
        $scope.pageConfig.currentPage = 1;
        orderList();
    };
    // 下单时间排序
    $scope.sortTime = function () {
        $scope.params.sort_time = $scope.params.sort_time == 2 ? 1 : 2;
        $scope.params.sort_money = '';      // 订单金额排序
        $scope.pageConfig.currentPage = 1;
        orderList();
    };

    // 列表数据请求
    function orderList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/order/find-order-list', $scope.params, function (res) {
            $scope.pageConfig.totalItems = res.data.count;
            $scope.list = res.data.details;
        })
    }
}]);