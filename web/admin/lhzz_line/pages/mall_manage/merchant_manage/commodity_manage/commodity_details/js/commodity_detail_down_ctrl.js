let commodity_detail_down=angular.module("commodity_detail_down_module",[]);
commodity_detail_down.controller("commodity_detail_down_ctrl",function ($scope,$http,$stateParams,$state,$anchorScroll,$location,$window) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    let gooddetail = $stateParams.offlinegood;
    let good_partdetail;
    let logistics;
    let goodid = gooddetail.id;
    const afterservice_arr = ['上门维修','上门退货','上门换货','退货','换货'];
    const safeguard_arr = ['提供发票','上门安装'];
    $scope.storeid = $stateParams.storeid; // 商家id;
    $scope.offlinereason_arr = ['分类下架','品牌下架','库存不足下架','商家下架','闭店下架'];
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
    $scope.left_number = gooddetail.left_number; //库存
    $scope.purchase_price_decoration_company = !gooddetail.purchase_price_decoration_company&&!gooddetail.purchase_price_manager&&!gooddetail.purchase_price_designer?'':gooddetail.purchase_price_decoration_company; //装修公司采购价
    $scope.purchase_price_manager = !gooddetail.purchase_price_decoration_company&&!gooddetail.purchase_price_manager&&!gooddetail.purchase_price_designer?'':gooddetail.purchase_price_manager; //项目经理采购价
    $scope.purchase_price_designer = !gooddetail.purchase_price_decoration_company&&!gooddetail.purchase_price_manager&&!gooddetail.purchase_price_designer?'':gooddetail.purchase_price_designer; //设计师采购价
    $scope.after_sale_services_desc = gooddetail.after_sale_services_desc;//售后保障
    $scope.qr_code = gooddetail.qr_code; //二维码
    $scope.offline_reason = gooddetail.offline_reason;//商品下架原因
    $scope.alljudgefalse = false;


    offlineGoodDetail();
    logisticsTemplate();


    //已下架商品详情
    function offlineGoodDetail() {
        $http({
            method: "get",
            params: {id:Number(gooddetail.id)},
            url: baseUrl+"/mall/goods-view",
        }).then(function (res) {
            good_partdetail = res.data.data.goods_view;
            $scope.subtitle = good_partdetail.subtitle;
            $scope.style_name = good_partdetail.style_name;//风格
            $scope.series_name = good_partdetail.series_name; //系列
            $scope.attrs = good_partdetail.attrs; //属性
            $scope.images = good_partdetail.images;//图片
        })
    }

    function logisticsTemplate() {
        $http({
            method:"get",
            url:baseUrl+"/mall/logistics-template-view",
            params:{id:Number($scope.logistics_template_id)}
        }).then(function (res) {
            logistics = res.data.data.logistics_template;
            $scope.name = logistics.name;//物流名称
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
        if(safeguard_arr.indexOf(obj) != -1){
            return true;
        }else{
            return false;
        }
    }


    $scope.price_flag = false;

    $scope.decorationPrice = function () {
        (+$scope.purchase_price_decoration_company>=+$scope.supplier_price)&&(+$scope.purchase_price_decoration_company<=+$scope.purchase_price_manager)&&(+$scope.purchase_price_decoration_company<=$scope.purchase_price_designer)&&
        (+$scope.purchase_price_decoration_company<=$scope.platform_price)&&(+$scope.purchase_price_decoration_company<=$scope.market_price)?$scope.price_flag=false:$scope.price_flag=true;
    }

    $scope.managerPrice = () => {
        (+$scope.purchase_price_manager>=+$scope.supplier_price)&&(+$scope.purchase_price_manager>=+$scope.purchase_price_decoration_company)&&(+$scope.purchase_price_manager<=+$scope.platform_price)&&
        (+$scope.purchase_price_manager<=+$scope.market_price)?$scope.price_flag=false:$scope.price_flag=true;
    }

    $scope.designerPrice = () => {
        (+$scope.purchase_price_designer>=+$scope.supplier_price)&&(+$scope.purchase_price_designer>=+$scope.purchase_price_decoration_company)&&(+$scope.purchase_price_designer<=+$scope.platform_price)&&
        (+$scope.purchase_price_designer<=+$scope.market_price)?$scope.price_flag=false:$scope.price_flag=true;
    }

    $scope.saveGoodDetail = function (val,error) {
        if(val&&!$scope.price_flag){
            $scope.savemodal = "#savesuremodal"
            let url = baseUrl+"/mall/goods-edit-lhzz";
            let data = {
                id:+goodid,
                purchase_price_decoration_company:Number($scope.purchase_price_decoration_company)*100,
                purchase_price_manager: Number($scope.purchase_price_manager)*100,
                purchase_price_designer: Number($scope.purchase_price_designer)*100,
                offline_reason:$scope.offline_reason
            };
            $http.post(url, data, config).then(function (res) {
                console.log(res);

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
            $state.go("commodity.offline",{id:$scope.storeid});
        },200)
    }



});