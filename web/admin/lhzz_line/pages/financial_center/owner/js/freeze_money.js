app.controller('freeze_money_ctrl', function ($uibModal,$state, $stateParams, _ajax, $scope, $rootScope) {
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
            name: '账户管理',
            link: function () {
                $state.go('finance_account')
            }
        }, {
            name: '详情',
            link: -1
        }, {
            name: '冻结金额'
        }
    ]
    //请求可冻结金额
    _ajax.get('/supplieraccount/owner-freeze-money', {
        user_id: $stateParams.user_id
    }, function (res) {
        console.log(res)
        $scope.allow_freeze_money = res.data.freeze_money
    })
    //初始化页面数据
    $scope.params = {
        user_id: $stateParams.user_id,
        freeze_money: '',
        freeze_reason: ''
    }
    $scope.$watch('params.freeze_money',function (newVal,oldVal) {
        $scope.flag = false
    })
    //冻结金额
    $scope.goFreezeMoney = function (valid) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '冻结成功'

            $scope.common_house = function () {
                $uibModalInstance.close()
                $state.go('account_detail',{id:$stateParams.user_id})
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(valid){
            _ajax.post('/supplieraccount/owner-apply-freeze',$scope.params,function (res) {
                console.log(res)
                if(res.code == 1075){
                    $scope.flag = true
                }else{
                    $scope.flag = false
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                }
            })
        }
    }
    //返回前一页
    $scope.goPrev = function () {
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
                name: '账户管理',
                link: -1
            }, {
                name: '详情'
            }
        ]
        $state.go('account_detail',{id:$stateParams.user_id})
    }
})