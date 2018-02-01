app.controller('main_ctrl',function ($timeout,_ajax,$scope,$state,$stateParams) {
    //初始化
    $scope.basic_materials = JSON.parse(sessionStorage.getItem('copies'))[$stateParams.index]
    $scope.materials = JSON.parse(sessionStorage.getItem('copies'))
    let obj = JSON.parse(sessionStorage.getItem('params'))
    //获取商品详情
    $scope.getDetails = function (item) {
        console.log(item);
        $scope.goods_details = item
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
    //保存数据
    $scope.saveData = function () {
        $scope.materials[$stateParams.index] = $scope.basic_materials
        sessionStorage.removeItem('copies')
        //材料项保存
        if(sessionStorage.getItem('materials')!=null){
            sessionStorage.setItem('materials',JSON.stringify($scope.materials))
            $state.go('nodata')
        }else if(sessionStorage.getItem('quotation_materials')!=null){
            sessionStorage.setItem('quotation_materials',JSON.stringify($scope.materials))
            $state.go('modelRoom',{effect_id:obj.effect_id,id:obj.id})
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        sessionStorage.removeItem('copies')
        //材料项保存
        if(sessionStorage.getItem('materials')!=null){
            $state.go('nodata')
        }else if(sessionStorage.getItem('quotation_materials')!=null){
            $state.go('modelRoom',{effect_id:obj.effect_id,id:obj.id})
        }
    }
})