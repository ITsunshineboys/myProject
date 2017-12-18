app.controller('add_case_ctrl', function ($scope, $rootScope, _ajax, $state, $stateParams) {
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
            name: $stateParams.index == 1 ? '编辑小区信息' : '添加小区信息',
            link: -1
        }, {
            name: '户型详情'
        }
    ]
    //工人信息
    _ajax.get('/quote/labor-list',{},function (res) {
        console.log(res);
        $scope.labor_list = res.labor_list
        for(let [key,value] of $scope.labor_list.entries()){
            $scope.cur_house.worker_list.push({
                worker_kind:value.worker_kind,
                price:''
            })
        }
    })
    //风格、系列以及楼梯结构
    _ajax.get('/quote/series-and-style', {}, function (res) {
        console.log(res)
        $scope.all_series = res.series
        $scope.all_style = res.style
        $scope.all_stair = res.stairs_details
        $scope.cur_house.series = $scope.all_series[0].id
        $scope.cur_house.style = $scope.all_style[0].id
        $scope.cur_house.stair = $scope.all_stair[0].id
    })
    //案例材料项
    _ajax.get('/quote/assort-goods-list',{
        city:$stateParams.city
    },function (res) {
        console.log(res);
        let arr = []
        //一级
        for(let [key,value] of angular.copy(res.classify).entries()){
            for(let [key1,value1] of angular.copy(res.list).entries()){
                let index = arr.findIndex(function (item) {
                    return item.id == value.id
                })
                if(value1.path.split(',')[0] == value.id&&index==-1){
                    arr.push({
                        id:value.id,
                        title:value.title,
                        level_two:[]
                    })
                }
            }
        }
        //二级
        for(let [key,value] of angular.copy(res.classify).entries()){
            for(let [key1,value1] of angular.copy(res.list).entries()){

            }
        }
    })
    $scope.house_informations = JSON.parse(sessionStorage.getItem('houseInformation'))
    $scope.cur_house = $scope.house_informations[$stateParams.cur_index]
    if($scope.cur_house.house_type_name == ''){
        Object.assign($scope.cur_house,{
            area:'',
            cur_room:1,
            cur_hall:1,
            cur_toilet:1,
            cur_kitchen:1,
            cur_imgSrc:'',
            have_stair:1,
            stair:'',
            high:'',
            window:'',
            series:'',
            style:'',
            drawing_list:[],
            worker_list:[],
            all_materials:[]
        })
    }else{

    }
    // 'house_type_name': value.house_type_name,
    //     'area': value.area,
    //     'cur_room': value.cur_room,
    //     'cur_hall': value.cur_hall,
    //     'cur_toilet': value.cur_toilet,
    //     'cur_kitchen': value.cur_kitchen,
    //     'cur_imgSrc': value.cur_imgSrc,
    //     'have_stair': value.have_stair,
    //     'stair': value.stair,
    //     'high': value.high,
    //     'sort_id': arr.length + 1,
    //     'window': value.window,
    //     'series': value.series.id,
    //     'style': value.style.id,
    //     'all_goods': value.all_goods || [],
    //     'drawing_list': value.drawing_list.join(','),
    //     'worker_list': value.worker_list || [],
        // 'backman_option': value.backman_list || [],
        // 'is_ordinary': 1
})