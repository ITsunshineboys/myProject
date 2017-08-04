/**
 * Created by Administrator on 2017/7/27.
 */
var onsale_edit = angular.module("onsaleeditModule", []);
onsale_edit.controller("onsaleEdit", function ($scope, $state, $stateParams, $http) {
	let pattern = /^[\u4e00-\u9fa5]{0,10}$/;
	$scope.showtishi = false;
	$scope.idarr = [];
	$scope.offsaleclasstitle = $stateParams.classtitle;
	$scope.offsaleclasslevel = $stateParams.classlevel;
	$scope.offsaleclassid = $stateParams.classid;
	/*获取分类名称*/
	$http({
		method: "get",
		url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
		params: {status: 1}
	}).then(function (res) {
		$scope.allonsalepro = res.data.data.category_list_admin.details;
	})

	/*分类名称判断*/
	$scope.addClassName = function () {
		if (!pattern.test($scope.class_name)) {
			$scope.tishi = "您的输入不满足条件,请重新输入"
			$scope.showtishi = true;
		} else {
			$http({
				method: "get",
				url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
			}).then(function (res) {
				for (let key in res.data.data.categories) {
					$scope.idarr.push(res.data.data.categories[key].title)
					$http({
						method: "get",
						url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
						params: {pid: res.data.data.categories[key].id}
					}).then(function (res) {
						for (let key in res.data.data.categories) {
							$scope.idarr.push(res.data.data.categories[key].title)
						}
					})
				}
			})

			for (let i = 0; i < $scope.idarr.length; i++) {
				if ($scope.class_name == $scope.idarr[i]) {
					$scope.tishi = "分类名称不能重复，请重新输入";
					$scope.showtishi = true;
					break;
				} else {
					$scope.showtishi = false;
				}
			}
		}
	}

	/**/
	$scope.findParentClass = (function () {
		// console.log($scope.onsaleclasslevel)
			if($scope.offsaleclasslevel=="三级"){
				$http({
					method: "get",
					url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
					params: {pid:$scope.offsaleclassid}
				}).then(function (res) {
					// $scope.allonsalepro = res.data.data.category_list_admin.details;
					console.log(res);
				})
			}
	})()

})