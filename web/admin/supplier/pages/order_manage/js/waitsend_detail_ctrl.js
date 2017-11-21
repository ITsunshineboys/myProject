;
let waitsend_detail = angular.module("waitsend_detail_module", []);
waitsend_detail.controller("waitsend_detail_ctrl", function ($rootScope,$scope, $http, $stateParams,$state,_ajax) {
  $scope.myng=$scope;
  $scope.tabflag=$stateParams.tabflag;
  console.log($scope.tabflag)
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
      $scope.shipping_way=$scope.item.goods_data.shipping_way;//配送方式
      //商品详情
      $scope.goods_name=$scope.item.goods_value.goods_name;//商品名称
      $scope.sku=$scope.item.goods_data.sku;//商品编号
      //$scope.goods_id=$stateParams.item.goods_value.goods_id;//商品编号
      $scope.attr=$scope.item.goods_value.attr[0];
      //收货详情
      $scope.consignee=$scope.item.receive_details.consignee;//收货人
      $scope.district=$scope.item.receive_details.district;//收获地址
      $scope.consignee_mobile=$scope.item.receive_details.consignee_mobile;//收货人电话
      $scope.buyer_message=$scope.item.receive_details.buyer_message;//留言
      $scope.invoice_header_type=$scope.item.receive_details.invoice_header_type;//发票类型
      $scope.invoice_header=$scope.item.receive_details.invoice_header;//抬头
      $scope.invoicer_card=$scope.item.receive_details.invoicer_card;//纳税人识别码
      $scope.invoice_content=$scope.item.receive_details.invoice_content;//发票内容
      for(let[key,value] of $scope.attr.entries()){
          if(value.unit==0){
              value.unit=''
          }else if(value.unit==1){
              value.unit='L'
          }else if(value.unit==2){
              value.unit='M'
          }else if(value.unit==3){
              value.unit='M²'
          }else if(value.unit==4){
              value.unit='Kg'
          }else if(value.unit==5){
              value.unit='MM'
          }
      }
      //异常
      _ajax.post('/order/find-unusual-list',{
          order_no:$scope.order_no,
          sku:+$scope.sku
      },function (res) {
          $scope.is_unusual_list_msg=res.data;
      })
      /*物流页面传值*/
      $scope.express_params = {
          order_no: $scope.item.goods_data.order_no,
          sku: $scope.item.goods_data.sku,
          statename: "waitsend_detail",
          tabflag:$stateParams.tabflag
      }
  })

  // 同意按钮
    $scope.agree_confirm=function () {
    console.log($scope.order_no)
    console.log($scope.sku)
    _ajax.post('/order/refund-handle',{
        order_no:$scope.order_no,
        sku:$scope.sku,
        handle:1
    },function (res) {
        console.log(res);
        setTimeout(function () {
            $state.go('order_manage',{wait_send_flag:true});
        },300)
     })
    }
    //驳回确认按钮
    $scope.turn_down_confirm=function () {
      _ajax.post('/order/refund-handle',{
          order_no:$scope.order_no,
          sku:$scope.sku,
          handle:2,
          handle_reason:$scope.turn_down_txt
      },function (res) {
          $scope.is_unusual=0;//驳回后按钮组变化为----》发货
          $scope.is_unusual_flag=true;
          _ajax.post('/order/find-unusual-list',{
              order_no:$scope.order_no,
              sku:+$scope.sku
          },function (res) {
              $scope.is_unusual_list_msg=res.data;
          })
      })
    };
    //显示隐藏待发货异常记录
    $scope.unshipped_ul_flag=true;
    $scope.unshipped_img_flag_up=true;
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
    }
    //显示隐藏待收货异常记录
    $scope.unreceived_ul_flag=true;
    $scope.unreceived_img_flag_up=true;
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
    //点击修改单号，初始化上次操作的状态
    $scope.clear_track=function () {
        $scope.track_change_flag=false;
        $scope.track_change_font='';
        $scope.track_input_model='';
    };
   //修改快递单号确认按钮
    $scope.track_change_flag=false;
    $scope.change_track_btn=function () {
        if(!!$scope.track_input_model){
          _ajax.post('/order/expressupdate',{
              order_no:$scope.order_no,
              sku:$scope.sku,
              waybillnumber:$scope.track_input_model
          },function (res) {
              if(res.code==200){
                  console.log(res);
                  $('#change_track_modal').modal('hide');
                  $scope.shipping_way=res.data.shipping_way;
              }else{
                  $scope.track_change_flag=true;
                  $scope.track_change_font='快递单号错误，请重新输入';
              }
          })
        }else{
            $scope.track_change_flag=true;
            $scope.track_change_font='快递单号不能为空';
        }
    }
    //发货按钮
    $scope.track_flag=false;
    $scope.wait_send_ship=function () {
        $scope.delivery_input_model='';
        $scope.track_flag=false;
        $scope.track_font='';
        if($scope.item.goods_data.shipping_type==0){
            $('#track_confirm_modal').modal('show');
        }else{
            $('#wait_send_confirm_modal').modal('show');
        }
            //快递发货确认按钮
            $scope.track_confirm_btn = function () {
                if(!!$scope.delivery_input_model){
                  _ajax.post('/order/supplierdelivery',{
                      order_no:$scope.item.goods_data.order_no,
                      sku:$scope.item.goods_data.sku,
                      shipping_type:'0',
                      waybillnumber:$scope.delivery_input_model
                  },function (res) {
                      console.log(res);
                      if(res.code==200){
                          $('#track_confirm_modal').modal('hide');
                          setTimeout(function () {
                              $state.go('order_manage',{wait_send_flag:true});
                          },300)
                      }else if(res.data.code==1000){
                          $scope.track_flag=true;
                          $scope.track_font='快递单号错误，请重新输入';//快递单号-显示
                      }
                  })
                }else{
                    $scope.track_flag=true;
                    $scope.track_font='快递单号不能为空';
                }
            };
            //正常发货
            $scope.ship_confirm_btn=function () {
              _ajax.post('/order/supplierdelivery',{
                  order_no:$scope.item.goods_data.order_no,
                  sku:$scope.item.goods_data.sku,
                  shipping_type:'1',
              },function (res) {
                  console.log(res);
                  $('#wait_send_confirm_modal').modal('hide');
                  setTimeout(function () {
                      $state.go('order_manage',{wait_send_flag:true});
                  },300)
              })
            }
            //取消按钮
            $scope.btn_dismiss=function () {
                $scope.delivery_input_model='';
                $scope.track_flag=false;
                $scope.track_font='';
            }
    };
});