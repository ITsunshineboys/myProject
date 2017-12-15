app.controller('experience_goods', ['$scope', '$http', '$timeout', '_ajax', function ($scope, $http, $timeout, _ajax) {

    let select_district_data = [];  // 省市区数据变量

    // 搜索参数
    $scope.search_params = {
        province_code: 0,   // 省code
        city_code: 0,       // 市code
        district_code: 0,   // 区code
        keyword: ''         // 查询关键字
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

    $scope.listenProvince = listenProvince;
    $scope.listenCity = listenCity;
    $scope.listenDistrict = listenDistrict;
    $scope.statusFun = statusFun;

    // 获取城市数据
    $http.get('city.json').then(function (res) {
        select_district_data = res.data[0];
        // 获取省的数据
        $scope.province = selectProvince(86);
        // 默认显示为四川省-成都市
        $scope.search_params.province_code = '510000';
        listenProvince('510000');
        $timeout(function () {
            $scope.search_params.city_code = '510100';
            listenCity('510100');
        })
    });

    // 开启或者关闭线下店
    $scope.open_or_close = function (status, line_goods_id) {
        _ajax.post('/supplier/switch-line-supplier-goods-status', {status: status, line_goods_id: line_goods_id}, function (res) {
            list();
        })
    };

    // 删除线下体验店商品
    $scope.del_goods = function (line_goods_id) {
        _ajax.post('/supplier/del-line-supplier-goods', {line_goods_id: line_goods_id}, function (res) {
            list();
        })
    };

    $scope.search = search;

    /**
     * 搜索框查询
     */
    function search() {
        // 默认区域选择为全部、状态选择为全部
        $scope.search_params.province_code = 0;
        $scope.search_params.city_code = 0;
        $scope.search_params.district_code = 0;
        $scope.params.district_code= '0';
        $scope.params.status = '0';
        $scope.params.keyword = $scope.search_params.keyword;   // 将输入框内容赋值给查询参数
        // 判断分页是否在第一页，若在第一页请求接口，若不在第一页将分页设置为第一页
        if ($scope.pageConfig.currentPage == 1) {
            list();
        } else {
            $scope.pageConfig.currentPage = 1;
        }
    }

    /**
     * 状态查询函数
     */
    function statusFun() {
        $scope.search_params.keyword = '';  // 清空输入框内容
        $scope.params.keyword = '';
        list();
    }

    /**
     * 监听省级下拉框
     * @param code
     */
    function listenProvince(code) {
        $scope.city = selectProvince(code);
        $scope.search_params.city_code = 0;
        listenCity(0);
    }

    /**
     * 监听城市下拉框
     * @param code
     */
    function listenCity(code) {
        $scope.district = selectProvince(code);
        $scope.search_params.district_code = 0;
        listenDistrict(0);
    }

    /**
     * 监听区域下拉框
     * @param code
     */
    function listenDistrict(code) {
        if (code == 0) {
            // 判断市是否为全部选项，不是则code为城市code，是则code是省份code
            if ($scope.search_params.city_code != 0) {
                $scope.params.district_code = $scope.search_params.city_code;
            } else {
                $scope.params.district_code = $scope.search_params.province_code;
            }
        } else {
            $scope.params.district_code = code;
        }
        $scope.search_params.keyword = '';  // 清空输入框内容
        $scope.params.keyword = '';
        if ($scope.pageConfig.currentPage == 1) {
            list()
        } else {
            $scope.pageConfig.currentPage = 1
        }
    }

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
        _ajax.get('/supplier/line-supplier-goods-list', $scope.params, function (res) {
            console.log(res, "线下体验店商品");
            $scope.pageConfig.totalItems = res.data.count;
            $scope.list = res.data.list;
        })
    }
}]);