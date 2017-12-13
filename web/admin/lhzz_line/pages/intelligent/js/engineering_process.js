app.controller('engineering_process_ctrl',function ($uibModal,$state,$stateParams, _ajax, $scope, $rootScope, $http) {
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
            name: '工程标准',
            link: -1
        },{
            name:$stateParams.project
        }
    ]
    let arr = [],arr1 = [],arr2 = [],arr3 = []
    console.log($stateParams)
    $scope.project_name = $stateParams.project
    //风格、系列以及楼梯结构
    _ajax.post('/quote/series-and-style', {}, function (res) {
        console.log(res)
        $scope.all_series = res.series
        $scope.all_style = res.style
    })
    //请求基本数据
    _ajax.get('/quote/project-norm-edit-list',{
        city:$stateParams.city,
        project:$stateParams.project
    },function (res) {
        console.log(res)
        $scope.cur_process_list = []
        if($stateParams.project == '杂工工艺'){
            //水泥整合
            for(let [key,value] of res.list.entries()){
                if(value.project_details.indexOf('水泥')!=-1){
                    arr.push(value)
                }
            }
            //河沙整合
            for(let [key,value] of res.list.entries()){
                if(value.project_details.indexOf('河沙')!=-1){
                    arr1.push(value)
                }
            }
            //其它整合
            for(let [key,value] of res.list.entries()){
                if(value.project_details.indexOf('水泥')==-1&&value.project_details.indexOf('河沙')==-1){
                    arr2.push(value)
                }
            }
            $scope.cur_process_list = [arr,arr1,arr2]
        }else if($stateParams.project == '木作工艺'){
            //细木工板整合
            for(let [key,value] of res.list.entries()){
                if(value.project_details.indexOf('细木工板')!=-1){
                    arr.push(value)
                }
            }
            //石膏整合
            for(let [key,value] of res.list.entries()){
                if(value.project_details.indexOf('石膏')!=-1){
                    arr1.push(value)
                }
            }
            //龙骨整合
            for(let [key,value] of res.list.entries()){
                if(value.project_details.indexOf('龙骨')!=-1){
                    arr2.push(value)
                }
            }
            //丝杆整合
            for(let [key,value] of res.list.entries()){
                if(value.project_details.indexOf('丝杆')!=-1){
                    arr3.push(value)
                }
            }
            $scope.cur_process_list = [arr,arr1,arr2,arr3]
        }else {
            arr.push(res.list)
            $scope.cur_process_list = arr
        }
        console.log($scope.cur_process_list)
    })
    if($stateParams.project=='木作工艺'){
        _ajax.get('/quote/project-norm-woodwork-list',{},function (res) {
            console.log(res)
            let arr4 = angular.copy($scope.all_series),
                arr5 = angular.copy($scope.all_series),
                arr6 = angular.copy($scope.all_series),
                arr7 = angular.copy($scope.all_style),
                arr8 = angular.copy($scope.all_style)
            //系数1
            for(let [key,value] of arr4.entries()){
                value['series_or_style'] = 0
                value['cur_id'] = value.id
                value['coefficient'] = 1
                value['value'] = ''
            }
            //系数2
            for(let [key,value] of arr5.entries()){
                value['series_or_style'] = 0
                value['cur_id'] = value.id
                value['coefficient'] = 2
                value['value'] = ''
            }
            //系数3
            for(let [key,value] of arr6.entries()){
                value['series_or_style'] = 0
                value['cur_id'] = value.id
                value['coefficient'] = 3
                value['value'] = ''
            }//风格1
            for(let [key,value] of arr7.entries()){
                value['series_or_style'] = 1
                value['cur_id'] = value.id
                value['coefficient'] = 1
                value['value'] = ''
            }
            //风格2
            for(let [key,value] of arr8.entries()){
                value['series_or_style'] = 1
                value['cur_id'] = value.id
                value['coefficient'] = 2
                value['value'] = ''
            }
            $scope.series_and_style = [arr4,arr5,arr6,arr7,arr8]
            console.log($scope.series_and_style)
        })
    }
})