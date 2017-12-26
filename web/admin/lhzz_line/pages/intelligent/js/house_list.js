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
            $scope.params.district_code!=''?tablePages():'';
        }
    }
    let tablePages = function () {
        $scope.params.page = $scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/quote/effect-plot-list', $scope.params, function (res) {
            console.log(res);
            $scope.house_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.params = {
        district_code: '',
        start_time: '',
        end_time: '',
        keyword: ''
    };
    //获取区列表
    $http.get('city.json').then(function (res) {
        console.log(res)
        $scope.province_name = res.data[0]['86'][$stateParams.province]
        $scope.city_name = res.data[0][$stateParams.province][$stateParams.city]
        $scope.city_code = $stateParams.city
        $scope.province_code = $stateParams.province
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
        $scope.params.district_code = $scope.region_options[0].region_code
        tablePages()
    })
        //获取小区列表
        $scope.getHouseList = function (index) {
            if($scope.params.district_code!=''){
                if(index == 1){
                    $scope.params.district_code = $scope.region_options[0].region_code
                    $scope.keyword = ''
                    $scope.params.keyword = ''
                    tablePages()
                }else{
                    $scope.params.start_time = ''
                    $scope.params.end_time = ''
                    $scope.keyword = ''
                    $scope.params.keyword = ''
                    tablePages()
                }
            }
        }
        $scope.$watch('keyword',function (newVal,oldVal) {
            if(newVal == ''&&oldVal!=''&&$scope.params.district_code!=''){
                // $scope.params.post = $scope.region_options[0].region_code
                $scope.params.start_time = ''
                $scope.params.end_time = ''
                $scope.params.keyword = ''
                tablePages()
            }
        })
        $scope.getKeywordHouseList = function () {
            if($scope.keyword!=''){
                $scope.params.district_code = $scope.region_options[0].region_code
                $scope.params.start_time = ''
                $scope.params.end_time = ''
                $scope.params.keyword = $scope.keyword
                tablePages()
            }
        }
        //删除小区
    $scope.deleteItem = function (item) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '是否删除小区？'
            $scope.is_cancel = true
            $scope.common_house = function () {
                _ajax.post('/quote/effect-del-plot', {
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
        all_modal.$inject = ['$scope', '$uibModalInstance']
        //弹出保存成功模态框
        $uibModal.open({
            templateUrl: 'pages/intelligent/cur_model.html',
            controller: all_modal
        })
    }
})