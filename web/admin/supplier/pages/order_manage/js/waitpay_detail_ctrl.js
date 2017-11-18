/**
 * Created by Administrator on 2017/9/21/021.
 */
let waitpay_detail = angular.module("waitpay_detailModule", []);
waitpay_detail.controller("waitpaydetail_ctrl", function ($rootScope, $scope, $http, $stateParams,$interval) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };


    orderDetail();
    let term;
    $scope.order_no = $stateParams.order_no; //订单号
    $scope.sku = $stateParams.sku;//商品编号
    $scope.tabflag = $stateParams.tabflag; //页面跳转

    $rootScope.crumbs = [{
        name: '订单管理',
        icon: 'icon-dingdanguanli',
        link: 'order_manage',
        params:{tabflag:$stateParams.tabflag}
    },{
        name: '订单详情',
    }];


    $scope.express_params = {
        order_no:$scope.order_no,
        sku:$scope.sku,
        statename:'waitpay_detail',
        tabflag:$stateParams.tabflag
    }


    /*订单详情
     * 商品详情
     * 收货详情*/
    function orderDetail() {
        let url = baseUrl+"/order/getsupplierorderdetails";
        let data = {
            order_no: $stateParams.order_no,
            sku: +$stateParams.sku
        };
        $http.post(url, data, config).then(function (res) {
            $scope.order_detail = res.data.data.goods_data; //订单详情
            $scope.goods_value = res.data.data.goods_value; //商品详情
            $scope.receive_details = res.data.data.receive_details;//收货详情
            $scope.is_refund = res.data.data.is_refund //是否有异常记录
            let time = res.data.data.goods_data.pay_term // 剩余付款时间秒数
                term = $interval(function () {
                time -= 1;
                $scope.payTerm = secondToDate(time);
                if (time < 1) {
                    $interval.cancel(term);
                }
            }, 1000);
        })
    }


    /**
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
        $scope.lefttime = h + '时' + m + '分';
    }

    $scope.$on('$destroy', function() {
        // 保证interval已经被销毁
        $interval.cancel(term);
    });
})






