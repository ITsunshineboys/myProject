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
    $scope.platformInter = $stateParams.type;
    $scope.params = params;

    // 获取订单详情
    _ajax.post('/order/getsupplierorderdetails', params, function (res) {
        console.log(res, '订单详情');
        let data = res.data;
        $scope.orderDetails = data.goods_data;          // 订单详情信息
        $scope.goodsDetails = data.goods_value;         // 商品详情信息
        $scope.receiveDetails = data.receive_details;   // 收货详情信息
        if (data.is_refund != 1) {
            _ajax.post('/order/find-unusual-list-lhzz', params, function (res) {
                console.log(res, '异常信息');
                $scope.receiving = res.data[0]; // 待发货异常记录
                $scope.delivery = res.data[1];  // 待收货异常记录
            })
        }
    });

    // 售后信息
    saleDetail();

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
        _ajax.post('/order/platformhandlesubmit', $scope.interOper, function (res) {
            console.log(res, '平台介入操作');
            window.history.go(-1);
        })
    };

    // 关闭订单模态框和数据初始化
    $scope.close_order = function () {
        $scope.close_order_params = {
            order_no: $stateParams.orderNo,
            sku: $stateParams.sku,
            reason: ''
        };
        $('#closeOrderModal').modal('show');
    };

    // 确认关闭订单
    $scope.enter_close_order = function () {
        _ajax.post('/order/close-order', $scope.close_order_params, function (res) {
            history.back();
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

    /**
     * 售后信息
     */
    function saleDetail() {
        _ajax.get('/order/after-sale-detail-admin', params, function (res) {
            console.log(res, "售后信息");
            let data = res.data;
            $scope.sale_detail = data.after_sale_detail;    // 售后信息
            $scope.sale_progress = data.after_sale_progress.data; // 售后进度
            $scope.sale_progress_platform = data.after_sale_progress.platform; // 售后进度-平台介入
            $scope.is_close_order = data.state === 'in';
            $scope.sale_progress_time = '00天00时00分00秒';
            // 判断是否有平台介入
            if ($scope.sale_progress_platform.length === 0) {   // 没有
                salesTimer($scope.sale_progress)
            } else {
                salesTimer($scope.sale_progress_platform)
            }

            let clear_watch = $scope.$watch('sale_temp_time', function (n, o) {
                if (n === o) return;
                if (n <= 0) {
                    saleDetail();
                    clear_watch();
                }
            });
        });
    }

    /**
     * 售后进度计时器
     * @param array 数组
     */
    function salesTimer(array) {
        // 遍历数组，查询倒计时，并实现
        for (let obj of array) {
            if (obj.code === 'user_unconfirm_received' || obj.code === 'supplier_unconfirm_received') {
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
}]);