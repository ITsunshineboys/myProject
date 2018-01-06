app.controller('order_details', ['$rootScope', '$scope', '$interval', '$state', '$stateParams', '_ajax', function ($rootScope, $scope, $interval, $state, $stateParams, _ajax) {
    console.log($rootScope.fromState_name);
    let fromState = $rootScope.fromState_name;
    if (fromState !== '' && fromState !== 'goods_details') {
        sessionStorage.setItem('fromState', fromState);
    }

    switch (sessionStorage.getItem('fromState')) {
        case 'search.order':
            $rootScope.crumbs = [{
                name: '商城管理',
                icon: 'icon-shangchengguanli',
                link: $rootScope.mall_click
            }, {
                name: '搜索',
                link: -1
            }, {
                name: '订单详情'
            }];
            break;
        default:
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
    }


    let params = {
        order_no: $stateParams.orderNo, // 订单编号
        sku: $stateParams.sku           // 商品编号
    };
    let orderType = $stateParams.status;
    $scope.isException = false; // 默认不显示异常记录
    $scope.orderType = orderType;
    $scope.platformInter = $stateParams.type;
    $scope.params = params;

    // 获取订单详情
    _ajax.get('/order/getsupplierorderdetails', params, function (res) {
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
        // 判断有无异常信息
        $scope.isException = data.is_refund != 1;
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

    // 已完成订单评论信息
    if (orderType == '已完成') {
        commentsFun()
    }

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
     * 秒转换为时分
     * @param time  // 秒数
     */
    function secondToDate(time) {
        let h = Math.floor(time / 3600 % 24);
        let m = Math.floor(time / 60 % 60);
        let s = Math.floor(time % 60);
        if (h < 10) {
            h = '0' + h;
        }
        if (m < 10) {
            m = '0' + m;
        }
        return h + '时' + m + '分';
    }

    /**
     * 评论回复信息函数
     */
    function commentsFun() {
        _ajax.get('/order/get-comment', params, function (res) {
            console.log(res, '评论回复');
            $scope.comments = res.data;
        })
    }
}]);