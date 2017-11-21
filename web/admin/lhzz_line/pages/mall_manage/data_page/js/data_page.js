// 商城数据
app.controller('mallDataCtrl', ['$rootScope', '$scope', '$stateParams', '_ajax', function ($rootScope, $scope, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商城数据'
    }];
    //请求参数
    $scope.params = {
        time_type: 'all',
        start_time: '',
        end_time: '',
        page: 1
    };

    // 分页配置
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            dataList();
        }
    };

    // 时间筛选器
    $scope.$watch('params.time_type', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if (value != 'custom') {
            $scope.params.start_time = '';     // 自定义开始时间
            $scope.params.end_time = '';       // 自定义结束时间
            $scope.pageConfig.currentPage = 1;
            dataList();
        }
    });

    //自定义时间筛选
    // 开始时间
    $scope.$watch('params.start_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.end_time != '') {
            $scope.pageConfig.currentPage = 1;
            dataList()
        }
    });
    // 结束时间
    $scope.$watch('params.end_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.start_time != '') {
            $scope.pageConfig.currentPage = 1;
            dataList()
        }
    });

    // 列表数据
    function dataList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/mall/shop-data', $scope.params, function (res) {
            console.log(res);
            $scope.pageConfig.totalItems = res.data.shop_data.total;
            $scope.data = res.data.shop_data;
        })
    }
}])
// 店铺数据
    .controller('storeDataCtrl', ['$rootScope', '$scope', '$stateParams', '_ajax', function ($rootScope, $scope, $stateParams, _ajax) {
        $rootScope.crumbs = [{
            name: '商城管理',
            icon: 'icon-shangchengguanli',
            link: $rootScope.mall_click
        }, {
            name: '商家管理',
            link: 'store_mag'
        }, {
            name: '店铺数据'
        }];
        //请求参数
        $scope.params = {
            supplier_id: $stateParams.id,
            time_type: 'all',
            start_time: '',
            end_time: '',
            page: 1
        };

        // 分页配置
        $scope.pageConfig = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                dataList();
            }
        };

        // 时间筛选器
        $scope.$watch('params.time_type', function (value, oldValue) {
            if (value == oldValue) {
                return
            }
            if (value != 'custom') {
                $scope.params.start_time = '';     // 自定义开始时间
                $scope.params.end_time = '';       // 自定义结束时间
                $scope.pageConfig.currentPage = 1;
                dataList();
            }
        });

        //自定义时间筛选
        // 开始时间
        $scope.$watch('params.start_time', function (value, oldValue) {
            if (value == oldValue) {
                return
            }
            if ($scope.params.end_time != '') {
                $scope.pageConfig.currentPage = 1;
                dataList()
            }
        });
        // 结束时间
        $scope.$watch('params.end_time', function (value, oldValue) {
            if (value == oldValue) {
                return
            }
            if ($scope.params.start_time != '') {
                $scope.pageConfig.currentPage = 1;
                dataList()
            }
        });

        // 列表数据
        function dataList() {
            $scope.params.page = $scope.pageConfig.currentPage;
            _ajax.get('/mall/shop-data', $scope.params, function (res) {
                console.log(res);
                $scope.pageConfig.totalItems = res.data.shop_data.total;
                $scope.data = res.data.shop_data;
            })
        }
    }]);