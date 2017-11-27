/**
 * Created by Administrator on 2017/9/25/025.
 */
let cancel_detail = angular.module("cancel_detailModule", []);
cancel_detail.controller("cancel_detail_ctrl", function ($rootScope,$scope, _ajax, $stateParams) {
    $scope.tabflag = $stateParams.tabflag; //页面跳转
    $scope.send = true;
    $scope.receive = true;
    $scope.plat_send = true;
    $scope.order_no = $stateParams.order_no; //订单号
    $scope.sku = $stateParams.sku;//商品编号
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

    /*物流页面跳转传参*/
    $scope.express_params = {
        order_no: $scope.order_no,
        sku: $scope.sku,
        statename: "cancel_detail",
        tabflag:$stateParams.tabflag
    }

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
            sku: +$stateParams.sku
        };
        _ajax.post("/order/getsupplierorderdetails", data, function (res) {
            $scope.order_detail = res.data.goods_data; //订单详情
            $scope.goods_value = res.data.goods_value; //商品详情
            $scope.receive_details = res.data.receive_details;//收货详情
            $scope.is_refund = res.data.is_refund; //是否有异常记录
            $scope.is_platform = res.data.is_platform;//平台是否介入
            abnormalHandle();
            $scope.is_platform == 1?$scope.show_plat = false:platAbnormalHandle();//是否显示平台记录
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