angular.module("brand_detail_module",[])
	.controller("brand_detail_ctrl",function ($rootScope,$scope,$http,_ajax,$stateParams) {
		$rootScope.crumbs = [{
			name: '品牌管理',
			icon: 'icon-shangchengguanli',
			link: 'brand_index'
		},{
			name: '品牌详情'
		}];
		$scope.brand_id=$stateParams.brand_id;//传过来的品牌id
		_ajax.get("/supplieraccount/supplier-brand-view",{
			brand_id:$scope.brand_id
		},function (res) {
			console.log(res);
			$scope.name=res.data.name;//名称
			$scope.certificate=res.data.certificate;//商标注册码
			$scope.logo=res.data.logo;//logo
			$scope.category_titles=res.data.category_titles;//所在分类
			$scope.review_status=res.data.review_status;//审核状态
			$scope.review_time=res.data.review_time;//审核  时间
			$scope.reason=res.data.reason;//审核备注
		});
		//返回
		$scope.back_btn=function () {
			history.go(-1);
		}
	});