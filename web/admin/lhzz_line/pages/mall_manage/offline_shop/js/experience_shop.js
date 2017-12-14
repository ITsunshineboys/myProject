app.controller('experience_shop', ['$scope', '$http', '$timeout', '_ajax', function ($scope, $http, $timeout, _ajax) {

    let select_district_data = [];  // 省市区数据变量

    // 选中的省市区code
    $scope.select_district = {
        province_code: 0,
        city_code: 0,
        district_code: 0
    };

    // 请求参数
    $scope.params = {
        district_code: 0, // 筛选地区编码
        status: '0',        // 状态 1:已关闭 2：已开启 0：全部
        page: 1,          // 当前页
        size: 12,         // 每页显示最大数
        keyword: ''       // 关键字
    };

    // 分页配置
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            list();
        }
    };

    // 监听省
    $scope.$watch('select_district.province_code', function (n, o) {
        console.log(n, o);
        if (n === o) return;
        $scope.city = selectProvince(n);
        $scope.select_district.city_code = 0;
        $scope.params.district_code = n;
        if ($scope.pageConfig.currentPage == 1) {
            list()
        } else {
            $scope.pageConfig.currentPage = 1
        }
    });

    // 监听市
    $scope.$watch('select_district.city_code', function (n, o) {
        if (n === o) return;
        $scope.district = selectProvince(n);
        if (n == 0) {
            $scope.params.district_code = $scope.select_district.province_code;
        } else {
            $scope.params.district_code = n;
        }
        if ($scope.pageConfig.currentPage == 1) {
            list()
        } else {
            $scope.pageConfig.currentPage = 1
        }
    });

    // 监听区
    $scope.$watch('select_district.district_code', function (n, o) {
        if (n === o) return;
        if (n == 0) {
            $scope.params.district_code = $scope.select_district.city_code;
        } else {
            $scope.params.district_code = n;
        }
        if ($scope.pageConfig.currentPage == 1) {
            list()
        } else {
            $scope.pageConfig.currentPage = 1
        }
    });

    // 获取城市数据
    $http.get('city.json').then(function (res) {
        select_district_data = res.data[0];
        // 获取省的数据
        $scope.province = selectProvince(86);
        // 默认显示为四川省-成都市
        $scope.select_district.province_code = '510000';
        $timeout(function () {
            $scope.select_district.city_code = '510100';
        })
    });

    // 开启或者关闭线下店
    $scope.open_or_close = function (status, shop_no) {
        _ajax.post('/supplier/switch-line-supplier-status', {status: status, shop_no: shop_no}, function (res) {
            list();
        })
    };

    // 删除线下体验店
    $scope.del_shop = function (shop_no) {
        _ajax.post('/supplier/del-line-supplier', {shop_no: shop_no}, function (res) {
            list();
        })
    };

    /**
     * 选择区域数据遍历
     * @param code      区域code
     */
    function selectProvince(code) {
        let temp_array = [{code: 0, value: '全部'}];
        if (code !== 0) {
            for (let key of Object.keys(select_district_data[code])) {
                let temp_obj = {
                    code: key,
                    value: select_district_data[code][key]
                };
                temp_array.push(temp_obj)
            }
        }
        return temp_array;
    }

    /**
     * 列表数据请求
     */
    function list() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/supplier/line-supplier-list', $scope.params, function (res) {
            console.log(res, "线下体验店");
            $scope.pageConfig.totalItems = res.data.count;
            $scope.list = res.data.list;
        })
    }
}]);