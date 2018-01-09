app.controller('level_three_ctrl',function (_ajax,$scope,$state,$stateParams) {
    //初始化
    $scope.basic_materials = JSON.parse(sessionStorage.getItem('materials'))[$stateParams.index]
    //请求三级项数据
    _ajax.get('/mall/categories-level3',{
        pid:$scope.basic_materials.id
    },function (res) {
        console.log('三级项');
        console.log(res);
        $scope.level_three = res.categories_level3
    })
    //跳转商品列表页
    $scope.goDetails = function (item) {
        $state.go('product_list',{index:$stateParams.index,status:$stateParams.status,category_id:item.id,title:item.title})
    }
    //返回上一页
    $scope.goPrev = function () {
        $state.go('other_materials',{index:$stateParams.index})
    }
})