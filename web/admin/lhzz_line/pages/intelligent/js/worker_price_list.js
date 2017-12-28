app.controller('worker_price_ctrl',function ($http,$stateParams,_ajax,$scope,$state,$rootScope) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
            link: function () {
                $state.go('intelligent_index')
                $rootScope.crumbs.splice(1, 4)
            }
        }, {
            name: '资费/做工标准'
        }
    ]
    $http.get('city.json').then(function (res) {
        console.log(res)
        let obj = JSON.parse(sessionStorage.getItem('area'))
        $scope.province_name = res.data[0]['86'][obj.province]
        $scope.city_name = res.data[0][obj.province][obj.city]
    })
    _ajax.get('/quote/labor-cost-list',{},function (res) {
        console.log(res)
        $scope.worker_list = res.list
    })
})