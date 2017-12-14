let market_detail = angular.module("market_detail_module", []);
market_detail.controller("market_detail_ctrl", function ($rootScope,$scope,$interval,$http,$stateParams,$state,_ajax) {
	$scope.myng=$scope;
	$scope.tabflag=$stateParams.tabflag;
	console.log($scope.tabflag);
	let phone_reg = /^1[3|4|5|7|8][0-9]{9}$/;//手机号
	//返回按钮
	$scope.back_list=function () {
		$state.go('order_manage',{tabflag:$scope.tabflag});//返回待收货列表
	};
	$rootScope.crumbs = [{
		name: '订单管理',
		icon: 'icon-dingdanguanli',
		link: $scope.back_list
	}, {
		name: '订单详情',
	}];
		// console.log($stateParams.order_no)
		// console.log($stateParams.sku)
		// console.log($stateParams.tabflag)
	//详情数据
	_ajax.post('/order/getsupplierorderdetails',{
		order_no:$stateParams.order_no,
		sku:$stateParams.sku
	},function (res) {
		$scope.item=res.data;
		console.log($scope.item)
		//是否是异常订单
		$scope.is_unusual=$scope.item.is_unusual;
		//订单详情
		$scope.order_no=$scope.item.goods_data.order_no;//订单号
		$scope.shipping_type=$scope.item.goods_data.shipping_type;//判断送货方式，0为快递 1为送货上门
		$scope.status=$scope.item.goods_data.status;//订单状态
		$scope.username=$scope.item.goods_data.username;//用户名
		$scope.amount_order=$scope.item.goods_data.amount_order;//总金额
		$scope.role=$scope.item.goods_data.role;//总金额后面的价格（实时变化）
		$scope.goods_price=$scope.item.goods_data.goods_price;//goods_price
		$scope.freight=$scope.item.goods_data.freight;//运费
		$scope.supplier_price=$scope.item.goods_data.supplier_price;//供货价格
		$scope.market_price=$scope.item.goods_data.market_price;//市场价
		$scope.goods_number=$scope.item.goods_data.goods_number;//商品个数
		$scope.pay_name=$scope.item.goods_data.pay_name;//付款方式
		$scope.create_time=$scope.item.goods_data.create_time;//下单时间
		$scope.paytime=$scope.item.goods_data.paytime;//付款时间
		$scope.complete_time=$scope.item.goods_data.complete_time//完成时间
		$scope.shipping_way=$scope.item.goods_data.shipping_way;//配送方式
		//商品详情
		$scope.goods_name=$scope.item.goods_value.goods_name;//商品名称
		$scope.sku=$scope.item.goods_data.sku;//商品编号
		//$scope.goods_id=$stateParams.item.goods_value.goods_id;//商品编号
		//$scope.attr=$scope.item.goods_value.attr[0];
		//收货详情
		$scope.consignee=$scope.item.receive_details.consignee;//收货人
		$scope.district=$scope.item.receive_details.district;//收获地址
		$scope.consignee_mobile=$scope.item.receive_details.consignee_mobile;//收货人电话
		$scope.buyer_message=$scope.item.receive_details.buyer_message;//留言
		$scope.invoice_header_type=$scope.item.receive_details.invoice_type;//发票类型
		$scope.invoice_header=$scope.item.receive_details.invoice_header;//抬头
		$scope.invoicer_card=$scope.item.receive_details.invoicer_card;//纳税人识别码
		$scope.invoice_content=$scope.item.receive_details.invoice_content;//发票内容
		// for(let[key,value] of $scope.attr.entries()){
		//     if(value.unit==0){
		//         value.unit=''
		//     }else if(value.unit==1){
		//         value.unit='L'
		//     }else if(value.unit==2){
		//         value.unit='M'
		//     }else if(value.unit==3){
		//         value.unit='M²'
		//     }else if(value.unit==4){
		//         value.unit='Kg'
		//     }else if(value.unit==5){
		//         value.unit='MM'
		//     }
		// }
		//异常
		_ajax.post('/order/find-unusual-list',{
			order_no:$scope.order_no,
			sku:+$scope.sku
		},function (res) {
			$scope.is_unusual_list_msg=res.data;
			// console.log(res);
		});
		/*物流页面传值*/
		$scope.express_params = {
			order_no: $scope.item.goods_data.order_no,
			sku: $scope.item.goods_data.sku,
			statename: "waitsend_detail",
			tabflag:$stateParams.tabflag
		}
	});
	//评论信息
	_ajax.post('/order/get-comment',{
		order_no:$stateParams.order_no,
		sku:$stateParams.sku
	},function (res) {
		console.log(res);
		res.data.length===0?$scope.comment_flag=false:$scope.comment_flag=true;

		$scope.comment_content=res.data.content;//评论内容
		$scope.comment_score=res.data.score;//商品评分
		$scope.comment_store_service_score=res.data.store_service_score;////店家服务评分
		$scope.comment_logistics_speed_score=res.data.logistics_speed_score;//物流速度评分
		$scope.comment_shipping_score=res.data.shipping_score;//配送人员服务评分
		$scope.comment_reply=res.data.reply;//店家回复
	})
	//售后详情
	function saleDetail() {
		_ajax.get('/order/after-sale-detail-admin',{
			order_no:$stateParams.order_no,
			sku:$stateParams.sku
		},function (res) {
			console.log(res);
			$scope.after_sale_type=res.data.after_sale_detail.type;//服务类型
			$scope.after_sale_description=res.data.after_sale_detail.description;//问题描述
			$scope.after_sale_images=res.data.after_sale_detail.image;//图片
			$scope.after_sale_progress_data=res.data.after_sale_progress.data;//售后进度
			let data_status=angular.copy($scope.after_sale_progress_data);
			$scope.after_sale_progress_platform=res.data.after_sale_progress.platform;//平台介入
			let platform_status=angular.copy($scope.after_sale_progress_platform);
			$scope.btn_status=data_status.reverse()[0].code;//判断按钮组的显示
			if($scope.after_sale_progress_platform.length>0&&$scope.btn_status==''){
				$scope.btn_status=platform_status.reverse()[0].code
				// $scope.sale_status=platform_status.reverse()[0].status;//判断订单状态（售后中，售后完成）
			}
			if(data_status.reverse()[0].status=='over'){
				console.log('over');
				$scope.status='售后完成'
			}

			$scope.sale_progress_time = '00天00时00分00秒';
			// 判断是否有平台介入
			if ($scope.after_sale_progress_platform.length === 0) {   // 没有
				salesTimer($scope.after_sale_progress_data)
			} else {
				salesTimer($scope.after_sale_progress_platform)
			}
			let clear_watch = $scope.$watch('sale_temp_time', function (n, o) {
				if (n === o) return;
				if (n <= 0) {
					saleDetail();
					clear_watch();
				}
			});
			// for(let[key,value] of $scope.after_sale_progress_data.entries()){
			// 	if(value.code=='shipped'){
			// 		$scope.express_number=value.number;//快递单号
			// 	}
			// }
		});
	}
	saleDetail();
	function salesTimer(array) {
		console.log(array)
		// 遍历数组，查询倒计时，并实现
		for (let obj of array) {
			if (obj.code === 'countdown'|| obj.code==='supplier_unconfirm_received') {
				$scope.sale_temp_time = obj.content;
				let clear_interval = $interval(function () {
					if ($scope.sale_temp_time < 1) {
						$interval.cancel(clear_interval)
					} else {
						$scope.sale_temp_time--;
						obj.content = secondToDate($scope.sale_temp_time, 'day');
					}
				}, 1000);
				break;
			}
		}
	}
	//显示隐藏---待发货---异常记录
	$scope.unshipped_ul_flag=false;
	$scope.unshipped_img_flag_down=true;
	$scope.arrow_unshipped=function () {
		if($scope.unshipped_ul_flag==true){
			$scope.unshipped_ul_flag=false;
			$scope.unshipped_img_flag_up=false;
			$scope.unshipped_img_flag_down=true;
		}else{
			$scope.unshipped_ul_flag=true;
			$scope.unshipped_img_flag_up=true;
			$scope.unshipped_img_flag_down=false;
		}
	};
	//显示隐藏---待收货---异常记录
	$scope.unreceived_ul_flag=false;
	$scope.unreceived_img_flag_down=true;
	$scope.arrow_unreceived=function () {
		if($scope.unreceived_ul_flag==true){
			$scope.unreceived_ul_flag=false;
			$scope.unreceived_img_flag_up=false;
			$scope.unreceived_img_flag_down=true;
		}else{
			$scope.unreceived_ul_flag=true;
			$scope.unreceived_img_flag_up=true;
			$scope.unreceived_img_flag_down=false;
		}
	}
//显示隐藏---售后进度---异常记录
	$scope.unmarket_ul_flag=false;
	$scope.unmarket_img_flag_down=true;
	$scope.arrow_unmarket=function () {
		if($scope.unmarket_ul_flag==true){
			$scope.unmarket_ul_flag=false;
			$scope.unmarket_img_flag_up=false;
			$scope.unmarket_img_flag_down=true;
		}else{
			$scope.unmarket_ul_flag=true;
			$scope.unmarket_img_flag_up=true;
			$scope.unmarket_img_flag_down=false;
		}
	}
	//显示隐藏---平台介入---异常记录
	$scope.unplatform_ul_flag=false;
	$scope.unplatform_img_flag_down=true;
	$scope.arrow_platform=function () {
		if($scope.unplatform_ul_flag==true){
			$scope.unplatform_ul_flag=false;
			$scope.unplatform_img_flag_up=false;
			$scope.unplatform_img_flag_down=true;
		}else{
			$scope.unplatform_ul_flag=true;
			$scope.unplatform_img_flag_up=true;
			$scope.unplatform_img_flag_down=false;
		}
	}

	//发货按钮
	$scope.ship=function () {
		if(!!$scope.ship_input_model){
			_ajax.post('/order/after-sale-delivery',{
				waybillnumber:$scope.ship_input_model,
				role:'supplier',
				order_no:$stateParams.order_no,
				sku:$stateParams.sku
			},function (res) {
				console.log(res);
				if(res.data.code==200){
					saleDetail();
				}else{
					$scope.track_flag=true;
					$scope.track_font='快递单号错误，请重新输入';
				}

			});
		}else{
			$scope.track_flag=true;
			$scope.track_font='快递单号不能为空'
		}
	};
	//收货按钮
	$scope.receiving_confirm_btn=function () {
		_ajax.post('/order/after-sale-supplier-confirm',{
			order_no:$stateParams.order_no,
			sku:$stateParams.sku,
			type:'received'
		},function (res) {
			console.log(res);
			saleDetail();
		})
	}
	//同意按钮
	$scope.agree_confirm=function () {
		_ajax.post('/order/supplier-after-sale-handle',{
			order_no:$stateParams.order_no,
			sku:$stateParams.sku,
			handle:'1'
		},function (res) {
			console.log(res);
			saleDetail();
		})
	};
	//驳回按钮
	$scope.turnDown=function () {
		_ajax.post('/order/supplier-after-sale-handle',{
			order_no:$stateParams.order_no,
			sku:$stateParams.sku,
			handle:'2',
			reject_reason:$scope.turn_down_txt
		},function (res) {
			console.log(res);
			setTimeout(function () {
				$state.go('order_manage');
			},300)
		});
	}
	//收货按钮
	$scope.receiving=function () {
		_ajax.post('/order/after-sale-supplier-confirm',{
			order_no:$stateParams.order_no,
			sku:$stateParams.sku,
			type:'received'
		},function (res) {
			console.log(res);
			saleDetail();
		})
	};
	//派出人员按钮
	$scope.send_staff=function () {
		!!$scope.send_name?$scope.name_show_flag=false:$scope.name_show_flag=true;
		!!$scope.send_phone?$scope.phone_show_flag=false:$scope.phone_show_flag=true;
		if($scope.phone_show_flag==false && $scope.name_show_flag==false){
			if(!phone_reg.test($scope.send_phone)){
				$scope.phone_txt_flag=true;
			}else{
				$scope.phone_txt_flag=false;
				_ajax.post('/order/after-sale-supplier-send-man',{
					order_no:$stateParams.order_no,
					sku:$stateParams.sku,
					worker_name:$scope.send_name,
					worker_mobile:$scope.send_phone
				},function (res) {
					console.log(res);
					$('#send_staff_modal').modal('hide');
					// setTimeout(function () {
					// 	$state.go('order_manage')
					// },300)
					saleDetail();
				})
			}
		}
	}
	//确认按钮
	$scope.supplier_confirm=function () {
		_ajax.post('/order/after-sale-supplier-confirm',{
			order_no:$stateParams.order_no,
			sku:$stateParams.sku
		},function (res) {
			console.log(res);
			saleDetail();
		})
	}
});