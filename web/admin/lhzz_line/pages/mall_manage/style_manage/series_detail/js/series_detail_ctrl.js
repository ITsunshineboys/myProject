let series_detail = angular.module("seriesdetailModule",[]);
series_detail.controller("series_detail",function ($scope,$http,$stateParams) {
  $scope.myng=$scope;
  $scope.items=$stateParams.item;
  $scope.series_arr=[];
  $scope.ser_name=$scope.items.series;//名称
  $scope.sec_time=$scope.items.creation_time;//时间
//	系列——展示数据
  $http.get('http://test.cdlhzz.cn:888/mall/series-list').then(function (res) {
    $scope.series_arr.push(res.data.data.series_list);
  },function (err) {
    console.log(err);
  });
  console.log($scope.series_arr);

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

  // let arr = $scope.list_ser.concat();
  // for(let [key1,ser] of arr.entries()){

    // for(let [key,list] of $scope.series_arr.entries()){
      // console.log("展示数据");
      // console.log(list);
      // if(ser.num===list.series_grade){
      //   $scope.list_ser.splice($scope.list_ser.indexOf(ser),1)
      // }
    // }
  // }
});