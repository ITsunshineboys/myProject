app.controller('nodata_ctrl', function ($timeout,$uibModal,$http, _ajax, $state, $scope, $anchorScroll, $location, $q) {
    //初始化
    $scope.vm = $scope
    $scope.special_request = ''
    $scope.materials = []
    $scope.worker_list = []
    let mySwiper
    $scope.showAll = function () {
        if(mySwiper!==undefined){
            mySwiper.destroy(true,true)
        }
        $timeout(function () {
            mySwiper = new Swiper(".swiper-container", {
                autoplay: 3000,
                loop: true,
                pagination: ".swiper-pagination",
                observer:true,//修改swiper自己或子元素时，自动初始化swiper
                observeParents:true,//修改swiper的父元素时，自动初始化swiper
                onSlideChangeEnd: function(swiper){
                    swiper.update(true);
                    // mySwiper.startAutoplay();
                    // mySwiper.reLoop();
                }
            })
            mySwiper.startAutoplay()
            mySwiper.reLoop()
        },300)
    }
    //监听滚动
    // window.addEventListener('scroll',function (event) {
    //     console.log(document.body.scrollTop);
    // })
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
        name: '',//小区名称
        address: ''//小区地址
    }
    //获取表单数据
    if (sessionStorage.getItem('toponymy') != null) {
        $scope.toponymy = JSON.parse(sessionStorage.getItem('toponymy'))
    }
    if (sessionStorage.getItem('worker_list') != null) {
        $scope.worker_list = JSON.parse(sessionStorage.getItem('worker_list'))
    }
    //获取材料信息
    if (sessionStorage.getItem('materials') != null) {
        $scope.materials = JSON.parse(sessionStorage.getItem('materials'))
        getPrice()
    }
    /*存基本信息*/
    $scope.$watch('params', function (newVal, oldVal) {
        console.log(JSON.stringify(newVal) === JSON.stringify(oldVal));
        if(!(JSON.stringify(newVal) === JSON.stringify(oldVal))){
            sessionStorage.setItem('params', JSON.stringify($scope.params))
            $scope.materials = []
            $scope.words = '生成材料'
        }
    }, true)
    $scope.$watch('toponymy', function (newVal, oldVal) {
        if(!(JSON.stringify(newVal) === JSON.stringify(oldVal))){
            sessionStorage.setItem('toponymy', JSON.stringify(newVal))
            $scope.materials = []
            $scope.words = '生成材料'
        }
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
        console.log('分类');
        console.log(res)
        $scope.first_level = res.data.pid.stair//一级
        $scope.second_level = res.data.pid.level//二级
    })
    //生成材料
    $scope.getMaterials = function (valid, error) {
        let arr = angular.copy($scope.params)
        let arr1 = angular.copy($scope.params)
        if(valid){
            //分类商品初始化
            $scope.materials = angular.copy($scope.first_level)
            for (let [key, value] of $scope.materials.entries()) {
                value.id = +value.id
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
                // (function () {
                //     return _ajax.get('/owner/plumber-price', $scope.params, function (res) {
                //         console.log('水电工费用');
                //         console.log(res);
                //         let index = $scope.worker_list.findIndex(function (item) {
                //             return item.worker_kind == res.labor_all_cost.worker_kind
                //         })
                //         if (index == -1) {
                //             $scope.worker_list.push(res.labor_all_cost)
                //         } else {
                //             $scope.worker_list[index].price += res.labor_all_cost.price
                //         }
                //     })
                // })(),
                //强弱电
                // (function () {
                //     return _ajax.get('/owner/electricity', $scope.params, function (res) {
                //         console.log('强弱电');
                //         console.log(res);
                //         //整合二级
                //         for (let [key, value] of res.data.entries()) {
                //             for (let [key1, value1] of $scope.materials.entries()) {
                //                 if (value1.id == value.path.split(',')[0]) {
                //                     let index = value1.second_level.findIndex(function (item) {
                //                         return item.id == value.path.split(',')[1]
                //                     })
                //                     let index1 = $scope.second_level.findIndex(function (item) {
                //                         return item.id == value.path.split(',')[1]
                //                     })
                //                     if (index == -1) {
                //                         value1.second_level.push({
                //                             id: +$scope.second_level[index1].id,
                //                             title: $scope.second_level[index1].title,
                //                             cost: 0,
                //                             procurement: 0,
                //                             goods: []
                //                         })
                //                     }
                //                 }
                //             }
                //         }
                //         //整合商品
                //         for (let [key, value] of res.data.entries()) {
                //             for (let [key1, value1] of $scope.materials.entries()) {
                //                 for (let [key2, value2] of value1.second_level.entries()) {
                //                     if (value2.id == value.path.split(',')[1]) {
                //                         let index = value2.goods.findIndex(function (item) {
                //                             return item.id == value.id
                //                         })
                //                         value1.cost += value.cost
                //                         value1.procurement += value.procurement
                //                         value2.cost += value.cost
                //                         value2.procurement += value.procurement
                //                         if (index == -1) {
                //                             value2.goods.push(value)
                //                             value1.count++
                //                         } else {
                //                             value2.goods[index].quantity += value.quantity
                //                             value2.goods[index].cost += value.cost
                //                             value2.goods[index].procurement += value.procurement
                //                         }
                //                     }
                //                 }
                //             }
                //         }
                //     })
                // })(),
                //水路
                // (function () {
                //     return _ajax.get('/owner/waterway', $scope.params, function (res) {
                //         console.log('水路');
                //         console.log(res);
                //         //整合二级
                //         for (let [key, value] of res.data.entries()) {
                //             for (let [key1, value1] of $scope.materials.entries()) {
                //                 if (value1.id == value.path.split(',')[0]) {
                //                     let index = value1.second_level.findIndex(function (item) {
                //                         return item.id == value.path.split(',')[1]
                //                     })
                //                     let index1 = $scope.second_level.findIndex(function (item) {
                //                         return item.id == value.path.split(',')[1]
                //                     })
                //                     if (index == -1) {
                //                         value1.second_level.push({
                //                             id: +$scope.second_level[index1].id,
                //                             title: $scope.second_level[index1].title,
                //                             cost: 0,
                //                             procurement: 0,
                //                             goods: []
                //                         })
                //                     }
                //                 }
                //             }
                //         }
                //         //整合商品
                //         for (let [key, value] of res.data.entries()) {
                //             for (let [key1, value1] of $scope.materials.entries()) {
                //                 for (let [key2, value2] of value1.second_level.entries()) {
                //                     if (value2.id == value.path.split(',')[1]) {
                //                         let index = value2.goods.findIndex(function (item) {
                //                             return item.id == value.id
                //                         })
                //                         value1.cost += value.cost
                //                         value1.procurement += value.procurement
                //                         value2.cost += value.cost
                //                         value2.procurement += value.procurement
                //                         if (index == -1) {
                //                             value2.goods.push(value)
                //                             value1.count++
                //                         } else {
                //                             value2.goods[index].quantity += value.quantity
                //                             value2.goods[index].cost += value.cost
                //                             value2.goods[index].procurement += value.procurement
                //                         }
                //                     }
                //                 }
                //             }
                //         }
                //     })
                // })(),
                //防水
                (function () {
                    return _ajax.get('/owner/waterproof', $scope.params, function (res) {
                        console.log('防水');
                        console.log(res);
                        let index = $scope.worker_list.findIndex(function (item) {
                            return item.worker_kind == res.labor_all_cost.worker_kind
                        })
                        if (index == -1) {
                            $scope.worker_list.push(res.labor_all_cost)
                        } else {
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
                                            value1.count++
                                        } else {
                                            value2.goods[index].quantity += value.quantity
                                            value2.goods[index].cost += value.cost
                                            value2.goods[index].procurement += value.procurement
                                        }
                                    }
                                }
                            }
                        }
                        Object.assign(arr, {waterproof_total_area: res.total_area})
                    })
                })(),
                //木作
                // (function () {
                //     return _ajax.get('/owner/carpentry', $scope.params, function (res) {
                //         console.log('木作');
                //         console.log(res);
                //         let index = $scope.worker_list.findIndex(function (item) {
                //             return item.worker_kind == res.labor_all_cost.worker_kind
                //         })
                //         if (index == -1) {
                //             $scope.worker_list.push(res.labor_all_cost)
                //         } else {
                //             $scope.worker_list[index].price += res.labor_all_cost.price
                //         }
                //         //整合二级
                //         for (let [key, value] of res.data.entries()) {
                //             for (let [key1, value1] of $scope.materials.entries()) {
                //                 if (value1.id == value.path.split(',')[0]) {
                //                     let index = value1.second_level.findIndex(function (item) {
                //                         return item.id == value.path.split(',')[1]
                //                     })
                //                     let index1 = $scope.second_level.findIndex(function (item) {
                //                         return item.id == value.path.split(',')[1]
                //                     })
                //                     if (index == -1) {
                //                         value1.second_level.push({
                //                             id: +$scope.second_level[index1].id,
                //                             title: $scope.second_level[index1].title,
                //                             cost: 0,
                //                             procurement: 0,
                //                             goods: []
                //                         })
                //                     }
                //                 }
                //             }
                //         }
                //         //整合商品
                //         for (let [key, value] of res.data.entries()) {
                //             for (let [key1, value1] of $scope.materials.entries()) {
                //                 for (let [key2, value2] of value1.second_level.entries()) {
                //                     if (value2.id == value.path.split(',')[1]) {
                //                         let index = value2.goods.findIndex(function (item) {
                //                             return item.id == value.id
                //                         })
                //                         value1.cost += value.cost
                //                         value1.procurement += value.procurement
                //                         value2.cost += value.cost
                //                         value2.procurement += value.procurement
                //                         if (index == -1) {
                //                             value2.goods.push(value)
                //                             value1.count++
                //                         } else {
                //                             value2.goods[index].quantity += value.quantity
                //                             value2.goods[index].cost += value.cost
                //                             value2.goods[index].procurement += value.procurement
                //                         }
                //                     }
                //                 }
                //             }
                //         }
                //     })
                // })(),
                //乳胶漆
                (function () {
                    return _ajax.get('/owner/coating', $scope.params, function (res) {
                        console.log('乳胶漆');
                        console.log(res);
                        let index = $scope.worker_list.findIndex(function (item) {
                            return item.worker_kind == res.labor_all_cost.worker_kind
                        })
                        if (index == -1) {
                            $scope.worker_list.push(res.labor_all_cost)
                        } else {
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
                                            value1.count++
                                        } else {
                                            value2.goods[index].quantity += value.quantity
                                            value2.goods[index].cost += value.cost
                                            value2.goods[index].procurement += value.procurement
                                        }
                                    }
                                }
                            }
                        }
                        arr1.bedroom_area = res.bedroom_area
                    })
                })(),
                //材料添加项
                // (function () {
                //     return _ajax.get('/owner/add-materials', {
                //         city: $scope.params.city,
                //         series: $scope.params.series,
                //         style: $scope.params.style,
                //         area: $scope.params.area
                //     }, function (res) {
                //         console.log('材料添加项');
                //         console.log(res);
                //         //整合二级
                //         for (let [key, value] of res.add_list.entries()) {
                //             for (let [key1, value1] of $scope.materials.entries()) {
                //                 if (value1.id == value.path.split(',')[0]) {
                //                     let index = value1.second_level.findIndex(function (item) {
                //                         return item.id == value.path.split(',')[1]
                //                     })
                //                     let index1 = $scope.second_level.findIndex(function (item) {
                //                         return item.id == value.path.split(',')[1]
                //                     })
                //                     if (index == -1) {
                //                         value1.second_level.push({
                //                             id: +$scope.second_level[index1].id,
                //                             title: $scope.second_level[index1].title,
                //                             cost: 0,
                //                             procurement: 0,
                //                             goods: []
                //                         })
                //                     }
                //                 }
                //             }
                //         }
                //         //整合商品
                //         for (let [key, value] of res.add_list.entries()) {
                //             for (let [key1, value1] of $scope.materials.entries()) {
                //                 for (let [key2, value2] of value1.second_level.entries()) {
                //                     if (value2.id == value.path.split(',')[1]) {
                //                         let index = value2.goods.findIndex(function (item) {
                //                             return item.id == value.id
                //                         })
                //                         value1.cost += value.cost
                //                         value1.procurement += value.procurement
                //                         value2.cost += value.cost
                //                         value2.procurement += value.procurement
                //                         if (index == -1) {
                //                             value2.goods.push(value)
                //                             value1.count++
                //                         } else {
                //                             value2.goods[index].quantity += value.quantity
                //                             value2.goods[index].cost += value.cost
                //                             value2.goods[index].procurement += value.procurement
                //                         }
                //                     }
                //                 }
                //             }
                //         }
                //     })
                // })()
            ]).then(function () {
                console.log($scope.materials);
                console.log($scope.worker_list);
                $q.all([
                    //泥作(需要防水面积(防水))
                    // (function () {
                    //     return _ajax.get('/owner/mud-make', arr, function (res) {
                    //         console.log('泥作');
                    //         console.log(res);
                    //         let index = $scope.worker_list.findIndex(function (item) {
                    //             return item.worker_kind == res.labor_all_cost.worker_kind
                    //         })
                    //         if (index == -1) {
                    //             $scope.worker_list.push(res.labor_all_cost)
                    //         } else {
                    //             $scope.worker_list[index].price += res.labor_all_cost.price
                    //         }
                    //         //整合二级
                    //         for (let [key, value] of res.data.entries()) {
                    //             for (let [key1, value1] of $scope.materials.entries()) {
                    //                 if (value1.id == value.path.split(',')[0]) {
                    //                     let index = value1.second_level.findIndex(function (item) {
                    //                         return item.id == value.path.split(',')[1]
                    //                     })
                    //                     let index1 = $scope.second_level.findIndex(function (item) {
                    //                         return item.id == value.path.split(',')[1]
                    //                     })
                    //                     if (index == -1) {
                    //                         value1.second_level.push({
                    //                             id: +$scope.second_level[index1].id,
                    //                             title: $scope.second_level[index1].title,
                    //                             cost: 0,
                    //                             procurement: 0,
                    //                             goods: []
                    //                         })
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //         //整合商品
                    //         for (let [key, value] of res.data.entries()) {
                    //             for (let [key1, value1] of $scope.materials.entries()) {
                    //                 for (let [key2, value2] of value1.second_level.entries()) {
                    //                     if (value2.id == value.path.split(',')[1]) {
                    //                         let index = value2.goods.findIndex(function (item) {
                    //                             return item.id == value.id
                    //                         })
                    //                         value1.cost += value.cost
                    //                         value1.procurement += value.procurement
                    //                         value2.cost += value.cost
                    //                         value2.procurement += value.procurement
                    //                         if (index == -1) {
                    //                             value2.goods.push(value)
                    //                             value1.count++
                    //                         } else {
                    //                             value2.goods[index].quantity += value.quantity
                    //                             value2.goods[index].cost += value.cost
                    //                             value2.goods[index].procurement += value.procurement
                    //                         }
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //     })
                    // })(),
                    //配套商品(需要卧室面积(乳胶漆))
                    (function () {
                        return _ajax.get('/owner/assort-facility', arr1, function (res) {
                            console.log('配套商品');
                            console.log(res);
                            //整合二级
                            for (let [key2, value2] of res.data.goods.entries()) {
                                for (let [key, value] of value2.entries()) {
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
                            }
                            //整合商品
                            for (let [key3, value3] of res.data.goods.entries()) {
                                for (let [key, value] of value3.entries()) {
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
                                                    value1.count++
                                                } else {
                                                    value2.goods[index].quantity += value.quantity
                                                    value2.goods[index].cost += value.cost
                                                    value2.goods[index].procurement += value.procurement
                                                }
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
                    getPrice()
                })
            })
        }else{
        $scope.submitted = true
        for (let [key, value] of error.entries()) {
            if (value.$invalid) {
                $anchorScroll.yOffset = 300
                $location.hash(value.$name)
                $anchorScroll()
                break
            }
        }
        }
    }
    //计算总价和折后价
    function getPrice() {
        let arr = [], arr1 = []
        $scope.total_prices = 0//原价
        $scope.special_offer = 0//折后价
        for (let [key, value] of $scope.materials.entries()) {
            arr.push({
                category_id: value.id,
                price: value.cost,
                procurement: value.procurement
            })
            for (let [key1, value1] of value.second_level.entries()) {
                for (let [key2, value2] of value1.goods.entries()) {
                    arr1.push({
                        goods_id: value2.id,
                        num: value2.quantity
                    })
                }
            }
        }
        $q.all([
            //运费
            (function () {
                return _ajax.post('/order/calculation-freight', {
                    goods: arr1
                }, function (res) {
                    console.log('运费');
                    console.log(res);
                    $scope.total_prices += +res.data
                    $scope.special_offer += +res.data
                })
            })(),
            //总价
            (function () {
                return _ajax.post('/owner/coefficient', {
                    list: arr
                }, function (res) {
                    console.log('总价');
                    console.log(res);
                    $scope.total_prices += +res.data.total_prices
                    $scope.special_offer += +res.data.special_offer
                })
            })()
        ]).then(function () {
            for(let [key,value] of $scope.materials.entries()){
                for(let [key1,value1] of value.second_level.entries()){
                    let index = value1.goods.findIndex(function(item){
                        return item.status == 0
                    })
                    value1.status = index == -1?2:0
                }
            }
            let worker_price = $scope.worker_list.reduce(function (prev, cur) {
                return prev + cur.price
            }, 0)
            $scope.total_prices += worker_price
            $scope.special_offer += worker_price
            $scope.words = ''
        })
    }
    //跳转内页页面
    $scope.goInner = function (item, index) {
        sessionStorage.setItem('materials', JSON.stringify($scope.materials))
        sessionStorage.setItem('copies', JSON.stringify($scope.materials))
        sessionStorage.setItem('worker_list', JSON.stringify($scope.worker_list))
        if (item.id == 1) {
            $state.go('basic_decoration', {index: index})
        } else if (item.id == 14) {
            $state.go('main_materials', {index: index})
        } else {
            $state.go('other_materials', {index: index})
        }
    }
    //跳转申请样板间
    // $scope.applyCase = function () {
    //     let materials = []//申请材料项
    //     let status = false//材料是否存在下架
    //     //整合申请样板间所需传值
    //     let obj = {
    //         province_code:$scope.params.province,
    //         city_code:$scope.params.city,
    //         street:$scope.toponymy.address,
    //         toponymy:$scope.toponymy.name,
    //         sittingRoom_diningRoom:$scope.params.hall,
    //         window:$scope.params.window,
    //         bedroom:$scope.params.bedroom,
    //         area:$scope.params.area,
    //         high:$scope.params.high,
    //         toilet:$scope.params.toilet,
    //         kitchen:$scope.params.kitchen,
    //         stair_id:$scope.params.stairway_id,
    //         stairway:$scope.params.stairway,
    //         series:$scope.params.series,
    //         style:$scope.params.style,
    //         type:0,
    //         requirement:$scope.special_request,
    //         original_price:$scope.total_prices,
    //         sale_price:$scope.special_offer
    //     }
    //     for(let [key,value] of $scope.materials.entries()){
    //         for(let [key1,value1] of value.second_level.entries()){
    //             for(let [key2,value2] of value1.goods.entries()){
    //                 materials.push({
    //                     goods_id:value2.id,
    //                     price:value2.cost,
    //                     count:value2.quantity,
    //                     first_cate_id:value.id
    //                 })
    //             }
    //         }
    //     }
    //     obj.materials = materials
    //     //遍历是否存在下架商品
    //     for(let [key,value] of $scope.materials.entries()){
    //         let index = value.second_level.findIndex(function (item) {
    //             return item.status == 0
    //         })
    //         if(index == -1){
    //             status = false
    //         }else{
    //             status = true
    //             break
    //         }
    //     }
    //     //模态框配置
    //     let all_modal = function ($scope, $uibModalInstance) {
    //         $scope.btn_word = '确认'
    //         $scope.big_word = '手机号输入不正确'
    //         $scope.common_house = function () {
    //             $uibModalInstance.close()
    //         }
    //     }
    //     all_modal.$inject = ['$scope', '$uibModalInstance']
    //     if(status){
    //         $uibModal.open({
    //             templateUrl: 'cur_model.html',
    //             controller: all_modal,
    //             windowClass:'cur_modal',
    //             backdrop:'static'
    //         })
    //     }else{
    //         sessionStorage.setItem('payParams',JSON.stringify(obj))
    //         $state.go('deposit')
    //     }
    // }

    //保存方案
    $scope.saveProgramme = function () {
        let materials = []
        //整合申请样板间所需传值
        let obj = {
            province_code:$scope.params.province,
            city_code:$scope.params.city,
            street:$scope.toponymy.address,
            toponymy:$scope.toponymy.name,
            sittingRoom_diningRoom:$scope.params.hall,
            window:$scope.params.window,
            bedroom:$scope.params.bedroom,
            area:$scope.params.area,
            high:$scope.params.high,
            toilet:$scope.params.toilet,
            kitchen:$scope.params.kitchen,
            stair_id:$scope.params.stairs,
            stairway:$scope.params.stairway_id,
            series:$scope.params.series,
            style:$scope.params.style,
            type:1,
            requirement:$scope.special_request,
            original_price:$scope.total_prices.toFixed(2),
            sale_price:$scope.special_offer.toFixed(2)
        }
        for(let [key,value] of $scope.materials.entries()){
            for(let [key1,value1] of value.second_level.entries()){
                for(let [key2,value2] of value1.goods.entries()){
                    materials.push({
                        goods_id:value2.id,
                        price:value2.cost,
                        count:value2.quantity,
                        first_cate_id:value.id
                    })
                }
            }
        }
        obj.materials = materials
        /*保存成功模态框*/
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.tips = '方案保存成功'
            $scope.save_status = true
            $scope.return = function () {
                $uibModalInstance.close()
            }
            $scope.viewDetails = function () {
                $uibModalInstance.close()
                window.AndroidWebView.skipZhuangXiu()
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        _ajax.post('/effect/app-apply-effect',obj,function (res) {
            console.log(res);
            if(res.code == 403||res.code == 1052){
                window.AndroidWebView.skipNotLogin()
            }else{
                $uibModal.open({
                    templateUrl: 'cur_model.html',
                    controller: all_modal,
                    windowClass:'cur_modal',
                    backdrop:'static'
                })
            }
        })
    }
    //去装修
    $scope.applyCase = function () {
        let materials = []//申请材料项
        let status = false//材料是否存在下架
        //整合申请样板间所需传值
        let obj = {
            province_code:$scope.params.province,
            city_code:$scope.params.city,
            street:$scope.toponymy.address,
            toponymy:$scope.toponymy.name,
            sittingRoom_diningRoom:$scope.params.hall,
            window:$scope.params.window,
            bedroom:$scope.params.bedroom,
            area:$scope.params.area,
            high:$scope.params.high,
            toilet:$scope.params.toilet,
            kitchen:$scope.params.kitchen,
            stair_id:$scope.params.stairs,
            stairway:$scope.params.stairway_id,
            series:$scope.params.series,
            style:$scope.params.style,
            type:0,
            requirement:$scope.special_request,
            original_price:$scope.total_prices.toFixed(2),
            sale_price:$scope.special_offer.toFixed(2)
        }
        for(let [key,value] of $scope.materials.entries()){
            for(let [key1,value1] of value.second_level.entries()){
                for(let [key2,value2] of value1.goods.entries()){
                    materials.push({
                        goods_id:value2.id,
                        price:value2.cost,
                        count:value2.quantity,
                        first_cate_id:value.id
                    })
                }
            }
        }
        //遍历是否存在下架商品
        for(let [key,value] of $scope.materials.entries()){
            let index = value.second_level.findIndex(function (item) {
                return item.status == 0
            })
            if(index == -1){
                status = false
            }else{
                status = true
                break
            }
        }
        //模态框配置
        /*下架商品模态框*/
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        /*保存成功模态框*/
        let all_modal1 = function ($scope, $uibModalInstance) {
            $scope.tips = '申请成功，我们会在3天内和你联系'
            $scope.save_status = true
            $scope.return = function () {
                $uibModalInstance.close()
            }
            $scope.viewDetails = function () {
                $uibModalInstance.close()
                window.AndroidWebView.skipZhuangXiu()
            }
        }
        all_modal1.$inject = ['$scope', '$uibModalInstance']
        //判断是否登录
        if(status){
            $uibModal.open({
                templateUrl: 'cur_model.html',
                controller: all_modal,
                windowClass:'cur_modal',
                backdrop:'static'
            })
        }else{
            _ajax.get('/site/check-is-login',{},function (res) {
                if(res.code == 403||res.code == 1052){
                    obj.materials = materials
                    sessionStorage.setItem('payParams',JSON.stringify(obj))
                    $state.go('deposit')
                }else if(res.code == 200){
                    obj.materials = materials
                    _ajax.post('/effect/app-apply-effect',obj,function (res) {
                        if(res.code == 200){
                            $uibModal.open({
                                templateUrl: 'cur_model.html',
                                controller: all_modal1,
                                windowClass:'cur_modal',
                                backdrop:'static'
                            })
                        }
                    })
                }
            })
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        $state.go('home')
    }
})