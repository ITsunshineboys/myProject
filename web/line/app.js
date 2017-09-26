angular.module("app", ["ui.router","ngAnimate", "all_controller"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/");
        $stateProvider
            .state("home", {  //首页
                url: "/",
                views: {
                    "": {templateUrl:"mall_index.html"}
                },
                controller:"mall_index_ctrl",
                params:{"pid":"","id":"",platform_price:"",title:"title",description:"description"}
            })
            .state("minute_class", {   //分类页
                url: "/minute_class",
                views: {
                    "": {templateUrl: "minute_class.html"}
                },
                controller: "minute_class_ctrl",
                params:{"pid":"","id":""}
            })
            .state("search", {   //小区搜索页
                url: "/search",
                views: {
                    "": {templateUrl: "search.html"}
                },
                controller: "search_ctrl"
                //params:{"pid":"","title":""}
            })
            .state("commodity_search", {   //商品搜索页
                url: "/commodity_search",
                views: {
                    "": {templateUrl: "commodity_search.html"}
                },
                controller: "commodity_search_ctrl",
                params:{"pid":"","id":""}
            })
            .state("details", {   //某个商品详细列表
                url: "/details",
                views: {
                    "": {templateUrl: "details.html"}
                },
                controller: "details_ctrl",
                params:{'pid':'',"id":""}
            })
            .state("product_details", {   //某个商品详细信息
                url: "/product_details",
                views: {
                    "": {templateUrl: "product_details.html"}
                },
                controller: "product_details_ctrl",
                params:{'pid':'',"id":"",platform_price:"",title:"title",description:"description"}
            })
            .state("shop_front", {   //店铺首页和全部商品
                url: "/shop_front",
                views: {
                    "": {templateUrl: "shop_front.html"}
                },
                controller: "shop_front_ctrl",
                params:{'pid':'',"id":""}
            })
            .state("order_commodity", {    //订单确认
                url: "/order_commodity",
                views: {
                    "": {templateUrl: "order_commodity.html"}
                },
                controller: "order_commodity",
                params:{}
            })
            .state("invoice", {    //发票信息
                url: "/invoice",
                views: {
                    "": {templateUrl: "invoice.html"}
                },
                controller: "invoice_ctrl",
                params:{}
            })
            .state("pay_success", {    //支付成功
                url: "/pay_success",
                views: {
                    "": {templateUrl: "pay_success.html"}
                },
                controller: "pay_success_ctrl",
                params:{}
            })
            .state("cut_net", {    //断网提示
                url: "/cut_net",
                views: {
                    "": {templateUrl: "cut_net.html"}
                },
                controller: "cut_net_ctrl",
                params:{}
            })

    }])

    .run(["$rootScope","$state",function ($rootScope,$state) {
        $rootScope.$on("$stateChangeSuccess",function (event,toState,toParams,fromState,fromParams) {
            document.body.scrollTop = document.documentElement.scrollTop = 0;
            $rootScope.goPrev = function (obj) {
                $state.go(fromState.name,obj)
            }
        })
    }]);
