angular.module('goods_detail_module',[])
.controller('goods_detail_ctrl',function ($rootScope,$scope,$http,$state,$stateParams,_ajax) {
  $scope.order_no=$stateParams.order_no;
  $scope.sku=$stateParams.sku;
  $scope.tabflag=$stateParams.tabflag;
  let statename = $stateParams.statename;
  //返回上一个页面
    $scope.backPage = function () {
	    console.log(statename);
	    $state.go(statename,{order_no:$scope.order_no,sku:$scope.sku,tabflag:$scope.tabflag})
    };
    //返回订单
    $scope.back_list=function () {
        $state.go('order_manage',{tabflag:$scope.tabflag});//返回待收货列表
    };
    //面包屑
    $rootScope.crumbs = [{
        name: '订单管理',
        icon: 'icon-dingdanguanli',
        link: $scope.back_list
    }, {
        name: '订单详情',
        link: $scope.backPage,
    },{
        name:'记录商品详情'
    }];
  _ajax.get("/order/getsupplierorderdetails",{
      order_no:$stateParams.order_no,
      sku:$stateParams.sku
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
});