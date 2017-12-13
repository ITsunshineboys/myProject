app.controller('support_goods_ctrl',function ($uibModal,$state,$stateParams, _ajax, $scope, $rootScope, $http) {
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
            name: '案例/社区配套商品管理'
        }
    ]
    //获取分类
    _ajax.get('/quote/assort-goods', {}, function (res) {
        console.log(res)
        $scope.level_one = res.data.categories
        $scope.cur_level_one = $scope.level_one[0]
        _ajax.get('/quote/assort-goods', {
            pid: $scope.cur_level_one.id
        }, function (res) {
            console.log(res)
            $scope.level_two = res.data.categories
            $scope.cur_level_two = $scope.level_two[0]
            _ajax.get('/quote/assort-goods', {
                pid: $scope.cur_level_two.id
            }, function (res) {
                console.log(res)
                $scope.level_three = res.data.categories
                $scope.cur_level_three = $scope.level_three[0]
            })
        })
    })
        $scope.getCategory = function (item,index) {
            if(index == 1){
                _ajax.get('/quote/assort-goods', {
                    pid: item.id
                }, function (res) {
                    console.log(res)
                    $scope.level_two = res.data.categories
                    $scope.cur_level_two = $scope.level_two[0]
                    _ajax.get('/quote/assort-goods', {
                        pid: $scope.cur_level_two.id
                    }, function (res) {
                        console.log(res)
                        $scope.level_three = res.data.categories
                        $scope.cur_level_three = $scope.level_three[0]
                    })
                })
            }else if(index == 2){

            }else{

            }
        }
})