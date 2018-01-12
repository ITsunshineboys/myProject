let up_shelves_detail = angular.module("up_shelves_detail_module", ['ngFileUpload']);
up_shelves_detail.controller("up_shelves_detail_ctrl", function ($rootScope, $scope, $http, $stateParams, $state, Upload, $location, $anchorScroll, $window, _ajax) {
    /*------------返回按钮----------------*/
    $scope.back_cm = function () {
        if ($stateParams.flag == 0) {
            $state.go('commodity_manage', {on_flag: true})
        } else if ($stateParams.flag == 1) {
            $state.go('commodity_manage', {down_flag: true})
        }
    };
    $rootScope.crumbs = [{
        name: '商品管理',
        icon: 'icon-shangpinguanli',
        link: $scope.back_cm
    }, {
        name: '商品详情'
    }];
    $scope.upload_txt = '上传';
    $scope.upload_dis = false;
    $scope.goods_all_attrs = [];//所有属性数据
    $scope.logistics = [];//物流模块列表
    $scope.series_null_flag = false;
    $scope.style_null_flag = false;
    $scope.series_null_arr = [];
    $scope.style_null_arr = [];
		$scope.attr_blur_flag = true
		$scope.own_submitted = true
		$scope.offline_style_ids = [] //下架的风格ids
		$scope.success_modal_flag = false // 添加成功，跳转列表页
		$scope.error_modal_flag = false   // 部分系列、风格关闭
		$scope.default_modal_flag = false // 关闭模态框，停留在当前页
    let reg = /^\d+(\.\d{1,2})?$/;//小数点后两位
		let reg_number = /^[0-9]{0,}$/; // 只能输入数字
    let pattern = /^[\u4E00-\u9FA5A-Za-z0-9\,\，\s]+$/;//只能输入中文、数字、字母、中英文逗号、空格
    $scope.myng = $scope;
    let goods_item = $stateParams.item;//点击对应的那条数据
    console.log(goods_item);
    $scope.config = $rootScope.config;//富文本编辑器配置
    if ($stateParams.flag == 0) {
        $scope.show_flag = false;
    } else if ($stateParams.flag == 1) {
        $scope.show_flag = true;
    }
    $scope.goods_id = goods_item.id;//
    $scope.category_title = goods_item.category_title;//三级分类
    $scope.goods_name = goods_item.title;//商品名称
    $scope.des_name = goods_item.subtitle; //商品特色
    $scope.upload_cover_src = goods_item.cover_image;//封面图
    $scope.upload_img_arr = goods_item.images;//多张图片
    $scope.supplier_price = goods_item.supplier_price;//供货价格
    $scope.platform_price = goods_item.platform_price;//平台价格
    $scope.market_price = goods_item.market_price;//市场价格
    $scope.left_number = goods_item.left_number;//库存
    $scope.purchase_price_decoration_company = goods_item.purchase_price_decoration_company;//装修公司采购价
    $scope.purchase_price_manager = goods_item.purchase_price_manager;//项目经理采购价
    $scope.purchase_price_designer = goods_item.purchase_price_designer;//设计师采购价
    $scope.logistics_template_id = goods_item.logistics_template_id;//物流id
    $scope.qr_code = goods_item.qr_code;//二维码
    $scope.ueditor_value = goods_item.description;//描述
    $scope.after_sale_services = goods_item.after_sale_services;//售后、保障
    $scope.operator = goods_item.operator;//操作人员
    $scope.offline_time = goods_item.offline_time;//下架时间


    for (let [key, value] of $scope.after_sale_services.entries()) {
        if (value == 1) {
            $scope.door_instal_model = true;
        }
        if (value == 2) {
            $scope.door_service_check = true;
        }
        if (value == 3) {
            $scope.door_return_check = true;
        }
        if (value == 4) {
            $scope.door_replacement_check = true;
        }
        if (value == 5) {
            $scope.return_check = true;
        }
        if (value == 6) {
            $scope.replacement_check = true;
        }
    }
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
		$scope.style_ids = [];
		$scope.style_check_arr=[];
	_ajax.get('/mall/goods-view-admin', {id: $scope.goods_id}, function (res) {
		console.log(res);
		$scope.detail_brand = res.data.goods_view_admin.brand_name;//品牌名称
		$scope.detail_ser = res.data.goods_view_admin.series_name;//系列名称
		$scope.detail_style = res.data.goods_view_admin.style_name;//风格名称
		$scope.line_goods = res.data.goods_view_admin.line_goods;//线下店信息
		$scope.style_ids = res.data.goods_view_admin.style_ids // 风格ids
		_ajax.get('/mall/category-brands-styles-series', {
			category_id: +goods_item.category_id,
			from_add_goods_page:1
		}, function (res) {
			console.log(res);
			/*品牌、系列、风格 下拉框开始*/
			$scope.brands_arr = res.data.category_brands_styles_series.brands;
			$scope.series_arr = res.data.category_brands_styles_series.series;
			$scope.styles_arr = res.data.category_brands_styles_series.styles;
			//循环品牌列表
			for (let [key, value] of $scope.brands_arr.entries()) {
				if (value.name == $scope.detail_brand) {
					$scope.brands_arr.splice(key, 1);
					$scope.brands_arr.unshift(value);
					//把对应的品牌前置到下拉框第一项
					$scope.brand_model = value.id;
				}
			}
			//循环系列列表
			for (let [key, value] of $scope.series_arr.entries()) {
				$scope.series_null_arr.push(value.series);
				if (value.series == $scope.detail_ser) {
					$scope.series_arr.splice(key, 1);
					$scope.series_arr.unshift(value);
					//把对应的系列前置到下拉框第一项
					$scope.series_model = value.id;
				}
			}

			if($scope.detail_ser === ''){
				$scope.series_null_flag = true
				$scope.series_hint_words = '添加系列，便于智能报价抓取该商品'
			}
			if (!!$scope.detail_ser) {
				let series_null_flag = $scope.series_null_arr.findIndex(function (value) {
					return $scope.detail_ser == value
				});
				if (series_null_flag === -1) {
					$scope.series_down_flag = true;
					$scope.series_hint_words = '该商品原系列已下架'
				} else {
					$scope.series_down_flag = false;
				}
			}
			// 风格默认勾选
			for (let [key,value] of $scope.styles_arr.entries()) {
				let style_index = $scope.style_ids.findIndex(function (value1) {
					return value.id == value1
				})
				style_index ===-1 ? value.status = false : value.status = true
			}
			// 风格关闭 提示
			$scope.style_all_ids = []
			for (let [key,value] of $scope.styles_arr.entries()) {
				$scope.style_all_ids.push(value.id)
			}
			for(let [key,value] of $scope.style_ids.entries()){
				for(let [key1,value1] of $scope.style_all_ids.entries()){
					console.log(value.indexOf(value1))
				}
			}
			let style_value = [...new Set($scope.style_all_ids.concat($scope.style_ids))]
			if (style_value.length === $scope.style_all_ids.length ) {
				$scope.style_null_flag=false
			}else if (style_value.length > $scope.style_all_ids.length && style_value.length < $scope.style_all_ids.length + $scope.style_ids.length ){
				$scope.style_null_flag=true
				$scope.style_hint_words= '*该商品部分风格已下架'
			}else if (style_value.length === $scope.style_all_ids.length + $scope.style_ids.length){
				$scope.style_null_flag=true
				$scope.style_hint_words= '*该商品原风格已下架'
			}
		});
	})
    /*品牌、系列、风格 下拉框结束*/

    /*---------------------------------属性获取开始---------------------------------*/
    $scope.goods_input_attrs = [];//普通文本框
		$scope.merchant_add_attrs = [] ; // 商家自己添加的属性
    $scope.goods_select_attrs = [];//下拉框
		$scope.goods_check_attrs = []; // 复选框
    $scope.goods_select_value = [];//下拉框的值
    $scope.pass_attrs_name = [];//名称
    $scope.pass_attrs_value = [];//值
    $scope.goods_select_attrs_value = []
    /*大后台属性值获取*/
    _ajax.get('/mall/goods-attrs-admin', {goods_id: +$scope.goods_id}, function (res) {
        console.log(res);
        $scope.goods_all_attrs = res.data.goods_attrs_admin;
        //循环所有获取到的属性值，判断是普通文本框还是下拉框
        for (let [key, value] of $scope.goods_all_attrs.entries()) {
	        if (value.addition_type === 0 && value.from_type === 0) {   // 文本框
		        $scope.goods_input_attrs.push(value);
	        } else if (value.addition_type === 1) {                     // 下拉框
		        $scope.goods_select_attrs.push(value);
	        }else if(value.from_type === 1) {                         // 商家自己添加的属性
		        $scope.merchant_add_attrs.push(value)
	        }else if (value.addition_type ===2 && value.from_type === 0) {
		        $scope.goods_check_attrs.push(value);
	        }
        }
        //循环添加名称和值
        for (let [key, value] of $scope.goods_input_attrs.entries()) {
            $scope.attr_name = value.name;
            $scope.attr_value = value.value;
        }
        //循环下拉框的value
        for (let [key, value] of $scope.goods_select_attrs.entries()) {
            $scope.goods_select_attrs_value.push(value.value);//下拉框的值
        }
    });
    //判断属性是否为数字
    $scope.testNumber = function (item) {
		    let reg_value = reg.test(item.value);
		    reg_value ? item.status = false : item.status = true
    }
		//库存 只能输入正整数
		$scope.onlyNumbers=function (left_number) {
			let reg_status = reg_number.test(left_number)
			reg_status ? $scope.left_number_flag = false :$scope.left_number_flag = true
		}
		// 复选框 点击事件
		$scope.attr_check_click=function ($event,select,items) {
			if($event.target.localName =='input'){
				select.indexOf(items) === -1 ? select.push(items) : select.splice(select.indexOf(items),1)
			}
		}
    /*----------------自己添加的属性--------------------*/
    $scope.own_attrs_arr = [];//自定义数组
    //添加属性
    $scope.i = 1;
    $scope.add_own_attrs = function () {
      $scope.own_attrs_arr.push({name: '', value: '', name_model: 'attrs' + $scope.i, value_model: 'value' + $scope.i});
      $scope.i++;
    };
    //删除自己添加的属性
    $scope.del_own_attrs = function (index) {
        $scope.own_attrs_arr.splice(index, 1);
    };
		$scope.del_admin_attrs = function (index) {
			$scope.merchant_add_attrs.splice(index, 1);
		};
		// 判断自己添加的属性，和之前的属性名有无重复
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
    /*---------------------------------属性获取结束---------------------------------*/

    /*----------------上传封面图-----------------------*/
    $scope.data = {
        file: null
    };
    $scope.upload_cover = function (file) {
        if (!$scope.data.file) {
            return
        }
        $scope.upload_dis = true;
        $scope.upload_txt = '上传中...';
        Upload.upload({
            url: '/site/upload',
            data: {'UploadForm[file]': file}
        }).then(function (response) {
            if (!response.data.data) {
                $scope.cover_flag = "上传图片格式不正确，请重新上传"
            } else {
                $scope.cover_flag = '';
                $scope.upload_cover_src = response.data.data.file_path;
            }
	        $scope.upload_dis = false;
	        $scope.upload_txt = '上传';
        }, function (error) {
            console.log(error)
            $scope.upload_cover_src = '';
        })
    };

    /*------------------------上传多张图片--------------------------*/
    $scope.data = {
        file: null
    };
    $scope.completeUpload = true;
    $scope.upload = function (file) {
        if (!$scope.data.file) {
            return
        }
        console.log($scope.data);
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
                $scope.upload_img_arr.push(response.data.data.file_path)
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

    //市场价
    $scope.price_flag = false;
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
        if (!reg_value) {
            $scope.price_flag = true;
        } else {
            (+$scope.market_price >= +$scope.platform_price) && (+$scope.platform_price >= +$scope.supplier_price) ? $scope.price_flag = false : $scope.price_flag = true;
        }

    };

    //售后、保障
    $scope.after_sale_services = [];//售后、保障传值数组
    $scope.invoice_check = true;

    //物流模块
    $scope.logistics_red = false;
    _ajax.post('/mall/logistics-templates-supplier', {}, function (res) {
        //判断有无物流模板数据
        if (res.data.logistics_templates_supplier.length > 0) {
            $scope.logistics = res.data.logistics_templates_supplier;
            //把当前商品添加时的所属的物流模板 前置到第一
            for (let [key, value] of $scope.logistics.entries()) {
                if (value.id == $scope.logistics_template_id) {
                    $scope.logistics.splice(key, 1);
                    $scope.logistics.unshift(value);
                    $scope.logistics_status = true;
                    $scope.logistics_red = false;
                    break;
                } else if (value.id != $scope.logistics_template_id) {  //判断该商品的物流模板是否删除，如果删除，显示提示文字
                    $scope.logistics_red = true;
                    $scope.logistics_status = true;
                }
            }
            $scope.shop_logistics = res.data.logistics_templates_supplier[0].id;
            $scope.$watch('shop_logistics', function (newVal, oldVal) {
                _ajax.get('/mall/logistics-template-view', {id: +newVal}, function (res) {
                    $scope.logistics_method = res.data.logistics_template.delivery_method;//快递方式
                    $scope.district_names = res.data.logistics_template.district_names;//地区名
                    $scope.delivery_cost_default = res.data.logistics_template.delivery_cost_default;//默认运费
                    $scope.delivery_number_default = res.data.logistics_template.delivery_number_default;//默认运费的数量
                    $scope.delivery_cost_delta = res.data.logistics_template.delivery_cost_delta;//增加件费用
                    $scope.delivery_number_delta = res.data.logistics_template.delivery_number_delta;//增加件的数量
                })
            });
        } else {
            $scope.logistics_null = true;//显示“添加物流模板”提示字
            $scope.logistics_status = false;//隐藏select
        }
    })
    // /*--------------编辑保存按钮----------------------*/
    $scope.edit_confirm = function (valid, error) {
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
		    console.log(value);
		    if(value.selected.length>0){
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
		// 判断 input框不能为空、封面图、物流模板、价格、库存、无重复属性名、有品牌、属性输入规则符合标准、属性复选框
        if (valid && $scope.upload_cover_src && $scope.logistics_status && !$scope.price_flag && !$scope.left_number_flag && $scope.own_submitted && $scope.brands_arr.length > 0 && $scope.attr_blur_flag && $scope.attrs_check_flag) {
            $scope.description = UE.getEditor('editor').getContent();//富文本编辑器
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
            //不勾选的状态删除对象项
            function del_correspond(num) {
                let del_index = $scope.after_sale_services.findIndex(function (value, index, arr) {
                    return value == num;
                });
                if (del_index != -1) {
                    $scope.after_sale_services.splice(del_index, 1);
                }
            }
	        /*判断风格和系列是否存在，如果不存在，值传[]*/
	        $scope.style_check_arr = []
	        for (let [key,value] of $scope.styles_arr.entries()) {
		        if (value.status === true) {
			        $scope.style_check_arr.push(value.id)
		        }
	        }
	        // 系列传值
	        $scope.series_model == undefined ? $scope.series_model = 0 : $scope.series_model = $scope.series_model;
	        // 风格传值
	        $scope.style_check_arr[0] == undefined ? $scope.style_check_arr = [] : $scope.style_check_arr = $scope.style_check_arr
          /*-----------------------属性传值--------------------------*/
            // 文本框
            if ($scope.goods_input_attrs[0] != undefined) {
                for (let [key, value] of $scope.goods_input_attrs.entries()) {
                    $scope.pass_attrs_name.push(value.name);
                    $scope.pass_attrs_value.push(value.value);
                }
            }
		        // 下拉框
		        if ($scope.goods_select_attrs[0] != undefined) {
			        for (let [key, value] of $scope.goods_select_attrs.entries()) {
				        $scope.pass_attrs_name.push(value.name);
				        $scope.pass_attrs_value.push(value.selected);
			        }
		        }
		        // 复选框
		        if($scope.goods_check_attrs[0] != undefined){
			        for (let [key,value] of $scope.goods_check_attrs.entries()) {
				        console.log(value.selected);
				        $scope.pass_attrs_name.push(value.name);
				        $scope.pass_attrs_value.push(value.selected);
			        }
			        console.log($scope.pass_attrs_name);
			        console.log($scope.pass_attrs_value);
		        }
		        // 商家本次自己添加的属性
		        if ($scope.own_attrs_arr[0] != undefined) {
			        for (let [key, value] of $scope.own_attrs_arr.entries()) {
				        $scope.pass_attrs_name.push(value.name);//属性名
				        $scope.pass_attrs_value.push(value.value);//属性值
			        }
		        }
		        // 商家之前自己添加的属性
			        if ($scope.merchant_add_attrs[0] != undefined) {
				        for (let [key, value] of $scope.merchant_add_attrs.entries()) {
					        $scope.pass_attrs_name.push(value.name);
					        $scope.pass_attrs_value.push(value.value);
				        }
			        }
	        /*如果没有属性，则传空数组*/
	        if ($scope.pass_attrs_name[0] == undefined) {
		        $scope.pass_attrs_name = [];
	        }
	        if ($scope.pass_attrs_value[0] == undefined) {
		        $scope.pass_attrs_value = [];
	        }
            _ajax.post('/mall/goods-edit', {
                id: +$scope.goods_id, //id
                title: $scope.goods_name,//名称
                subtitle: $scope.des_name,//特色
                brand_id: +$scope.brand_model,//品牌
                style_id: $scope.style_check_arr.join(','),//风格
                series_id: $scope.series_model,//系列
                'names[]': $scope.pass_attrs_name,//属性名称
                'values[]': $scope.pass_attrs_value,//属性值
                cover_image: $scope.upload_cover_src,//封面图
                'images[]': $scope.upload_img_arr,//多张图片
                supplier_price: $scope.supplier_price * 100,//供货价
                platform_price: $scope.platform_price * 100,//平台价
                market_price: $scope.market_price * 100,//市场价
                logistics_template_id: +$scope.shop_logistics,//物流模板id
                after_sale_services: $scope.after_sale_services.join(','),//售后服务
                left_number: +$scope.left_number,//库存
                description: $scope.description//描述
            }, function (res) {
	            if (res.code === 200) {
		            // 正常添加
		            $('#change_ok').modal('show')
		            $scope.success_modal_flag = true
		            $scope.error_modal_flag = false
		            $scope.default_modal_flag = false
		            $scope.add_moal_txt = '保存成功'
	            } else if (res.code === 1045 || res.code === 1047) {  // 所选系列或风格，全部关闭
		            $('#change_ok').modal('show')
		            $scope.default_modal_flag = true
		            $scope.success_modal_flag = false
		            $scope.error_modal_flag = false
		            $scope.add_moal_txt = res.msg
	            }else if(res.code === 1046){                        // 部分系列或风格关闭
		            $('#change_ok').modal('show')
		            $scope.error_modal_flag = true
		            $scope.default_modal_flag = false
		            $scope.success_modal_flag = false
		            $scope.add_moal_txt = res.msg
		            $scope.offline_style_ids = []
		            $scope.offline_style_ids=res.data.offline_style_ids
	            }
            })
        } else {
            $scope.submitted = true;
        }
        //名称输入框为空， 文本框变红，并跳转到对于的位置
        if (!valid) {
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
	// 部分系列或风格关闭 模态框确认按钮
	$scope.someModalBtn =function () {
		for (let [key,value2] of $scope.offline_style_ids.entries()){
			let index = $scope.style_check_arr.findIndex((value) => {
				return value == value2;
			})
			$scope.style_check_arr.splice(index,1)
		}
		_ajax.post('/mall/goods-edit',{
			id:+$scope.goods_id, //id
			title:$scope.goods_name,//名称
			subtitle:$scope.des_name,//特色
			brand_id:+$scope.brand_model,//品牌
			style_id:$scope.style_check_arr.join(','),//风格
			series_id:$scope.series_model,//系列
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
			description:$scope.description//描述
		},function (res) {
			$('#change_ok').modal('hide')
			setTimeout(function () {
				$state.go('commodity_manage');
			}, 300)
		})
	}

    /*-----------------------保存成功跳转--------------------------------*/
    $scope.change_go = function () {
        setTimeout(function () {
            if ($stateParams.flag == 0) {
                $state.go('commodity_manage', {on_flag: true})
            } else if ($stateParams.flag == 1) {
                $state.go('commodity_manage', {down_flag: true})
            }
        }, 300)
    }
    /*--------------------------下架-------------------------------------*/
    $scope.down_btn = function (id) {
        _ajax.post('/mall/goods-status-toggle', {id: id}, function (res) {
            setTimeout(function () {
                $state.go('commodity_manage', {on_flag: true})
            }, 300)
        })
    }
});