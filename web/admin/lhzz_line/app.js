var app = angular.module("app",["ng.ueditor","intelligent_directive","ui.router",
  "clamagModule","onsaleeditModule",
  "offsaleeditModule","addclassModule",'brand_details_module',
  'brand_check','check_right','account_comment','change_num',
  'bind_record','operation_record',"mallmagModule","storemagModule","addstoreModule",
  "onlineeditModule","offlineeditModule","addbrandModule","styleindexModule","chooseseriesModule",
  "addseriesModule",
  "seriesdetailModule","addstyleModule","choose_styleModule","styledetailModule",
  "storedetailModule",

  "intelligent_index",'angularCSS','intelligent_directive','apply_case','distribution',

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
      //========================张放====================================
      //智能报价
      .state('intelligent',{//智能报价头部
          url:'/intelligent/',
          templateUrl:'pages/intelligent/index.html',
          css:'pages/intelligent/css/apply_case_header.css',
      })
      .state('intelligent.intelligent_index',{//智能报价首页
          url:'index',
          templateUrl:'pages/intelligent/intelligent_index.html',
          css:'pages/intelligent/css/intelligent_index.css'
      })
      .state('intelligent.house_list',{//智能报价小区列表
          url:'house_list',
          templateUrl:'pages/intelligent/house_list.html',
          css:'pages/intelligent/css/house_list.css'
      })
      .state('intelligent.add_house',{//智能报价添加/编辑小区
          url:'add_house',
          templateUrl:'pages/intelligent/add_house.html',
          css:'pages/intelligent/css/add_house.css'
      })
      .state('intelligent.add_case',{//智能报价添加/编辑案例
          url:'add_case',
          templateUrl:'pages/intelligent/add_case.html',
          css:'pages/intelligent/css/add_case.css'
      })
      .state('intelligent.edit_house',{//智能报价添加/编辑普通小区
          url:'edit_house',
          templateUrl:'pages/intelligent/edit_house.html',
          css:'pages/intelligent/css/edit_house.css'
      })
      .state('intelligent.add_drawing',{//智能报价添加/编辑普通小区图纸
          url:'add_drawing',
          templateUrl:'pages/intelligent/add_drawing.html',
          css:'pages/intelligent/css/add_drawing.css'
      })
      .state('intelligent.add_support_goods',{//智能报价案列/社区店配套商品管理
          url:'add_support_goods',
          templateUrl:'pages/intelligent/add_support_goods.html',
          css:'pages/intelligent/css/add_support_goods.css'
      })
      .state('intelligent.worker_price_list',{//智能报价工人资费列表
          url:'worker_price_list',
          templateUrl:'pages/intelligent/worker_price_list.html',
          css:'pages/intelligent/css/worker_price_list.css'
      })
      .state('intelligent.edit_worker',{//智能报价工人资费编辑
          url:'edit_worker',
          templateUrl:'pages/intelligent/edit_worker.html',
          css:'pages/intelligent/css/edit_worker.css'
      })
      // .state('intelligent.add_worker',{
      //     url:'add_worker',
      //     templateUrl:'pages/intelligent/add_worker.html',
      //     css:'pages/intelligent/css/add_worker.css'
      // })
      .state('intelligent.home_manage',{//智能报价首页管理
          url:'home_manage',
          templateUrl:'pages/intelligent/home_manage.html',
          css:'pages/intelligent/css/home_manage.css'
      })
      .state('intelligent.add_manage',{//添加推荐
          url:'add_manage',
          templateUrl:'pages/intelligent/add_manage.html',
          css:'pages/intelligent/css/add_manage.css'
      })
      .state('intelligent.engineering_standards',{//工程标准
          url:'engineering_standards',
          templateUrl:'pages/intelligent/engineering_standards.html',
          css:'pages/intelligent/css/engineering_standards.css'
      })
      .state('intelligent.engineering_process',{//工程标准编辑
          url:'engineering_process',
          templateUrl:'pages/intelligent/engineering_process.html',
          css:'pages/intelligent/css/engineering_process.css'
      })
    //样板间申请
      .state('apply_case',{
          url:'/apply_case/',
          templateUrl:'pages/apply_case/index.html',
          // css:'pages/apply_case/css/apply_case_header.css'
      })
      .state('apply_case.index',{//样板间申请主页
          url:'index',
          templateUrl:'pages/apply_case/apply_case_index.html',
          css:'pages/apply_case/css/apply_case_index.css'
      })
      .state('apply_case.case_detail',{//样板间申请详情
          url:'case_detail',
          templateUrl:'pages/apply_case/case_detail.html',
          css:'pages/apply_case/css/case_detail.css'
      })
    //分销
      .state('distribution',{
          url:'/distribution/',
          templateUrl:'pages/distribution/index.html'
      })
      .state('distribution.index',{//分销主页
      url:'index',
      templateUrl:'pages/distribution/distribution_index.html',
      css:'pages/distribution/css/distribution_index.css'
  })
      .state('distribution.detail',{//分销详情
          url:'detail',
          templateUrl:'pages/distribution/distribution_detail.html',
          css:'pages/distribution/css/distribution_detail.css'
      })
      .state('distribution.associate_list',{
          url:'associate_list',
          templateUrl:'pages/distribution/associate_list.html',
          css:'pages/distribution/css/associate_list.css'
      })
      /*=============== 廖欢 start ===============*/
      .state('home', {  // 首页
          url: '/home',
          templateUrl: 'pages/home/home.html',
          css: 'pages/home/css/home.css'
      })
      .state('order', { // 订单管理
          abstract:true,
          url: '/order?id',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/order.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'order'
      })
      .state('order.all', { // 全部订单
          url: '/all',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/order_all.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'order_all'
      })
      .state('order.unpaid', {  // 待付款订单
          url: '/unpaid',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/order_unpaid.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'order_unpaid'
      })
      .state('order.unshipped', {   // 待发货订单
          url: '/unshipped',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/order_unshipped.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'order_unshipped'
      })
      .state('order.unreceived', {  // 待收货订单
          url: '/unreceived',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/order_unreceived.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'order_unreceived'
      })
      .state('order.completed', {   // 已完成订单
          url: '/completed',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/order_completed.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'order_completed'
      })
      .state('order.cancel', {  // 已取消订单
          url: '/cancel',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/order_cancel.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'order_cancel'
      })
      .state('comments_del', {  // 删除评论列表
          url: '/order/comments?id',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/comments_del/comments_del.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'comments'
      })
      .state('order_details', { // 订单详情
          url: '/order/details?orderNo&sku&status&type',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/order_details.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order_details.css',
          controller: 'order_details'
      })
      .state('express', {   // 物流详情
          url: '/order/express?orderNo&sku',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/express/order_express.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order_details.css',
          controller: 'express'
      })
      .state('comm_details', {  // 删除评论详情
          url: '/order/comments/details?orderNo&sku',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/comments_del/del_details.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order_details.css',
          controller: 'comments_details'
      })
      .state('goods_details', { // 商品详情
          url: '/order/goods/details?orderNo&sku',
          templateUrl: 'pages/mall_manage/merchant_manage/order_manage/goods_details/goods_details.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/goods_details.css',
          controller: 'order_goods'
      })
      .state('search', {    // 搜索页面
          url: '/merchant_index/search',
          templateUrl: 'pages/mall_manage/merchant_manage/search_page/search_page.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'searchCtrl'
      })
      .state('mall_data', { // 商城数据
          url: '/mall_data',
          templateUrl: 'pages/mall_manage/data_page/data_page.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'mallDataCtrl'
      })
      .state('store_data', {    // 店铺数据
          url: '/store_mag/store_data?id',
          templateUrl: 'pages/mall_manage/data_page/data_page.html',
          css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
          controller: 'storeDataCtrl'
      })
    /*=============== 廖欢 end ===============*/
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