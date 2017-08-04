/*分类管理
* 控制器
**/

const config = {
	headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	transformRequest: function (data) {
		return $.param(data)
	}
};
var cla_mag = angular.module("clamagModule", []);
cla_mag.controller("cla_mag_tabbar", function ($scope, $http, $state) {
	/*当前页*/
	$scope.firstselect = 1;
	$scope.parentclass = [];
	$scope.selPage = 1; /*起始页面页码*/
	$scope.secclass = 0;/*一级分类下的二级分类数*/
	$scope.totaloffclass = 0;/*总的分类数*/
	$scope.classidinoffsec = [];/*下架的二级分类中的id，包括三级和它本身*/
	$scope.classidinofffirst = [];/*下架的一级分类中的id，包括三级、二级和它本身*/
	$scope.secclassidin_offfirst = [];/*下架的一级分类中的二级分类*/
	$scope.allpages = 0;
	$scope.offlinereason = '';/*已上架单个下架初始化下架原因*/
	$scope.isSelected = false;
	$scope.isSelectedtwo = false;
	$scope.xiajiaarr = []; /*已上架批量下架初始化数组*/
	$scope.shangjiaarr = [];
	$scope.piliangofflinereason = '';/*已上架批量下架初始化下架原因*/
	$scope.seloffPage = 1;

	$scope.showonsale = true;
	$scope.showoffsale = false;

	/*选项卡切换方法*/
	$scope.changeToonsale = function () {
		$scope.showonsale = true;
		$scope.showoffsale = false;
	}

	$scope.changeTooffsale = function () {
		$scope.showonsale = false;
		$scope.showoffsale = true;
	}

	/*已上架创建时间排序*/
	$scope.ascorder = true;
	$scope.desorder =  false;

	$scope.changepic = function () {
		$scope.ascorder = false;
		$scope.desorder =  true;
			$http({
				method: "get",
				params: {status: 1,"sort[]":"id:3"},
				url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
			}).then(function (response) {
				$scope.allonsalepro = response.data.data.category_list_admin.details;
				$scope.selPage = 1;
			})
	}

	$scope.changepictwo = function () {
		$scope.ascorder = true;
		$scope.desorder =  false;
		$http({
			method: "get",
			params: {status: 1,"sort[]":"id:4"},
			url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
		}).then(function (response) {
			$scope.allonsalepro = response.data.data.category_list_admin.details;
			$scope.selPage = 1;
		})
	}

	/*已下架创建时间排序*/
	$scope.offascorder = true;
	$scope.offdesorder =  false;

	$scope.offchangepic = function () {
		$scope.offascorder = false;
		$scope.offdesorder =  true;
		$http({
			method: "get",
			params: {status: 0,"sort[]":"id:3"},
			url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
		}).then(function (response) {
			$scope.alloffsalepro = response.data.data.category_list_admin.details;
			$scope.seloffPage = 1;
		})
	}

	$scope.offchangepictwo = function () {
		$scope.offascorder = true;
		$scope.offdesorder =  false;
		$http({
			method: "get",
			params: {status: 0,"sort[]":"id:4"},
			url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
		}).then(function (response) {
			$scope.alloffsalepro = response.data.data.category_list_admin.details;
			$scope.seloffPage = 1;
		})
	}


	/*分类选择一级下拉框*/
	$scope.firstClass = (function () {
		$http({
			method: "get",
			url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
		}).then(function (response) {
			$scope.firstclass = response.data.data.categories;
		})
	})()

	$scope.subClassDefault = (function () {
		$http({
			method: "get",
			url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
			params: {pid: 0}
		}).then(function (res) {
			$scope.secondclass = res.data.data.categories;
		})
	})()

	/*分类选择二级下拉框*/
	$scope.subClass = function (obj) {
		$http({
			method: "get",
			url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
			params: {pid: obj}
		}).then(function (response) {
			$scope.secondclass = response.data.data.categories;
		})
	}

	/*已上架table数据内容*/
	$scope.tableContent = (function () {
		$scope.pageSize = 5; /*显示的页数*/
		$http({
			method: "get",
			url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
			params: {status: 1,"sort[]":"id:3"},
		}).then(function (res) {
			$scope.allonsalepro = res.data.data.category_list_admin.details;
			$scope.allpages = Math.ceil(res.data.data.category_list_admin.total / 12); /*总页数*/
			$scope.newPages = $scope.allpages > 5 ? 5 : $scope.allpages; /*总页数大于5就显示5页 小于5页有多少页显示多少页*/
			$scope.pageList = [];
			for(var i = 1;i <=$scope.newPages;i++){
				$scope.pageList.push(i);
			}
			return $scope.pageList;
		})
	})()


	//已下架table数据内容
	$scope.offtableContent = (function () {
		$scope.offpageSize = 5; /*显示的页数*/
		$http({
			method: "get",
			url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
			params: {status: 0,"sort[]":"id:3"},
		}).then(function (res) {
			$scope.alloffsalepro = res.data.data.category_list_admin.details;
			$scope.alloffpages = Math.ceil(res.data.data.category_list_admin.total / 12); /*总页数*/
			$scope.offnewPages = $scope.alloffpages > 5 ? 5 : $scope.alloffpages; /*总页数大于5就显示5页 小于5页有多少页显示多少页*/
			$scope.offpageList = [];
			for(var i = 1;i <=$scope.offnewPages;i++){
				$scope.offpageList.push(i);
			}
			return $scope.offpageList;
		})
	})()

/*已上架点击跳转至相应页数*/
	$scope.choosePage = function (page) {
		$http({
			method: "get",
			url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
			params: {status: 1, page: page},
		}).then(function (res) {
			$scope.allonsalepro = res.data.data.category_list_admin.details;
			$scope.selPage = page;
			$scope.isActivePage(page);
		})
	}

	/*已下架点击跳转至相应页数*/
	$scope.chooseOffPage = function (page) {
		$http({
			method: "get",
			url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
			params: {status: 0, page: page},
		}).then(function (res) {
			$scope.alloffsalepro = res.data.data.category_list_admin.details;
			$scope.seloffPage = page;
			$scope.isOffActivePage(page);
		})
	}


//上一页
	$scope.Previous = function () {
		if($scope.selPage>1){
			$scope.selPage--
			$scope.choosePage($scope.selPage);
		}
	}

	$scope.offPrevious = function () {
		if($scope.seloffPage>1){
			$scope.seloffPage--
			$scope.chooseOffPage($scope.seloffPage);
		}
	}

//下一页
	$scope.Next = function () {
		if($scope.selPage<$scope.allpages){
			$scope.selPage++;
			$scope.choosePage($scope.selPage);
		}
	};

	$scope.offNext = function () {
		if($scope.seloffPage<$scope.alloffpages){
			$scope.seloffPage++;
			$scope.chooseOffPage($scope.seloffPage);
		}
	};
/*点击页码加样式*/
	$scope.isActivePage = function (page) {
		return $scope.selPage == page;
	};

	$scope.isOffActivePage = function (page) {
		return $scope.seloffPage == page;
	};

	/*已上架通用跳转页面*/
	$scope.selectPage = function (page,url) {
		$http({
			method: "get",
			url: url,
			params: {status: 1, page: page},
		}).then(function (res) {
			$scope.allonsalepro = res.data.data.category_list_admin.details;
			$scope.selPage = page;
			$scope.isActivePage(page);
		})
	}

	/*已下架通用跳转*/
	$scope.selectOffPage = function (page,url) {
		$http({
			method: "get",
			url: url,
			params: {status: 0, page: page},
		}).then(function (res) {
			$scope.alloffsalepro = res.data.data.category_list_admin.details;
			$scope.seloffPage = page;
			$scope.isOffActivePage(page);
		})
	}
/*===========================已上架 下架操作==========================*/
	/*=已上架列表 单个下架*/

	/*已上架单个分类下架种类统计*/
	$scope.tobeoffline = function (id,level) {
		$scope.singleoffid = id;
		$scope.singleofflevel = level;
	}

	/*已下架单个分类上架种类统计*/
	$scope.tobeonline = function (id,level) {
		$scope.singleonid = id;
		$scope.singleonlevel = level;
	}

	/*单个确认下架*/
	$scope.sureoffline = function () {
			let url = "http://test.cdlhzz.cn:888/mall/category-status-toggle";
			let data =  {id:$scope.singleoffid,offline_reason:$scope.offlinereason};
			$http.post(url,data,config).then(function (res) {
				$scope.offlinereason = '';
				window.location.reload();
			})
	}

	/*单个确认上架*/
	$scope.sureonline = function () {
		let url = "http://test.cdlhzz.cn:888/mall/category-status-toggle";
		let data =  {id:$scope.singleonid};
		$http.post(url,data,config).then(function (res) {
			console.log(res);
			window.location.reload();
		})
	}

	/*单个取消下架*/
	$scope.canceloffline = function () {
		$scope.offlinereason = '';
	}

	/*单个取消上架无操作*/

	/*已上架列表 批量下架*/
	$scope.piliangxiajia = function () {
		$scope.xiajiaarr.length = 0;
		for(let [key,value] of $scope.allonsalepro.entries()){
			if(value.state){
				$scope.xiajiaarr.push(value.id)
			}
		}
	}

	$scope.surepiliangoffline = function () {
			$scope.piliangoffids = $scope.xiajiaarr.join(',');
			let url = "http://test.cdlhzz.cn:888/mall/category-disable-batch";
			let data =  {ids:$scope.piliangoffids,offline_reason:$scope.piliangofflinereason};
			$http.post(url,data,config).then(function (res) {
				$scope.piliangofflinereason = '';
				window.location.reload();
			})
		}

		$scope.cancelplliangoffline = function () {
			$scope.xiajiaarr.length = 0;
			$scope.piliangofflinereason = '';

		}

	/*已下架列表 批量上架*/
	$scope.piliangshangjia = function () {
		$scope.shangjiaarr.length = 0;
		for(let [key,value] of $scope.alloffsalepro.entries()){
			if(value.state){
				$scope.shangjiaarr.push(value.id)
			}
		}
	}

	$scope.surepiliangonline = function () {
		$scope.piliangonids = $scope.shangjiaarr.join(',');
		let url = "http://test.cdlhzz.cn:888/mall/category-enable-batch";
		let data =  {ids:$scope.piliangonids};
		$http.post(url,data,config).then(function (res) {
			window.location.reload();
		})
	}

	$scope.cancelplliangonline = function () {
		$scope.shangjiaarr.length = 0;
	}


		/*===========================下架/下架处理结束=======================*/

	/*筛选查询*/
	$scope.chaxun = function () {
		$scope.pageList.length = 0;
		$scope.selPage = 1;
	// 	/*只有一级下拉的全部*/
			if(($scope.firstselect==0&&$scope.secselect==0)||($scope.firstselect==0&&$scope.secselect==null)){
				$http({
					method: "get",
					url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
					params: {status: 1},
				}).then(function (res) {
					$scope.allonsalepro = res.data.data.category_list_admin.details;
					$scope.allpages = Math.ceil(res.data.data.category_list_admin.total / 12); /*总页数*/
					// $scope.pageList = [];
					$scope.newPages = $scope.allpages > 5 ? 5 : $scope.allpages;
					for(var i = 1;i <=$scope.newPages;i++){
						$scope.pageList.push(i);
					}
				})
				/*二级下拉为全部*/
			}else if($scope.firstselect!=0&&$scope.secselect==0){
				$http({
					method: "get",
					url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
					params: {status: 1,pid:$scope.firstselect},
				}).then(function (res) {
					$scope.allonsalepro = res.data.data.category_list_admin.details;
					$scope.allpages = Math.ceil(res.data.data.category_list_admin.total / 12); /*总页数*/
					$scope.newPages = $scope.allpages > 5 ? 5 : $scope.allpages;
					for(var i = 1;i <=$scope.newPages;i++){
						$scope.pageList.push(i);
					}

					$scope.Previous = function () {
						console.log("previous")
						if($scope.selPage>1){
							$scope.selPage--
							$scope.selectPage($scope.selPage,"http://test.cdlhzz.cn:888/mall/category-list-admin");
							// $scope.selectPage($scope.selPage,)
						}
					}
					//下一页
					$scope.Next = function () {
						console.log("next")
						if($scope.selPage<$scope.allpages){
							$scope.selPage++;
							$scope.selectPage($scope.selPage,"http://test.cdlhzz.cn:888/mall/category-list-admin");
						}
					};

				})
				/*两个都不为全部*/
			}else if($scope.firstselect!=0&&$scope.secselect!=0){
				$http({
					method: "get",
					url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
					params: {status: 1,pid:$scope.secselect},
				}).then(function (res) {
					$scope.allonsalepro = res.data.data.category_list_admin.details;
					$scope.allpages = Math.ceil(res.data.data.category_list_admin.total / 12); /*总页数*/
					$scope.newPages = $scope.allpages > 5 ? 5 : $scope.allpages;
					for(var i = 1;i <=$scope.newPages;i++){
						$scope.pageList.push(i);
					}
				})
			}
}

	$scope.offchaxun = function () {
		$scope.offpageList.length = 0;
		$scope.seloffPage = 1;
		// 	/*只有一级下拉的全部*/
		if(($scope.firstselect==0&&$scope.secselect==0)||($scope.firstselect==0&&$scope.secselect==null)){
			$http({
				method: "get",
				url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
				params: {status: 0},
			}).then(function (res) {
				$scope.alloffsalepro = res.data.data.category_list_admin.details;
				$scope.alloffpages = Math.ceil(res.data.data.category_list_admin.total / 12); /*总页数*/
				$scope.offnewPages = $scope.alloffpages > 5 ? 5 : $scope.alloffpages;
				for(var i = 1;i <=$scope.offnewPages;i++){
					$scope.offpageList.push(i);
				}
			})
			/*二级下拉为全部*/
		}else if($scope.firstselect!=0&&$scope.secselect==0){
			$http({
				method: "get",
				url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
				params: {status: 0,pid:$scope.firstselect},
			}).then(function (res) {
				$scope.alloffsalepro = res.data.data.category_list_admin.details;
				$scope.alloffpages = Math.ceil(res.data.data.category_list_admin.total / 12); /*总页数*/
				$scope.offnewPages = $scope.alloffpages > 5 ? 5 : $scope.alloffpages;
				for(var i = 1;i <=$scope.offnewPages;i++){
					$scope.offpageList.push(i);
				}

				$scope.offPrevious = function () {
					if($scope.seloffPage>1){
						$scope.seloffPage--
						$scope.selectOffPage($scope.seloffPage,"http://test.cdlhzz.cn:888/mall/category-list-admin");
						// $scope.selectPage($scope.selPage,)
					}
				}
				//下一页
				$scope.offNext = function () {
					if($scope.seloffPage<$scope.alloffpages){
						$scope.seloffPage++;
						$scope.selectOffPage($scope.seloffPage,"http://test.cdlhzz.cn:888/mall/category-list-admin");
					}
				};

			})
			/*两个都不为全部*/
		}else if($scope.firstselect!=0&&$scope.secselect!=0){
			$http({
				method: "get",
				url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
				params: {status: 0,pid:$scope.secselect},
			}).then(function (res) {
				$scope.alloffsalepro = res.data.data.category_list_admin.details;
				$scope.alloffpages = Math.ceil(res.data.data.category_list_admin.total / 12); /*总页数*/
				$scope.offnewPages = $scope.alloffpages > 5 ? 5 : $scope.alloffpages;
				for(var i = 1;i <=$scope.offnewPages;i++){
					$scope.offpageList.push(i);
				}
			})
		}
	}

})





