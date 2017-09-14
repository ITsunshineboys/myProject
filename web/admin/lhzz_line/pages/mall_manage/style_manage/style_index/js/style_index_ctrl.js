/**
 * Created by hulingfangzi on 2017/8/10.
 */
/*系列/风格/属性管理*/
var style_index = angular.module("styleindexModule",[]);
style_index.controller("style_index",function ($scope) {
	$scope.showseries = true;
	$scope.showstyle = false;
	$scope.showattr = false;

	/*选项卡切换方法*/
	$scope.changeToseries = function () {
		$scope.showseries = true;
		$scope.showstyle = false;
		$scope.showattr = false;
	}

	$scope.changeTostyle = function () {
		$scope.showseries = false;
		$scope.showstyle = true;
		$scope.showattr = false;
	}

	$scope.changeToattr = function () {
		$scope.showseries = false;
		$scope.showstyle = false;
		$scope.showattr = true;
	}

	//系类管理


});