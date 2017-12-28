app.controller('else_general_ctrl', function ($uibModal,$scope, $rootScope, $state, $stateParams, $http, _ajax) {
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
            name: '通用管理',
            link: -1
        }, {
            name: '通用管理详情'
        }
    ]
    $scope.project_name = $stateParams.title
    $scope.regx = $stateParams.title == '杂工' ? /^\d{1,}(\.5)?$/ : /^\d{1,}(.\d{1,2})?$/
    //请求数据
    _ajax.get('/quote/commonality-else-list', {
        id: $stateParams.id
    }, function (res) {
        console.log(res);
        $scope.basic_data = res.list//除面积外数据
        $scope.area_data = []
        let arr = []
        for(let [key,value] of res.else_area.entries()){
            value['project_value'] = ''
            arr.push(value)
        }
        //面积数据
        for (let [key, value] of res.area.entries()) {
            let index = $scope.area_data.findIndex(function (item) {
                return item.project_name == value.project_name
            })
            if (index == -1) {
                $scope.area_data.push({
                    project_name: value.project_name,
                    else_area: angular.copy(arr)
                })
            }
        }
        for (let [key, value] of res.area.entries()) {
            for (let [key1, value1] of $scope.area_data.entries()) {
               let index = value1.else_area.findIndex(function (item) {
                   return item.min_area == value.min_area && item.max_area == value.max_area && value.project_name == value1.project_name
               })
                if(index != -1){
                   value1.else_area[index].id = value.id
                   value1.else_area[index].project_value = value.project_value
                }
            }
        }
        console.log($scope.area_data);
    })
    //保存数据
    $scope.saveData = function (valid) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        let arr = [],arr1 = []
        for(let [key,value] of $scope.basic_data.entries()){
            arr.push({
                id:value.id,
                coefficient:value.project_value
            })
        }
        for(let [key,value] of $scope.area_data.entries()){
            for(let [key1,value1] of value.else_area.entries()){
                if(value1.id!=undefined){
                    arr1.push({
                        id:value1.id,
                        value:value1.project_value
                    })
                }else{
                    arr1.push({
                        min_area: value1.min_area,
                        max_area: value1.max_area,
                        project_value: value1.project_value,
                        project_name: value.project_name,
                        points_id: $stateParams.id
                    })
                }
            }
        }
        if(valid){
            _ajax.post('/quote/commonality-else-edit',{
                value:arr,
                area:arr1
            },function (res) {
                console.log(res);
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
        history.go(-1)
    }
})