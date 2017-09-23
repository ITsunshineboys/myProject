let index = angular.module("index_module",[]);
index.controller("index_ctrl",function ($scope,$http) {
  $scope.show_class=1;
  $scope.dl_click=function (num) {
    $scope.show_class=num;
  };
});