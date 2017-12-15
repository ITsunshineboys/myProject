app.controller('edit_experience_goods', ['$rootScope', '$scope', '$stateParams', '$http', '_ajax', function ($rootScope, $scope, $stateParams, $http, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '线下体验店',
        link: -1
    }, {
        name: '编辑商品'
    }];

    $scope.sku = $stateParams.sku;

    // 查询店铺参数
    $scope.shop_params = {
        district_code: '',  // 地区code
        keyword: ''         // 店铺名称关键字
    };

    // 添加请求参数
    $scope.params = {
        line_id: '',            // 线下体验店ID
        sku: $stateParams.sku,  // 商品编号
        status: ''              // 开启 or 关闭
    };

    // 线下店铺信息
    $scope.line_shop_info = {
        name: '',       // 店铺名称
        address: '',    // 店铺地址
        mobile: ''      // 电话
    };

    // 地区选择
    $scope.select_district = {
        province_code: '',
        city_code: '',
        district_code: ''
    };

    // 城市数据
    let select_district_data = [];

    // 获取商品信息
    _ajax.get('/supplier/get-up-supplier-line-goods', {line_id: $stateParams.id, sku: $stateParams.sku}, function (res) {
        console.log(res, '线下体验店商品信息');
        let data = res.data;
        $scope.shop_data = data;
        // 展示省市区
        $scope.select_district.province_code = data.pro;
        $scope.select_district.city_code = data.ci;
        $scope.select_district.district_code = data.dis;
        $scope.shop_params.district_code = data.dis;    // 默认把区域code赋值给请求参数
        // 展示店铺信息
        $scope.line_shop_info.name = data.line_shop_name;
        $scope.line_shop_info.address = data.district;
        $scope.line_shop_info.mobile = data.mobile;
        $scope.params.status = data.status;     // 将商品是否开启赋值给请求参数
        $scope.params.line_id = data.line_id;   // 默认线下店铺ID
        // 获取城市数据
        $http.get('city.json').then(function (res) {
            select_district_data = res.data[0];
            $scope.province = selectProvince(86);
            $scope.city = selectProvince(data.pro);
            $scope.district = selectProvince(data.ci);
            $scope.select_district.district_code = data.dis;
        });

        shopList();

        // 监听地区code 变化时请求店铺数据
        $scope.$watch('select_district.district_code', function (n, o) {
            if (n === o) return;
            $scope.shop_params.district_code = n;
            $scope.shop_params.keyword = '';
            shopList('district');
        });

        // 监听店铺关键字搜索 刷新店铺列表
        $scope.$watch('shop_params.keyword', function (o, n) {
            if (n === o) return;
            shopList();
        });
    });

    // 保存商品
    $scope.edit_experience_goods = function () {
        if ($scope.params.line_id == '') return;
        _ajax.post('/supplier/up-line-supplier-goods', $scope.params, function (res) {
            console.log(res, "编辑线下体验店商品");
            _alert('提示', '添加成功', function () {
                history.back();
            })
        })
    };

    // 选择省获取城市和区数据
    $scope.cityList = function () {
        $scope.city = selectProvince($scope.select_district.province_code); // 城市数据
        $scope.select_district.city_code = $scope.city[0].code;     // 默认为数据第一个城市
        $scope.district = selectProvince($scope.select_district.city_code); // 区数据
        $scope.params.district_code = $scope.district[0].code; // 默认为数据第一个区
    };

    // 选择城市获取区数据
    $scope.districtList = function () {
        $scope.district = selectProvince($scope.select_district.city_code);
        $scope.select_district.district_code = $scope.district[0].code;
    };

    $scope.select_shop = shopInfo;

    /**
     * 选择的线下店信息
     * @param obj
     */
    function shopInfo(obj) {
        $scope.params.line_id = obj.line_id;
        $scope.line_shop_info.name = obj.shop_name;
        $scope.line_shop_info.address = obj.district;
        $scope.line_shop_info.mobile = obj.mobile;
    }

    /**
     * 选择线下店列表
     * @param type  进入页面时传入字符串 district
     */
    function shopList(type) {
        _ajax.get('/supplier/find-supplier-line-by-district-code', $scope.shop_params, function (res) {
            let data = res.data;
            $scope.shop_list = data;
            if (type == 'district') {
                if (data.length === 0) {
                    $scope.params.line_id = '';
                    $scope.line_shop_info.name = '无';
                    $scope.line_shop_info.address = '';
                    $scope.line_shop_info.mobile = '';
                } else {
                    $scope.line_shop_info.name = data[0].shop_name;
                    $scope.line_shop_info.address = data[0].district;
                    $scope.line_shop_info.mobile = data[0].mobile;
                    $scope.params.line_id = data[0].line_id;
                }
            }
        })
    }

    /**
     * 选择区域数据遍历
     * @param code      区域code
     */
    function selectProvince(code) {
        let temp_array = [];
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
}]);