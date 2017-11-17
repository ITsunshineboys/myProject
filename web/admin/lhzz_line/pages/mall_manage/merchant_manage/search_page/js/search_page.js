app.controller('searchCtrl', ['$rootScope', '$scope', '_ajax', function ($rootScope, $scope, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: 'merchant_index'
    }, {
        name: '搜索'
    }];
    // 筛选器
    $scope.orderFilter = {
        orderNum: true,     // 订单编号
        goodsNum: true,    // 商品编号
        goodsName: true,    // 商品名称
        orderMoney: true,   // 订单金额
        orderTime: true,    // 下单时间
        user: true,         // 用户
        phone: true,       // 绑定手机
        orderState: true,   // 订单状态
        exception: true,    // 异常
        comments: true,     // 评论
        details: true,      // 详情
        operation: true     // 操作
    };

    // 请求参数
    $scope.params = {
        page: 1,                        // 当前页数
        keyword: '',                    // 关键字查询
    };

    // 分页配置
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        totalItems: 0,
        onChange: function () {
            searchList();
        }
    };

    // 查询事件
    $scope.search = function () {
        if ($scope.params.keyword == '') {
            return
        }
        $scope.pageConfig.currentPage = 1;
        searchList()
    };

    function searchList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/order/find-order-list', $scope.params, function (res) {
            $scope.pageConfig.totalItems = res.data.count;
            $scope.list = res.data.details;
            console.log(res, '搜索页面');
        })
    }
}]);