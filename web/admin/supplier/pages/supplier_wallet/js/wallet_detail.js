angular.module('wallet_detail_module',[])
.controller('wallet_detail_ctrl',function ($scope,$http,$state,$stateParams) {
  console.log($stateParams.id);//提现单id
  $scope.wallet_detail=[];
  $http.get('http://test.cdlhzz.cn:888/supplier-cash/get-cash',{
    params:{
      cash_id:+$stateParams.id
    }
  }).then(function (res) {
    console.log(res);
    $scope.wallet_detail=res.data.data;
    console.log($scope.wallet_detail);

  },function (err) {
    console.log(err);
  });
});