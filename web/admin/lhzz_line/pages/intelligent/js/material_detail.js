app.controller('material_detail_ctrl', function ($rootScope,_ajax, $scope, $state, $stateParams, $http, $uibModal) {
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
            name: '添加材料项',
            link: -1
        }, {
            name: '材料项详情'
        }
    ]
    $scope.cur_status = $stateParams.status
    $scope.vm = $scope
    $scope.basic_attr = ''//基本属性
    $scope.other_attr = ''//商品属性
    $scope.tab_status = 0//tab状态
    //获取户型相关
    _ajax.get('/quote/house-type-list', {}, function (res) {
        console.log(res)
        $scope.area_range = res.list
        for(let [key,value] of $scope.area_range.entries()){
            value['quantity'] = ''
        }
    })
    //风格、系列以及楼梯结构
    _ajax.post('/quote/series-and-style', {}, function (res) {
        console.log(res)
        $scope.all_series = res.series
        $scope.all_style = res.style
        for(let [key,value] of $scope.all_series.entries()){
            value['quantity'] = ''
        }
        for(let [key,value] of $scope.all_style.entries()){
            value['quantity'] = ''
        }
    })
    if($scope.cur_status == 0){
        //获取分类
        _ajax.get('/quote/assort-goods', {}, function (res) {
            console.log(res)
            $scope.level_one = res.data.categories
            $scope.cur_level_one = $scope.level_one[0]
            _ajax.get('/quote/assort-goods', {
                pid: $scope.cur_level_one.id
            }, function (res) {
                console.log(res)
                $scope.level_two = res.data.categories
                $scope.cur_level_two = $scope.level_two[0]
                _ajax.get('/quote/assort-goods', {
                    pid: $scope.cur_level_two.id
                }, function (res) {
                    console.log(res)
                    $scope.level_three = res.data.categories
                    $scope.cur_level_three = $scope.level_three[0]
                })
            })
        })
    }else{
        _ajax.post('/quote/decoration-edit-list',{
            id:$stateParams.id
        },function (res) {
            console.log(res);
        })
    }
    //切换tab
    $scope.changeTab = function (index) {
        $scope.submitted = false
        //初始化其余项数据
        if(index == 1){
            $scope.tab_status = 0
            for(let [key,value] of $scope.all_style.entries()){
                value['quantity'] = ''
                value['flag'] = false
            }
            for(let [key,value] of $scope.area_range.entries()){
                value['quantity'] = ''
                value['flag'] = false
            }
        }else if(index == 2){
            $scope.tab_status = 1
            for(let [key,value] of $scope.all_series.entries()){
                value['quantity'] = ''
                value['flag'] = false
            }
            for(let [key,value] of $scope.area_range.entries()){
                value['quantity'] = ''
                value['flag'] = false
            }
        }else{
            $scope.tab_status = 2
            for(let [key,value] of $scope.all_series.entries()){
                value['quantity'] = ''
                value['flag'] = false
            }
            for(let [key,value] of $scope.all_style.entries()){
                value['quantity'] = ''
                value['flag'] = false
            }
        }
    }
    //改变分类
    $scope.getCategory = function (index) {
        if (index == 1) {
            _ajax.get('/quote/assort-goods', {
                pid: $scope.cur_level_one.id
            }, function (res) {
                console.log(res)
                $scope.level_two = res.data.categories
                $scope.cur_level_two = $scope.level_two[0]
                _ajax.get('/quote/assort-goods', {
                    pid: $scope.cur_level_two.id
                }, function (res) {
                    console.log(res)
                    $scope.level_three = res.data.categories
                    $scope.cur_level_three = $scope.level_three[0]
                })
            })
        } else {
            _ajax.get('/quote/assort-goods', {
                pid: $scope.cur_level_two.id
            }, function (res) {
                console.log(res)
                $scope.level_three = res.data.categories
                $scope.cur_level_three = $scope.level_three[0]
            })
        }
    }
    //抓取材料
    $scope.getMaterialDetail = function () {
        console.log($scope.cur_level_three);
        _ajax.get('/quote/decoration-add-classify',{
            category_id:$scope.cur_level_three.id
        },function (res) {
            console.log(res);
            //基本属性
            $scope.basic_attr = {
                goods_name:res.goods.goods_name,
                sku:res.goods.sku,
                purchase_price_decoration_company:res.goods.purchase_price_decoration_company,
                platform_price:res.goods.platform_price,
                market_price:res.goods.market_price,
                left_number:res.goods.left_number,
            }
            //商品属性
            $scope.other_attr = res.goods_attr
        })
    }
    //保存添加材料详情
    $scope.saveMaterial = function (valid) {
        let arr = [],arr1 = [],arr2 = []
        //系列相关
        for (let [key,value] of $scope.all_series.entries()){
            arr.push({
                series:value.id,
                quantity:value.quantity
            })
        }
        //风格相关
        for (let [key,value] of $scope.all_style.entries()){
            arr1.push({
                style:value.id,
                quantity:value.quantity
            })
        }
        //户型相关
        for (let [key,value] of $scope.area_range.entries()){
            arr2.push({
                min_area:value.min_area,
                max_area:value.max_area,
                quantity:value.quantity
            })
        }
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        let next_all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '抓取材料信息错误，请重新抓取'
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        next_all_modal.$inject = ['$scope', '$uibModalInstance']
        if($scope.basic_attr !=''){
            if(valid){
                _ajax.post('/quote/decoration-add',{
                    city:$stateParams.city,
                    code:$scope.basic_attr.sku,
                    c_id:$scope.cur_level_three.id,
                    message:$scope.tab_status == 0?'系列相关':($scope.tab_status == 1?'风格相关':'户型相关'),
                    add:$scope.tab_status == 0?arr:($scope.tab_status == 1?arr1:arr2)
                },function (res) {
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                })
            }else{
                $scope.submitted = true
            }
        }else{
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: next_all_modal
            })
        }
    }
})