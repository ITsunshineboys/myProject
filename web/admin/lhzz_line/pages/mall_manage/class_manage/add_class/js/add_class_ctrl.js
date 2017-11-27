/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*已下架 添加分类*/
var add_class = angular.module("addclassModule",['ngFileUpload']);
add_class.controller("addClass",function ($scope, $http,Upload,$state,$rootScope,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '分类管理',
        link: 'class.offline',
    }, {
        name: '添加分类'
    }];

    let pattern = /^[\u4E00-\u9FA5A-Za-z0-9]+$/;
    let pid;
    $scope.showsub = false; /*初始无二级下拉选项*/
	$scope.showtishi = false;
	$scope.idarr = [];
	$scope.picwarning = false;
	$scope.iconpath = 'pages/mall_manage/class_manage/add_class/images/default.png';
	$scope.picwarningtext = '';
	$scope.classicon = '';



	/*富文本编辑器初始化*/
    $scope.config = $rootScope.config;

	/*分类名称是否存在的判断*/
	$scope.addClassName = function () {
        if (!pattern.test($scope.class_name)||!$scope.class_name) {
			$scope.tishi = "您的输入不满足条件,请重新输入"
			$scope.showtishi = true;
		}else{
            $scope.showtishi = false;
		}
	}

	/*分类所属 第一个下拉框的值*/
	$scope.findParentClass =  (function () {
		_ajax.get('/mall/categories-manage-admin',{},function (res) {
            $scope.firstclass = res.data.data.categories.splice(1);
        })
	})()

	/*一级选择后的二级*/
	$scope.subClass = function (obj) {
		if (obj != '') {
			$scope.showsub = true;
			_ajax.get('/mall/categories-manage-admin',{pid: obj},function (res) {
                $scope.secondclass = response.data.data.categories.splice(1);
                $scope.secselect = '0';
            })
		}else{
			$scope.showsub = false;
		}
	}

	//上传图片
	$scope.data = {
		file:null
	}
	$scope.upload = function (file) {
        $scope.picwarning = false;
		if(!$scope.data.file){
			return
		}
		Upload.upload({
			url:baseUrl+'/site/upload',
			data:{'UploadForm[file]':file}
		}).then(function (response) {
			if(!response.data.data){
				$scope.picwarningtext = '上传图片格式不正确或尺寸不匹配，请重新上传';
                $scope.picwarning = true;
				$scope.iconpath = 'pages/mall_manage/class_manage/add_class/images/default.png';
			}else{
				$scope.picwarning = false;
				$scope.iconpath = response.data.data.file_path;
				$scope.classicon = response.data.data.file_path;
			}
		},function (error) {
			console.log(error)
		})
	}

	/*确认添加分类*/
	$scope.sureaddclass = function () {
        if (!pattern.test($scope.class_name)||!$scope.class_name) {
            $scope.tishi = "您的输入不满足条件,请重新输入"
            $scope.showtishi = true;
            return;
        }


        if($scope.iconpath =='pages/mall_manage/class_manage/add_class/images/default.png'){
			$scope.picwarningtext = '请上传图片';
			$scope.picwarning = true;
			return;
		}

		if($scope.showtishi==false&&$scope.picwarning==false&&$scope.classicon!=''){
            let description = UE.getEditor('editor').getContent();
			if(!$scope.firstselect){
				pid = 0;
			}else if($scope.firstselect!=0&&$scope.secselect==0){
				pid = $scope.firstselect;
			}else if($scope.firstselect!=0&&$scope.secselect!=0){
				pid = $scope.secselect;
			}

			let data =  {title:$scope.class_name,pid:pid,icon:$scope.classicon,description:description};
			_ajax.post('/mall/category-add',data,function (res) {
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

	//*保存模态框确认*/
	$scope.suresave = function () {
		setTimeout(function () {
			$state.go("class.offline");
		},200)
	}


})

