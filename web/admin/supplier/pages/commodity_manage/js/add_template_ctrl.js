/**
 * Created by xl on 2017/8/7 0007.
 */
app.controller("add_template_ctrl", ['$rootScope',"$scope", "$state", "$http", "_ajax", function ($rootScope,$scope, $state, $http, _ajax) {
    $scope.getBack = function () {
      $state.go('commodity_manage', {logistics_flag: true})
    };
    $rootScope.crumbs = [{
        name: '商品管理',
        icon: 'icon-shangpinguanli',
        link: $scope.getBack
    },{
        name:'添加物流模板'
    }];
    let provincesChecked = [];  // 选中的省份
    let cityChecked = [];   // 选中的城市
    $scope.cityList = [];  // 省份
    $scope.city = [];   // 城市
    $scope.selectCity = []; // 选中的城市
    $scope.packageMail = true; // 默认不包邮
    $scope.modalName = false    // 模板名称是否出错
    $scope.params = {
        name: '',    // 同一商家模板名称不能重复
        delivery_method: 0,   // 0：快递物流，1；送货上门
        district_codes: "", // 区域码列表，以逗号分隔，形如：130100，130101
        delivery_cost_default: "",  // 默认运费，当选择“快递物流”时，必填(单位：分，需要在用户输入值基础上 X 100)
        delivery_number_default: "",  // 默认运费对应商品数量，当选择“快递物流”时，必填
        delivery_cost_delta: "",  // 增加件运费，当选择“快递物流”时，必填(同上)
        delivery_number_delta: ""   // 增加件运费对应商品数量，当选择“快递物流”时，必填
    };
    // 运费信息
    $scope.freight = {
        cost_default: "",
        number_default: "",
        cost_delta: "",
        number_delta: ""
    };

    // 获取城市列表
    $http.get('city.json').then(function (response) {
        let data = angular.copy(response.data[0][86]);
        // 循环省份
        for (let [key, value] of Object.entries(data)) {
            let provincesTemp = {
                code: key,
                name: value,
                isChecked: false,
                isSelectAll: '',
                city: []
            };
            // 循环城市
            let cityData = response.data[0][key];
            for (let [code, text] of Object.entries(cityData)) {
                let cityTemp = {
                    parentCode: key,
                    code: code,
                    name: text,
                    isChecked: false
                };
                provincesTemp.city.push(cityTemp);
            }
            $scope.cityList.push(provincesTemp);
        }
    });

    // 编辑服务地区
    $scope.editCity = function () {
        for (let obj of $scope.cityList) {
            if (provincesChecked.length === 0) {
                obj.isChecked = false;
                obj.isSelectAll = '';
                for (let o of obj.city) {
                    o.isChecked = false
                }
                continue;
            }
            for (let code of provincesChecked) {
                console.log(code);
                if (obj.code === code) {
                    obj.isChecked = true;
                    break;
                } else {
                    obj.isChecked = false;
                }
            }
        }
        $('#detate_modal').modal('show');
    };

    //点击勾选省份
    $scope.provincesFun = function (obj) {
        obj.isChecked = !obj.isChecked;
        if (obj.isChecked) {
            obj.isSelectAll = true;
            for (let o of obj.city) {
                o.isChecked = true
            }
        } else {
            obj.isSelectAll = false;
            for (let o of obj.city) {
                o.isChecked = false
            }
        }
    };

    // 获取城市
    $scope.getMoreCity = function (obj) {
        if (obj.isSelectAll === '') {
            for (let o of obj.city) {
                for (let str of cityChecked) {
                    if (o.code === str) {
                        o.isChecked = true;
                        break;
                    } else {
                        o.isChecked = false;
                    }
                }
            }
        }
        $scope.city = obj.city;
        $('#sed_modal').modal('show');
    };

    $scope.cityFun = function (obj) {
        obj.isChecked = !obj.isChecked;
        for (let o of $scope.cityList) {
            if (o.code === obj.parentCode) {
                o.isSelectAll = "";
                break;
            }
        }
    };

    // 确定选择的省份
    $scope.addCity = function () {
        for (let obj of $scope.cityList) {
            let idx = provincesChecked.indexOf(obj.code);
            if (obj.isChecked) {
                if (idx === -1) {
                    provincesChecked.push(obj.code)
                }
                if (obj.isSelectAll) {
                    for (let o of obj.city) {
                        let idx = cityChecked.indexOf(o.code);
                        let nameIdx = $scope.selectCity.indexOf(o.name);
                        if (idx === -1) {
                            cityChecked.push(o.code)
                        }
                        if (nameIdx === -1) {
                            $scope.selectCity.push(o.name)
                        }
                    }
                }
            } else {
                if (idx !== -1) {
                    provincesChecked.splice(idx, 1)
                }
                if (!obj.isSelectAll) {
                    for (let o of obj.city) {
                        let idx = cityChecked.indexOf(o.code);
                        let nameIdx = $scope.selectCity.indexOf(o.name);
                        if (idx !== -1) {
                            cityChecked.splice(idx, 1)
                        }
                        if (nameIdx !== -1) {
                            $scope.selectCity.splice(nameIdx, 1)
                        }
                    }
                }
            }
        }
    };

    // 确定选择的城市
    $scope.getCity = function () {
        for (let obj of $scope.city) {
            let index = cityChecked.indexOf(obj.code);
            let nameIdx = $scope.selectCity.indexOf(obj.name);
            if (obj.isChecked) {
                if (index === -1) {
                    cityChecked.push(obj.code)
                }
                if (nameIdx === -1) {
                    $scope.selectCity.push(obj.name)
                }
                for (let o of $scope.cityList) {
                    if (o.code === obj.parentCode) {
                        o.isChecked = true;
                        break;
                    }
                }
            } else {
                if (index !== -1) {
                    cityChecked.splice(index, 1)
                }
                if (nameIdx !== -1) {
                    $scope.selectCity.splice(obj.name)
                }
                for (let o of $scope.cityList) {
                    if (o.code === obj.parentCode) {
                        let flag = true;
                        for (let city of o.city) {
                            if (city.isChecked) {
                                flag = false;
                                break;
                            }
                        }
                        if (flag) {
                            o.isChecked = false;
                        }
                        break;
                    }
                }
            }
        }
        $('#sed_modal').modal('hide');
    };

    // 保存物流模板详情
    $scope.getReally = function () {
        let reg = /^[A-Za-z0-9\u4e00-\u9fa5]+$/;
        if($scope.params.name === ''){
            alert('请输入模板名称');
            return
        }

        if (!reg.test($scope.params.name)) {
            $scope.modalName = true
            return
        } else {
            $scope.modalName = false
        }

        if(cityChecked.length === 0){
            alert('请选择地区');
            return
        }
        if ($scope.params.delivery_method === 0) {
            if ($scope.packageMail) {
                if ($scope.freight.cost_default === '' || $scope.freight.number_default === '') {
                    alert("请将默认运费信息填写完整");
                    return
                }
                if ($scope.freight.cost_delta === '' || $scope.freight.number_delta === '') {
                    alert("请将增加件信息填写完整");
                    return
                }
                $scope.params.delivery_cost_default = $scope.freight.cost_default * 100; // 默认运费
                $scope.params.delivery_number_default = $scope.freight.number_default; // 默认运费对应商品数量
                $scope.params.delivery_cost_delta = $scope.freight.cost_delta * 100; // 增加件运费
                $scope.params.delivery_number_delta = $scope.freight.number_delta; // 增加件运费对应商品数量
            } else {
                $scope.params.delivery_cost_default = 0;
                $scope.params.delivery_number_default = 0;
                $scope.params.delivery_cost_delta = 0;
                $scope.params.delivery_number_delta = 0;
            }
        }else {
            $scope.params.delivery_cost_default = 0;
            $scope.params.delivery_number_default = 0;
            $scope.params.delivery_cost_delta = 0;
            $scope.params.delivery_number_delta = 0;
        }
        $scope.params.district_codes = cityChecked.join(',');
        _ajax.post('/mall/logistics-template-add', $scope.params, function () {
            _alert('提示', '保存成功！', function(){
                $state.go('commodity_manage',{logistics_flag:true})
            })
        });
    };

    $('#sed_modal').on('hidden.bs.modal', function () {
        $('body').addClass('modal-open');
    })
}]);
