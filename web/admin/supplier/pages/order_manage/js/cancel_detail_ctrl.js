/**
 * Created by Administrator on 2017/9/25/025.
 */
let cancel_detail = angular.module("cancel_detailModule", []);
cancel_detail.controller("cancel_detail_ctrl", function ($rootScope,$scope, _ajax, $stateParams) {
    $scope.tabflag = $stateParams.tabflag; //页面跳转
	  $scope.order_no = $stateParams.order_no; //订单号
	  $scope.sku = $stateParams.sku;//商品编号
	  $scope.statename="cancel_detail"
    $scope.send = true;
    $scope.receive = true;
    $scope.plat_send = true;

    $rootScope.crumbs = [{
        name: '订单管理',
        icon: 'icon-dingdanguanli',
        link: 'order_manage',
        params:{tabflag:$stateParams.tabflag}
    },{
        name: '订单详情',
    }];

    const handle_type = {
        1:'关闭订单退款',
        2:'关闭订单线下退款',
        3:'退货',
        4:'换货',
        5:'上门维修',
        6:'上门退货',
        7:'上门换货'
    }

    let abnormal_result;



    /*异常记录-待发货
     * 异常记录-待收货
     * 记录是否显示*/
    $scope.abnormal = function (obj) {
        $scope[obj] = !$scope[obj];
    }

    orderDetail();

    /*订单详情
     * 商品详情
     * 收货详情*/
    function orderDetail() {
        let data = {
            order_no: $stateParams.order_no,
            sku: $stateParams.sku
        };
        _ajax.get("/order/getsupplierorderdetails", data, function (res) {
            $scope.order_detail = res.data.goods_data; //订单详情
            $scope.goods_value = res.data.goods_value; //商品详情
            $scope.receive_details = res.data.receive_details;//收货详情
            $scope.is_refund = res.data.is_refund; //是否有异常记录
            $scope.is_platform = res.data.is_platform;//平台是否介入
            abnormalHandle();
            $scope.is_platform == 1?$scope.show_plat = false:platAbnormalHandle();//是否显示平台记录
	          $scope.attr=$scope.goods_value.attr;//商品属性
          if(!!$scope.attr.length){
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
          }
        });
    }


    $scope.unshipped_detail = [];
    $scope.unreceived_detail = [];


    /*订单异常处理*/
    function abnormalHandle() {
        $scope.unshipped = false;
        $scope.unreceived = false;
        $scope.is_refund == 1 ? $scope.show_abnormal = false : $scope.show_abnormal = true;
        if ($scope.is_refund == 2) {
            let data = {
                order_no: $stateParams.order_no,
                sku: $stateParams.sku
            };
            _ajax.post("/order/find-unusual-list", data, function (res) {
                abnormal_result = res.data;
                res.data[0] instanceof Array? $scope.unshipped=false:abnormalDetail('unshipped',0);
                res.data[1] instanceof Array? $scope.unreceived=false:abnormalDetail('unreceived',1);
            });
        }
    }

    /*异常详情*/
    function abnormalDetail(type,index) {
        $scope[type]=true;
        $scope[type+'_detail']= abnormal_result[index].list;
    }


    /*平台介入异常*/
    function platAbnormalHandle() {
       $scope.show_plat = true;//显示平台记录
        let data = {
            order_no: $stateParams.order_no,
            sku: $stateParams.sku
        };
        _ajax.post("/order/getplatformdetail", data, function (res) {
            $scope.platforminfo = res.data;
            $scope.handle_type = handle_type[Number(res.data.handle)]; //操作类型
        });
    }
});