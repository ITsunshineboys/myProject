;
let account_index=angular.module("account_index_module",[]);
account_index.controller("account_index_ctrl",function ($scope,$http) {
  $scope.normal_flag=true;
  $scope.close_flag=false;

  $scope.normal=function () {
    $scope.normal_flag=true;
    $scope.close_flag=false;
  };
  $scope.close=function () {
    $scope.close_flag=true;
    $scope.normal_flag=false;

  }
});