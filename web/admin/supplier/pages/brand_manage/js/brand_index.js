angular.module("brand_index_module",[])
	.controller("brand_index_ctrl",function ($rootScope,$scope,$http,_ajax) {
	$rootScope.crumbs = [{
		name: '品牌管理',
		icon: 'icon-shuju',
	}];
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
		page: 1,                        // 当前页数
		sort_time: '2',               // 时间类型
	};
	let tablePages=function () {
		$scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
		_ajax.get('/supplieraccount/supplier-brand-list',$scope.params,function (res) {
			console.log(res);
			$scope.brand_list=res.data.details;
			$scope.Config.totalItems = res.data.total;
		});
	};
		$scope.params.sort_time=2;//申请时间排序状态
	//审核备注
	$scope.remark=function (value) {
		$scope.remark_value=value;
		$("#remark_modal").modal('show');
	}
	//申请时间
	$scope.sortClick=function () {
		$scope.params.sort_time===2?$scope.params.sort_time=1:$scope.params.sort_time=2;
		tablePages();
	}
});