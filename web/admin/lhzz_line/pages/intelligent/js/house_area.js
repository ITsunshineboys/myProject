app.controller('house_area_ctrl',function ($rootScope,_ajax,$http,$state,$stateParams,$scope,$uibModal) {
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
            name: '通用管理',
            link: -1
        }, {
            name: '户型面积'
        }
    ]
    let arr =[{min_area:1,max_area:180}]
    $scope.error_tips = ''
    //请求数据
    _ajax.get('/quote/commonality-else-list', {
        id: $stateParams.id
    }, function (res) {
        console.log(res);
        $scope.area_list = res.apartment_area
        //计算范围
        for(let [key,value] of $scope.area_list.entries()){
            let index = arr.findIndex(function (item) {
                return item.min_area<value.min_area&&item.max_area>value.max_area
            })
            let index1 = arr.findIndex(function (item) {
                return item.min_area==value.min_area&&item.max_area>value.max_area
            })
            let index2 = arr.findIndex(function (item) {
                return item.min_area<value.min_area&&item.max_area==value.max_area
            })
            let index3 = arr.findIndex(function (item) {
                return item.min_area==value.min_area&&item.max_area==value.max_area
            })
            if(index != -1){
                arr.push({
                    min_area:arr[index].min_area,
                    max_area:+value.min_area-1
                },{
                    min_area:+value.max_area + 1,
                    max_area:arr[index].max_area
                })
                arr.splice(index,1)
            }
            if(index1 != -1){
                arr.push({
                    min_area:+value.max_area + 1,
                    max_area:arr[index1].max_area
                })
                arr.splice(index1,1)
            }
            if(index2 != -1){
                arr.push({
                    min_area:arr[index2].min_area,
                    max_area:+value.min_area-1
                })
                arr.splice(index2,1)
            }
            if(index3 != -1){
                arr.splice(index3,1)
            }
        }
    })
    //增加户型面积
    $scope.addArea = function () {
        let index = arr.findIndex(function (item) {
            return item.min_area!=item.max_area
        })
        if(index==-1){

        }else{
            $scope.error_tips = ''
            $scope.area_list.push({
                min_area:'',
                max_area:''
            })
        }
    }
    //去除面积范围
    $scope.removeArea = function (item,index) {
        let index1 = arr.findIndex(function (item1) {
            return +item1.max_area + 1 == item.min_area
        })
        if(index1!=-1){
            arr.push({
                min_area:arr[index1].min_area,
                max_area:item.max_area
            })
            arr.splice(index1,1)
        }
        let index2 = arr.findIndex(function (item1) {
            return +item1.min_area - 1 == item.max_area
        })
        if(index2!=-1){
            arr.push({
                min_area:item.min_area,
                max_area:arr[index2].max_area
            })
            arr.splice(index2,1)
        }
        if(index1 == -1&&index2 == -1){
            arr.push({
                min_area:item.min_area,
                max_area:item.max_area
            })
        }
        $scope.area_list.splice(index,1)
        console.log(arr);
        console.log($scope.area_list);
    }
    //验证规则
    $scope.checkItem = function (item) {
        let index = arr.findIndex(function (item1) {
            return item1.min_area<=item.min_area&&item1.max_area>=item.max_area
        })
        if(!/^[1-9]\d{0,1}$|^1[0-7]\d{1}$|^180$/.test(item.min_area)||!/^[1-9]\d{0,1}$|^1[0-7]\d{1}$|^180$/.test(item.max_area)){
            $scope.error_tips = '*请填写0＜X≤180的整数字'
        }
        if(index == -1){
            $scope.error_tips = '*请不要填写已添加范围内的数值'
        }
        if(item.min_area > item.max_area){
            $scope.error_tips = '*请填写的最大面积大于最小面积'
        }else{
            $scope.error_tips = ''
        }
    }
    //保存数据
    $scope.saveData = function () {
        let arr = []
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        for(let [key,value] of $scope.area_list.entries()){
            arr.push({
                add_id:$stateParams.id,
                min_area:value.min_area,
                max_area:value.max_area
            })
        }
        if($scope.error_tips == ''){
            _ajax.post('/quote/commonality-else-edit',{
                apartment_area: arr.sort(function (a, b) {
                    return +a.min_area - b.min_area
                })
            },function (res) {
                console.log(res);
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            })
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        $scope.error_tips = ''
        history.go(-1)
    }
})