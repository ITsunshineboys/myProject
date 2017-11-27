/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*已下架 编辑*/

var offsale_edit = angular.module("offsaleeditModule",['ngFileUpload']);
offsale_edit.controller("offsaleEdit",function ($scope,$state,$stateParams,$http,Upload,$rootScope,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '分类管理',
        link: 'fenleiguanli',
        params:{offsale_flag:true}
    },{
        name: '分类详情',
	}];

	// const picprefix = baseUrl+"/";
    let pid;
    let pattern = /^[\u4E00-\u9FA5A-Za-z0-9]+$/;  //分类名称正则
	$scope.show_class_warning = false; //分类名称错误提示
	$scope.firstclass = []; /*一级下拉框*/
	$scope.subClass = [];/*二级下拉框*/
	$scope.item = $stateParams.item;  //列表传参
	//分类路径处理
    let onlinepath = $stateParams.item.path.split(",");
	onlinepath.splice(onlinepath.length-1,onlinepath.length);
	$scope.finalpatharr = onlinepath;
	$scope.iconpath = $stateParams.item.icon; /*图片路径*/
	$scope.show_picwarning = false;
	$scope.selectscope = $scope;


	/*富文本编辑器*/
    $scope.config =  $rootScope.config;

	/*分类名称是否存在的判断*/
	$scope.addClassName = function () {
		if (!pattern.test($scope.item.title)||$scope.item.title=='') {
			$scope.class_warning = "您的输入不满足条件,请重新输入"
			$scope.show_class_warning = true;
		}else{
            $scope.show_class_warning = false;
		}
	}

	/*分类所属 第一个下拉框的值*/
	$scope.findParentClass =  (function () {
        _ajax.get('/mall/categories-manage-admin',{},function (res) {
            $scope.firstclass = res.data.categories.splice(1);
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
		_ajax.get('/mall/categories-manage-admin',{pid: $scope.finalpatharr[0]},function (res) {
            $scope.secondclass = res.data.categories.splice(1);
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
		_ajax.get('/mall/categories-manage-admin',{pid: obj},function (res) {
            $scope.secondclass = res.data.categories.splice(1);
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
		Upload.upload({
			url:baseUrl+'/site/upload',
			data:{'UploadForm[file]':file}
		}).then(function (response) {
			if(!response.data.data){
				$scope.show_picwarning = true;
				$scope.iconpath = 'pages/mall_manage/class_manage/offsale_edit/images/default.png';
			}else{
				$scope.show_picwarning = false;
				$scope.iconpath = response.data.data.file_path;
				$scope.classicon = response.data.data.file_path;
			}
		},function (error) {
			console.log(error)
		})
	}

	/*保存编辑*/
	$scope.saveclass = function () {
        if (!pattern.test($scope.item.title)||$scope.item.title=='') {
            $scope.class_warning = "您的输入不满足条件,请重新输入"
            $scope.show_class_warning = true;
            return;
        }

		if($scope.iconpath == 'pages/mall_manage/class_manage/offsale_edit/images/default.png'){
            $scope.show_picwarning = true;
            return;
		}

		if($scope.show_class_warning==false&&$scope.show_picwarning==false){
            let description = UE.getEditor('editor').getContent();
			if($scope.finalpatharr.length==3){
				pid = $scope.secselect;
			}else if($scope.finalpatharr.length==2){
				pid = $scope.firstselect;
			}else{
				pid = 0;
			}

			let data =  {id:+$scope.item.id,title:$scope.item.title,pid:+pid,icon:$scope.classicon||$stateParams.item.icon,description:description,offline_reason:$scope.item.offline_reason};
			 _ajax.post('/mall/category-edit',data,function (res) {
                 if(res.code==200){
                     $scope.save_msg="保存成功"
                     $scope.success_flag = true;
                 }else if(res.code==1006){
                     $scope.save_msg = res.data.msg;
                     $scope.success_flag = false;
                 }
                 $("#save_tishi").modal("show");
             })
		}
	}

    //*保存模态框确认*/
    $scope.suresave = function () {
        setTimeout(function () {
            $state.go("fenleiguanli",{offsale_flag:true});
        },200)
    }
})

