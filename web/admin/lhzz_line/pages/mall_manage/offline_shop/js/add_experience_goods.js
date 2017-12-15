app.controller('add_experience_goods', ['$rootScope', '$scope', '$http', '_ajax', function ($rootScope, $scope, $http, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '线下体验店',
        link: -1
    }, {
        name: '添加商品'
    }];

    // 商品编号
    $scope.shop_no = {
        sku: '',
        is_error: false,
        error_msg: ''
    };

    // 查询店铺参数
    $scope.shop_params = {
        district_code: '',  // 地区code
        keyword: ''         // 店铺名称关键字
    };

    // 添加请求参数
    $scope.params = {
        line_id: '',    // 线下体验店ID
        sku: ''         // 商品编号
    };

    // 线下店铺信息
    $scope.line_shop_info = {
        name: '',       // 店铺名称
        address: '',    // 店铺地址
        mobile: ''      // 电话
    };
    $scope.is_experience_shop = false;  //判断是否是线下体验店，默认不是

    // 查询商品编号
    $scope.search_number = function () {
        let reg = /^\d+$/;
        if ($scope.shop_no.sku === '') {
            $scope.shop_no.is_error = true;
            $scope.shop_no.error_msg = '请输入商品编号!';
            return;
        }
        if (!reg.test($scope.shop_no.sku)) {
            $scope.shop_no.is_error = true;
            $scope.shop_no.error_msg = '商品编号为数字!';
            return;
        }
        _ajax.get('/supplier/find-supplier-line-goods', {sku: $scope.shop_no.sku}, function (res) {
            console.log(res, '查询商品编号');
            if (res.code == 1078 || res.code == 1079) {
                $scope.shop_no.is_error = true;
                $scope.shop_no.error_msg = res.msg;
            }
            if (res.code == 200) {
                $scope.shop_no.is_error = false;
                $scope.is_experience_shop = true;
                $scope.shop_data = res.data;
                $scope.params.sku = $scope.shop_no.sku;
            }
        })
    };

    $scope.add_experience_goods = function () {
        if ($scope.params.line_id == '') return;
        _ajax.post('/supplier/add-line-supplier-goods', $scope.params, function (res) {
            console.log(res, "添加线下体验店商品");
            _alert('提示', '添加成功', function () {
                history.back();
            })
        })
    };

    // 地区选择
    $scope.select_district = {
        province_code: '510000',
        city_code: '510100',
        district_code: ''
    };

    let select_district_data = [];
    $http.get('city.json').then(function (res) {
        console.log(res.data[0]);
        select_district_data = res.data[0];
        $scope.province = selectProvince(86);
        $scope.city = selectProvince(510000);
        $scope.district = selectProvince(510100);
        $scope.select_district.district_code = '510104';
    });

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