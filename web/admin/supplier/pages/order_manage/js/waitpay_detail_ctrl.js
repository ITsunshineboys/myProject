/**
 * Created by Administrator on 2017/9/21/021.
 */
let waitpay_detail = angular.module("waitpay_detailModule", []);
waitpay_detail.controller("waitpaydetail_ctrl", function ($rootScope, $scope, _ajax, $stateParams,$interval) {
    orderDetail();
    let term;
    $scope.order_no = $stateParams.order_no; //订单号
    $scope.sku = $stateParams.sku;//商品编号
    $scope.tabflag = $stateParams.tabflag; //页面跳转
    $scope.statename='waitpay_detail'
    $rootScope.crumbs = [{
        name: '订单管理',
        icon: 'icon-dingdanguanli',
        link: 'order_manage',
        params:{tabflag:$stateParams.tabflag}
    },{
        name: '订单详情',
    }];

    /*订单详情
     * 商品详情
     * 收货详情*/
    function orderDetail() {
        let data = {
            order_no: $stateParams.order_no,
            sku: +$stateParams.sku
        };

        _ajax.get("/order/getsupplierorderdetails", data, function (res) {
            $scope.order_detail = res.data.goods_data; //订单详情
            $scope.goods_value = res.data.goods_value; //商品详情
            $scope.receive_details = res.data.receive_details;//收货详情
            $scope.is_refund = res.data.is_refund; //是否有异常记录
            $scope.attr=$scope.goods_value.attr;
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
            let time = res.data.goods_data.pay_term; // 剩余付款时间秒数
            term = $interval(function () {
                time -= 1;
                $scope.payTerm = secondToDate(time);
                if (time < 1) {
                    $interval.cancel(term);
                }
            }, 1000);
        });
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
});






