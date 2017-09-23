angular.module('shop_data_module',[])
.controller('shop_data_ctrl',function ($scope,$http) {
  $scope.myng=$scope;
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  $scope.data_list=[];
  //时间类型
  $http.get('http://test.cdlhzz.cn:888/site/time-types').then(function (response) {
    $scope.time = response.data.data.time_types;
    $scope.selectValue = response.data.data.time_types[0].value;
  });
  //列表数据
  $scope.$watch('selectValue',function (newVal,oldVal) {
    if(!!newVal){
      $http.get('http://test.cdlhzz.cn:888/mall/shop-data',{
        params:{
          time_type:newVal
        }
      }).then(function (res) {
        console.log(res);
        $scope.data_list=res.data.data.shop_data.details;
        $scope.total_sold_number=res.data.data.shop_data.total_sold_number;//销量
        $scope.total_amount_sold=res.data.data.shop_data.total_amount_sold;//销量额
        $scope.total_ip_number=res.data.data.shop_data.total_ip_number;//游客数
        $scope.total_viewed_number=res.data.data.shop_data.total_viewed_number;//访问量
      },function (err) {
        console.log(err);
      })
    }
  });
  //监听开始时间
  $scope.$watch('begin_time',function (newVal,oldVal) {
    //$scope.page=1;//默认第一页
    console.log(newVal);
    if(newVal!=undefined && newVal!='' && $scope.begin_time!=undefined && $scope.end_time!=undefined){
      let url='http://test.cdlhzz.cn:888/mall/shop-data';
      $http.get(url,{
        params:{
          'time_type':'custom',
          'start_time':newVal,
          'end_time': $scope.end_time
        }
      }).then(function (res) {
        $scope.data_list=res.data.data.shop_data.details;
        $scope.total_sold_number=res.data.data.shop_data.total_sold_number;//销量
        $scope.total_amount_sold=res.data.data.shop_data.total_amount_sold;//销量额
        $scope.total_ip_number=res.data.data.shop_data.total_ip_number;//游客数
        $scope.total_viewed_number=res.data.data.shop_data.total_viewed_number;//访问量
      },function (err) {
        console.log(err)
      })
    }
  });
  //监听结束时间
  $scope.$watch('end_time',function (newVal,oldVal) {
    //$scope.page=1;//默认第一页
    console.log(newVal);
    if(newVal!=undefined && newVal!='' && $scope.begin_time!=undefined && $scope.end_time!=undefined){
      let url='http://test.cdlhzz.cn:888/mall/shop-data';
      $http.get(url,{
        params:{
          'time_type':'custom',
          'start_time':$scope.begin_time,
          'end_time':newVal
        }
      }).then(function (res) {
        $scope.data_list=res.data.data.shop_data.details;
        $scope.total_sold_number=res.data.data.shop_data.total_sold_number;//销量
        $scope.total_amount_sold=res.data.data.shop_data.total_amount_sold;//销量额
        $scope.total_ip_number=res.data.data.shop_data.total_ip_number;//游客数
        $scope.total_viewed_number=res.data.data.shop_data.total_viewed_number;//访问量
      },function (err) {
        console.log(err)
      })
    }
  });
});