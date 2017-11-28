;
let system_offline = angular.module("systemoffline_Module",[]);
system_offline.controller("system_offline",function ($rootScope,$scope,$http,$stateParams,$state,_ajax) {
    $rootScope.crumbs = [{
        name: '商品管理',
        icon: 'icon-shangpinguanli',
        link: 'commodity_manage',
        params:{down_flag:true}
    }, {
        name: '商品详情',
    }];
	$scope.detail_item=$stateParams.item;
	console.log($scope.detail_item);
	$scope.detail_arr=[];//详情数组
  	$scope.logistics_templates_supplier=[];//物流模板类型数组
	$scope.detail_txt=$scope.detail_item.description;//详情描述
	$scope.detail_item_sale_ser=$scope.detail_item.after_sale_services_desc;
  	$scope.sale_services_flag=false;
	for(let [key,value] of $scope.detail_item_sale_ser.entries()){
		if(value==('上门维修'||'上门退货'||'上门换货'||'退货'||'换货')){
			$scope.sale_services_flag=true
		}
	}
	/*-----------------------------商品详情---------------------------------*/
  _ajax.get('/mall/goods-view',{id:+$scope.detail_item.id},function (res) {
      console.log(res);
      $scope.detail_arr=res.data.goods_view;
  })
	/*-----------------------------商品属性---------------------------------*/
  _ajax.get('/mall/goods-attrs-admin',{goods_id:+$scope.detail_item.id},function (res) {
      $scope.detail_attrs=res.data.goods_attrs_admin;
      console.log(res);
  })
  /*------------------------------物流模板-------------------------------------*/
 _ajax.get('/mall/logistics-template-view',{id:+$scope.detail_item.logistics_template_id},function (res) {
     console.log(res);
	   $scope.logistics_name=res.data.logistics_template.name;//模板名称
     $scope.logistics_delivery_method=res.data.logistics_template.delivery_method;//快递方式
     $scope.logistics_district_names_arr=res.data.logistics_template.district_names;//地区
     $scope.delivery_cost_default=res.data.logistics_template.delivery_cost_default;//默认运费
     $scope.delivery_number_default=res.data.logistics_template.delivery_number_default;//默认运费的数量
     $scope.delivery_cost_delta=res.data.logistics_template.delivery_cost_delta;//增加件费用
     $scope.delivery_number_delta=res.data.logistics_template.delivery_number_delta;//增加件的数量
 })
});