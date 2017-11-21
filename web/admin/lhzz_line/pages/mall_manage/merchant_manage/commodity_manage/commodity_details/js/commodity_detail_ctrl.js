/**
 * Created by Administrator on 2017/11/21/021.
 */
/**
 * Created by Administrator on 2017/10/25/025.
 */
app.controller('commodity_detail', ['_ajax','$rootScope','$scope','$http','$stateParams','$state','$location','$anchorScroll','$window', function (_ajax,$rootScope,$scope,$http,$stateParams,$state,$location,$anchorScroll,$window) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: 'merchant_index'
    }, {
        name: '商家管理',
        link: 'store_mag',
    },{
        name: '商品管理',
        link: -1,
    },{
        name: '商品详情',
    }];


    // console.log($stateParams.storeid)
    $stateParams.storeid == null? $scope.showd_default = true : $scope.showd_default = false;

    _ajax.get('/mall/goods-view-admin',{id:$stateParams.id},function (res) {
        $scope.good_detail = res.data.goods_view_admin;
        let price_a = Number( $scope.good_detail.purchase_price_decoration_company); //装修公司采购价
        let price_b = Number( $scope.good_detail.purchase_price_manager);            //项目经理采购价
        let price_c = Number( $scope.good_detail.purchase_price_designer);           //设计师采购价
        $scope.purchase_price_decoration_company = !price_a&&! price_b&&!price_c?'':price_a; //装修公司采购价
        $scope.purchase_price_manager = !price_a&&! price_b&&!price_c?'':price_b //项目经理采购价
        $scope.purchase_price_designer = !price_a&&! price_b&&!price_c?'':price_c ; //设计师采购价
        logisticsTemplate();
    })

    let good_partdetail;
    let logistics;
    // let goodid = good_detail.id;
    const afterservice_arr = ['上门维修','上门退货','上门换货','退货','换货'];
    const safeguard_arr = ['提供发票','上门安装'];
    $scope.storeid = $stateParams.storeid;
    $scope.offline_reason = '';

    $scope.alljudgefalse = false;


    function logisticsTemplate () {
        _ajax.get('/mall/logistics-template-view',{id:$scope.good_detail.logistics_template_id},function (res) {
            logistics = res.data.logistics_template;
            $scope.name = logistics.name;
            $scope.delivery_method = logistics.delivery_method; //快递方式
            $scope.delivery_cost_default = logistics.delivery_cost_default;//单个运费
            $scope.delivery_cost_delta = logistics.delivery_cost_delta;//没增加一件商品，运费增量
            $scope.alldistricts = logistics.district_names;
            if(logistics.district_names.length > 3 ){
                $scope.district_names = logistics.district_names.slice(0,3).join(',');
            }else{
                $scope.district_names = logistics.district_names.join(',');
            }//物流地区
        })
    }



    /*单个商品确认下架*/
    $scope.sureGoodOffline = () => {
        let data = {id: Number($scope.good_detail.id), offline_reason: $scope.offline_reason};
        _ajax.post('/mall/goods-status-toggle',data,function (res) {
            setTimeout(()=>
                $state.go("commodity.online",{id:$scope.storeid}),200);
        })
    }



    // /*判断是否显示售后*/
    // $scope.afterServiceShow = function () {
    //     for (let [key, value] of $scope.good_detail.after_sale_services.entries()) {
    //         if(afterservice_arr.indexOf(value) != -1){
    //             $scope.aaa = true;
    //         }
    //     }
    // }
    //
    // /*售后显示内容判断*/
    // $scope.afterserviceTest = function (obj) {
    //     if(afterservice_arr.indexOf(obj) != -1){
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }
    //
    // /*保障显示内容判断*/
    // $scope.safeguardTest = function (obj) {
    //     if (safeguard_arr.indexOf(obj) != -1) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    $scope.price_flag = false;




    // 时间筛选器
    $scope.$watch('purchase_price_decoration_company', function (value, oldValue) {
        if (value==undefined) {
            $scope.price_flag = true;
            return;
        };

        if (parseFloat(value) < parseFloat($scope.supplier_price)) {    // 不能小于供货价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) > parseFloat($scope.purchase_price_manager) || parseFloat(value) > parseFloat($scope.purchase_price_designer)) {  // 不能大于项目经理采购价，或者设计师采购价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) > parseFloat($scope.platform_price)) {    // 不能大于平台价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) > parseFloat($scope.market_price)) {  // 不能大于市场价
            $scope.price_flag = true;
            return;
        }

        $scope.price_flag = false;
    });


    $scope.$watch('purchase_price_manager', function (value, oldValue) {
        if (value == undefined) {
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) < parseFloat($scope.supplier_price)) {    // 不能小于供货价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) < parseFloat($scope.purchase_price_decoration_company)) { // 不能小于装修公司采购价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) > parseFloat($scope.platform_price)) {    // 不能大于平台价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) > parseFloat($scope.market_price)) {  // 不能大于市场价
            $scope.price_flag = true;
            return;
        }

        $scope.price_flag = false;
    });


    $scope.$watch('purchase_price_designer', function (value, oldValue) {
        if (value == undefined) {
            $scope.price_flag = true
        }

        if (parseFloat(value) < parseFloat($scope.supplier_price)) {    // 不能小于供货价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) < parseFloat($scope.purchase_price_decoration_company)) { // 不能小于装修公司采购价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) > parseFloat($scope.platform_price)) {    // 不能大于平台价
            $scope.price_flag = true;
            return;
        }

        if (parseFloat(value) > parseFloat($scope.market_price)) {  // 不能大于市场价
            $scope.price_flag = true;
            return;
        }

        $scope.price_flag = false;
    });

    $scope.savegood_detail = function (val,error) {
        if(val&&!$scope.price_flag){
            // $scope.savemodal = "#savesuremodal"
            let data = {
                id:$scope.good_detail.id,
                purchase_price_decoration_company:Number($scope.purchase_price_decoration_company)*100,
                purchase_price_manager: Number($scope.purchase_price_manager)*100,
                purchase_price_designer: Number($scope.purchase_price_designer)*100,
            };

            _ajax.post('/mall/goods-edit-lhzz',data,function (res) {
                $('#savesuremodal').modal('show');
            })

        }else{
            $scope.alljudgefalse = true;
            // $scope.savemodal = ""
        }

        if(!val){
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
        },200)
    }


    $scope.backPage = function () {
        history.go(-1);
    }



}]);
;