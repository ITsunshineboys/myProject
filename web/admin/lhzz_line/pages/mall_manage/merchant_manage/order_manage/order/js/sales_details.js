app.controller('sales_details', ['$rootScope', '$scope', '$interval', '$state', '$stateParams', '_ajax', function ($rootScope, $scope, $interval, $state, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家管理',
        link: 'store_mag'
    }, {
        name: '订单管理',
        link: -1
    }, {
        name: '订单详情'
    }];
    let params = {
        order_no: $stateParams.orderNo, // 订单编号
        sku: $stateParams.sku           // 商品编号
    };
    $scope.isException = false; // 默认不显示异常记录
    $scope.platformInter = $stateParams.type;
    $scope.params = params;

    // 获取订单详情
    _ajax.post('/order/getsupplierorderdetails', params, function (res) {
        console.log(res, '订单详情');
        let data = res.data;
        $scope.orderDetails = data.goods_data;          // 订单详情信息
        $scope.goodsDetails = data.goods_value;         // 商品详情信息
        $scope.receiveDetails = data.receive_details;   // 收货详情信息
        if (data.goods_data.pay_term <= 0) {
            $scope.payTerm = '00时00分'
        } else {
            let time = data.goods_data.pay_term;
            let term = $interval(function () {
                time -= 1;
                $scope.payTerm = secondToDate(time);
                if (time < 1) {
                    $interval.cancel(term);
                }
            }, 1000);
        }
        // 判断平台是否介入过
        $scope.isPlatform = data.is_platform == 2;
        if (data.is_refund != 1) {
            _ajax.post('/order/find-unusual-list-lhzz', params, function (res) {
                console.log(res, '异常信息');
                $scope.receiving = res.data[0]; // 待发货异常记录
                $scope.delivery = res.data[1];  // 待收货异常记录
            })
        }
        // 平台介入信息
        if (data.is_platform == 2) {
            _ajax.post('/order/getplatformdetail', params, function (res) {
                console.log(res, '平台介入信息');
                $scope.platformInfo = res.data
            })
        }
    });

    // 售后信息
    _ajax.get('/order/after-sale-detail-admin', params, function (res) {
        console.log(res, "售后信息");
        let data = res.data;
        $scope.sale_detail = data.after_sale_detail;    // 售后信息
        $scope.sale_progress = data.after_sale_progress.data; // 售后进度
        $scope.sale_progress_platform = data.after_sale_progress.platform; // 售后进度-平台介入
    });

    // 已完成订单评论信息
    commentsFun();

    // 显示评论图片原图
    $scope.showImage = function (src) {
        $scope.showImg = src;
        $('#myModal').modal('show')
    };

    // 删除评论
    $scope.delComments = function () {
        _confirm('是否删除评论？', delCommFun)
    };

    /* 删除评论函数 */
    function delCommFun() {
        _ajax.post('/order/supplier-delete-comment', params, function (res) {
            commentsFun()
        })
    }

    /* 平台介入操作 */
    $scope.interOper = {
        order_no: $stateParams.orderNo, // 订单编号
        sku: $stateParams.sku,          // 商品编号
        handle_type: '',               // 平台介入类型
        reason: ''                      // 原因
    };

    // 获取订单平台可介入类型
    _ajax.get('/order/find-order-after-handel-status', params, function (res) {
        console.log(res, "平台介入类型");
        let data = res.data;
        $scope.platform_intervention = data;
        if (data.length !== 0) {
            $scope.interOper.handle_type = data[0].value;
        }
    });

    $scope.confirmAction = function () {
        _ajax.post('/order/platformhandlesubmit', $scope.interOper,function (res) {
            console.log(res, '平台介入操作');
            window.history.go(-1);
        })
    };

    /**
     * 评论回复信息函数
     */
    function commentsFun() {
        _ajax.post('/order/get-comment', params, function (res) {
            console.log(res, '评论回复');
            $scope.comments = res.data;
        })
    }
}]);