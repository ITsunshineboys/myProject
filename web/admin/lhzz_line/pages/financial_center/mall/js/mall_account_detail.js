app.controller('mall_account_detail_ctrl',function ($scope,$stateParams,$rootScope,$state,$uibModal,_ajax) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '商城财务',
            link: function () {
                $state.go('mall_finance')
            }
        }, {
            name: '账户管理',
            link:function () {
                $state.go('mall_account')
            }
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
    $scope.goPrev = function () {
        $state.go('mall_account')
    }
})