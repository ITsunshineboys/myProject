angular.module('income_pay_module',[])
.controller('income_pay_ctrl',function ($scope,$http,$state) {
  $scope.myng=$scope;
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  //状态
  $scope.status_arr=[
    {id:0,value:'全部'},
    {id:1,value:'贷款'},
    {id:2,value:'驳回'},
    {id:3,value:'提现中'},
    {id:4,value:'已提现'},
    {id:5,value:'充值'},
    {id:6,value:'扣款'}
    ];
  $scope.selectStatus=$scope.status_arr[0].id;
  //时间类型
  $http.get('http://test.cdlhzz.cn:888/site/time-types').then(function (response) {
    $scope.time = response.data.data.time_types;
    $scope.selectValue = response.data.data.time_types[0];
  });
});