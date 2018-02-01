app.controller('basic_ctrl',function ($timeout,$scope,$state,$stateParams,_ajax) {
    //初始化
    $scope.basic_materials = JSON.parse(sessionStorage.getItem('copies'))[$stateParams.index]
    let obj = JSON.parse(sessionStorage.getItem('params'))//基本信息
    $scope.materials = JSON.parse(sessionStorage.getItem('copies'))
    $scope.worker_list = JSON.parse(sessionStorage.getItem('worker_list'))
    $scope.params = {
        city:510100,
        '12_dismantle':'',
        '24_dismantle':'',
        'repair':'',
        '12_new_construction':'',
        '24_new_construction':'',
        building_scrap:0,
        area:obj.area,
        series:obj.series,
        style:obj.style
    }
    if(sessionStorage.getItem('options')!=null){
        $scope.params = JSON.parse(sessionStorage.getItem('options'))
    }
    //请求风格、系列
    _ajax.get('/owner/series-and-style', {}, function (res) {
        console.log(res);
        $scope.series = res.data.show.series//系列
        $scope.style = res.data.show.style//风格
    })
    //一级、二级分类数据请求
    _ajax.post('/owner/classify', {}, function (res) {
        console.log(res)
        $scope.first_level = res.data.pid.stair//一级
        $scope.second_level = res.data.pid.level//二级
    })
    //获取商品详情
    $scope.getDetails = function (item) {
        console.log(item);
        $scope.goods_details = item
        //系列名称
        let index = $scope.series.findIndex(function (item) {
            return item.id == $scope.goods_details.series_id
        })
        $scope.goods_details.series_name = (index == -1?'':$scope.series[index].series)
        //风格名称
        let index1 = $scope.style.findIndex(function (item) {
            return item.id == $scope.goods_details.style_id
        })
        $scope.goods_details.style_name = (index1 == -1?'':$scope.style[index].style)
    }
    //跳转详情页
    $scope.goDetails = function () {
        $timeout(function () {
            $state.go('product_details',{index:$stateParams.index,status:0,id:$scope.goods_details.id})
        },300)
    }
    //请求杂工数据
    $scope.getHandyman = function (valid) {
        let obj1 = angular.copy($scope.params)
        for(let [key,value] of Object.entries($scope.params)){
            if(value === ''){
               delete obj1[key]
            }
        }
        console.log($scope.params);
        console.log(obj);
        if($scope.params['12_dismantle']!=''||$scope.params['24_dismantle']!=''||$scope.params['repair']!=''||$scope.params['12_new_construction']!=''||$scope.params['24_new_construction']!=''){
            if(valid){
                _ajax.get('/owner/handyman',obj1,function (res) {
                    console.log('杂工');
                    console.log(res);
                    if(sessionStorage.getItem('other_data')!=null){
                        let other_data = JSON.parse(sessionStorage.getItem('other_data'))
                        //清除数据
                        for(let [key,value] of $scope.materials.entries()){
                            for(let [key1,value1] of value.second_level.entries()){
                                for(let [key2,value2] of value1.goods.entries()){
                                    let index = other_data.data.findIndex(function (item) {
                                        return item.id == value2.id
                                    })
                                    if(index!=-1){
                                        value2.quantity -= other_data.data[index].quantity
                                        value2.cost -= other_data.data[index].cost
                                        value2.procurement -= other_data.data[index].procurement
                                        value1.cost -= other_data.data[index].cost
                                        value1.procurement -= other_data.data[index].procurement
                                        value.cost -= other_data.data[index].cost
                                        value.procurement -= other_data.data[index].procurement
                                    }
                                }
                            }
                        }
                        console.log($scope.materials);
                        let materials = angular.copy($scope.materials)
                        for(let [key,value] of materials.entries()){
                            for(let [key1,value1] of value.second_level.entries()){
                                let index = $scope.materials[key].second_level.findIndex(function (item) {
                                    return item.id == value1.id
                                })
                                if(value1.cost === 0){
                                    if(index != -1){
                                        $scope.materials[key].count -= $scope.materials[key].second_level[index].goods.length
                                        $scope.materials[key].second_level.splice(index,1)
                                    }
                                }else{
                                    for(let [key2,value2] of value1.goods.entries()){
                                        if(index!=-1){
                                            let index1 = $scope.materials[key].second_level[index].goods.findIndex(function (item) {
                                                return item.id == value2.id
                                            })
                                            if(value2.cost == 0){
                                                if(index1!=-1){
                                                    $scope.materials[key].count --
                                                    $scope.materials[key].second_level[index].goods.splice(index1,1)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //获取工人费用
                    let index = $scope.worker_list.findIndex(function (item) {
                        return item.worker_kind == res.labor_all_cost.worker_kind
                    })
                    if(index == -1){
                        $scope.worker_list.push(res.labor_all_cost)
                    }else{
                        $scope.worker_list[index].price = res.labor_all_cost.price
                    }
                    //整合二级
                    for (let [key, value] of res.data.entries()) {
                        for (let [key1, value1] of $scope.materials.entries()) {
                            if (value1.id == value.path.split(',')[0]) {
                                let index = value1.second_level.findIndex(function (item) {
                                    return item.id == value.path.split(',')[1]
                                })
                                let index1 = $scope.second_level.findIndex(function (item) {
                                    return item.id == value.path.split(',')[1]
                                })
                                if (index == -1) {
                                    value1.second_level.push({
                                        id: +$scope.second_level[index1].id,
                                        title: $scope.second_level[index1].title,
                                        cost: 0,
                                        procurement: 0,
                                        goods: []
                                    })
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of res.data.entries()) {
                        for (let [key1, value1] of $scope.materials.entries()) {
                            for (let [key2, value2] of value1.second_level.entries()) {
                                if (value2.id == value.path.split(',')[1]) {
                                    let index = value2.goods.findIndex(function (item) {
                                        return item.id == value.id
                                    })
                                    value1.cost += value.cost
                                    value1.procurement += value.procurement
                                    value2.cost += value.cost
                                    value2.procurement += value.procurement
                                    if (index == -1) {
                                        value2.goods.push(value)
                                        value1.count ++
                                    } else {
                                        value2.goods[index].quantity += value.quantity
                                        value2.goods[index].cost += value.cost
                                        value2.goods[index].procurement += value.procurement
                                    }
                                }
                            }
                        }
                    }
                    sessionStorage.setItem('options',JSON.stringify($scope.params))
                    sessionStorage.setItem('other_data',JSON.stringify(res))//杂工项数据保存
                    sessionStorage.removeItem('copies')//去除复制品
                    sessionStorage.setItem('worker_list',JSON.stringify($scope.worker_list))//工人项保存
                    //材料项保存
                    console.log(obj);
                    if(sessionStorage.getItem('materials')!=null){
                        sessionStorage.setItem('materials',JSON.stringify($scope.materials))
                        $state.go('nodata')
                    }else if(sessionStorage.getItem('quotation_materials')!=null){
                        sessionStorage.setItem('quotation_materials',JSON.stringify($scope.materials))
                        $state.go('modelRoom',{effect_id:obj.effect_id,id:obj.id})
                    }
                })
            }
        }else{
            //清除数据
            if(sessionStorage.getItem('other_data')!=null){
                let other_data = JSON.parse(sessionStorage.getItem('other_data'))
                //清除数据
                for(let [key,value] of $scope.materials.entries()){
                    for(let [key1,value1] of value.second_level.entries()){
                        for(let [key2,value2] of value1.goods.entries()){
                            let index = other_data.data.findIndex(function (item) {
                                return item.id == value2.id
                            })
                            if(index!=-1){
                                value2.quantity -= other_data.data[index].quantity
                                value2.cost -= other_data.data[index].cost
                                value2.procurement -= other_data.data[index].procurement
                                value1.cost -= other_data.data[index].cost
                                value1.procurement -= other_data.data[index].procurement
                                value.cost -= other_data.data[index].cost
                                value.procurement -= other_data.data[index].procurement
                            }
                        }
                    }
                }
                console.log($scope.materials);
                let materials = angular.copy($scope.materials)
                for(let [key,value] of materials.entries()){
                    for(let [key1,value1] of value.second_level.entries()){
                        let index = $scope.materials[key].second_level.findIndex(function (item) {
                            return item.id == value1.id
                        })
                        if(value1.cost === 0){
                            if(index != -1){
                                $scope.materials[key].count -= $scope.materials[key].second_level[index].goods.length
                                $scope.materials[key].second_level.splice(index,1)
                            }
                        }else{
                            for(let [key2,value2] of value1.goods.entries()){
                                if(index!=-1){
                                    let index1 = $scope.materials[key].second_level[index].goods.findIndex(function (item) {
                                        return item.id == value2.id
                                    })
                                    if(value2.cost == 0){
                                        if(index1!=-1){
                                            $scope.materials[key].count --
                                            $scope.materials[key].second_level[index].goods.splice(index1,1)
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            let index = $scope.worker_list.findIndex(function (item) {
                return item.worker_kind == '杂工'
            })
            if(index != -1){
                $scope.worker_list.splice(index,1)
            }
            sessionStorage.removeItem('options')
            sessionStorage.removeItem('other_data')
            sessionStorage.removeItem('copies')
            sessionStorage.setItem('worker_list',JSON.stringify($scope.worker_list))//工人项保存
            //材料项保存
            if(sessionStorage.getItem('materials')!=null){
                sessionStorage.setItem('materials',JSON.stringify($scope.materials))
                $state.go('nodata')
            }else if(sessionStorage.getItem('quotation_materials')!=null){
                sessionStorage.setItem('quotation_materials',JSON.stringify($scope.materials))
                $state.go('modelRoom',{effect_id:obj.effect_id,id:obj.id})
            }
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        sessionStorage.removeItem('copies')
        if(sessionStorage.getItem('materials')!=null){
            $state.go('nodata')
        }else if(sessionStorage.getItem('quotation_materials')!=null){
            $state.go('modelRoom',{effect_id:obj.effect_id,id:obj.id})
        }
    }
})