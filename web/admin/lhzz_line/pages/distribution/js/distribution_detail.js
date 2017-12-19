app.controller('distribution_detail_ctrl',function ($scope,$rootScope,$state,$stateParams,$uibModal,_ajax) {
    //面包屑
    $rootScope.crumbs = [
        {
            name:'分销',
            icon:'icon-fenxiao',
            link:-1
        },{
        name:'详情'
        }
    ]
    $scope.vm = $scope
    $scope.mobile = $stateParams.mobile
    _ajax.post('/distribution/getdistributiondetail',{
        mobile:$stateParams.mobile
    },function (res) {
        console.log(res);
        $scope.fatherset = res.data.fatherset
        $scope.myself = res.data.myself
        $scope.profit = res.data.profit
        $scope.subset = res.data.subset
    })
    //保存数据
    $scope.saveProfit = function (valid) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(valid){
            _ajax.post('/distribution/add-profit',{
                mobile:$stateParams.mobile,
                profit:$scope.profit
            },function (res) {
                console.log(res)
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
    //返回上一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})