app.controller('comments', ['$rootScope', '$scope', '$stateParams', '_ajax', function ($rootScope, $scope, $stateParams, _ajax) {
    sessionStorage.setItem('shopID', $stateParams.id);
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家管理',
        link: 'store_mag'
    }, {
        name: '订单管理',
        link: 'order.all',
        params: {id: $stateParams.id}
    }, {
        name: '删除评论'
    }];
    // 分页配置
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            orderList();
        }
    };

    // 请求参数
    $scope.params = {
        supplier_id: $stateParams.id,   // 商家ID
        page: 1,                        // 当前页
        size: 12,                       // 每页显示最大数
        keyword: '',                    // 查询关键字
        time_type: 'all',               // 时间类型
        start_time: '',                 // 开始时间
        end_time: ''                    // 结束时间
    };

    // 查询事件
    $scope.search = function () {
        $scope.params.time_type = 'all';
        $scope.params.start_time = '';
        $scope.params.end_time = '';
        $scope.pageConfig.currentPage = 1;
        orderList();
    };

    // 日期筛选
    $scope.$watch('params.time_type', function (v, o) {
        if (v == o) {
            return
        }
        if (v == 'all' && $scope.params.keyword != '') {
            return
        }
        if (v != 'custom') {
            $scope.params.keyword = '';
            $scope.pageConfig.currentPage = 1;
            orderList();
        }
    });

    // 自定义时间筛选
    // 开始时间
    $scope.$watch('params.start_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.end_time != '') {
            $scope.params.keyword = '';        // 关键字查询
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
            $scope.pageConfig.currentPage = 1;
            orderList()
        }
    });

    // 列表数据请求
    function orderList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/order/delete-comment-list', $scope.params, function (res) {
            $scope.pageConfig.totalItems = res.data.count;
            $scope.list = res.data.details;
            console.log(res, '删除评论列表');
        })
    }
}])
    .controller('comments_details', ['$rootScope', '$scope', '$stateParams', '_ajax', function ($rootScope, $scope, $stateParams, _ajax) {
        $rootScope.crumbs = [{
            name: '商城管理',
            icon: 'icon-shangchengguanli',
            link: $rootScope.mall_click
        }, {
            name: '商家管理',
            link: 'store_mag'
        }, {
            name: '订单管理',
            link: 'order.all',
            params: {id: sessionStorage.getItem('shopID')}
        }, {
            name: '删除评论',
            link: -1
        }, {
            name: '删除评论详情'
        }];

        let params = {
            order_no: $stateParams.orderNo,
            sku: $stateParams.sku
        };

        $scope.params = params;

        _ajax.post('/order/delete-comment-details', params, function (res) {
            console.log(res, '删除评论详情');
            $scope.data = res.data;
        });

        // 显示评论图片原图
        $scope.showImage = function (src) {
            $scope.showImg = src;
            $('#myModal').modal('show')
        };
    }]);