app.controller('add_material_ctrl',function ($rootScope,$scope,$stateParams,$state,$uibModal,$http,_ajax) {
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
            name: '添加材料项',
        }
    ]
    $scope.status_words = '刷新材料抓取'
    $scope.city = $stateParams.city
    //请求省市数据
    $http.get('city.json').then(function (res) {
        console.log(res)
        $scope.province_name = res.data[0]['86'][$stateParams.province]
        $scope.city_name = res.data[0][$stateParams.province][$stateParams.city]
    })
    //分页配置
    $scope.Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    }
    let tablePages = function () {
        $scope.params.page = $scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/quote/decoration-list', $scope.params, function (res) {
            console.log(res);
            $scope.material_list = res.list.details
            $scope.Config.totalItems = res.list.size
        })
    };
    $scope.params = {
        city:$stateParams.city
    };
    //删除项
    $scope.deleteMaterial = function (item) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '是否确认删除'
            $scope.is_cancel = true
            $scope.common_house = function () {
                _ajax.post('/quote/decoration-del', {
                    id: item.id
                }, function (res) {
                    console.log(res)
                    tablePages()
                    $uibModalInstance.close()
                    $scope.is_cancel = false
                })
            }
            $scope.cancel_delete = function () {
                $uibModalInstance.close()
                $scope.is_cancel = false
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        $uibModal.open({
            templateUrl: 'pages/intelligent/cur_model.html',
            controller: all_modal
        })
    }
    //一键刷新
    $scope.refresh = function () {
        $scope.status_words = '刷新中...'
        _ajax.get('/quote/decoration-up',{},function (res) {
            if(res.code == 200){
                $scope.status_words = '刷新材料抓取'
            }
        })
    }
})