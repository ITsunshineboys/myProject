let commodity_detail=angular.module("commodity_detail_on_module",[]);
// onlineGoodDetail();
commodity_detail.controller("commodity_detail_on_ctrl",function (_ajax,$rootScope,$scope,$http,$stateParams,$state,$location,$anchorScroll,$window) {
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

    let gooddetail = $stateParams.onlinegood;
    let good_partdetail;
    let logistics;
    let goodid = gooddetail.id;
    const afterservice_arr = ['上门维修','上门退货','上门换货','退货','换货'];
    const safeguard_arr = ['提供发票','上门安装'];
    $scope.storeid = $stateParams.storeid;
    $scope.offline_reason = '';
    $scope.logistics_template_id = gooddetail.logistics_template_id;//物流编号
    $scope.category_title = gooddetail.category_title; //商品分类
    $scope.brand_name = gooddetail.brand_name; //品牌名称
    $scope.title = gooddetail.title;//商品名称
    $scope.description = gooddetail.description;
    $scope.cover_image = gooddetail.cover_image;//封面图
    $scope.supplier_price = gooddetail.supplier_price; //供货价格
    $scope.platform_price = gooddetail.platform_price; //平台价格
    $scope.market_price = gooddetail.market_price; //市场价格

    console.log($scope.supplier_price,$scope.platform_price,$scope.market_price)
    $scope.left_number = gooddetail.left_number; //库存
    let price_a = Number(gooddetail.purchase_price_decoration_company); //装修公司采购价
    let price_b = Number(gooddetail.purchase_price_manager);            //项目经理采购价
    let price_c = Number(gooddetail.purchase_price_designer);           //设计师采购价
    $scope.purchase_price_decoration_company = !price_a&&! price_b&&!price_c?'':price_a; //装修公司采购价
    $scope.purchase_price_manager = !price_a&&! price_b&&!price_c?'':price_b //项目经理采购价
    $scope.purchase_price_designer = !price_a&&! price_b&&!price_c?'':price_c ; //设计师采购价
    $scope.after_sale_services_desc = gooddetail.after_sale_services_desc;//售后保障
    $scope.qr_code = gooddetail.qr_code; //二维码
    $scope.alljudgefalse = false;

    onlineGoodDetail();
    logisticsTemplate();

    //已上架商品部分详情
    function onlineGoodDetail () {
        _ajax.get('/mall/goods-view',{id:Number(gooddetail.id)},function (res) {
            good_partdetail = res.data.goods_view;
            $scope.subtitle = good_partdetail.subtitle;
            $scope.style_name = good_partdetail.style_name;//风格
            $scope.series_name = good_partdetail.series_name; //系列
            $scope.attrs = good_partdetail.attrs; //属性
            $scope.images = good_partdetail.images;//图片
        })
    }
    
     function logisticsTemplate () {
         _ajax.get('/mall/logistics-template-view',{id:Number($scope.logistics_template_id)},function (res) {
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
        let data = {id: Number(goodid), offline_reason: $scope.offline_reason};
        _ajax.post('/mall/goods-status-toggle',data,function (res) {
            setTimeout(()=>
                $state.go("commodity.online",{id:$scope.storeid}),200);
        })
    }



    /*判断是否显示售后*/
    $scope.afterServiceShow = function () {
        for (let [key, value] of $scope.after_sale_services_desc.entries()) {
           if(afterservice_arr.indexOf(value) != -1){
               return true;
           }
        }
    }

    /*售后显示内容判断*/
    $scope.afterserviceTest = function (obj) {
        if(afterservice_arr.indexOf(obj) != -1){
            return true;
        }else{
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

    $scope.saveGoodDetail = function (val,error) {
        if(val&&!$scope.price_flag){
            $scope.savemodal = "#savesuremodal"
            let data = {
                id:+goodid,
                purchase_price_decoration_company:Number($scope.purchase_price_decoration_company)*100,
                purchase_price_manager: Number($scope.purchase_price_manager)*100,
                purchase_price_designer: Number($scope.purchase_price_designer)*100,
            };

            _ajax.post('/mall/goods-edit-lhzz',data,function (res) {
                $('#savesuremodal').modal('show');
            })

        }else{
            $scope.alljudgefalse = true;
            $scope.savemodal = ""
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
            $state.go("commodity.online",{id:$scope.storeid});
        },200)
    }



});
