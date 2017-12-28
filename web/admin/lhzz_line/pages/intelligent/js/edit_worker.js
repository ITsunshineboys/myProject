app.controller('edit_worker_ctrl', function ($uibModal,$state,$stateParams, _ajax, $scope, $rootScope, $http) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
            link: function () {
                $state.go('intelligent_index')
                $rootScope.crumbs.splice(1, 4)
            }
        }, {
            name: '资费/做工标准',
            link: -1
        }, {
            name: '资费/做工标准详情'
        }
    ]
    let obj = JSON.parse(sessionStorage.getItem('area'))
    _ajax.get('/quote/labor-cost-edit-list',{
        id:$stateParams.id,
        city_code:obj.city,
        province_code:obj.province
    },function (res) {
        console.log(res)
        $scope.basic_data = res.labor_cost
        $scope.other_data = res.worker_craft_norm
    })
    //保存修改
    $scope.saveWorkerPrice = function (valid) {
        let arr = []
        for (let [key, value] of $scope.other_data.entries()) {
            arr.push({
                id: value.id,
                quantity: value.quantity
            })
        }
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(valid){
            let obj = {
                univalence:$scope.basic_data.univalence,
                city_code:obj.city,
                province_code:obj.province,
                else:arr
            }
            if($scope.basic_data.id!=undefined){
                Object.assign(obj,{
                    id:$scope.basic_data.id
                })
            }else{
                Object.assign(obj,{
                    worker_id:$scope.basic_data.worker_id
                })
            }
            if (valid) {
                _ajax.post('/quote/labor-cost-edit', obj, function (res) {
                    console.log(res)
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                })
            } else {
                $scope.submitted = true
            }
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        history.go(-1)
    }
})