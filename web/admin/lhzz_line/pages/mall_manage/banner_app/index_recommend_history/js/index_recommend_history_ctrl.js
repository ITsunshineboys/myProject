let index_recommend_history = angular.module("index_recommend_history_module", []);
index_recommend_history.controller("index_recommend_history_ctrl", function ($rootScope,$scope,$http,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: 'APP推荐位-推荐管理',
        link: 'index_recommend',
    }, {
        name: '历史数据统计'
    }];
    $scope.myng=$scope;
    $scope.recommendList=[];
    /*分页配置*/
    $scope.Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    }
    $scope.params = {
        page: 1,                        // 当前页数
        district_code: '510100',               // 时间类型
        type:2,
        time_type: 'all',                    // 关键字查询
        start_time: '',                 // 自定义开始时间
        end_time: ''                   // 自定义结束时间
    };
    let tablePages=function () {
        $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/mall/recommend-history',$scope.params,function (res) {
            $scope.recommendList = res.data.recommend_history.details
            $scope.Config.totalItems = res.data.recommend_history.total;
        })
    };

    _ajax.get('/site/time-types',{},function (res) {
        $scope.time = res.data.time_types;
        $scope.selectValue = res.data.time_types[0].value;
    })
    //监听时间类型
    $scope.type_change=function (value) {
        if(value=='custom'){
            $scope.params.start_time='';
            $scope.params.end_time='';
        }
        $scope.Config.currentPage = 1; //页数跳转到第一页
        tablePages();
    }
    //监听开始和结束时间
    $scope.time_change=function () {
        $scope.Config.currentPage = 1; //页数跳转到第一页
        tablePages();
    }
  $scope.shop_details=function (item) {
    $scope.shop_datails=item;
    console.log($scope.shop_datails);
    if($scope.shop_datails.from_type=='商家'){
      $scope.shop_details_title=$scope.shop_datails.title; //标题
      $scope.shop_details_subtitle=$scope.shop_datails.description;//副标题
      $scope.shop_details_types=$scope.shop_datails.from_type; //类型
      $scope.shop_details_sku=$scope.shop_datails.sku;  //编号
      $scope.shop_details_time=$scope.shop_datails.create_time; //创建时间
      $scope.shop_details_status=$scope.shop_datails.status;  //是否启用
      $scope.shop_details_platform_price=$scope.shop_datails.platform_price;//平台价格
      $scope.shop_details_supplier_name=$scope.shop_datails.supplier_name;//来源商家
      $scope.shop_details_supplier_price=$scope.shop_datails.supplier_price; //供货价格
      $scope.shop_details_market_price=$scope.shop_datails.market_price;  //市场价格
      $scope.shop_details_img=$scope.shop_datails.image;//图片
      $scope.shop_details_viewed_number=$scope.shop_datails.viewed_number;//上架浏览
      $scope.shop_details_viewed_left=$scope.shop_datails.left_number;//库存
      $scope.shop_details_sold_number=$scope.shop_datails.sold_number;//上架销量
    }
    //链接
    if($scope.shop_datails.from_type=='链接'){
      $scope.link_details_title=$scope.shop_datails.title;  //标题
      $scope.link_details_subtitle=$scope.shop_datails.description;//副标题
      $scope.link_details_from_type=$scope.shop_datails.from_type; //类型
      $scope.link_details_show_price=$scope.shop_datails.show_price;//显示价格
      $scope.link_details_supplier_name=$scope.shop_datails.supplier_name;//来源商家
      $scope.link_details_create_time=$scope.shop_datails.create_time;//创建时间
      $scope.link_details_status=$scope.shop_datails.status;//是否启用
      $scope.link_details_viewed_number=$scope.shop_datails.viewed_number;//浏览
      $scope.link_details_img=$scope.shop_datails.image;//图片
    }
  };
});


