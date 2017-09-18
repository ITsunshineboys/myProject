;
let supplier_index = angular.module("supplier_index",[])
  .controller("supplier_index_ctrl",function ($scope,$http,$state,$stateParams) {
        $http.get('http://test.cdlhzz.cn:888/mall/supplier-index-admin').then(function (res) {
          console.log('首页返回')
          console.log(res);
        },function (err) {
          console.log(err);
        })
  });