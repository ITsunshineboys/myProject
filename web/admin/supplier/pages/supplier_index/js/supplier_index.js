;
let supplier_index = angular.module("supplier_index",[])
  .controller("supplier_index_ctrl",function ($scope,$http,$state,$stateParams,$filter) {
        $http.get('http://test.cdlhzz.cn:888/mall/supplier-index-admin').then(function (res) {
          console.log('首页返回');
          console.log(res);
          $scope.today_amount_order=res.data.data.supplier_index_admin.today_amount_order;//今日订单金额
          $scope.today_order_number=res.data.data.supplier_index_admin.today_order_number;//今日订单数
          $scope.total_ip_number=res.data.data.supplier_index_admin.today_ip_number;//今日游客数
          $scope.total_viewed_number=res.data.data.supplier_index_admin.today_viewed_number;//今日访问量
        },function (err) {
          console.log(err);
        });
    $scope.dt1 = new Date();
    $scope.dt2 = $filter("date")($scope.dt1, "yyyy-MM-dd");
  });