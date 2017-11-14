/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*已下架 添加分类*/
var add_class = angular.module("addclassModule",['ngFileUpload']);
add_class.controller("addClass",function ($scope, $http,Upload,$state) {
    const picprefix = baseUrl+"/";
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
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
		$http({
			method: "get",
			url: baseUrl+"/mall/categories-manage-admin",
		}).then(function (res) {
			$scope.firstclass = res.data.data.categories.splice(1);
		})
	})()

	/*一级选择后的二级*/
	$scope.subClass = function (obj) {
		if (obj != '') {
			$scope.showsub = true;
			$http({
				method: "get",
				url: baseUrl+"/mall/categories-manage-admin",
				params: {pid: obj}
			}).then(function (response) {
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
				$scope.iconpath = picprefix + response.data.data.file_path;
				$scope.classicon = response.data.data.file_path;
			}
		},function (error) {
			console.log(error)
		})
	}

	/*确认添加分类*/
	$scope.sureaddclass = function () {
        console.log($scope.iconpath);
        if (!pattern.test($scope.class_name)||!$scope.class_name) {
            $scope.tishi = "您的输入不满足条件,请重新输入"
            $scope.showtishi = true;
            return;
        }




        if($scope.iconpath =='pages/mall_manage/class_manage/add_class/images/default.png'){
			$scope.picwarningtext = '上传图片格式不正确或尺寸不匹配，请重新上传';
			$scope.picwarning = true;
			return;
		}

		if($scope.showtishi==false&&$scope.picwarning==false&&$scope.classicon!=''){
			if(!$scope.firstselect){
				pid = 0;
			}else if($scope.firstselect!=0&&$scope.secselect==0){
				pid = $scope.firstselect;
			}else if($scope.firstselect!=0&&$scope.secselect!=0){
				pid = $scope.secselect;
			}

			let url = baseUrl+"/mall/category-add";
			let data =  {title:$scope.class_name,pid:pid,icon:$scope.classicon,description:$scope.addclassdes};
			$http.post(url,data,config).then(function (res) {
                // console.log(res);
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
			$state.go("fenleiguanli",{offsale_flag:true});
		},200)
	}


})

