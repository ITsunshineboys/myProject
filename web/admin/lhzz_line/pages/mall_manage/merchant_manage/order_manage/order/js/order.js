app.controller('order', ['$scope', '$state', '$stateParams', '_ajax', function ($scope, $state, $stateParams, _ajax) {
    $scope.id = $stateParams.id;    // 商家ID
    $scope.showDel = $state.is('order.all');    // 判断是否处于全部订单位置
    $scope.show = function (bool) {     // 显示删除评论按钮
        $scope.showDel = !!bool;
    };

    // 显示订单数量
    _ajax.get('/order/get-order-num', {supplier_id: $scope.id}, function (res) {
        $scope.orderNum = res.data
    })
}]);