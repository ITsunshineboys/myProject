app.controller('other_ctrl', function ($scope, $state, $stateParams, _ajax, $timeout) {
    //初始化
    $scope.materials = JSON.parse(sessionStorage.getItem('copies'))
    $scope.basic_materials = angular.copy($scope.materials)[$stateParams.index]
    $scope.header_title = $scope.basic_materials.title
    $scope.edit_status = 0
    $scope.modal_status = 'modal'
    //切换编辑状态
    $scope.changeEditStatus = function () {
        $scope.edit_status = $scope.edit_status == 0 ? 1 : 0
        $scope.modal_status = $scope.edit_status == 0?'modal':''
    }
    //删除项
    $scope.deleteItem = function (item) {
        console.log(item);
        for (let [key, value] of $scope.basic_materials.second_level.entries()) {
            let index = value.goods.findIndex(function (item1) {
                return item1.id == item.id
            })
            if (index != -1) {
                $scope.basic_materials.cost -= value.goods[index].cost
                value.cost -= value.goods[index].cost
                $scope.basic_materials.procurement -= value.goods[index].procurement
                value.procurement -= value.goods[index].procurement
                $scope.basic_materials.count--
                value.goods.splice(index, 1)
            }
        }
        for (let [key, value] of $scope.basic_materials.second_level.entries()) {
            if (value.cost == 0) {
                $scope.basic_materials.second_level.splice(key, 1)
                key--
            }
        }
        $scope.materials[$stateParams.index] = $scope.basic_materials
        sessionStorage.setItem('copies', JSON.stringify($scope.materials))
    }
    //获取商品详情
    $scope.getDetails = function (item) {
        console.log(item);
        $scope.goods_details = item
        //系列名称
        // let index = $scope.series.findIndex(function (item) {
        //     return item.id == $scope.goods_details.series_id
        // })
        // $scope.goods_details.series_name = (index == -1?'':$scope.series[index].series)
        // //风格名称
        // let index1 = $scope.style.findIndex(function (item) {
        //     return item.id == $scope.goods_details.style_id
        // })
        // $scope.goods_details.style_name = (index1 == -1?'':$scope.style[index].style)
    }
    //跳转详情页
    $scope.goDetails = function (index) {
        if(index == 1){
            $timeout(function () {
                $state.go('product_list',{index:$stateParams.index,status:1,id:$scope.goods_details.id,category_id:$scope.goods_details.category_id,title:$scope.goods_details.title})
            },300)
        }else{
            $timeout(function () {
                $state.go('product_details',{index:$stateParams.index,status:0,id:$scope.goods_details.id})
            },300)
        }
    }
    //跳转三级页
    $scope.goLevelThree = function () {
        $state.go('level_three',{status:2,index:$stateParams.index})
    }
    //保存数据
    $scope.saveData = function () {
        $scope.materials = JSON.parse(sessionStorage.getItem('materials'))
        $scope.materials[$stateParams.index] = $scope.basic_materials
        sessionStorage.setItem('materials',JSON.stringify($scope.materials))
        sessionStorage.removeItem('copies')
        $state.go('nodata')
    }
    //返回上一页
    $scope.goPrev = function () {
        sessionStorage.removeItem('copies')
        $state.go('nodata')
    }
})