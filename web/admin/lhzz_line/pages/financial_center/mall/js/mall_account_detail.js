app.controller('mall_account_detail_ctrl',function ($scope,$stateParams,$rootScope,$state,$uibModal,_ajax) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '商城财务',
            link: -1
        }, {
            name: '账户管理',
            link:-1
        },{
        name:'详情'
        }
    ]
    //请求页面数据
    _ajax.get('/supplieraccount/account-view',{
        id:$stateParams.id
    },function (res) {
        console.log(res);
        $scope.account_detail = res.data
    })
})