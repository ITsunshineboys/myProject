
;
let brand_details= angular.module("brand_details_module",[]);
brand_details.controller("brand_details_ctrl",function ($scope,$http,$stateParams) {
  $scope.item=$stateParams.item;//点击传入的数据
  console.log($scope.item)
});