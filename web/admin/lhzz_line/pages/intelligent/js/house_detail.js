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
    if($stateParams.index == 1){

    }else{//添加
        let arr = []
        //户型信息
        $scope.house_informations.push({
            house_type_name:'',
            is_ordinary:0
        },{
            house_type_name:'',
            is_ordinary:1
        })
        //图纸对应户型
        for(let [key,value] of $scope.house_informations.entries()){
            if(value.is_ordinary == 0){
                arr.push(value)
            }
        }
        //图纸信息
        $scope.drawing_informations.push({
            drawing_name:'',
            options:arr
        })
    }
    //添加房屋或者图纸
    $scope.addData = function (index) {
        if(index == 1){
            $scope.house_informations.push({
                house_type_name:'',
                is_ordinary:1
            })
        }else if(index == 2){
            let arr = []
            for(let [key,value] of $scope.house_informations.entries()){
                if(value.is_ordinary == 0){
                    arr.push(value)
                }
            }
            $scope.drawing_informations.push({
                drawing_name:'',
                options:arr
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
        if(num == 1){
            $scope.house_informations.splice(index,1)
            $scope.house_informations.splice(index+1,0,item)
        }else{
            $scope.house_informations.splice(index,1)
            $scope.house_informations.splice(index-1,0,item)
        }
    }
    //删除项
    $scope.deleteItem = function (index,num) {
        if(num == 1){
            $scope.drawing_informations.splice(index,1)
        }else{
            $scope.house_informations.splice(index,1)
        }
    }
    //跳转详情页
    $scope.goDetail = function (item,index) {
        sessionStorage.setItem('houseInformation',JSON.stringify($scope.house_informations))
        sessionStorage.setItem('drawingInformation',JSON.stringify($scope.drawing_informations))
        if(item.is_ordinary == 0){

        }else if(item.is_ordinary == 1){
            $state.go('add_case',({index:$stateParams.index,cur_index:index,city:$stateParams.city}))
        }else{

        }
    }

    console.log()
})