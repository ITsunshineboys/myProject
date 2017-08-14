let add_series = angular.module("addseriesModule",[]);
add_series.controller("add_series",function ($scope,$http,$stateParams) {
	$scope.myng=$scope; //解决ng-model 原型继承问题
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };

  //所处等级
  $scope.list=$stateParams.list;//获取系列数据列表
  $scope.list_ser=[    //初始化等级列表
    {id:1,num:'1'},
    {id:2,num:'2'},
    {id:3,num:'3'},
    {id:4,num:'4'},
    {id:5,num:'5'},
    {id:6,num:'6'},
    {id:7,num:'7'},
    {id:8,num:'8'},
    {id:9,num:'9'},
    {id:10,num:'10'}
    ];
  //判断系列名称是否存在
  $scope.name_flag=false;
  $scope.$watch('series_name',function(newVal,oldVal){
    for(let [key,value] of $scope.list.entries()){
      if(newVal===value.series){
        $scope.name_flag=true;
        return;
      }else {
        $scope.name_flag=false;
      }
    }
  });
  //判断等级
  let arr = $scope.list_ser.concat();
  for(let [key1,ser] of arr.entries()){
    for(let [key2,list] of $scope.list.entries()){
      if(ser.num===list.series_grade){
        $scope.list_ser.splice($scope.list_ser.indexOf(ser),1)
      }
    }
  }
  $scope.select_model=$scope.list_ser[0].num;

  //添加input框
  $scope.label_num=1;//初始化input框的个数
  $scope.add_num=function () {
    $scope.label_num=$scope.label_num+1;
    console.log($scope.label_num);
  };
	//添加确认按钮
	$scope.add_ok=function () {
  console.log($scope.select_model);
  console.log($scope.series_label2)
    let url='http://test.cdlhzz.cn:888/mall/series-add';
    $http.post(url,{
      series:$scope.series_name, //名称
      theme:$scope.series_label1+','+$scope.series_label2, //标签
      intro:$scope.series_introduction,//介绍
      series_grade:$scope.select_model//等级
    },config).then(function (res) {
      console.log("确认按钮");
      console.log(res);
    },function (err) {
      console.log(err);
    })
    };
});