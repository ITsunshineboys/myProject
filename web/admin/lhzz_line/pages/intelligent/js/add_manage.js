app.controller('add_manage_ctrl', function ($scope, $rootScope, _ajax, $http, $state, $stateParams, $uibModal) {
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
            name: '首页管理',
            link: -1
        }, {
            name: $stateParams.index == 1 ? '编辑推荐' : '添加推荐'
        }
    ]
    $scope.recommend_name = ''
    $scope.vm = $scope
    let obj = JSON.parse(sessionStorage.getItem('area'))
    _ajax.get('/quote/homepage-district',{
        province:obj.province,
        city:obj.city
    },function (res) {
        console.log(res);
        $scope.district = res.list
        $scope.choose_district = $scope.district[0].district_code
        _ajax.post('/quote/homepage-toponymy',{
            province:obj.province,
            city:obj.city,
            district:$scope.choose_district
        },function (res) {
            console.log(res);
        })
    })
})