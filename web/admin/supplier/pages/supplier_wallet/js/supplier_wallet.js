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
  $scope.supplier_wallet_list=[];
  //状态
  $scope.status_arr=[{id:0,value:'全部'},{id:1,value:'提现中'},{id:2,value:'已提现'},{id:3,value:'驳回'}];
  $scope.selectStatus=$scope.status_arr[0].id;
  //时间类型
  $http.get('http://test.cdlhzz.cn:888/site/time-types').then(function (response) {
    $scope.time = response.data.data.time_types;
    $scope.selectValue = response.data.data.time_types[0];
  });
  //列表数据
  $scope.$watch('selectValue',function (newVal,oldVal) {
    if(!!newVal){
      $http.post('http://test.cdlhzz.cn:888/supplier-cash/get-cash-list',{
          time_type:newVal.value,
          status:+$scope.selectStatus
        }
      ,config).then(function (res) {
        console.log(res);
        $scope.supplier_wallet_list=res.data.data.list;
      },function (err) {
        console.log(err);
      })
    }
  });
  //监听开始时间
  $scope.$watch('begin_time',function (newVal,oldVal) {
    //$scope.page=1;//默认第一页
    if(newVal!=undefined && newVal!='' && $scope.begin_time!=undefined && $scope.end_time!=undefined){
      let url='http://test.cdlhzz.cn:888/supplier-cash/get-cash-list';
      $http.post(url,{
          time_type:'custom',
          time_start:newVal,
          time_end: $scope.end_time,
          status:+$scope.selectStatus

      },config).then(function (res) {
        console.log(res);
        $scope.supplier_wallet_list=res.data.data.list;
      },function (err) {
        console.log(err)
      })
    }
  });
  //监听结束时间
  $scope.$watch('end_time',function (newVal,oldVal) {
    //$scope.page=1;//默认第一页
    if(newVal!=undefined && newVal!='' && $scope.begin_time!=undefined && $scope.end_time!=undefined){
      let url='http://test.cdlhzz.cn:888/supplier-cash/get-cash-list';
      $http.post(url,{
          time_type:'custom',
          time_start:$scope.begin_time,
          time_end:newVal,
          status:+$scope.selectStatus
      },config).then(function (res) {
        console.log(res);
        $scope.supplier_wallet_list=res.data.data.list;
      },function (err) {
        console.log(err)
      })
    }
  });
 //监听类型
  $scope.$watch('selectStatus',function (newVal,oldVal) {
    if(newVal!=undefined &&!!$scope.selectValue){
      $http.post('http://test.cdlhzz.cn:888/supplier-cash/get-cash-list',{
        time_type:$scope.selectValue.value,
        status:+newVal,
        time_start:$scope.begin_time,
        time_end: $scope.end_time
      },config).then(function (res) {
        console.log(res);
        $scope.supplier_wallet_list=res.data.data.list;
      },function (err) {
        console.log(err);
      })
    }
  })
});