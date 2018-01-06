app.controller('basic_ctrl',function ($scope,$state,$stateParams,_ajax) {
    //初始化
    $scope.basic_materials = JSON.parse(sessionStorage.getItem('materials'))[$stateParams.index]
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
    $scope.goDetails = function () {
        $state.go('product_details',{index:$stateParams.index,status:0,id:$scope.goods_details.id})
    }
})