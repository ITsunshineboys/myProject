;
let brand_check= angular.module("brand_check_module",[]);
brand_check.controller("brand_check_ctrl",function ($scope,$http,$stateParams,$state) {
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };

  $scope.myng=$scope;
  $scope.item=$stateParams.item;
  console.log($scope.item);
  $scope.check_arr=[{value:2,name:'通过'},{value:1,name:'不通过'}];
  $scope.check_select=$scope.check_arr[0].value;//默认第一项是通过
  //监视通过、不通过的下拉框
  $scope.$watch('check_select',function (newVal,oldVal) {
      console.log(newVal)
    });
    //审核确认按钮
  $scope.review_btn=function () {
    $http.post(baseUrl+'/mall/brand-application-review',{
        id:+$scope.item.id,
        review_status:$scope.check_select,
        review_note:$scope.review_txt
    },config).then(function (res) {
      console.log(res);
      $state.go('brand_index',{check_flag:true})
    },function (err) {
      console.log(err);
    })
  }
});