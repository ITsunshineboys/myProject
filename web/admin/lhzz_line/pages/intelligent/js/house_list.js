app.controller('house_list_ctrl',function ($scope,$state,$stateParams,$uibModal,$http,$rootScope,_ajax) {
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
            name: '小区列表页',
        }
    ]
    $scope.vm = $scope
    $scope.keyword = ''
    //分页配置
    $scope.Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            $scope.params.post!=''?tablePages():'';
        }
    }
    let tablePages = function () {
        $scope.params.page = $scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/quote/plot-list', $scope.params, function (res) {
            console.log(res);
            $scope.house_list = res.model.details
            $scope.Config.totalItems = res.model.size
        })
    };
    $scope.params = {
        post: '',
        min: '',
        max: '',
        toponymy: ''
    };
    //获取区列表
    $http.get('city.json').then(function (res) {
        console.log(res)
        $scope.province_name = res.data[0]['86'][$stateParams.province]
        $scope.city_name = res.data[0][$stateParams.province][$stateParams.city]
        $scope.city_code = $stateParams.city
        let arr = res.data[0][$stateParams.city]
        $scope.region_options = []
        for(let [key,value] of Object.entries(arr)){
            $scope.region_options.push({
                region_code:key,
                region_name:value
            })
        }
        $scope.region_options.unshift({
            region_code:$stateParams.city,
            region_name:'全市'
        })
        $scope.params.post = $scope.region_options[0].region_code
        tablePages()
    })
        //获取小区列表
        $scope.getHouseList = function (index) {
            if($scope.params.post!=''){
                if(index == 1){
                    $scope.params.post = $scope.region_options[0].region_code
                    $scope.keyword = ''
                    $scope.params.toponymy = ''
                    tablePages()
                }else{
                    $scope.params.min = ''
                    $scope.params.max = ''
                    $scope.keyword = ''
                    $scope.params.toponymy = ''
                    tablePages()
                }
            }
        }
        $scope.$watch('keyword',function (newVal,oldVal) {
            if(newVal == ''&&oldVal!=''&&$scope.params.post!=''){
                // $scope.params.post = $scope.region_options[0].region_code
                $scope.params.min = ''
                $scope.params.max = ''
                $scope.params.toponymy = ''
                tablePages()
            }
        })
        $scope.getKeywordHouseList = function () {
            if($scope.keyword!=''){
                $scope.params.post = $scope.region_options[0].region_code
                $scope.params.min = ''
                $scope.params.max = ''
                $scope.params.toponymy = $scope.keyword
                tablePages()
            }
        }
        //删除小区
    $scope.deleteItem = function (item) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '是否删除小区？'
            $scope.is_cancel = true
            $scope.common_house = function () {
                _ajax.post('/quote/plot-del', {
                    del_id: item.id
                }, function (res) {
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
        //弹出保存成功模态框
        $uibModal.open({
            templateUrl: 'pages/intelligent/cur_model.html',
            controller: all_modal
        })
    }
})