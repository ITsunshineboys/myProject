let series_detail = angular.module("seriesdetailModule",[]);
series_detail.controller("series_detail",function ($rootScope,$scope,$http,$state,$stateParams,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: 'merchant_index'
    }, {
        name: '系列/风格/属性管理',
        link: 'style_index',
    }, {
        name: '系列详情页'
    }];
  $scope.myng=$scope;
  $scope.items=$stateParams.item;  // 主页传参--对应一条的数组

  //设置初始值
  $scope.ser_name=$scope.items.series;//名称
  $scope.sec_time=$scope.items.creation_time;//时间
  $scope.ser_intro=$scope.items.intro; //介绍

  $scope.change_txts=function () {
    if($scope.ser_intro==undefined){
      $scope.ser_intro='';
    }
  };
    /*---------------判断等级 开始---------------*/
  //初始化等级列表
  $scope.list_ser=[
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

  //系列的所有数据
  $scope.ser_arr=[];
  _ajax.get('/mall/series-list',{},function (res) {
      console.log(res);
      $scope.ser_arr=res.data.series_list.details;
      //判断剩余的等级
      let arr = $scope.list_ser.concat();
      for(let [key1,ser] of arr.entries()){
          for(let [key2,list] of $scope.ser_arr.entries()){
              if(ser.num==list.series_grade){
                  $scope.list_ser.splice($scope.list_ser.indexOf(ser),1);
              }
          }
      }
      //把点击的那条数据的等级，前置在第一条
      $scope.list_ser.unshift({id:+$scope.items.series_grade,num:$scope.items.series_grade});
      $scope.series_grade=$scope.list_ser[0].id // 默认设置第一项
  })
  /*---------------判断等级 结束---------------*/

  /*-------------判断系列名称是否存在 开始-------------*/
  $scope.name_flag=false;
  $scope.$watch('ser_name',function(newVal,oldVal){
    for(let [key,value] of $scope.ser_arr.entries()){
      if(newVal===value.series && newVal!=$scope.items.series){
        $scope.name_flag=true;
        return;
      }else {
        $scope.name_flag=false;
      }
    }
  });
  /*-------------判断系列名称是否存在 结束-------------*/

  /*-----------------系列标签 开始---------------*/
  $scope.label_list=$scope.items.theme.split(',');//系列标签--列表
  $scope.ser_label_arr=[];//标签组循环数组
  $scope.i=1;
  for(let[key,value] of $scope.label_list.entries()){
    $scope.ser_label_arr.push({num:value,label_name:'label_name'+$scope.i});
    $scope.i++;
  }
  //增加标签
  $scope.ser_label_add=function () {
    $scope.i++;
    $scope.ser_label_arr.push({num:'',label_name:'label_name'+$scope.i});
    console.log($scope.ser_label_arr)
  };
  //删除标签
  $scope.ser_close_label=function (index) {
    $scope.ser_label_arr.splice(index,1);
    console.log($scope.ser_label_arr)
  };
  /*-----------------系列标签 结束---------------*/

  //点击保存按钮
  $scope.tran_arr=[];
    $scope.ser_det_ok=function (valid) {
      if(valid && !$scope.name_flag){
        $scope.sur_id='suremodal';
        for(let[key,value] of $scope.ser_label_arr.entries()){
          if(value.num!=''){
            $scope.tran_arr.push(value.num);//标签组
          }
        }
        _ajax.post('/mall/series-edit',{
            id:+$scope.items.id,
            series:$scope.ser_name,
            theme:$scope.tran_arr.join(','),
            intro:$scope.ser_intro,
            series_grade:+$scope.series_grade
        },function (res) {
            console.log(res);
        })
      }else{
        $scope.submitted = true;
        console.log('error')
      }
    };

    //点击模态框确认按钮
  $scope.ser_det_return=function () {
    setTimeout(function () {
      $state.go('style_index');
    },300)
  };

});