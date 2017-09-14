;
let add_series = angular.module("addseriesModule",[]);
add_series.controller("add_series",function ($scope,$http,$stateParams,$state) {
	//$scope.myng=$scope; //解决ng-model 原型继承问题
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  $scope.list=$stateParams.list;//获取系列数据列表
  //所处等级
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
  $scope.add_ser_select=$scope.list_ser[0].id;

  /*-----------------系列标签 开始-------------------*/
  //标签列表
  $scope.i=1;
  $scope.ser_label_arr=[{num:'',label_name:'label_name'+1}];
 //增加标签
  $scope.label_add=function () {
    $scope.i++;
    $scope.ser_label_arr.push({num:'',label_name:'label_name'+$scope.i});
  };
  //删除标签
  $scope.close_label=function (index) {
    $scope.ser_label_arr.splice(index,1);
  };
  /*-----------------系列标签 结束-------------------*/

  //点击添加按钮
  $scope.tran_arr=[];
  $scope.ser_add_btn=function (valid) {
    if(valid && !$scope.name_flag){
      console.log('ok');
      for(let[key,value] of $scope.ser_label_arr.entries()){
        if(value.num!=''){
          $scope.tran_arr.push(value.num);//标签组
        }
      }
      $scope.sur_id='suremodal';
      console.log($scope.tran_arr);
      console.log($scope.add_ser_select)
    }else{
      console.log('error');
      $scope.submitted=true;
    }
  };
	//添加确认按钮
	$scope.add_ok=function () {
    let url='http://test.cdlhzz.cn:888/mall/series-add';
    $http.post(url,{
      series:$scope.series_name, //名称
      theme:$scope.tran_arr.join(','), //标签
      intro:$scope.ser_intro,//介绍
      series_grade:$scope.add_ser_select//等级
    },config).then(function (res) {
      console.log("添加成功");
      console.log(res);
    },function (err) {
      console.log(err);
    });
    setTimeout(function () {
      $state.go("style_index");
    },300);
    };
});