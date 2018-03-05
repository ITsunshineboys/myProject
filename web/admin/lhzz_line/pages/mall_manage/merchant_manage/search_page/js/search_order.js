app.controller('searchOrderCtrl', ['$rootScope', '$scope', '_ajax', function ($rootScope, $scope, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '搜索'
    }];

    let fromState = $rootScope.fromState_name === 'order_details' || $rootScope.fromState_name === 'sales_details';  // 判断页面是否从详情页进到当前页面
    if (!fromState) {
        sessionStorage.removeItem('saveStatus');
        sessionStorage.removeItem('isOperation')
    }

    // 筛选器
    $scope.orderFilter = {
        orderNum: true,     // 订单编号
        goodsName: true,    // 商品名称
        orderMoney: true,   // 订单金额
        orderTime: true,    // 下单时间
        user: true,         // 用户
        phone: true,        // 绑定手机
        merchantName: true, // 商家名称
        orderState: true,   // 订单状态
        exception: true,    // 异常
        details: true,      // 详情
        operation: true     // 操作
    };

    // 请求参数
    $scope.params = {
        page: 1,                        // 当前页数
        keyword: ''                     // 关键字查询
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

    let isOperation = sessionStorage.getItem('isOperation');
    if (isOperation === null) {     // 判断详情是否是操作数据后跳转到当前页面的
        let saveTempStatus = sessionStorage.getItem('saveStatus');
        if (saveTempStatus !== null) {      // 判断是否保存参数状态
            saveTempStatus = JSON.parse(saveTempStatus);
            $scope.params = saveTempStatus;
            $scope.pageConfig.currentPage = saveTempStatus.page
            searchList();
        }
    }

    // 查询事件
    $scope.search = function () {
        if ($scope.params.keyword == '') {
            return
        }
        $scope.pageConfig.currentPage = 1;
        searchList()
    };

    $scope.saveStatus = saveParams;

    function searchList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/order/find-order-list', $scope.params, function (res) {
            $scope.pageConfig.totalItems = res.data.count;
            $scope.list = res.data.details;
            console.log(res, '搜索页面');
        })
    }

    // 缓存当前页面状态参数
    function saveParams() {
        console.log($scope.params);
        let temp = JSON.stringify($scope.params);
        sessionStorage.setItem('saveStatus', temp)
    }
}]);