app.controller('engineering_process_ctrl',function ($uibModal,$state,$stateParams, _ajax, $scope, $rootScope, $http) {
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
            link: -1
        },{
            name:$stateParams.project
        }
    ]
    let arr = [],arr1 = [],arr2 = [],arr3 = []
    let obj = JSON.parse(sessionStorage.getItem('area'))
    $scope.project_name = $stateParams.project
    $scope.cur_process_list = []
    //风格、系列以及楼梯结构
    _ajax.post('/quote/series-and-style', {}, function (res) {
        console.log(res)
        $scope.all_series = res.series
        $scope.all_style = res.style
    })
    //请求基本数据
    _ajax.get('/quote/project-norm-edit-list',{
        city_code:obj.city,
        id:$stateParams.id
    },function (res) {
        console.log(res)
        if($stateParams.project == '杂工工艺'){
            //水泥整合
            for(let [key,value] of res.list.entries()){
                if(value.project.indexOf('水泥')!=-1){
                    arr.push(value)
                }
            }
            //河沙整合
            for(let [key,value] of res.list.entries()){
                if(value.project.indexOf('河沙')!=-1){
                    arr1.push(value)
                }
            }
            //其它整合
            for(let [key,value] of res.list.entries()){
                if(value.project.indexOf('水泥')==-1&&value.project.indexOf('河沙')==-1){
                    arr2.push(value)
                }
            }
            $scope.cur_process_list = [arr,arr1,arr2]
        }else if($stateParams.project == '木作工艺'){
            //细木工板整合
            for(let [key,value] of res.list.entries()){
                if(value.project.indexOf('细木工板')!=-1){
                    arr.push(value)
                }
            }
            //石膏整合
            for(let [key,value] of res.list.entries()){
                if(value.project.indexOf('石膏')!=-1){
                    arr1.push(value)
                }
            }
            //龙骨整合
            for(let [key,value] of res.list.entries()){
                if(value.project.indexOf('龙骨')!=-1){
                    arr2.push(value)
                }
            }
            //丝杆整合
            for(let [key,value] of res.list.entries()){
                if(value.project.indexOf('丝杆')!=-1){
                    arr3.push(value)
                }
            }
            _ajax.get('/quote/project-norm-woodwork-list',{
                city:obj.city
            },function (res) {
                console.log(res)
                let arr4 = angular.copy($scope.all_series),
                    arr5 = angular.copy($scope.all_series),
                    arr6 = angular.copy($scope.all_series),
                    arr7 = angular.copy($scope.all_style),
                    arr8 = angular.copy($scope.all_style)
                let options = angular.copy(res.specification.find_specification)
                //系数1
                for(let [key,value] of arr4.entries()){
                    let index = res.coefficient.findIndex(function (item) {
                        return item.project == value.id&&item.series_or_style == '0'&&item.coefficient=='1'
                    })
                    value['series_or_style'] = 0
                    if(index != -1){
                        value['cur_id'] = res.coefficient[index].id
                    }
                    value['coefficient'] = 1
                    value['value'] = (index==-1?'':res.coefficient[index].value)
                }
                //系数2
                for(let [key,value] of arr5.entries()){
                    let index = res.coefficient.findIndex(function (item) {
                        return item.project == value.id&&item.series_or_style == 0&&item.coefficient==2
                    })
                    value['series_or_style'] = 0
                    if(index != -1){
                        value['cur_id'] = res.coefficient[index].id
                    }
                    value['coefficient'] = 2
                    value['value'] = (index==-1?'':res.coefficient[index].value)
                }
                //系数3
                for(let [key,value] of arr6.entries()){
                    let index = res.coefficient.findIndex(function (item) {
                        return item.project == value.id&&item.series_or_style == 0&&item.coefficient==3
                    })
                    value['series_or_style'] = 0
                    if(index != -1){
                        value['cur_id'] = res.coefficient[index].id
                    }
                    value['coefficient'] = 3
                    value['value'] = (index==-1?'':res.coefficient[index].value)
                }//风格1
                for(let [key,value] of arr7.entries()){
                    let index = res.coefficient.findIndex(function (item) {
                        return item.project == value.id&&item.series_or_style == 1&&item.coefficient==1
                    })
                    value['series_or_style'] = 1
                    if(index != -1){
                        value['cur_id'] = res.coefficient[index].id
                    }
                    value['coefficient'] = 1
                    value['value'] = (index==-1?'':res.coefficient[index].value)
                }
                //风格2
                for(let [key,value] of arr8.entries()){
                    let index = res.coefficient.findIndex(function (item) {
                        return item.project == value.id&&item.series_or_style == 1&&item.coefficient==2
                    })
                    value['series_or_style'] = 1
                    if(index != -1){
                        value['cur_id'] = res.coefficient[index].id
                    }
                    value['coefficient'] = 2
                    value['value'] = (index==-1?'':res.coefficient[index].value)
                }
                $scope.series_and_style = [arr4,arr5,arr6,arr7,arr8]
                console.log($scope.series_and_style)
                for(let [key,value] of options.entries()){
                    for(let [key1,value1] of res.specification.specification.entries()){
                        if((value.title.indexOf('龙骨')!=-1&&value1.title.indexOf('龙骨')!=-1)||
                            (value.title.indexOf('丝杆')!=-1&&value1.title.indexOf('丝杆')!=-1)||
                            (value.title.indexOf('石膏')!=-1&&value1.title.indexOf('石膏')!=-1)||
                            (value.title.indexOf('细木工板')!=-1&&value1.title.indexOf('细木工板')!=-1&&value.title.indexOf(value1.name)!=-1)
                        ){
                            value['options'] = value1.value
                            value['project'] = value.title
                            value['material'] = value.value + ''
                        }
                    }
                }
                //细木工板整合
                for(let [key,value] of options.entries()){
                    if(value.title.indexOf('细木工板')!=-1){
                        arr.push(value)
                    }
                }
                //石膏整合
                for(let [key,value] of options.entries()){
                    if(value.title.indexOf('石膏')!=-1){
                        arr1.unshift(value)
                    }
                }
                //龙骨整合
                for(let [key,value] of options.entries()){
                    if(value.title.indexOf('龙骨')!=-1){
                        arr2.unshift(value)
                    }
                }
                //丝杆整合
                for(let [key,value] of options.entries()){
                    if(value.title.indexOf('丝杆')!=-1){
                        arr3.unshift(value)
                    }
                }
                $scope.cur_process_list = [arr,arr1,arr2,arr3]
                console.log($scope.cur_process_list)
            })
        }else {
            arr.push(res.list)
            $scope.cur_process_list = arr
        }
    })
    //保存数据
    $scope.saveData = function (valid) {
        console.log($scope.cur_process_list);
        // console.log($scope.series_and_style);
        let all_modal = function ($scope, $uibModalInstance) {
                    $scope.cur_title = '保存成功'
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                        history.go(-1)
                    }
                }
                all_modal.$inject = ['$scope', '$uibModalInstance']
        let arr = [],arr1 = [],arr2 = []
        for(let [key,value] of $scope.cur_process_list.entries()){
            for(let [key1,value1] of value.entries()){
                if(value1.options == undefined){
                    if(value1.id == undefined){
                        arr.push({
                            project_id:value1.project_id,
                            value:value1.material
                        })
                    }else{
                        arr.push({
                            id:value1.id,
                            value:value1.material
                        })
                    }
                }else{
                    if(value1.id == undefined){
                        arr1.push({
                            title:value1.title,
                            value:value1.material=='其它'?0:value1.material
                        })
                    }else{
                        arr1.push({
                            id:value1.id,
                            value:value1.material=='其它'?0:value1.material
                        })
                    }
                }
            }
        }
        if($scope.series_and_style!=undefined){
            for(let [key,value] of $scope.series_and_style.entries()){
                for(let [key1,value1] of value.entries()){
                    if(value1.cur_id != undefined){
                        arr2.push({
                            id:value1.cur_id,
                            value:value1.value
                        })
                    }else{
                        arr2.push({
                            project:value1.id,
                            value:value1.value,
                            coefficient:value1.coefficient,
                            series_or_style:value1.series_or_style
                        })
                    }
                }
            }
        }
        if(valid){
            if($stateParams.project == '木作工艺'){
                _ajax.post('/quote/project-norm-woodwork-edit',{
                    city_code:obj.city,
                    value:arr,
                    specification:arr1,
                    coefficient:arr2
                },function (res) {
                    console.log(res);
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                })
            }else{
                _ajax.post('/quote/project-norm-edit',{
                    city_code:obj.city,
                    material:arr
                },function (res) {
                    console.log(res);
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                })
            }
        }else{
            $scope.submitted = true
        }
    }
    //返回前一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})