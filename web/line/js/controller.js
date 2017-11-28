angular.module("all_controller", ['ngCookies'])
    // 微信控制器
    .controller('wxShare_ctrl',function ($rootScope, $scope,$http) {
        $rootScope.$on("$locationChangeSuccess", function () {
            $scope.wxShareUrl = window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash;
            wxConfig($scope.wxShareUrl);
        });
        // 微信事宜
        $rootScope.isWxOpen = false;
        $http.get(baseUrl+'/order/iswxlogin').then(function (res) {
            if (res.code === 200) { // 是微信浏览器打开
                let data = res.data;
                wx.config({
                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: data.appId, // 必填，公众号的唯一标识
                    timestamp: data.timestamp, // 必填，生成签名的时间戳
                    nonceStr: data.nonceStr, // 必填，生成签名的随机串
                    signature: data.signature,// 必填，签名，见附录1
                    jsApiList: ["onMenuShareTimeline", "onMenuShareAppMessage", "onMenuShareQQ", "onMenuShareWeibo", "onMenuShareQZone"] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                });
                wx.ready(function () {
                    wxConfig($scope.wxShareUrl);
                });
                $rootScope.isWxOpen = true;
                if (sessionStorage.getItem("openId") === null) {
                    if (getUrlParams('code') === "") {
                        let url = location.href;
                        _ajax.post('/order/find-open-id', {url: url}, function (res) {
                            location.href = res.data
                        })
                    } else {
                        let code = getUrlParams('code');
                        _ajax.post('/order/get-open-id', {code: code}, function (res) {
                            let openId = res.data;
                            sessionStorage.setItem('openId', openId);
                        })
                    }
                }
            }
        });
        // 微信配置
        function wxConfig (url) {
            // 分享到朋友圈
            wx.onMenuShareTimeline({
                title: '', // 分享标题
                link: window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            // 分享给朋友
            wx.onMenuShareAppMessage({
                title: 'Demo', // 分享标题
                desc: url, // 分享描述
                link: url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            // 分享到QQ
            wx.onMenuShareQQ({
                title: '', // 分享标题
                desc: '', // 分享描述
                link: window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash, // 分享链接
                imgUrl: '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            // 腾讯微博
            wx.onMenuShareWeibo({
                title: '', // 分享标题
                desc: '', // 分享描述
                link: window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash, // 分享链接
                imgUrl: '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            // 分享到QQ空间
            wx.onMenuShareQZone({
                title: '', // 分享标题
                desc: '', // 分享描述
                link: window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash, // 分享链接
                imgUrl: '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        }
    })
     //首页控制器
    .controller("mall_index_ctrl", function ($rootScope,$scope,$timeout,$http,$state,$stateParams) {  //首页控制器
        $rootScope.baseUrl = baseUrl;
        $scope.search_flag = false;
        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        //清空支付后的cookie
        if(sessionStorage.getItem('adressInfo') != null){
            sessionStorage.removeItem('adressInfo')
        }
        if(sessionStorage.getItem('shopInfo') != null){
            sessionStorage.removeItem('shopInfo')
        }
        if(sessionStorage.getItem('invoiceInfo') != null){
            sessionStorage.removeItem('invoiceInfo')
        }

        $http({   //轮播接口调用
            method: 'get',
            url: baseUrl+"/mall/carousel"
        }).then(function successCallback(response) {
            console.log(response);
            $scope.swiper_img = response.data.data.carousel;
            //轮播变量
            $timeout(function () {
                var mySwiper = new Swiper('#swiperList', {
                    autoplay: 3000,
                    loop: true,
                    pagination : '#swiperList-pagination',
                });
            })
        }, function errorCallback(response) {
            console.log(response)
        });
        $http({   //商品分类列表
            method: 'get',
            url: baseUrl+"/mall/categories"
        }).then(function successCallback (response) {
            console.log(response);
            $scope.message=response.data.data.categories;
        }, function errorCallback (response) {

        });

        // 点击轮播图跳转
        $scope.getDetails = function (item) {
            console.log(item.id);
            sessionStorage.setItem('wxflag',true);
            $http.post(baseUrl+'/mall/recommend-click-record',{
                recommend_id:item.id
            },config).then(function (response) {
                console.log(response)
            });
            console.log(item);
            $scope.mall_id = item.url.split('=')[1];
            if(item.from_type == 1){
                $state.go('product_details',{'mall_id':$scope.mall_id,'id':$state.mall_id})
            }else{
                window.location = item.url
            }
        };

        $http({   //推荐分类商品列表
            method: 'get',
            url: baseUrl+"/mall/recommend-second"
        }).then(function successCallback (response) {
            console.log(response);
            $scope.commodity = response.data.data.recommend_second;
        });
        // 点击推荐跳转商品详情
        $scope.getProduct = function (m) {
            console.log($scope.recommend_id);
            sessionStorage.setItem('wxflag',true);
            console.log(m);
            $http.post( baseUrl+'/mall/recommend-click-record',{
                recommend_id:m.id
            },config).then(function (response){
                console.log(response)
            });
            if(m.from_type == 1){ //商铺类型
                $scope.mall_id = m.url.split('=')[1];
                $scope.id = m.id;
                $state.go('product_details',{'mall_id':$scope.mall_id,'id':$scope.id});
            }else {              //链接类型
                window.location = m.url
            }
        };
    })

    //分类详情控制器
    .controller("minute_class_ctrl", function ($rootScope,$scope,$http ,$state,$stateParams) {
        $rootScope.baseUrl = baseUrl;
        $scope.pid = $stateParams.pid;
        $scope.id = $stateParams.id;
        $scope.search_flag = true;
        $scope.details = '';
        //左侧数据获取
        $http({
            method: 'get',
            url: baseUrl+'/mall/categories'
        }).then(function successCallback(response) {
            $scope.star = response.data.data.categories;
        });
        //首页列表点击分类列表传值id获取数据(一级id查去二级)
        $http({
            method: 'get',
            url: baseUrl+'/mall/categories?pid='+$scope.pid
        }).then(function successCallback(response) {
            console.log(response);
            $scope.details = response.data.data.categories;
        });

        //首页列表点击分类列表传值id获取数据(一级id查去三级)
        $http({
            method: 'get',
            url:  baseUrl+'/mall/categories-level3?pid=' + $scope.pid
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
        });

        //点击左侧分类列表菜单获取右边数据
        $scope.getTitle = function (item) {
            //首页列表点击分类列表传值id获取数据(一级id查去二级)
            $scope.pid = item.id;
            $http({
                method: 'get',
                url:  baseUrl+'/mall/categories?pid=' + item.id
            }).then(function successCallback(response) {
                $scope.details = response.data.data.categories;
            });

            //首页列表点击分类列表传值id获取数据(一级id查去三级)
            $http({
                method: 'get',
                url:  baseUrl+'/mall/categories-level3?pid=' + item.id
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
            });
        }
    })

    //商品搜索
    .controller("commodity_search_ctrl", function ($rootScope,$scope,$http ,$state,$stateParams) {
        $rootScope.baseUrl = baseUrl;
        $scope.data = '';
        $scope.id =  $stateParams.id;
        $scope.pid = $stateParams.pid;
        $scope.search_flag = $stateParams.search_flag;
        $scope.search_flag_details = $stateParams.search_flag_details;
        //判断
        $scope.getSearch = function () {
			if ($scope.data != '') {
                let arr=[];
                $http({
                    method:'get',
                    url:baseUrl+"/mall/search?keyword="+$scope.data
                }).then( function successCallback (response) {
                    console.log(response);
                    $scope.commoditySearch = response.data.data.search.goods;
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
                });
            }else {
                   $scope.search_data = []
                }
        };
        // 点击取消回到首页
        $scope.goPrevIndex = function () {
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
            console.log(item);

            if($scope.commoditySearch.length == 0){
                $state.go("details",{'pid':$scope.pid,'id':item.id,'flag':0})
            }else {
                sessionStorage.setItem('flag_goods','htsb');
                $state.go("product_details",{'mall_id':item.id,'id':item.id,'flag':0})
            }
        }
    })

    //某个商品的详细列表
    .controller("details_ctrl", function ($rootScope,$scope,$http ,$state,$stateParams) {
        $rootScope.baseUrl = baseUrl;
        let flag = $stateParams.flag;
        window.addEventListener("hashchange", function() {
            // 注册返回按键事件
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open')
        });
        $scope.getRetrnUp = function () {
            if(flag == 0){
                history.go(-2)
            }
            else{
                history.go(-1)
            }
        };
        $scope.id  = $stateParams.id;
        $scope.pid = $stateParams.pid;
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
        $scope.staus = 'sold_number';

        /*分页配置*/
        $scope.Config = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages();
            }
        };
        let tablePages = function () {

            // $scope.params.page = $scope.Config.currentPage;//点击页数，传对应的参数
            $http.get(baseUrl+'/mall/category-goods', {
                params: $scope.params
            }).then(function (res) {
                console.log(res);
                $scope.cur_replace_material = [];
                for (let [key, value] of res.data.data.category_goods.entries()) {
                    $scope.cur_replace_material.push({
                        id: value.id,
                        image: value.cover_image,
                        cost: +value.platform_price,
                        // name: $scope.cur_goods_detail.name,
                        favourable_comment_rate: value.favourable_comment_rate,
                        sold_number: value.sold_number,
                        platform_price: value.platform_price,
                        profit_rate: value.profit_rate,
                        purchase_price_decoration_company: value.purchase_price_decoration_company,
                        quantity: 1,
                        series_id: !!$scope.cur_goods_detail?$scope.cur_goods_detail.series_id:'',
                        style_id: !!$scope.cur_goods_detail?$scope.cur_goods_detail.style_id:'',
                        subtitle: value.subtitle,
                        supplier_price: value.supplier_price,
                        title: value.title
                        // shop_name: value.shop_name
                    })
                }
                // $scope.house_detail = res.data.model.details
                $scope.Config.totalItems = $scope.cur_replace_material.length
                $http.get(baseUrl +'/mall/category-brands-styles-series', {
                    params: {
                        category_id: $scope.params.category_id,
                    }
                }).then(function (res) {
                    $scope.all_goods_series = res.data.data.category_brands_styles_series.series
                    $scope.all_goods_style = res.data.data.category_brands_styles_series.styles
                    $scope.all_goods_brands = res.data.data.category_brands_styles_series.brands
                    console.log($scope.all_goods_style)
                }, function (error) {
                    console.log(error)
                })
            }, function (err) {
                console.log(err);
            })
        };
        $scope.platform_status = 0;
        $scope.rate_status = 0;
        $scope.params = {
            category_id: '',
            platform_price_min: '',
            platform_price_max: '',
            'sort[]': '',
            brand_id: '',
            style_id: '',
            series_id: ''
        };
        $scope.params.category_id = $scope.id;
        $scope.params['sort[]'] ='sold_number:3';
        $scope.cur_series_arr = [];
        $scope.cur_style_arr = [];
        $scope.cur_brand_arr = [];
        //重置筛选
        $scope.reset_filter = function () {
            $scope.cur_series_arr = [];
            $scope.cur_style_arr = [];
            $scope.cur_brand_arr = [];
            $scope.price_min = '';
            $scope.price_max = '';
            $scope.params.platform_price_min = '';
            $scope.params.platform_price_max = '';
            $scope.params.brand_id = '';
            $scope.params.style_id = '';
            $scope.params.series_id = '';
        };
        //商品排序
        $scope.sort = function (str) {
            console.log($scope.platform_status);
            if (str == 'sold_number') {
                $scope.platform_status = 0;
                $scope.rate_status = 0
            } else if (str == 'platform_price') {
                if ($scope.platform_status == 0 || $scope.platform_status == 2) {
                    $scope.platform_status = 1
                } else if ($scope.platform_status == 1) {
                    $scope.platform_status = 2
                }
                $scope.rate_status = 0
            } else if (str == 'favourable_comment_rate') {
                if ($scope.rate_status == 0 || $scope.rate_status == 2) {
                    $scope.rate_status = 1
                } else if ($scope.rate_status == 1) {
                    $scope.rate_status = 2
                }
                $scope.platform_status = 0
            }
            $scope.params.category_id = $scope.id;
            $scope.params['sort[]'] = str + ($scope.platform_status == 0 ? ($scope.rate_status == 0 ? '' : ($scope.rate_status == 1 ? ':3' : ':4')) : ($scope.platform_status == 1 ? ':3' : ':4'))
            tablePages()
        };
        //填写筛选价格区间
        $scope.get_price = function (item) {
            console.log($scope.price_min);
            console.log($scope.price_max);
            if(item == 1){
                if($scope.price_max != ''){
                    if(+$scope.price_min>+$scope.price_max){
                        let cur_item = $scope.price_min;
                        $scope.price_min = $scope.price_max;
                        $scope.price_max = cur_item
                    }
                }
            }else{
                if($scope.price_min != ''){
                    if(+$scope.price_min>+$scope.price_max){
                        let cur_item = $scope.price_min;
                        $scope.price_min = $scope.price_max;
                        $scope.price_max = cur_item
                    }
                }
            }
        };
        //改变风格系列以及品牌
        $scope.all_change = function (item,cur_item) {
            if(item == 1){
                let index = $scope.cur_style_arr.findIndex(function (item) {
                    return item ===cur_item.id
                })
                if(index != -1){
                    $scope.cur_style_arr.splice(index,1);
                    $scope.params.style_id = $scope.cur_style_arr.join(',')
                }else{
                    $scope.cur_style_arr.push(cur_item.id);
                    $scope.params.style_id = $scope.cur_style_arr.join(',')
                }
            }else if(item == 2){
                let index = $scope.cur_series_arr.findIndex(function (item) {
                    return item ===cur_item.id
                })
                if(index != -1){
                    $scope.cur_series_arr.splice(index,1);
                    $scope.params.series_id = $scope.cur_series_arr.join(',')
                }else{
                    $scope.cur_series_arr.push(cur_item.id);
                    $scope.params.series_id = $scope.cur_series_arr.join(',')
                }
            }else if(item == 3){
                let index = $scope.cur_brand_arr.findIndex(function (item) {
                    return item ===cur_item.id
                });
                if(index != -1){
                    $scope.cur_brand_arr.splice(index,1);
                    $scope.params.brand_id = $scope.cur_brand_arr.join(',')
                }else{
                    $scope.cur_brand_arr.push(cur_item.id);
                    $scope.params.brand_id = $scope.cur_brand_arr.join(',')
                }
            }else if(item == 4){
                let index = $scope.cur_brand_copy.findIndex(function (item) {
                    return item ===cur_item.id
                });
                if(index != -1){
                    $scope.cur_brand_copy.splice(index,1)
                }else{
                    $scope.cur_brand_copy.push(cur_item.id)
                }
            }
        };
        //跳转内层模态框
        $scope.go_inner_data = function () {
            $scope.cur_brand_copy = angular.copy($scope.cur_brand_arr);
            $scope.all_brand_copy = angular.copy($scope.all_goods_brands)
        };
        //保存内层数据
        $scope.save_inner_data = function () {
            $scope.cur_brand_arr = $scope.cur_brand_copy;
            $scope.params.brand_id = $scope.cur_brand_arr.join(',')
        };
        //完成筛选
        $scope.complete_filter = function () {
            $scope.params.platform_price_min = $scope.price_min*100;
            $scope.params.platform_price_max = $scope.price_max*100;
            tablePages()
        }
        //筛选关键字
        $scope.$watch('keyword',function (newVal,oldVal) {
            console.log(newVal);
            if(newVal!='' && newVal != oldVal){
                let arr = [];
                if(!!$scope.all_goods_brands){
                    for(let [key,value] of $scope.all_goods_brands.entries()){
                        if(value.name.indexOf(newVal)!= -1){
                            arr.push(value)
                        }
                    }
                }

                $scope.all_goods_brands = arr
            }else{
                $scope.all_goods_brands = $scope.all_brand_copy
            }
        });

        // 点击产品列表商品跳转到产品详情页面
        $scope.getDetailsProduct = function (item) {
            sessionStorage.setItem('wxflag',true);
            $scope.mall_id = item.id;
            $state.go('product_details',{mall_id:$scope.mall_id,id:$scope.id})
        };
        // 点击主页返回到主页
        $scope.getHome = function () {
            $state.go('home')
        }

    })

    //某个 商品详细信息展示
    .controller("product_details_ctrl", function ($rootScope,$scope,$http,$state,$stateParams) {  //首页控制器
        $rootScope.baseUrl = baseUrl;
        let vm = $scope.vm = {};
        window.addEventListener("hashchange", function() {
            // 注册返回按键事件
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open')
        });

        $scope.id=$stateParams.id;
        $scope.datailsShop = $stateParams.datailsShop;
        $scope.mall_id = $stateParams.mall_id;
        $scope.activeTab = $stateParams.activeTab;
        // console.log($scope.activeTab);

        if($scope.activeTab != undefined){
            sessionStorage.setItem('activeTab',$scope.activeTab)
        }else {
            sessionStorage.removeItem('activeTab')
        }
        $scope.shop_goods = '';

        $http({
            method:'get',
            url:baseUrl+"/mall/goods-view",
            params:{
                id:+$scope.mall_id
            }
        }).then( function successCallback (response) {
            console.log(response);
            $scope.datailsShop = response.data.data.goods_view;
            $scope.supplier_id = response.data.data.goods_view.supplier.id;
            $scope.status = response.data.data.goods_view.status;
            $scope.showPrompt = false;
            //轮播变量
            setTimeout(function () {
                let mySwiper = new Swiper("#swiperList", {
                    autoplay: 3000,
                    loop: true,
                    observer: true,
                    pagination: ".swiper-pagination"
                });
            },300);

            if($scope.status == 2){
                $scope.myModal = '#myModal';
                $scope.myModal_sec = '#myModal_sec'
            }else {
                $scope.showPrompt = true;
                $scope.myModal = '';
                $scope.myModal_sec = ''
            }
            if($scope.datailsShop.left_number == 0){
                $scope.shop_goods = '#goods_model';
                $scope.myModal = '';
            }else {
                $scope.shop_goods = '#myModal';
            }

            $scope.style_parameter = false;
            $scope.series_parameter = false;
            // 判断是否存在系列
            if($scope.series_name == '' ){
                $scope.style_parameter = false;
            }else {
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
            }
            // 判断售后服务 不存在时  不显示售后
            if($scope.on_site == false && $scope.changeGoods == false && $scope.returnGoods == false && $scope.changeMore  == false && $scope.returnMore  == false){
                $scope.show_service = false;
            }
        });
        $scope.getOtherApp = function () {
            window.location = 'http://ac.cdlhzz.cn/owner/mall/#!/nodata/cell_search'
        };
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
            if($scope.shopNum > 1){
                $scope.shopNum--
            }else {
                $scope.shopNum = 1;
            }
        };
        // 监听购买数量输入是否大于库存
        $scope.getQuantity = function () {
            if($scope.shopNum > $scope.datailsShop.left_number){
                $scope.shopNum =  $scope.datailsShop.left_number
            }
        };

        // 判断是否是微信浏览器打开 =======是微信浏览器打开 做分享的配置
        // $http({   // 判断是否微信浏览器打开
        //     method: 'get',
        //     url:  baseUrl+'/order/iswxlogin'
        // }).then(function successCallback(response) {
        //     console.log(response);
        //     $scope.codeWX = response.data.code;
        //     if( $scope.codeWX == 200){
        //         $scope.appId  = response.data.data.appId;
        //         $scope.timestamp  = response.data.data.timestamp;
        //         $scope.nonceStr  = response.data.data.nonceStr;
        //         $scope.signature  = response.data.data.signature;
        //     }
        // });
        // 返回
        $scope.getRetun = function () {
            if (sessionStorage.getItem('wxflag') == null) {
                $state.go('home')
            }else if(sessionStorage.getItem('flag_goods') !=null){
                history.go(-2);
            }else {
                history.go(-1);
            }
        };
        // 跳转到订单页面
        $scope.getOrder =function () {
            setTimeout(function () {
                $state.go('order_commodity',{mall_id:$scope.mall_id,shopNum:$scope.shopNum,supplier_id:$scope.supplier_id,show_address:true})
            },300)


        }
    })

    //店铺首页和全部商品
    .controller("shop_front_ctrl", function ($rootScope,$scope,$http,$state,$stateParams) {  //首页控制器
        $rootScope.baseUrl = baseUrl;
        $scope.vm = {activeTab:1};

        window.addEventListener("hashchange", function() {
            // 注册返回按键事件
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open')
        });

        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        $(document).ready(function(){
             $scope.p=0;
             $scope.t=0;
            $(window).scroll(function(e){
                $scope.p = $(this).scrollTop();
                if($scope.t<=$scope.p){//下滚
                    if($scope.p >= 226 ){
                        $('.list_tab').addClass('fixed_tab');
                        $('.memo_pad').addClass('fixed_memo')
                    }
                }else{ //上滚
                    if($scope.p <= 225 ){
                        $('.list_tab').removeClass('fixed_tab');
                        $('.memo_pad').removeClass('fixed_memo')
                    }
                }
                setTimeout(function(){$scope.t = $scope.p;},0);
            });
        });
        //获取商品列表
        $scope.id  = $stateParams.id;
        $scope.pid = $stateParams.pid;
        $scope.mall_id = $stateParams.mall_id;
        $scope.supplier_id = $stateParams.supplier_id;
        $scope.datailsShop = $stateParams.datailsShop;
        $scope.brands = '';
        $scope.series = '';
        $scope.styles = '';
        $scope.good_pic_up = 2;
        $scope.good_pic =$scope.good_pic_up==2?'images/mall_filter_sort.png':
            ($scope.good_pic_up==1?'images/mall_arrow_up.png':'images/down.png');
        // $scope.good_pic_down = false;
        $scope.praise_up = 2;
        $scope.good_pra_up =$scope.praise_up==2?'images/mall_filter_sort.png':
            ($scope.praise_up==1?'images/mall_arrow_up.png':'images/down.png');
        $http({
            method:"get",
            url: baseUrl+'/supplier/index?supplier_id='+$scope.supplier_id
        }).then(function successCallback (response) {
            console.log(response);
            $scope.swiperList = response.data.data.index.carousel;//轮播图
            $scope.follower_number = response.data.data.index.follower_number; //粉丝数量
            $scope.icon = response.data.data.index.icon; //店铺图片
            $scope.shop_name = response.data.data.index.shop_name; //店铺名称
            //轮播变量
            let mSwiper = new Swiper("#swiperList", {
                autoplay: 3000,
                loop: true,
                observer: true,
                pagination: ".swiper-pagination"
            });
            console.log($scope.detailsList)
        });
        $http({   //店铺首页推荐列表
            method: 'get',
            url: baseUrl+"/supplier/recommend-second",
            params:{
                supplier_id:+$scope.supplier_id
            }
        }).then(function successCallback (response) {
            console.log(response);
            $scope.recommendList = response.data.data.recommend_second;
            for( let [key, value] of $scope.recommendList.entries() ){
                console.log(value.id);
                $scope.recommend_id = value.id
            }
        });
        // 全部商品展示
        $http({   //店铺全部商品列表
            method: 'get',
            url: baseUrl+"/supplier/goods",
            params:{
                supplier_id:+$scope.supplier_id,
                "sort[]":"sold_number:4",
            }
        }).then(function successCallback (response) {
            console.log(response);
            $scope.supplier_goods=response.data.data.supplier_goods;
            setTimeout(function () {
                // 获取滚动条高度
                $(document).ready(function () {
                    $scope.paramsMore = 1;
                    var pagesFlag = true;
                    $(window).scroll(function(e){
                        // $scope.sorllMore = $(this).scrollHeight();
                        var node = $('.recommend_div');
                        var top  = node.get(node.length-1).getBoundingClientRect().top;
                        var se   = document.documentElement.clientHeight;
                        if(top<=se){
                            if(pagesFlag){
                                $scope.paramsMore++;
                                $http({   //店铺全部商品列表
                                    method: 'get',
                                    url: baseUrl+"/supplier/goods",
                                    params:{
                                        supplier_id:+$scope.supplier_id,
                                        "sort[]":"sold_number:4",
                                        page:$scope.paramsMore
                                    }
                                }).then(function successCallback (response) {
                                    console.log(response);
                                    if (response.data.data.supplier_goods.length == 0) {
                                        return
                                    }
                                    for( let [key,vaule] of response.data.data.supplier_goods.entries()){
                                        $scope.supplier_goods.push(vaule)
                                    }
                                    setTimeout(function () {
                                        pagesFlag = true;
                                    },200);

                                });
                            }
                            pagesFlag = false;
                        }
                    })
                });
            },300)
        });


        // 点击推荐商品判断跳转商品详情
        $scope.getProductMore = function (item) {
           //店铺首页推荐列表
            $scope.mall_id = item.url.split('=')[1];
            console.log($scope.mall_id);
            $http.post( baseUrl+'/mall/recommend-click-record-supplier',{
                // recommend_id:+$scope.recommend_id
				recommend_id:item.id
            },config).then(function (response){
                console.log(response)
            });
            $state.go("product_details",{mall_id:$scope.mall_id,datailsShop:$scope.datailsShop});

        };
        // 点击全部商品跳转到商品详情页面
        $scope.allGetProdouct = function (item) {
            sessionStorage.setItem('scroll_value',$scope.p);
            $scope.mall_id = item.id;
            $state.go("product_details",{mall_id:$scope.mall_id,activeTab:2})
        };
        // 点击上下排序
        //价格排序
        $scope.filterPraise = function () {
            $scope.praise_up = 2;
            $scope.good_pra_up =$scope.praise_up==2?'images/mall_filter_sort.png':
                ($scope.praise_up==1?'images/mall_arrow_up.png':'images/down.png');
            if($scope.good_pic_up == 2){
                $scope.good_pic_up = 1;
            }else {
                $scope.good_pic_up = +!$scope.good_pic_up;
            }

            $scope.good_pic =$scope.good_pic_up==2?'images/mall_filter_sort.png':
                ($scope.good_pic_up==1?'images/mall_arrow_up.png':'images/down.png')

            $http({
                method: 'get',
                url: baseUrl+'/supplier/goods',
                params:{
                    supplier_id:+$scope.supplier_id,
                    "sort[]":"platform_price:"+($scope.good_pic_up?'4':'3')
                }
            }).then(function successCallback(response) {
                $scope.supplier_goods = response.data.data.supplier_goods;
            });
        };
        // 好评率排序
        $scope.filterPicUp = function () {
            $scope.good_pic_up = 2
            $scope.good_pic =$scope.good_pic_up==2?'images/mall_filter_sort.png':
                ($scope.good_pic_up==1?'images/mall_arrow_up.png':'images/down.png')
            if($scope.praise_up == 2){
                $scope.praise_up = 1;
            }else {
                $scope.praise_up = +!$scope.praise_up;
            }

            $scope.good_pra_up =$scope.praise_up==2?'images/mall_filter_sort.png':
                ($scope.praise_up==1?'images/mall_arrow_up.png':'images/down.png');
            $http({
                method: 'get',
                url: baseUrl+'/supplier/goods',
                params:{
                    supplier_id:+$scope.supplier_id,
                    "sort[]":"favourable_comment_rate:"+($scope.good_pic_up?'4':'3')
                }
            }).then(function successCallback(response) {
                $scope.supplier_goods = response.data.data.supplier_goods;
            });
        };

        // 销量优先
        $scope.filterPar = function () {
            console.log(11);
            $http({
                method: 'get',
                url: baseUrl+'/supplier/goods',
                params:{
                    supplier_id:+$scope.supplier_id,
                    "sort[]":"sold_number:3"
                }
            }).then(function successCallback(response) {
                $scope.supplier_goods = response.data.data.supplier_goods;
            });
        };
        // 店铺简介
        $http({
            method: 'get',
            url: baseUrl+"/supplier/view",
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
        });
        if(sessionStorage.getItem('activeTab') != null){
            $scope.vm.activeTab = 2
        }
        if(sessionStorage.getItem('scroll_value') != null){
            setTimeout(function () {
                $(document).scrollTop(sessionStorage.getItem('scroll_value'));
            },150)
        }
        console.log($scope.vm);
        // 点击跳转到首页
        $scope.getHome = function () {
            $state.go("home")
        }
    })

    //发票信息
    .controller('invoice_ctrl',function($rootScope,$scope,$http,$state,$stateParams){
        $rootScope.baseUrl = baseUrl;
        window.addEventListener("hashchange", function() {
            // 注册返回按键事件
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open')
        });
        $scope.harvestAddress  = $stateParams.harvestAddress;
        $scope.harvestName     = $stateParams.harvestName;
        $scope.harvestNum      = $stateParams.harvestNum;
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
        // $scope.choose_personal = true;
        // $scope.choose_company  = false;
        $scope.invoice_name    = ''; //纳税人名称抬头
        $scope.invoice_number  = '';//纳税人识别号
        $scope.invoice_model   = '';
        $scope.contentInvoice  = '';
        $scope.check_in = '1';

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
        };
        // // 切换个人和单位
        // $scope.choosePersonal = function () { //个人
        //
        //     $scope.choose_personal = true;
        //     $scope.choose_company  = false;
        // };
        // $scope.chooseCompany = function () { //单位
        //     $scope.choose_personal = true;
        //     $scope.choose_company  = true;
        // };
        // 点击确认按钮时保存数据
        $scope.getSave = function () {
            // 选择为个人时
            if($scope.check_in == 1 ){
                if($scope.invoice_name == ''){
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '请输入抬头名称';
                }
                else {
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '保存成功';

                    // 添加发票接口
                    $http.post( baseUrl+'/order/orderinvoicelineadd',{
                        invoice_type: 1,
                        invoice_header_type:1,
                        invoice_header:$scope.invoice_name,
                        invoice_content:'明细',
                    },config).then(function (response) {
                        console.log(response);
                        $scope.invoice_id = response.data.data.invoice_id;
                        let invoiceObj = { // 保存
                            invoice_id: $scope.invoice_id,
                            invoice_content: $scope.invoice_name,
                            status:$scope.check_in
                        };
                        sessionStorage.setItem('invoiceInfo', JSON.stringify(invoiceObj));
                    });
                    // 模态框确认按钮 == 跳转保存数据
                    $scope.jumpOrder = function () {
                        setTimeout(function () {
                            $state.go('order_commodity',({invoice_id:$scope.invoice_id,invoice_name:$scope.invoice_name,invoice_number:$scope.invoice_number,
                                harvestNum:$scope.harvestNum,harvestName:$scope.harvestName,
                                harvestAddress:$scope.harvestAddress,title:$scope.title,subtitle:$scope.subtitle,shop_name:$scope.shop_name,
                                platform_price:$scope.platform_price,cover_image:$scope.cover_image,icon:$scope.icon,
                                goods_num:$scope.goods_num,show_address:$scope.show_address,show_harvest:$scope.show_harvest,shopNum:$scope.shopNum,
                                mall_id:$scope.mall_id, consigneeName:$scope.consigneeName,mobile:$scope.mobile,districtMore:$scope.districtMore,
                                regionMore:$scope.regionMore,leaveMessage:$scope.leaveMessage,supplier_id:$scope.supplier_id,address_id:$scope.address_id
                            }))
                        },300);
                    }
                }
            }
            // 选择为单位时
            if($scope.check_in == 2  ){
                console.log('选择支付吧');
                console.log($scope.invoice_name );
                console.log($scope.invoice_number );
                if($scope.invoice_name == '' || $scope.invoice_number == ''){
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '请填写完整';
                }
                if($scope.invoice_name != '' && $scope.invoice_number != '' && !numMap.test($scope.invoice_number)){
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '请填写正确的纳税人识别号'
                }
                if($scope.invoice_name != '' && $scope.invoice_number != '' && numMap.test($scope.invoice_number) ){
                    $scope.invoice_model = '.bs-example-modal-sm';
                    $scope.contentInvoice = '保存成功';
                    // 添加发票接口
                    $http.post( baseUrl+'/order/orderinvoicelineadd',{
                        invoice_type: 1,
                        invoice_header_type:2,
                        invoice_header:$scope.invoice_name,
                        invoice_content:'明细',
                        invoicer_card:$scope.invoice_number
                    },config).then(function (response) {
                        console.log(response);
                        $scope.invoice_id = response.data.data.invoice_id;
                        let invoiceObj = { // 保存
                            invoice_id: $scope.invoice_id,
                            invoice_content: $scope.invoice_name,
                            invoicer_card: $scope.invoice_number,
                            status:$scope.check_in
                        };
                        sessionStorage.setItem('invoiceInfo', JSON.stringify(invoiceObj));
                    });
                    $scope.jumpOrder = function () {
                        setTimeout(function () {
                            $state.go('order_commodity',({invoice_id:$scope.invoice_id,invoice_name:$scope.invoice_name,invoice_number:$scope.invoice_number,
                                harvestNum:$scope.harvestNum,harvestName:$scope.harvestName,
                                harvestAddress:$scope.harvestAddress,title:$scope.title,subtitle:$scope.subtitle,shop_name:$scope.shop_name,
                                platform_price:$scope.platform_price,cover_image:$scope.cover_image,icon:$scope.icon,
                                goods_num:$scope.goods_num,show_address:$scope.show_address,show_harvest:$scope.show_harvest,shopNum:$scope.shopNum,
                                mall_id:$scope.mall_id, consigneeName:$scope.consigneeName,mobile:$scope.mobile,districtMore:$scope.districtMore,
                                regionMore:$scope.regionMore,leaveMessage:$scope.leaveMessage
                            }))
                        },300);
                    }
                }
            }

        };
        if (sessionStorage.getItem('invoiceInfo') != null) {
            //获取 商品信息
            let invoiceInfo = JSON.parse(sessionStorage.getItem('invoiceInfo'));
            console.log(invoiceInfo);
            $scope.invoice_id =  invoiceInfo.invoice_id;
            $scope.invoice_name =  invoiceInfo.invoice_content;
            $scope.invoice_number =  invoiceInfo.invoicer_card;
            $scope.check_in =  invoiceInfo.status;
        }

    })

    //确认订单
    .controller('order_commodity_ctrl',function ($rootScope,$scope,$http,$state,$stateParams,$cookieStore,$cookies) {
        $rootScope.baseUrl = baseUrl;
        window.addEventListener("hashchange", function() {
            // 注册返回按键事件
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open')
        });
        $scope.show_harvest = false;
        $scope.show_address = true; //显示第一个
        $scope.mall_id = $stateParams.mall_id;
        if($scope.mall_id == undefined){
            $scope.mall_id = sessionStorage.getItem('mall_id')
        }
        sessionStorage.setItem('mall_id',$stateParams.mall_id);
        $scope.shopNum = $stateParams.shopNum;
        $scope.leaveMessage = $stateParams.leaveMessage ; //买家留言
        $scope.invoice_id  = $stateParams.invoice_id;//纳税人识别号ID
        $scope.supplier_id  = $stateParams.supplier_id;//商家ID
        $scope.address_id  = $stateParams.address_id;//地址ID
        if($stateParams.show_address !== ''){

            $scope.harvestNum = $stateParams.harvestNum;//收获人号码
            $scope.harvestName = $stateParams.harvestName;//收货人名字
            $scope.harvestAddress = $stateParams.harvestAddress;//收货人地址
            $scope.consigneeName = $stateParams.consigneeName;
            $scope.mobile = $stateParams.mobile;
            $scope.districtMore = $stateParams.districtMore;
            $scope.regionMore = $stateParams.regionMore;
            $scope.invoice_name    = $stateParams.invoice_name; //纳税人名称抬头
            $scope.invoice_number  = $stateParams.invoice_number;//纳税人识别号
        }
        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        let area = new LArea();
        area.init({
            'trigger': '#demo1',//触发选择控件的文本框，同时选择完毕后name属性输出到该位置
            'valueTo':'#value1',//选择完毕后id属性输出到该位置
            'keys':{id:'id',name:'name'},//绑定数据源相关字段 id对应valueTo的value属性输出 name对应trigger的value属性输出
            'type':1,//数据源类型
            'data':LAreaData1//数据源
        });
        area.value = [22,0,0];
        // 点击编写收货地址 获取城市内容

        // 点击收货地址 ===== 模态框
        $scope.harvestMadel = '';
        $scope.getHarvestMadel = function () {
            if(sessionStorage.getItem('adressInfo') == null){
                $scope.harvestName    = '';
                $scope.harvestNum     = '';
                $scope.harvestAddress = '';
            }else {
                let addresObJ = JSON.parse(sessionStorage.getItem('adressInfo'));
                $scope.harvestName    = addresObJ.name;
                $scope.harvestNum     = addresObJ.phone;
                $scope.harvestAddress = addresObJ.address;
                $scope.prove_city_more = addresObJ.prove +'—'+ addresObJ.prove_city+'—'+ addresObJ.prove_city_qu;
            }

            $scope.harvestMadel ='#delivery_address';

            // 判断三级 =========== 初始化  ===========
        };
        //订单信息===>获取商品的信息
        $http({
            method: 'get',
            url: baseUrl+'/order/getgoodsdata',
            params:{
                goods_id:+$scope.mall_id,
                goods_num:+$scope.shopNum
            }
        }).then(function (response) {
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
            let shopObj = { // 保存
                title: response.data.data.title,
                subtitle: response.data.data.subtitle,
                shop_name:  response.data.data.shop_name,
                platform_price: response.data.data.platform_price,
                market_price: response.data.data.market_price,
                freight: response.data.data.freight,
                allCost: response.data.data.allCost,
                cover_image:  response.data.data.cover_image,
                icon: response.data.data.icon,
                goods_num:response.data.data.goods_num,

            };
            sessionStorage.setItem('shopInfo', JSON.stringify(shopObj));
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
                $scope.addressCode = document.getElementById("value1").value;
                $scope.addressCode = $scope.addressCode.split('—');
                $http.post( baseUrl+'/order/adduseraddress',{
                    mobile:+$scope.harvestNum,
                    consignee:$scope.harvestName,
                    districtcode:$scope.addressCode[2],
                    region:$scope.harvestAddress
                },config).then(function (response) {
                    console.log(response);
                    sessionStorage.setItem('address_id',response.data.data.address_id);
                    // $scope.address_id = response.data.data.address_id;
                    console.log($scope.address_id);
                    // alert($scope.address_id)
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
                // 获取订单收货信息地址
                $http({
                    method: 'get',
                    url:  baseUrl+'/order/getaddress',
                    params:{
                        address_id:sessionStorage.getItem('address_id')
                    }
                }).then(function successCallback(response) {
                    console.log(response);
                    $scope.adCode = response.data.data[0].adCode;
                    console.log($scope.adCode);
                    let adressObj = { // 保存
                        show_harvest: true,
                        show_address: false,
                        name: response.data.data[0].consignee,
                        phone: response.data.data[0].mobile,
                        city: response.data.data[0].district,
                        address: response.data.data[0].region,
                        prove:document.getElementById("demo1").value.split('—')[0],
                        prove_city:document.getElementById("demo1").value.split('—')[1],
                        prove_city_qu:document.getElementById("demo1").value.split('—')[2],
                        adCode:response.data.data[0].adCode
                        // code:document.getElementById("value1").value

                    };
                    sessionStorage.setItem('adressInfo', JSON.stringify(adressObj));
                    $scope.show_harvest = true;
                    $scope.show_address = false;
                    $scope.consigneeName = response.data.data[0].consignee;
                    $scope.mobile = response.data.data[0].mobile;
                    $scope.districtMore = response.data.data[0].district;
                    $scope.regionMore = response.data.data[0].region;
                    $scope.adCode = response.data.data[0].adCode;
                });
            }
        };
        // 获取sessionStorage
        if (sessionStorage.getItem('adressInfo') != null) {  //获取收获的信息
            let adressInfo = JSON.parse(sessionStorage.getItem('adressInfo'));
            console.log(adressInfo);
            $scope.show_address = false;
            $scope.show_harvest = true;
            $scope.consigneeName = adressInfo.name; //收货人名字
            $scope.mobile = adressInfo.phone;   ///收货人电话
            $scope.districtMore = adressInfo.city; //收货人城市
            $scope.regionMore = adressInfo.address; //收货人地址
            $scope.adCode   = adressInfo.adCode;
        }
        if (sessionStorage.getItem('shopInfo') != null) {
            //获取 商品信息
            let shopInfo = JSON.parse(sessionStorage.getItem('shopInfo'));
            console.log(shopInfo);
            $scope.title =  shopInfo.title;
            $scope.subtitle =  shopInfo.subtitle;
            $scope.shop_name =  shopInfo.shop_name;//店铺名称
            $scope.platform_price =  shopInfo.platform_price;//优惠价格
            $scope.market_price =  shopInfo.market_price;//原始价格
            $scope.freight =  shopInfo.freight;//运费
            $scope.allCost = shopInfo.allCost;//总费用
            $scope.cover_image =  shopInfo.cover_image; //封面图
            $scope.icon =  shopInfo.icon; //店家头像
            $scope.goods_num =  shopInfo.goods_num;//购买数量
        }

        // 获取发票信息
        if($scope.invoice_id != undefined){
            $http({
                method: 'get',
                url:  baseUrl+'/order/getinvoicelinedata',
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
        // 点击切换购买协议的转状态
        $scope.check_agressment = false;
        $scope.chooseCheck = function () {
            $scope.check_agressment = !$scope.check_agressment;
        };

        // 点击去支付判断是否填写完整
        $scope.getModel = function () {
            // alert($scope.mall_id);
            $scope.order_order = '';
            $scope.order_address_model = '';
            if( $scope.show_harvest == false && $scope.show_address == true){
                $scope.order_address_model = '#order_address_modal';
                $scope.order_order = '请填写完整信息';
                return
            }
            if($scope.invoice_id == ''){
                $scope.order_address_model = '#order_address_modal';
                $scope.order_order = '请填写发票信息';
            }
            if (!$scope.check_agressment) {
                $scope.order_address_model = '#order_address_modal';
                $scope.order_order = '请勾选商城协议';
                return
            }
            if($scope.show_harvest == true && $scope.show_address == false ){
                //判断收货地址是否在配送范围内
                $http.post( baseUrl+'/order/judegaddress',{
                    goods_id:+$scope.mall_id,
                    districtcode:$scope.adCode
                },config).then(function (response) {
                    // alert(JSON.stringify(response));
                    // console.log(response);
                    $scope.code = response.data.code;
                    if($scope.code == 1000){
                        $('#order_address_modal').modal('show');
                        // $scope.order_address_model = '#order_address_modal';
                        $scope.order_order = '您好，您的地址超过商品配送范围内，请更换商品或收货地址！';
                        return
                    }
                    if($scope.code == 200){
                        console.log(baseUrl+'/order/iswxlogin');
                        // 判断是否微信浏览器打开
                        $http({
                            method: 'get',
                            url:  baseUrl+'/order/iswxlogin'
                        }).then(function successCallback(response) {
                            console.log(response);
                            $scope.codeWX = response.data.code;
                            // 是微信浏览器打开
                            if($scope.codeWX == 200){  // 微信支付
                                // 微信接口 === 调用
                                // alert(sessionStorage.getItem('address_id'));
                                // alert(JSON.stringify(sessionStorage.getItem('address_id')));
                                $http({     //获取openid 的地址
                                    method: 'get',
                                    url:  baseUrl+'/order/lineplaceorder',
                                    params:{
                                        // goods_name: $scope.title,
                                        order_price:$scope.allCost,
                                        goods_num:+$scope.shopNum,
                                        goods_id:+$scope.mall_id,
                                        address_id:sessionStorage.getItem('address_id'),
                                        invoice_id:+$scope.invoice_id,
                                        supplier_id:+$scope.supplier_id,
                                        freight:+$scope.freight,
                                        // openid:'oyKJL0oHDKwyzBXidhyhshxluBOg'
                                    }
                                }).then(function successCallback(response) {
                                    console.log(response);
                                    $scope.open_id = response.data.data;
                                    window.location = $scope.open_id
                                },function (error) {
                                    alert(JSON.stringify(error))
                                });

                            }
                            if($scope.codeWX == 201){  //非微信浏览器 === 支付宝
                                // 支付宝接口
                                let config = {
                                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                    transformRequest: function (data) {
                                        return $.param(data)
                                    }
                                };
                                // http://test.cdlhzz.cn/
                                $http.post( baseUrl+'/order/alipaylinesubmit',{
                                    goods_name: $scope.title,
                                    order_price:+$scope.allCost,
                                    goods_num:+$scope.shopNum,
                                    goods_id:+$scope.mall_id,
                                    address_id:+sessionStorage.getItem('address_id'),
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

                                },function (error) {
                                    console.log(error)
                                })
                            }

                        });

                    }
                });
            }

        };
        $scope.getProduct_details =  function () {
            $state.go('product_details',{'mall_id':$scope.mall_id,'id':$scope.id})
        }
    })

    // 支付成功
    .controller('pay_success_ctrl',function($rootScope,$scope,$http,$state,$stateParams,$interval){
        $rootScope.baseUrl = baseUrl;
        // pay_success
        $scope.timeOutNum = 5;
        let setTimeDown = setInterval(function () {
            $scope.$apply(function(){$scope.timeOutNum--});
            if($scope.timeOutNum == 0) {
                clearInterval(setTimeDown);
                $state.go('home');
            }
        },1000);
    })

    //断网提示
    .controller('cut_net_ctrl',function($rootScope,$scope,$http,$state,$stateParams){
        $rootScope.baseUrl = baseUrl;

    })

    .directive('tmPagination', function () {
        return {
            restrict: 'EA',
            template: `<div class="no-items" style="padding-top: 2rem;background: #fff;color: #b1b1b1;font-size: 40px;text-align: center;" ng-show="conf.totalItems <= 0">暂无符合条件的商品</div>`,
            replace: true,
            scope: {
                conf: '='
            },
            link: function (scope, element, attrs) {

                let conf = scope.conf;

                // 默认分页长度
                let defaultPagesLength = 9;

                // 默认分页选项可调整每页显示的条数
                let defaultPerPageOptions = [10, 15, 20, 30, 50];
                conf.perPageOptions = [];
                // 默认每页的个数
                let defaultPerPage = 15;

                // 获取分页长度
                if (conf.pagesLength) {
                    // 判断一下分页长度
                    conf.pagesLength = parseInt(conf.pagesLength, 10);

                    if (!conf.pagesLength) {
                        conf.pagesLength = defaultPagesLength;
                    }

                    // 分页长度必须为奇数，如果传偶数时，自动处理
                    if (conf.pagesLength % 2 === 0) {
                        conf.pagesLength += 1;
                    }

                } else {
                    conf.pagesLength = defaultPagesLength
                }

                // 分页选项可调整每页显示的条数
                if (!conf.perPageOptions) {
                    conf.perPageOptions = defaultPagesLength;
                }

                // pageList数组
                function getPagination(newValue, oldValue) {

                    // conf.currentPage
                    if (conf.currentPage) {
                        conf.currentPage = parseInt(scope.conf.currentPage, 10);
                    }

                    if (!conf.currentPage) {
                        conf.currentPage = 1;
                    }

                    // conf.totalItems
                    if (conf.totalItems) {
                        conf.totalItems = parseInt(conf.totalItems, 10);
                    }

                    // conf.totalItems
                    if (!conf.totalItems) {
                        conf.totalItems = 0;
                        return;
                    }

                    // conf.itemsPerPage
                    if (conf.itemsPerPage) {
                        conf.itemsPerPage = parseInt(conf.itemsPerPage, 10);
                    }
                    if (!conf.itemsPerPage) {
                        conf.itemsPerPage = defaultPerPage;
                    }

                    // numberOfPages
                    conf.numberOfPages = Math.ceil(conf.totalItems / conf.itemsPerPage);

                    // 如果分页总数>0，并且当前页大于分页总数
                    if (scope.conf.numberOfPages > 0 && scope.conf.currentPage > scope.conf.numberOfPages) {
                        scope.conf.currentPage = scope.conf.numberOfPages;
                    }

                    // 如果itemsPerPage在不在perPageOptions数组中，就把itemsPerPage加入这个数组中
                    let perPageOptionsLength = scope.conf.perPageOptions.length;

                    // 定义状态
                    let perPageOptionsStatus;
                    for (var i = 0; i < perPageOptionsLength; i++) {
                        if (conf.perPageOptions[i] == conf.itemsPerPage) {
                            perPageOptionsStatus = true;
                        }
                    }
                    // 如果itemsPerPage在不在perPageOptions数组中，就把itemsPerPage加入这个数组中
                    if (!perPageOptionsStatus) {
                        conf.perPageOptions.push(conf.itemsPerPage);
                    }

                    // 对选项进行sort
                    conf.perPageOptions.sort(function (a, b) {
                        return a - b
                    });


                    // 页码相关
                    scope.pageList = [];
                    if (conf.numberOfPages <= conf.pagesLength) {
                        // 判断总页数如果小于等于分页的长度，若小于则直接显示
                        for (i = 1; i <= conf.numberOfPages; i++) {
                            scope.pageList.push(i);
                        }
                    } else {
                        // 总页数大于分页长度（此时分为三种情况：1.左边没有...2.右边没有...3.左右都有...）
                        // 计算中心偏移量
                        let offset = (conf.pagesLength - 1) / 2;
                        if (conf.currentPage <= offset) {
                            // 左边没有...
                            for (i = 1; i <= offset + 1; i++) {
                                scope.pageList.push(i);
                            }
                            scope.pageList.push('...');
                            scope.pageList.push(conf.numberOfPages);
                        } else if (conf.currentPage > conf.numberOfPages - offset) {
                            scope.pageList.push(1);
                            scope.pageList.push('...');
                            for (i = offset + 1; i >= 1; i--) {
                                scope.pageList.push(conf.numberOfPages - i);
                            }
                            scope.pageList.push(conf.numberOfPages);
                        } else {
                            // 最后一种情况，两边都有...
                            scope.pageList.push(1);
                            scope.pageList.push('...');

                            for (i = Math.ceil(offset / 2); i >= 1; i--) {
                                scope.pageList.push(conf.currentPage - i);
                            }
                            scope.pageList.push(conf.currentPage);
                            for (i = 1; i <= offset / 2; i++) {
                                scope.pageList.push(conf.currentPage + i);
                            }

                            scope.pageList.push('...');
                            scope.pageList.push(conf.numberOfPages);
                        }
                    }

                    scope.$parent.conf = conf;
                }

                // prevPage
                scope.prevPage = function () {
                    if (conf.currentPage == 1) {
                        return false;
                    }
                    if (conf.currentPage > 1) {
                        conf.currentPage -= 1;
                    }
                    getPagination();
                    if (conf.onChange) {
                        conf.onChange();
                    }
                };

                // nextPage
                scope.nextPage = function () {
                    if (conf.currentPage == conf.numberOfPages) {
                        return false;
                    }
                    if (conf.currentPage < conf.numberOfPages) {
                        conf.currentPage += 1;
                    }
                    getPagination();
                    if (conf.onChange) {
                        conf.onChange();
                    }
                };

                // 变更当前页
                scope.changeCurrentPage = function (item) {

                    if (item == '...' || item == conf.currentPage) {
                        return;
                    } else {
                        conf.currentPage = item;
                        getPagination();
                        // conf.onChange()函数
                        if (conf.onChange) {
                            conf.onChange();
                        }
                    }
                };

                // 跳转到页面
                scope.jumpPage = function () {
                    let jumpNum = angular.element('#pageJump').val();
                    scope.changeCurrentPage(jumpNum);
                    angular.element('#pageJump').val('')
                };

                scope.$watch('conf.totalItems', function (value, oldValue) {
                    // 在无值或值不相等的时候，去执行onChange事件
                    if (value == undefined && oldValue == undefined) {
                        if (conf.onChange) {
                            conf.onChange();
                        }
                    }
                    getPagination();
                });
            }
        };
    })
    .filter("toHtml", ["$sce", function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        }
    }]);
