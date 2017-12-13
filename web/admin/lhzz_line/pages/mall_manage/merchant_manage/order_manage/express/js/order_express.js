app.controller('express', ['$rootScope', '$scope', '$stateParams', '_ajax', function ($rootScope, $scope, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家管理',
        link: 'store_mag'
    }, {
        name: '订单管理',
        link: -2
    }, {
        name: '订单详情',
        link: -1
    }, {
        name: '物流详情'
    }];
    $scope.order_no = $stateParams.orderNo;
    if ($stateParams.type === "sales") {
        // 售后订单物流信息
        _ajax.get('/order/after-find-express', {waybillnumber: $stateParams.waybillnumber}, function (res) {
            console.log(res, "售后物流信息");
            $scope.data = res.data;
        })
    } else {
        // 正常售后订单物流信息
        let params = {
            order_no: $stateParams.orderNo,
            sku: $stateParams.sku
        };
        _ajax.post('/order/getexpress', params, function (res) {
            console.log(res, '物流信息');
            $scope.data = res.data;
        })
    }
}]);