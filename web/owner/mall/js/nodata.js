app.controller('nodata_ctrl', function ($http, _ajax, $state, $scope, $anchorScroll, $location, $q) {
    $scope.vm = $scope
    /*初始化*/
    //基本信息
    $scope.params = {
        bedroom: 1,//卧室
        area: 60,      //面积
        hall: 1,       //餐厅
        toilet: 1,   // 卫生间
        kitchen: 1,  //厨房
        series: '',   //系列
        style: '',  //风格
        window: 0,//飘窗
        high: '', //层高
        province: 510000,   //省编码
        city: 510100,      // 市编码
        stairway_id: 0,//有无楼梯
        stairs: 0//楼梯结构
    }
    $scope.words = '生成材料'
    //层高信息
    $scope.high = [2.8, 3, 3.3, 4.5]
    $scope.params.high = $scope.high[0]
    if (sessionStorage.getItem('params') != null) {
        $scope.params = JSON.parse(sessionStorage.getItem('params'))
    }
    //风格、系列以及楼梯信息
    _ajax.get('/owner/series-and-style', {}, function (res) {
        console.log(res);
        $scope.series = res.data.show.series//系列
        $scope.style = res.data.show.style//风格
        $scope.stairs = res.data.show.stairs_details//楼梯
        if (sessionStorage.getItem('params') == null) {
            $scope.params.series = $scope.series[0].id
            $scope.params.style = $scope.style[0].id
        }
        $scope.cur_series = $scope.series[0]
        $scope.cur_style = $scope.style[0]
    })
    //小区信息
    $scope.toponymy = {
        name: '',
        address: ''
    }
    if (sessionStorage.getItem('toponymy') != null) {
        $scope.toponymy = JSON.parse(sessionStorage.getItem('toponymy'))
    }
    /*存基本信息sessionStorage*/
    $scope.$watch('params', function (newVal, oldVal) {
        console.log(newVal);
        let index = Object.entries(newVal).findIndex(function (item) {
            return item[1] === ''
        })
        if(index == -1) {
            sessionStorage.setItem('params', JSON.stringify(newVal))
        }
    }, true)
    $scope.$watch('toponymy', function (newVal, oldVal) {
        sessionStorage.setItem('toponymy', JSON.stringify(newVal))
    }, true)
    //改变室厅厨卫
    $scope.changeQuantity = function (str, flag, limit) {
        if (flag == 1) {
            if ($scope.params[str.split('.')[1]] >= limit) {
                $scope.params[str.split('.')[1]] = limit
            } else {
                $scope.params[str.split('.')[1]]++
            }
        } else {
            if ($scope.params[str.split('.')[1]] <= limit) {
                $scope.params[str.split('.')[1]] = limit
            } else {
                $scope.params[str.split('.')[1]]--
            }
        }
    }
    //一级、二级分类数据请求
    _ajax.post('/owner/classify', {}, function (res) {
        console.log(res)
        $scope.first_level = res.data.pid.stair//一级
        $scope.second_level = res.data.pid.level//二级
    })
    //生成材料
    $scope.getMaterials = function (valid, error) {
        //分类商品初始化
        $scope.materials = angular.copy($scope.first_level)
        for (let [key, value] of $scope.materials.entries()) {
            value['cost'] = 0
            value['count'] = 0
            value['second_level'] = []
            value['procurement'] = 0
        }
        //工人费用初始化
        $scope.worker_list = []
        //请求生成材料
        $scope.words = '生成中...'
        $q.all([
            //水电工费用
            (function () {
                _ajax.get('/owner/plumber-price',$scope.params,function (res) {
                    console.log('水电工费用');
                    console.log(res);
                    let index = $scope.worker_list.findIndex(function (item) {
                        return item.worker_kind == res.labor_all_cost.worker_kind
                    })
                    if(index == -1){
                        $scope.worker_list.push(res.labor_all_cost)
                    }else{
                        $scope.worker_list[index].price += res.labor_all_cost.price
                    }
                })
            })(),
            //强弱电
            (function () {
                return _ajax.get('/owner/electricity', $scope.params, function (res) {
                    console.log('强弱电');
                    console.log(res);
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
                                        id: $scope.second_level[index1].id,
                                        title: $scope.second_level[index1].title,
                                        count: 0,
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
                                    value1.quantity += value.quantity
                                    value1.cost += value.cost
                                    value1.procurement += value.procurement
                                    value2.quantity += value.quantity
                                    value2.cost += value.cost
                                    value2.procurement += value.procurement
                                    if (index == -1) {
                                        value2.goods.push(value)
                                    } else {
                                        value2.goods[index].quantity += value.quantity
                                        value2.goods[index].cost += value.cost
                                        value2.goods[index].procurement += value.procurement
                                    }
                                }
                            }
                        }
                    }
                })
            })(),
            //水路
            (function () {
                return _ajax.get('/owner/waterway', $scope.params, function (res) {
                    console.log('水路');
                    console.log(res);
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
                                        id: $scope.second_level[index1].id,
                                        title: $scope.second_level[index1].title,
                                        count: 0,
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
                                    value1.quantity += value.quantity
                                    value1.cost += value.cost
                                    value1.procurement += value.procurement
                                    value2.quantity += value.quantity
                                    value2.cost += value.cost
                                    value2.procurement += value.procurement
                                    if (index == -1) {
                                        value2.goods.push(value)
                                    } else {
                                        value2.goods[index].quantity += value.quantity
                                        value2.goods[index].cost += value.cost
                                        value2.goods[index].procurement += value.procurement
                                    }
                                }
                            }
                        }
                    }
                })
            })(),
            //防水
            (function () {
                return _ajax.get('/owner/waterproof', $scope.params, function (res) {
                    console.log('防水');
                    console.log(res);
                    let index = $scope.worker_list.findIndex(function (item) {
                        return item.worker_kind == res.labor_all_cost.worker_kind
                    })
                    if(index == -1){
                        $scope.worker_list.push(res.labor_all_cost)
                    }else{
                        $scope.worker_list[index].price += res.labor_all_cost.price
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
                                        id: $scope.second_level[index1].id,
                                        title: $scope.second_level[index1].title,
                                        count: 0,
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
                                    value1.quantity += value.quantity
                                    value1.cost += value.cost
                                    value1.procurement += value.procurement
                                    value2.quantity += value.quantity
                                    value2.cost += value.cost
                                    value2.procurement += value.procurement
                                    if (index == -1) {
                                        value2.goods.push(value)
                                    } else {
                                        value2.goods[index].quantity += value.quantity
                                        value2.goods[index].cost += value.cost
                                        value2.goods[index].procurement += value.procurement
                                    }
                                }
                            }
                        }
                    }
                })
            })(),
            //木作
            (function () {
               _ajax.get('/owner/carpentry',$scope.params,function (res) {
                   console.log('木作');
                   console.log(res);
                   let index = $scope.worker_list.findIndex(function (item) {
                       return item.worker_kind == res.labor_all_cost.worker_kind
                   })
                   if(index == -1){
                       $scope.worker_list.push(res.labor_all_cost)
                   }else{
                       $scope.worker_list[index].price += res.labor_all_cost.price
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
                                       id: $scope.second_level[index1].id,
                                       title: $scope.second_level[index1].title,
                                       count: 0,
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
                                   value1.quantity += value.quantity
                                   value1.cost += value.cost
                                   value1.procurement += value.procurement
                                   value2.quantity += value.quantity
                                   value2.cost += value.cost
                                   value2.procurement += value.procurement
                                   if (index == -1) {
                                       value2.goods.push(value)
                                   } else {
                                       value2.goods[index].quantity += value.quantity
                                       value2.goods[index].cost += value.cost
                                       value2.goods[index].procurement += value.procurement
                                   }
                               }
                           }
                       }
                   }
               })
            })(),
            //乳胶漆
            (function () {
               _ajax.get('/owner/coating',$scope.params,function (res) {
                   console.log('乳胶漆');
                   console.log(res);
                   let index = $scope.worker_list.findIndex(function (item) {
                       return item.worker_kind == res.labor_all_cost.worker_kind
                   })
                   if(index == -1){
                       $scope.worker_list.push(res.labor_all_cost)
                   }else{
                       $scope.worker_list[index].price += res.labor_all_cost.price
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
                                       id: $scope.second_level[index1].id,
                                       title: $scope.second_level[index1].title,
                                       count: 0,
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
                                   value1.quantity += value.quantity
                                   value1.cost += value.cost
                                   value1.procurement += value.procurement
                                   value2.quantity += value.quantity
                                   value2.cost += value.cost
                                   value2.procurement += value.procurement
                                   if (index == -1) {
                                       value2.goods.push(value)
                                   } else {
                                       value2.goods[index].quantity += value.quantity
                                       value2.goods[index].cost += value.cost
                                       value2.goods[index].procurement += value.procurement
                                   }
                               }
                           }
                       }
                   }
               })
            })()
        ]).then(function () {
            console.log($scope.materials);
            console.log($scope.worker_list);
        })
        // if(valid){
        //
        // }else{
        // $scope.submitted = true
        // for (let [key, value] of error.entries()) {
        //     if (value.$invalid) {
        //         $anchorScroll.yOffset = 300
        //         $location.hash(value.$name)
        //         $anchorScroll()
        //         break
        //     }
        // }
        // }
    }
})