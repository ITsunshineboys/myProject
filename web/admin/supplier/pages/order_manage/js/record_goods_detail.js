angular.module('goods_detail_module',[])
.controller('goods_detail_ctrl',function ($scope,$http,$state,$stateParams,_ajax) {
  console.log($stateParams.express_params)
  $scope.order_no=$stateParams.express_params.order_no;
  $scope.sku=$stateParams.express_params.sku;
  $scope.tabflag=$stateParams.express_params.tabflag;
  let statename = $stateParams.express_params.statename;
  _ajax.post("/order/getsupplierorderdetails",{
      order_no:$stateParams.express_params.order_no,
      sku:$stateParams.express_params.sku
  },function (res) {
      $scope.item=res.data;
  })
  //商品详情
  if(!!$scope.order_no && !!$scope.sku){
    _ajax.post('/order/goods-view',{
        order_no:$scope.order_no,
        sku:$scope.sku
    },function (res) {
        $scope.goods_item=res.data;
        $scope.goods_attrs=res.data.goods_attr;//商品属性
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
        $scope.goods_images=res.data.goods_image;//商品图片
        $scope.goods_city=res.data.logisticsDistrict;//物流城市
        $scope.goods_after=res.data.after;//售后
        $scope.goods_guarantee=res.data.guarantee;//保障
    })
  }
    /*返回上一个页面*/
    $scope.backPage = function () {
        if(statename=='waitsend_detail'){
            $state.go('waitsend_detail',{order_no:$scope.order_no,sku:$scope.sku,tabflag:$scope.tabflag})
        }else{
            $state.go(statename,{order_no:$scope.order_no,sku:$scope.sku,tabflag:$scope.tabflag})
        }
    }

    //返回订单
    $scope.back_list=function () {
        $state.go('order_manage',{tabflag:$scope.tabflag});//返回待收货列表
    };
});