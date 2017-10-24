const app = angular.module("app", ["ui.router", "shop_style", "freight_template", "template_details",
  "shopoffline_Module", "systemoffline_Module", "wait_online_Module", "commodity_manage",
    "up_shelves_detail_module", "index_module", "shopmanageModule", "applybrandModule", "authorizedetailModule",
  /*三阶段王杰---开始*/
    "supplier_index", "login","shop_decoration_module","supplier_wallet_module",
    "intelligent_directive","shop_data_module","wallet_detail_module","income_pay_module",
    "set_password_module",
  /*三阶段王杰---结束*/
  /*三阶段芳子---开始*/
    "supplier_accountModule","withdraw_depositModule",
    "edit_cardModule","frozen_moneyModule"
  /*三阶段芳子---结束*/
]);

// 传参：通过url的get参数stage来获取，不传则使用默认的开发域名
let baseUrl = (function () {
    let stages = [
        "http://test.cdlhzz.cn:888", // 开发接口域名
        "http://v1.cdlhzz.cn:888" // 展示接口域名
    ];
    let stage = 0;
    try {
        let stageParam = location.search.split("&stage=")[1].split("&")[0];
        if (stages[stageParam])  {
            stage = stageParam;
        }
    } catch (e) {}
    return stages[stage];
})();

//路由拦截
app.config(function ($stateProvider, $httpProvider, $urlRouterProvider) {
  $urlRouterProvider.otherwise("/login");
  $httpProvider.defaults.withCredentials = true;
  $stateProvider
  /*--------------三阶段开始----王杰-----------------*/
      .state("login", {   //登录
          url: "/login",
          templateUrl: "pages/login/login.html"
      })
      .state("supplier_index", {   //首页
          url: "/supplier_index",
          templateUrl: "pages/supplier_index/supplier_index.html"
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
          params:{item:null,sku:null,wait_receive:null}
      })
      .state("record_goods_detail", {   //记录商品详情
          url: "/record_goods_detail",
          templateUrl: "pages/order_manage/record_goods_detail.html",
          params:{item:null,wait_receive:null}
      })
      /*--------------三阶段结束----王杰-----------------*/


      /*--------------三阶段开始----芳子-----------------*/
      .state("supplier_account", {   //商家账户
          url: "/supplier_account",
          templateUrl: "pages/supplier_wallet/supplier_account.html"
      })
      .state("withdraw_deposit", {   //提现
          url: "/withdraw_deposit",
          templateUrl: "pages/supplier_wallet/withdraw_deposit.html"
      })
      .state("edit_card", {   //添加/修改银行卡
          url: "/edit_card",
          templateUrl: "pages/supplier_wallet/edit_card.html"
      })
      .state("frozen_money", {   //冻结银行卡
          url: "/frozen_money",
          templateUrl: "pages/supplier_wallet/frozen_money.html"
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
      params: {id: 'id', name: 'name', on_flag: '', down_flag: ''}
    })
    .state("order_manage", {   //订单管理
      url: "/order_manage",
      templateUrl: "pages/order_manage/order_manage.html"
    })
    .state("brand_manage", {   //品牌管理
      url: "/brand_manage",
      templateUrl: "pages/brand_manage/brand_manage.html"
    })
    .state("class_manage", {   //分类管理
      url: "/class_manage",
      templateUrl: "pages/class_manage/class_manage.html"
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
      templateUrl: "pages/commodity_manage/freight_template.html"
      //controller: "shop_style_ctrl"
    })
    .state("template_details", {   //商品管理物流模板详情
      url: "/template_details",
      templateUrl: "pages/commodity_manage/template_details.html",
      params: {id: 'id', name: 'name'}
    })

    .state("up_shelves_detail", {   //商品管理==>已上架商品详情
      url: "/up_shelves_detail",
      templateUrl: "pages/commodity_manage/up_shelves_detail.html",
      params: {item: '', flag: ''}
    })
    .state("shop_offline", {
      /*已下架-商家下架*/
      url: "/shop_offline",
      templateUrl: "pages/commodity_manage/shop_offline.html"
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
  });