let index_recommend_history = angular.module("index_recommend_history_module", []);
index_recommend_history.controller("index_recommend_history_ctrl", function ($scope, $http) {
  $scope.myng=$scope;
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  $scope.recommendList=[];

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
    }, function (error) {
      console.log(error)
    })
  }, function (error) {
    console.log(error)
  });
  //总页数 数组
  $scope.$watch('selectValue',function(newVal,oldVal){
    if(!!newVal){
      $http.get('http://test.cdlhzz.cn:888/mall/recommend-history', {
        params:{
          'district_code': 510100,
          'time_type': newVal.value,
          'type': 2,
        }
      }).then(function (response) {
        console.log("推荐历史");
        console.log(response);
        $scope.recommendList = response.data.data.recommend_history.details;

        /*-----------------------------分页-----------------------*/
        $scope.history_list=[];
        $scope.history_all_page=Math.ceil(response.data.data.recommend_history.total/12);//获取总页数
        let all_num=$scope.history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.history_list.push(i+1)
        }
        console.log($scope.history_list)

        //点击数字，跳转到多少页
        $scope.choosePage=function (page) {
          $scope.page=page;
          $http.get('http://test.cdlhzz.cn:888/mall/recommend-history',{
            params:{
              'district_code': 510100,
              'time_type': newVal.value,
              'type': 2,
              'page':$scope.page,
              'start_time':$scope.begin_time,
              'end_time':$scope.end_time
            }
          }).then(function (res) {
            $scope.recommendList = res.data.data.recommend_history.details;
          },function (err) {
            console.log(err);
          });
        };
        //显示当前是第几页的样式
        $scope.isActivePage=function (page) {
          return $scope.page==page;
        };
        //进入页面，默认设置为第一页
        if($scope.page===undefined){
          $scope.page=1;
        }
        //上一页
        $scope.Previous=function () {
          if($scope.page>1){                //当页数大于1时，执行
            $scope.page--;
            $scope.choosePage($scope.page);
          }
        };
        //下一页
        $scope.Next=function () {
          if($scope.page<$scope.history_all_page){ //判断是否为最后一页，如果不是，页数+1,
            $scope.page++;
            $scope.choosePage($scope.page);
          }
        }
      },function (error) {
        console.log(error)
      })
    }
  });

  //监听开始时间
  $scope.$watch('begin_time',function (newVal,oldVal) {
    $scope.page=1;//默认第一页
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
        $scope.history_list=[];
        $scope.history_all_page=Math.ceil(response.data.data.recommend_history.total/12);//获取总页数
        let all_num=$scope.history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.history_list.push(i+1)
        }
      },function (err) {
        console.log(err)
      });
    }
  });
  //监听结束时间
  $scope.$watch('end_time',function (newVal,oldVal) {
    $scope.page=1;//默认第一页
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
        $scope.history_list=[];
        $scope.history_all_page=Math.ceil(response.data.data.recommend_history.total/12);//获取总页数
        let all_num=$scope.history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.history_list.push(i+1)
        }
      },function (err) {
        console.log(err)
      })
    }
  });
});


