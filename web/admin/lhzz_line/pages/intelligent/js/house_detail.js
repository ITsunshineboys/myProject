app.controller('house_detail_ctrl', function ($scope, $rootScope, _ajax, $uibModal, $state, $stateParams,$http) {
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
            link: -1
        }, {
            name: $stateParams.index == 1 ? '编辑小区信息' : '添加小区信息'
        }
    ]
    //初始化数据
    $scope.params = {
        name:'',
        region_code:'',
        address:''
    }
    $scope.house_informations = []//房屋信息
    $scope.drawing_informations = []//图纸信息
    $http.get('city.json').then(function (res) {
        console.log(res)
        let arr = res.data[0][$stateParams.city]
        $scope.region_options = []
        for(let [key,value] of Object.entries(arr)){
            $scope.region_options.push({
                region_code:key,
                region_name:value
            })
        }
        $scope.params.region_code = $scope.region_options[0].region_code
    })
    if($stateParams.index == 1){//编辑
        $scope.house_informations = []
        $scope.drawing_informations = []
        _ajax.get('/quote/effect-plot-edit-view',{
            plot_id:$stateParams.id
        },function (res) {
            console.log(res);
            $scope.params = {
                name:res.effect.toponymy,
                region_code:res.effect.district_code,
                address:res.effect.street
            }
            //整合小区户型
            for(let [key,value] of res.effect.effect.entries()){
                if(value.type == 0){
                    $scope.house_informations.push({
                        id:value.id,
                        house_type_name:value.particulars,
                        area: value.area,
                        cur_room: value.bedroom,
                        cur_hall: value.sittingRoom_diningRoom,
                        cur_toilet: value.toilet,
                        cur_kitchen: value.kitchen,
                        cur_imgSrc: value.house_image,
                        have_stair: value.stairway,
                        stair: value.stair_id,
                        high: value.high,
                        window: value.window,
                        is_ordinary:0
                        // hall_area: value.,
                        // hall_girth: value.,
                        // room_area: value.,
                        // room_girth: value.,
                        // toilet_area: value.,
                        // toilet_girth: value.,
                        // kitchen_area: value.,
                        // kitchen_girth: value.,
                        // other_length: value.,
                        // flattop_area: value.,
                        // balcony_area: value.
                    })
                }else{
                    $scope.house_informations.push({
                        id:value.id,
                        house_type_name:value.particulars,
                        area: value.area,
                        cur_room: value.bedroom,
                        cur_hall: value.sittingRoom_diningRoom,
                        cur_toilet: value.toilet,
                        cur_kitchen: value.kitchen,
                        cur_imgSrc: value.house_image,
                        have_stair: value.stairway,
                        stair: value.stair_id,
                        high: value.high,
                        window: value.window,
                        worker_list:[],
                        all_goods:[],
                        is_ordinary:1
                    })
                }
            }
            //普通户型面积
            for(let [key,value] of res.effect.decoration_particulars.entries()){
                let index = $scope.house_informations.findIndex(function (item) {
                    return item.id == value.effect_id
                })
                console.log($scope.house_informations[index]);
                if(index!=-1){
                    Object.assign($scope.house_informations[index],{
                        other_id:value.id,
                        hall_area: value.hall_area,
                        hall_girth: value.hall_perimeter,
                        room_area: value.bedroom_area,
                        room_girth: value.bedroom_perimeter,
                        toilet_area: value.toilet_area,
                        toilet_girth: value.toilet_perimeter,
                        kitchen_area: value.kitchen_area,
                        kitchen_girth: value.kitchen_perimeter,
                        other_length: value.modelling_length,
                        flattop_area: value.flat_area,
                        balcony_area: value.balcony_area,
                        drawing_list:[]
                    })
                }
            }
            //整合案例图片
            for(let [key,value] of res.effect.images.entries()){
                let index = $scope.house_informations.findIndex(function (item) {
                    return item.id == value.effect_id
                })
                if(index != -1){
                    if(value.images_user == '案例添加'){
                        Object.assign($scope.house_informations[index],{
                            drawing_id:value.id,
                            drawing_list:value.effect_images.split(','),
                            series:value.series_id,
                            style:value.style_id
                        })
                    }else{
                        $scope.drawing_informations.push({
                            id:value.id,
                            all_drawing:value.effect_images.split(','),
                            series:value.series_id,
                            style:value.style_id,
                            drawing_name:value.images_user,
                            index:index
                        })
                    }
                }
            }
            //修改后的数据
            if(sessionStorage.getItem('houseInformation')!=null){
                $scope.house_informations = JSON.parse(sessionStorage.getItem('houseInformation'))
            }
            if(sessionStorage.getItem('drawingInformation')!=null){
                $scope.drawing_informations = JSON.parse(sessionStorage.getItem('drawingInformation'))
            }
            console.log($scope.drawing_informations);
            console.log($scope.house_informations);
        })
    }else{//添加
        // let arr = []
        //初始化数据
        //户型信息
        $scope.house_informations.push({
            house_type_name:'',
            is_ordinary:0
        },{
            house_type_name:'',
            is_ordinary:1
        })
        //图纸对应户型
        // for(let [key,value] of $scope.house_informations.entries()){
        //     if(value.is_ordinary == 0){
        //         arr.push(value)
        //     }
        // }
        //图纸信息
        $scope.drawing_informations.push({
            drawing_name:'',
            // options:arr
        })
        //修改后的数据
        if(sessionStorage.getItem('houseInformation')!=null){
            $scope.house_informations = JSON.parse(sessionStorage.getItem('houseInformation'))
        }
        if(sessionStorage.getItem('drawingInformation')!=null){
            $scope.drawing_informations = JSON.parse(sessionStorage.getItem('drawingInformation'))
        }
        console.log($scope.drawing_informations);
    }
    //添加房屋或者图纸
    $scope.addData = function (index) {
        if(index == 1){
            $scope.house_informations.push({
                house_type_name:'',
                is_ordinary:1
            })
        }else if(index == 2){
            // let arr = []
            // for(let [key,value] of $scope.house_informations.entries()){
            //     if(value.is_ordinary == 0){
            //         arr.push(value)
            //     }
            // }
            $scope.drawing_informations.push({
                drawing_name:'',
                // options:arr
            })
        }else{
            $scope.house_informations.push({
                house_type_name:'',
                is_ordinary:0
            })
        }
    }
    //上移、下移
    $scope.move = function (item,index,num) {
        let drawing_information = ''
        if(sessionStorage.getItem('drawingInformation')){
            drawing_information = JSON.parse(sessionStorage.getItem('drawingInformation'))
        }
        if(num == 1){
            for(let [key,value] of drawing_information.entries()){
                if(value.index == index){
                    value.index = index+1
                }
                if(value.index == index+1){
                    value.index = index
                }
            }
            sessionStorage.setItem('drawingInformation',JSON.stringify(drawing_information))
            $scope.house_informations.splice(index,1)
            $scope.house_informations.splice(index+1,0,item)
        }else{
            for(let [key,value] of drawing_information.entries()){
                if(value.index == index){
                    value.index = index-1
                }
                if(value.index == index-1){
                    value.index = index
                }
            }
            sessionStorage.setItem('drawingInformation',JSON.stringify(drawing_information))
            $scope.house_informations.splice(index,1)
            $scope.house_informations.splice(index-1,0,item)
        }
    }
    //删除项
    $scope.deleteItem = function (index,num) {
        if(num == 1){
            $scope.drawing_informations.splice(index,1)
        }else{
            for(let [key,value] of $scope.drawing_informations.entries()){
                if(value.index == index){
                    value.index = ''
                }
            }
            $scope.house_informations.splice(index,1)
            for(let [key,value] of $scope.drawing_informations.entries()){
                let index = $scope.house_informations.findIndex(function (item) {
                    return item.is_ordinary == 0
                })
                if(value.index == ''){
                    value.index = index
                }
            }
        }
    }
    //跳转详情页
    $scope.goDetail = function (item,index) {
        sessionStorage.setItem('houseInformation',JSON.stringify($scope.house_informations))
        sessionStorage.setItem('drawingInformation',JSON.stringify($scope.drawing_informations))
        if(item.is_ordinary == 0){
            $state.go('edit_house',({index:$stateParams.index,cur_index:index,city:$stateParams.city}))
        }else if(item.is_ordinary == 1){
            $state.go('add_case',({index:$stateParams.index,cur_index:index,city:$stateParams.city}))
        }else{
            $state.go('add_drawing',({index:$stateParams.index,cur_index:index,city:$stateParams.city}))
        }
    }
    //保存数据
    $scope.saveData = function (valid) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                sessionStorage.removeItem('drawingInformation')
                sessionStorage.removeItem('houseInformation')
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        let arr = angular.copy($scope.house_informations)
        for(let [key,value] of $scope.drawing_informations.entries()){
            console.log(arr[value.index]);
            if(value.id){
                arr[value.index].drawing_list.push({
                    id:value.id,
                    all_drawing:value.all_drawing.join(','),
                    series:value.series,
                    style:value.style,
                    drawing_name:value.drawing_name
                })
            }else{
                arr[value.index].drawing_list.push({
                    all_drawing:value.all_drawing.join(','),
                    series:value.series,
                    style:value.style,
                    drawing_name:value.drawing_name
                })
            }
        }
        for(let [key,value] of arr.entries()){
            value['sort_id'] = key
            if(value.is_ordinary == 1){
                let goods_arr = []
                let worker_list = []
                for(let [key1,value1] of value.all_goods.entries()){
                    for(let [key2,value2] of value1.two_level.entries()){
                        for(let [key3,value3] of value2.three_level.entries()){
                            if(value3.good_code!=''&&value3.good_quantity!=''){
                                goods_arr.push({
                                    first_name:value1.title,
                                    second_name:value2.title,
                                    three_name:value3.title,
                                    good_code:value3.good_code,
                                    good_quantity:value3.good_quantity,
                                    three_id:value3.id
                                })
                            }
                        }
                    }
                }
                value.all_goods = goods_arr
                for(let [key1,value1] of value.worker_list.entries()){
                    if(value1.price!=''){
                        worker_list.push(value1)
                    }
                }
                value.worker_list = worker_list
                value.drawing_list = value.drawing_list.join(',')
            }
        }
        if(valid){
            if($stateParams.index == 1){
                _ajax.post('/quote/effect-edit-plot',{
                    effect_id:$stateParams.id,
                    province_code:$stateParams.province,
                    city_code:$stateParams.city,
                    address:$scope.params.address,
                    house_name:$scope.params.name,
                    district_code:$scope.params.region_code,
                    house_informations:arr
                },function () {
                    $scope.submitted = false
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                })
            }else{
                _ajax.post('/quote/effect-plot-add',{
                    province_code:$stateParams.province,
                    city_code:$stateParams.city,
                    address:$scope.params.address,
                    house_name:$scope.params.name,
                    district_code:$scope.params.region_code,
                    house_informations:arr
                },function (res) {
                    $scope.submitted = false
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
    //返回上一页
    $scope.goPrev = function () {
        history.go(-1)
    }
})