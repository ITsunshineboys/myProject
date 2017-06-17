//获取本地json 数据
  var myApp = angular.module("myApp",[]);
  myApp.controller("comment_controller",function($scope, $http){
    $http({
        method: 'get',
        url: 'commodity.json'
    }).then(function successCallback(response) {
        $scope.message = response.data.data.category_goods;

    }, function errorCallback(response) {
        // 请求失败执行代码
        alert(response);

    });
  });
//当点击价格是按价格高低排序
myApp.controller("salesPriority",function ($scope,$http) {
  $scope.sales_priority=function () {
   this.$(".memo_pad li").on("click",function () {
      $http({
        method: 'get',
        url: 'commodity.json'
      }).then( function () {
        if (this.$(".memo_pad li")==0) { //判断点击li的下标是0时，就按销量排序
          
        }
        if (this.$(".memo_pad li")==1) { //判断点击li的下标是0时，就按价格排序


        }
        if (this.$(".memo_pad li")==2) { //判断点击li的下标是0时，就按好评率排序

        }
      },function () {

      })
    })
  }
});

