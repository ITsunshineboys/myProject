app.controller('mall_finance_ctrl',function ($scope,$rootScope,$state,$stateParams,_ajax,$uibModal) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '商城财务'
        }
    ]
    _ajax.get('/supplier-cash/cash-index',{},function (res) {
        console.log(res);
        $scope.cash_data = res.data
        sessionStorage.setItem('mall_finance_data',JSON.stringify($scope.cash_data))
    })
})