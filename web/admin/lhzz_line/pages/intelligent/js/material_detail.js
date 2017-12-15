app.controller('material_detail_ctrl', function ($rootScope, _ajax, $scope, $state, $stateParams, $http, $uibModal) {
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
        for (let [key, value] of $scope.area_range.entries()) {
            value['quantity'] = ''
        }
    })
    //风格、系列以及楼梯结构
    _ajax.post('/quote/series-and-style', {}, function (res) {
        console.log(res)
        $scope.all_series = res.series
        $scope.all_style = res.style
        for (let [key, value] of $scope.all_series.entries()) {
            value['quantity'] = ''
        }
        for (let [key, value] of $scope.all_style.entries()) {
            value['quantity'] = ''
        }
    })
    //获取页面数据
    if ($scope.cur_status == 0) {
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
    }
    else {
        _ajax.post('/quote/decoration-edit-list', {
            id: $stateParams.id
        }, function (res) {
            console.log(res);
            $scope.cur_level_one = res.decoration_add.goods_cate.one_category
            $scope.cur_level_two = res.decoration_add.goods_cate.two_category
            $scope.cur_level_three ={
                title:res.decoration_add.goods_cate.three_category,
                id:res.decoration_add.c_id
            }
            //获取风格、系列或者户型面积数据
            for (let [key, value] of res.decoration_message.entries()) {
                for (let [key1, value1] of $scope.all_style.entries()) {
                    if (value1.id == value.style_id) {
                        value1.quantity = value['quantity']
                        value1.style_id = value.id
                    }
                }
                for (let [key1, value1] of $scope.all_series.entries()) {
                    if (value1.id == value.series_id) {
                        value1.quantity = value['quantity']
                        value1.series_id = value.id
                    }
                }
                for (let [key1, value1] of $scope.area_range.entries()) {
                    if (value.min_area == value1.min_area && value.max_area == value1.max_area) {
                        value1.quantity = value['quantity']
                        value1.area_id = value.id
                    }
                }
            }
            //tab切换
            if (res.decoration_add.correlation_message == '系列相关') {
                $scope.tab_status = 0
            } else if (res.decoration_add.correlation_message == '风格相关') {
                $scope.tab_status = 1
            } else {
                $scope.tab_status = 2
            }
            //基本属性
            $scope.basic_attr = {
                id:res.decoration_add.id,
                goods_name: res.goods.title,
                sku: res.goods.sku,
                supplier_price: res.goods.supplier_price,
                platform_price: res.goods.platform_price,
                market_price: res.goods.market_price,
                left_number: res.goods.left_number,
            }
            //商品属性
            $scope.other_attr = res.goods_attr
            console.log($scope.all_series);
        })
    }
    //切换tab
    $scope.changeTab = function (index) {
        $scope.submitted = false
        //初始化其余项数据
        if (index == 1) {
            $scope.tab_status = 0
            for (let [key, value] of $scope.all_style.entries()) {
                value['quantity'] = ''
                value['flag'] = false
            }
            for (let [key, value] of $scope.area_range.entries()) {
                value['quantity'] = ''
                value['flag'] = false
            }
        } else if (index == 2) {
            $scope.tab_status = 1
            for (let [key, value] of $scope.all_series.entries()) {
                value['quantity'] = ''
                value['flag'] = false
            }
            for (let [key, value] of $scope.area_range.entries()) {
                value['quantity'] = ''
                value['flag'] = false
            }
        } else {
            $scope.tab_status = 2
            for (let [key, value] of $scope.all_series.entries()) {
                value['quantity'] = ''
                value['flag'] = false
            }
            for (let [key, value] of $scope.all_style.entries()) {
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
        let next_all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '抓取材料信息错误，请重新抓取'
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        next_all_modal.$inject = ['$scope', '$uibModalInstance']
        _ajax.get('/quote/decoration-add-classify', {
            category_id: $scope.cur_level_three.id
        }, function (res) {
            console.log(res);
            if(res.code == 200){
                //基本属性
                $scope.basic_attr = {
                    goods_name: res.goods.goods_name,
                    sku: res.goods.sku,
                    supplier_price: res.goods.supplier_price,
                    platform_price: res.goods.platform_price,
                    market_price: res.goods.market_price,
                    left_number: res.goods.left_number,
                }
                //商品属性
                $scope.other_attr = res.goods_attr
            }else{
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: next_all_modal
                })
            }
        })
    }
    //保存添加材料详情
    $scope.saveMaterial = function (valid) {
        let arr = [], arr1 = [], arr2 = []
        //系列相关
        for (let [key, value] of $scope.all_series.entries()) {
            if(value.series_id == undefined){
                arr.push({
                    series: value.id,
                    quantity: value.quantity
                })
            }else{
                arr.push({
                    id: value.series_id,
                    quantity: value.quantity
                })
            }
        }
        //风格相关
        for (let [key, value] of $scope.all_style.entries()) {
            if(value.style_id == undefined){
                arr1.push({
                    style: value.id,
                    quantity: value.quantity
                })
            }else{
                arr1.push({
                    id: value.style_id,
                    quantity: value.quantity
                })
            }
        }
        //户型相关
        for (let [key, value] of $scope.area_range.entries()) {
            if(value.area_id == undefined){
                arr1.push({
                    min_area: value.min_area,
                    max_area: value.max_area,
                    quantity: value.quantity
                })
            }else{
                arr1.push({
                    id: value.area_id,
                    quantity: value.quantity
                })
            }
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
            $scope.cur_title = '未抓取到材料信息,请先抓取'
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        next_all_modal.$inject = ['$scope', '$uibModalInstance']
        if ($scope.basic_attr != '') {
            if (valid) {
                if($scope.cur_status == 0){
                    _ajax.post('/quote/decoration-add', {
                        city: $stateParams.city,
                        sku: $scope.basic_attr.sku,
                        category_id: $scope.cur_level_three.id,
                        message: $scope.tab_status == 0 ? '系列相关' : ($scope.tab_status == 1 ? '风格相关' : '户型相关'),
                        add: $scope.tab_status == 0 ? arr : ($scope.tab_status == 1 ? arr1 : arr2)
                    }, function (res) {
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    })
                }else{
                    _ajax.post('/quote/decoration-edit',{
                        id:$scope.basic_attr.id,
                        sku:$scope.basic_attr.sku,
                        message: $scope.tab_status == 0 ? '系列相关' : ($scope.tab_status == 1 ? '风格相关' : '户型相关'),
                        add: $scope.tab_status == 0 ? arr : ($scope.tab_status == 1 ? arr1 : arr2)
                    },function (res) {
                        console.log(res);
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    })
                }
            } else {
                $scope.submitted = true
            }
        } else {
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: next_all_modal
            })
        }
    }
    //返回前一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})