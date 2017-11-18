;
angular.module('supplier_wallet_module',[])
.controller('supplier_wallet_ctrl',function ($rootScope,$scope,$http,$state,_ajax) {
    $rootScope.crumbs = [{
        name: '钱包',
        icon: 'icon-qianbao'
    }];

    $scope.myng=$scope;
    /*分页配置*/
    $scope.wjConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    }
    $scope.params = {
        page: 1,                        // 当前页数
        time_type: 'all',               // 时间类型
        time_start: '',                 // 自定义开始时间
        time_end: '',                   // 自定义结束时间
        status: '0',                      // 类型选择
    };
    let tablePages=function () {
        $scope.params.page=$scope.wjConfig.currentPage;//点击页数，传对应的参数
        _ajax.get('/supplier-cash/get-cash-list',$scope.params,function (res) {
            console.log(res);
            $scope.supplier_wallet_list=res.data.list;
            $scope.wjConfig.totalItems = res.data.count;
        })
    };


  $scope.supplier_wallet_list=[];
  //状态
  $scope.status_arr=[{id:0,value:'全部'},{id:1,value:'提现中'},{id:2,value:'已提现'},{id:3,value:'驳回'}];
  $scope.params.status=$scope.status_arr[0].id;
  //时间类型
  _ajax.get('/site/time-types',{},function (res) {
      console.log(res);
      $scope.time = res.data.time_types;
      $scope.params.time_type = res.data.time_types[0].value;
  })
  //监听时间
    $scope.time_status_change=function () {
        $scope.wjConfig.currentPage=1;
        $scope.params.time_start='';
        $scope.params.time_end='';
        tablePages();
    }
  //监听开始和结束时间
    $scope.time_change=function () {
        $scope.wjConfig.currentPage=1;
        tablePages();
    };
});