app.controller('order', ['$rootScope', '$scope', '$state', '$stateParams', '_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家管理',
        link: 'store_mag'
    }, {
        name: '订单管理'
    }];

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