app.controller('add_case_ctrl', function ($window,$uibModal,$anchorScroll,$location,Upload, $scope, $rootScope, _ajax, $state, $stateParams) {
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
    $scope.all_num = ['一','二','三','四','五','六','七','八']
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
        if($scope.cur_house.house_type_name == ''){
            $scope.cur_house.series = $scope.all_series[0].id
            $scope.cur_house.style = $scope.all_style[0].id
            $scope.cur_house.stair = $scope.all_stair[0].id
        }
    })
    //案例材料项
    _ajax.get('/quote/assort-goods-list',{
        city:$stateParams.city
    },function (res) {
        console.log(res);
        let arr = []
        for(let [key,value] of angular.copy(res.list).entries()){
            let index = res.classify.findIndex(function (item) {
                return item.id == value.path.split(',')[0]
            })
            let index1 = res.classify.findIndex(function (item) {
                return item.id == value.pid
            })
            let index2 = arr.findIndex(function (item) {
                return item.id == value.path.split(',')[0]
            })
            if(index2 == -1){
                arr.push({
                    id:res.classify[index].id,
                    title:res.classify[index].title,
                    two_level:[{
                        id:res.classify[index1].id,
                        title:res.classify[index1].title,
                        three_level:[{
                            id:value.id,
                            title:value.title,
                            good_code:'',
                            good_quantity:''
                        }]
                    }]
                })
            }else{
                let index3 = arr[index2].two_level.findIndex(function (item) {
                    return item.id == value.pid
                })
                if(index3 == -1){
                    arr[index2].two_level.push({
                        id:res.classify[index1].id,
                        title:res.classify[index1].title,
                        three_level:[{
                            id:value.id,
                            title:value.title,
                            sku:'',
                            quantity:''
                        }]})
                }else{
                    let index4 = arr[index2].two_level[index3].findIndex(function(item){
                        return item.id == value.id
                    })
                    if(index4 == -1){
                        arr[index2].two_level[index3].three_level.push({
                            id:value.id,
                            title:value.title,
                            sku:'',
                            quantity:''
                        })
                    }
                }
            }
        }
        console.log(arr);
        $scope.cur_house.all_goods = arr
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
            high:2.8,
            window:'',
            series:'',
            style:'',
            drawing_list:[],
            worker_list:[],
            all_goods:[]
        })
        console.log($scope.cur_house);
    }else{

    }
    //判断商品编号是否填写正确
    $scope.determineGoodsCode = function (item) {
        console.log(item)
        if(item.good_code!=''){
            _ajax.get('/quote/sku-fefer',{
                category:item.title,
                sku:item.good_id
            },function (res) {
                console.log(res)
                if(res.code == 1043){
                    item.flag = true
                    item.msg = res.msg
                    console.log(item)
                }else if(res.code == 200){
                    item.flag = false
                    item.msg = ''
                }
            })
        }else{
            item.flag = false
            item.msg = ''
        }
    }
    //改变户型数据
    $scope.changeQuantity = function (item,limit,index) {
        if(index == 1){
            if($scope[item.split('.')[0]][item.split('.')[1]] >= limit){
                $scope[item.split('.')[0]][item.split('.')[1]] = limit
            }else{
                $scope[item.split('.')[0]][item.split('.')[1]] ++
            }
        }else{
            if($scope[item.split('.')[0]][item.split('.')[1]] <= limit){
                $scope[item.split('.')[0]][item.split('.')[1]] = limit
            }else{
                $scope[item.split('.')[0]][item.split('.')[1]] --
            }
        }
    }
    /*上传图片*/
    $scope.drawing_error = ''
    $scope.img_error = ''
    $scope.data = {
        file: null
    }
    $scope.upload_txt = '上传'
    //上传
    $scope.upload = function (file,index) {
        $scope.drawing_error = ''
        $scope.img_error = ''
        if (file != null) {
            index == 0 ?$scope.upload_txt = '上传中...':''
            Upload.upload({
                url: '/site/upload',
                data: {'UploadForm[file]': file}
            }).then(function (res) {
                if(res.data.code == 200){
                    if(index == 0){
                        $scope.cur_house.cur_imgSrc = res.data.data.file_path
                        $scope.upload_txt = '上传'
                        $scope.img_error = ''
                    }else{
                        $scope.cur_house.drawing_list.push(res.data.data.file_path)
                        $scope.drawing_error = ''
                    }
                }else{
                    index == 0?$scope.img_error = '上传图片格式不正确或尺寸不匹配，请重新上传':$scope.drawing_error = '上传图片格式不正确或尺寸不匹配，请重新上传'

                    // $timeout(function () {
                    //     $scope.upload_txt = '上传'
                    // },3000)
                }
                console.log(res)
            }, function (error) {
                console.log(error)
            })
        }
    }
    //删除图纸图片
    $scope.deleteDrawing = function (index) {
        $scope.cur_house.drawing_list.splice(index,1)
    }
    //保存案例
    $scope.saveCase = function (valid) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
               $uibModalInstance.close()
               history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(valid&&$scope.cur_house.cur_imgSrc != ''&&$scope.cur_house.drawing_list.length > 0){
            $scope.submitted = false
            $scope.house_informations[$stateParams.cur_index] = $scope.cur_house
            sessionStorage.setItem('houseInformation',JSON.stringify($scope.house_informations))
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: all_modal
            })
        }else if($scope.cur_house.cur_imgSrc == ''){
            $scope.img_error = '请上传图片'
        }else if(scope.cur_house.drawing_list.length == 0){
            $scope.drawing_error = '请上传图片'
        }else{
            $scope.submitted = true
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})