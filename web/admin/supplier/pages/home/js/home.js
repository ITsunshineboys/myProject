;
let supplier_index = angular.module("supplier_index",[])
  .controller("supplier_index_ctrl",function ($rootScope,$scope,$http,$filter,_ajax) {
        _ajax.get('/mall/supplier-index-admin',{},function (res) {
            $scope.today_amount_order=res.data.supplier_index_admin.today_amount_order;//今日订单金额
            $scope.today_order_number=res.data.supplier_index_admin.today_order_number;//今日订单数
            $scope.total_ip_number=res.data.supplier_index_admin.today_ip_number;//今日游客数
            $scope.total_viewed_number=res.data.supplier_index_admin.today_viewed_number;//今日访问量
        })
    $scope.dt1 = new Date();
    $scope.dt2 = $filter("date")($scope.dt1, "yyyy-MM-dd");
      $rootScope.crumbs = [{
          name: '首页',
          icon: 'icon-shangchengguanli',
      }];

  });