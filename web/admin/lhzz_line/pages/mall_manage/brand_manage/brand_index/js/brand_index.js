;
let brand_index = angular.module("brand_index_module",[]);
brand_index.controller("brand_index_ctrl",function ($scope,$http,$stateParams) {

  //页面初始化
  $scope.on_flag=true;
  $scope.down_flag=false;
  $scope.check_flag=false;
  //点击TAB 切换内容
  $scope.on_shelves=function () {
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.check_flag=false;
  };
  //已下架
  $scope.down_shelves=function () {
    $scope.down_flag=true;
    $scope.on_flag=false;
    $scope.check_flag=false;
  };
  //品牌审核
  $scope.wait_shelves=function () {
    $scope.check_flag=true;
    $scope.on_flag=false;
    $scope.down_flag=false;
  };
});