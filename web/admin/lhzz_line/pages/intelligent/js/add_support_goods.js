app.controller('support_goods_ctrl', function ($uibModal, $state, $stateParams, _ajax, $scope, $rootScope, $http) {
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
    //请求省市数据
    $http.get('city.json').then(function (res) {
        console.log(res)
        $scope.province_name = res.data[0]['86'][$stateParams.province]
        $scope.city_name = res.data[0][$stateParams.province][$stateParams.city]
    })
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
                //获取列表
                _ajax.get('/quote/assort-goods-list',{
                    city:$stateParams.city
                },function (res) {
                    console.log(res)
                    $scope.support_goods_list = res.list
                    for(let [key,value] of $scope.level_three.entries()){
                        let index = $scope.support_goods_list.findIndex(function (item) {
                            return item.id == value.id
                        })
                        if(index == -1){
                            value.flag = false
                        }else{
                            value.flag = true
                        }
                    }
                })

            })
        })
    })
    //改变分类
    $scope.getCategory = function (item, index) {
        if (index == 1) {
            $scope.cur_level_one = item
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
                    // $scope.cur_level_three = $scope.level_three[0]
                    for(let [key,value] of $scope.level_three.entries()){
                        let index = $scope.support_goods_list.findIndex(function (item) {
                            return item.id == value.id
                        })
                        if(index == -1){
                            value.flag = false
                        }else{
                            value.flag = true
                        }
                    }
                })
            })
        } else {
            $scope.cur_level_two = item
            _ajax.get('/quote/assort-goods', {
                pid: item.id
            }, function (res) {
                console.log(res)
                $scope.level_three = res.data.categories
                // $scope.cur_level_three = $scope.level_three[0]
                for(let [key,value] of $scope.level_three.entries()){
                    let index = $scope.support_goods_list.findIndex(function (item) {
                        return item.id == value.id
                    })
                    if(index == -1){
                        value.flag = false
                    }else{
                        value.flag = true
                    }
                }
            })
        }
    }
    //选择项
    $scope.chooseCategory = function (item) {
        console.log(item)
        if(item.flag == false){
            console.log(1);
            let index = $scope.support_goods_list.findIndex(function (item1) {
                return item.id == item1.id
            })
            index!=-1?$scope.support_goods_list.splice(index,1):''
        }else{
            console.log(2);
            item.quantity = ''
            $scope.support_goods_list.push(item)
        }
    }
    //删除项
    $scope.removeCategory = function (item) {
        let index = $scope.support_goods_list.findIndex(function (item1) {
            return item1.id == item.id
        })
        let index1 = $scope.level_three.findIndex(function (item1) {
            return item1.id == item.id
        })
        $scope.support_goods_list.splice(index,1)
        $scope.level_three[index1].flag = false
    }
    //保存数据
    $scope.saveCategory = function (valid) {
        console.log($scope.support_goods_list);
        let arr = []
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        for(let [key,value] of $scope.support_goods_list.entries()){
            arr.push({
                id:value.id,
                path:value.path,
                pid:value.pid,
                title:value.title,
                quantity:value.quantity
            })
        }
        if(valid){
            _ajax.post('/quote/assort-goods-add',{
                city:$stateParams.city,
                assort:arr
            },function (res) {
                $scope.submitted = false
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            })
        }else{
            $scope.submitted = true
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})