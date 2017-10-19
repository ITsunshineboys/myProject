angular.module("all_controller", [])
//首页控制器
    .controller("mall_index_ctrl", function ($scope,$http,$state,$stateParams) {  //首页控制器
        $http({   //轮播接口调用
            method: 'get',
            url: "http://common.cdlhzz.cn/mall/carousel"
        }).then(function successCallback(response) {
            console.log($scope.swiper_img);
            console.log(response);
            $scope.swiper_img = response.data.data.carousel;
        }, function errorCallback(response) {
            console.log(response)
        });
        $http({   //商品分类列表
            method: 'get',
            url: "http://common.cdlhzz.cn/mall/categories"
        }).then(function successCallback (response) {
            $scope.message=response.data.data.categories;
            console.log( $scope.message);
            console.log(response);
        }, function errorCallback (response) {

        });
        // 点击轮播图跳转
        $scope.getDetails = function (item) {
            console.log(item);
            if(item.from_type == 1){
                $state.go('product_details',{'id':$state.mall_id})
            }else{
                alert(121);
                $state.go(item.url)
            }
        };

        $http({   //推荐分类商品列表
            method: 'get',
            url: "http://common.cdlhzz.cn/mall/recommend-second"
        }).then(function successCallback (response) {
            console.log(response);
            $scope.commodity = response.data.data.recommend_second;
            for(let [key,value] of  $scope.commodity.entries()) {
                // console.log(value.url)
            }
            console.log( $scope.commodity);
            // console.log( $scope.mall_id);

        });
        // 点击推荐跳转商品详情
        $scope.getProduct = function (m) {
            console.log(m);
            if(m.from_type == 1){ //商铺类型
                console.log(11);
                $scope.mall_id = m.url.split('=')[1];
                $state.go('product_details',{'mall_id':$scope.mall_id});
                console.log($scope.mall_id);
            }else {              //链接类型
                console.log(222);
                $state.go('m.url')
            }
        }
    })

    //分类详情控制器
    .controller("minute_class_ctrl", function ($scope,$http ,$state,$stateParams) {
        $scope.pid = $stateParams.pid;
        //$scope.title = $stateParams.title;
        console.log($scope.pid);
        //console.log($scope.title);
        $scope.details = '';

        //左侧数据获取
        $http({
            method: 'get',
            url: 'http://common.cdlhzz.cn/mall/categories'
        }).then(function successCallback(response) {
            $scope.star = response.data.data.categories;
            console.log(response)
        });
        //首页列表点击分类列表传值id获取数据(一级id查去二级)
        $http({
            method: 'get',
            url: 'http://common.cdlhzz.cn/mall/categories?pid=' + $stateParams.pid
        }).then(function successCallback(response) {
            $scope.details = response.data.data.categories;
            //console.log(response.data.data.categories[0].id);
            console.log(response)
        });

        //首页列表点击分类列表传值id获取数据(一级id查去三级)
        $http({
            method: 'get',
            url: 'http://common.cdlhzz.cn/mall/categories-level3?pid=' + $stateParams.pid
        }).then(function successCallback(response) {
            let arr= {};
            for(let [key,value] of response.data.categories_level3.entries()){
                if(!(value.path.split(',')[1] in arr)){
                    arr[value.path.split(',')[1]] = [value]
                }else{
                    arr[value.path.split(',')[1]].push(value)
                }
            }
            $scope.commentThree = arr;
            console.log(response)
        });

        //点击左侧分类列表菜单获取右边数据
        $scope.getTitle = function (item) {
            //首页列表点击分类列表传值id获取数据(一级id查去二级)
            $scope.pid = item.id;
            $http({
                method: 'get',
                url: 'http://common.cdlhzz.cn/mall/categories?pid=' + item.id
            }).then(function successCallback(response) {
                $scope.details = response.data.data.categories;
                //console.log(response.data.data.categories[0].id);
                console.log(response)
            });

            //首页列表点击分类列表传值id获取数据(一级id查去三级)
            $http({
                method: 'get',
                url: 'http://common.cdlhzz.cn/mall/categories-level3?pid=' + item.id
            }).then(function successCallback(response) {
                let arr= {};
                for(let [key,value] of response.data.categories_level3.entries()){
                    if(!(value.path.split(',')[1] in arr)){
                        arr[value.path.split(',')[1]] = [value]
                    }else{
                        arr[value.path.split(',')[1]].push(value)
                    }
                }
                $scope.commentThree = arr;
                console.log(response)
            });
            //小区搜索
            //.controller("search_ctrl", function ($scope, $http, $state, $stateParams) {
            //
            //})
        }
    })

    //商品搜索
    .controller("commodity_search_ctrl", function ($scope,$http ,$state,$stateParams) {
        $scope.data = '';
        //$scope.title =  $stateParams.title;
        $scope.pid = $stateParams.pid;
        //判断
        $scope.getSearch = function () {
            let arr=[];
            $http({
                method:'get',
                url:"http://common.cdlhzz.cn/mall/search?keyword="+$scope.data
            }).then( function successCallback (response) {
                console.log(response);
                $scope.commoditySearch= response.data.data.search.goods;
                $scope.commoditySearchTwo= response.data.data.search.categories;
                if($scope.commoditySearch.length > 0) {
                    for (let [key,item] of response.data.data.search.goods.entries()) { //判断输入框数据和数据库内容匹配
                        if (item.title.indexOf($scope.data) != -1 && $scope.data != '') {
                            arr.push({"title": item.title,"id":item.id})
                        }
                    }
                }
                if($scope.commoditySearchTwo.length > 0){
                    for (let [key,item] of response.data.data.search.categories.entries()) { //判断输入框数据和数据库内容匹配
                        if (item.title.indexOf($scope.data) != -1 && $scope.data != '') {
                            arr.push({"title": item.title,"id":item.id})
                        }
                    }
                }

                $scope.search_data = arr;
                console.log(response)
            });
        };
        //跳转道某个商品详情
        $scope.getBackData = function (item) {
            $state.go("details",{'pid':$scope.pid,'id':item.id})
        }
    })

    //某个商品的详细列表
    .controller("details_ctrl", function ($scope,$http ,$state,$stateParams) {
        console.log($stateParams);
        $scope.id=$stateParams.id;
        $scope.pid=$stateParams.pid;
        $scope.brands = '';
        $scope.series = '';
        $scope.styles = '';
        $scope.pic_flag = true;
        $scope.pic_strat = false;
        $scope.good_pra = true;
        $scope.good_pra_filter = false;
        $scope.show_style = true;
        $scope.show_series = true;
        console.log($stateParams.id);
        //展示数据 默认展示
        $http({
            method:"get",
            url:'http://common.cdlhzz.cn/mall/category-goods?category_id='+$scope.id,
            params:{
                "sort[]":"sold_number:4"
            }
        }).then(function successCallback (response) {
            console.log(response);
            $scope.detailsList = response.data.data.category_goods;
        });
        //返回上一页
        $scope.curGoPrev = function () {
            $state.go("minute_class",{'pid':$scope.pid,'id':$scope.id,'commentThree':$scope.commentThree})
        };
        //筛选  排序
        //价格排序  升序
        $scope.changePic = function () {
            $scope.pic_strat = true;
            $scope.pic_flag = false;
            $http({
                method: 'get',
                url:'http://common.cdlhzz.cn/mall/category-goods?category_id='+$stateParams.id,
                params:{
                    "sort[]":"platform_price:3"
                }

            }).then(function successCallback(response) {
                console.log(response);
                $scope.detailsList = response.data.data.category_goods;
            });
        };
        //价格排序  降序
        $scope.changePicse = function () {
            $scope.pic_flag = true;
            $scope.pic_strat = false;
            $http({
                method: 'get',
                url:'http://common.cdlhzz.cn/mall/category-goods?category_id='+$stateParams.id,
                params:{
                    "sort[]":"platform_price:4"
                }

            }).then(function successCallback(response) {
                console.log(response);
                $scope.detailsList = response.data.data.category_goods;
            });
        };

        //好评率 降序排序
        $scope.filterPraise = function () {
            $scope.good_pra = false;
            $scope.good_pra_filter = true;
            $http({
                method: 'get',
                url:'http://common.cdlhzz.cn/mall/category-goods?category_id='+$stateParams.id,
                params:{
                    "sort[]":"favourable_comment_rate:3"
                }
            }).then(function successCallback(response) {
                console.log(response);
                $scope.detailsList = response.data.data.category_goods;
            });
        };
        //好评率  升序
        $scope.PraiseDown   = function () {
            $scope.good_pra = true;
            $scope.good_pra_filter = false;
            $http({
                method: 'get',
                url:'http://common.cdlhzz.cn/mall/category-goods?category_id='+$stateParams.id,
                params:{
                    "sort[]":"favourable_comment_rate:4"
                }
            }).then(function successCallback(response) {
                console.log(response);
                $scope.detailsList = response.data.data.category_goods;
            });
        };

        //风格  系类 品牌 接数据调用
        $http({
            method:"get",
            url:"http://common.cdlhzz.cn/mall/category-brands-styles-series",
            params:{
                category_id:+$scope.id
            }
        }).then (function successCallBack (response) {
            console.log(response);
            $scope.brands = response.data.data.category_brands_styles_series.brands;
            $scope.series = response.data.data.category_brands_styles_series.series;
            $scope.styles = response.data.data.category_brands_styles_series.styles;
            console.log($scope.brands);
            console.log($scope.series);
            console.log($scope.styles);
            // 判断是否有风格 系列 是否做展示
            //判断风格是否存在
            if($scope.styles.length > 0){
                console.log(11111);
                $scope.show_style = true;
            }else {
                console.log(22222);
                $scope.show_style = false;
            }
            //判断系列是否存在
            if($scope.series.length > 0){
                console.log(33333)
                $scope.show_series = true;
            }else {
                console.log(444444)
                $scope.show_series = false;
            }
        });


        //具体几级某个商品跳转到产品详情列表
    })

    //某个 商品详细信息展示
    .controller("product_details_ctrl", function ($scope,$http,$state,$stateParams) {  //首页控制器
        let vm = $scope.vm = {};
        $scope.id=$stateParams.id;
        $scope.title=$stateParams.title;
        $scope.description=$stateParams.description;
        $scope.platform_price=$stateParams.platform_price;
        $scope.mall_id = $stateParams.mall_id;
        console.log($scope.mall_id);
        $http({
            method:'get',
            url:"http://common.cdlhzz.cn/mall/goods-view",
            params:{
                id:+$scope.mall_id
            }
        }).then( function successCallback (response) {
            console.log(response);
            $scope.datailsShop = response.data.data.goods_view;
            $scope.detailsTitle = response.data.data.goods_view.title;
            $scope.detailsSubtitle = response.data.data.goods_view.subtitle;
            $scope.platform_price = response.data.data.goods_view.platform_price;
            $scope.after_sale_services = response.data.data.goods_view.after_sale_services;//服务类型
            $scope.supplier = response.data.data.goods_view.supplier;
            $scope.cover_image = response.data.data.goods_view.cover_image;
            $scope.shop_name = response.data.data.goods_view.supplier.shop_name;//店铺名称
            $scope.icon = response.data.data.goods_view.supplier.icon; //店铺图标
            $scope.comprehensive_score = response.data.data.goods_view.supplier.comprehensive_score; //综合评分
            $scope.goods_number = response.data.data.goods_view.supplier.goods_number; //商品数量
            $scope.brand_name = response.data.data.goods_view.brand_name; //产品参数-品牌
            $scope.left_number = response.data.data.goods_view.left_number; //产品库存
            $scope.sku = response.data.data.goods_view.sku; //产品参数-编码
            $scope.series_name = response.data.data.goods_view.series_name; //产品参数-系列
            $scope.style_name = response.data.data.goods_view.series_name; //产品参数-风格
            $scope.attrs = response.data.data.goods_view.attrs; //产品参数-属性
            $scope.description = response.data.data.goods_view.description; //产品参数-属性
            $scope.images = response.data.data.goods_view.images; //产品参数-属性

            $scope.style_parameter = false;
            $scope.series_parameter = false;
            // 判断是否存在系列
            if($scope.series_name == '' ){
                $scope.style_parameter = false;
            }else {
                $scope.style_parameter = true;
            }
            // 判断是否存在风格
            if($scope.series_parameter == ''){
                $scope.series_parameter = false;
            }else {
                $scope.series_parameter = true;
            }
            // 判断服务存在类型
            $scope.on_site     = false;
            $scope.changeGoods = false;
            $scope.returnGoods = false;
            $scope.changeMore  = false;
            $scope.returnMore  = false;
            $scope.getInvoice  = false;
            $scope.doorPay     = false;
            for( let [key,vaule] of $scope.after_sale_services.entries()){
                if(vaule == "上门维修"){
                    $scope.on_site     = true;
                }
                else if(vaule == "上门换货"){
                    $scope.changeGoods = true;
                }
                else if(vaule == "上门退货"){
                    $scope.returnGoods = true;
                }
                else if(vaule == "换货"){
                    $scope.changeMore  = true;
                }
                else if(vaule == "退货"){
                    $scope.returnMore  = true;
                }
                else if(vaule == "提供发票"){
                    $scope.getInvoice  = true;
                }
                else if(vaule == "上门安装"){
                    $scope.doorPay     = true
                }
                console.log(vaule);
            }

        });
        // 购买数量=======点击加减
        $scope.shopNum = 1;
        $scope.addNumber = function () { //点击==>加
            if($scope.shopNum <= $scope.left_number){
                $scope.shopNum++
            }else {
                $scope.shopNum = $scope.left_number;
            }
        };
        $scope.reduceNumber =function () { //点击==>减
            if($scope.shopNum > 0){
                $scope.shopNum--
            }else {
                $scope.shopNum = 0;
            }
        };
        // 监听购买数量输入是否大于库存
        $scope.getQuantity = function () {
            if($scope.shopNum > $scope.left_number){
                $scope.shopNum =  $scope.left_number
            }
        };


        // 跳转到订单页面
        $scope.getOrder =function () {
            setTimeout(function () {
                $state.go('order_commodity')
            },300)
        }
    })

    //店铺首页和全部商品
    .controller("shop_front_ctrl", function ($scope,$http,$state,$stateParams) {  //首页控制器
        let vm = $scope.vm = {};
        //获取商品列表
        console.log($stateParams);
        $scope.id=$stateParams.id;
        $scope.pid=$stateParams.pid;
        $scope.brands = '';
        $scope.series = '';
        $scope.styles = '';
        $scope.orderType = 'sold_number';
        $scope.order = '-';
        
        console.log($stateParams.id);
        $http({
            method:"get",
            url:'http://common.cdlhzz.cn/mall/category-goods?category_id='+$stateParams.id
        }).then(function successCallback (response) {
            $scope.detailsList = response.data.data.category_goods;
            console.log(response)
        });
        $http({   //分类商品列表
            method: 'get',
            url: "http://common.cdlhzz.cn/mall/recommend-second"
        }).then(function successCallback (response) {
            $scope.commodity=response.data.data.recommend_second;
            console.log( $scope.commodity);
        }, function errorCallback(response) {

        });
        // 点击跳转到首页
        $scope.getHome = function () {
            $state.go("home")
        }

    })

    //确认订单
    .controller('order_commodity_ctrl',function ($scope,$http,$state,$stateParams) {

    })

    //发票信息
    .controller('invoice_ctrl',function($scope,$http,$state,$stateParams){

    })

    // 支付成功
    .controller('pay_success_ctrl',function($scope,$http,$state,$stateParams){

    })

    //断网提示
    .controller('cut_net_ctrl',function($scope,$http,$state,$stateParams){

    })

    //=================分割 飞机线========================
    .directive("swiper", function () {
        return {
            restrict: "EA",
            link: function (scope, element, attrs) {
                var mySwiper = new Swiper('.swiper-container', {
                    direction:'horizontal',
                    loop: true,
                    autoplay: 1000,

                    // 分页器
                    pagination : '.swiper-pagination',
                    paginationClickable :true,
                })
            }
        }
    });