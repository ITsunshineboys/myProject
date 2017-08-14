
let style_index = angular.module("styleindexModule",[]);
style_index.controller("style_index",function ($scope,$http) {
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
	//系列——展示数据数组
	$scope.series_arr=[];

	//初始化
	$scope.showseries = true;
	$scope.showstyle = false;
	$scope.showattr = false;

	/*选项卡切换方法*/
	$scope.changeToseries = function () {
		$scope.showseries = true;
		$scope.showstyle = false;
		$scope.showattr = false;
	};

	$scope.changeTostyle = function () {
		$scope.showseries = false;
		$scope.showstyle = true;
		$scope.showattr = false;
	};

	$scope.changeToattr = function () {
		$scope.showseries = false;
		$scope.showstyle = false;
		$scope.showattr = true;
	};
//	系列——展示数据
  $http.get('http://test.cdlhzz.cn:888/mall/series-list').then(function (res) {
    $scope.series_arr.push(res.data.data.series_list);
    console.log(res);
  },function (err) {
    console.log(err);
  });
  //开启操作
  $scope.open_status=function (item) {
		$scope.open_item=item;
  };
	//开启确认按钮
	$scope.open_btn_ok=function () {
    let url='http://test.cdlhzz.cn:888/mall/series-status';
    $http.post(url,{
      id:+$scope.open_item.id,
      status:1
    },config).then(function (res) {
      console.log(res);
      window.location.reload();
      $http.get('http://test.cdlhzz.cn:888/mall/series-list').then(function (res) {
        $scope.series_arr.push(res.data.data.series_list);
      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log(err);
    })
  };

  //关闭操作
  $scope.close_status=function (item) {
    $scope.close_item=item;
  };
  //关闭确认按钮
	$scope.close_btn_ok=function () {
    let url='http://test.cdlhzz.cn:888/mall/series-status';
    $http.post(url,{
      id:+$scope.close_item.id,
      status:0
    },config).then(function (res) {
      console.log(res);
      $http.get('http://test.cdlhzz.cn:888/mall/series-list').then(function (res) {
        $scope.series_arr.push(res.data.data.series_list);
        window.location.reload();
      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log(err);
    })
  };

});