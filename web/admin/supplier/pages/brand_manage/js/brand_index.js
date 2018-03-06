angular.module("brand_index_module",[])
	.controller("brand_index_ctrl",function ($rootScope,$scope,$http,_ajax) {
	$rootScope.crumbs = [{
		name: '品牌管理',
		icon: 'icon-Brand',
	}];
		// 判断页面是否从详情页进到当前页面
		let fromState = $rootScope.fromState_name === 'add_brand' || $rootScope.fromState_name === 'brand_detail' || $rootScope.fromState_name === 'edit_brand';
		if (!fromState) {
			sessionStorage.removeItem('saveStatus');
		}
	/*分页配置*/
	$scope.Config = {
		showJump: true,
		itemsPerPage: 12,
		currentPage: 1,
		onChange: function () {
			tablePages();
		}
	};
	$scope.params = {
		page: 1,                     // 当前页数
		sort_time: 2,               // 时间类型
	};
	let tablePages=function () {
		$scope.params.page=$scope.Config.currentPage;// 点击页数，传对应的参数
		_ajax.get('/supplieraccount/supplier-brand-list',$scope.params,function (res) {
			console.log(res);
			$scope.brand_list=res.data.details;
			$scope.Config.totalItems = res.data.total;
		});
	};
	// 审核备注
	$scope.remark=function (value) {
		$scope.remark_value=value;
		$("#remark_modal").modal('show');
	}
	// 申请时间
	$scope.sortClick=function () {
		$scope.Config.currentPage = 1;
		$scope.params.page = 1;
		$scope.params.sort_time===2?$scope.params.sort_time=1:$scope.params.sort_time=2;
		tablePages();
	}
		// 缓存当前页面状态参数
		$scope.saveStatus = saveParams
		function saveParams() {
			let temp = JSON.stringify($scope.params);
			sessionStorage.setItem('saveStatus', temp) // 列表数据
		}
		// 判断是否在详情进行过操作，如果没有，记录状态
		let saveTempStatus = sessionStorage.getItem('saveStatus');
		if (saveTempStatus !== null) {      // 判断是否保存参数状态
			saveTempStatus = JSON.parse(saveTempStatus);
			$scope.params = saveTempStatus;
			$scope.Config.currentPage = saveTempStatus.page
		}
});