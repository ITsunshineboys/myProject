angular.module('wallet_detail_module',[])
.controller('wallet_detail_ctrl',function ($scope,$http,$state,$stateParams) {
  console.log($stateParams.transaction_no);//交易单号
  $scope.wallet_detail=[];
  $http.get('http://test.cdlhzz.cn:888/supplier-cash/get-cash',{
    params:{
        transaction_no:$stateParams.transaction_no
    }
  }).then(function (res) {
    console.log(res);
    $scope.wallet_detail=res.data.data;
    console.log($scope.wallet_detail);
  },function (err) {
    console.log(err);
  });
  //判断返回哪个页面
    $scope.wallet_back=function () {
        if(!!$stateParams.income){      //----------> 收支详情
            console.log($stateParams.income)
                $state.go('income_pay')
        }else{                          //-----------> 钱包首页
            console.log($stateParams.income)
                $state.go('supplier_wallet')
        }
    };

});