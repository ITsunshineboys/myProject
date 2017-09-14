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
<<<<<<< Updated upstream
	$scope.offsaleclasstitle = $stateParams.classtitle;
	$scope.class_name = $stateParams.classtitle;
	$scope.onlinetitles = $stateParams.classtitles;
	$scope.iconpath = picprefix+$stateParams.iconpath; /*图片路径*/
	$scope.picwarning = false;
	$scope.savemodal = '';
	$scope.addperson =  $stateParams.addperson;
	$scope.online_time = $stateParams.online_time;


	let onlinepath = $stateParams.classpath.split(",");
	onlinepath.splice(onlinepath.length-1,onlinepath.length);
	$scope.finalpatharr = onlinepath;
	$scope.offlinereason = '';
	$scope.toolbar = {
		toolbars:['bold', 'italic'],
	}

    $scope.config = {
        // 定制图标
        toolbars: [
            ['Undo','Redo','formatmatch','removeformat', 'Bold','italic','underline','strikethrough','fontborder',
                'horizontal','fontfamily', 'fontsize','justifyleft', 'justifyright',
                'justifycenter', 'justifyjustify', 'forecolor',  'backcolor','insertorderedlist', 'insertunorderedlist',
                'rowspacingtop','rowspacingbottom','attachment','imagecenter','simpleupload', 'time', 'date', 'preview']
        ],

        //首行缩进距离,默认是2em
        indentValue:'2em',
        //初始化编辑器宽度,默认1000
        initialFrameWidth:800,
        //初始化编辑器高度,默认320
        initialFrameHeight:320,
        //编辑器初始化结束后,编辑区域是否是只读的，默认是false
        readonly : false ,
        //启用自动保存
        enableAutoSave: false,
        //自动保存间隔时间， 单位ms
        saveInterval:1000,
        //是否开启初始化时即全屏，默认关闭
        fullscreen : false,
        //图片操作的浮层开关，默认打开
        imagePopup:true,
        //提交到后台的数据是否包含整个html字符串
        allHtmlEnabled:false,
        //是否启用元素路径，默认是显示
        elementPathEnabled:false,
        //是否开启字数统计
        wordCount:false
    }
	/*获取所有分类名称*/
	$scope.alltitles = (function () {
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
	})()


	/*分类名称是否存在的判断*/
=======
	/*获取分类名称*/
	$http({
		method: "get",
		url: "http://test.cdlhzz.cn:888/mall/category-list-admin",
		params: {status: 1}
	}).then(function (res) {
		$scope.allonsalepro = res.data.data.category_list_admin.details;
	})

	/*分类名称判断*/
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream

	/*编辑里的下架*/
	$scope.sureoffline = function () {
		let url = "http://test.cdlhzz.cn:888/mall/category-status-toggle";
		let data =  {id:$scope.offsaleclassid,offline_reason:$scope.offlinereason};
		$http.post(url,data,config).then(function (res) {
			$scope.offlinereason = '';
			$state.go("fenleiguanli");
		})
	}

	/*编辑里的下架 取消下架*/
	$scope.cancelOffInEdit = function () {
        $scope.offlinereason = '';
    }


	/*保存编辑*/
	$scope.saveclass = function () {
		// console.log($scope.classicon)
		$scope.addClassName();
		if($scope.showtishi==false&&$scope.picwarning==false){
			if($scope.finalpatharr.length==3){
				pid = $scope.secselect
			}else if($scope.finalpatharr.length==2){
				pid = $scope.firstselect;
			}else{
				pid = 0;
			}
			let url = "http://test.cdlhzz.cn:888/mall/category-edit";
			let data =  {id:$scope.offsaleclassid,title:$scope.class_name,pid:pid,icon:$scope.classicon||$stateParams.iconpath,description:$scope.onlinedes};
			$http.post(url,data,config).then(function (res) {
				// console.log(res)
			})
			$scope.savemodal = '#save_tishi'
			$scope.savesuccess = true;
		}
	}
=======
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
>>>>>>> Stashed changes

})