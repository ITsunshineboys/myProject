angular.module('shop_data_module',[])
.controller('shop_data_ctrl',function ($rootScope,$scope,$http,_ajax) {
    $rootScope.crumbs = [{
        name: '店铺数据',
        icon: 'icon-shangchengguanli',
    }];

    $scope.myng=$scope;
  $scope.data_list=[];
    /*分页配置*/
    $scope.wjConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    };
    $scope.params = {
        page: 1,                        // 当前页数
        time_type: 'all',               // 时间类型
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
    };
    let tablePages=function () {
        $scope.params.page=$scope.wjConfig.currentPage;//点击页数，传对应的参数
        _ajax.get('/mall/shop-data',$scope.params,function (res) {
            $scope.data_list=res.data.shop_data.details;
            $scope.wjConfig.totalItems = res.data.shop_data.total;
            $scope.total_sold_number=res.data.shop_data.total_sold_number;//销量
            $scope.total_amount_sold=res.data.shop_data.total_amount_sold;//销量额
            $scope.total_ip_number=res.data.shop_data.total_ip_number;//游客数
            $scope.total_viewed_number=res.data.shop_data.total_viewed_number;//访问量
        });
    };
  //时间类型
  _ajax.get('/site/time-types',{},function (res) {
      $scope.time = res.data.time_types;
      $scope.params.time_type = res.data.time_types[0].value;
  });
  //监听开始和结束时间
    $scope.time_change=function () {
        $scope.wjConfig.currentPage = 1; //页数跳转到第一页
        tablePages();
    };

});