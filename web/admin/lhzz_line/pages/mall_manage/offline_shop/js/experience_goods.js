app.controller('experience_goods', ['$scope', '$http', '_ajax', function ($scope, $http, _ajax) {
    let select_district_data = [];  // 省市区数据变量
    // 选中的省市区code
    $scope.select_district = {
        province_code: '510000',
        city_code: '510100',
        district_code: 0
    };

    $http.get('city.json').then(function (res) {
        select_district_data = res.data[0];
        // 获取省的数据
        selectProvince(86);
    });

    $scope.select_province = selectProvince;

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

    /**
     * 选择区域数据遍历
     * @param code      区域code
     * @param level     级别
     */
    function selectProvince(code, level) {
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
        switch (level) {
            case 1:
                $scope.city = temp_array;   // 市
                $scope.select_district.city_code = 0;
                $scope.select_district.district_code = 0;
                break;
            case 2:
                $scope.district = temp_array;   // 区
                $scope.select_district.district_code = 0;
                break;
            default:
                $scope.province = temp_array;   // 省
        }
    }

    /**
     * 列表数据请求
     */
    function list() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/supplier/line-supplier-goods-list', $scope.params, function (res) {
            console.log(res, "线下体验商品");
            $scope.pageConfig.totalItems = res.data.count;
            $scope.list = res.data.list;
        })
    }
}]);