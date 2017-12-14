app.controller('add_experience_shop', ['$rootScope', '$scope', '$http', '_ajax', function ($rootScope, $scope, $http, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '线下体验店',
        link: -1
    }, {
        name: '添加商家'
    }];

    // 商家编号
    $scope.shop_no = {
        number: '',
        is_error: false,
        error_msg: ''
    };

    // 地址和手机输入是否正确
    $scope.reg = {
        address: false,
        phone: false
    };

    // 请求参数
    $scope.params = {
        mobile: '',               // 手机号
        district_code: '510104',  // 地区编码
        address: '',              // 详细地址
        shop_no: ''               // 店铺号
    };

    $scope.is_experience_shop = false;  //判断是否是线下体验店，默认不是

    // 查询商家编号
    $scope.search_number = function () {
        let reg = /^\d+$/;
        if ($scope.shop_no.number === '') {
            $scope.shop_no.is_error = true;
            $scope.shop_no.error_msg = '请输入商家编号!';
            return;
        }
        if (!reg.test($scope.shop_no.number)) {
            $scope.shop_no.is_error = true;
            $scope.shop_no.error_msg = '商家编号为数字!';
            return;
        }
        _ajax.get('/supplier/get-supplier-info-by-shop-no', {shop_no: $scope.shop_no.number}, function (res) {
            console.log(res, '查询商家编号');
            if (res.code == 1076 || res.code == 1077) {
                $scope.shop_no.is_error = true;
                $scope.shop_no.error_msg = res.msg;
            }
            if (res.code == 200) {
                $scope.shop_no.is_error = false;
                $scope.is_experience_shop = true;
                $scope.shop_data = res.data;
                $scope.params.shop_no = $scope.shop_no.number;
            }
        })
    };

    $scope.add_experience_shop = function () {
        let reg =  /^1[3|4|5|7|8][0-9]{9}$/; // 手机号码正则
        if ($scope.params.address == '') {
            $scope.reg.address = true;
            return;
        } else {
            $scope.reg.address = false;
        }
        if (!reg.test($scope.params.mobile)) {
            $scope.reg.phone = true;
            return;
        } else {
            $scope.reg.phone = false;
        }
        _ajax.post('/supplier/add-line-supplier', $scope.params, function (res) {
            console.log(res, "添加线下体验店");
            _alert('提示', '添加成功', function () {
                history.back();
            })
        })
    };

    $scope.select_district = {
        province_code: '510000',
        city_code: '510100'
    };

    let select_district_data = [];
    $http.get('city.json').then(function (res) {
        console.log(res.data[0]);
        select_district_data = res.data[0];
        $scope.province = selectProvince(86);
        $scope.city = selectProvince(510000);
        $scope.district = selectProvince(510100);
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
        $scope.params.district_code = $scope.district[0].code;
    };

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