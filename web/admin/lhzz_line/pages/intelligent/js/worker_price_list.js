app.controller('worker_price_ctrl',function ($http,$stateParams,_ajax,$scope,$state,$rootScope) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
            link: function () {
                $state.go('intelligent.intelligent_index')
                $rootScope.crumbs.splice(1, 4)
            }
        }, {
            name: '资费/做工标准'
        }
    ]
    $http.get('city.json').then(function (res) {
        console.log(res)
        $scope.province_name = res.data[0]['86'][$stateParams.province]
        $scope.city_name = res.data[0][$stateParams.province][$stateParams.city]
    })
    _ajax.get('/quote/labor-cost-list',{
        city:$stateParams.city
    },function (res) {
        console.log(res)
        $scope.worker_list = res.list
    })
})