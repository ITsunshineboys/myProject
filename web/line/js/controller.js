angular.module("all_controller", [])
//首页控制器
    .controller("mall_index_ctrl", function ($scope,$http,$state,$stateParams) {  //首页控制器
        $scope.search_flag = false;
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
                $scope.id = m.id;
                $state.go('product_details',{'mall_id':$scope.mall_id,'id':$scope.id});
                console.log($scope.mall_id);
                console.log($scope.id);
            }else {              //链接类型
                console.log(222);
                $state.go('m.url')
            }
        }
    })

    //分类详情控制器
    .controller("minute_class_ctrl", function ($scope,$http ,$state,$stateParams) {
        $scope.pid = $stateParams.pid;
        $scope.id = $stateParams.id;
        $scope.search_flag = true;
        console.log($scope.pid);
        console.log($scope.search_flag);
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
            url: 'http://common.cdlhzz.cn/mall/categories?pid=' + $scope.pid
        }).then(function successCallback(response) {
            console.log(response)
            $scope.details = response.data.data.categories;
            //console.log(response.data.data.categories[0].id);

        });

        //首页列表点击分类列表传值id获取数据(一级id查去三级)
        $http({
            method: 'get',
            url: 'http://common.cdlhzz.cn/mall/categories-level3?pid=' + $scope.pid
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
        $scope.id =  $stateParams.id;
        $scope.pid = $stateParams.pid;
        $scope.search_flag = $stateParams.search_flag;
        $scope.search_flag_details = $stateParams.search_flag_details;
        console.log($scope.search_flag);
        console.log('详情变量：'+$scope.search_flag_details);
        console.log($scope.pid);
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
        // 点击取消回到首页
        $scope.goPrevIndex = function () {
            console.log(123);
            if($scope.search_flag == true){
                $state.go("minute_class",{'pid':$scope.pid,'id':$scope.id,'commentThree':$scope.commentThree})
            }
            if($scope.search_flag_details == true){
                $state.go('details',{'id':$scope.id})
            }
            if($scope.search_flag != true && $scope.search_flag_details != true){
                $state.go("home")
            }
        };
        //跳转道某个商品详情
        $scope.getBackData = function (item) {
            $state.go("details",{'pid':$scope.pid,'id':item.id})
        }
    })

    //某个商品的详细列表
    .controller("details_ctrl", function ($scope,$http ,$state,$stateParams) {
        console.log($stateParams);
        $scope.id  = $stateParams.id;
        $scope.pid = $stateParams.pid;
        console.log($scope.id);
        console.log($scope.pid);
        $scope.brands = '';
        $scope.series = '';
        $scope.styles = '';
        $scope.pic_flag = true;
        $scope.pic_strat = false;
        $scope.good_pra = true;
        $scope.good_pra_filter = false;
        $scope.show_style = true;
        $scope.show_series = true;
        $scope.search_flag_details = true; //判断搜素页面是否是从详情页面进的变量

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
        // 点击产品列表商品跳转到产品详情页面
        $scope.getDetailsProduct = function (item) {
            console.log(item);
            console.log($scope.id);
            $scope.mall_id = item.id;
            $state.go('product_details',{mall_id:$scope.mall_id,id:$scope.id})
        };
        //返回上一页
        // $scope.curGoPrev = function () {
        //     $state.go("minute_class",{'pid':$scope.pid,'id':$scope.id,'commentThree':$scope.commentThree})
        // };
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
        $scope.datailsShop = $stateParams.datailsShop;
        // $scope.title=$stateParams.title;
        // $scope.description=$stateParams.description;
        // $scope.platform_price=$stateParams.platform_price;
        $scope.mall_id = $stateParams.mall_id;
        console.log( $scope.mall_id);
        console.log( $scope.id);
        console.log($stateParams);
        $http({
            method:'get',
            url:"http://common.cdlhzz.cn/mall/goods-view",
            params:{
                id:+$scope.mall_id
            }
        }).then( function successCallback (response) {
            console.log(response);
            $scope.datailsShop = response.data.data.goods_view;
            $scope.supplier_id = response.data.data.goods_view.supplier.id;
            console.log($scope.datailsShop);
            console.log($scope.supplier_id);
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
            $scope.show_service = true;
            for( let [key,vaule] of $scope.datailsShop.after_sale_services.entries()){
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
            // 判断售后服务 不存在时  不显示售后
            if($scope.on_site == false && $scope.changeGoods == false && $scope.returnGoods == false && $scope.changeMore  == false && $scope.returnMore  == false){
                $scope.show_service = false;
            }

        });
        // 购买数量=======点击加减
        $scope.shopNum = 1;
        $scope.addNumber = function () { //点击==>加
            if($scope.shopNum < $scope.datailsShop.left_number){
                $scope.shopNum++
            }else {
                $scope.shopNum = $scope.datailsShop.left_number;
            }
        };
        $scope.reduceNumber = function () { //点击==>减
            if($scope.shopNum > 0){
                $scope.shopNum--
            }else {
                $scope.shopNum = 0;
            }
        };
        // 监听购买数量输入是否大于库存
        $scope.getQuantity = function () {
            if($scope.shopNum > $scope.datailsShop.left_number){
                $scope.shopNum =  $scope.datailsShop.left_number
            }
        };

        // 跳转到订单页面
        $scope.getOrder =function () {
            console.log($scope.id);
            console.log($scope.shopNum);
            setTimeout(function () {
                $state.go('order_commodity',{mall_id:$scope.mall_id,shopNum:$scope.shopNum,supplier_id:$scope.supplier_id})
            },300)
        }
    })

    //店铺首页和全部商品
    .controller("shop_front_ctrl", function ($scope,$http,$state,$stateParams) {  //首页控制器
        let vm = $scope.vm = {};
        //获取商品列表
        console.log($stateParams);
        $scope.id  = $stateParams.id;
        $scope.pid = $stateParams.pid;
        $scope.mall_id = $stateParams.mall_id;
        $scope.supplier_id = $stateParams.datailsShop.supplier.id;
        $scope.datailsShop = $stateParams.datailsShop;
        $scope.brands = '';
        $scope.series = '';
        $scope.styles = '';
        $scope.good_pic_up = false;
        $scope.good_pic_down = true;
        $scope.praise_up = true;
        $scope.praise_down = false;
        console.log($scope.id);
        console.log($scope.supplier_id);
        $http({
            method:"get",
            url:'http://common.cdlhzz.cn/supplier/index?supplier_id='+$scope.supplier_id
        }).then(function successCallback (response) {
            console.log(response);
            $scope.swiperList = response.data.data.index.carousel;//轮播图
            $scope.follower_number = response.data.data.index.follower_number; //粉丝数量
            $scope.icon = response.data.data.index.icon; //店铺图片
            $scope.shop_name = response.data.data.index.shop_name; //店铺名称
            console.log($scope.detailsList)
        });
        $http({   //店铺首页推荐列表
            method: 'get',
            url: "http://common.cdlhzz.cn/supplier/recommend-second",
            params:{
                supplier_id:+$scope.supplier_id
            }
        }).then(function successCallback (response) {
            console.log(response);
            $scope.recommendList = response.data.data.recommend_second;
        });
        $http({   //店铺全部商品列表
            method: 'get',
            url: "http://common.cdlhzz.cn/supplier/goods",
            params:{
                supplier_id:+$scope.supplier_id
            }
        }).then(function successCallback (response) {
            console.log(response);
            $scope.supplier_goods=response.data.data.supplier_goods;
        });
        // 点击商品判断跳转商品详情
        $scope.getProductMore = function (item) {
            console.log(item);
            $scope.mall_id = item.url.split('=')[1];
            $state.go("product_details",{mall_id:$scope.mall_id,datailsShop:$scope.datailsShop});
            console.log( $scope.mall_id)
        };
        // 点击上下排序
        //价格排序  升序
        $scope.filterPicUp = function () {
            $scope.good_pic_up = true;
            $scope.good_pic_down = false;
            $http({
                method: 'get',
                url:'http://common.cdlhzz.cn/supplier/goods',
                params:{
                    supplier_id:+$scope.supplier_id,
                    "sort[]":"platform_price:3"
                }
            }).then(function successCallback(response) {
                console.log(response);
                $scope.supplier_goods=response.data.data.supplier_goods;
            });
        };
        // 价格降序
        $scope.filterPicDown = function () {
            $scope.good_pic_up = false;
            $scope.good_pic_down = true;
            $http({
                method: 'get',
                url:'http://common.cdlhzz.cn/supplier/goods',
                params:{
                    supplier_id:+$scope.supplier_id,
                    "sort[]":"platform_price:4"
                }
            }).then(function successCallback(response) {
                console.log(response);
                $scope.supplier_goods=response.data.data.supplier_goods;
            });
        };
        //销量排序
        $scope.filterSalesUp = function () {
            $scope.praise_up = true;
            $scope.praise_down = false;
            $http({
                method: 'get',
                url:'http://common.cdlhzz.cn/supplier/goods',
                params:{
                    supplier_id:+$scope.supplier_id,
                    "sort[]":"favourable_comment_rate:3"
                }

            }).then(function successCallback(response) {
                console.log(response);
                $scope.supplier_goods=response.data.data.supplier_goods;
            });
        };
        $scope.filterSalesDown = function () {
            $scope.praise_up = false;
            $scope.praise_up = true;
            $http({
                method: 'get',
                url:'http://common.cdlhzz.cn/supplier/goods',
                params:{
                    supplier_id:+$scope.supplier_id,
                    "sort[]":"favourable_comment_rate:4"
                }

            }).then(function successCallback(response) {
                console.log(response);
                $scope.supplier_goods=response.data.data.supplier_goods;
            });
        };

        // 店铺简介
        $http({
            method: 'get',
            url: "http://common.cdlhzz.cn/supplier/view",
            params:{
                id:+$scope.supplier_id
            }
        }).then(function successCallback (response) {
            console.log(response);
            $scope.supplier_view = response.data.data.supplier_view;
            $scope.t_icon = response.data.data.supplier_view.icon;
            $scope.t_shop_name = response.data.data.supplier_view.shop_name;//店铺名称
            $scope.shop_no = response.data.data.supplier_view.shop_no;//店铺编号
            $scope.open_shop_time = response.data.data.supplier_view.open_shop_time;//开店时间
            $scope.comprehensive_score = response.data.data.supplier_view.comprehensive_score; //综合评分
            $scope.store_service_score = response.data.data.supplier_view.store_service_score; //店家服务
            $scope.logistics_speed_score = response.data.data.supplier_view.logistics_speed_score; //物流速度
            $scope.delivery_service_score = response.data.data.supplier_view.delivery_service_score; //配送服务
            $scope.quality_guarantee_deposit = response.data.data.supplier_view.quality_guarantee_deposit; //资质
            console.log($scope.t_shop_name)
        });
        // 点击跳转到首页
        $scope.getHome = function () {
            $state.go("home")
        }
    })

    //发票信息
    .controller('invoice_ctrl',function($scope,$http,$state,$stateParams){
        $scope.harvestAddress  = $stateParams.harvestAddress;
        $scope.harvestName     = $stateParams.harvestName;
        $scope.harvestNum      = $stateParams.harvestNum;
        $scope.show_address    = $stateParams.show_address;
        $scope.show_harvest    = $stateParams.show_harvest;
        $scope.mall_id         = $stateParams.mall_id;
        $scope.shopNum         = $stateParams.shopNum;
        $scope.supplier_id     = $stateParams.supplier_id;
        $scope.address_id     = $stateParams.address_id;

        $scope.consigneeName   = $stateParams.consigneeName;
        $scope.mobile          = $stateParams.mobile;
        $scope.districtMore    = $stateParams.districtMore;
        $scope.regionMore      = $stateParams.regionMore;
        $scope.leaveMessage    = $stateParams.leaveMessage;
        $scope.invoice_name    = $stateParams.invoice_name; //纳税人名称抬头
        $scope.invoice_number  = $stateParams.invoice_number;//纳税人识别号
        $scope.invoice_id      = $stateParams.invoice_id;//纳税人识别号  id
        $scope.choose_personal = true;
        $scope.choose_company  = false;
        $scope.invoice_name    = ''; //纳税人名称抬头
        $scope.invoice_number  = '';//纳税人识别号
        $scope.invoice_model   = '';
        $scope.contentInvoice  = '';
        alert( $scope.supplier_id );
        alert( $scope.invoice_id );
        alert( $scope.address_id );

        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        let numMap = /^(?![0-9]+$)(?![A-Z]+$)[0-9A-Z]{18,}/;
        // 点击返回按钮传参到上一个页面
        $scope.getOrderPre = function () {
            console.log(123);
            if($scope.show_address == true){
                $scope.show_harvest = false;
                $scope.show_address = true;
            }else {
                $scope.show_harvest = true;
                $scope.show_address = false;
            }
            $state.go('order_commodity',({harvestNum:$scope.harvestNum,harvestName:$scope.harvestName,
                harvestAddress:$scope.harvestAddress,title:$scope.title,subtitle:$scope.subtitle,shop_name:$scope.shop_name,
                platform_price:$scope.platform_price,cover_image:$scope.cover_image,icon:$scope.icon,
                goods_num:$scope.goods_num,show_address:$scope.show_address,show_harvest:$scope.show_harvest,shopNum:$scope.shopNum,
                mall_id:$scope.mall_id, consigneeName:$scope.consigneeName,mobile:$scope.mobile,districtMore:$scope.districtMore,
                regionMore:$scope.regionMore,leaveMessage:$scope.leaveMessage,invoice_name:$scope.invoice_name,invoice_number:$scope.invoice_number,
                invoice_id:$scope.invoice_id
            }))
        };
        // 切换个人和单位
        $scope.choosePersonal = function () { //个人
            console.log('个人');
            $scope.choose_personal = true;
            $scope.choose_company  = false;
        };
        $scope.chooseCompany = function () { //单位
            console.log('单位');
            $scope.choose_personal = true;
            $scope.choose_company  = true;
        };
        // 点击确认按钮时保存数据
        $scope.getSave = function () {
            console.log('确认');
            console.log($scope.invoice_name );
            console.log($scope.invoice_number );
            // 选择为个人时
            if($scope.choose_personal == true && $scope.choose_company  == false ){
                if($scope.invoice_name == ''){
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '请输入抬头名称';
                    console.log(11111111111111)
                }
                else {
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '保存成功';
                    console.log(222222222222);
                    // 添加发票接口
                    $http.post('http://common.cdlhzz.cn/order/orderinvoicelineadd',{
                        invoice_type: 1,
                        invoice_header_type:1,
                        invoice_header:'发票抬头',
                        invoice_content:$scope.invoice_name,
                    },config).then(function (response) {
                        console.log(response);
                        $scope.invoice_id = response.data.data.invoice_id;
                        alert($scope.invoice_id)
                    });
                    // 模态框确认按钮 == 跳转保存数据
                    $scope.jumpOrder = function () {
                        $state.go('order_commodity',({invoice_id:$scope.invoice_id,invoice_name:$scope.invoice_name,invoice_number:$scope.invoice_number,
                            harvestNum:$scope.harvestNum,harvestName:$scope.harvestName,
                            harvestAddress:$scope.harvestAddress,title:$scope.title,subtitle:$scope.subtitle,shop_name:$scope.shop_name,
                            platform_price:$scope.platform_price,cover_image:$scope.cover_image,icon:$scope.icon,
                            goods_num:$scope.goods_num,show_address:$scope.show_address,show_harvest:$scope.show_harvest,shopNum:$scope.shopNum,
                            mall_id:$scope.mall_id, consigneeName:$scope.consigneeName,mobile:$scope.mobile,districtMore:$scope.districtMore,
                            regionMore:$scope.regionMore,leaveMessage:$scope.leaveMessage,supplier_id:$scope.supplier_id,address_id:$scope.address_id
                        }))
                    }
                }
            }
            // 选择为单位时
            if($scope.choose_personal == true && $scope.choose_company  == true ){
                console.log('选择支付吧');
                console.log($scope.invoice_name );
                console.log($scope.invoice_number );
                if($scope.invoice_name == '' || $scope.invoice_number == ''){
                    console.log(13333333333333)
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '请填写完整';
                }
                if($scope.invoice_name != '' && $scope.invoice_number != '' && !numMap.test($scope.invoice_number)){
                    console.log(44444444444444)
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '请填写正确的纳税人识别号'

                }
                if($scope.invoice_name != '' && $scope.invoice_number != '' && numMap.test($scope.invoice_number) ){
                    console.log(15555555555555);
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '保存成功';
                    // 添加发票接口
                    $http.post('http://common.cdlhzz.cn/order/orderinvoicelineadd',{
                        invoice_type: 1,
                        invoice_header_type:2,
                        invoice_header:'发票抬头',
                        invoice_content:$scope.invoice_name,
                        invoicer_card:$scope.invoice_number
                    },config).then(function (response) {
                        console.log(response);
                        $scope.invoice_id = response.data.data.invoice_id;
                        console.log($scope.invoice_id);

                    });
                    $scope.jumpOrder = function () {
                        $state.go('order_commodity',({invoice_id:$scope.invoice_id,invoice_name:$scope.invoice_name,invoice_number:$scope.invoice_number,
                            harvestNum:$scope.harvestNum,harvestName:$scope.harvestName,
                            harvestAddress:$scope.harvestAddress,title:$scope.title,subtitle:$scope.subtitle,shop_name:$scope.shop_name,
                            platform_price:$scope.platform_price,cover_image:$scope.cover_image,icon:$scope.icon,
                            goods_num:$scope.goods_num,show_address:$scope.show_address,show_harvest:$scope.show_harvest,shopNum:$scope.shopNum,
                            mall_id:$scope.mall_id, consigneeName:$scope.consigneeName,mobile:$scope.mobile,districtMore:$scope.districtMore,
                            regionMore:$scope.regionMore,leaveMessage:$scope.leaveMessage
                        }))
                    }
                }
            }

        }


    })

    //确认订单
    .controller('order_commodity_ctrl',function ($scope,$http,$state,$stateParams) {
        $scope.show_harvest = false;
        $scope.show_address = true; //显示第一个
        $scope.mall_id = $stateParams.mall_id;
        $scope.shopNum = $stateParams.shopNum;
        $scope.leaveMessage = $stateParams.leaveMessage ; //买家留言
        $scope.invoice_id  = $stateParams.invoice_id;//纳税人识别号ID
        $scope.supplier_id  = $stateParams.supplier_id;//商家ID
        $scope.address_id  = $stateParams.address_id;//地址ID

        console.log($scope.invoice_id);
        console.log($scope.supplier_id);
        if($stateParams.show_address !== ''){
            console.log(12345456);
            $scope.show_address = $stateParams.show_address;
            $scope.show_harvest = $stateParams.show_harvest;
            $scope.harvestNum = $stateParams.harvestNum;//收获人号码
            $scope.harvestName = $stateParams.harvestName;//收货人名字
            $scope.harvestAddress = $stateParams.harvestAddress;//收货人地址
            $scope.consigneeName = $stateParams.consigneeName;
            $scope.mobile = $stateParams.mobile;
            $scope.districtMore = $stateParams.districtMore;
            $scope.regionMore = $stateParams.regionMore;
            $scope.invoice_name    = $stateParams.invoice_name; //纳税人名称抬头
            $scope.invoice_number  = $stateParams.invoice_number;//纳税人识别号

            console.log($scope.invoice_name );
            console.log($scope.invoice_id );
            console.log($scope.harvestName);
            console.log($scope.harvestAddress)

        }
        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        // 点击收货地址 ===== 模态框
        $scope.harvestMadel = '';
        $scope.getHarvestMadel = function () {
            $scope.harvestName    = '';
            $scope.harvestNum     = '';
            $scope.harvestAddress = '';
            $scope.harvestMadel ='#delivery_address';

            // 判断三级 =========== 初始化  ===========

            var area = new LArea();
            area.init({
                'trigger': '#demo1',//触发选择控件的文本框，同时选择完毕后name属性输出到该位置
                'valueTo':'#value1',//选择完毕后id属性输出到该位置
                'keys':{id:'id',name:'name'},//绑定数据源相关字段 id对应valueTo的value属性输出 name对应trigger的value属性输出
                'type':1,//数据源类型
                'data':LAreaData1//数据源
            });

            // 点击编写收货地址 获取城市内容


        };
        //订单信息===>获取商品的信息
        $http.post('http://common.cdlhzz.cn/order/getgoodsdata',{
            goods_id:+$scope.mall_id,
            goods_num:+$scope.shopNum
        },config).then(function (response) {
            console.log(response);
            $scope.title =  response.data.data.title;
            $scope.subtitle =  response.data.data.subtitle;
            $scope.shop_name =  response.data.data.shop_name;//店铺名称
            $scope.platform_price =  response.data.data.platform_price;//优惠价格
            $scope.market_price =  response.data.data.market_price;//原始价格
            $scope.freight =  response.data.data.freight;//运费
            $scope.allCost = response.data.data.allCost;//总费用
            $scope.cover_image =  response.data.data.cover_image; //封面图
            $scope.icon =  response.data.data.icon; //店家头像
            $scope.goods_num =  response.data.data.goods_num;//购买数量
        });

        // 编辑收货地址的信息
        $scope.harvestName    = '';
        $scope.harvestNum     = '';
        $scope.harvestAddress = '';
        $scope.flagContent    = '';
        let rag =/^1[3|4|5|7|8][0-9]{9}$/;
        // $scope.numModel ='';
        // 点击保存按钮保存编辑收获地址的信息
        $scope.getAddress = function () {
            $scope.numModel= '';
            console.log(111);
            console.log($scope.harvestNum);
            if(!rag.test($scope.harvestNum)){
                console.log(222);
                $scope.numModel = '#harvestNum_modal';
                $scope.flagContent = '请输入正确的手机号';
                $('#harvestNum_modal').modal('hide');
                $('#delivery_address').modal('show');
            }
            if($scope.harvestNum == '' ||  $scope.harvestName == '' || $scope.harvestAddress == ''){
                $scope.numModel = '#harvestNum_modal';
                $scope.flagContent = '请填写完整信息'
            }
            if(rag.test($scope.harvestNum) && !$scope.harvestNum == '' && ! $scope.harvestName == '' && !$scope.harvestAddress == ''){
                // 添加收货地址
                $http.post('http://common.cdlhzz.cn/order/adduseraddress',{
                    mobile:+$scope.harvestNum,
                    consignee:$scope.harvestName,
                    districtcode:110100,
                    region:$scope.harvestAddress
                },config).then(function (response) {
                    console.log(response);
                    $scope.address_id = response.data.data.address_id;
                    console.log($scope.address_id);
                    alert($scope.address_id)
                });
                $scope.numModel = '#harvestNum_modal';
                $scope.flagContent = '保存成功'
            }
        };
        // 点击保存成功按钮获取收获地址信息

        $scope.getHarvest = function () {
            if(!rag.test($scope.harvestNum) || $scope.harvestNum == '' || $scope.harvestName == '' || $scope.harvestAddress == ''){
                $('#harvestNum_modal').modal('hide');
            }else {
                $('#delivery_address').modal('hide');
                $('#harvestNum_modal').modal('hide');
                // 获取订单收货信息
                $http({
                    method: 'get',
                    url: 'http://common.cdlhzz.cn/order/getaddress',
                    params:{
                        address_id:+$scope.address_id
                    }
                }).then(function successCallback(response) {
                    console.log(response);
                    $scope.show_harvest = true;
                    $scope.show_address = false;
                    $scope.consigneeName = response.data.data[0].consignee;
                    $scope.mobile = response.data.data[0].mobile;
                    $scope.districtMore = response.data.data[0].district;
                    $scope.regionMore = response.data.data[0].region;
                    console.log($scope.consigneeName);
                    console.log($scope.mobile)
                });
            }
        };
        // 获取发票信息
        if($scope.invoice_id != ''){
            $http({
                method: 'get',
                url: 'http://common.cdlhzz.cn/order/getinvoicelinedata',
                params:{
                    invoice_id:+$scope.invoice_id
                }
            }).then(function successCallback(response) {
                console.log(response);
                $scope.invoice_content = response.data.data.invoice_content;
                $scope.invoice_header = response.data.data.invoice_header + '-';
                console.log($scope.invoice_content)
            })
        }

        // 点击去支付判断是否填写完整
        $scope.getModel = function () {
            $scope.order_order = '';
            $scope.order_address_model = '';
            if( $scope.show_harvest == false && $scope.show_address == true ){
                $scope.order_address_model = '#order_address_modal';
                $scope.order_order = '请填写完整信息'
            }
            if($scope.show_harvest == true && $scope.show_address == false ){
                console.log(222222);
                //判断收货地址是否在配送范围内
                $http.post('http://common.cdlhzz.cn/order/judegaddress',{
                    goods_id:+$scope.mall_id,
                    districtcode:110100
                },config).then(function (response) {
                    console.log(response);
                    $scope.code = response.data.code;
                    if($scope.code == 1000){
                        console.log(123456);
                        $('#order_address_modal').modal('show');
                        // $scope.order_address_model = '#order_address_modal';
                        $scope.order_order = '不在配送范围内，请重新填写'
                    }
                    if($scope.code == 200){
                        console.log('成功');
                        // 判断是否微信浏览器打开
                        $http({
                            method: 'get',
                            url: 'http://common.cdlhzz.cn/order/iswxlogin'
                        }).then(function successCallback(response) {
                            console.log(response);
                            $scope.codeWX = response.data.code;
                            // 是微信浏览器打开
                            if($scope.codeWX == 200){  // 微信支付
                                alert('调用微信接口');
                                // 微信接口
                                $http.get('http://common.cdlhzz.cn/order/lineplaceorder?goods_name='+$scope.title+'&order_price='+$scope.allCost+'&goods_num='+$scope.shopNum+'&goods_id='+$scope.mall_id+'&address_id='+$scope.address_id+'&invoice_id='+$scope.invoice_id+'&supplier_id='+$scope.supplier_id+'&freight='+$scope.freight
                                    // buyer_message: $scope.leaveMessage
                                ).then(function (response) {
                                    // console.log(response);
                                    alert($scope.mall_id +'商品ID');
                                    alert($scope.address_id+'地址id');
                                    alert($scope.invoice_id+'发票id');
                                    alert($scope.supplier_id+'商家id');
                                    alert(JSON.stringify(response));
                                    alert(JSON.stringify(response.data));
                                    alert(JSON.stringify(response.config));
                                })
                            }
                            if($scope.codeWX == 201){  //非微信浏览器 === 支付宝
                                // 支付宝接口
                                $http.post('http://test.cdlhzz.cn:888/order/alipaylinesubmit',{
                                    goods_name: $scope.title,
                                    order_price:$scope.allCost,
                                    goods_num:+$scope.shopNum,
                                    goods_id:+$scope.mall_id,
                                    address_id:+$scope.address_id,
                                    invoice_id:+$scope.invoice_id,
                                    supplier_id:+$scope.supplier_id,
                                    freight:+$scope.freight,
                                    buyer_message: $scope.leaveMessage
                                },config).then(function (response) {
                                    console.log(response);
                                    $scope.status = response.status;
                                    $scope.dataFram = response.data;
                                    console.log($scope.dataFram);
                                    console.log($scope.status);
                                    $('body').append($scope.dataFram)

                                })
                            }

                        });

                    }
                });
            }

        };
    })

    // 支付成功
    .controller('pay_success_ctrl',function($scope,$http,$state,$stateParams,$interval){
        $scope.timeOut = 5;
        $interval(function () {
            if($scope.timeOut!=0)  {
                $scope.timeOut --;
            }else {
                $state.go('home')
            }
        },1000)


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