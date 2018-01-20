/**
 * Created by Administrator on 2017/10/25/025.
 */
app.controller('commodity_detail', ['_ajax', '$rootScope', '$scope', '$http', '$stateParams', '$state', '$location', '$anchorScroll', '$window', '$rootScope', function (_ajax, $rootScope, $scope, $http, $stateParams, $state, $location, $anchorScroll, $window, $rootScope) {
    if ($stateParams.storeid == null && $stateParams.good_state == null) {
        $rootScope.crumbs = [{
            name: '装修申请',
            icon: 'icon-yangbanjian',
            link: 'apply_case.index'
        }, {
            name: '详情',
            link: -1,
        }, {
            name: '商品详情',
        }];
    } else if ($stateParams.storeid !== null && $stateParams.good_state == null) {
        $rootScope.crumbs = [{
            name: '商城管理',
            icon: 'icon-shangchengguanli',
            link: 'merchant_index'
        }, {
            name: '商家管理',
            link: 'store_mag',
        }, {
            name: '商品管理',
            link: -1,
        }, {
            name: '商品详情',
        }];
    } else {
        $rootScope.crumbs = [{
            name: '商城管理',
            icon: 'icon-shangchengguanli',
            link: 'merchant_index'
        }, {
            name: '搜索',
            link: -1,
        }, {
            name: '商品详情',
        }];
    }


    $scope.allprice = {
        purchase_price_decoration_company: $scope.purchase_price_decoration_company,
        purchase_price_manager: $scope.purchase_price_manager,
        purchase_price_designer: $scope.purchase_price_designer
    }

    $rootScope.fromState_name == 'commodity.online' || $stateParams.good_state == 'online' ? $scope.online_btn = true : $scope.online_btn = false;
    $rootScope.fromState_name == 'commodity.offline' || $stateParams.good_state == 'offline' ? $scope.offline_btn = true : $scope.offline_btn = false;
    $rootScope.fromState_name == 'commodity.wait' || $stateParams.good_state == 'wait' ? $scope.wait_btn = true : $scope.wait_btn = false;
    $rootScope.fromState_name == 'commodity.deleted' || $rootScope.fromState_name == 'case_detail' || $stateParams.good_state == 'deleted' ? $scope.deleted_btn = true : $scope.deleted_btn = false;


    let logistics;
    const afterservice_arr = ['上门维修', '上门退货', '上门换货', '退货', '换货'];
    const safeguard_arr = ['提供发票', '上门安装'];
    ($stateParams.storeid && $stateParams.good_state == null) && ($stateParams.storeid == null && $stateParams.good_state == true) ? $scope.show_default = true : $scope.show_default = false
    $scope.storeid = $stateParams.storeid;
    $scope.offline_reason = '';
    $scope.alljudgefalse = false;
    $scope.show_service = false; //显示售后
    $scope.price_flag = false;
    $scope.good_detail = ''


    _ajax.get('/mall/goods-view-admin', {id: $stateParams.id}, function (res) {
        $scope.good_detail = res.data.goods_view_admin;
        let price_a = Number($scope.good_detail.purchase_price_decoration_company); //装修公司采购价
        let price_b = Number($scope.good_detail.purchase_price_manager);            //项目经理采购价
        let price_c = Number($scope.good_detail.purchase_price_designer);           //设计师采购价
        $scope.allprice.purchase_price_decoration_company = !price_a && !price_b && !price_c ? '' : price_a.toFixed(2); //装修公司采购价
        $scope.allprice.purchase_price_manager = !price_a && !price_b && !price_c ? '' : price_b.toFixed(2) //项目经理采购价
        $scope.allprice.purchase_price_designer = !price_a && !price_b && !price_c ? '' : price_c.toFixed(2); //设计师采购价
        logisticsTemplate();
        afterServiceShow()
    })

    function logisticsTemplate() {
        _ajax.get('/mall/logistics-template-view', {id: $scope.good_detail.logistics_template_id}, function (res) {
            logistics = res.data.logistics_template;
            $scope.name = logistics.name;
            $scope.delivery_method = logistics.delivery_method; //快递方式
            $scope.delivery_cost_default = logistics.delivery_cost_default;//单个运费
            $scope.delivery_cost_delta = logistics.delivery_cost_delta;//没增加一件商品，运费增量
            $scope.alldistricts = logistics.district_names;
            if (logistics.district_names.length > 3) {
                $scope.district_names = logistics.district_names.slice(0, 3).join(',');
            } else {
                $scope.district_names = logistics.district_names.join(',');
            }//物流地区
        })
    }


    /*单个商品确认下架*/
    $scope.sureGoodOffline = () => {
        let data = {id: Number($scope.good_detail.id), offline_reason: $scope.offline_reason};
        _ajax.post('/mall/goods-status-toggle', data, function (res) {
            setTimeout(() =>
                $state.go("commodity.online", {id: $scope.storeid}), 200);
        })
    }


    /*判断是否显示售后*/
    function afterServiceShow() {
        for (let [key, value] of $scope.good_detail.after_sale_services.entries()) {
            if (afterservice_arr.indexOf(value) != -1) {
                $scope.show_service = true;
            }
        }
    }


    /*售后显示内容判断*/
    $scope.afterserviceTest = (obj) => {
        if (afterservice_arr.indexOf(obj) != -1) {
            return true;
        } else {
            return false;
        }
    }

    /*保障显示内容判断*/
    $scope.safeguardTest = function (obj) {
        if (safeguard_arr.indexOf(obj) != -1) {
            return true;
        } else {
            return false;
        }
    }


    // 装修公司采购价
    $scope.$watch('allprice.purchase_price_decoration_company', function (value, oldValue) {
        if (value == undefined || value == '' ||
            $scope.allprice.purchase_price_manager == undefined ||      // 项目经理采购价
            $scope.allprice.purchase_price_manager == '' ||             // 项目经理采购价
            $scope.allprice.purchase_price_designer == undefined ||     // 设计师采购价
            $scope.allprice.purchase_price_designer == '') {            // 设计师采购价
            return;
        }

        let flow1 = flowOne(parseFloat(value), parseFloat($scope.allprice.purchase_price_manager))
        let flow2 = flowTwo(parseFloat(value), parseFloat($scope.allprice.purchase_price_designer))

        if (flow1 && flow2) {
            $scope.price_flag = false;
        } else {
            $scope.price_flag = true;
        }
    });

    // 项目经理采购价
    $scope.$watch('allprice.purchase_price_manager', function (value, oldValue) {
        if (value == undefined || value == '' ||
            $scope.allprice.purchase_price_decoration_company == undefined ||   // 装修公司采购价
            $scope.allprice.purchase_price_decoration_company == '' ||          // 装修公司采购价
            $scope.allprice.purchase_price_designer == undefined ||             // 设计师采购价
            $scope.allprice.purchase_price_designer == '') {                    // 设计师采购价
            return;
        }

        let flow1 = flowOne(parseFloat($scope.allprice.purchase_price_decoration_company), parseFloat(value))
        let flow2 = flowTwo(parseFloat($scope.allprice.purchase_price_decoration_company), parseFloat($scope.allprice.purchase_price_designer))

        if (flow1 && flow2) {
            $scope.price_flag = false;
        } else {
            $scope.price_flag = true;
        }
    });

    // 设计师采购价
    $scope.$watch('allprice.purchase_price_designer', function (value, oldValue) {
        if (value == undefined || value == '' ||
            $scope.allprice.purchase_price_decoration_company == undefined ||   // 装修公司采购价
            $scope.allprice.purchase_price_decoration_company == '' ||          // 装修公司采购价
            $scope.allprice.purchase_price_manager == undefined ||              // 项目经理采购价
            $scope.allprice.purchase_price_manager == '') {                     // 项目经理采购价
            return;
        }

        let flow1 = flowOne(parseFloat($scope.allprice.purchase_price_decoration_company), parseFloat($scope.allprice.purchase_price_manager))
        let flow2 = flowTwo(parseFloat($scope.allprice.purchase_price_decoration_company), parseFloat(value))

        if (flow1 && flow2) {
            $scope.price_flag = false;
        } else {
            $scope.price_flag = true;
        }
    });

    /**
     * 供货价 <= 装修公司采购价 <= 项目经理采购价 <= 平台价 <= 市场价
     * @param purchase_price_decoration_company type: number 装修公司采购价
     * @param purchase_price_manager            type: number 项目经理采购价
     * @returns {Boolean}
     */
    function flowOne(purchase_price_decoration_company, purchase_price_manager) {
        let supplier_price = parseFloat($scope.good_detail.supplier_price), // 供货价
            platform_price = parseFloat($scope.good_detail.platform_price); // 平台价
        return supplier_price <= purchase_price_decoration_company && purchase_price_decoration_company <= purchase_price_manager && purchase_price_manager <= platform_price
    }

    /**
     * 供货价 <= 装修公司采购价 <= 设计师采购价 <= 平台价 <= 市场价
     * @param purchase_price_decoration_company type: number 装修公司采购价
     * @param purchase_price_designer           type: number 设计师采购价
     * @return {Boolean}
     */
    function flowTwo(purchase_price_decoration_company, purchase_price_designer) {
        let supplier_price = parseFloat($scope.good_detail.supplier_price), // 供货价
            platform_price = parseFloat($scope.good_detail.platform_price); // 平台价
        return supplier_price <= purchase_price_decoration_company && purchase_price_decoration_company <= purchase_price_designer && purchase_price_designer <= platform_price
    }

    $scope.saveGoodDetail = function (val, error) {
        if (val && !$scope.price_flag) {
            let data = {
                id: $scope.good_detail.id,
                purchase_price_decoration_company: Number($scope.allprice.purchase_price_decoration_company) * 100,
                purchase_price_manager: Number($scope.allprice.purchase_price_manager) * 100,
                purchase_price_designer: Number($scope.allprice.purchase_price_designer) * 100,
            };

            _ajax.post('/mall/goods-edit-lhzz', data, function (res) {
                $('#savesuremodal').modal('show');
            })

        } else {
            $scope.alljudgefalse = true;
        }

        if (!val) {
            $scope.alljudgefalse = true;
            for (let [key, value] of error.entries()) {
                if (value.$invalid) {
                    $anchorScroll.yOffset = 150;
                    $location.hash(value.$name);
                    $anchorScroll();
                    $window.document.getElementById(value.$name).focus();
                    break;
                }
            }
        }
    }

    $scope.sureSaveDetail = function () {
        setTimeout(function () {
            history.go(-1);
        }, 200)
    }

    /*下架原因清空*/
    $scope.offlineGood = function () {
        $scope.offline_reason = ''
    }


    $scope.backPage = function () {
        history.go(-1);
    }
}]);
