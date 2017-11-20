angular.module('wallet_detail_module',[])
.controller('wallet_detail_ctrl',function ($rootScope,$scope,$http,$state,$stateParams,_ajax) {
  console.log($stateParams.transaction_no);//交易单号
    $rootScope.crumbs = [{
        name: '钱包',
        icon: 'icon-qianbao',
        link: 'supplier_wallet'
    }, {
        name: '钱包详情',
    }];
  $scope.wallet_detail=[];
  _ajax.get('/supplier-cash/get-cash',{transaction_no:$stateParams.transaction_no},function (res) {
      console.log(res);
      $scope.wallet_detail=res.data;
  })
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