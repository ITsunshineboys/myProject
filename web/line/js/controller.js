angular.module("all_controller", [])
    //首页控制器
    .controller("mall_index_ctrl", function ($scope,$http,$state,$stateParams) {  //首页控制器
        $http({   //轮播接口调用
            method: 'get',
            url: "http://test.cdlhzz.cn:888/mall/carousel"
        }).then(function successCallback(response) {
            console.log($scope.swiper_img);
            console.log(response);
            $scope.swiper_img = response.data.data.carousel;
        }, function errorCallback(response) {
            console.log(response)
        });
        $http({   //商品分类列表
            method: 'get',
            url: "http://test.cdlhzz.cn:888/mall/categories"
        }).then(function successCallback (response) {
            $scope.message=response.data.data.categories;
            console.log( $scope.message);
            console.log(response);
        }, function errorCallback (response) {

        });
        $http({   //推荐分类商品列表
            method: 'get',
            url: "http://test.cdlhzz.cn:888/mall/recommend-second"
        }).then(function successCallback (response) {
            $scope.commodity = response.data.data.recommend_second;
            $scope.mall_id   = response.data.data.recommend_second.url;
            console.log( $scope.commodity);
            console.log( $scope.mall_id);
            console.log( response);
        });
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
            url: 'http://test.cdlhzz.cn:888/mall/categories'
        }).then(function successCallback(response) {
            $scope.star = response.data.data.categories;
            console.log(response)
        });
        //首页列表点击分类列表传值id获取数据(一级id查去二级)
        $http({
            method: 'get',
            url: 'http://test.cdlhzz.cn:888/mall/categories?pid=' + $stateParams.pid
        }).then(function successCallback(response) {
            $scope.details = response.data.data.categories;
            //console.log(response.data.data.categories[0].id);
            console.log(response)
        });

        //首页列表点击分类列表传值id获取数据(一级id查去三级)
        $http({
            method: 'get',
            url: 'http://test.cdlhzz.cn:888/mall/categories-level3?pid=' + $stateParams.pid
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
                url: 'http://test.cdlhzz.cn:888/mall/categories?pid=' + item.id
            }).then(function successCallback(response) {
                $scope.details = response.data.data.categories;
                //console.log(response.data.data.categories[0].id);
                console.log(response)
            });

            //首页列表点击分类列表传值id获取数据(一级id查去三级)
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/categories-level3?pid=' + item.id
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
                url:"http://test.cdlhzz.cn:888/mall/search?keyword="+$scope.data
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
            url:'http://test.cdlhzz.cn:888/mall/category-goods?category_id='+$scope.id,
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
                url:'http://test.cdlhzz.cn:888/mall/category-goods?category_id='+$stateParams.id,
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
                url:'http://test.cdlhzz.cn:888/mall/category-goods?category_id='+$stateParams.id,
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
                url:'http://test.cdlhzz.cn:888/mall/category-goods?category_id='+$stateParams.id,
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
                url:'http://test.cdlhzz.cn:888/mall/category-goods?category_id='+$stateParams.id,
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
            url:"http://test.cdlhzz.cn:888/mall/category-brands-styles-series",
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
        console.log($stateParams.id);
        console.log($stateParams.title);
        console.log($stateParams.description);
        console.log($stateParams.platform_price);
        $http({
            method:'get',
            url:"http://test.cdlhzz.cn:888/mall/goods-view",
            params:{
                id:4
            }
        }).then( function successsCallback (response) {
            console.log(response);
        });
        // 跳转到订单页面
        $scope.getOrder =function () {
            console.log(222222);
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
            url:'http://test.cdlhzz.cn:888/mall/category-goods?category_id='+$stateParams.id
        }).then(function successCallback (response) {
            $scope.detailsList = response.data.data.category_goods;
            console.log(response)
        });
        $http({   //分类商品列表
            method: 'get',
            url: "http://test.cdlhzz.cn:888/mall/recommend-second"
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