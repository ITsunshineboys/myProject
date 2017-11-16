/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*已上架 编辑*/
var onsale_edit = angular.module("onsaleeditModule", ['ngFileUpload']);
onsale_edit.controller("onsaleEdit", function ($scope, $state, $stateParams,$http,Upload,$rootScope) {
    const picprefix = baseUrl+"/";
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    let pid;
    let pattern =/^[\u4E00-\u9FA5A-Za-z0-9]+$/;


    $scope.changescope = $scope;
    $scope.show_class_warning = false;
    $scope.idarr = [];
    $scope.firstclass = []; /*一级下拉框*/
    $scope.subClass = [];/*二级下拉框*/
    $scope.item =  $stateParams.item;
	$scope.iconpath = picprefix+$stateParams.item.icon; /*图片路径*/
	$scope.picwarning = false;
	$scope.savemodal = '';
	let onlinepath = $stateParams.item.path.split(",");
	onlinepath.splice(onlinepath.length-1,onlinepath.length);
	$scope.finalpatharr = onlinepath;
	$scope.offlinereason = '';

    $scope.config =  $rootScope.config;


        /*分类名称是否存在的判断*/
	$scope.addClassName = function () {
		if ((!pattern.test($scope.item.title))||$scope.item.title=='') {
			$scope.class_warning = "您的输入不满足条件,请重新输入"
			$scope.show_class_warning = true;
		}else{
            $scope.show_class_warning = false;
		}
	}

	/*分类所属 第一个下拉框的值*/
	$scope.findParentClass =  (function () {
			$http({
				method: "get",
				url: baseUrl+"/mall/categories-manage-admin",
			}).then(function (res) {
				$scope.firstclass = res.data.data.categories.splice(1);
				for(let i=0;i<$scope.firstclass.length;i++){
					if($scope.firstclass[i].id==$scope.finalpatharr[0]){
						$scope.firstselect=$scope.firstclass[i].id;
						break;
					}
				}
			})
	})()

	/*二级默认*/
	$scope.subClassDefault = (function () {
		$http({
			method: "get",
			url: baseUrl+"/mall/categories-manage-admin",
			params: {pid: $scope.finalpatharr[0]}
		}).then(function (res) {
			$scope.secondclass = res.data.data.categories.splice(1);
			for(let i=0;i<$scope.secondclass.length;i++){
				if($scope.secondclass[i].id==$scope.finalpatharr[1]){
					$scope.secselect=$scope.secondclass[i].id;
					break;
				}
			}
		})
	})()

	/*一级选择后的二级*/
	$scope.subClass = function (obj) {
		$http({
			method: "get",
			url: baseUrl+"/mall/categories-manage-admin",
			params: {pid: obj}
		}).then(function (response) {
			$scope.secondclass = response.data.data.categories.splice(1);
			$scope.secselect = $scope.secondclass[0].id
		})
	}

	//上传图片
	$scope.data = {
		file:null
	}
	$scope.upload = function (file) {
		if(!$scope.data.file){
			return
		}
		// console.log($scope.data)
		Upload.upload({
			url:baseUrl+'/site/upload',
			data:{'UploadForm[file]':file}
		}).then(function (response) {
			if(!response.data.data){
					$scope.picwarning = true;
					$scope.iconpath = 'pages/mall_manage/class_manage/onsale_edit/images/default.png';
			}else{
				$scope.picwarning = false;
				$scope.iconpath = picprefix + response.data.data.file_path;
				$scope.classicon = response.data.data.file_path;
			}
		},function (error) {
			console.log(error)
		})
	}


	/*编辑里的下架*/
	$scope.sureoffline = function () {
		let url = baseUrl+"/mall/category-status-toggle";
		let data =  {id:$scope.item.id,offline_reason:$scope.offlinereason};
		$http.post(url,data,config).then(function (res) {
			if(res.data.code==200){
                $scope.offlinereason = '';
                $state.go("fenleiguanli");
			}
		})
	}

	/*编辑里的下架 取消下架*/
	$scope.cancelOffInEdit = function () {
        $scope.offlinereason = '';
    }

	/*保存编辑*/
	$scope.saveclass = function () {
        if (!pattern.test($scope.item.title)||$scope.item.title=='') {
            $scope.class_warning = "您的输入不满足条件,请重新输入"
            $scope.show_class_warning = true;
            return;
        }


        if($scope.iconpath == 'pages/mall_manage/class_manage/offsale_edit/images/default.png'){
            $scope.picwarning = true;
            return;
        }

		if($scope.show_class_warning==false&&$scope.picwarning==false){
            let description = UE.getEditor('editor').getContent();
			if($scope.finalpatharr.length==3){
				pid = $scope.secselect
			}else if($scope.finalpatharr.length==2){
				pid = $scope.firstselect;
			}else{
				pid = 0;
			}
			let url = baseUrl+"/mall/category-edit";
			let data =  {id:$scope.item.id,title:$scope.item.title,pid:pid,icon:$scope.classicon||$stateParams.item.icon,description:description};
			$http.post(url,data,config).then(function (res) {
                console.log(res);
                $("#save_tishi").modal("show");
                if(res.data.code==200){
                    $scope.save_msg="保存成功"
                    $scope.success_flag = true;
                }else if(res.data.code==1006){
                    $scope.save_msg = res.data.msg;
                    $scope.success_flag = false;
                }
			})
		}
	}

	/*保存模态框确认*/
	$scope.suresave = function () {
		setTimeout(function () {
			$state.go("fenleiguanli");
		},200)
	}
})