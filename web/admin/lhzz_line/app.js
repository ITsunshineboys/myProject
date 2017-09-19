var app = angular.module("app",["ng.ueditor","intelligent_directive","ui.router",
  "clamagModule","onsaleeditModule",
  "offsaleeditModule","addclassModule",'brand_details_module',
  'brand_check','check_right','account_comment','change_num',
  'bind_record','operation_record',"mallmagModule","storemagModule","addstoreModule",
  "onlineeditModule","offlineeditModule","addbrandModule","styleindexModule","chooseseriesModule",
  "addseriesModule",
  "seriesdetailModule","addstyleModule","choose_styleModule","styledetailModule",
  "storedetailModule",

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
  "add_user_module",
  //第三次需求开始
  "login_module"
  //第三次需求结束
  //王杰 结束
]);
/*路由拦截*/
app.config(function ($stateProvider,$httpProvider,$urlRouterProvider) {
  $httpProvider.defaults.withCredentials = true;
  $urlRouterProvider.otherwise("/login");
  $stateProvider

    //  ==============王杰  开始====================
    .state("login",{   //登录
      url:"/login",
      templateUrl:"pages/login/login.html"
    })
    .state("banner_recommend",{   //APP推荐-banner
    url:"/banner_recommend",
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
      params:({on_flag:null,down_flag:null,wait_flag:null,del_flag:null,storeid:null,offlineflag:null,waitflag:null,deleteflag:null}),
      url:"/commodity_manage",
      templateUrl:"pages/mall_manage/merchant_manage/commodity_manage/commodity_manage.html"
    })
    .state("commodity_detail_on",{   //商城管理——商品管理——商品详情（已上架）
      params:{onlinegood:null},
      url:"/commodity_detail_on",
      templateUrl:"pages/mall_manage/merchant_manage/commodity_detail_on/commodity_detail_on.html"
    })
    .state("commodity_detail_down",{   //商城管理——商品管理——商品详情（已下架）
      params:{offlinegood:null},
      url:"/commodity_detail_down",
      templateUrl:"pages/mall_manage/merchant_manage/commodity_detail_down/commodity_detail_down.html"
    })
    .state("commodity_detail_wait",{   //商城管理——商品管理——商品详情（等待上架）
      params:{waitgood:null},
      url:"/commodity_detail_wait",
      templateUrl:"pages/mall_manage/merchant_manage/commodity_detail_wait/commodity_detail_wait.html"
    })
    .state("commodity_detail_del",{   //商城管理——商品管理——商品详情（已删除）
      params:{deletegood:null},
      url:"/commodity_detail_del",
      templateUrl:"pages/mall_manage/merchant_manage/commodity_detail_del/commodity_detail_del.html"
    })
      .state("brand_index",{   //商城管理——品牌管理
        url:"/brand_index",
        templateUrl:"pages/mall_manage/brand_manage/brand_index/brand_index.html",
        params:{down_flag:'',check_flag:''}
      })
    .state("edit_attribute",{   //属性管理——属性编辑
      params:{titles:'',propattrs:'',propid:''},
      url:"/edit_attribute",
      templateUrl:"pages/mall_manage/style_manage/edit_attribute/edit_attribute.html"
    })
    .state("account_index",{   //账户管理
      url:"/account_index",
      templateUrl:"pages/account_manage/account_index/account_index.html",
      params:{icon:'icon',nickname:'nickname'
        ,old_nickna:'old_nickname',district_name:'district_name',birthday:'birthday',
        signature:'signature',mobile:'mobile',aite_cube_no:'aite_cube_no',
        create_time:'create_time',role_names:'role_names',review_status_desc:'review_status_desc',
        status:'status',legal_person:'legal_person',identity_no:'identity_no'
        ,identity_card_front_imagen:'identity_card_front_image',identity_card_back_image:
          'identity_card_back_image',review_time:'review_time',
        status_remark:'status_remark',status_operator:'status_operator',
        a:''}
    })
      .state("add_user",{   //账户管理——添加新用户
        url:"/add_user",
        templateUrl:"pages/account_manage/add_user/add_user.html"
      })

    // =================王杰  结束==============


    .state("fenleiguanli",{
      params:{'showoffsale':null},
      url:"/fenleiguanli",
      templateUrl:"pages/mall_manage/class_manage/cla_mag/cla_mag.html"
    })
    .state("onsale_edit",{
      params:{"classtitle":'',"classid":'',"classlevel":'',"classpath":'',"iconpath":'',"addperson":'',"online_time":''},
      url:"/onsale_edit",
      templateUrl:"pages/mall_manage/class_manage/onsale_edit/onsale_edit.html"
    })
    .state("offsale_edit",{
      params:{"classtitle":'',"classid":'',"classlevel":'',"classpath":'',"iconpath":'',"addperson":'',"offline_time":'',"offline_reason":null},
      url:"/offsale_edit",
      templateUrl:"pages/mall_manage/class_manage/offsale_edit/offsale_edit.html"
    })
    .state("add_class",{
      url:"/add_class",
      templateUrl:"pages/mall_manage/class_manage/add_class/add_class.html"
    })
    .state("store_detail",{
      params:{"store":null},
      url:"/store_detail",
      templateUrl:"pages/mall_manage/merchant_manage/store_mag/store_detail.html"
    })
    .state("merchant_details",{
      url:"/merchant_details",
      templateUrl:"pages/mall_manage/merchant_manage/merchant_comment/merchant_details.html"
    })
  //   .state("comment",{
  //   url:"/comment",
  //   templateUrl:"pages/mall_manage/merchant_manage/merchant_comment/comment.html"
  // })

    .state("check_right",{
    url:"/check_right",
    templateUrl:"pages/mall_manage/merchant_manage/merchant_comment/check-right.html"
  })

    .state("account_comment",{
    url:"/account_comment",
    templateUrl:"pages/account_manage/account_comment/account_comment.html",
    params:{icon:'icon',nickname:'nickname'
            ,old_nickna:'old_nickname',district_name:'district_name',birthday:'birthday',
            signature:'signature',mobile:'mobile',aite_cube_no:'aite_cube_no',
            create_time:'create_time',names:'names',review_status_desc:'review_status_desc',
            status:'status',id:'id',legal_person:'legal_person',identity_no:'identity_no'
            ,identity_card_front_imagen:'identity_card_front_image',identity_card_back_image:
                'identity_card_back_image',review_time:'review_time',status_remark:'status_remark',status_operator:'status_operator',
            a:''
    }
  })

    .state("change_num",{  //更换手机号码
      url:"/change_num",
      templateUrl:"pages/account_manage/account_comment/change_num.html",
      params:{icon:'icon',nickname:'nickname'
        ,old_nickna:'old_nickname',district_name:'district_name',birthday:'birthday',
        signature:'signature',mobile:'mobile',aite_cube_no:'aite_cube_no',
        create_time:'create_time',names:'names',review_status_desc:'review_status_desc',
        status:'status',id:'id',legal_person:'legal_person',identity_no:'identity_no'
        ,identity_card_front_imagen:'identity_card_front_image',identity_card_back_image:
          'identity_card_back_image',review_time:'review_time',
        status_remark:'status_remark',status_operator:'status_operator',a:''}
    })
    .state("bind_record",{
      url:"/bind_record",
      templateUrl:"pages/account_manage/account_comment/bind_record.html",
      params:{icon:'icon',nickname:'nickname'
        ,old_nickna:'old_nickname',district_name:'district_name',birthday:'birthday',
        signature:'signature',mobile:'mobile',aite_cube_no:'aite_cube_no',
        create_time:'create_time',names:'names',review_status_desc:'review_status_desc',
        status:'status',id:'id',legal_person:'legal_person',identity_no:'identity_no'
        ,identity_card_front_imagen:'identity_card_front_image',identity_card_back_image:
          'identity_card_back_image',review_time:'review_time',
        status_remark:'status_remark',status_operator:'status_operator',a:''}
    })
    .state("operation_record",{
      url:"/operation_record",
      templateUrl:"pages/account_manage/account_comment/operation_record.html",
      params:{icon:'icon',nickname:'nickname'
        ,old_nickna:'old_nickname',district_name:'district_name',birthday:'birthday',
        signature:'signature',mobile:'mobile',aite_cube_no:'aite_cube_no',
        create_time:'create_time',names:'names',review_status_desc:'review_status_desc',
        status:'status',id:'id',legal_person:'legal_person',identity_no:'identity_no'
        ,identity_card_front_imagen:'identity_card_front_image',identity_card_back_image:
          'identity_card_back_image',review_time:'review_time',
        status_remark:'status_remark',status_operator:'status_operator',a:''}
    })
    .state("idcard_right",{
      url:"/idcard_right",
      templateUrl:"pages/account_manage/account_comment/idcard_right.html",
      params:{icon:'icon',nickname:'nickname'
        ,old_nickna:'old_nickname',district_name:'district_name',birthday:'birthday',
        signature:'signature',mobile:'mobile',aite_cube_no:'aite_cube_no',
        create_time:'create_time',names:'names',review_status_desc:'review_status_desc',
        status:'status',id:'id',legal_person:'legal_person',identity_no:'identity_no'
        ,identity_card_front_imagen:'identity_card_front_image',identity_card_back_image:
          'identity_card_back_image',review_time:'review_time',
        status_remark:'status_remark',status_operator:'status_operator',a:''}
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
        templateUrl:"pages/mall_manage/brand_manage/online_edit/online_edit.html",
        params:{item:'',index:'',online_time_flag:''}
      })
      .state("offline_edit",{ /*品牌管理-已下架编辑*/
        url:"/offline_edit",
        templateUrl:"pages/mall_manage/brand_manage/offline_edit/offline_edit.html",
        params:{down_shelves_list:'',index:'',online_time_flag:''}
      })
      .state("add_brand",{ /*品牌管理-添加品牌*/
        url:"/add_brand",
        templateUrl:"pages/mall_manage/brand_manage/add_brand/add_brand.html"
      })
    .state("brand_details",{ /*品牌管理-品牌详情*/
      url:"/brand_details",
      templateUrl:"pages/mall_manage/brand_manage/brand_details/brand_details.html",
      params:{item:''}
    })
    .state("brand_check",{ /*品牌管理-品牌详情（审核）*/
      url:"/brand_check",
      templateUrl:"pages/mall_manage/brand_manage/brand_check/brand_check.html",
      params:{item:''}
    })
      .state("style_index",{ /*系列/风格/属性管理*/
        url:"/style_index",
        templateUrl:"pages/mall_manage/style_manage/style_index/style_index.html",
        params:{showstyle:'',page:'',showattr:null}
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
        params:{item:'',ser_arr:'',index:''}
      })
      .state("add_style",{ /*系列/风格/属性管理-风格-添加新风格*/
        url:"/add_style",
        templateUrl:"pages/mall_manage/style_manage/add_style/add_style.html",
        params:{style_arr:''}
      })
      .state("choose_style",{ /*系列/风格/属性管理-风格-选择拥有风格的分类*/
        url:"/choose_style",
        templateUrl:"pages/mall_manage/style_manage/choose_style/choose_style.html"
      })
      .state("style_detail",{ /*系列/风格/属性管理-风格-风格详情*/
        url:"/style_detail",
        templateUrl:"pages/mall_manage/style_manage/style_detail/style_detail.html",
        params:{style_item:'',page:''}
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