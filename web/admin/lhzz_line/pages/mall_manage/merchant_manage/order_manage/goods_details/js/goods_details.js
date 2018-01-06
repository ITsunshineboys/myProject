app.controller('order_goods', ['$rootScope', '$scope', '$stateParams', '_ajax', function ($rootScope, $scope, $stateParams, _ajax) {
    let fromStateName = $rootScope.fromState_name;
    if (fromStateName !== '') {
        sessionStorage.setItem('fromStateName', fromStateName);
    }
    switch (sessionStorage.getItem('fromStateName')) {
        case 'comm_details':
            $rootScope.crumbs = [{
                name: '商城管理',
                icon: 'icon-shangchengguanli',
                link: $rootScope.mall_click
            }, {
                name: '商家管理',
                link: 'store_mag'
            }, {
                name: '订单管理',
                link: 'order.all',
                params: {id: sessionStorage.getItem('shopID')}
            }, {
                name: '删除评论',
                link: -2
            }, {
                name: '删除评论详情',
                link: -1
            }, {
                name: '商品详情'
            }];
            break;
        default:
            if(sessionStorage.getItem('fromState') === 'search.order') {
                $rootScope.crumbs = [{
                    name: '商城管理',
                    icon: 'icon-shangchengguanli',
                    link: $rootScope.mall_click
                }, {
                    name: '搜索',
                    link: 'search.order'
                }, {
                    name: '订单详情',
                    link: -1
                }, {
                    name: '商品详情'
                }];
            } else {
                $rootScope.crumbs = [{
                    name: '商城管理',
                    icon: 'icon-shangchengguanli',
                    link: $rootScope.mall_click
                }, {
                    name: '商家管理',
                    link: 'store_mag'
                }, {
                    name: '订单管理'
                }, {
                    name: '订单详情',
                    link: -1
                }, {
                    name: '商品详情'
                }];
            }
    }
    let params = {
        order_no: $stateParams.orderNo,
        sku: $stateParams.sku
    };

    _ajax.get('/order/goods-view', params, function (res) {
        console.log(res, '商品详情页');
        $scope.data = res.data
    });

    // 显示城市
    $scope.showCity = function () {
        $('#cityModal').modal('show');
    };
}]);