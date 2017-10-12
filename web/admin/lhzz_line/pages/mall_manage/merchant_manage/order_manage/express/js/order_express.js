app.controller('express', ['$scope', '$stateParams', '_ajax', function ($scope, $stateParams, _ajax) {
    $scope.shopID = sessionStorage.getItem('shopID');   // 商家ID
    let params = {
        order_no: $stateParams.orderNo,
        sku: $stateParams.sku
    };
    _ajax.post('/order/getexpress', params, function (res) {
        console.log(res, '物流信息');
        $scope.data = res.data;
    })
}]);