angular.module("all_controller", [])
    .controller("cell_search_ctrl", function ($scope, $http) {//小区搜索控制器
        $scope.data = ''
        $scope.getData = function () {
            let arr = []
            let url = "/owner/search"
            let data = {
                str: $scope.data
            }
            let config = {
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data)
                }
            }
            $scope.getData = function () {
                $http.post(url, data, config).then(function (response) {
                    console.log(response)
                    $scope.search_data = response.data.data.effect
                    // console.log(response)
                    // for (let item of response.data.data.effect) {
                    //     if (item.toponymy.indexOf($scope.data) != -1 && $scope.data != '') {
                    //         arr.push({"toponymy": item.toponymy, "site_particulars": item.site_particulars})
                    //     }
                    // }
                    // $scope.search_data = arr;
                }, function (response) {

                })
            }
        }
    })
    .controller("intelligent_index_ctrl", function ($scope, $http) {//主页控制器
        /* $scope.get_quotation = function () {
         $http({
         url:"/owner/search?id=1",
         method:"get"
         }).then(function (response) {
         $scope.data = response.data.data.effect
         $scope.imgSrc =  response.data.data.effect_picture
         $scope.all_data = {toponymy:$scope.data.toponymy,site_particulars:$scope.data.site_particulars,
         high:$scope.data.high,particulars:$scope.data.particulars,window:$scope.data.window}
         console.log(response)
         },function (response) {

         })
         }*/
    })
<<<<<<< HEAD
    //.controller("intelligent_nodata_ctrl", function ($scope, $stateParams, $http, $state) { //无数据控制器
    //    console.log($stateParams)
    //    $scope.message = ''
    //    $scope.nowStyle = '现代简约'
    //    $scope.nowStairs = '实木结构'
    //    $scope.nowSeries = '齐家'
    //    $scope.area = $stateParams.area || ''
    //    $scope.series_index = $stateParams.series_index || 0 //系列编号
    //    $scope.style_index = $stateParams.style_index || 0 //风格编号
    //    $scope.window = $stateParams.window || ''
    //    $scope.labor_price = $stateParams.labor_price || 0  //工人总费用
    //    $scope.labor_category = $stateParams.worker_category || {}//工人详细费用
    //    $scope.toponymy = $stateParams.toponymy || ''
    //    $scope.choose_stairs = $stateParams.choose_stairs || false;
    //    console.log($scope.choose_stairs)
    //    $scope.stair = $stateParams.stair //默认一级传递值
    //    $scope.level = $stateParams.level //默认二级传递值
    //    $scope.isClick = $stateParams.isBack || false
    //    $scope.handyman_price = $stateParams.worker_category['杂工'] || 0
    //    //生成材料变量
    //    $scope.house_bedroom = $stateParams.house_bedroom || 1
    //    $scope.house_hall = $stateParams.house_hall || 1
    //    $scope.house_kitchen = $stateParams.house_kitchen || 1
    //    $scope.house_toilet = $stateParams.house_toilet || 1
    //    $scope.highCrtl = $stateParams.highCrtl || 2.8
    //
    //
    //    //无资料户型加减方法
    //    $scope.add = function (item, category) {
    //        if ($scope[category] < item) {
    //            $scope[category]++
    //        } else {
    //            $scope[category] = item
    //        }
    //    }
    //    $scope.subtract = function (item, category) {
    //        if ($scope[category] > item) {
    //            $scope[category]--
    //        } else {
    //            $scope[category] = item
    //        }
    //    }
    //    let config = {
    //        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    //        transformRequest: function (data) {
    //            return $.param(data)
    //        }
    //    }
    //    //生成材料方法
    //    $scope.getData = function () {
    //        $http.post("/owner/classify", {}, config).then(function (response) {
    //            $scope.level = response.data.data.pid.level
    //            $scope.stair = response.data.data.pid.stair
    //            for (let [key, value] of $scope.level.entries()) {
    //                value["cost"] = 0
    //                value["three_level"] = []
    //            }
    //            for (let [key, value] of $scope.stair.entries()) {
    //                value["cost"] = 0
    //                value["second_level"] = []
    //                value["labor_total_price"] = 0
    //            }
    //        }, function () {
    //
    //        })
    //        let url = "/owner/weak-current"
    //        let strong = "/owner/strong-current"
    //        let waterway = "/owner/waterway"
    //        let waterproof = "/owner/waterproof"
    //        let carpentry = "/owner/carpentry"
    //        let coating = "/owner/coating"
    //        let make = "/owner/mud-make"
    //        let material = "/owner/principal-material"
    //        let soft = "/owner/soft-outfit-assort"
    //        let fixation = "/owner/fixation-furniture"
    //        let move = "/owner/move-furniture"
    //        let assort = "/owner/appliances-assort"
    //        let life = "/owner/life-assort"
    //        let intelligence = "/owner/intelligence-assort"
    //        let data = {
    //            bedroom: $scope.house_bedroom,
    //            area: $scope.area,      //面积
    //            hall: $scope.house_hall,       //餐厅
    //            toilet: $scope.house_toilet,   // 卫生间
    //            kitchen: $scope.house_kitchen,  //厨房
    //            stairway: +$scope.choose_stairs, //楼梯
    //            structure: $scope.nowStairs,
    //            series: $scope.series_index + 1,   //系列
    //            style: $scope.style_index + 1,  //风格
    //            window: $scope.window,//飘窗
    //            high: $scope.highCrtl, //层高
    //            province: 510000,   //省编码
    //            city: 510100      // 市编码
    //        }
    //        let labor_category = {"worker_category": ['杂工'], '杂工': {'price': 0, 'worker_kind': '杂工'}}
    //        let data1 = {}
    //        for (let i in data) {
    //            data1[i] = data[i]
    //        }
    //        data1["waterproof_total_area"] = 60
    //        //发数据给后台
    //        //第一个 弱电接口
    //        $http.post(url, data, config).then(function (response) {
    //            console.log("弱电")
    //            console.log(response)
    //            $scope.labor_price += response.data.data.weak_current_labor_price.price
    //            let arr = response.data.data.weak_current_material
    //            let weak_arr = []
    //            for (let item in arr) {
    //                if (item != "total_cost") {
    //                    weak_arr.push(arr[item])
    //                }
    //            }
    //            console.log(weak_arr)
    //            //一级总费用统计
    //            for (let [key, value] of weak_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    if (value.path.split(',')[0] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //二级总费用统计
    //            for (let [key, value] of weak_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //
    //            //工人费用处理
    //            if (labor_category["worker_category"].indexOf(response.data.data.weak_current_labor_price.worker_kind) == -1) {
    //                labor_category["worker_category"].push(response.data.data.weak_current_labor_price.worker_kind)
    //                labor_category[response.data.data.weak_current_labor_price.worker_kind + ''] = response.data.data.weak_current_labor_price
    //            } else {
    //                for (let [key, value] of labor_category["worker_category"].entries()) {
    //                    if (response.data.data.weak_current_labor_price.worker_kind == value) {
    //                        if (!labor_category[value + '']) {
    //                            labor_category[value + ''] = response.data.data.weak_current_labor_price
    //                        } else {
    //                            labor_category[value + ''].price += response.data.data.weak_current_labor_price.price
    //                        }
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of weak_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.cost
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].cost += value.cost
    //                                value1[value.path.split(',')[2]][value.id].quantity += value.quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of weak_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            $scope.labor_category = labor_category
    //            console.log($scope.level)
    //            console.log($scope.stair)
    //            console.log(labor_category)
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第二个 强电接口
    //        $http.post(strong, data, config).then(function (response) {
    //            console.log("强电")
    //            console.log(response)
    //            $scope.labor_price += response.data.data.strong_current_labor_price.price
    //            let arr = response.data.data.strong_current_material
    //            let strong_arr = []
    //            for (let item in arr) {
    //                if (item != "total_cost") {
    //                    strong_arr.push(arr[item])
    //                }
    //            }
    //            console.log(strong_arr)
    //            //一级总费用统计
    //            for (let [key, value] of strong_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    if (value.path.split(',')[0] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //二级总费用统计
    //            for (let [key, value] of strong_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //工人费用处理
    //            if (labor_category["worker_category"].indexOf(response.data.data.strong_current_labor_price.worker_kind) == -1) {
    //                labor_category["worker_category"].push(response.data.data.strong_current_labor_price.worker_kind)
    //                labor_category[response.data.data.strong_current_labor_price.worker_kind + ''] = response.data.data.strong_current_labor_price
    //            } else {
    //                for (let [key, value] of labor_category["worker_category"].entries()) {
    //                    if (response.data.data.strong_current_labor_price.worker_kind == value) {
    //                        if (!labor_category[value + '']) {
    //                            labor_category[value + ''] = response.data.data.strong_current_labor_price
    //                        } else {
    //                            labor_category[value + ''].price += response.data.data.strong_current_labor_price.price
    //                        }
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of strong_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.cost
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].cost += value.cost
    //                                value1[value.path.split(',')[2]][value.id].quantity += value.quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of strong_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            $scope.labor_category = labor_category
    //            console.log(labor_category)
    //            console.log($scope.level)
    //            console.log($scope.stair)
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第三个 水路接口
    //        $http.post(waterway, data, config).then(function (response) {
    //            console.log("水路")
    //            // console.log(response)
    //            let arr = response.data.data.waterway_material_price
    //            let waterway_arr = []
    //            for (let item in arr) {
    //                if (item != "total_cost") {
    //                    waterway_arr.push(arr[item])
    //                }
    //            }
    //            console.log(waterway_arr)
    //            //一级总费用统计
    //            for (let [key, value] of waterway_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    if (value.path.split(',')[0] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //二级总费用统计
    //            for (let [key, value] of waterway_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of waterway_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.cost
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].cost += value.cost
    //                                value1[value.path.split(',')[2]][value.id].quantity += value.quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of waterway_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第四个 防水接口
    //        $http.post(waterproof, data, config).then(function (response) {
    //            console.log("防水")
    //            console.log(response)
    //            $scope.labor_price += response.data.data.waterproof_labor_price.price
    //            let carpentry = response.data.data.waterproof_material
    //            //一级总费用统计
    //            for (let [key1, value1] of $scope.stair.entries()) {
    //                if (carpentry.path.split(',')[0] == value1.id) {
    //                    value1["cost"] += carpentry.cost
    //                }
    //            }
    //            //二级总费用统计
    //            for (let [key1, value1] of $scope.level.entries()) {
    //                if (carpentry.path.split(',')[1] == value1.id) {
    //                    value1["cost"] += carpentry.cost
    //                }
    //            }
    //            //工人费用处理
    //            if (labor_category["worker_category"].indexOf(response.data.data.waterproof_labor_price.worker_kind) == -1) {
    //                labor_category["worker_category"].push(response.data.data.waterproof_labor_price.worker_kind)
    //                labor_category[response.data.data.waterproof_labor_price.worker_kind + ''] = response.data.data.waterproof_labor_price
    //            } else {
    //                for (let [key, value] of labor_category["worker_category"].entries()) {
    //                    if (response.data.data.waterproof_labor_price.worker_kind == value) {
    //                        if (!labor_category[value + '']) {
    //                            labor_category[value + ''] = response.data.data.waterproof_labor_price
    //                        } else {
    //                            labor_category[value + ''].price += response.data.data.waterproof_labor_price.price
    //                        }
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key1, value1] of $scope.level.entries()) {
    //                if (carpentry.path.split(',')[1] == value1.id) {
    //                    if (value1.three_level.indexOf(carpentry.path.split(',')[2]) == -1) {
    //                        value1.three_level.push(carpentry.path.split(',')[2])
    //                        if (!value1[carpentry.path.split(',')[2]]) {
    //                            value1[carpentry.path.split(',')[2]] = {'goods_detail': [], 'cost': 0}
    //                        }
    //                        value1[carpentry.path.split(',')[2]][carpentry.id] = carpentry
    //                        value1[carpentry.path.split(',')[2]]['goods_detail'].push(carpentry.id)
    //                        value1[carpentry.path.split(',')[2]].cost = carpentry.cost
    //                    } else {
    //                        if (value1[carpentry.path.split(',')[2]]['goods_detail'].indexOf(carpentry.id) == -1) {
    //                            value1[carpentry.path.split(',')[2]]['goods_detail'].push(carpentry.id)
    //                            value1[carpentry.path.split(',')[2]][carpentry.id] = carpentry
    //                            value1[carpentry.path.split(',')[2]].cost += value.cost
    //                        } else {
    //                            value1[carpentry.path.split(',')[2]][carpentry.id].cost += carpentry.cost
    //                            value1[carpentry.path.split(',')[2]][carpentry.id].quantity += carpentry.quantity
    //                            value1[carpentry.path.split(',')[2]].cost += value.cost
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key1, value1] of $scope.stair.entries()) {
    //                for (let [key2, value2] of $scope.level.entries()) {
    //                    if (carpentry.path.split(',')[0] == value1.id && carpentry.path.split(',')[1] == value2.id) {
    //                        if (value1.second_level.indexOf(value2.id) == -1) {
    //                            value1.second_level.push(value2.id)
    //                            value1[value2.id] = value2
    //                        } else {
    //                            value1[value2.id] = value2
    //                        }
    //                    }
    //                }
    //            }
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第五个 木作接口
    //        $http.post(carpentry, data, config).then(function (response) {
    //            console.log("木作")
    //            console.log(response)
    //            $scope.labor_price += response.data.data.carpentry_labor_price.price
    //            let arr = response.data.data.carpentry_material
    //            let carpentry_arr = []
    //            for (let item in arr) {
    //                if (item != "total_cost") {
    //                    carpentry_arr.push(arr[item])
    //                }
    //            }
    //            console.log(carpentry_arr)
    //            //一级总费用统计
    //            for (let [key, value] of carpentry_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    if (value.path.split(',')[0] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //二级总费用统计
    //            for (let [key, value] of carpentry_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //工人费用处理
    //            if (labor_category["worker_category"].indexOf(response.data.data.carpentry_labor_price.worker_kind) == -1) {
    //                labor_category["worker_category"].push(response.data.data.carpentry_labor_price.worker_kind)
    //                labor_category[response.data.data.carpentry_labor_price.worker_kind + ''] = response.data.data.carpentry_labor_price
    //            } else {
    //                for (let [key, value] of labor_category["worker_category"].entries()) {
    //                    if (response.data.data.carpentry_labor_price.worker_kind == value) {
    //                        if (!labor_category[value + '']) {
    //                            labor_category[value + ''] = response.data.data.carpentry_labor_price
    //                        } else {
    //                            labor_category[value + ''].price += response.data.data.carpentry_labor_price.price
    //                        }
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of carpentry_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.cost
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].cost += value.cost
    //                                value1[value.path.split(',')[2]][value.id].quantity += value.quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of carpentry_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            $scope.labor_category = labor_category
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第六个 乳胶漆接口
    //        $http.post(coating, data, config).then(function (response) {
    //            console.log("乳胶漆")
    //            console.log(response)
    //            $scope.labor_price += response.data.data.coating_labor_price.price
    //            let arr = response.data.data.coating_material
    //            let coating_arr = []
    //            for (let item in arr) {
    //                if (item != "total_cost") {
    //                    coating_arr.push(arr[item])
    //                }
    //            }
    //            console.log(coating_arr)
    //            //一级总费用统计
    //            for (let [key, value] of coating_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    if (value.path.split(',')[0] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //二级总费用统计
    //            for (let [key, value] of coating_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.cost
    //                    }
    //                }
    //            }
    //            //工人费用处理
    //            if (labor_category["worker_category"].indexOf(response.data.data.coating_labor_price.worker_kind) == -1) {
    //                labor_category["worker_category"].push(response.data.data.coating_labor_price.worker_kind)
    //                labor_category[response.data.data.coating_labor_price.worker_kind + ''] = response.data.data.coating_labor_price
    //            } else {
    //                for (let [key, value] of labor_category["worker_category"].entries()) {
    //                    if (response.data.data.coating_labor_price.worker_kind == value) {
    //                        if (!labor_category[value + '']) {
    //                            labor_category[value + ''] = response.data.data.coating_labor_price
    //                        } else {
    //                            labor_category[value + ''].price += response.data.data.coating_labor_price.price
    //                        }
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of coating_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.cost
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].cost += value.cost
    //                                value1[value.path.split(',')[2]][value.id].quantity += value.quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of coating_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            $scope.labor_category = labor_category
    //            console.log(labor_category)
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第七个 泥作接口
    //        $http.post(make, data1, config).then(function (response) {
    //            console.log("泥作")
    //            console.log(response)
    //            // let arr = response.data.data.mud_make_material
    //            // let mud_make_arr = []
    //            // for (let item in arr) {
    //            //     if (item != "total_cost") {
    //            //         mud_make_arr.push(arr[item])
    //            //     }
    //            // }
    //            //一级总费用统计
    //            // for (let [key, value] of mud_make_arr.entries()) {
    //            //     for (let [key1, value1] of $scope.stair.entries()) {
    //            //         if (value.path.split(',')[0] == value1.id) {
    //            //             value1["cost"] += value.cost
    //            //         }
    //            //     }
    //            // }
    //            //二级总费用统计
    //            // for (let [key, value] of mud_make_arr.entries()) {
    //            //     for (let [key1, value1] of $scope.level.entries()) {
    //            //         if (value.path.split(',')[1] == value1.id) {
    //            //             value1["cost"] +=  value.cost
    //            //         }
    //            //     }
    //            // }
    //            //一级二级三级整合
    //            // for (let [key, value] of mud_make_arr.entries()) {
    //            //     for (let [key1, value1] of $scope.stair.entries()) {
    //            //         for (let [key2, value2] of $scope.level.entries()) {
    //            //             if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
    //            //                 value1[value2.id] = {"title":value2.title,"cost":value2.cost}
    //            //                 if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1){
    //            //                     value1["second_level"].push(value2.id)
    //            //                 }
    //            //                 if(value2[value.category_id]){
    //            //                     value2[value.category_id] = {"title":value.title,"name":value.name,"platform_price":value.platform_price,"cost":value.cost+value2[value.category_id].cost,
    //            //                         "quantity":value.quantity+value2[value.category_id].quantity,"subtitle":value.subtitle}
    //            //                 }else{
    //            //                     value2[value.category_id] = {"title":value.title,"name":value.name,"platform_price":value.platform_price,"cost":value.cost,
    //            //                         "quantity":value.quantity,"subtitle":value.subtitle}
    //            //                 }
    //            //                 if(!value2["three_level"] || value2["three_level"].indexOf(value.category_id) == -1){
    //            //                     value2["three_level"].push(value.category_id)
    //            //                 }
    //            //             }
    //            //         }
    //            //     }
    //            // }
    //            // for(let [key,value] of $scope.stair.entries())
    //            //     for(let [key1,value1] of $scope.level.entries()){
    //            //         if( value1.id in value){
    //            //             value[value1.id] = value1
    //            //         }
    //            //     }
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第八个 主材接口
    //        $http.post(material, data, config).then(function (response) {
    //            console.log("主材")
    //            console.log(response)
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第九个 软装接口
    //        $http.post(soft, data, config).then(function (response) {
    //            console.log("软装")
    //            console.log(response)
    //            let soft_arr = response.data.data.goods
    //            console.log(soft_arr)
    //            //一级总费用统计
    //            for (let [key, value] of soft_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    if (value.path.split(',')[0] == value1.id) {
    //                        value1["cost"] += value.show_price
    //                    }
    //                }
    //            }
    //            //二级总费用统计
    //            for (let [key, value] of soft_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.show_price
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of soft_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.show_price
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.show_price
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].show_price += value.show_price
    //                                value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of soft_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            console.log($scope.stair)
    //            console.log($scope.level)
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第十个 固定家居接口
    //        $http.post(fixation, data, config).then(function (response) {
    //            console.log("固定家具")
    //            console.log(response)
    //            let fixation_arr = response.data.data.goods
    //            for (let [key, value] of fixation_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.show_price
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of fixation_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.show_price
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.show_price
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].show_price += value.show_price
    //                                value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of fixation_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第十一个 移动家具接口
    //        $http.post(move, data, config).then(function (response) {
    //            console.log("移动家具")
    //            console.log(response)
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第十二个 家电配套接口
    //        $http.post(assort, data, config).then(function (response) {
    //            console.log("家电配套")
    //            console.log(response)
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第十三个 生活配套接口
    //        $http.post(life, data, config).then(function (response) {
    //            console.log("生活配套")
    //            console.log(response)
    //            let life_arr = response.data.data.goods
    //            for (let [key, value] of life_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.show_price
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of life_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.show_price
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.show_price
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].show_price += value.show_price
    //                                value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of life_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        //第十四个 智能配套接口
    //        $http.post(intelligence, data, config).then(function (response) {
    //            console.log("智能配套")
    //            console.log(response)
    //            let intelligence_arr = response.data.data.goods
    //            for (let [key, value] of intelligence_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        value1["cost"] += value.show_price
    //                    }
    //                }
    //            }
    //            //整合一级二级三级
    //            for (let [key, value] of intelligence_arr.entries()) {
    //                for (let [key1, value1] of $scope.level.entries()) {
    //                    if (value.path.split(',')[1] == value1.id) {
    //                        if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
    //                            value1.three_level.push(value.path.split(',')[2])
    //                            if (!value1[value.path.split(',')[2]]) {
    //                                value1[value.path.split(',')[2]] = {
    //                                    'goods_detail': [], 'cost': 0,
    //                                    'id': value.path.split(',')[2], 'title': value.title
    //                                }
    //                            }
    //                            value1[value.path.split(',')[2]][value.id] = value
    //                            value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                            value1[value.path.split(',')[2]].cost = value.show_price
    //                        } else {
    //                            if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
    //                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
    //                                value1[value.path.split(',')[2]][value.id] = value
    //                                value1[value.path.split(',')[2]].cost += value.show_price
    //                            } else {
    //                                value1[value.path.split(',')[2]][value.id].show_price += value.show_price
    //                                value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
    //                                value1[value.path.split(',')[2]].cost += value.cost
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //            for (let [key, value] of intelligence_arr.entries()) {
    //                for (let [key1, value1] of $scope.stair.entries()) {
    //                    for (let [key2, value2] of $scope.level.entries()) {
    //                        if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
    //                            if (value1.second_level.indexOf(value2.id) == -1) {
    //                                value1.second_level.push(value2.id)
    //                                value1[value2.id] = value2
    //                            } else {
    //                                value1[value2.id] = value2
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        $scope.isClick = true
    //    }
    //    //传递数据
    //    $scope.goDetail = function (item, index) {
    //        console.log(item)
    //        console.log($scope.series_index)
    //        console.log($scope.labor_category)
    //        if (item.title == "辅材") {
    //            $state.go("basics_decoration", {
    //                'stair': $scope.stair,
    //                'level': $scope.level,
    //                'stair_copy': angular.copy($scope.stair),
    //                'level_copy': angular.copy($scope.level),
    //                'index': index,
    //                'worker_category': $scope.labor_category,
    //                'handyman_price': $scope.handyman_price,
    //                'area': $scope.area,
    //                'series_index': $scope.series_index,
    //                'style_index': $scope.style_index,
    //                'labor_price': $scope.labor_price,
    //                'house_bedroom': $scope.house_bedroom,
    //                'house_hall': $scope.house_hall,
    //                'house_kitchen': $scope.house_kitchen,
    //                'house_toilet': $scope.house_toilet,
    //                'highCrtl': $scope.highCrtl,
    //                'window': $scope.window,
    //                'choose_stairs': $scope.choose_stairs
    //            })
    //        } else if (item.title == "主要材料") {
    //            $state.go("")
    //        } else {
    //            $state.go("other_material", {
    //                'stair': $scope.stair,
    //                'stair_copy': $scope.stair,
    //                'level_copy': $scope.level,
    //                'index': index,
    //                'level': $scope.level,
    //                'worker_category': $scope.labor_category,
    //                'handyman_price': $scope.handyman_price,
    //                'area': $scope.area,
    //                'series_index': $scope.series_index,
    //                'style_index': $scope.style_index,
    //                'labor_price': $scope.labor_price,
    //                'house_bedroom': $scope.house_bedroom,
    //                'house_hall': $scope.house_hall,
    //                'house_kitchen': $scope.house_kitchen,
    //                'house_toilet': $scope.house_toilet,
    //                'highCrtl': $scope.highCrtl,
    //                'window': $scope.window,
    //                'choose_stairs': $scope.choose_stairs
    //            })
    //        }
    //    }
    //    //详细地址监听
    //    $scope.$watch('message', function (newVal, oldVal) {
    //        if (newVal && newVal != oldVal) {
    //            if (newVal.length > 45) {
    //                $scope.message = newVal.substr(0, 45)
    //            }
    //        }
    //    })
    //
    //    //请求后台数据
    //    $http.get('/owner/series-and-style').then(function (response) {
    //        $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
    //        $scope.series = response.data.data.show.series;//系列数据
    //        $scope.style = response.data.data.show.style;//风格数据
    //        $scope.style_picture = response.data.data.show.style_picture;//轮播图片数据
    //        console.log($scope.series)
    //    }, function (response) {
    //
    //    })
    //    //切换楼梯
    //    $scope.toggleStairs = function (item) {
    //        $scope.nowStairs = item;
    //    }
    //    //切换系列
    //    $scope.toggleSeries = function (item, index) {
    //        $scope.nowSeries = item;
    //        $scope.series_index = index
    //        $scope.swiperImg = $scope.style_picture.slice(index, index * 3)
    //    }
    //    //切换风格
    //    $scope.toggleStyle = function (item, index) {
    //        $scope.style_index = index
    //        $scope.nowStyle = item;
    //    }
    //})
=======
    .controller("intelligent_nodata_ctrl", function ($scope, $stateParams, $http) { //无数据控制器
        $scope.message = ''
        $scope.nowStyle = '现代简约'
        $scope.nowStairs = '实木结构'
        $scope.nowSeries = '齐家'
        $scope.series_index = 0
        $scope.style_index = 0
        $scope.window = ''
        $scope.toponymy =''|| $stateParams.toponymy
        $scope.choose_stairs = true;
        //生成材料变量
        $scope.house_bedroom = 1
        $scope.house_hall = 1
        $scope.house_kitchen = 1
        $scope.house_toilet = 1
        $scope.highCrtl = 2.8


        //无资料户型加减方法
        $scope.add = function (item,category) {
            if($scope[category]<item){
                $scope[category]++
            }else{
                $scope[category] = item
            }
        }
        $scope.subtract = function (item,category) {
            if($scope[category]>item){
                $scope[category]--
            }else{
                $scope[category] = item
            }
        }
        let config = {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            transformRequest:function (data) {
                return $.param(data)
            }
        }
        //生成材料方法
        $scope.getData = function () {
            $http.post("/owner/classify", {}, config).then(function (response) {
                $scope.level = response.data.data.pid.level
                $scope.stair = response.data.data.pid.stair
                for (let [key, value] of $scope.level.entries()) {
                    value["cost"] = 0
                    value["second_level"] = []
                }
                for (let [key, value] of $scope.stair.entries()) {
                    value["cost"] = 0
                    value["second_level"] = []
                }
            }, function () {

            })
            let url = "/owner/weak-current"
            let strong ="/owner/strong-current"
            let waterway="/owner/waterway"
            let waterproof="/owner/waterproof"
            let carpentry="/owner/carpentry"
            let coating="/owner/coating"
            let make ="/owner/mud-make"
            let material="/owner/principal-material"
            let soft="/owner/soft-outfit-assort"
            let fixation= "/owner/fixation-furniture"
            let move= "/owner/move-furniture"
            let assort="/owner/appliances-assort"
            let life="/owner/life-assort"
            let intelligence="/owner/intelligence-assort"
            let data = {
                bedroom:$scope.house_bedroom,
                area:$scope.area,      //面积
                hall:$scope.house_hall,       //餐厅
                toilet:$scope.house_toilet,   // 卫生间
                kitchen:$scope.house_kitchen,  //厨房
                stairway:+$scope.choose_stairs, //楼梯
                structure:$scope.nowStairs,
                series:$scope.series_index+1,   //系列
                style:$scope.style_index+1,  //风格
                window:$scope.window,//飘窗
                high:$scope.highCrtl, //层高
                province:510000,   //省编码
                city:510100      // 市编码
            }
            //发数据给后台
            //第一个 弱电接口
            $http.post(url,data,config).then(function (response) {
                console.log("弱电")
                // console.log(response)
                let arr = response.data.data.weak_current_material
                let weak_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        weak_arr.push(arr[item])
                    }
                }
                console.log(weak_arr)
                for (let [key, value] of weak_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] +=  value.cost
                        }
                    }
                }
                for (let [key, value] of weak_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
                                value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                                if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                                    value1["second_level"].push(value2.id)
                            }
                        }
                    }
                }
                console.log($scope.level)
                console.log($scope.stair)
            },function (error) {
                console.log(error)
            })
            //第二个 强电接口
            $http.post(strong,data,config).then(function (response) {
                console.log("强电")
                // console.log(response)
                let arr = response.data.data.strong_current_material
                let strong_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        strong_arr.push(arr[item])
                    }
                }
                console.log(strong_arr)
                for (let [key, value] of strong_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] +=  value.cost
                        }
                    }
                }
                for (let [key, value] of strong_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                                if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                                    value1["second_level"].push(value2.id)
                            }
                        }
                    }
                }
            },function (error) {
                console.log(error)
            })
            //第三个 水路接口
            $http.post(waterway,data,config).then(function (response) {
                console.log("水路")
                // console.log(response)
                let arr = response.data.data.waterway_material_price
                let waterway_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        waterway_arr.push(arr[item])
                    }
                }
                console.log(waterway_arr)
                for (let [key, value] of waterway_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] +=  value.cost
                        }
                    }
                }
                for (let [key, value] of waterway_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
                                value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                                if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                                    value1["second_level"].push(value2.id)
                            }
                        }
                    }
                }
            },function (error) {
                console.log(error)
            })
            //第四个 防水接口
            $http.post(waterproof,data,config).then(function (response) {
                console.log("防水")
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第五个 木作接口
            $http.post(carpentry,data,config).then(function (response) {
                console.log("木作")
                // console.log(response)
                let arr = response.data.data.carpentry_material
                let carpentry_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        carpentry_arr.push(arr[item])
                    }
                }
                console.log(carpentry_arr)
                for (let [key, value] of carpentry_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] +=  value.cost
                        }
                    }
                }
                for (let [key, value] of carpentry_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
                                value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                                if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                                    value1["second_level"].push(value2.id)
                            }
                        }
                    }
                }
            },function (error) {
                console.log(error)
            })
            //第六个 乳胶漆接口
            $http.post(coating,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第七个 泥作接口
            $http.post(make,data,config).then(function (response) {
                console.log("泥作")
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第八个 主材接口
            $http.post(material,data,config).then(function (response) {
                console.log("主材")
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第九个 软装接口
            $http.post(soft,data,config).then(function (response) {
                console.log("软装")
                console.log(response)
                // let arr = response.data.data.carpentry_material
                // let carpentry_arr = []
                // for (let item in arr) {
                //     if (item != "total_cost") {
                //         carpentry_arr.push(arr[item])
                //     }
                // }
                // console.log(carpentry_arr)
                // for (let [key, value] of carpentry_arr.entries()) {
                //     for (let [key1, value1] of $scope.level.entries()) {
                //         if (value.path.split(',')[1] == value1.id) {
                //             value1["cost"] +=  value.cost
                //         }
                //     }
                // }
                // for (let [key, value] of carpentry_arr.entries()) {
                //     for (let [key1, value1] of $scope.stair.entries()) {
                //         for (let [key2, value2] of $scope.level.entries()) {
                //             if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
                //                 value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                //                 if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                //                     value1["second_level"].push(value2.id)
                //             }
                //         }
                //     }
                // }
            },function (error) {
                console.log(error)
            })
            //第十个 固定家居接口
            $http.post(fixation,data,config).then(function (response) {
                console.log("固定家具")
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第十一个 移动家具接口
            $http.post(move,data,config).then(function (response) {
                console.log("移动家具")
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第十二个 家电配套接口
            $http.post(assort,data,config).then(function (response) {
                console.log("家电配套")
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第十三个 生活配套接口
            $http.post(life,data,config).then(function (response) {
                console.log("生活配套")
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第十四个 智能配套接口
            $http.post(intelligence,data,config).then(function (response) {
                console.log("智能配套")
                console.log(response)
            },function (error) {
                console.log(error)
            })

        }
        //详细地址监听
        $scope.$watch('message', function (newVal, oldVal) {
            if (newVal && newVal != oldVal) {
                if (newVal.length > 45) {
                    $scope.message = newVal.substr(0, 45)
                }
            }
        })

        //请求后台数据
        $http.get('/owner/series-and-style').then(function (response) {
            $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
            $scope.series = response.data.data.show.series;//系列数据
            $scope.style = response.data.data.show.style;//风格数据
            $scope.style_picture = response.data.data.show.style_picture;//轮播图片数据
            console.log($scope.series)
        }, function (response) {

        })
        //切换楼梯
        $scope.toggleStairs = function (item) {
            $scope.nowStairs = item;
        }
        //切换系列
        $scope.toggleSeries = function (item,index) {
            $scope.nowSeries = item;
            $scope.series_index = index
            $scope.swiperImg = $scope.style_picture.slice(index,index*3)
        }
        //切换风格
        $scope.toggleStyle = function (item,index) {
            $scope.style_index = index
            $scope.nowStyle = item;
        }
    })
>>>>>>> 3744ed0535f2dab0266c0771190b54bf355d2ac8
    .controller("move_furniture_ctrl", function ($scope, $http) {//移动家具控制器
        $http({
            method: 'get',
            url: "/mall/categories"
        }).then(function successCallback(response) {
            $scope.message = response.data.data.categories;
        }, function errorCallback(response) {

        });
    })
<<<<<<< HEAD
    .controller("location_city_ctrl",function ($scope) { //城市选择控制器
        // $scope.goPrev = function () {
        //     window.history.back()
        // }
        })
    .controller("intelligent_quotation_ctrl",function ($scope,$http,$stateParams) {//有资料选择器
          $scope.name = $stateParams.name;
          $scope.address = $stateParams.address;
          $scope.pic_one = $stateParams.pic_one;
          $scope.pic_two = $stateParams.pic_two;
          console.log($stateParams);

    //    $scope.nowSeries ='齐家'; //系列
    //    $scope.nowStyle = '现代简约'; //风格
    //    $scope.nowStairs = '实木结构'; //有楼梯风格
    //    $scope.choose_stairs = true; //是否有楼梯
    //    $scope.index = 0;
    //    $scope.house_index = 0;//户型
    //    $scope.id = $stateParams.id;//主页传过来的id
    //    let url = "/owner/search";//搜索url
    //    let data = {
    //        id:$stateParams.id//有资料请求id
    //    }
    //    let config = {//所有post请求必须的请求头
    //        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    //        transformRequest:function (data) {
    //            return $.param(data)
    //        }
    //    }
    //    $http.post(url,data,config).then(function (response) {//小区房型基本信息
    //        console.log(response);
    //        $scope.data = response.data.data.effect//房型所有信息
    //        $scope.series_id = response.data.data.effect[0].series_id//默认房型系列id
    //        $scope.style_id = response.data.data.effect[0].style_id//默认房型风格id
    //        $scope.imgSrc = response.data.data.effect_picture//默认房型对应轮播图
    //        $scope.isNoChange = true//可选择项是否修改
    //        $scope.labor_price = 0 //工人费用
    //        let data = {//请求发送的数据
    //            area: response.data.data.effect[0].area,      //面积
    //            bedroom: response.data.data.effect[0].bedroom, //卧室
    //            hall: response.data.data.effect[0].sittingRoom_diningRoom,       //餐厅
    //            toilet: response.data.data.effect[0].toilet,   // 卫生间
    //            kitchen: response.data.data.effect[0].kitchen,  //厨房
    //            stairway: response.data.data.effect[0].stairway, //楼梯
    //            series: response.data.data.effect[0].series_id,   //系列
    //            style: response.data.data.effect[0].style_id,  //风格
    //            window: response.data.data.effect[0].window,//飘窗
    //            province: 510000,   //省编码
    //            city: 510100      // 市编码
    //        }
    //        // //泥作请求发送的数据:不一样
    //        // let data1 = {}
    //        // for(let i in data){
    //        //     data1[i] = data[i]
    //        // }
    //        // data1["waterproof_total_area"] = 60
    //        // //一系列请求
    //        $http.post("/owner/weak-current", data, config).then(function (response) {//弱电请求
    //            console.log("弱电请求"+response)
    //            console.log(response)
    //            $scope.weak_current_category = response.data.data.weak_current
    //            $scope.weak_current_bottom_case = response.data.data.weak_current_bottom_case
    //            $scope.weak_current_labor_price = response.data.data.weak_current_labor_price
    //            $scope.weak_current_material_price = response.data.data.weak_current_material_price
    //            $scope.weak_current_reticle_cost = response.data.data.weak_current_reticle_cost
    //            $scope.weak_current_reticle_quantity = response.data.data.weak_current_reticle_quantity
    //            $scope.weak_current_spool_cost = response.data.data.weak_current_spool_cost
    //            $scope.weak_current_spool_quantity = response.data.data.weak_current_spool_quantity
    //            $scope.labor_price += $scope.weak_current_labor_price
    //        }, function (error) {
    //            console.log(error)
    //        })
    //        // $http.post("/owner/strong-current", data, config).then(function (response) {//强电请求
    //        //     console.log("强电请求"+response)
    //        //     console.log(response)
    //        //     $scope.strong_current_category = response.data.data.strong_current
    //        //     $scope.strong_current_bottom_case = response.data.data.strong_current_bottom_case
    //        //     $scope.strong_current_labor_price = response.data.data.strong_current_labor_price
    //        //     $scope.strong_current_material_price = response.data.data.strong_current_material_price
    //        //     $scope.strong_current_wire_cost = response.data.data.strong_current_wire_cost
    //        //     $scope.strong_current_wire_quantity = response.data.data.strong_current_wire_quantity
    //        //     $scope.strong_current_spool_cost = response.data.data.strong_current_spool_cost
    //        //     $scope.strong_current_spool_quantity = response.data.data.strong_current_spool_quantity
    //        //     $scope.labor_price += $scope.strong_current_labor_price
    //        // }, function (error) {
    //        //     console.log(error)
    //        // })
    //        // $http.post("/owner/waterway", data, config).then(function (response) {//水路请求
    //        //     console.log("水路请求"+response)
    //        //     console.log(response)
    //        //     $scope.waterway_current = response.data.data.waterway_current
    //        //     $scope.waterway_labor_price = response.data.data.waterway_labor_price
    //        //     $scope.waterway_material_price = response.data.data.waterway_material_price
    //        //     $scope.waterway_ppr_cost = response.data.data.waterway_ppr_cost
    //        //     $scope.waterway_ppr_quantity = response.data.data.waterway_ppr_quantity
    //        //     $scope.waterway_pvc_cost = response.data.data.waterway_pvc_cost
    //        //     $scope.waterway_pvc_quantity = response.data.data.waterway_pvc_quantity
    //        //     $scope.labor_price += $scope.waterway_labor_price
    //        // }, function (error) {
    //        //     console.log(error)
    //        // })
    //        // $http.post("/owner/carpentry", data, config).then(function (response) {//木作请求
    //        //     console.log("木作请求"+response)
    //        //     console.log(response)
    //        //     $scope.carpentry_current = response.data.data.goods_price
    //        //     $scope.carpentry_labor_price = response.data.data.carpentry_labor_price
    //        //     $scope.carpentry_material_price = response.data.data.carpentry_material_price
    //        //     $scope.keel_cost = response.data.data.keel_cost
    //        //     $scope.plasterboard_cost = response.data.data.plasterboard_cost
    //        //     $scope.pole_cost = response.data.data.pole_cost
    //        //     $scope.labor_price += $scope.carpentry_labor_price
    //        // }, function (error) {
    //        //     console.log(error)
    //        // })
    //        // $http.post("/owner/coating", data, config).then(function (response) {//乳胶漆请求
    //        //     console.log("乳胶漆请求"+response)
    //        //     console.log(response)
    //        //     $scope.coating_current = response.data.data.goods_price
    //        //     $scope.coating_labor_price = response.data.data.coating_labor_price
    //        //     $scope.coating_material_price = response.data.data.coating_material_price
    //        //     $scope.concave_line_cost = response.data.data.concave_line_cost
    //        //     $scope.finishing_coat_cost = response.data.data.finishing_coat_cost
    //        //     $scope.gypsum_powder_cost = response.data.data.gypsum_powder_cost
    //        //     $scope.primer_cost = response.data.data.primer_cost
    //        //     $scope.putty_cost = response.data.data.putty_cost
    //        //     $scope.labor_price += $scope.coating_labor_price
    //        // }, function (error) {
    //        //     console.log(error)
    //        // })
    //        // $http.post("/owner/mud-make", data1, config).then(function (response) {//泥作请求
    //        //     console.log("泥作请求"+response)
    //        //     console.log(response)
    //        //     $scope.mud_make_current = response.data.data.goods_price
    //        //     $scope.mud_make_labor_price = response.data.data.mud_make_labor_price
    //        //     $scope.mud_make_material_price = response.data.data.mud_make_material_price
    //        //     $scope.cement_cost = response.data.data.cement_cost
    //        //     $scope.drawing_room_cost = response.data.data.drawing_room_cost
    //        //     $scope.kitchen_and_toilet_floor_tile = response.data.data.kitchen_and_toilet_floor_tile
    //        //     $scope.river_sand_cost = response.data.data.river_sand_cost
    //        //     $scope.self_leveling_cost = response.data.data.self_leveling_cost
    //        //     $scope.wall_brick_cost = response.data.data.wall_brick_cost
    //        //     $scope.labor_price += $scope.mud_make_labor_price
    //        // }, function (error) {
    //        //     console.log(error)
    //        // })
    //        //系列风格请求
    //        $http.get('/owner/series-and-style').then(function (response) {
    //            $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
    //            $scope.series = response.data.data.show.series;//系列数据
    //            for(let i = 0;i<$scope.series.length;i++){
    //                if($scope.series[i].id == $scope.series_id){
    //                    $scope.nowSeries = $scope.series[i].series;
    //                }
    //            }
    //            $scope.style = response.data.data.show.style;//风格数据
    //            for(let i = 0;i< $scope.style.length;i++){
    //                if($scope.style[i].id == $scope.series_id){
    //                    $scope.nowStyle = $scope.style[i].style;
    //                }
    //            }
    //            $scope.style_picture = response.data.data.show.style_picture;//轮播图片数据
    //            console.log(response)
    //        }, function (response) {
    //
    //        })
    //    })
    //    //切换户型
    //    $scope.toggleHouse = function (item) {
    //        $scope.house_index = item
    //        $scope.isNoChange = false
    //    }
    //    //切换系列
    //    $scope.toggleSeries = function (item) {
    //        $scope.nowSeries = item;
    //        $scope.isNoChange = false
    //    }
    //
    //    //切换风格
    //    $scope.toggleStyle = function (item) {
    //        $scope.nowStyle = item;
    //        $scope.isNoChange = false
    //    }
    //    //切换楼梯结构
    //    $scope.toggleStairs = function (index) {
    //        $scope.nowStairs = index
    //    }
    })
    .controller("all_comment_ctrl",function (){

    })
    .controller("basics_decoration_ctrl",function ($scope,$http ,$state,$stateParams){
        $scope.name=$stateParams.name;
        $scope.address=$stateParams.address;
        $scope.pic_one=$stateParams.pic_one;
        $scope.pic_two=$stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("main_material_ctrl",function ($scope,$http ,$state,$stateParams){
        $scope.name=$stateParams.name;
        $scope.address=$stateParams.address;
        $scope.pic_one=$stateParams.pic_one;
        $scope.pic_two=$stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("house_hold_ctrl",function ($scope,$http ,$state,$stateParams){
        $scope.name=$stateParams.name;
        $scope.address=$stateParams.address;
        $scope.pic_one=$stateParams.pic_one;
        $scope.pic_two=$stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("soft_house_ctrl",function ($scope,$http ,$state,$stateParams){
        $scope.name=$stateParams.name;
        $scope.address=$stateParams.address;
        $scope.pic_one=$stateParams.pic_one;
        $scope.pic_two=$stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("other_materials_ctrl",function ($scope,$http ,$state,$stateParams){
        $scope.name=$stateParams.name;
        $scope.address=$stateParams.address;
        $scope.pic_one=$stateParams.pic_one;
        $scope.pic_two=$stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("fixed_home_ctrl",function ($scope,$http ,$state,$stateParams){
        $scope.name=$stateParams.name;
        $scope.address=$stateParams.address;
        $scope.pic_one=$stateParams.pic_one;
        $scope.pic_two=$stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("add_main_ctrl",function ($scope,$http ,$state,$stateParams){
        $scope.name=$stateParams.name;
        $scope.address=$stateParams.address;
        $scope.pic_one=$stateParams.pic_one;
        $scope.pic_two=$stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("have_search_ctrl", function ($scope, $http,$state,$stateParams) {//小区搜索控制器
       console.log($stateParams);
        $scope.data = '';
        $scope.name=$stateParams.name;
        $scope.address=$stateParams.address;
        $scope.pic_one=$stateParams.pic_one;
        $scope.pic_two=$stateParams.pic_two;

        $scope.getHave = function () {
            let arr = [];
            $http.get("have_data.json").then(function (response) {
                $scope.message = response.data.data;
                console.log($scope.message);
                 for (let [key,item] of response.data.data.entries()) {
                     if (item.name.indexOf($scope.data) != -1 && $scope.data != '') {
                         arr.push({"name": item.name, "comment_address": item.comment_address})
                     }
                 }
                 $scope.search_data = arr;
            }, function (response) {

            })
        };
        $scope.getBack = function (item) {
            if(item == "今日花园"){
                $state.go("have_data",{name:'今日花园',address:'四川省成都市郫县高新西区泰山大道',pic_one:'91135',pic_two:'95280'})
            }else if (item == "花好月圆") {
                $state.go("have_data",{name:'花好月圆',address:'四川省成都市蜀汉路东89号',pic_one:'116688',pic_two:'138280'})
            }
            else if (item == "蓝光COCO时代") {
                $state.go("have_data",{name:'蓝光COCO时代',address:'四川省成都市青羊区清百路110号',pic_one:'168135',pic_two:'185280'})
            }
        }

=======
    .controller("location_city_ctrl", function ($scope) {//城市选择控制器
        // $scope.goPrev = function () {
        //     window.history.back()
        // }
    })
    .controller("intelligent_quotation_ctrl", function ($scope, $http, $stateParams) {//有资料选择器
        let arr_total = []
        $scope.require_msg = ''
        $scope.nowSeries = '齐家'//系列
        $scope.nowStyle = '现代简约'//风格
        $scope.nowStairs = '实木结构'//有楼梯风格
        $scope.choose_stairs = true;//是否有楼梯
        $scope.house_index = 0//户型
        $scope.level = []//二级分类
        $scope.stair = []//一级分类
        $scope.total_cost = 0 //总价
        $scope.labor_price = 0 //工人费用
        $scope.hydropower_materials_price = 0
        //一级、二级接口


        let url = "/owner/search"//搜索url
        let data = {
            id: $stateParams.id//有资料请求id
        }
        let config = {//所有post请求必须的请求头
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        }
        $http.post("/owner/classify", {}, config).then(function (response) {
            $scope.level = response.data.data.pid.level
            $scope.stair = response.data.data.pid.stair
            for (let [key, value] of $scope.level.entries()) {
                value["cost"] = 0
                value["second_level"] = []
            }
            for (let [key, value] of $scope.stair.entries()) {
                value["cost"] = 0
                value["second_level"] = []
            }
        }, function () {

        })
        $http.post(url, data, config).then(function (response) {//小区房型基本信息
            console.log(response)
            $scope.data = response.data.data.effect//房型所有信息
            $scope.series_id = response.data.data.effect[0].series_id//默认房型系列id
            $scope.style_id = response.data.data.effect[0].style_id//默认房型风格id
            $scope.imgSrc = response.data.data.effect_picture//默认房型对应轮播图
            $scope.isNoChange = true//可选择项是否修改
            $scope.labor_price = 0//工人费用
            let data = {//请求发送的数据
                area: response.data.data.effect[0].area,      //面积
                bedroom: response.data.data.effect[0].bedroom, //卧室
                hall: response.data.data.effect[0].sittingRoom_diningRoom,       //餐厅
                toilet: response.data.data.effect[0].toilet,   // 卫生间
                kitchen: response.data.data.effect[0].kitchen,  //厨房
                stairway: response.data.data.effect[0].stairway, //楼梯
                series: response.data.data.effect[0].series_id,   //系列
                style: response.data.data.effect[0].style_id,  //风格
                window: response.data.data.effect[0].window,//飘窗
                province: 510000,   //省编码
                city: 510100      // 市编码
            }
            // //泥作请求发送的数据:不一样
            let data1 = {}
            for(let i in data){
                data1[i] = data[i]
            }
            data1["waterproof_total_area"] = 60
            // //一系列请求
            $http.post("/owner/weak-current", data, config).then(function (response) {//弱电请求
                console.log("弱电请求" + response)
                console.log(response)
                //整理弱电接口数据
                // $scope.weak_labor_price = response.data.data.weak_current_labor_price
                // $scope.weak_total_cost = response.data.data.weak_current_material.total_cost
                let arr = response.data.data.weak_current_material
                let weak_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        weak_arr.push(arr[item])
                    }
                }
                console.log(weak_arr)
                for (let [key, value] of weak_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] +=  value.cost
                        }
                    }
                }
                for (let [key, value] of weak_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
                                value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                                if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                                    value1["second_level"].push(value2.id)
                            }
                        }
                    }
                }
            }, function (error) {
                console.log(error)
            })
            $http.post("/owner/strong-current", data, config).then(function (response) {//强电请求
                console.log("强电请求"+response)
                console.log(response)
                let arr = response.data.data.strong_current_material
                let strong_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        strong_arr.push(arr[item])
                    }
                }
                console.log(strong_arr)
                for (let [key, value] of strong_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] +=  value.cost
                        }
                    }
                }
                for (let [key, value] of strong_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                                if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                                value1["second_level"].push(value2.id)
                            }
                        }
                    }
                }
            }, function (error) {
                console.log(error)
            })
            $http.post("/owner/waterway", data, config).then(function (response) {//水路请求
                console.log("水路请求"+response)
                console.log(response)
                let arr = response.data.data.waterway_material_price
                let waterway_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        waterway_arr.push(arr[item])
                    }
                }
                console.log(waterway_arr)
                for (let [key, value] of waterway_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] +=  value.cost
                        }
                    }
                }
                for (let [key, value] of waterway_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
                                value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                                if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                                    value1["second_level"].push(value2.id)
                            }
                        }
                    }
                }
            }, function (error) {
                console.log(error)
            })
            $http.post("/owner/carpentry", data, config).then(function (response) {//木作请求
                console.log("木作请求"+response)
                console.log(response)
                let arr = response.data.data.carpentry_material
                let carpentry_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        carpentry_arr.push(arr[item])
                    }
                }
                console.log(carpentry_arr)
                for (let [key, value] of carpentry_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] +=  value.cost
                        }
                    }
                }
                for (let [key, value] of carpentry_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
                                value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                                if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                                    value1["second_level"].push(value2.id)
                            }
                        }
                    }
                }
                console.log($scope.level)
                console.log($scope.stair)
            }, function (error) {
                console.log(error)
            })
            $http.post("/owner/coating", data, config).then(function (response) {//乳胶漆请求
                console.log("乳胶漆请求"+response)
                console.log(response)
                // let arr = response.data.data.carpentry_material
                // let carpentry_arr = []
                // for (let item in arr) {
                //     if (item != "total_cost") {
                //         carpentry_arr.push(arr[item])
                //     }
                // }
                // console.log(carpentry_arr)
                // for (let [key, value] of carpentry_arr.entries()) {
                //     for (let [key1, value1] of $scope.level.entries()) {
                //         if (value.path.split(',')[1] == value1.id) {
                //             value1["cost"] +=  value.cost
                //         }
                //     }
                // }
                // for (let [key, value] of carpentry_arr.entries()) {
                //     for (let [key1, value1] of $scope.stair.entries()) {
                //         for (let [key2, value2] of $scope.level.entries()) {
                //             if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id ) {
                //                 value1[value2.id] = {"title":value2.title,"cost":value2.cost}
                //                 if(!value1["second_level"] || value1["second_level"].indexOf(value2.id) == -1)
                //                     value1["second_level"].push(value2.id)
                //             }
                //         }
                //     }
                // }
                console.log($scope.stair)
            }, function (error) {
                console.log(error)
            })
            $http.post("/owner/mud-make", data1, config).then(function (response) {//泥作请求
                console.log("泥作请求"+response)
                console.log(response)
            }, function (error) {
                console.log(error)
            })
            //系列风格请求
            $http.get('/owner/series-and-style').then(function (response) {
                $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
                $scope.series = response.data.data.show.series;//系列数据
                for (let i = 0; i < $scope.series.length; i++) {
                    if ($scope.series[i].id == $scope.series_id) {
                        $scope.nowSeries = $scope.series[i].series;
                    }
                }
                $scope.style = response.data.data.show.style;//风格数据
                for (let i = 0; i < $scope.style.length; i++) {
                    if ($scope.style[i].id == $scope.series_id) {
                        $scope.nowStyle = $scope.style[i].style;
                    }
                }
                $scope.style_picture = response.data.data.show.style_picture;//轮播图片数据
                console.log(response)
            }, function (response) {

            })

        })
        $scope.$watch('require_msg', function (newVal, oldVal) {
            if (newVal && newVal != oldVal) {
                if (newVal.length > 300) {
                    $scope.require_msg = newVal.substr(0, 300)
                }
            }
        })
        //切换户型
        $scope.toggleHouse = function (item) {
            $scope.house_index = item
            $scope.isNoChange = false
        }
        //切换系列
        $scope.toggleSeries = function (item) {
            $scope.nowSeries = item;
            $scope.isNoChange = false
        }

        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.nowStyle = item;
            $scope.isNoChange = false
        }
        //切换楼梯结构
        $scope.toggleStairs = function (index) {
            $scope.nowStairs = index
        }
        //有资料修改生成方法
        $scope.reviseData = function () {
            let data = {

            }
        }
>>>>>>> 3744ed0535f2dab0266c0771190b54bf355d2ac8
    })
