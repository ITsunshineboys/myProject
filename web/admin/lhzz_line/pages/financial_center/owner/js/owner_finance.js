app.controller('owner_finance_ctrl',function ($scope,_ajax) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '业主财务'
        }
    ]
    //获取页面数据
    _ajax.get('/supplier-cash/owner-cash-index',{},function (res) {
        console.log(res)
        $scope.owner_finance_data = res.data
        sessionStorage.setItem('owner_finance_data',JSON.stringify($scope.owner_finance_data))
    })
})