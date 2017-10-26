;
angular.module('supplier_wallet_module',[])
.controller('supplier_wallet_ctrl',function ($scope,$http,$state) {
  $scope.myng=$scope;
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };

    /*分页配置*/
    $scope.wjConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    }
    let tablePages=function () {
        $scope.params.page=$scope.wjConfig.currentPage;//点击页数，传对应的参数
        $http.get(baseUrl+'/supplier-cash/get-cash-list',{
          params:$scope.params
        }).then(function (res) {
            console.log(res);
            $scope.supplier_wallet_list=res.data.data.list;
            $scope.wjConfig.totalItems = res.data.data.count;
        },function (err) {
            console.log(err);
        })
    };
    $scope.params = {
        page: 1,                        // 当前页数
        time_type: 'all',               // 时间类型
        time_start: '',                 // 自定义开始时间
        time_end: '',                   // 自定义结束时间
        status: '0',                      // 类型选择
    };

  $scope.supplier_wallet_list=[];
  //状态
  $scope.status_arr=[{id:0,value:'全部'},{id:1,value:'提现中'},{id:2,value:'已提现'},{id:3,value:'驳回'}];
  $scope.params.status=$scope.status_arr[0].id;
  //时间类型
  $http.get('http://test.cdlhzz.cn:888/site/time-types').then(function (response) {
    $scope.time = response.data.data.time_types;
    $scope.params.time_type = response.data.data.time_types[0].value;
  });
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
    }
});