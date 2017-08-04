var app = angular.module("app",["ng.ueditor","intelligent_directive","ui.router","banner_recommend_module","index_recommend_module","banner_history_module","index_recommend_history_module","index_module","clamagModule","onsaleeditModule","offsaleeditModule","addclassModule"]);
/*路由拦截*/
app.config(function ($stateProvider,$httpProvider) {
  $httpProvider.defaults.withCredentials = true;

  $stateProvider.state("banner_recommend",{   //APP推荐-banner
    url:"/banner_recommend",
    templateUrl:"pages/banner_recommend/banner_recommend.html"
  })
    .state("index_recommend",{   //首页推荐-推荐
      url:"/index_recommend",
      templateUrl:"pages/index_recommend/index_recommend.html"
    })
    .state("banner_history",{  //首页推荐-banner-历史数据
      url:"/banner_history",
      templateUrl:"pages/banner_history/banner_history.html"
    })
    .state("index_recommend_history",{  //首页推荐-推荐-历史数据
      url:"/index_recommend_history",
      templateUrl:"pages/index_recommend_history/index_recommend_history.html"
    })
    .state("fenleiguanli",{
      url:"/fenleiguanli",
      templateUrl:"pages/cla_mag/cla_mag.html"
    })
    .state("onsale_edit",{
    params:{"classtitle":null,"classid":null,"classlevel":null},
    url:"/onsale_edit",
    templateUrl:"pages/onsale_edit/onsale_edit.html",
    })
    .state("offsale_edit",{
    params:{"classtitle":null,"classid":null,"classlevel":null},
    url:"/offsale_edit",
    templateUrl:"pages/offsale_edit/offsale_edit.html"
  })
    .state("add_class",{
    url:"/add_class",
    templateUrl:"pages/add_class/add_class.html"
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