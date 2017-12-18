var app = angular.module("app", ["ng.ueditor", "ui.router","ngFileUpload",
    "addclassModule", 'brand_details_module', 'account_comment', 'change_num', 'bind_record', 'operation_record',
    "mallmagModule", "storemagModule", "addstoreModule", "onlineeditModule", "offlineeditModule", "addbrandModule",
    "styleindexModule", "chooseseriesModule", "addseriesModule", "seriesdetailModule", "addstyleModule",
    "choose_styleModule", "styledetailModule", "storedetailModule", "merchant_details", "intelligent_index",
    'angularCSS', 'distribution', 'mall_finance', 'idcard_right',
    //  王杰 开始
    "banner_recommend_module",
    "index_recommend_module",
    "banner_history_module",
    "index_recommend_history_module",
    "brand_index_module",
    "brand_check_module",
    "edit_attribute_module",
    "add_user_module",
    "checklist-model",
    "new_brand_module",
    "new_class_module",
    "new_brand_check_module",
    "new_class_check_module"
    //王杰 结束
]);
/*路由拦截*/
app.config(function ($stateProvider, $httpProvider, $urlRouterProvider) {
    $httpProvider.defaults.withCredentials = true;
    $urlRouterProvider.otherwise("/home");
    $stateProvider

    /*---------------------------王杰开始--------------------------------------*/
        .state("banner_recommend", {   //APP推荐-banner
            url: "/banner_recommend",
            templateUrl: "pages/mall_manage/banner_app/banner_recommend/banner_recommend.html"
        })
        .state("index_recommend", {   //首页推荐-推荐
            url: "/index_recommend",
            templateUrl: "pages/mall_manage/banner_app/index_recommend/index_recommend.html"
        })
        .state("banner_history", {  //首页推荐-banner-历史数据
            url: "/banner_history",
            templateUrl: "pages/mall_manage/banner_app/banner_history/banner_history.html"
        })
        .state("index_recommend_history", {  //首页推荐-推荐-历史数据
            url: "/index_recommend_history",
            templateUrl: "pages/mall_manage/banner_app/index_recommend_history/index_recommend_history.html"
        })

        .state("brand_index", {   //商城管理——品牌管理
            url: "/brand_index",
            templateUrl: "pages/mall_manage/brand_manage/brand_index/brand_index.html",
            params: {down_flag: '', check_flag: ''}
        })
        .state("online_edit", {
            /*品牌管理-已上架编辑*/
            url: "/online_edit",
            templateUrl: "pages/mall_manage/brand_manage/online_edit/online_edit.html",
            params: {on_shelves_list: ''}
        })
        .state("offline_edit", {
            /*品牌管理-已下架编辑*/
            url: "/offline_edit",
            templateUrl: "pages/mall_manage/brand_manage/offline_edit/offline_edit.html",
            params: {down_shelves_list: ''}
        })
        .state("add_brand", {
            /*品牌管理-添加品牌*/
            url: "/add_brand",
            templateUrl: "pages/mall_manage/brand_manage/add_brand/add_brand.html"
        })
        .state("brand_details", {
            /*品牌管理-品牌详情*/
            url: "/brand_details",
            templateUrl: "pages/mall_manage/brand_manage/brand_details/brand_details.html",
            params: {item: ''}
        })
        .state("brand_check", {
            /*品牌管理-品牌详情*/
            url: "/brand_check",
            templateUrl: "pages/mall_manage/brand_manage/brand_check/brand_check.html",
            params: {item: ''}
        })
        .state("style_index", {
            /*系列/风格/属性管理*/
            url: "/style_index",
            templateUrl: "pages/mall_manage/style_manage/style_index/style_index.html",
            params: {showstyle: '', page: '', showattr: null}
        })

        .state("add_series", {
            /*系列/风格/属性管理-添加新系列*/
            url: "/add_series",
            templateUrl: "pages/mall_manage/style_manage/add_series/add_series.html",
            params: {"list": ""}
        })
        .state("series_detail", {
            /*系列/风格/属性管理-系列详情页*/
            url: "/series_detail",
            templateUrl: "pages/mall_manage/style_manage/series_detail/series_detail.html",
            params: {item: '', ser_arr: '', index: ''}
        })
        .state("add_style", {
            /*系列/风格/属性管理-风格-添加新风格*/
            url: "/add_style",
            templateUrl: "pages/mall_manage/style_manage/add_style/add_style.html",
            params: {style_arr: ''}
        })
        .state("style_detail", {
            /*系列/风格/属性管理-风格-风格详情*/
            url: "/style_detail",
            templateUrl: "pages/mall_manage/style_manage/style_detail/style_detail.html",
            params: {style_item: '', page: ''}
        })
        .state("new_brand_class", {
            /*新品牌/分类管理*/
            abstract: true,
            url: "/new_brand_class",
            templateUrl: "pages/mall_manage/new_brand_class_manage/new_brand_class/new_brand_class_tab.html"
        })
        .state("new_brand_class.new_brand", {
            /*新品牌管理*/
            url: "/new_brand",
            templateUrl: "pages/mall_manage/new_brand_class_manage/new_brand_class/new_brand.html"
        })
        .state("new_brand_class.new_class", {
            /*新分类管理*/
            url: "/new_class",
            templateUrl: "pages/mall_manage/new_brand_class_manage/new_brand_class/new_class.html"
        })
        .state("new_brand_check", {
            /*新品牌审核*/
            url: "/new_brand_check?brand_id&review_status",
            templateUrl: "pages/mall_manage/new_brand_class_manage/new_brand_class/new_brand_check.html",
        })
        .state("new_class_check", {
            /*新分类审核*/
            url: "/new_class_check?cate_id&review_status",
            templateUrl: "pages/mall_manage/new_brand_class_manage/new_brand_class/new_class_check.html",
        })
        /*---------------------------王杰结束--------------------------------------*/

        /*---------------------------谢力开始--------------------------------------*/
        .state("add_user", {   //账户管理——添加新用户
            url: "/add_user",
            templateUrl: "pages/account_manage/user_list/add_user/add_user.html",
            css: "pages/account_manage/user_list/add_user/css/add_user.css"
        })

        .state("account_comment", {
            url: "/account_comment?id",
            templateUrl: "pages/account_manage/user_list/account_comment/account_comment.html",
            css: "pages/account_manage/user_list/account_comment/css/account_comment.css"
        })

        .state("change_num", {  //更换手机号码
            url: "/change_num",
            templateUrl: "pages/account_manage/user_list/account_comment/change_num.html",
            css: "pages/account_manage/user_list/account_comment/css/change_num.css",
            params: {
                icon: 'icon',
                nickname: 'nickname',
                old_nickna: 'old_nickname',
                district_name: 'district_name',
                birthday: 'birthday',
                signature: 'signature',
                mobile: 'mobile',
                aite_cube_no: 'aite_cube_no',
                create_time: 'create_time',
                names: 'names',
                review_status_desc: 'review_status_desc',
                status: 'status',
                id: 'id',
                legal_person: 'legal_person',
                identity_no: 'identity_no'
                ,
                identity_card_front_imagen: 'identity_card_front_image',
                identity_card_back_image: 'identity_card_back_image',
                review_time: 'review_time',
                status_remark: 'status_remark',
                status_operator: 'status_operator',
                a: ''
            }
        })
        .state("bind_record", {
            url: "/bind_record?id",
            templateUrl: "pages/account_manage/user_list/account_comment/bind_record.html",
            css: "pages/account_manage/user_list/account_comment/css/bind_record.css",
            params: {
                icon: 'icon',
                nickname: 'nickname'
                ,
                old_nickna: 'old_nickname',
                district_name: 'district_name',
                birthday: 'birthday',
                signature: 'signature',
                mobile: 'mobile',
                aite_cube_no: 'aite_cube_no',
                create_time: 'create_time',
                names: 'names',
                review_status_desc: 'review_status_desc',
                status: 'status',
                id: 'id',
                legal_person: 'legal_person',
                identity_no: 'identity_no'
                ,
                identity_card_front_imagen: 'identity_card_front_image',
                identity_card_back_image: 'identity_card_back_image',
                review_time: 'review_time',
                status_remark: 'status_remark',
                status_operator: 'status_operator',
                a: ''
            }
        })
        .state("operation_record", {
            url: "/operation_record",
            templateUrl: "pages/account_manage/user_list/account_comment/operation_record.html",
            css: "pages/account_manage/user_list/account_comment/css/operation_record.css",
            params: {
                icon: 'icon',
                nickname: 'nickname'
                ,
                old_nickna: 'old_nickname',
                district_name: 'district_name',
                birthday: 'birthday',
                signature: 'signature',
                mobile: 'mobile',
                aite_cube_no: 'aite_cube_no',
                create_time: 'create_time',
                names: 'names',
                review_status_desc: 'review_status_desc',
                status: 'status',
                id: 'id',
                legal_person: 'legal_person',
                identity_no: 'identity_no'
                ,
                identity_card_front_imagen: 'identity_card_front_image',
                identity_card_back_image: 'identity_card_back_image',
                review_time: 'review_time',
                status_remark: 'status_remark',
                status_operator: 'status_operator',
                a: ''
            }
        })
        .state("idcard_right", {
            url: "/idcard_right?id",
            templateUrl: "pages/account_manage/user_list/account_comment/idcard_right.html",
            css: "pages/account_manage/user_list/account_comment/css/idcard_right.css",
            params: {
                icon: 'icon',
                nickname: 'nickname'
                ,
                old_nickna: 'old_nickname',
                district_name: 'district_name',
                birthday: 'birthday',
                signature: 'signature',
                mobile: 'mobile',
                aite_cube_no: 'aite_cube_no',
                create_time: 'create_time',
                names: 'names',
                review_status_desc: 'review_status_desc',
                status: 'status',
                id: 'id',
                legal_person: 'legal_person',
                identity_no: 'identity_no'
                ,
                identity_card_front_imagen: 'identity_card_front_image',
                identity_card_back_image: 'identity_card_back_image',
                review_time: 'review_time',
                status_remark: 'status_remark',
                status_operator: 'status_operator',
                a: ''
            }
        })
        .state("choose_series", {
            /*系列/风格/属性管理-选择拥有系列的分类*/
            url: "/choose_series",
            templateUrl: "pages/mall_manage/style_manage/choose_series/choose_series.html"
        })
        .state("choose_style", {
            /*系列/风格/属性管理-风格-选择拥有风格的分类*/
            url: "/choose_style",
            templateUrl: "pages/mall_manage/style_manage/choose_style/choose_style.html"
        })

        /*---------------------------谢力结束--------------------------------------*/


        /*=============== 芳子 start ===============*/
        .state('class', { // 分类管理
            abstract: true,
            url: '/class_mag',
            templateUrl: 'pages/mall_manage/class_manage/cla_mag/class.html',
            css: 'pages/mall_manage/class_manage/cla_mag/css/cla_mag.css',
            controller: 'class'
        })
        .state('class.online', { // 已上架分类
            url: '/class_online',
            templateUrl: 'pages/mall_manage/class_manage/cla_mag/class_online.html',
            css: 'pages/mall_manage/class_manage/cla_mag/css/cla_mag.css',
            controller: 'class_online'
        })
        .state('class.offline', { // 已下架分类
            url: '/class_offline',
            templateUrl: 'pages/mall_manage/class_manage/cla_mag/class_offline.html',
            css: 'pages/mall_manage/class_manage/cla_mag/css/cla_mag.css',
            controller: 'class_offline'
        })

        .state("onsale_edit", { //  已上架分类 - 编辑
            params: {item: null},
            url: '/onsale_edit',
            css: 'pages/mall_manage/class_manage/onsale_edit/css/onsale_edit.css',
            templateUrl: 'pages/mall_manage/class_manage/onsale_edit/onsale_edit.html',
            controller: 'onsale_edit'
        })

        .state("offsale_edit", { //  已下架分类 - 编辑
            params: {item: null},
            url: "/offsale_edit",
            css: 'pages/mall_manage/class_manage/onsale_edit/css/onsale_edit.css',
            templateUrl: "pages/mall_manage/class_manage/offsale_edit/offsale_edit.html",
            controller: 'offsale_edit'
        })

        .state("add_class", {  //添加分类
            url: "/add_class",
            templateUrl: "pages/mall_manage/class_manage/add_class/add_class.html",
            css: "pages/mall_manage/class_manage/add_class/css/add_class.css"
        })

        .state("merchant_index", {  //商城管理首页
            url: "/merchant_index",
            templateUrl: "pages/mall_manage/merchant_manage/merchant_index/merchant_index.html",
            css: "pages/mall_manage/merchant_manage/merchant_index/css/merchant_index.css"
        })
        .state("store_mag", {       //商家管理
            url: "/store_mag",
            templateUrl: "pages/mall_manage/merchant_manage/store_mag/store_mag.html",
            css: "pages/mall_manage/merchant_manage/store_mag/css/store_mag.css"
        })
        .state("add_store", {       //添加商家
            url: "/add_store",
            templateUrl: "pages/mall_manage/merchant_manage/add_store/add_store.html",
            css: "pages/mall_manage/merchant_manage/add_store/css/add_store.css"
        })
        .state("store_detail", {    //商家详情
            params: {"store": null, "authorize_flag": null},
            url: "/store_detail",
            templateUrl: "pages/mall_manage/merchant_manage/store_mag/store_detail.html"
        })
        .state("merchant_details", { //品牌授权详情
            params: {"itemdetail": null, "store": null},
            url: "/merchant_details",
            templateUrl: "pages/mall_manage/merchant_manage/merchant_comment/merchant_details.html"
        })
        .state('commodity', {       //商品管理
            abstract: true,
            url: '/commodity?id',
            templateUrl: "pages/mall_manage/merchant_manage/commodity_manage/commodity/commodity.html",
            css: 'pages/mall_manage/merchant_manage/commodity_manage/css/commodity_manage.css',
            controller: 'commodity'
        })
        .state('commodity.online', { //已上架
            url: '/online',
            templateUrl: 'pages/mall_manage/merchant_manage/commodity_manage/commodity/commodity_online.html',
            css: 'pages/mall_manage/merchant_manage/commodity_manage/css/commodity_manage.css',
            controller: 'commodity_online'
        })
        .state('commodity.offline', { //已下架
            url: '/offline',
            templateUrl: 'pages/mall_manage/merchant_manage/commodity_manage/commodity/commodity_offline.html',
            css: 'pages/mall_manage/merchant_manage/commodity_manage/css/commodity_manage.css',
            controller: 'commodity_offline'
        })
        .state('commodity.wait', { //等待上架
            url: '/wait',
            templateUrl: 'pages/mall_manage/merchant_manage/commodity_manage/commodity/commodity_wait.html',
            css: 'pages/mall_manage/merchant_manage/commodity_manage/css/commodity_manage.css',
            controller: 'commodity_wait'
        })
        .state('commodity.deleted', { //已删除
            url: '/deleted',
            templateUrl: 'pages/mall_manage/merchant_manage/commodity_manage/commodity/commodity_deleted.html',
            css: 'pages/mall_manage/merchant_manage/commodity_manage/css/commodity_manage.css',
            controller: 'commodity_deleted'
        })

        .state('commodity_detail', { // 商品详情
            params: {id: null, storeid: null},
            url: '/commodity_detail',
            templateUrl: 'pages/mall_manage/merchant_manage/commodity_manage/commodity_details/commodity_detail.html',
            css: 'pages/mall_manage/merchant_manage/commodity_manage/commodity_details/css/commodity_detail.css',
            controller: 'commodity_detail'
        })
        .state("edit_attribute", {   //属性管理——属性编辑
            params: {titles: '', propattrs: '', propid: ''},
            url: "/edit_attribute",
            templateUrl: "pages/mall_manage/style_manage/edit_attribute/edit_attribute.html"
        })

        .state('account_user_list', { //账户管理 - 用户列表
            abstract: true,
            url: '/account_mag_list',
            templateUrl: 'pages/account_manage/user_list/account_mag/user_list.html',
            controller: 'account_user_list'
        })

        .state('account_user_list.normal', { //正常
            url: '/account_user_list_normal',
            templateUrl: 'pages/account_manage/user_list/account_mag/user_list_normal.html',
            css: 'pages/account_manage/user_list/account_mag/css/user_list.css',
            controller: 'account_user_list_normal'
        })

        .state('account_user_list.closed', { //关闭
            url: '/account_user_list_closed',
            templateUrl: 'pages/account_manage/user_list/account_mag/user_list_closed.html',
            css: 'pages/account_manage/user_list/account_mag/css/user_list.css',
            controller: 'account_user_list_closed'
        })

        .state('account_user_verify', { //账户管理 - 用户审核
            abstract: true,
            url: '/account_user_verify',
            templateUrl: 'pages/account_manage/user_verify/user_verify.html',
            controller: 'account_user_verify'
        })

        .state('account_user_verify.wait', { // 用户审核 - 等待
            url: '/account_user_verify_wait',
            templateUrl: 'pages/account_manage/user_verify/user_verify_wait.html',
            controller: 'account_user_verify_wait'
        })

        .state('account_user_verify.pass', { // 用户审核 - 通过
            url: '/account_user_verify_pass',
            templateUrl: 'pages/account_manage/user_verify/user_verify_pass.html',
            controller: 'account_user_verify_pass'
        })

        .state('account_user_verify.fail', { // 用户审核 - 未通过
            url: '/account_user_verify_fail',
            templateUrl: 'pages/account_manage/user_verify/user_verify_fail.html',
            controller: 'account_user_verify_fail'
        })

        .state('user_verify_detail', { // 用户审核 - 详情
            url: '/user_verify_detail?id',
            templateUrl: 'pages/account_manage/user_verify/user_verify_detail.html',
            css: 'pages/account_manage/user_verify/css/user_verify_detail.css',
            controller: 'user_verify_detail'
        })

        .state('settle_verify', { //商家入驻审核
            url: '/settle_verify',
            templateUrl: 'pages/mall_manage/merchant_settle/settle_verify.html',
            controller: 'settle_verify'
        })

        .state('settle_verify.wait', { //商家入驻审核 -- 等待
            url: '/settle_verify_wait',
            templateUrl: 'pages/mall_manage/merchant_settle/settle_verify_wait.html',
            css: 'pages/mall_manage/merchant_settle/css/settle_verify.css',
            controller: 'settle_verify_wait'
        })

        .state('settle_verify.pass', { //商家入驻审核 -- 通过
            url: '/settle_verify_pass',
            templateUrl: 'pages/mall_manage/merchant_settle/settle_verify_pass.html',
            css: 'pages/mall_manage/merchant_settle/css/settle_verify.css',
            controller: 'settle_verify_pass'
        })

        .state('settle_verify.fail', { //商家入驻审核 -- 未通过
            url: '/settle_verify_fail',
            templateUrl: 'pages/mall_manage/merchant_settle/settle_verify_fail.html',
            css: 'pages/mall_manage/merchant_settle/css/settle_verify.css',
            controller: 'settle_verify_fail'
        })

        .state('verify_detail', { //商家入驻审核 -- 详情
            url: '/verify_detail?id',
            templateUrl: 'pages/mall_manage/merchant_settle/verify_detail.html',
            css: 'pages/mall_manage/merchant_settle/css/verify_detail.css',
            controller: 'verify_detail'
        })

        /*=============== 芳子 end ===============*/

        //========================张放====================================
        //智能报价
        .state('intelligent', {//智能报价头部
            url: '/intelligent/',
            templateUrl: 'pages/intelligent/index.html',
            // css: 'pages/intelligent/css/apply_case_header.css'
        })
        .state('intelligent.intelligent_index', {//智能报价首页
            url: 'index',
            templateUrl: 'pages/intelligent/intelligent_index.html',
            css: 'pages/intelligent/css/intelligent_index.css'
        })
        .state('intelligent.house_list', {//智能报价小区列表
            url: 'house_list',
            templateUrl: 'pages/intelligent/house_list.html',
            css: 'pages/intelligent/css/house_list.css'
        })
        .state('intelligent.add_house', {//智能报价添加/编辑小区
            url: 'add_house',
            templateUrl: 'pages/intelligent/add_house.html',
            css: 'pages/intelligent/css/add_house.css'
        })
        .state('intelligent.add_case', {//智能报价添加/编辑案例
            url: 'add_case',
            templateUrl: 'pages/intelligent/add_case.html',
            css: 'pages/intelligent/css/add_case.css'
        })
        .state('intelligent.edit_house', {//智能报价添加/编辑普通小区
            url: 'edit_house',
            templateUrl: 'pages/intelligent/edit_house.html',
            css: 'pages/intelligent/css/edit_house.css'
        })
        .state('intelligent.add_drawing', {//智能报价添加/编辑普通小区图纸
            url: 'add_drawing',
            templateUrl: 'pages/intelligent/add_drawing.html',
            css: 'pages/intelligent/css/add_drawing.css'
        })
        .state('add_support_goods', {//智能报价案列/社区店配套商品管理
            url: '/add_support_goods?city&province&name',
            templateUrl: 'pages/intelligent/add_support_goods.html',
            css: 'pages/intelligent/css/add_support_goods.css',
            controller: 'support_goods_ctrl'
        })
        .state('worker_price_list', {//智能报价工人资费列表
            url: '/worker_price_list?city&province',
            templateUrl: 'pages/intelligent/worker_price_list.html',
            css: 'pages/intelligent/css/worker_price_list.css',
            controller: 'worker_price_ctrl'
        })
        .state('edit_worker', {//智能报价工人资费编辑
            url: '/edit_worker?id',
            templateUrl: 'pages/intelligent/edit_worker.html',
            css: 'pages/intelligent/css/edit_worker.css',
            controller: 'edit_worker_ctrl'
        })
        .state('intelligent.home_manage', {//智能报价首页管理
            url: 'home_manage',
            templateUrl: 'pages/intelligent/home_manage.html',
            css: 'pages/intelligent/css/home_manage.css'
        })
        .state('intelligent.add_manage', {//添加推荐
            url: 'add_manage',
            templateUrl: 'pages/intelligent/add_manage.html',
            css: 'pages/intelligent/css/add_manage.css'
        })
        .state('engineering_standards', {//工程标准
            url: '/engineering_standards?city&province',
            templateUrl: 'pages/intelligent/engineering_standards.html',
            css: 'pages/intelligent/css/engineering_standards.css',
            controller: 'engineering_standards_ctrl'
        })
        .state('engineering_process', {//工程标准编辑
            url: '/engineering_process?city&project',
            templateUrl: 'pages/intelligent/engineering_process.html',
            css: 'pages/intelligent/css/engineering_process.css',
            controller: 'engineering_process_ctrl'
        })
        .state('coefficient_manage', {//系数管理
            url: '/coefficient_manage?city&province',
            templateUrl: 'pages/intelligent/coefficient_manage.html',
            css: 'pages/intelligent/css/coefficient_manage.css',
            controller: 'coefficient_manage_ctrl'
        })
        .state('add_material', {//添加材料项
            url: '/add_material?city&province',
            templateUrl: 'pages/intelligent/add_material.html',
            css: 'pages/intelligent/css/add_material.css',
            controller: 'add_material_ctrl'
        })
        .state('material_detail', {//添加材料详情
            url: '/material_detail?status&id&city',
            templateUrl: 'pages/intelligent/material_detail.html',
            css: 'pages/intelligent/css/material_detail.css',
            controller: 'material_detail_ctrl'
        })
        .state('house_area', {//房屋面积编辑
            url: '/house_area?id',
            templateUrl: 'pages/intelligent/house_area.html',
            css: 'pages/intelligent/css/house_area.css',
            controller:'house_area_ctrl'
        })
        .state('general_manage', {//通用管理列表
            url: '/general_manage',
            templateUrl: 'pages/intelligent/general_manage.html',
            css: 'pages/intelligent/css/engineering_standards.css',
            controller:'general_manage_ctrl'
        })
        .state('general_detail', {//通用管理详情
            url: '/general_detail?id',
            templateUrl: 'pages/intelligent/general_detail.html',
            css: 'pages/intelligent/css/general_detail.css',
            controller:'general_detail_ctrl'
        })
        .state('else_general_manage', {//其他通用管理
            url: '/else_general_manage?id&title',
            templateUrl: 'pages/intelligent/else_general_manage.html',
            css: 'pages/intelligent/css/else_general_manage.css',
            controller:'else_general_ctrl'
        })
        .state('goods_manage', {//智能报价商品管理
            url: '/goods_manage?city&province',
            templateUrl: 'pages/intelligent/goods_manage.html',
            css: 'pages/intelligent/css/goods_manage.css',
            controller: 'goods_manage_ctrl'
        })
        //样板间申请
        .state('apply_case_index', {//装修申请
            url: '/apply_case_index',
            templateUrl: 'pages/apply_case/apply_case_index.html',
            css: 'pages/apply_case/css/apply_case_index.css',
            controller: 'apply_case_ctrl'
        })
        .state('case_detail', {//申请详情
            url: '/case_detail?id',
            templateUrl: 'pages/apply_case/case_detail.html',
            css: 'pages/apply_case/css/case_detail.css',
            controller: 'case_detail_ctrl'
        })
        //分销
        .state('distribution', {
            url: '/distribution/',
            templateUrl: 'pages/distribution/index.html'
        })
        .state('distribution.index', {//分销主页
            url: 'index',
            templateUrl: 'pages/distribution/distribution_index.html',
            css: 'pages/distribution/css/distribution_index.css'
        })
        .state('distribution.detail', {//分销详情
            url: 'detail',
            templateUrl: 'pages/distribution/distribution_detail.html',
            css: 'pages/distribution/css/distribution_detail.css'
        })
        .state('distribution.associate_list', {//相关联订单
            url: 'associate_list',
            templateUrl: 'pages/distribution/associate_list.html',
            css: 'pages/distribution/css/associate_list.css'
        })
        //财务中心
        //商城财务
        .state('mall_finance', {
            url: '/mall_finance/',
            templateUrl: 'pages/financial_center/mall/index.html'
        })
        .state('mall_finance.index', {//商城财务主页
            url: 'index',
            templateUrl: 'pages/financial_center/mall/mall_finance.html',
            css: 'pages/financial_center/mall/css/mall_finance.css'
        })
        .state('mall_finance.withdraw', {//商家提现管理
            url: 'withdraw',
            templateUrl: 'pages/financial_center/mall/withdraw_manage.html',
            css: 'pages/financial_center/mall/css/withdraw_manage.css'
        })
        .state('mall_finance.withdraw_manage_detail', {//商家提现管理详情
            url: 'withdraw_manage_detail?index',
            templateUrl: 'pages/financial_center/mall/withdraw_manage_detail.html',
            css: 'pages/financial_center/mall/css/account_detail.css'
        })
        .state('mall_finance.account', {//财务账户管理
            url: 'account',
            templateUrl: 'pages/financial_center/mall/account.html',
            css: 'pages/financial_center/mall/css/account.css'
        })
        .state('mall_finance.account_detail', {//财务账户详情
            url: 'account_detail',
            templateUrl: 'pages/financial_center/mall/account_detail.html',
            css: 'pages/financial_center/mall/css/account_detail.css'
        })
        .state('mall_finance.freeze_money', {//冻结金额
            url: 'freeze_money',
            templateUrl: 'pages/financial_center/mall/freeze_money.html',
            css: 'pages/financial_center/mall/css/freeze_money.css'
        })
        .state('mall_finance.withdraw_list', {//提现列表
            url: 'withdraw_list',
            templateUrl: 'pages/financial_center/mall/withdraw_list.html',
            css: 'pages/financial_center/mall/css/withdraw_list.css'
        })
        .state('mall_finance.freeze_list', {//冻结金额列表
            url: 'freeze_list',
            templateUrl: 'pages/financial_center/mall/freeze_list.html',
            css: 'pages/financial_center/mall/css/withdraw_list.css'
        })
        .state('mall_finance.withdraw_detail', {//提现详情
            url: 'withdraw_detail',
            templateUrl: 'pages/financial_center/mall/withdraw_detail.html',
            css: 'pages/financial_center/mall/css/account_detail.css'
        })
        .state('mall_finance.recorded_detail', {
            url: 'recorded_detail',
            templateUrl: 'pages/financial_center/mall/recorded_detail.html',
            css: 'pages/financial_center/mall/css/withdraw_manage.css'
        })
        .state('mall_finance.money_list', {//收支列表
            url: 'money_list',
            templateUrl: 'pages/financial_center/mall/money_list.html',
            css: 'pages/financial_center/mall/css/account.css'
        })
        /*业主财务中心*/
        .state('owner_finance', {//业主财务中心主页
            url: '/owner_finance',
            templateUrl: 'pages/financial_center/owner/owner_finance.html',
            css: 'pages/financial_center/owner/css/owner_finance.css',
            controller: 'owner_finance_ctrl'
        })
        .state('withdraw_manage', {//业主提现管理
            url: '/withdraw_manage?time_type&status',
            templateUrl: 'pages/financial_center/owner/withdraw_manage.html',
            css: 'pages/financial_center/owner/css/withdraw_manage.css',
            controller: 'withdraw_manage_ctrl'
        })
        .state('withdraw_detail', {//业主提现详情
            url: '/withdraw_detail?transaction_no',
            templateUrl: 'pages/financial_center/owner/withdraw_manage_detail.html',
            css: 'pages/financial_center/owner/css/account_detail.css',
            controller: 'withdraw_detail_ctrl'
        })
        .state('finance_account', {//财务账户管理
            url: '/finance_account',
            templateUrl: 'pages/financial_center/owner/account.html',
            css: 'pages/financial_center/owner/css/account.css',
            controller: 'account_ctrl'
        })
        .state('account_detail', {//财务账户管理详情
            url: '/account_detail?id',
            templateUrl: 'pages/financial_center/owner/account_detail.html',
            css: 'pages/financial_center/owner/css/account_detail.css',
            controller: 'account_detail_ctrl'
        })
        .state('freeze_money', {//冻结金额
            url: '/freeze_money?user_id',
            templateUrl: 'pages/financial_center/owner/freeze_money.html',
            css: 'pages/financial_center/owner/css/freeze_money.css',
            controller: 'freeze_money_ctrl'
        })
        .state('freeze_list', {//冻结金额
            url: '/freeze_list?user_id',
            templateUrl: 'pages/financial_center/owner/freeze_list.html',
            css: 'pages/financial_center/owner/css/withdraw_list.css',
            controller: 'freeze_list_ctrl'
        })
        .state('money_list', {//收支明细
            url: '/money_list?user_id',
            templateUrl: 'pages/financial_center/owner/money_list.html',
            css: 'pages/financial_center/owner/css/account.css',
            controller: 'money_list_ctrl'
        })
        /*=============== 廖欢 start ===============*/
        .state('home', {  // 首页
            url: '/home',
            templateUrl: 'pages/home/home.html',
            css: 'pages/home/css/home.css'
        })
        .state('order', { // 订单管理
            abstract: true,
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
        .state('order.after_sales', {   // 售后订单
            url: '/after-sales',
            templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/after_sales.html',
            css: 'pages/mall_manage/merchant_manage/order_manage/css/order.css',
            controller: 'after_sales'
        })
        .state('sales_details', {   // 售后详情
            url: '/sales-details?orderNo&sku&type',
            templateUrl: 'pages/mall_manage/merchant_manage/order_manage/order/sales_details.html',
            css: 'pages/mall_manage/merchant_manage/order_manage/css/order_details.css',
            controller: 'sales_details'
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
            url: '/order/express?orderNo&sku&waybillnumber&type',
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
        .state('offline_shop', {    // 线下体验店
            abstract: true,
            url: '/offline-shop',
            templateUrl: 'pages/mall_manage/offline_shop/offline_shop.html',
            controller: 'offline_shop'

        })
        .state('offline_shop.shop', {   // 体验店
            url: '/offline-shop/experience-shop',
            templateUrl: 'pages/mall_manage/offline_shop/experience_shop.html',
            controller: 'experience_shop'
        })
        .state('add_experience_shop', { // 添加线下店
            url: '/offline-shop/add-shop',
            templateUrl: 'pages/mall_manage/offline_shop/add_experience_shop.html',
            css: 'pages/mall_manage/offline_shop/css/experience.css',
            controller: 'add_experience_shop'
        })
        .state('edit_experience_shop', { // 编辑线下店
            url: '/offline-shop/edit-shop?shopNo',
            templateUrl: 'pages/mall_manage/offline_shop/edit_experience_shop.html',
            css: 'pages/mall_manage/offline_shop/css/experience.css',
            controller: 'edit_experience_shop'
        })
        .state('offline_shop.goods', {  // 体验商品
            url: '/offline-shop/experience-goods',
            templateUrl: 'pages/mall_manage/offline_shop/experience_goods.html',
            controller: 'experience_goods'
        })
        .state('add_experience_goods', { // 添加线下商品
            url: '/offline-shop/add-goods',
            templateUrl: 'pages/mall_manage/offline_shop/add_experience_goods.html',
            css: 'pages/mall_manage/offline_shop/css/experience.css',
            controller: 'add_experience_goods'
        })
        .state('edit_experience_goods', { // 编辑线下商品
            url: '/offline-shop/edit-goods?id&sku',
            templateUrl: 'pages/mall_manage/offline_shop/edit_experience_goods.html',
            css: 'pages/mall_manage/offline_shop/css/experience.css',
            controller: 'edit_experience_goods'
        })
    /*=============== 廖欢 end ===============*/
})
// .run(function ($rootScope,$state,$stateParams) {
//     $rootScope.$state = $state;
//     $rootScope.$stateParams = $stateParams;
//     $rootScope.$on("$stateChangeSuccess",  function(event, toState, toParams, fromState, fromParams) {
//         $rootScope.previousState_name = fromState.name;
//         $rootScope.previousState_params = fromParams;
//     });
//     $rootScope.back = function() {//实现返回的函数
//         $state.go($rootScope.previousState_name,$rootScope.previousState_params);
//     };
// })
    .directive('wdatePicker', function () {
        return {
            restrict: "A",
            link: function (scope, element, attr) {
                element.bind('click', function () {
                    window.WdatePicker({
                        onpicked: function () {
                            element.change()
                        },
                        oncleared: function () {
                            element.change()
                        }
                    })
                });
            }
        }
    })
    .run(["$rootScope", "$state", function ($rootScope, $state) {
        $rootScope.$on("$stateChangeSuccess", function (event, toState, toParams, fromState, fromParams) {
            document.body.scrollTop = document.documentElement.scrollTop = 0
            $rootScope.fromState_name = fromState.name
            $rootScope.curState_name = toState.name
        })
        $rootScope.goPrev = function () {
            $state.go($rootScope.fromState_name)
        }
    }]);