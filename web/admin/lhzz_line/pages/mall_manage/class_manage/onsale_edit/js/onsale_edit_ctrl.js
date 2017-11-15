/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*已上架 编辑*/
var onsale_edit = angular.module("onsaleeditModule", ['ngFileUpload']);
onsale_edit.controller("onsaleEdit", function ($scope, $state, $stateParams,$http,Upload) {
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
	$scope.showtishi = false;
	$scope.idarr = [];
	$scope.firstclass = []; /*一级下拉框*/
	$scope.subClass = [];/*二级下拉框*/
	$scope.offsaleclasslevel = $stateParams.classlevel;
	$scope.offsaleclassid = $stateParams.classid;
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
                'rowspacingtop','rowspacingbottom','imagecenter','simpleupload', 'time', 'date', 'preview']
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

	/*分类名称是否存在的判断*/
	$scope.addClassName = function () {
		if ((!pattern.test($scope.class_name))||$scope.class_name=='') {
			$scope.tishi = "您的输入不满足条件,请重新输入"
			$scope.showtishi = true;
		}else{
            $scope.showtishi = false;
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
		let data =  {id:$scope.offsaleclassid,offline_reason:$scope.offlinereason};
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
		// console.log($scope.classicon)
        if (!pattern.test($scope.class_name)||$scope.class_name=='') {
            $scope.tishi = "您的输入不满足条件,请重新输入"
            $scope.showtishi = true;
            return;
        }


        if($scope.iconpath == 'pages/mall_manage/class_manage/offsale_edit/images/default.png'){
            $scope.picwarning = true;
            return;
        }

		if($scope.showtishi==false&&$scope.picwarning==false){
			if($scope.finalpatharr.length==3){
				pid = $scope.secselect
			}else if($scope.finalpatharr.length==2){
				pid = $scope.firstselect;
			}else{
				pid = 0;
			}
			let url = baseUrl+"/mall/category-edit";
			let data =  {id:$scope.offsaleclassid,title:$scope.class_name,pid:pid,icon:$scope.classicon||$stateParams.iconpath,description:$scope.onlinedes};
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
			// $scope.savemodal = '#save_tishi'
			// $scope.savesuccess = true;
		}
	}

	/*保存模态框确认*/
	$scope.suresave = function () {
		setTimeout(function () {
			$state.go("fenleiguanli");
		},200)
	}
})