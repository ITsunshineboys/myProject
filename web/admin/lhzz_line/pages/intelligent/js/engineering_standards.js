app.controller('engineering_standards_ctrl',function ($uibModal,$state,$stateParams, _ajax, $scope, $rootScope, $http) {
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
            name: '工程标准',
        }
    ]
    let obj = JSON.parse(sessionStorage.getItem('area'))
    //请求省市数据
    $http.get('city.json').then(function (res) {
        console.log(res)
        $scope.province_name = res.data[0]['86'][obj.province]
        $scope.city_name = res.data[0][obj.province][obj.city]
    })
    //请求工程标准列表
    _ajax.get('/quote/project-norm-list',{
        city:obj.city
    },function (res) {
        console.log(res)
        $scope.process_list = res.list
    })
    //跳转详情
    $scope.goEngineeringDetail = function (item) {
        $state.go('engineering_process',{id:item.id,project:item.project})
    }
})