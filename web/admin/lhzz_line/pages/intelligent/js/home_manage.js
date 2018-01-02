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
        _ajax.get('/quote/homepage-status',{
            id:item.id,
            status:item.status == 0?1:0
        },function (res) {
            console.log(res);
            _ajax.get('/quote/homepage-list',{
                city_code:obj.city
            },function (res) {
                console.log(res);
                $scope.home_manage_list = res.list
            })
        })
    }
    //首页推荐删除
    $scope.deleteManage = function (item) {
        let vm = $scope
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.is_cancel = true
            $scope.cur_title = '是否删除'
            $scope.cancel_delete = function () {
                $uibModalInstance.close()
                $scope.is_cancel = false
            }
            $scope.common_house = function () {
                _ajax.post('/quote/homepage-delete',{
                    id:item.id
                },function (res) {
                    console.log(res);
                    _ajax.get('/quote/homepage-list',{
                        city_code:obj.city
                    },function (res) {
                        console.log(res);
                        $uibModalInstance.close()
                        vm.home_manage_list = res.list
                    })
                })
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(item.status == 0){
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: all_modal
            })
        }
    }
    //保存首页排序
    $scope.saveSort = function () {
        let arr = []
        for(let [key,value] of $scope.home_manage_list.entries()){
            arr.push({
                id:value.id,
                sort:key
            })
        }
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        _ajax.post('/quote/homepage-sort',{
            sort:arr
        },function (res) {
            console.log(res);
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: all_modal
            })
        })
    }
})