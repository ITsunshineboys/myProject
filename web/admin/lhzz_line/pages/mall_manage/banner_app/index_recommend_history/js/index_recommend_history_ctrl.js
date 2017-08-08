var index_recommend_history = angular.module("index_recommend_history_module", []);
index_recommend_history.controller("index_recommend_history_ctrl", function ($scope, $http) {
  $scope.myng=$scope;

  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  // $scope.selectValue = '全部时间'
  $http.get('http://test.cdlhzz.cn:888/site/time-types').then(function (response) {
    $scope.time = response.data.data.time_types;
    $scope.selectValue = response.data.data.time_types[0];
    // console.log($scope.selectValue.value)
    $http.get('http://test.cdlhzz.cn:888/mall/recommend-history', {
      params: {
        'district_code': 510100,
        'time_type': $scope.selectValue.value,
        'type': 2
      }
    }).then(function (response) {
      // console.log("推荐历史");
      // console.log(response);
      $scope.recommendList = response.data.data.recommend_history.details
    }, function (error) {
      console.log(error)
    })
  }, function (error) {
    console.log(error)
  });
  $scope.$watch('selectValue',function(newVal,oldVal){
    console.log(newVal);
    if(!!newVal){
      $http.get('http://test.cdlhzz.cn:888/mall/recommend-history', {
        params: {
          'district_code': 510100,
          'time_type': newVal.value,
          'type': 2
        }
      }).then(function (response) {
        $scope.recommendList = response.data.data.recommend_history.details;
      }, function (error) {
        console.log(error)
      })
    }
  });
  //监听开始时间
  $scope.$watch('begin_time',function (newVal,oldVal) {
    if(newVal!=undefined && newVal!='' && $scope.begin_time!=undefined && $scope.end_time!=undefined){
      let url='http://test.cdlhzz.cn:888/mall/recommend-history';
      $http.get(url,{
        params:{
          'district_code':510100,
          'time_type':'custom',
          'type':2,
          'start_time':newVal,
          'end_time': $scope.end_time
        }
      }).then(function (response) {
        console.log(response);
        $scope.recommendList = response.data.data.recommend_history.details;
      },function (err) {
        console.log(err)
      })
    }
  });
  //监听结束时间
  $scope.$watch('end_time',function (newVal,oldVal) {
    if(newVal!=undefined && newVal!='' && $scope.begin_time!=undefined && $scope.end_time!=undefined){
      let url='http://test.cdlhzz.cn:888/mall/recommend-history';
      $http.get(url,{
        params:{
          'district_code':510100,
          'time_type':'custom',
          'type':2,
          'start_time':$scope.begin_time,
          'end_time': newVal
        }
      }).then(function (response) {
        console.log(response);
        $scope.recommendList = response.data.data.recommend_history.details;
      },function (err) {
        console.log(err)
      })
    }
  });
});


