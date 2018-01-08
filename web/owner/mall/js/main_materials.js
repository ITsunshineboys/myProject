app.controller('main_ctrl',function ($timeout,_ajax,$scope,$state,$stateParams) {
    //初始化
    $scope.basic_materials = JSON.parse(sessionStorage.getItem('copies'))[$stateParams.index]
    //请求风格、系列
    _ajax.get('/owner/series-and-style', {}, function (res) {
        console.log(res);
        $scope.series = res.data.show.series//系列
        $scope.style = res.data.show.style//风格
    })
    //获取商品详情
    $scope.getDetails = function (item) {
        console.log(item);
        $scope.goods_details = item
        //系列名称
        let index = $scope.series.findIndex(function (item) {
            return item.id == $scope.goods_details.series_id
        })
        $scope.goods_details.series_name = (index == -1?'':$scope.series[index].series)
        //风格名称
        let index1 = $scope.style.findIndex(function (item) {
            return item.id == $scope.goods_details.style_id
        })
        $scope.goods_details.style_name = (index1 == -1?'':$scope.style[index].style)
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