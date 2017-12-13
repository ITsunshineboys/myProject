app.controller('withdraw_detail_ctrl', function ($uibModal,$rootScope, $state, $stateParams, _ajax, $scope) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '业主财务',
            link: function () {
                $state.go('owner_finance')
            }
        }, {
            name: '业主提现管理',
            link: -1
        }, {
            name: '业主提现管理详情'
        }
    ]
    $scope.all_status = [
        {num: 3, status: '驳回'},
        {num: 2, status: '提现'}
    ]
    // 接口数据初始化
    $scope.params = {
        cash_id: '',
        status: $scope.all_status[0].num,
        real_money: '',
        reason: ''
    }
    // 请求详情数据
    _ajax.get('/supplier-cash/owner-cashed-detail', {
        transaction_no: $stateParams.transaction_no
    }, function (res) {
        console.log(res)
        $scope.all_withdraw_detail = res.data
    })
    let all_modal = function ($scope, $uibModalInstance) {
        $scope.cur_title = '提交成功'

        $scope.common_house = function () {
        $uibModalInstance.close()
        sessionStorage.getItem('index')==null?$state.go('withdraw_manage'):history.go(-1)
    }
}
all_modal.$inject = ['$scope', '$uibModalInstance']

    //保存提现状态
    $scope.saveWithdraw = function (valid) {
        if(valid){
            $scope.params.cash_id = $scope.all_withdraw_detail.id
            _ajax.post('/supplier-cash/owner-do-cash-deal',$scope.params,function (res) {
                console.log(res)
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            })
        }
    }
    // 返回上一页
    $scope.goPrev = function () {
        history.go(-1)
    }
})