app.controller('mall_withdraw_detail_ctrl',function ($scope,$rootScope,$state,$stateParams,_ajax,$uibModal) {
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
            link: function () {
                $state.go('mall_account')
            }
        }, {
            name: '详情',
            link: function(){
                $state.go('mall_account_detail',{id:$stateParams.supplier_id})
            }
        }, {
            name: '提现列表',
            link:-1
        },{
            name:'详情'
        }
    ]
    _ajax.get('/supplieraccount/cashed-view',{
        id:$stateParams.id
    },function (res) {
        console.log(res);
        $scope.withdraw_detail = res.data
    })
    //返回前一页
    $scope.goPrev = function () {
        history.go(-1)
    }
})