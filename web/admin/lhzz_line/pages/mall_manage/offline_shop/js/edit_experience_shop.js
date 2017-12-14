app.controller('edit_experience_shop', ['$rootScope', '$scope', '$stateParams', '$http', '_ajax', function ($rootScope, $scope, $stateParams, $http, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '线下体验店',
        link: -1
    }, {
        name: '编辑商家'
    }];

    $scope.shopNo = $stateParams.shopNo;    // 商家编号

    // 城市选择数据
    let select_district_data = [];

    // 地址和手机输入是否正确
    $scope.reg = {
        address: false,
        phone: false
    };

    // 请求参数
    $scope.params = {
        mobile: '',                  // 手机号
        district_code: '',           // 地区编码
        address: '',                 // 详细地址
        shop_no: $stateParams.shopNo // 店铺号
    };

    // 选择地区
    $scope.select_district = {
        province_code: '',
        city_code: ''
    };

    _ajax.get('/supplier/get-edit-supplier-info-by-shop-no', {shop_no: $stateParams.shopNo}, function (res) {
        console.log(res, '查询商家信息');
        if (res.code == 200) {
            $scope.shop_data = res.data;
            $scope.select_district.province_code = res.data.pro;
            $scope.select_district.city_code = res.data.ci;
            $scope.params.district_code = res.data.dis;
            $scope.params.mobile = res.data.mobile;
            $scope.params.address = res.data.address;
            $http.get('city.json').then(function (res) {
                console.log(res.data[0]);
                select_district_data = res.data[0];
                $scope.province = selectProvince(86);
                $scope.city = selectProvince($scope.select_district.province_code);
                $scope.district = selectProvince($scope.select_district.city_code);
            });
        }
    });

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
        _ajax.post('/supplier/up-line-supplier', $scope.params, function (res) {
            console.log(res, "编辑线下体验店");
            _alert('提示', '保存成功', function () {
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