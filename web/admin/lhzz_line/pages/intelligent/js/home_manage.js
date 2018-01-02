app.controller('home_manage_ctrl',function ($http,$scope,_ajax,$rootScope,$state,$stateParams,$uibModal) {
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
            name: '首页管理'
        }
    ]
    let obj = JSON.parse(sessionStorage.getItem('area'))
    //请求省市数据
    $http.get('city.json').then(function (res) {
        console.log(res)
        $scope.province_name = res.data[0]['86'][obj.province]
        $scope.city_name = res.data[0][obj.province][obj.city]
    })
    //获取首页管理数据
    _ajax.get('/quote/homepage-list',{
        city_code:obj.city
    },function (res) {
        console.log(res);
        $scope.home_manage_list = res.list
    })
    //拖拽排序
    $scope.dropComplete = function (index, obj) {
        let idx = $scope.home_manage_list.indexOf(obj)
        $scope.home_manage_list[idx] = $scope.home_manage_list[index]
        $scope.home_manage_list[index] = obj
    }
    //首页推荐开启关闭
    $scope.saveStatus = function (item) {
        
    }
    //首页推荐删除
    //保存首页排序
})