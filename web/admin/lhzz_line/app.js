var app = angular.module("app",["ng.ueditor",
"ngFileUpload",
"intelligent_directive",
"ui.router",
"banner_recommend_module",
"index_recommend_module",
"banner_history_module",
"index_recommend_history_module",
"index_module",
"commodity_manage_module",
"commodity_detail_module"
]);
/*路由拦截*/
app.config(function ($stateProvider,$httpProvider) {
  $httpProvider.defaults.withCredentials = true;

  $stateProvider.state("banner_recommend",{   //商城管理——APP推荐-banner
    url:"/banner_app",
    templateUrl:"pages/mall_manage/banner_app/banner_recommend/banner_recommend.html"
  })
    .state("index_recommend",{   //商城管理——APP推荐-banner——推荐
      url:"/index_recommend",
      templateUrl:"pages/mall_manage/banner_app/index_recommend/index_recommend.html"
    })
    .state("banner_history",{  //商城管理——APP推荐-banner——banner历史数据
      url:"/banner_history",
      templateUrl:"pages/mall_manage/banner_app/banner_history/banner_history.html"
    })
    .state("index_recommend_history",{  //商城管理——APP推荐-banner——推荐历史数据
      url:"/index_recommend_history",
      templateUrl:"pages/mall_manage/banner_app/index_recommend_history/index_recommend_history.html"
    })
    .state("commodity_manage",{   //商城管理——商品管理
      url:"/commodity_manage",
      templateUrl:"pages/mall_manage/merchant_manage/commodity_manage/commodity_manage.html"
    })
    .state("commodity_detail",{   //商城管理——商品管理——商品详情（已上架）
      url:"/commodity_detail",
      templateUrl:"pages/mall_manage/merchant_manage/commodity_detail/commodity_detail.html"
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