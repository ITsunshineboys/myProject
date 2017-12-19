app.controller('mall_freeze_ctrl',function ($scope,$rootScope,$state,$stateParams,_ajax,$uibModal) {
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
            link: -1
        }, {
            name: '详情',
            link: -1
        }, {
            name: '冻结金额'
        }
    ]
    $scope.all_cash = $stateParams.cash//账户余额
    $scope.freeze_obj = {
        freeze_money: '',
        freeze_reason: '',
        supplier_id: $stateParams.id
    }
    $scope.$watch('freeze_obj.freeze_money',function (newVal,oldVal) {
        if(+newVal > +$scope.all_cash){
            $scope.overrun = true
        }else{
            $scope.overrun = false
        }
    })
    //保存数据
    $scope.saveFreezeMoney = function (valid) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '冻结成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(valid&&!$scope.overrun){
            _ajax.post('/supplieraccount/apply-freeze',$scope.freeze_obj,function (res) {
                console.log(res);
                $scope.submitted = false
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            })
        }else{
            $scope.submitted = true
        }
    }
    //返回前一页
    $scope.goPrev = function () {
        $scope.submitted = false
        $scope.overrun = false
        history.go(-1)
    }
})