var wait_online = angular.module("wait_online_Module",["ngFileUpload"]);
wait_online.controller("wait_online",function ($rootScope,$scope,$http,$stateParams,$state,Upload,$location,$anchorScroll,$window) {
    $scope.config=$rootScope.config;//富文本编辑器配置
    let reg=/^\d+(\.\d{1,2})?$/;
	$scope.goods_all_attrs=[];//所有属性数据
	$scope.logistics=[];//物流模块列表
    $rootScope.crumbs = [{
        name: '商品管理',
        icon: 'icon-shangpinguanli',
        link: 'commodity_manage'
    },{
        name: '商品详情',
    }];
	const config = {
		headers: {'Content-Type': 'application/x-www-form-urlencoded'},
		transformRequest: function (data) {
			return $.param(data)
		}
	};
    $scope.upload_txt='上传';
    $scope.upload_dis=false;
	$scope.myng=$scope;
	let goods_item=$stateParams.item;//点击对应的那条数据
	console.log(goods_item);
	console.log($stateParams.flag);
	if($stateParams.flag==0){
		$scope.show_flag=false;
	}else if($stateParams.flag==1){
		$scope.show_flag=true;
	}
	$scope.goods_id=goods_item.id;//
	$scope.category_title=goods_item.category_title;//三级分类
	$scope.goods_name=goods_item.title;//商品名称
	$scope.des_name=goods_item.subtitle; //商品特色
	$scope.upload_cover_src=goods_item.cover_image;//封面图
	$scope.upload_img_arr=goods_item.images;//多张图片
	$scope.supplier_price=goods_item.supplier_price;//供货价格
	$scope.platform_price=goods_item.platform_price;//平台价格
	$scope.market_price=goods_item.market_price;//市场价格
	$scope.left_number=goods_item.left_number;//库存
	$scope.purchase_price_decoration_company=goods_item.purchase_price_decoration_company;//装修公司采购价
	$scope.purchase_price_manager=goods_item.purchase_price_manager;//项目经理采购价
	$scope.purchase_price_designer=goods_item.purchase_price_designer;//设计师采购价
	$scope.logistics_template_id=goods_item.logistics_template_id;//物流id
	$scope.qr_code=goods_item.qr_code;//二维码
	$scope.detail_description=goods_item.description;//描述
	$scope.after_sale_services=goods_item.after_sale_services;//售后、保障
	$scope.operator=goods_item.operator;//操作人员
	$scope.offline_time=goods_item.offline_time;//下架时间
	$scope.reason=goods_item.reason;//下架时间


	for(let[key,value] of $scope.after_sale_services.entries()){
		if(value==1){
			$scope.door_instal_model=true;
		}
		if(value==2){
			$scope.door_service_check=true;
		}
		if(value==3){
			$scope.door_return_check=true;
		}
		if(value==4){
			$scope.door_replacement_check=true;
		}
		if(value==5){
			$scope.return_check=true;
		}
		if(value==6){
			$scope.replacement_check=true;
		}
	}

	/*-----------------品牌、系列、风格 获取-----------------*/
	$scope.brands_arr=[];
	$scope.series_arr=[];
	$scope.styles_arr=[];
	$http.get(baseUrl+'/mall/category-brands-styles-series',{
		params:{
			category_id:+goods_item.category_id
		}
	}).then(function (res) {
		/*品牌、系列、风格 下拉框开始*/
		$scope.brands_arr=res.data.data.category_brands_styles_series.brands;
		$scope.series_arr=res.data.data.category_brands_styles_series.series;
		$scope.styles_arr=res.data.data.category_brands_styles_series.styles;
		//商品详情接口，获取品牌、系列、风格名称、重置 第一项下拉框
		$http.get(baseUrl+'/mall/goods-view',{
			params:{
				id:$scope.goods_id
			}
		}).then(function (res) {
			$scope.detail_brand=res.data.data.goods_view.brand_name;//品牌名称
			$scope.detail_ser=res.data.data.goods_view.series_name;//系列名称
			$scope.detail_style=res.data.data.goods_view.style_name;//风格名称
			//循环品牌列表
			for(let [key,value] of $scope.brands_arr.entries()){
				if(value.name==$scope.detail_brand){
					$scope.brands_arr.splice(key,1);
					$scope.brands_arr.unshift(value);
					//把对应的品牌前置到下拉框第一项
					$scope.brand_model=value.id;
				}
			}
			//循环系列列表
			for(let [key,value] of $scope.series_arr.entries()){
				if(value.series==$scope.detail_ser){
					$scope.series_arr.splice(key,1);
					$scope.series_arr.unshift(value);
					//把对应的系列前置到下拉框第一项
					$scope.series_model=value.id;
				}
			}
			//循环风格列表
			for(let [key,value] of $scope.styles_arr.entries()){
				if(value.style==$scope.detail_style){
					$scope.styles_arr.splice(key,1);
					$scope.styles_arr.unshift(value);
					//把对应的风格前置到下拉框第一项
					$scope.style_model=value.id;
				}
			}
		},function (err) {
			console.log(err);
		});

	},function (err) {
		console.log(err);
	});
	/*==================品牌、系列、风格 下拉框结束===========================*/

	/*---------------------------------属性获取开始---------------------------------*/

	$scope.goods_input_attrs=[];//普通文本框
	$scope.goods_select_attrs=[];//下拉框
	$scope.goods_select_value=[];//下拉框的值
	$scope.pass_attrs_name=[];//名称
	$scope.pass_attrs_value=[];//值

	$http.get(baseUrl+'/mall/goods-attrs-admin',{
		params:{
			goods_id:+$scope.goods_id
		}
	}).then(function (res) {
		console.log(res);
		$scope.goods_all_attrs=res.data.data.goods_attrs_admin;
		// console.log('属性');
		// console.log($scope.goods_all_attrs);
		//循环所有获取到的属性值，判断是普通文本框还是下拉框
		for( let [key,value] of $scope.goods_all_attrs.entries()){
			if(value.addition_type==1){
				$scope.goods_select_attrs.push(value);
			}else{
				$scope.goods_input_attrs.push(value);
			}
		}

		//循环添加名称和值
		for(let [key,value] of $scope.goods_input_attrs.entries()){
			$scope.attr_name=value.name;
			$scope.attr_value=value.value;
		}

		//循环下拉框的value
		for(let [key,value] of $scope.goods_select_attrs.entries()){
			$scope.goods_select_name=value.name;//名称
			$scope.goods_select_value=value.value;//下拉框
			$scope.goods_select_model=$scope.goods_select_value[0];
		}
	},function (err) {
		console.log(err)
	});
	/*----------------自己添加的属性--------------------*/
	$scope.own_attrs_arr=[];//自定义数组
	//添加属性
	$scope.i=1;
	$scope.add_own_attrs=function () {
		$scope.own_attrs_arr.push({name:'',value:'',name_model:'attrs'+$scope.i,value_model:'value'+$scope.i});
		$scope.i++;
	};
	//删除属性
	$scope.del_own_attrs=function (index) {
		console.log(index);
		$scope.own_attrs_arr.splice(index,1);
	};
	/*---------------------------------属性获取结束---------------------------------*/
	/*----------------上传封面图-----------------------*/
	//$scope.upload_cover_src='';
    $scope.data = {
        file:null
    };
    $scope.upload_cover = function (file) {
        if(!$scope.data.file){
            return
        }
        $scope.upload_dis=true;
        $scope.upload_txt='上传中...';
        Upload.upload({
            url:baseUrl+'/site/upload',
            data:{'UploadForm[file]':file}
        }).then(function (response) {
            if(!response.data.data){
                $scope.cover_flag="上传图片格式不正确，请重新上传"
            }else{
                $scope.cover_flag='';
                $scope.upload_cover_src=response.data.data.file_path;
            }
            $scope.upload_dis=false;
            $scope.upload_txt='上传';
        },function (error) {
            console.log(error)
            $scope.upload_cover_src='';
        })
    };
	/*================封面图片结束=================*/
	/*------------------------上传多张图片--------------------------*/
	//上传图片
    $scope.data = {
        file:null
    };
    $scope.completeUpload = true;
    $scope.upload = function (file) {
        if(!$scope.data.file){
            return
        }
        console.log($scope.data);
        $scope.completeUpload = false;
        $scope.upload_img_arr.push(loadingPicUri);
        Upload.upload({
            url:baseUrl+'/site/upload',
            data:{'UploadForm[file]':file}
        }).then(function (response) {
            $scope.upload_img_arr.pop();
            if(!response.data.data){
                $scope.img_flag="上传图片格式不正确，请重新上传"
            }else{
                $scope.completeUpload = true;
                $scope.img_flag='';
                $scope.upload_img_arr.push(response.data.data.file_path)
            }
        },function (error) {
            console.log(error)
            $scope.upload_img_arr.pop();
        })
    };
	//删除图片
	$scope.del_img=function (item) {
		$http.post(baseUrl+'/site/upload-delete',{
			file_path:item
		},config).then(function (res) {
			console.log(res);
			$scope.upload_img_arr.splice($scope.upload_img_arr.indexOf(item),1);
		},function (err) {
			console.log(err);
		})
	};

	//售后、保障
	$scope.after_sale_services=[];//售后、保障传值数组
	$scope.invoice_check=true;

	//物流模块
	$http.post(baseUrl+'/mall/logistics-templates-supplier',{},config).then(function (res) {
		console.log('物流模板')
		console.log(res);
		$scope.logistics=res.data.data.logistics_templates_supplier;
		//把当前商品添加时的所属的物流模板 前置到第一
		for(let [key,value] of $scope.logistics.entries()){
			if(value.id==$scope.logistics_template_id){
				$scope.logistics.splice(key,1);
				$scope.logistics.unshift(value)
			}
		}
		$scope.shop_logistics=res.data.data.logistics_templates_supplier[0].id;
		$scope.$watch('shop_logistics',function (newVal,oldVal) {
			$http.get(baseUrl+'/mall/logistics-template-view',{
				params:{
					id:+newVal
				}
			}).then(function (res) {
				console.log('物流详情');
				console.log(res);
				$scope.logistics_method=res.data.data.logistics_template.delivery_method;//快递方式
				$scope.district_names=res.data.data.logistics_template.district_names;//地区名
				$scope.delivery_cost_default=res.data.data.logistics_template.delivery_cost_default;//默认运费
				$scope.delivery_number_default=res.data.data.logistics_template.delivery_number_default;//默认运费的数量
				$scope.delivery_cost_delta=res.data.data.logistics_template.delivery_cost_delta;//增加件费用
				$scope.delivery_number_delta=res.data.data.logistics_template.delivery_number_delta;//增加件的数量
				console.log($scope.district_names);
				if($scope.district_names.length > 3){
					$scope.viewTo = true
				}else{
					$scope.viewTo = false
				}
			},function (err) {
				console.log(err);
			});
		});
	},function (err) {
		console.log(err)
	});
    //市场价
    $scope.price_flag=false;
    $scope.my_market_price=function (value) {
        let reg_value=reg.test(value);
        if(!reg_value){
            $scope.price_flag=true;
        }else{
            (+$scope.market_price>=+$scope.platform_price)&&(+$scope.market_price>=+$scope.supplier_price)?$scope.price_flag=false:$scope.price_flag=true;
        }
    };
    //平台价
    $scope.my_platform_price=function (value) {
        let reg_value=reg.test(value);
        if(!reg_value){
            $scope.price_flag=true;
        }else{
            (+$scope.platform_price<=+$scope.market_price)&&(+$scope.platform_price>=+$scope.supplier_price)?$scope.price_flag=false:$scope.price_flag=true;
        }
    };
    //供货商价
    $scope.my_supplier_price=function (value) {
        let reg_value=reg.test(value);
        if(!reg_value){
            $scope.price_flag=true;
        }else{
            (+$scope.supplier_price<=+$scope.platform_price)&&(+$scope.supplier_price<=+$scope.market_price)?$scope.price_flag=false:$scope.price_flag=true;
        }

    };
	/*--------------编辑保存按钮----------------------*/
	$scope.edit_confirm=function (valid,error) {
        let description = UE.getEditor('editor').getContent();//富文本编辑器
		console.log($scope.upload_cover_src);
		console.log($scope.price_flag);
		if(valid && $scope.upload_cover_src && !$scope.price_flag){
			$scope.change_ok='#change_ok';//编辑成功
			$scope.after_sale_services=[];
			//提供发票
			if($scope.invoice_check){
				$scope.after_sale_services.push(0);
			}
			//上门安装
			if($scope.door_instal_model){
				$scope.after_sale_services.push(1);
			}else if($scope.door_instal_model===false){
				del_correspond(1);
			}
			//上门维修
			if($scope.door_service_check){
				$scope.after_sale_services.push(2);
			}else if($scope.door_service_check===false){
				del_correspond(2);
			}
			//上门退货
			if($scope.door_return_check){
				$scope.after_sale_services.push(3);
			}else if($scope.door_return_check===false){
				del_correspond(3);
			}
			//上门换货
			if($scope.door_replacement_check){
				$scope.after_sale_services.push(4);
			}else if($scope.door_replacement_check===false){
				del_correspond(4);
			}
			//退货
			if($scope.return_check){
				$scope.after_sale_services.push(5);
			}else if($scope.return_check===false){
				del_correspond(5);
			}
			//换货
			if($scope.replacement_check){
				$scope.after_sale_services.push(6);
			}else if($scope.replacement_check===false){
				del_correspond(6);
			}
			//不勾选的状态删除对象项
			function del_correspond(num){
				let del_index=$scope.after_sale_services.findIndex(function(value,index,arr) {
					return value==num ;
				});
				if(del_index!= -1){
					$scope.after_sale_services.splice(del_index,1);
				}
			}
			/*判断风格和系列是否存在，如果不存在，值传0*/
			$scope.series_model==undefined?$scope.series_model=0:$scope.series_model=parseInt($scope.series_model);
			$scope.style_model==undefined?$scope.style_model=0:$scope.style_model=parseInt($scope.style_model);
			/*循环自己添加的属性*/
			for(let[key,value] of $scope.own_attrs_arr.entries()){
				$scope.pass_attrs_name.push(value.name);//属性名
				$scope.pass_attrs_value.push(value.value);//属性值
			}
			/*如果没有属性，则传空数组*/
			if($scope.pass_attrs_name[0]==undefined){
				$scope.pass_attrs_name=[];
			}
			if($scope.pass_attrs_value[0]==undefined){
				$scope.pass_attrs_value=[];
			}
			/*判断是默认属性是 下拉框还是普通文本框*/
			if($scope.goods_input_attrs[0]!=undefined){
				for(let[key,value] of $scope.goods_input_attrs.entries()){
					$scope.pass_attrs_name.push(value.name);
					$scope.pass_attrs_value.push(value.value);
				}
			}
			if($scope.goods_select_attrs[0]!=undefined){
				$scope.pass_attrs_name.push($scope.goods_select_name);
				$scope.pass_attrs_value.push($scope.goods_select_model);
			}
			console.log($scope.pass_attrs_name);
			console.log($scope.pass_attrs_value);
			$http.post(baseUrl+'/mall/goods-edit',{
				id:+$scope.goods_id, //id
				title:$scope.goods_name,//名称
				subtitle:$scope.des_name,//特色
				brand_id:+$scope.brand_model,//品牌
				style_id:+$scope.style_model,//风格
				series_id:+$scope.series_model,//系列
				'names[]':$scope.pass_attrs_name,//属性名称
				'values[]':$scope.pass_attrs_value,//属性值
				cover_image:$scope.upload_cover_src,//封面图
				'images[]':$scope.upload_img_arr,//多张图片
				supplier_price:$scope.supplier_price*100,//供货价
				platform_price:$scope.platform_price*100,//平台价
				market_price:$scope.market_price*100,//市场价
				logistics_template_id:+$scope.shop_logistics,//物流模板id
				after_sale_services:$scope.after_sale_services.join(','),//售后服务
				left_number:+$scope.left_number,//库存
				description:description//描述
			},config).then(function (res) {
				console.log(res);
			},function (err) {
				console.log(err);
			})
		}else{
			$scope.submitted=true;
		}
		//名称输入框为空， 文本框变红，并跳转到对于的位置
		if(!valid){
			$scope.submitted = true;
			// if(value.$invalid=true){
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
	/*------------返回按钮----------------*/
	$scope.back_wait=function () {
		$state.go('commodity_manage',{wait_flag:true})
	};
	/*-----------------------保存成功跳转--------------------------------*/
	$scope.change_go=function () {
		setTimeout(function () {
			$state.go('commodity_manage',{wait_flag:true})
		},300)
	}
})