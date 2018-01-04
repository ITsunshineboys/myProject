angular.module("edit_brand_module",[])
	.controller("edit_brand_ctrl",function ($rootScope,$scope,$http,_ajax,$stateParams,$state,Upload,$location,$anchorScroll,$window) {
		$rootScope.crumbs = [{
			name: '品牌管理',
			icon: 'icon-shangchengguanli',
			link: 'brand_index'
		},{
			name: '编辑品牌'
		}];
		$scope.trademark_txt='上传';
		$scope.logo_txt='上传'
		$scope.upload_dis=false;
		$scope.brand_id=$stateParams.brand_id;//传过来的品牌id
		$scope.categories=[];
		$scope.item_check = [];
		$scope.three=[];
		$scope.ids_arr=[]; // 三级分类
		$scope.edit_title_red = false
		_ajax.get("/supplieraccount/supplier-brand-view",{
			brand_id:$scope.brand_id
		},function (res) {
			console.log(res);
			$scope.brand_on_name_model=res.data.name;//名称
			$scope.upload_img_src=res.data.certificate;//商标注册码
			$scope.upload_logo_src=res.data.logo;//logo
			$scope.categories=res.data.categories;//三级分类
			//默认进页面获取三级分类所具有的系类
			for(let[key,value] of $scope.categories.entries()){
				$scope.item_check.push(value);
				console.log($scope.item_check);
			}
			//获取三级
			_ajax.get('/mall/categories',{pid:2},function (res) {
				$scope.three = res.data.categories;
				for(let [key,value] of $scope.three.entries()){
					console.log(value)
					if($scope.item_check.length == 0){
						value.complete = false
					}else{
						for(let [key1,value1] of $scope.item_check.entries()){
							if(value.id == value1.id){
								value.complete = true
							}
						}
					}
				}
			});
		});
		/*===========================上传图片开始===============================*/
		//上传商标注册证
		$scope.data = {
			file:null
		};
		$scope.upload = function (file) {
			if(!$scope.data.file){
				return
			}
			$scope.trademark_txt='上传...';
			$scope.upload_dis=true;
			console.log($scope.data);
			Upload.upload({
				url:'/site/upload',
				data:{'UploadForm[file]':file}
			}).then(function (response) {
				console.log(response);
				if(!response.data.data){
					$scope.img_flag="上传图片格式不正确，请重新上传"
				}else{
					$scope.img_flag='';
					$scope.upload_img_src=response.data.data.file_path;
				}
				$scope.trademark_txt='上传';
				$scope.upload_dis=false;
			},function (error) {
				console.log(error)
			})
		};
		$scope.logo_data = {
			file:null
		};
		//上传品牌LOGO
		$scope.upload_logo = function (file) {
			if(!$scope.logo_data.file){
				return
			}
			$scope.logo_txt='上传...';
			$scope.upload_dis=true;
			console.log($scope.data);
			Upload.upload({
				url:'/site/upload',
				data:{'UploadForm[file]':file}
			}).then(function (response) {
				console.log(response);
				if(!response.data.data){
					$scope.img_logo_flag="上传图片格式不正确，请重新上传"
				}else{
					$scope.img_logo_flag='';
					$scope.upload_logo_src=response.data.data.file_path;
				}
				$scope.logo_txt='上传';
				$scope.upload_dis=false;
			},function (error) {
				console.log(error)
			})
		};
		/*===========================上传图片结束==============================*/
		//获取一级
		_ajax.get('/mall/categories',{},function (res) {
			$scope.details = res.data.categories;
			$scope.oneColor = $scope.details[0];
		})
		//获取二级
		_ajax.get('/mall/categories',{pid:1},function (res) {
			$scope.second = res.data.categories;
			$scope.twoColor= $scope.second[0];
		})

		//点击一级 获取相对应的二级
		$scope.getMore = function (n) {
			$scope.oneColor = n;
			_ajax.get('/mall/categories',{pid:n.id},function (res) {
				$scope.second = res.data.categories;
				$scope.twoColor = $scope.second[0];
				_ajax.get('/mall/categories',{pid:+ $scope.second[0].id},function (res) {
					$scope.three = res.data.categories;
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
				})
			})
		};
		//点击二级 获取相对应的三级
		$scope.getMoreThree = function (n) {
			$scope.id=n;
			$scope.twoColor = n;
			_ajax.get('/mall/categories',{pid:n.id},function (res) {
				$scope.three = res.data.categories;
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
		};
		//添加拥有系列的三级
		$scope.check_item = function(item){
			$scope.add_three=0;
			for(let[key,value] of $scope.item_check.entries()){
				if(item.id==value.id){
					$scope.item_check.splice(key,1);
					$scope.add_three=1;
					break;
				}else{
					$scope.add_three=0
				}
				console.log($scope.add_three);
			}
			if($scope.add_three!=1){
				$scope.item_check.push(item);
			}
			//分类提示文字
			if($scope.item_check.length<1){
				$scope.sort_check='请至少选择一个分类';
			}else{
				$scope.sort_check='';
			}
		};
		//删除拥有系列的三级
		$scope.delete_item = function (item) {
			for(let[key,value] of $scope.three.entries()){
				console.log(value)
				if(item.id==value.id){
					value.complete=false;
				}
			}
			$scope.item_check.splice($scope.item_check.indexOf(item),1);
			//分类提示文字
			if($scope.item_check.length<1){
				$scope.sort_check='请至少选择一个分类';
			}else{
				$scope.sort_check='';
			}
		};
		
		// 编辑确认
		$scope.editBtn=function () {
			$scope.ids_arr=[];
			for(let [key,value] of $scope.item_check.entries()){
				$scope.ids_arr.push($scope.item_check[key].id)
			}
			_ajax.post('/supplieraccount/supplier-brand-edit',{
				brand_id:$scope.brand_id,
				name:$scope.brand_on_name_model,
				certificate:$scope.upload_img_src,
				logo:$scope.upload_logo_src,
				category_ids:$scope.ids_arr.join(',')
			},function (res) {
				if(res.code==200){
					$('#edit_modal').modal('show');
				}else{
					$scope.edit_title_red=true;
					$anchorScroll.yOffset = 150;
					$location.hash('brand_title');
					$anchorScroll();
					$window.document.getElementById('brand_title').focus();
				}
				console.log(res);
			})
		};
		//返回按钮
		$scope.back_upper=function () {
			history.go(-1);
		}
		//模态框确定
		$scope.back_index=function () {
			setTimeout(function () {
				$state.go('brand_index');
			},300)
		}
	});