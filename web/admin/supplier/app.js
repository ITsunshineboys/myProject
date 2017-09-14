var app = angular.module("app",["ui.router","shop_style","freight_template","template_details",
    "shopoffline_Module","systemoffline_Module","wait_online_Module"
    ,"commodity_manage","up_shelves_detail_module",'index_module'])
//路由拦截
   app.config(function ($stateProvider,$httpProvider,$urlRouterProvider) {
        $urlRouterProvider.otherwise("/");
        $httpProvider.defaults.withCredentials = true;
        $stateProvider

            .state("supplier_index",{   //首页
                url:"/supplier_index",
                templateUrl:"pages/supplier_index/supplier_index.html"
            })
            .state("shop_manage",{   //店铺管理
                url:"/shop_manage",
                templateUrl:"pages/shop_manage/shop_manage.html"
            })
            .state("commodity_manage",{   //商品管理
                url:"/commodity_manage",
                templateUrl:"pages/commodity_manage/commodity_manage.html",
                params:{id:'id',name:'name',on_flag:'',down_flag:''}
            })
            .state("order_manage",{   //订单管理
                url:"/order_manage",
                templateUrl:"pages/order_manage/order_manage.html"
            })
            .state("store_data",{   //店铺数据
                url:"/store_data",
                templateUrl:"pages/store_data/store_data.html"
            })

            .state("shop_decoration",{   //店铺装修
                url:"/shop_decoration",
                templateUrl:"pages/shop_decoration/shop_decoration.html"
            })
            .state("brand_manage",{   //品牌管理
                url:"/brand_manage",
                templateUrl:"pages/brand_manage/brand_manage.html"
            })
            .state("class_manage",{   //分类管理
                url:"/class_manage",
                templateUrl:"pages/class_manage/class_manage.html"
            })
            .state("supplier_wallet",{   //钱包
                url:"/supplier_wallet",
                templateUrl:"pages/supplier_wallet/supplier_wallet.html"
            })
            .state("shop_style",{   //商品管理风格系类跳转
                url:"/shop_style",
                templateUrl:"pages/commodity_manage/shop_style.html"
                //controller: "shop_style_ctrl"
            })
            .state("freight_template",{   //商品管理添加物流模板
                url:"/freight_template",
                templateUrl:"pages/commodity_manage/freight_template.html"
                //controller: "shop_style_ctrl"
            })
            .state("template_details",{   //商品管理物流模板详情
                url:"/template_details",
                templateUrl:"pages/commodity_manage/template_details.html",
                params:{id:'id',name:'name'}
            })

            .state("up_shelves_detail",{   //商品管理==>已上架商品详情
                url:"/up_shelves_detail",
                templateUrl:"pages/commodity_manage/up_shelves_detail.html"
            })
            .state("shop_offline",{/*已下架-商家下架*/
                url:"/shop_offline",
                templateUrl:"pages/commodity_manage/shop_offline.html"
            })
            .state("system_offline",{   /*已下架-系统下架*/
                url:"/system_offline",
                templateUrl:"pages/commodity_manage/system_offline.html"
            })
            .state("wait_online",{   /*等待上架*/
                url:"/wait_online",
                templateUrl:"pages/commodity_manage/wait_online.html",
                params:{item:'',flag:''}
            })



    });
