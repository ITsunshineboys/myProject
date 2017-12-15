/**
 * Created by Administrator on 2017/9/22/022.
 */
let done_detail = angular.module("done_detailModule", []);
done_detail.controller("done_detail_ctrl", function ($rootScope,$scope, _ajax, $stateParams) {
    $scope.tabflag = $stateParams.tabflag; //页面跳转
	  $scope.order_no = $stateParams.order_no; //订单号
	  $scope.sku = $stateParams.sku;//商品编号
    $scope.statename='done_detail';
    $scope.textcount = '';
    $scope.sp = {
        textcount: $scope.textcount
    };
    $scope.savemodal = '';
    $rootScope.crumbs = [{
        name: '订单管理',
        icon: 'icon-dingdanguanli',
        link: 'order_manage',
        params:{tabflag:$stateParams.tabflag}
    },{
        name: '订单详情',
    }];

    orderDetail();
    commentDetail();
    $scope.send = true;
    $scope.receive = true;
    $scope.emptywarning = false;
    $scope.unshipped_detail = [];
    $scope.unreceived_detail = [];



    /*异常记录-待发货
     * 异常记录-待收货
     * 记录是否显示*/
    $scope.abnormal = function (obj) {
        $scope[obj] = !$scope[obj];
    };



    // 物流页面传参
    $scope.express_params = {
        order_no: $scope.order_no,
        sku: $scope.sku,
        statename: "done_detail",
        tabflag:$stateParams.tabflag
    };

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
            abnormalHandle()
        });
    }

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


    /*评论详情*/
    function commentDetail() {
        let data = {
            order_no: $stateParams.order_no,
            sku: $stateParams.sku
        };
        _ajax.post("/order/get-comment", data, function (res) {
            console.log(res);
            /*判断有无评论*/
            if (res.data.length === 0) {
                $scope.showcomment = false;
            } else {
                $scope.showcomment = true;
                $scope.comment = res.data;
                $scope.store_service_score = $scope.comment.store_service_score;
	              $scope.logistics_speed_score = $scope.comment.logistics_speed_score;
	              $scope.shipping_score = $scope.comment.shipping_score;
                !$scope.comment.reply == true ? $scope.show_storecomment = false : $scope.show_storecomment = true;
            }

            /*是否显示保存按钮*/
            $scope.showcomment && !$scope.show_storecomment ? $scope.showbtn = true : $scope.showbtn = false;
        });
    }

    /*保存商家评论*/
    $scope.saveStoreComment = () => {
        !$scope.sp.textcount.length ? $scope.emptywarning = true : $scope.emptywarning = false;
        if (!$scope.emptywarning) {
            $scope.savemodal = "#send_modal";
            let data = {
                order_no: +$stateParams.order_no,
                sku: +$stateParams.sku,
                reply_content: $scope.sp.textcount
            };
            _ajax.post("/order/comment-reply", data, function (res) {
                commentDetail();
            });
        }
    };

    /*商家回复为空的处理*/
    $scope.checkEmpty = () => {
        !$scope.sp.textcount.length ? $scope.emptywarning = true : $scope.emptywarning = false
    };

    /*已完成图片放大显示*/
    $scope.showImgs = (src) => {
        $scope.showImg = src;
    }
});