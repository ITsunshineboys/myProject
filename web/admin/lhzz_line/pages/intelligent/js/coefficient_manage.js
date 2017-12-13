app.controller('coefficient_manage_ctrl', function ($uibModal, $state, $stateParams, _ajax, $scope, $rootScope, $http) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
            link: function () {
                $state.go('intelligent.intelligent_index')
                $rootScope.crumbs.splice(1, 4)
            }
        }, {
            name: '系数管理',
        }
    ]
    //请求省市数据
    $http.get('districts2.json').then(function (res) {
        console.log(res)
        $scope.province_name = res.data[0]['86'][$stateParams.province]
        $scope.city_name = res.data[0][$stateParams.province][$stateParams.city]
    })
    //请求列表数据
    _ajax.get('/quote/coefficient-list', {
        city: $stateParams.city
    }, function (res) {
        console.log(res)
        $scope.all_coefficient = []
        $scope.all_category = angular.copy(res.list)
        for (let [key, value] of res.coefficient.entries()) {
            let arr1 = $scope.all_category
            for(let [key1,value1] of $scope.all_coefficient.entries()){
                let index = arr1.findIndex(function (item) {
                    return value1.category_id == item.id
                })
                arr1.splice(index,1)
            }
            $scope.all_coefficient.push({
                category_id: value.category_id,
                coefficient: value.coefficient,
                options: arr1
            })
        }
    })
    //添加项
    $scope.addCoefficient = function () {
        console.log($scope.all_coefficient)
        let arr1 = angular.copy($scope.all_category)
        for(let [key,value] of $scope.all_coefficient.entries()){
            let index = arr1.findIndex(function (item) {
                return value.category_id == item.id
            })
            arr1.splice(index,1)
        }
        $scope.all_coefficient.push({
            category_id: arr1[0].id,
            coefficient: '',
            options: arr1
        })
    }
    //删除项
    $scope.removeCoefficient = function (item) {
        let arr1 = angular.copy($scope.all_category)
        let index = $scope.all_coefficient.findIndex(function (item1) {
            return item1.category_id == item.category_id
        })
        let index1 = arr1.findIndex(function (item1) {
            return item1.id == item.category_id
        })
        for(let [key,value] of $scope.all_coefficient.entries()){
            if(key>index){
                value.options.push(arr1[index1])
            }
        }
        $scope.all_coefficient.splice(index,1)
    }
    //保存系数
    $scope.saveCoefficient = function (valid) {
        let arr = []
        for(let [key,value] of $scope.all_coefficient.entries()){
            arr.push({
                id:value.category_id,
                value:value.coefficient
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
            _ajax.post('/quote/coefficient-add',{
                value:arr,
                city:$stateParams.city
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
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})