const app = angular.module("app", ["ui.router", "ng.ueditor","ngFileUpload","angularCSS","shop_style", "freight_template",
    "systemoffline_Module", "wait_online_Module", "commodity_manage",
    "up_shelves_detail_module", "index_module", "shopmanageModule", "applybrandModule", "authorizedetailModule",
  /*三阶段王杰---开始*/
    "supplier_index","shop_decoration_module","supplier_wallet_module",
    "intelligent_directive","shop_data_module","wallet_detail_module","income_pay_module",
    "set_password_module","waitsend_detail_module","goods_detail_module",
    "brand_index_module","add_brand_module","edit_brand_module","brand_detail_module",
  /*三阶段王杰---结束*/
  /*三阶段芳子---开始*/
    "supplier_accountModule","withdraw_depositModule",
    "edit_cardModule","frozen_moneyModule",
    "ordermanageModule",
    "waitpay_detailModule","done_detailModule","cancel_detailModule","expressModule",
  /*三阶段芳子---结束*/
  /*公共开始*/
  "checklist-model"
  /*公共结束*/
]);

//路由拦截
app.config(function ($stateProvider, $httpProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise("/home");
    $httpProvider.defaults.withCredentials = true;
    $stateProvider
    /*--------------三阶段开始----王杰-----------------*/
        .state("home", {   //首页
            url: "/home",
            templateUrl: "pages/home/home.html"
        })
        .state("shop_data", {   //店铺数据
            url: "/shop_data",
            templateUrl: "pages/shop_data/shop_data.html"
        })
        .state("shop_decoration", {   //店铺装修
            url: "/shop_decoration",
            templateUrl: "pages/shop_decoration/shop_decoration.html"
        })

        .state("supplier_wallet", {   //钱包
            url: "/supplier_wallet",
            templateUrl: "pages/supplier_wallet/supplier_wallet.html"
        })
        .state("wallet_detail", {   //钱包详情
            url: "/wallet_detail",
            templateUrl: "pages/supplier_wallet/wallet_detail.html",
            params:{transaction_no:null,income:null}
        })
        .state("income_pay", {   //收支详情
            url: "/income_pay",
            templateUrl: "pages/supplier_wallet/income_pay.html"
        })
        .state("set_password", {   //交易密码
            url: "/set_password",
            templateUrl: "pages/supplier_wallet/set_password.html",
            params:{code_status:null}
        })
        .state("waitsend_detail", {   //待发货详情
            url: "/waitsend_detail",
            templateUrl: "pages/order_manage/waitsend_detail.html",
            params:{order_no:null,sku:null,tabflag:null}
        })
        .state("record_goods_detail", {   //记录商品详情
            url: "/record_goods_detail",
            templateUrl: "pages/order_manage/record_goods_detail.html",
            params:{express_params:null},
        })
        .state("brand_index", {   //品牌管理
          url: "/brand_index",
          templateUrl: "pages/brand_manage/brand_index.html"
        })
        .state("add_brand", {   //添加品牌
          url: "/add_brand",
          templateUrl: "pages/brand_manage/add_brand.html"
        })
	      .state("edit_brand", {   //编辑品牌
		      url: "/edit_brand",
		      templateUrl: "pages/brand_manage/edit_brand.html"
	      })
        .state("brand_detail", {   //编辑品牌
          url: "/brand_detail",
          templateUrl: "pages/brand_manage/brand_detail.html"
        })
        /*--------------三阶段结束----王杰-----------------*/


        /*--------------三阶段开始----芳子-----------------*/
        .state("supplier_account", {   //商家账户信息
            url: "/supplier_account",
            templateUrl: "pages/supplier_wallet/supplier_account.html"
        })
        .state("withdraw_deposit", {   //提现
            url: "/withdraw_deposit",
            templateUrl: "pages/supplier_wallet/withdraw_deposit.html"
        })
        .state("edit_card", {         //添加/修改银行卡
            url: "/edit_card",
            templateUrl: "pages/supplier_wallet/edit_card.html"
        })
        .state("frozen_money", {      //冻结银行卡
            url: "/frozen_money",
            templateUrl: "pages/supplier_wallet/frozen_money.html"
        })
        .state("order_manage", {      //订单管理
            params:{tabflag:null},
            url: "/order_manage",
            templateUrl: "pages/order_manage/order_manage_index.html"
        })
        .state("waitpay_detail", {   //待付款订单详情
            params:{order_no:null,sku:null,tabflag:null},
            url: "/waitpay_detail",
            templateUrl: "pages/order_manage/waitpay_detail.html"
        })
        .state("done_detail", {     //已完成订单详情
            params:{order_no:null,sku:null,tabflag:null},
            url: "/done_detail",
            templateUrl: "pages/order_manage/done_detail.html"
        })
        .state("cancel_detail", {   //已取消订单详情
            params:{order_no:null,sku:null,tabflag:null},
            url: "/cancel_detail",
            templateUrl: "pages/order_manage/cancel_detail.html"
        })
        .state("express", {        //物流详情
            params:{express_params:null},
            url: "/express",
            templateUrl: "pages/order_manage/express.html"
        })
        .state('class_manage', { // 分类管理
            url: '/class-manage',
            templateUrl: 'pages/class_manage/class_manage.html',
            css: 'pages/class_manage/css/class_manage.css',
            controller: 'class_manage'
        })

        .state('add_class', { // 添加分类
            url: '/add-class',
            templateUrl: 'pages/class_manage/add_class.html',
            css: 'pages/class_manage/css/add_class.css',
            controller: 'add_class'
        })
        .state('class_detail', { // 分类详情
            url: '/class-detail?id',
            templateUrl: 'pages/class_manage/class_detail.html',
            css: 'pages/class_manage/css/class_detail.css',
            controller: 'class_detail'
        })
        .state('edit_class', { // 分类详情
            url: '/edit-class?id',
            templateUrl: 'pages/class_manage/edit_class.html',
            css: 'pages/class_manage/css/edit_class.css',
            controller: 'edit_class'
        })



        /*--------------三阶段结束----芳子-----------------*/


        .state("shop_manage", {   //店铺管理
            url: "/shop_manage",
            templateUrl: "pages/shop_manage/shop_manage_index.html",
            params:{authorize_flag:null}
        })
        .state("apply_brand", {   //申请新品牌
            url: "/apply_brand",
            templateUrl: "pages/shop_manage/apply_brand.html"
        })
        .state("authorize_detail", {   //品牌授权详情
            url: "/authorize_detail",
            templateUrl: "pages/shop_manage/authorize_detail.html"
        })
        .state("commodity_manage", {   //商品管理
            url: "/commodity_manage",
            templateUrl: "pages/commodity_manage/commodity_manage.html",
            params: {id: 'id', name: 'name', on_flag: '', down_flag: '',wait_flag:'', logistics_flag:''}
        })
        .state("shop_style", {   //商品管理风格系类跳转
            url: "/shop_style",
            templateUrl: "pages/commodity_manage/shop_style.html",
            params: {
                category_id: '',
                first_category_title: '',
                second_category_title: '',
                third_category_title: ''
            }
        })
        .state("freight_template", {   //商品管理添加物流模板
            url: "/freight_template",
            templateUrl: "pages/commodity_manage/freight_template.html",
            params: {logistics_flag:''}
            //controller: "shop_style_ctrl"
        })
        .state("template_details", {   //商品管理物流模板详情
            url: "/template_details?id&name",
            templateUrl: "pages/commodity_manage/template_details.html",
            params: {logistics_flag:''}
        })

        .state("up_shelves_detail", {   //商品管理==>已上架商品详情
            url: "/up_shelves_detail",
            templateUrl: "pages/commodity_manage/up_shelves_detail.html",
            params: {item: '', flag: ''}
        })
        .state("system_offline", {
          /*已下架-系统下架*/
            url: "/system_offline",
            templateUrl: "pages/commodity_manage/system_offline.html",
            params: {item: ''}
        })
        .state("wait_online", {
          /*等待上架*/
            url: "/wait_online",
            templateUrl: "pages/commodity_manage/wait_online.html",
            params: {item: '', flag: ''}
        })
})
    .directive('wdatePicker',function(){
        return{
            restrict:"A",
            link:function(scope,element,attr){
                element.bind('click',function(){
                    window.WdatePicker({
                        onpicked: function(){element.change()},
                        oncleared:function(){element.change()}
                    })
                });
            }
        }
    })
