let shop_style_let = angular.module("shop_style", ['ngFileUpload']);
shop_style_let.controller("shop_style_ctrl", function ($rootScope, $scope, $http, $stateParams, $state, Upload, $location, $anchorScroll, $window, _ajax) {
	$rootScope.crumbs = [{
		name: '商品管理',
		icon: 'icon-shangpinguanli',
		link: 'commodity_manage'
	}, {
		name: '添加新商品'
	}];
	$scope.upload_txt='上传';
	$scope.upload_dis=false;
	$scope.price_flag = false;//价格flag
	$scope.myng = $scope;
	$scope.style_check_arr = [] // 风格id 数组
	$scope.attrs_check_arr = [] // 属性内容 数组
	$scope.style_check_model = ''
	$scope.attrs_check_model = ''
	$scope.logistics = [];//物流模块列表
	$scope.goods_all_attrs = [];//所有属性数据
	$scope.shop_logistics = [];//物流模板默认第一项
	$scope.own_attrs_arr = [];//自己添加的属性数组
	$scope.offline_style_ids = [] //下架的风格ids
	$scope.attr_blur_flag = true
	$scope.own_submitted = true
	$scope.success_modal_flag = false // 添加成功，跳转列表页
	$scope.error_modal_flag = false   // 部分系列、风格关闭
	$scope.default_modal_flag = false // 关闭模态框，停留在当前页
	$scope.category_id = $stateParams.category_id;//三级分类的id
	$scope.first_category_title = $stateParams.first_category_title;//一级分类名称
	$scope.second_category_title = $stateParams.second_category_title;//二级分类名称
	$scope.third_category_title = $stateParams.third_category_title;//三级分类名称
	let reg = /^\d+(\.\d{1,2})?$/; // 数字或数字最多保留两位小数
	let reg_number = /^[0-9]{0,}$/; // 只能输入数字
	let pattern = /^[\u4E00-\u9FA5A-Za-z0-9\,\，\s]+$/;//只能输入中文、数字、字母、中英文逗号、空格
	$scope.config = $rootScope.config;//富文本编辑器配置

	/*-------------------限制特殊字符----------------------------*/
	$scope.g_name_change = function (value) {
		let reg_value = pattern.test(value)
		reg_value ? $scope.g_flag = false : $scope.g_flag = true;
	}
	$scope.d_name_change = function (value) {
		let reg_value = pattern.test(value)
		reg_value ? $scope.d_flag = false : $scope.d_flag = true;
	}
	/*-----------------品牌、系列、风格 获取-----------------*/
	$scope.brands_arr = [];
	$scope.series_arr = [];
	$scope.styles_arr = [];
	/*品牌、系列、风格 下拉框*/
	_ajax.get('/mall/category-brands-styles-series', {
		category_id: +$scope.category_id,
		from_add_goods_page:1
	}, function (res) {
		console.log(res);
		//初始化下拉框的第一项
		$scope.brands_arr = res.data.category_brands_styles_series.brands;
		if ($scope.brands_arr.length > 0) {
			$scope.brand_model = res.data.category_brands_styles_series.brands[0].id;
		}
		$scope.series_arr = res.data.category_brands_styles_series.series;
		if ($scope.series_arr.length > 0) {
			$scope.series_model = res.data.category_brands_styles_series.series[0].id;
		}
		$scope.styles_arr = res.data.category_brands_styles_series.styles;
	})
	/*品牌、系列、风格 下拉框结束*/

	//风格复选框
	$scope.style_change = function (status,item) {
		if(status === true){
			$scope.style_check_arr.push(item.id)
		} else {
			style_check_del(item.id)
		}
	}
	function style_check_del(num) {
		let del_index = $scope.style_check_arr.findIndex(function (value, index, arr) {
			return value == num;
		});
		if (del_index != -1) {
			$scope.style_check_arr.splice(del_index, 1);
		}
	}

	/*---------------属性获取-----------------*/
	$scope.goods_input_attrs = [];//普通文本框
	$scope.goods_select_attrs = [];//下拉框
	$scope.goods_check_attrs = []; // 复选框
	$scope.goods_select_attrs_value = [];//属性下拉框的值
	$scope.pass_attrs_name = [];//接口传的属性名称
	$scope.pass_attrs_value = [];//接口传的属性值
	//大后台添加的属性
	_ajax.get('/mall/category-attrs', {category_id: +$scope.category_id}, function (res) {
		$scope.goods_all_attrs = res.data.category_attrs;
		//循环所有获取到的属性值，判断是普通文本框、下拉框、复选框
		for (let [key, value] of $scope.goods_all_attrs.entries()) {
			if (value.addition_type == 0) {   // 下拉框
				$scope.goods_input_attrs.push(value);
			} else if (value.addition_type == 1) {
				$scope.goods_select_attrs.push(value);
			}else if (value.addition_type == 2) {
				$scope.goods_check_attrs.push(value);
			}
		}
		//循环下拉框的值
		for (let [key, value] of $scope.goods_select_attrs.entries()) {
			$scope.goods_select_attrs_value.push(value.value);//下拉框的值
			value.value = value.value[0]
		}
		//循环属性复选框
		for (let [key, value] of $scope.goods_check_attrs.entries()) {
			value.attrs_value=[]
		}
	})
	// 属性复选框
	$scope.attrs_check_change = function (status,item,items) {
		if(status === true){
			item.attrs_value.push(items)
		} else {
			attrs_check_del(item,items)
		}
	}
	function attrs_check_del(item,items) {
		let del_index = item.attrs_value.findIndex(function (value, index, arr) {
			return value == items;
		});
		if (del_index != -1) {
			item.attrs_value.splice(del_index, 1);
		}
	}
	//判断属性内容是否符合规则（数字或数字后保留两位小数）
	$scope.testNumber=function (item) {
		let reg_value = reg.test(item.value);
		reg_value ? item.status = false : item.status = true
	};
	//库存 只能输入正整数
	$scope.onlyNumbers=function (left_number) {
		let reg_status = reg_number.test(left_number)
		reg_status ? $scope.left_number_flag = false :$scope.left_number_flag = true
	}
	/*----------------自己添加的属性--------------------*/

	$scope.own_all_attrs=[];//大后台属性和自己添加的属性 数组
	//添加属性
	$scope.i = 1;
	$scope.add_own_attrs = function () {
		$scope.own_attrs_arr.push({name: '', value: '', name_model: 'attrs' + $scope.i, value_model: 'value' + $scope.i});
		$scope.i++;
	};
	//删除属性
	$scope.del_own_attrs = function (index) {
		$scope.own_attrs_arr.splice(index, 1);
	};
	//自己增加的属性的change事件
	$scope.own_input_change = function () {
		let arr=[];
		arr=$scope.goods_all_attrs.concat($scope.own_attrs_arr);
		for(let [key,value] of arr.entries()){
			let num=angular.copy(arr).filter(function (item) {
				return item.name==value.name
			});
			if(num.length!=1){
				value.own_status = true
			}else{
				value.own_status = false
			}
		}
	}
	/*----------------上传封面图-----------------------*/
	$scope.data = {
		file: null
	};
	$scope.upload_cover = function (file) {
		if (!$scope.data.file) {
			return
		}
		$scope.upload_dis=true;
		$scope.upload_txt='上传中...';
		Upload.upload({
			url: '/site/upload',
			data: {'UploadForm[file]': file}
		}).then(function (response) {
			console.log(response);
			if (!response.data.data) {
				$scope.cover_flag = "上传图片格式不正确，请重新上传"
			} else {
				$scope.cover_flag = '';
				$scope.upload_cover_src = response.data.data.file_path;
			}
			$scope.upload_dis=false;
			$scope.upload_txt='上传';
		}, function (error) {
			console.log(error);
			$scope.upload_cover_src = '';
		})
	};

	/*------------------------上传多张图片--------------------------*/
	//上传图片
	$scope.upload_img_arr = []; //图片数组
	$scope.data = {
		file: null
	};
	$scope.completeUpload = true;
	$scope.upload = function (file) {
		if (!$scope.data.file) {
			return
		}
		$scope.completeUpload = false;
		$scope.upload_img_arr.push(loadingPicUri);
		Upload.upload({
			url: '/site/upload',
			data: {'UploadForm[file]': file}
		}).then(function (response) {
			$scope.upload_img_arr.pop();
			if (!response.data.data) {
				$scope.img_flag = "上传图片格式不正确，请重新上传"
			} else {
				$scope.img_flag = '';
				$scope.upload_img_arr.push(response.data.data.file_path);
			}
			$scope.completeUpload = true;
		}, function (error) {
			console.log(error)
			$scope.upload_img_arr.pop();
		})
	};
	//删除图片
	$scope.del_img = function (item) {
		_ajax.post('/site/upload-delete', {file_path: item}, function (res) {
			$scope.upload_img_arr.splice($scope.upload_img_arr.indexOf(item), 1);
		})
	};
	//售后、保障
	$scope.after_sale_services = [];//售后、保障传值数组
	$scope.invoice_check = true;

	//市场价
	$scope.my_market_price = function (value) {
		let reg_value = reg.test(value);
		if (!reg_value) {
			$scope.price_flag = true;
		} else {
			(+$scope.market_price >= +$scope.platform_price) && (+$scope.platform_price >= +$scope.supplier_price) ? $scope.price_flag = false : $scope.price_flag = true;
		}
	};
	//平台价
	$scope.my_platform_price = function (value) {
		let reg_value = reg.test(value);
		if (!reg_value) {
			$scope.price_flag = true;
		} else {
			(+$scope.market_price >= +$scope.platform_price) && (+$scope.platform_price >= +$scope.supplier_price) ? $scope.price_flag = false : $scope.price_flag = true;
		}
	};
	//供货商价
	$scope.my_supplier_price = function (value) {
		let reg_value = reg.test(value);
		console.log(value)
		console.log(reg_value)
		if (!reg_value) {
			$scope.price_flag = true;
		} else {
			(+$scope.market_price >= +$scope.platform_price) && (+$scope.platform_price >= +$scope.supplier_price) ? $scope.price_flag = false : $scope.price_flag = true;
		}
	};
	/*---------------物流模板--------------------*/
	_ajax.post('/mall/logistics-templates-supplier', {}, function (res) {
		if (res.data.logistics_templates_supplier.length > 0) {
			$scope.logistics_flag1 = true;
			$scope.logistics = res.data.logistics_templates_supplier;
			$scope.shop_logistics = res.data.logistics_templates_supplier[0].id;
			//物流模块详情
			$scope.$watch('shop_logistics', function (newVal, oldVal) {
				_ajax.get('/mall/logistics-template-view', {id: +newVal}, function (res) {
					$scope.logistics_method = res.data.logistics_template.delivery_method;//快递方式
					$scope.district_names = res.data.logistics_template.district_names;//地区
					$scope.delivery_cost_default = res.data.logistics_template.delivery_cost_default;//默认运费
					$scope.delivery_number_default = res.data.logistics_template.delivery_number_default;//默认运费的数量
					$scope.delivery_cost_delta = res.data.logistics_template.delivery_cost_delta;//增加件费用
					$scope.delivery_number_delta = res.data.logistics_template.delivery_number_delta;//增加件的数量
				})
			});
		} else {
			$scope.logistics_flag2 = true;
		}
	})
	/*-----------------添加按钮-----------------------*/
	$scope.add_goods_confirm = function (valid, error) {
		$scope.after_sale_services = [];
		//提供发票
		if ($scope.invoice_check) {
			$scope.after_sale_services.push(0);
		}
		//上门安装
		if ($scope.door_instal_model) {
			$scope.after_sale_services.push(1);
		} else if ($scope.door_instal_model === false) {
			del_correspond(1);
		}
		//上门维修
		if ($scope.door_service_check) {
			$scope.after_sale_services.push(2);
		} else if ($scope.door_service_check === false) {
			del_correspond(2);
		}
		//上门退货
		if ($scope.door_return_check) {
			$scope.after_sale_services.push(3);
		} else if ($scope.door_return_check === false) {
			del_correspond(3);
		}
		//上门换货
		if ($scope.door_replacement_check) {
			$scope.after_sale_services.push(4);
		} else if ($scope.door_replacement_check === false) {
			del_correspond(4);
		}
		//退货
		if ($scope.return_check) {
			$scope.after_sale_services.push(5);
		} else if ($scope.return_check === false) {
			del_correspond(5);
		}
		//换货
		if ($scope.replacement_check) {
			$scope.after_sale_services.push(6);
		} else if ($scope.replacement_check === false) {
			del_correspond(6);
		}

		//不勾选的状态删除对应项
		function del_correspond(num) {
			let del_index = $scope.after_sale_services.findIndex(function (value, index, arr) {
				return value == num;
			});
			if (del_index != -1) {
				$scope.after_sale_services.splice(del_index, 1);
			}
		}
		// 判断默认属性输入规则是否符合标准
		for (let [key,value] of $scope.goods_input_attrs.entries()) {
			if (value.status === true) {
				$scope.attr_blur_flag = false
				break
			}else{
				$scope.attr_blur_flag = true
			}
		}
		// 判断有无重复属性名
		let arr=[];
		arr=$scope.goods_all_attrs.concat($scope.own_attrs_arr);
		for(let [key,value] of arr.entries()){
			let num=angular.copy(arr).filter(function (item) {
				return item.name==value.name
			});
			if(num.length!=1){
				value.own_status = true
			}else{
				value.own_status = false
			}
			if(value.own_status === true){
				$scope.own_submitted = false
				break
			}else{
				$scope.own_submitted = true
			}
		}
		// 判断属性复选框
		for (let [key, value] of $scope.goods_check_attrs.entries()) {
			if(value.attrs_value.length>0){
				$scope.attrs_check_flag=true
			}else{
				$scope.attrs_check_flag=false
				break
			}
		}
		// 没有 属性复选框，传true
		if($scope.goods_check_attrs.length === 0){
			$scope.attrs_check_flag=true
		}
		// 判断 input框不能为空、封面图、物流模板、无重复属性名、价格、库存、有品牌、属性输入规则符合标准、属性复选框
		if (valid && $scope.upload_cover_src && $scope.logistics_flag1 && $scope.own_submitted && !$scope.price_flag && !$scope.left_number_flag && $scope.brands_arr.length > 0 && $scope.attr_blur_flag && $scope.attrs_check_flag) {
			$scope.description = UE.getEditor('editor').getContent();//富文本编辑器
			$scope.pass_attrs_name = []
			$scope.pass_attrs_value = []
			/* ---------------属性传值---------------- */
			// 自己添加的属性
			for (let [key, value] of $scope.own_attrs_arr.entries()) {
				$scope.pass_attrs_name.push(value.name);//属性名
				$scope.pass_attrs_value.push(value.value);//属性值
			}
			// 文本框
			if ($scope.goods_input_attrs[0] != undefined) {
				for (let [key, value] of $scope.goods_input_attrs.entries()) {
					$scope.pass_attrs_name.push(value.name)
					$scope.pass_attrs_value.push(value.value);
				}
			}
			// 下拉框
			if ($scope.goods_select_attrs[0] != undefined) {
				for (let [key, value] of $scope.goods_select_attrs.entries()) {
					$scope.pass_attrs_name.push(value.name);
					$scope.pass_attrs_value.push(value.value);
				}
			}
			// 复选框
			if ($scope.goods_check_attrs[0] != undefined) {
				for (let [key, value] of $scope.goods_check_attrs.entries()) {
					$scope.pass_attrs_name.push(value.name)
					$scope.pass_attrs_value.push(value.attrs_value.join(','));
				}
			}
			console.log($scope.pass_attrs_name);
			console.log($scope.pass_attrs_value);
			/*判断风格和系列是否存在，如果不存在，值传0*/
			$scope.series_model == undefined ? $scope.series_model = 0 : $scope.series_model = $scope.series_model;
			$scope.style_check_arr[0] == undefined ? $scope.style_check_arr = [] : $scope.style_check_arr = $scope.style_check_arr
			/*如果没有属性，则传空数组*/
			if ($scope.pass_attrs_name[0] == undefined) {
				$scope.pass_attrs_name = [];
			}
			if ($scope.pass_attrs_value[0] == undefined) {
				$scope.pass_attrs_value = [];
			}
			_ajax.post('/mall/goods-add', {
				category_id: +$scope.category_id,      //三级分类id
				title: $scope.goods_name,              //名称
				subtitle: $scope.des_name,             //特色
				brand_id: $scope.brand_model,      //品牌
				style_id: $scope.style_check_arr.join(','),      //风格
				series_id: $scope.series_model,    //系列
				'names[]': $scope.pass_attrs_name,   // 属性名称
				'values[]': $scope.pass_attrs_value, //属性值
				cover_image: $scope.upload_cover_src,//封面图
				'images[]': $scope.upload_img_arr,   //图片
				supplier_price: +$scope.supplier_price * 100,//供货价
				platform_price: +$scope.platform_price * 100,//平台价
				market_price: +$scope.market_price * 100,//市场价
				left_number: +$scope.left_number,//库存
				logistics_template_id: +$scope.shop_logistics,//物流模板
				after_sale_services: $scope.after_sale_services.join(','),//售后、保障
				description: $scope.description//描述
			}, function (res) {
				console.log(res);
				if (res.code === 200) {
					// 正常添加
					$('#on_shelves_add_success').modal('show')
					$scope.success_modal_flag = true
					$scope.error_modal_flag = false
					$scope.default_modal_flag = false
					$scope.add_moal_txt = '添加成功'
				} else if (res.code === 1045 || res.code === 1047) {  // 所选系列或风格，全部关闭
					$('#on_shelves_add_success').modal('show')
					$scope.default_modal_flag = true
					$scope.success_modal_flag = false
					$scope.error_modal_flag = false
					$scope.add_moal_txt = res.msg
				}else if(res.code === 1046){                        // 部分系列或风格关闭
					$('#on_shelves_add_success').modal('show')
					$scope.error_modal_flag = true
					$scope.default_modal_flag = false
					$scope.success_modal_flag = false
					$scope.add_moal_txt = res.msg
					$scope.offline_style_ids=res.data.offline_style_ids
				}
			})
		} else {
			$scope.submitted = true;
		}
		//判断封面图是否上传
		if (!$scope.upload_cover_src) {
			$scope.cover_flag = '请上传图片'
		}
		//名称输入框为空， 文本框变红，并跳转到对于的位置
		if (!valid) {
			$scope.submitted = true;
			for (let [key, value] of error.entries()) {
				if (value.$invalid) {
					$anchorScroll.yOffset = 150;
					$location.hash(value.$name);
					$anchorScroll();
					$window.document.getElementById(value.$name).focus();
					break
				}
			}
		}
	};
	// 部分系列或风格关闭 模态框确认按钮
	$scope.someModalBtn =function () {
		for (let [key,value2] of $scope.offline_style_ids.entries()){
			let index = $scope.style_check_arr.findIndex((value) => {
				return value == value2;
			})
			$scope.style_check_arr.splice(index,1)
		}
		_ajax.post('/mall/goods-add',{
			category_id: +$scope.category_id,      //三级分类id
			title: $scope.goods_name,              //名称
			subtitle: $scope.des_name,             //特色
			brand_id: $scope.brand_model,      //品牌
			style_id: $scope.style_check_arr.join(','),      //风格
			series_id: $scope.series_model,    //系列
			'names[]': $scope.pass_attrs_name,   // 属性名称
			'values[]': $scope.pass_attrs_value, //属性值
			cover_image: $scope.upload_cover_src,//封面图
			'images[]': $scope.upload_img_arr,   //图片
			supplier_price: +$scope.supplier_price * 100,//供货价
			platform_price: +$scope.platform_price * 100,//平台价
			market_price: +$scope.market_price * 100,//市场价
			left_number: +$scope.left_number,//库存
			logistics_template_id: +$scope.shop_logistics,//物流模板
			after_sale_services: $scope.after_sale_services.join(','),//售后、保障
			description: $scope.description//描述
		},function (res) {
			$('#on_shelves_add_success').modal('hide')
			setTimeout(function () {
				$state.go('commodity_manage');
			}, 300)
		})
	}
	//添加成功模态框确认按钮
	$scope.on_shelves_add_success = function () {
		setTimeout(function () {
			$state.go('commodity_manage');
		}, 300)
	}
});