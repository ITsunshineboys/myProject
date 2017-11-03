angular.module('goods_detail_module',[])
.controller('goods_detail_ctrl',function ($scope,$http,$state,$stateParams) {
    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
  console.log($stateParams.wait_receive);
  console.log($stateParams.item);
  $scope.item=$stateParams.item;
  $scope.wait_receive=$stateParams.wait_receive;//待收货进入
  $scope.order_no=$stateParams.item.goods_data.order_no;
  $scope.sku=$stateParams.item.goods_data.sku;
  //商品详情
    $scope.goods_item='';
  if(!!$scope.order_no && !!$scope.sku){
      $http.post(baseUrl+'/order/goods-view',{
          order_no:$scope.order_no,
          sku:$scope.sku
      },config).then(function (res) {
          $scope.goods_item=res.data.data;
          $scope.goods_attrs=res.data.data.goods_attr;//商品属性
          for(let[key,value] of $scope.goods_attrs.entries()){
              if(value.unit==0){
                  value.unit=''
              }else if(value.unit==1){
                  value.unit='L'
              }else if(value.unit==2){
                  value.unit='M'
              }else if(value.unit==3){
                  value.unit='M²'
              }else if(value.unit==4){
                  value.unit='Kg'
              }else if(value.unit==5){
                  value.unit='MM'
              }
          }
          $scope.goods_images=res.data.data.goods_image;//商品图片
          $scope.goods_city=res.data.data.logisticsDistrict;//物流城市
          $scope.goods_after=res.data.data.after;//售后
          $scope.goods_guarantee=res.data.data.guarantee;//保障
      },function (err) {
          console.log(err);
      })
  }
    $scope.back_list=function () {
        if($scope.wait_receive=='wait_receive'){
            $state.go('order_manage',{wait_receive_flag:true});//返回待收货列表
        }else if($scope.wait_receive=='wait_send'){
            $state.go('order_manage',{wait_send_flag:true});//返回待发货列表
        }
    };
});