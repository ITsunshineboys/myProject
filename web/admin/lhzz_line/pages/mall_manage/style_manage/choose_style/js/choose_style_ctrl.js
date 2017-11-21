var choose_style = angular.module("choose_styleModule",[]);
choose_style.controller("choose_style",function ($scope,$stateParams,$http,$state) {
	$scope.name = "吞吞吐吐";
	$scope.showstyle = true

	//风格管理

	$scope.item_check = [];
	//获取一级
	$http({
		method: 'get',
		url: baseUrl+'/mall/categories'
	}).then(function successCallback(response) {
		$scope.details = response.data.data.categories;
		$scope.oneColor= $scope.details[0];
		// console.log(response);

	});
	//获取二级
	$http({
		method: 'get',
		url: baseUrl+'/mall/categories?pid=1'
	}).then(function successCallback(response) {
		$scope.second = response.data.data.categories;
		$scope.twoColor= $scope.second[0];
		// console.log(response)
	});
	//获取三级
	$http({
		method: 'get',
		url: baseUrl+'/mall/categories?pid=2'
	}).then(function successCallback(response) {
        console.log(response);
		$scope.three = response.data.data.categories;
		for(let [key,value] of $scope.three.entries()){
			if($scope.item_check.length == 0){
				value['complete'] = false
			}else{
				for(let [key1,value1] of $scope.item_check.entries()){
					if(value.id == value1.id){
						value.complete = true
					}
				}
			}
		}

	});
	//点击一级 获取相对应的二级
	$scope.getMore = function (n) {
		$scope.oneColor = n;
		$http({
			method: 'get',
			url: baseUrl+'/mall/categories?pid='+ n.id
		}).then(function successCallback(response) {
			$scope.second = response.data.data.categories;
			//console.log(response.data.data.categories[0].id);
			console.log(response);
			$scope.twoColor = $scope.second[0];
			$http({
				method: 'get',
				url: baseUrl+'/mall/categories?pid='+ $scope.second[0].id
			}).then(function successCallback(response) {
				$scope.three = response.data.data.categories;
				//console.log(response.data.data.categories[0].id);
				for(let [key,value] of $scope.three.entries()){
					if($scope.item_check.length == 0){
						value['complete'] = false
					}else{
						for(let [key1,value1] of $scope.item_check.entries()){
							if(value.id == value1.id){
								value.complete = true
							}
						}
					}
				}
				console.log(response);
			});
		});

	}
	//点击二级 获取相对应的三级
    $scope.three = []
	$scope.getMoreThree = function (n) {
		$scope.id=n;
		$scope.twoColor = n;
		$http({
			method: 'get',
			url: baseUrl+'/mall/categories?pid='+ n.id
		}).then(function successCallback(response) {
			$scope.three = response.data.data.categories;
            // console.log($scope.three);
			for(let [key,value] of $scope.three.entries()){
				if($scope.item_check.length == 0){
					value['complete'] = false
				}else{
					for(let [key1,value1] of $scope.item_check.entries()){
						if(value.id == value1.id){
							value.complete = true
						}
					}
				}

			}

		});
	}

	//添加拥有系列的三级
	$scope.check_item = function(item){
		console.log(item);
		if(item.complete){
			$scope.item_check.push(item)
		}else{
			$scope.item_check.splice($scope.item_check.indexOf(item),1)
		}

	};
	//删除拥有系列的三级
	$scope.delete_item = function (item) {
		item.complete = false;
		$scope.item_check.splice($scope.item_check.indexOf(item),1)
	};
	//模态框确认按钮保存数据发送后台
	$scope.send_series = function(){
		let obj = {};
		for(let [key,value] of $scope.item_check.entries()){
			if(value.pid in obj){
				obj[value.pid+''].push(+value.id)
			}else{
				obj[value.pid+''] = [+value.id]
			}
		}

		console.log(obj);
		//发送分类所拥有的系类分类、
		let config = {
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			transformRequest: function (data) {
				return $.param(data)
			}
		};
		$http.post(baseUrl+'/mall/categories-style-series-reset',{
			category_ids:obj,
			type:'style'
		},config).then(function(response){
			console.log(response)
		}, function (error) {
			console.log(error)
		})
	};

	$scope.back_return =function () {
		setTimeout(function () {
            $state.go("style_index",{showstyle:true});
		},300)

	}
	//默认进页面获取三级分类所具有的系类
	$http({
		method: 'get',
		url: baseUrl+'/mall/categories-have-style-series?type='+'style'
	}).then(function successCallback(response) {
		console.log(response);

		$scope.item_check = response.data.data.have_style_series_categories;
		for(let [key,value] of Object.entries($scope.three)){
			if($scope.item_check.length == 0){
				value['complete'] = false
			}else{
				for(let [key1,value1] of $scope.item_check.entries()){
					if(value.id == value1.id){
						value.complete =true
					}
				}
			}
		}

		console.log($scope.item_check);

	});
});