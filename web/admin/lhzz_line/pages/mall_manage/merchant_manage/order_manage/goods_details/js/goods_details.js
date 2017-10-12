app.controller('order_goods', ['$scope', '$stateParams', '_ajax', function ($scope, $stateParams, _ajax) {
    $scope.shopID = sessionStorage.getItem('shopID');
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