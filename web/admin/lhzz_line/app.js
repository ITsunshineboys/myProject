var app = angular.module("app",["ng.ueditor","intelligent_directive","ui.router",
  "clamagModule","onsaleeditModule",
  "offsaleeditModule","addclassModule","comment",'merchant_details',
  'brand_check','check_right','account_comment','change_num',
  'bind_record','operation_record',"mallmagModule","storemagModule","addstoreModule",
  "onlineeditModule","offlineeditModule","addbrandModule","styleindexModule","chooseseriesModule",
  "addseriesModule",
  "seriesdetailModule","addstyleModule","choose_styleModule","styledetailModule",

  //  王杰 开始
  "index_module",
  "banner_recommend_module",
  "index_recommend_module",
  "banner_history_module",
  "index_recommend_history_module",
  "commodity_manage_module",
  "commodity_detail_on_module",
  "commodity_detail_down_module",
  "commodity_detail_wait_module",
  "commodity_detail_del_module",
  "brand_index_module",
  "edit_attribute_module",
  "account_index_module",
  "add_user_module"
  //王杰 结束
]);
/*路由拦截*/
app.config(function ($stateProvider,$httpProvider,$urlRouterProvider) {
  $httpProvider.defaults.withCredentials = true;
  $urlRouterProvider.otherwise("/");
  $stateProvider

    //  ==============王杰  开始====================

    .state("banner_recommend",{   //APP推荐-banner
    url:"/banner_app",
    templateUrl:"pages/mall_manage/banner_app/banner_recommend/banner_recommend.html"
  })
    .state("index_recommend",{   //首页推荐-推荐
      url:"/index_recommend",
      templateUrl:"pages/mall_manage/banner_app/index_recommend/index_recommend.html"
    })
    .state("banner_history",{  //首页推荐-banner-历史数据
      url:"/banner_history",
      templateUrl:"pages/mall_manage/banner_app/banner_history/banner_history.html"
    })
    .state("index_recommend_history",{  //首页推荐-推荐-历史数据
      url:"/index_recommend_history",
      templateUrl:"pages/mall_manage/banner_app/index_recommend_history/index_recommend_history.html"
    })
      .state("commodity_manage",{   //商城管理——商品管理
        params:({on_flag:null,down_flag:null,wait_flag:null,del_flag:null}),
        url:"/commodity_manage",
        templateUrl:"pages/mall_manage/merchant_manage/commodity_manage/commodity_manage.html"
      })
      .state("commodity_detail_on",{   //商城管理——商品管理——商品详情（已上架）
        url:"/commodity_detail_on",
        templateUrl:"pages/mall_manage/merchant_manage/commodity_detail_on/commodity_detail_on.html"
      })
      .state("commodity_detail_down",{   //商城管理——商品管理——商品详情（已下架）
        url:"/commodity_detail_down",
        templateUrl:"pages/mall_manage/merchant_manage/commodity_detail_down/commodity_detail_down.html"
      })
      .state("commodity_detail_wait",{   //商城管理——商品管理——商品详情（等待上架）
        url:"/commodity_detail_wait",
        templateUrl:"pages/mall_manage/merchant_manage/commodity_detail_wait/commodity_detail_wait.html"
      })
      .state("commodity_detail_del",{   //商城管理——商品管理——商品详情（已删除）
        url:"/commodity_detail_del",
        templateUrl:"pages/mall_manage/merchant_manage/commodity_detail_del/commodity_detail_del.html"
      })
      .state("brand_index",{   //商城管理——品牌管理
        url:"/brand_index",
        templateUrl:"pages/mall_manage/brand_manage/brand_index/brand_index.html"
      })
      .state("edit_attribute",{   //商城管理——品牌管理
        url:"/edit_attribute",
        templateUrl:"pages/mall_manage/style_manage/edit_attribute/edit_attribute.html"
      })
      .state("account_index",{   //账户管理
        url:"/account_index",
        templateUrl:"pages/account_manage/account_index/account_index.html"
      })
      .state("add_user",{   //账户管理——添加新用户
        url:"/add_user",
        templateUrl:"pages/account_manage/add_user/add_user.html"
      })

    // =================王杰  结束==============


    .state("fenleiguanli",{
      url:"/fenleiguanli",
      templateUrl:"pages/mall_manage/class_manage/cla_mag/cla_mag.html"
    })
    .state("onsale_edit",{
    params:{"classtitle":null,"classid":null,"classlevel":null},
    url:"/onsale_edit",
    templateUrl:"pages/mall_manage/class_manage/onsale_edit/onsale_edit.html"
    })
    .state("offsale_edit",{
    params:{"classtitle":null,"classid":null,"classlevel":null},
    url:"/offsale_edit",
    templateUrl:"pages/mall_manage/class_manage/offsale_edit/offsale_edit.html"
  })
    .state("add_class",{
    url:"/add_class",
    templateUrl:"pages/mall_manage/class_manage/add_class/add_class.html"
  })
    .state("comment",{
    url:"/comment",
    templateUrl:"pages/mall_manage/merchant_manage/merchant_comment/comment.html"
  })
    .state("merchant_details",{
    url:"/merchant_details",
    templateUrl:"pages/mall_manage/merchant_manage/merchant_comment/merchant_details.html"
  })
    .state("brand_check",{
    url:"/brand_check",
    templateUrl:"pages/mall_manage/merchant_manage/merchant_comment/brand_check.html"
  })
    .state("check_right",{
    url:"/check_right",
    templateUrl:"pages/mall_manage/merchant_manage/merchant_comment/check-right.html"
  })
    .state("account_comment",{
    url:"/account_comment",
    templateUrl:"pages/account_manage/account_comment/account_comment.html"
  })
    .state("change_num",{
    url:"/change_num",
    templateUrl:"pages/account_manage/account_comment/change_num.html"
  })
      .state("bind_record",{
    url:"/bind_record",
    templateUrl:"pages/account_manage/account_comment/bind_record.html"
  })
    .state("operation_record",{
    url:"/operation_record",
    templateUrl:"pages/account_manage/account_comment/operation_record.html"
  })
      .state("idcard_right",{
    url:"/idcard_right",
    templateUrl:"pages/account_manage/account_comment/idcard_right.html"
  })

  //===============================================
      .state("merchant_index",{  //商城管理
        url:"/merchant_index",
        templateUrl:"pages/mall_manage/merchant_manage/merchant_index/merchant_index.html"
      })
      .state("store_mag",{   //商城管理-商家管理
        url:"/store_mag",
        templateUrl:"pages/mall_manage/merchant_manage/store_mag/store_mag.html"
      })
      .state("add_store",{    //商城管理-商家管理-添加商家
        url:"/add_store",
        templateUrl:"pages/mall_manage/merchant_manage/add_store/add_store.html"
      })
      .state("online_edit",{/*品牌管理-已上架编辑*/
        url:"/online_edit",
        templateUrl:"pages/mall_manage/brand_manage/online_edit/online_edit.html"
      })
      .state("offline_edit",{ /*品牌管理-已下架编辑*/
        url:"/offline_edit",
        templateUrl:"pages/mall_manage/brand_manage/offline_edit/offline_edit.html"
      })
      .state("add_brand",{ /*品牌管理-添加品牌*/
        url:"/add_brand",
        templateUrl:"pages/mall_manage/brand_manage/add_brand/add_brand.html"
      })
      .state("style_index",{ /*系列/风格/属性管理*/
        url:"/style_index",
        templateUrl:"pages/mall_manage/style_manage/style_index/style_index.html"
      })
      .state("choose_series",{ /*系列/风格/属性管理-选择拥有系列的分类*/
        url:"/choose_series",
        templateUrl:"pages/mall_manage/style_manage/choose_series/choose_series.html"
      })
      .state("add_series",{ /*系列/风格/属性管理-添加新系列*/
        url:"/add_series",
        templateUrl:"pages/mall_manage/style_manage/add_series/add_series.html",
        params:{"list":""},
      })
      .state("series_detail",{ /*系列/风格/属性管理-系列详情页*/
        url:"/series_detail",
        templateUrl:"pages/mall_manage/style_manage/series_detail/series_detail.html",
        params:{item:''}
      })
      .state("add_style",{ /*系列/风格/属性管理-风格-添加新风格*/
        url:"/add_style",
        templateUrl:"pages/mall_manage/style_manage/add_style/add_style.html"
      })
      .state("choose_style",{ /*系列/风格/属性管理-风格-选择拥有风格的分类*/
        url:"/choose_style",
        templateUrl:"pages/mall_manage/style_manage/choose_style/choose_style.html"
      })
      .state("style_detail",{ /*系列/风格/属性管理-风格-风格详情*/
        url:"/style_detail",
        templateUrl:"pages/mall_manage/style_manage/style_detail/style_detail.html"
      })
  //============================================================
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