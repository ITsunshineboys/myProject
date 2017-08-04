var index_recommend_history = angular.module("index_recommend_history_module", []);
index_recommend_history.controller("index_recommend_history_ctrl", function ($scope, $http) {
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
      $scope.recommendList = response.data.data.recommend_history.details
      // console.log(response)
    }, function (error) {
      console.log(error)
    })
    // console.log(response)
  }, function (error) {
    console.log(error)
  });
  $scope.$watch('selectValue',function(newVal,oldVal){
    // console.log(newVal)
    if(!!newVal){
      $http.get('http://test.cdlhzz.cn:888/mall/recommend-history', {
        params: {
          'district_code': 510100,
          'time_type': newVal.value,
          'type': 2
        }
      }).then(function (response) {
        $scope.recommendList = response.data.data.recommend_history.details
        // console.log(response)
      }, function (error) {
        console.log(error)
      })
    }
  })
  // console.log($scope.selectValue)
  // $http.get('http://test.cdlhzz.cn:888/mall/recommend-history?district_code=510100&time_type'+$scope.selectValue.value
  //   +'&type=2').then(function (response) {
  //   console.log(response)
  // },function (error) {
  //   console.log(error)
  // })
//  搜素按钮
  $scope.start_time=new Date();
  $scope.stop_time=new Date();
  //搜索按钮
  $scope.history_search=function () {
    console.log($scope.start_time);
    console.log($scope.stop_time);
  };


});


