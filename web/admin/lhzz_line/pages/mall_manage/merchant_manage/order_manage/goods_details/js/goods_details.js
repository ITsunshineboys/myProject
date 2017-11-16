app.controller('order_goods', ['$rootScope', '$scope', '$stateParams', '_ajax', function ($rootScope, $scope, $stateParams, _ajax) {
    console.log($rootScope);
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: 'merchant_index'
    }, {
        name: '商家管理',
        link: 'store_mag'
    }, {
        name: '订单管理',
        link: -1
    }, {
        name: '订单详情',
        link: -1
    }, {
        name: '商品详情'
    }];
    let params = {
        order_no: $stateParams.orderNo,
        sku: $stateParams.sku
    };

    _ajax.post('/order/goods-view', params, function (res) {
        console.log(res, '商品详情页');
        $scope.data = res.data
    });

    // 显示城市
    $scope.showCity = function () {
        $('#cityModal').modal('show');
    };
}]);