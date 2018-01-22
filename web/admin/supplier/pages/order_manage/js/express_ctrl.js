let express = angular.module("expressModule", []);
express.controller("express_ctrl", function ($rootScope, $scope, _ajax, $stateParams, $state) {
	$scope.order_no = $stateParams.order_no; //订单号
	$scope.sku = $stateParams.sku; //商品编号
	$scope.tabflag = $stateParams.tabflag;
	let statename = $stateParams.statename;

	/*获取物流信息*/
	_ajax.post("/order/getexpress", {
		order_no: $scope.order_no,
		sku: $scope.sku
	}, function (res) {
		$scope.order_info = res.data;
		!!$scope.order_info.waybillname ? $scope.order_info.waybillname = $scope.order_info.waybillname : $scope.order_info.waybillname = '送货上门'
	});

	/*返回上一个页面*/
	$scope.backPage = function () {
		$state.go(statename, {order_no: $scope.order_no, sku: $scope.sku, tabflag: $scope.tabflag})
	};

	/*面包屑参数*/
	$rootScope.crumbs = [{
		name: '订单管理',
		icon: 'icon-dingdanguanli',
		link: 'order_manage',
		params: {tabflag: $scope.tabflag}
	}, {
		name: '订单详情',
		link: $scope.backPage,
	}, {
		name: '物流详情'
	}];
});