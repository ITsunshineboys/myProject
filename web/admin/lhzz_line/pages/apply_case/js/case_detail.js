app.controller('case_detail_ctrl', function ($rootScope,_ajax, $stateParams, $state, $scope, $uibModal) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '装修申请',
            icon: 'icon-yangbanjian',
            link: -1
        }, {
            name: '详情'
        }
    ]
    //获取详情
    _ajax.post('/effect/effect-view',{
        id:$stateParams.id
    },function (res) {
        console.log(res)
        $scope.particulars_view = res.data.particulars_view
        let material = res.data.material
        $scope.material = []
        for(let [key,value] of Object.entries(material)){
            $scope.material.push({
                first_level:key,
                goods:value,
                flag:false
            })
        }
        console.log($scope.material)
    })
    let all_modal = function ($scope, $uibModalInstance) {
        $scope.cur_title = '保存成功'

        $scope.common_house = function () {
            $uibModalInstance.close()
            $state.go('apply_case_index')
        }
    }
    all_modal.$inject = ['$scope', '$uibModalInstance']
    //编辑备注
    $scope.editRemark = function () {
        _ajax.post('/effect/effect-view',{
            id:$scope.particulars_view.id,
            remark:$scope.particulars_view.remark
        },function (res) {
            console.log(res)
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: all_modal
            })
        })
    }
    //返回前一页
    $scope.goPrev = function () {
        $state.go('apply_case_index')
    }
})