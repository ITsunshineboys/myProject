angular.module("all_controller", [])
    .controller("cell_search_ctrl", function ($scope, $http) {//小区搜索控制器
        $scope.data = ''
        $scope.search_data = ''
        let arr = []
        let url = "/owner/search"

        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        }
        $scope.getData = function () {
            let data = {
                str: $scope.data
            }
            console.log(data)
            let arr = [], arr1 = []
            if (data.str) {
                $http.post(url, data, config).then(function (response) {
                    console.log(response)
                    for (let [key, value] of response.data.data.effect.entries()) {
                        if (arr.length == 0) {
                            arr.push({toponymy: value.toponymy, id: value.id, site_particulars: value.site_particulars})
                        } else {
                            for (let [key1, value1] of arr.entries()) {
                                if (value.toponymy !== value1.toponymy && key1 === arr.length - 1) {
                                    arr.push({
                                        toponymy: value.toponymy,
                                        id: value.id,
                                        site_particulars: value.site_particulars
                                    })
                                }
                            }
                        }
                    }
                    console.log(arr)
                    $scope.search_data = arr
                }, function (response) {

                })
            } else {
                $scope.search_data = ''
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

    .controller("move_furniture_ctrl", function ($scope, $http) {//移动家具控制器
        $http({
            method: 'get',
            url: "/mall/categories"
        }).then(function successCallback(response) {
            $scope.message = response.data.data.categories;
        }, function errorCallback(response) {

        });
    })
    .controller("intelligent_quotation_ctrl", function ($scope, $http, $stateParams) {//有资料选择器
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
    .controller("all_comment_ctrl", function () {

    })
    .controller("basics_decoration_ctrl", function ($scope, $http, $state, $stateParams) {
        $scope.name = $stateParams.name;
        $scope.address = $stateParams.address;
        $scope.pic_one = $stateParams.pic_one;
        $scope.pic_two = $stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("main_material_ctrl", function ($scope, $http, $state, $stateParams) {
        $scope.name = $stateParams.name;
        $scope.address = $stateParams.address;
        $scope.pic_one = $stateParams.pic_one;
        $scope.pic_two = $stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("house_hold_ctrl", function ($scope, $http, $state, $stateParams) {
        $scope.name = $stateParams.name;
        $scope.address = $stateParams.address;
        $scope.pic_one = $stateParams.pic_one;
        $scope.pic_two = $stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("soft_house_ctrl", function ($scope, $http, $state, $stateParams) {
        $scope.name = $stateParams.name;
        $scope.address = $stateParams.address;
        $scope.pic_one = $stateParams.pic_one;
        $scope.pic_two = $stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("other_materials_ctrl", function ($scope, $http, $state, $stateParams) {
        $scope.name = $stateParams.name;
        $scope.address = $stateParams.address;
        $scope.pic_one = $stateParams.pic_one;
        $scope.pic_two = $stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("fixed_home_ctrl", function ($scope, $http, $state, $stateParams) {
        $scope.name = $stateParams.name;
        $scope.address = $stateParams.address;
        $scope.pic_one = $stateParams.pic_one;
        $scope.pic_two = $stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("add_main_ctrl", function ($scope, $http, $state, $stateParams) {
        $scope.name = $stateParams.name;
        $scope.address = $stateParams.address;
        $scope.pic_one = $stateParams.pic_one;
        $scope.pic_two = $stateParams.pic_two;
        console.log($stateParams);
    })
    .controller("have_search_ctrl", function ($scope, $http, $state, $stateParams) {//小区搜索控制器
        console.log($stateParams);
        $scope.data = '';
        $scope.name = $stateParams.name;
        $scope.address = $stateParams.address;
        $scope.pic_one = $stateParams.pic_one;
        $scope.pic_two = $stateParams.pic_two;

        $scope.getHave = function () {
            let arr = [];
            $http.get("have_data.json").then(function (response) {
                $scope.message = response.data.data;
                console.log($scope.message);
                for (let [key, item] of response.data.data.entries()) {
                    if (item.name.indexOf($scope.data) != -1 && $scope.data != '') {
                        arr.push({"name": item.name, "comment_address": item.comment_address})
                    }
                }
                $scope.search_data = arr;
            }, function (response) {

            })
        };
        $scope.getBack = function (item) {
            if (item == "今日花园") {
                $state.go("have_data", {name: '今日花园', address: '四川省成都市郫县高新西区泰山大道', pic_one: '91135', pic_two: '95280'})
            } else if (item == "花好月圆") {
                $state.go("have_data", {name: '花好月圆', address: '四川省成都市蜀汉路东89号', pic_one: '116688', pic_two: '138280'})
            }
            else if (item == "蓝光COCO时代") {
                $state.go("have_data", {
                    name: '蓝光COCO时代',
                    address: '四川省成都市青羊区清百路110号',
                    pic_one: '168135',
                    pic_two: '185280'
                })
            }
        }

    })
    .controller("intelligent_nodata_ctrl", function ($scope, $stateParams, $http, $state) { //无数据控制器
        let all_url = 'http://test.cdlhzz.cn:888'
        console.log($stateParams)
        $scope.message = ''
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.nowStyle = '现代简约'
        $scope.nowStairs = $stateParams.cur_stair || '实木构造'
        $scope.nowSeries = '齐家'
        $scope.area = $stateParams.area || ''
        $scope.series_index = $stateParams.series_index || 0//系列编号
        $scope.style_index = $stateParams.style_index || 0//风格编号
        $scope.window = $stateParams.window || ''
        $scope.labor_price = $stateParams.labor_price || 0//工人总费用
        $scope.labor_category = $stateParams.worker_category || {}//工人详细费用
        $scope.toponymy = $stateParams.toponymy || ''
        $scope.choose_stairs = $stateParams.choose_stairs || false;//楼梯选择
        console.log($scope.choose_stairs)
        $scope.stair = $stateParams.stair//默认一级传递值
        $scope.level = $stateParams.level//默认二级传递值
        $scope.isClick = $stateParams.isBack || false
        $scope.handyman_price = $stateParams.worker_category['杂工'] || 0
        //生成材料变量
        $scope.house_bedroom = $stateParams.house_bedroom || 1
        $scope.house_hall = $stateParams.house_hall || 1
        $scope.house_kitchen = $stateParams.house_kitchen || 1
        $scope.house_toilet = $stateParams.house_toilet || 1
        $scope.highCrtl = $stateParams.highCrtl || 2.8

        //杂工数据
        $scope.twelve_dismantle = $stateParams.twelve_dismantle || ''
        $scope.twenty_four_dismantle = $stateParams.twenty_four_dismantle || ''
        $scope.repair = $stateParams.repair || ''
        $scope.twelve_new_construction = $stateParams.twelve_new_construction || ''
        $scope.twenty_four_new_construction = $stateParams.twenty_four_new_construction || ''
        $scope.building_scrap = $stateParams.building_scrap || false

        //无资料户型加减方法
        $scope.add = function (item, category) {
            if ($scope[category] < item) {
                $scope[category]++
            } else {
                $scope[category] = item
            }
        }
        $scope.subtract = function (item, category) {
            if ($scope[category] > item) {
                $scope[category]--
            } else {
                $scope[category] = item
            }
        }
        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        }
        //生成材料方法
        $scope.getData = function () {
            $scope.platform_price = 0 //平台价格
            $scope.supply_price = 0//装修公司供货价
            $http.post(all_url + "/owner/classify", {}, config).then(function (response) {
                $scope.level = response.data.data.pid.level
                $scope.stair = response.data.data.pid.stair
                for (let [key, value] of $scope.level.entries()) {
                    value["cost"] = 0
                    value["three_level"] = []
                }
                for (let [key, value] of $scope.stair.entries()) {
                    value["cost"] = 0
                    value["second_level"] = []
                    value["labor_total_price"] = 0
                    value["goods_count"] = 0
                }
            }, function () {

            })
            let url = all_url + "/owner/weak-current"
            let strong = all_url + "/owner/strong-current"
            let waterway = all_url + "/owner/waterway"
            let waterproof = all_url + "/owner/waterproof"
            let carpentry = all_url + "/owner/carpentry"
            let coating = all_url + "/owner/coating"
            let make = all_url + "/owner/mud-make"
            let material = all_url + "/owner/principal-material"
            let soft = all_url + "/owner/soft-outfit-assort"
            let fixation = all_url + "/owner/fixation-furniture"
            let move = all_url + "/owner/move-furniture"
            let assort = all_url + "/owner/appliances-assort"
            let life = all_url + "/owner/life-assort"
            let intelligence = all_url + "/owner/intelligence-assort"
            let data = {
                bedroom: $scope.house_bedroom,
                area: $scope.area,      //面积
                hall: $scope.house_hall,       //餐厅
                toilet: $scope.house_toilet,   // 卫生间
                kitchen: $scope.house_kitchen,  //厨房
                stairway: +$scope.choose_stairs, //楼梯
                structure: $scope.nowStairs,
                series: $scope.series_index + 1,   //系列
                style: $scope.style_index + 1,  //风格
                window: $scope.window,//飘窗
                high: $scope.highCrtl, //层高
                province: 510000,   //省编码
                city: 510100,      // 市编码
                stairway_id: +$scope.choose_stairs,//有无楼梯
                stairs: $scope.nowStairs//楼梯结构
            }
            let labor_category = {"worker_category": ['杂工'], '杂工': {'price': 0, 'worker_kind': '杂工'}}
            let data1 = {}
            for (let i in data) {
                data1[i] = data[i]
            }
            data1["waterproof_total_area"] = 60
            //发数据给后台
            //第一个 弱电接口
            $http.post(url, data, config).then(function (response) {
                console.log("弱电")
                console.log(response)
                $scope.labor_price += response.data.data.weak_current_labor_price.price
                let arr = response.data.data.weak_current_material
                let weak_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        weak_arr.push(arr[item])
                    }
                }
                console.log(weak_arr)
                //一级总费用统计
                for (let [key, value] of weak_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of weak_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of weak_arr.entries()) {
                    $scope.platform_price += value.cost
                }
                //装修公司供货价处理
                for (let [key, value] of weak_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                }
                console.log($scope.platform_price)
                //工人费用处理
                if (labor_category["worker_category"].indexOf(response.data.data.weak_current_labor_price.worker_kind) == -1) {
                    labor_category["worker_category"].push(response.data.data.weak_current_labor_price.worker_kind)
                    labor_category[response.data.data.weak_current_labor_price.worker_kind + ''] = response.data.data.weak_current_labor_price
                } else {
                    for (let [key, value] of labor_category["worker_category"].entries()) {
                        if (response.data.data.weak_current_labor_price.worker_kind == value) {
                            if (!labor_category[value + '']) {
                                labor_category[value + ''] = response.data.data.weak_current_labor_price
                            } else {
                                labor_category[value + ''].price += response.data.data.weak_current_labor_price.price
                            }
                        }
                    }
                }
                //整合一级二级三级
                for (let [key, value] of weak_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of weak_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                            }
                        }
                    }
                }
                $scope.labor_category = labor_category
                console.log($scope.level)
                console.log($scope.stair)
                console.log(labor_category)
            }, function (error) {
                console.log(error)
            })
            //第二个 强电接口
            $http.post(strong, data, config).then(function (response) {
                console.log("强电")
                console.log(response)
                $scope.labor_price += response.data.data.strong_current_labor_price.price
                let arr = response.data.data.strong_current_material
                let strong_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        strong_arr.push(arr[item])
                    }
                }
                console.log(strong_arr)
                //一级总费用统计
                for (let [key, value] of strong_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of strong_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of strong_arr.entries()) {
                    $scope.platform_price += value.cost
                }
                //装修公司供货价处理
                for (let [key, value] of strong_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                }
                //工人费用处理
                if (labor_category["worker_category"].indexOf(response.data.data.strong_current_labor_price.worker_kind) == -1) {
                    labor_category["worker_category"].push(response.data.data.strong_current_labor_price.worker_kind)
                    labor_category[response.data.data.strong_current_labor_price.worker_kind + ''] = response.data.data.strong_current_labor_price
                } else {
                    for (let [key, value] of labor_category["worker_category"].entries()) {
                        if (response.data.data.strong_current_labor_price.worker_kind == value) {
                            if (!labor_category[value]) {
                                labor_category[value] = response.data.data.strong_current_labor_price
                            } else {
                                labor_category[value].price += response.data.data.strong_current_labor_price.price
                            }
                        }
                    }
                }
                //整合一级二级三级
                for (let [key, value] of strong_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of strong_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                            }
                        }
                    }
                }
                $scope.labor_category = labor_category
                console.log(labor_category)
                console.log($scope.level)
                console.log($scope.stair)
            }, function (error) {
                console.log(error)
            })
            //第三个 水路接口
            $http.post(waterway, data, config).then(function (response) {
                console.log("水路")
                console.log(response)
                $scope.labor_price += response.data.data.waterway_labor_price.price
                let arr = response.data.data.waterway_material_price
                let waterway_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        waterway_arr.push(arr[item])
                    }
                }
                console.log(waterway_arr)
                //一级总费用统计
                for (let [key, value] of waterway_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of waterway_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of waterway_arr.entries()) {
                    $scope.platform_price += value.cost
                }
                //装修公司供货价处理
                for (let [key, value] of waterway_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                }
                //工人费用处理
                if (labor_category["worker_category"].indexOf(response.data.data.waterway_labor_price.worker_kind) == -1) {
                    labor_category["worker_category"].push(response.data.data.waterway_labor_price.worker_kind)
                    labor_category[response.data.data.waterway_labor_price.worker_kind + ''] = response.data.data.waterway_labor_price
                } else {
                    for (let [key, value] of labor_category["worker_category"].entries()) {
                        if (response.data.data.waterway_labor_price.worker_kind == value) {
                            if (!labor_category[value]) {
                                labor_category[value] = response.data.data.waterway_labor_price
                            } else {
                                labor_category[value].price += response.data.data.waterway_labor_price.price
                            }
                        }
                    }
                }
                console.log(labor_category)
                //整合一级二级三级
                for (let [key, value] of waterway_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of waterway_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                            }
                        }
                    }
                }
            }, function (error) {
                console.log(error)
            })
            //第四个 防水接口
            $http.post(waterproof, data, config).then(function (response) {
                console.log("防水")
                console.log(response)
                $scope.labor_price += response.data.data.waterproof_labor_price.price
                let carpentry = response.data.data.waterproof_material
                //一级总费用统计
                for (let [key1, value1] of $scope.stair.entries()) {
                    if (carpentry.path.split(',')[0] == value1.id) {
                        value1["cost"] += carpentry.cost
                    }
                }
                //二级总费用统计
                for (let [key1, value1] of $scope.level.entries()) {
                    if (carpentry.path.split(',')[1] == value1.id) {
                        value1["cost"] += carpentry.cost
                    }
                }
                //平台价格处理
                $scope.platform_price += carpentry.cost
                //装修公司供货价处理
                $scope.supply_price += carpentry.purchase_price_decoration_company * carpentry.quantity
                //工人费用处理
                if (labor_category["worker_category"].indexOf(response.data.data.waterproof_labor_price.worker_kind) == -1) {
                    labor_category["worker_category"].push(response.data.data.waterproof_labor_price.worker_kind)
                    labor_category[response.data.data.waterproof_labor_price.worker_kind + ''] = response.data.data.waterproof_labor_price
                } else {
                    for (let [key, value] of labor_category["worker_category"].entries()) {
                        if (response.data.data.waterproof_labor_price.worker_kind == value) {
                            if (!labor_category[value + '']) {
                                labor_category[value + ''] = response.data.data.waterproof_labor_price
                            } else {
                                labor_category[value + ''].price += response.data.data.waterproof_labor_price.price
                            }
                        }
                    }
                }
                //整合一级二级三级
                for (let [key1, value1] of $scope.level.entries()) {
                    if (carpentry.path.split(',')[1] == value1.id) {
                        if (value1.three_level.indexOf(carpentry.path.split(',')[2]) == -1) {
                            value1.three_level.push(carpentry.path.split(',')[2])
                            if (!value1[carpentry.path.split(',')[2]]) {
                                value1[carpentry.path.split(',')[2]] = {
                                    'goods_detail': [], 'cost': 0,
                                    'id': carpentry.path.split(',')[2], 'title': carpentry.title
                                }
                            }
                            value1[carpentry.path.split(',')[2]][carpentry.id] = carpentry
                            value1[carpentry.path.split(',')[2]]['goods_detail'].push(carpentry.id)
                            value1[carpentry.path.split(',')[2]].cost = carpentry.cost
                        } else {
                            if (value1[carpentry.path.split(',')[2]]['goods_detail'].indexOf(carpentry.id) == -1) {
                                value1[carpentry.path.split(',')[2]]['goods_detail'].push(carpentry.id)
                                value1[carpentry.path.split(',')[2]][carpentry.id] = carpentry
                                value1[carpentry.path.split(',')[2]].cost += carpentry.cost
                            } else {
                                value1[carpentry.path.split(',')[2]][carpentry.id].cost += carpentry.cost
                                value1[carpentry.path.split(',')[2]][carpentry.id].quantity += carpentry.quantity
                                value1[carpentry.path.split(',')[2]].cost += carpentry.cost
                            }
                        }
                    }
                }
                for (let [key1, value1] of $scope.stair.entries()) {
                    for (let [key2, value2] of $scope.level.entries()) {
                        if (carpentry.path.split(',')[0] == value1.id && carpentry.path.split(',')[1] == value2.id) {
                            if (value1.second_level.indexOf(value2.id) == -1) {
                                value1.second_level.push(value2.id)
                                value1[value2.id] = value2
                            } else {
                                value1[value2.id] = value2
                            }
                        }
                    }
                }
            }, function (error) {
                console.log(error)
            })
            //第五个 木作接口
            $http.post(carpentry, data, config).then(function (response) {
                console.log("木作")
                console.log(response)
                $scope.labor_price += response.data.data.carpentry_labor_price.price
                let arr = response.data.data.carpentry_material
                let carpentry_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        carpentry_arr.push(arr[item])
                    }
                }
                console.log(carpentry_arr)
                //一级总费用统计
                for (let [key, value] of carpentry_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of carpentry_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of carpentry_arr.entries()) {
                    $scope.platform_price += value.cost
                }
                //装修公司供货价处理
                for (let [key, value] of carpentry_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                }
                //工人费用处理
                if (labor_category["worker_category"].indexOf(response.data.data.carpentry_labor_price.worker_kind) == -1) {
                    labor_category["worker_category"].push(response.data.data.carpentry_labor_price.worker_kind)
                    labor_category[response.data.data.carpentry_labor_price.worker_kind + ''] = response.data.data.carpentry_labor_price
                } else {
                    for (let [key, value] of labor_category["worker_category"].entries()) {
                        if (response.data.data.carpentry_labor_price.worker_kind == value) {
                            if (!labor_category[value + '']) {
                                labor_category[value + ''] = response.data.data.carpentry_labor_price
                            } else {
                                labor_category[value + ''].price += response.data.data.carpentry_labor_price.price
                            }
                        }
                    }
                }
                //整合一级二级三级
                for (let [key, value] of carpentry_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of carpentry_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                            }
                        }
                    }
                }
                $scope.labor_category = labor_category
            }, function (error) {
                console.log(error)
            })
            //第六个 乳胶漆接口
            $http.post(coating, data, config).then(function (response) {
                console.log("乳胶漆")
                console.log(response)
                $scope.labor_price += response.data.data.coating_labor_price.price
                let arr = response.data.data.coating_material
                let coating_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        coating_arr.push(arr[item])
                    }
                }
                console.log(coating_arr)
                //一级总费用统计
                for (let [key, value] of coating_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of coating_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of coating_arr.entries()) {
                    $scope.platform_price += value.cost
                }
                //装修公司供货价处理
                for (let [key, value] of coating_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                }
                //工人费用处理
                if (labor_category["worker_category"].indexOf(response.data.data.coating_labor_price.worker_kind) == -1) {
                    labor_category["worker_category"].push(response.data.data.coating_labor_price.worker_kind)
                    labor_category[response.data.data.coating_labor_price.worker_kind + ''] = response.data.data.coating_labor_price
                } else {
                    for (let [key, value] of labor_category["worker_category"].entries()) {
                        if (response.data.data.coating_labor_price.worker_kind == value) {
                            if (!labor_category[value + '']) {
                                labor_category[value + ''] = response.data.data.coating_labor_price
                            } else {
                                labor_category[value + ''].price += response.data.data.coating_labor_price.price
                            }
                        }
                    }
                }
                //整合一级二级三级
                for (let [key, value] of coating_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of coating_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                            }
                        }
                    }
                }
                $scope.labor_category = labor_category
                console.log(labor_category)
            }, function (error) {
                console.log(error)
            })
            //第七个 泥作接口
            $http.post(make, data1, config).then(function (response) {
                console.log("泥作")
                console.log(response)
                $scope.labor_price += response.data.data.mud_make_labor_price.price
                let arr = response.data.data.mud_make_material
                let mud_make_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        mud_make_arr.push(arr[item])
                    }
                }
                console.log(mud_make_arr)
                //一级总费用统计
                for (let [key, value] of mud_make_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of mud_make_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of mud_make_arr.entries()) {
                    $scope.platform_price += value.cost
                }
                //装修公司供货价处理
                for (let [key, value] of mud_make_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                }
                //工人费用处理
                if (labor_category["worker_category"].indexOf(response.data.data.mud_make_labor_price.worker_kind) == -1) {
                    labor_category["worker_category"].push(response.data.data.mud_make_labor_price.worker_kind)
                    labor_category[response.data.data.mud_make_labor_price.worker_kind + ''] = response.data.data.mud_make_labor_price
                } else {
                    for (let [key, value] of labor_category["worker_category"].entries()) {
                        if (response.data.data.mud_make_labor_price.worker_kind == value) {
                            if (!labor_category[value + '']) {
                                labor_category[value + ''] = response.data.data.mud_make_labor_price
                            } else {
                                labor_category[value + ''].price += response.data.data.mud_make_labor_price.price
                            }
                        }
                    }
                }
                //整合一级二级三级
                for (let [key, value] of mud_make_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of mud_make_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                            }
                        }
                    }
                }
                $scope.labor_category = labor_category
                console.log(labor_category)
                console.log($scope.level)
                console.log($scope.stair)
            }, function (error) {
                console.log(error)
            })
            //第八个 主材接口
            $http.post(material, data, config).then(function (response) {
                console.log("主材")
                console.log(response)
                let material_arr = response.data.data.goods
                console.log(material_arr)
                //一级总费用统计
                for (let [key, value] of material_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of material_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of material_arr.entries()) {
                    $scope.platform_price += value.cost
                }
                //装修公司供货价处理
                for (let [key, value] of material_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                }
                //整合一级二级三级
                for (let [key, value] of material_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of material_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                            }
                        }
                    }
                }
                console.log($scope.stair)
                console.log($scope.level)
            }, function (error) {
                console.log(error)
            })
            //第九个 软装接口
            $http.post(soft, data, config).then(function (response) {
                console.log("软装")
                console.log(response)
                let soft_arr = response.data.data.goods
                console.log(soft_arr)
                //一级总费用统计
                for (let [key, value] of soft_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of soft_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of soft_arr.entries()) {
                    $scope.platform_price += value.show_cost
                }
                //装修公司供货价处理
                for (let [key, value] of soft_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.show_quantity
                }
                //整合一级二级三级
                for (let [key, value] of soft_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.show_cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.show_cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].show_cost += value.show_cost
                                    value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of soft_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                                value1['goods_count'] = soft_arr.length
                            }
                        }
                    }
                }
                console.log($scope.stair)
                console.log($scope.level)
            }, function (error) {
                console.log(error)
            })
            //第十个 固定家居接口
            $http.post(fixation, data, config).then(function (response) {
                console.log("固定家具")
                console.log(response)
                let fixation_arr = []
                for (let [key, value] of  response.data.data.goods.entries()) {
                    if (value != null) {
                        fixation_arr.push(value)
                    }
                }
                //一级总费用统计
                for (let [key, value] of fixation_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of fixation_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of fixation_arr.entries()) {
                    $scope.platform_price += value.show_cost
                }
                //装修公司供货价处理
                for (let [key, value] of fixation_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.show_quantity
                }
                //整合一级二级三级
                for (let [key, value] of fixation_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.show_cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.show_cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].show_cost += value.show_cost
                                    value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of fixation_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                                value1['goods_count'] = fixation_arr.length
                            }
                        }
                    }
                }
            }, function (error) {
                console.log(error)
            })
            //第十一个 移动家具接口
            $http.post(move, data, config).then(function (response) {
                console.log("移动家具")
                console.log(response)
                let move_arr = response.data.data.goods
                console.log(move_arr)
                //一级总费用统计
                for (let [key, value] of move_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of move_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of move_arr.entries()) {
                    $scope.platform_price += value.show_cost
                }
                //装修公司供货价处理
                for (let [key, value] of move_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.show_quantity
                }
                //整合一级二级三级
                for (let [key, value] of move_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.show_cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.show_cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].show_cost += value.show_cost
                                    value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of move_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                                value1['goods_count'] = move_arr.length
                            }
                        }
                    }
                }
                console.log($scope.stair)
                console.log($scope.level)
            }, function (error) {
                console.log(error)
            })
            //第十二个 家电配套接口
            $http.post(assort, data, config).then(function (response) {
                console.log("家电配套")
                console.log(response)

                response.data.data.goods.splice(response.data.data.goods.indexOf(null), 1)
                let assort_arr = response.data.data.goods
                console.log(assort_arr)
                //一级总费用统计
                for (let [key, value] of assort_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of assort_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of assort_arr.entries()) {
                    $scope.platform_price += value.show_cost
                }
                //装修公司供货价处理
                for (let [key, value] of assort_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.show_quantity
                }
                //整合一级二级三级
                for (let [key, value] of assort_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.show_cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.show_cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].show_cost += value.show_cost
                                    value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of assort_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                                value1['goods_count'] = assort_arr.length
                            }
                        }
                    }
                }
                console.log($scope.stair)
                console.log($scope.level)
            }, function (error) {
                console.log(error)
            })
            //第十三个 生活配套接口
            $http.post(life, data, config).then(function (response) {
                console.log("生活配套")
                console.log(response)
                response.data.data.goods.splice(response.data.data.goods.indexOf(null), 1)
                let life_arr = response.data.data.goods
                //一级总费用统计
                for (let [key, value] of life_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of life_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of life_arr.entries()) {
                    $scope.platform_price += value.show_cost
                }
                //装修公司供货价处理
                for (let [key, value] of life_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.show_quantity
                }
                //整合一级二级三级
                for (let [key, value] of life_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.show_cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.show_cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].show_cost += value.show_cost
                                    value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of life_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                                value1['goods_count'] = life_arr.length
                            }
                        }
                    }
                }
            }, function (error) {
                console.log(error)
            })
            //第十四个 智能配套接口
            $http.post(intelligence, data, config).then(function (response) {
                console.log("智能配套")
                console.log(response)
                response.data.data.goods.splice(response.data.data.goods.indexOf(null), 1)
                let intelligence_arr = response.data.data.goods
                //一级总费用统计
                for (let [key, value] of intelligence_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of intelligence_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.show_cost
                        }
                    }
                }
                //平台价格处理
                for (let [key, value] of intelligence_arr.entries()) {
                    $scope.platform_price += value.show_cost
                }
                //装修公司供货价处理
                for (let [key, value] of intelligence_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.show_quantity
                }
                //整合一级二级三级
                for (let [key, value] of intelligence_arr.entries()) {
                    for (let [key1, value1] of $scope.level.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.show_cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.show_cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].show_cost += value.show_cost
                                    value1[value.path.split(',')[2]][value.id].show_quantity += value.show_quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of intelligence_arr.entries()) {
                    for (let [key1, value1] of $scope.stair.entries()) {
                        for (let [key2, value2] of $scope.level.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                    value1[value2.id] = value2
                                } else {
                                    value1[value2.id] = value2
                                }
                                value1['goods_count'] = intelligence_arr.length
                            }
                        }
                    }
                }
            }, function (error) {
                console.log(error)
            })
            $scope.isClick = true
            console.log()
        }
        //传递数据
        $scope.goDetail = function (item, index) {
            console.log(item)
            console.log($scope.series_index)
            console.log($scope.labor_category)
            if (item.title == "辅材") {
                $state.go("basics", {
                    'stair': $scope.stair,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'level': $scope.level,
                    'stair_copy': angular.copy($scope.stair),
                    'level_copy': angular.copy($scope.level),
                    'index': index,
                    'worker_category': $scope.labor_category,
                    'handyman_price': $scope.handyman_price,
                    'area': $scope.area,
                    'cur_stair': !$scope.choose_stairs ? $scope.nowStairs : '实木构造',
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'labor_price': $scope.labor_price,
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'window': $scope.window,
                    'choose_stairs': $scope.choose_stairs,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                })
            } else if (item.title == "主要材料") {
                $state.go("main", {
                    'stair': $scope.stair,
                    'level': $scope.level,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'stair_copy': angular.copy($scope.stair),
                    'level_copy': angular.copy($scope.level),
                    'index': index,
                    'worker_category': $scope.labor_category,
                    'handyman_price': $scope.handyman_price,
                    'area': $scope.area,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'labor_price': $scope.labor_price,
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'cur_stair': !$scope.choose_stairs ? $scope.nowStairs : '实木构造',
                    'window': $scope.window,
                    'choose_stairs': $scope.choose_stairs,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                })
            } else {
                $state.go("other", {
                    'stair': $scope.stair,
                    'stair_copy': $scope.stair,
                    'level_copy': $scope.level,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'index': index,
                    'level': $scope.level,
                    'worker_category': $scope.labor_category,
                    'handyman_price': $scope.handyman_price,
                    'area': $scope.area,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'labor_price': $scope.labor_price,
                    'cur_stair': !$scope.choose_stairs ? $scope.nowStairs : '实木构造',
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'window': $scope.window,
                    'choose_stairs': $scope.choose_stairs,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                })
            }
        }
        //详细地址监听
        $scope.$watch('message', function (newVal, oldVal) {
            if (newVal && newVal != oldVal) {
                if (newVal.length > 45) {
                    $scope.message = newVal.substr(0, 45)
                }
            }
        })
        //页面表单数据有所改变监听
        $scope.$watch('area', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        $scope.$watch('house_bedroom', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        $scope.$watch('house_hall', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        $scope.$watch('house_toilet', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        $scope.$watch('house_kitchen', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        $scope.$watch('highCrtl', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        $scope.$watch('window', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        $scope.$watch('choose_stairs', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        $scope.$watch('nowStairs', function (newVal, oldVal) {
            if (newVal != oldVal) {
                $scope.isClick = false
            }
        })
        // $scope.$watch('choose_stairs', function (newVal, oldVal) {
        //     if (newVal != oldVal) {
        //         $scope.isClick = false
        //     }
        // })
        // $scope.$watch('choose_stairs', function (newVal, oldVal) {
        //     if (newVal != oldVal) {
        //         $scope.isClick = false
        //     }
        // })

        //请求后台数据
        $http.get(all_url + '/owner/series-and-style').then(function (response) {
            $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
            $scope.series = response.data.data.show.series;//系列数据
            $scope.style = response.data.data.show.style;//风格数据
            $scope.style_picture = response.data.data.show.style_picture;//轮播图片数据
            console.log(response)
        }, function (response) {

        })
        //切换楼梯
        $scope.toggleStairs = function (item) {
            $scope.nowStairs = item;
            $scope.isClick = false
        }
        //切换系列
        $scope.toggleSeries = function (item, index) {
            $scope.nowSeries = item;
            $scope.series_index = index
            $scope.swiperImg = $scope.style_picture.slice(index, index * 3)
            $scope.isClick = false
        }
        //切换风格
        $scope.toggleStyle = function (item, index) {
            $scope.style_index = index
            $scope.nowStyle = item;
            $scope.isClick = false
        }
    })
    .controller("location_city_ctrl", function ($scope) {//城市选择控制器
        // $scope.goPrev = function () {
        //     window.history.back()
        // }
    })
    .controller("basics_ctrl", function ($scope, $stateParams, $http, $state) {//辅材页面
        console.log($stateParams)
        $scope.modalData = ''
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.stair = $stateParams.stair
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.index = $stateParams.index
        $scope.stair_copy = $stateParams.stair_copy
        $scope.level_copy = $stateParams.level_copy
        $scope.first_level = $stateParams.stair[$stateParams.index]
        $scope.labor_price = $stateParams.labor_price
        $scope.series_index = $stateParams.series_index
        $scope.style_index = $stateParams.style_index
        $scope.cur_stair = $stateParams.cur_stair
        $scope.worker_category = $stateParams.worker_category
        $scope.level = $stateParams.level
        $scope.area = $stateParams.area
        $scope.house_bedroom = $stateParams.house_bedroom
        $scope.house_hall = $stateParams.house_hall
        $scope.house_kitchen = $stateParams.house_kitchen
        $scope.house_toilet = $stateParams.house_toilet
        $scope.highCrtl = $stateParams.highCrtl
        $scope.window = $stateParams.window
        let arr = []
        let arr1 = $scope.stair
        for (let [key, value] of $stateParams.worker_category.worker_category.entries()) {
            if (value == '杂工') {
                $scope.handyman_price = $stateParams.worker_category[value].price
            } else {
                arr.push($stateParams.worker_category[value])
            }
        }
        $scope.labor_category = arr
        console.log(arr)
        //杂工数据
        $scope.complete = !!$stateParams.twelve_dismantle || false
        $scope.complete1 = !!$stateParams.twenty_four_dismantle || false
        $scope.complete2 = !!$stateParams.repair || false
        $scope.complete3 = !!$stateParams.twelve_new_construction || false
        $scope.complete4 = !!$stateParams.twenty_four_new_construction || false
        $scope.twelve_dismantle = $stateParams.twelve_dismantle || ''
        $scope.twenty_four_dismantle = $stateParams.twenty_four_dismantle || ''
        $scope.repair = $stateParams.repair || ''
        $scope.twelve_new_construction = $stateParams.twelve_new_construction || ''
        $scope.twenty_four_new_construction = $stateParams.twenty_four_new_construction || ''
        $scope.building_scrap = $stateParams.building_scrap || false
        console.log($stateParams.first_level)
        console.log($scope.first_level)
        $scope.goModalData = function (item) {
            $scope.modalData = item
            console.log($scope.modalData)
        }
        //杂工数据请求
        let url = 'http://test.cdlhzz.cn:888/owner/handyman'
        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        }
        console.log(arr1)
        //返回上一页
        $scope.curGoPrev = function () {
            console.log($stateParams.stair)
            $state.go('nodata', {
                    'isBack': true,
                    'level': $stateParams.level,
                    'stair': $stateParams.stair,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                    'level_copy': $stateParams.level,
                    'stair_copy': $stateParams.stair,
                    'labor_price': $scope.labor_price,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'worker_category': $scope.worker_category,
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'area': $scope.area,
                    'window': $scope.window,
                    'cur_stair': $scope.cur_stair,
                    'choose_stairs': $scope.choose_stairs,
                    'twelve_dismantle': $stateParams.twelve_dismantle,
                    'twenty_four_dismantle': $stateParams.twenty_four_dismantle,
                    'repair': $stateParams.repair,
                    'twelve_new_construction': $stateParams.twelve_new_construction,
                    'twenty_four_new_construction': $stateParams.twenty_four_new_construction,
                    'building_scrap': $stateParams.building_scrap
                }
            )
        }
        //杂工费用
        $scope.get_handyman_price = function () {
            let data = {
                'province': 510000,
                'city': 510100,
                '12_dismantle': $scope.complete ? +$scope.twelve_dismantle : 0,
                '24_dismantle': $scope.complete1 ? +$scope.twenty_four_dismantle : 0,
                'repair': $scope.complete2 ? +$scope.repair : 0,
                '12_new_construction': $scope.complete3 ? +$scope.twelve_new_construction : 0,
                '24_new_construction': $scope.complete4 ? +$scope.twenty_four_new_construction : 0,
                'building_scrap': $scope.building_scrap,
                'area': $stateParams.area,
                'series': $stateParams.series_index + 1,
                'style': $stateParams.style_index + 1,
            }
            $http.post(url, data, config).then(function (response) {
                console.log(response)
                let arr = response.data.data.total_material
                let other_arr = []
                for (let item in arr) {
                    if (item != "total_cost") {
                        other_arr.push(arr[item])
                    }
                }
                console.log(other_arr)
                let cur_price = response.data.data.labor_cost.price - $stateParams.worker_category['杂工'].price
                $scope.worker_category['杂工'].price = response.data.data.labor_cost.price
                $scope.labor_price += cur_price
                console.log($scope.labor_price)
                //一级总费用统计
                for (let [key, value] of other_arr.entries()) {
                    for (let [key1, value1] of $scope.stair_copy.entries()) {
                        if (value.path.split(',')[0] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //二级总费用统计
                for (let [key, value] of other_arr.entries()) {
                    for (let [key1, value1] of $scope.level_copy.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            value1["cost"] += value.cost
                        }
                    }
                }
                //整合一级二级三级
                for (let [key, value] of other_arr.entries()) {
                    for (let [key1, value1] of $scope.level_copy.entries()) {
                        if (value.path.split(',')[1] == value1.id) {
                            if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                value1.three_level.push(value.path.split(',')[2])
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'goods_detail': [], 'cost': 0,
                                        'id': value.path.split(',')[2], 'title': value.title
                                    }
                                }
                                value1[value.path.split(',')[2]][value.id] = value
                                value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                value1[value.path.split(',')[2]].cost = value.cost
                            } else {
                                if (value1[value.path.split(',')[2]]['goods_detail'].indexOf(value.id) == -1) {
                                    value1[value.path.split(',')[2]]['goods_detail'].push(value.id)
                                    value1[value.path.split(',')[2]][value.id] = value
                                    value1[value.path.split(',')[2]].cost += value.cost
                                } else {
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                            }
                        }
                    }
                }
                for (let [key, value] of other_arr.entries()) {
                    for (let [key1, value1] of $scope.stair_copy.entries()) {
                        for (let [key2, value2] of $scope.level_copy.entries()) {
                            if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                if (value1.second_level.indexOf(value2.id) == -1) {
                                    value1.second_level.push(value2.id)
                                }
                                if (!value1[value2.id]) {
                                    value1[value2.id] = value2
                                }
                            }
                        }
                    }
                }
                $scope.stair = $scope.stair_copy
                $scope.level = $scope.level_copy
                console.log($scope.stair_copy)
                console.log($scope.level_copy)
                console.log($scope.level)
                console.log($scope.stair)
                console.log($scope.worker_category)
                console.log($scope.labor_price)
                $scope.goPrev({
                    'isBack': true,
                    'level': $scope.level,
                    'stair_copy': $scope.stair,
                    'level_copy': $scope.level,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'stair': $scope.stair,
                    'labor_price': $scope.labor_price,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'worker_category': $scope.worker_category,
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'area': $scope.area,
                    'choose_stairs': $scope.choose_stairs,
                    'window': $scope.window,
                    'cur_stair': $scope.cur_stair,
                    'twelve_dismantle': $scope.complete ? $scope.twelve_dismantle : '',
                    'twenty_four_dismantle': $scope.complete ? $scope.twenty_four_dismantle : '',
                    'repair': $scope.complete ? $scope.repair : '',
                    'twelve_new_construction': $scope.complete ? $scope.twelve_new_construction : '',
                    'twenty_four_new_construction': $scope.complete ? $scope.twenty_four_new_construction : '',
                    'building_scrap': $scope.building_scrap,
                })
                console.log(response)
                console.log(arr1)
            }, function (error) {

            })
        }
    })
    .controller('other_ctrl', function ($scope, $stateParams, $state) {
        console.log($stateParams)
        //获取传递的数据(固定)
        $scope.stair = $stateParams.stair_copy
        $scope.level = $stateParams.level_copy
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.platform_price_copy = $stateParams.platform_price_copy || 0
        $scope.supply_price_copy = $stateParams.supply_price_copy || 0
        $scope.stair_copy = angular.copy($stateParams.stair_copy)
        $scope.level_copy = angular.copy($stateParams.level_copy)
        $scope.index = $stateParams.index
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.house_bedroom = $stateParams.house_bedroom
        $scope.house_hall = $stateParams.house_hall
        $scope.house_kitchen = $stateParams.house_kitchen
        $scope.house_toilet = $stateParams.house_toilet
        $scope.labor_price = $stateParams.labor_price
        $scope.series_index = $stateParams.series_index
        $scope.style_index = $stateParams.style_index
        $scope.area = $stateParams.area
        $scope.window = $stateParams.window
        $scope.worker_category = $stateParams.worker_category
        $scope.highCrtl = $stateParams.highCrtl
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.cur_stair = $stateParams.cur_stair
        //杂工数据
        $scope.twelve_dismantle = $stateParams.twelve_dismantle || ''
        $scope.twenty_four_dismantle = $stateParams.twenty_four_dismantle || ''
        $scope.repair = $stateParams.repair || ''
        $scope.twelve_new_construction = $stateParams.twelve_new_construction || ''
        $scope.twenty_four_new_construction = $stateParams.twenty_four_new_construction || ''
        $scope.building_scrap = $stateParams.building_scrap || false
        //编辑删除变量初始化
        $scope.edit = '编辑'
        $scope.is_delete_btn = false
        $scope.all_goods = angular.copy($scope.stair)
        //总价格
        $scope.all_price = $scope.stair[$stateParams.index].cost
        $scope.goModalData = function (item) {
            $scope.modalData = item
            console.log($scope.modalData)
        }
        $scope.curGoPrev = function () {
            console.log($stateParams.stair)
            $state.go('nodata', {
                    'isBack': true,
                    'level': $stateParams.level,
                    'stair': $stateParams.stair,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                    'level_copy': $stateParams.level,
                    'stair_copy': $stateParams.stair,
                    'labor_price': $scope.labor_price,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'worker_category': $scope.worker_category,
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'area': $scope.area,
                    'cur_stair': $scope.cur_stair,
                    'window': $scope.window,
                    'choose_stairs': $scope.choose_stairs,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                }
            )
        }
        //编辑删除处理
        $scope.edit_and_del = function () {
            if ($scope.edit == '编辑') {
                $scope.edit = '完成'
                $scope.is_delete_btn = true
            } else {
                $scope.edit = '编辑'
                $scope.is_delete_btn = false
            }
            console.log($scope.stair)
        }
        //保存
        $scope.change_material = function () {
            $scope.stair = $scope.stair_copy
            $scope.level = $scope.level_copy
            $scope.platform_price = $scope.platform_price_copy
            $scope.supply_price = $scope.supply_price_copy
            $state.go('nodata', {
                'isBack': true,
                'level': $scope.level,
                'stair': $scope.stair,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                'level_copy': $scope.level_copy,
                'stair_copy': $scope.stair_copy,
                'labor_price': $scope.labor_price,
                'series_index': $scope.series_index,
                'style_index': $scope.style_index,
                'worker_category': $scope.worker_category,
                'house_bedroom': $scope.house_bedroom,
                'house_hall': $scope.house_hall,
                'house_kitchen': $scope.house_kitchen,
                'house_toilet': $scope.house_toilet,
                'highCrtl': $scope.highCrtl,
                'cur_stair': $scope.cur_stair,
                'area': $scope.area,
                'window': $scope.window,
                'choose_stairs': $scope.choose_stairs,
                'twelve_dismantle': $scope.twelve_dismantle,
                'twenty_four_dismantle': $scope.twenty_four_dismantle,
                'repair': $scope.repair,
                'twelve_new_construction': $scope.twelve_new_construction,
                'twenty_four_new_construction': $scope.twenty_four_new_construction,
                'building_scrap': $scope.building_scrap
            })
        }
        //添加商品
        $scope.add_material = function () {
            $state.go('second_level_material', {
                'isBack': true,
                'level': $scope.level,
                'stair': $scope.stair,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                'level_copy': $scope.level,
                'stair_copy': $scope.stair,
                'labor_price': $scope.labor_price,
                'series_index': $scope.series_index,
                'style_index': $scope.style_index,
                'worker_category': $scope.worker_category,
                'house_bedroom': $scope.house_bedroom,
                'house_hall': $scope.house_hall,
                'house_kitchen': $scope.house_kitchen,
                'house_toilet': $scope.house_toilet,
                'highCrtl': $scope.highCrtl,
                'cur_stair': $scope.cur_stair,
                'area': $scope.area,
                'window': $scope.window,
                'index': $scope.index,
                'choose_stairs': $scope.choose_stairs,
                'twelve_dismantle': $scope.twelve_dismantle,
                'twenty_four_dismantle': $scope.twenty_four_dismantle,
                'repair': $scope.repair,
                'twelve_new_construction': $scope.twelve_new_construction,
                'twenty_four_new_construction': $scope.twenty_four_new_construction,
                'building_scrap': $scope.building_scrap
            })
        }
        //更换商品
        $scope.replace_material = function (item) {
            $state.go('commodity_details', {
                'isBack': true,
                'level': $scope.level,
                'stair': $scope.stair,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                'level_copy': $scope.level_copy,
                'stair_copy': $scope.stair_copy,
                'labor_price': $scope.labor_price,
                'series_index': $scope.series_index,
                'style_index': $scope.style_index,
                'worker_category': $scope.worker_category,
                'house_bedroom': $scope.house_bedroom,
                'house_hall': $scope.house_hall,
                'house_kitchen': $scope.house_kitchen,
                'house_toilet': $scope.house_toilet,
                'highCrtl': $scope.highCrtl,
                'area': $scope.area,
                'window': $scope.window,
                'index': $scope.index,
                'choose_stairs': $scope.choose_stairs,
                'excluded_item': item,
                'prev_index': 1,
                'cur_stair': $scope.cur_stair,
                'twelve_dismantle': $scope.twelve_dismantle,
                'twenty_four_dismantle': $scope.twenty_four_dismantle,
                'repair': $scope.repair,
                'twelve_new_construction': $scope.twelve_new_construction,
                'twenty_four_new_construction': $scope.twenty_four_new_construction,
                'building_scrap': $scope.building_scrap
            })
        }
        $scope.delete_category = function (item) {
            console.log($scope.stair)
            console.log($scope.level)
            console.log(item)
            for (let [key, value] of $scope.stair_copy.entries()) {
                if (item.path.split(',')[0] == value.id) {
                    value.cost -= item.show_cost
                    value[item.path.split(',')[1]][item.path.split(',')[2]].cost -= item.show_cost
                    delete  value[item.path.split(',')[1]][item.path.split(',')[2]][item.id]
                    value[item.path.split(',')[1]][item.path.split(',')[2]].goods_detail.splice(
                        value[item.path.split(',')[1]][item.path.split(',')[2]].goods_detail.indexOf(item.id), 1)
                    value['goods_count']--
                }
            }
            for (let [key, value] of $scope.level_copy.entries()) {
                if (item.path.split(',')[1] == value.id) {
                    value[item.path.split(',')[2]].cost -= item.show_cost
                    delete value[item.path.split(',')[2]][item.id]
                    value[item.path.split(',')[2]].goods_detail.splice(
                        value[item.path.split(',')[2]].goods_detail.indexOf(item.id), 1)
                }
            }
            // $scope.all_goods = arr
            console.log($scope.stair)
        }
    })
    .controller('second_level_material_ctrl', function ($scope, $http, $stateParams, $state) {//添加二级分类选择控制器
        console.log($stateParams)
        //获取传递的数据(固定)
        $scope.stair = $stateParams.stair
        $scope.level = $stateParams.level
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.stair_copy = $stateParams.stair_copy
        $scope.level_copy = $stateParams.level_copy
        $scope.index = $stateParams.index
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.house_bedroom = $stateParams.house_bedroom
        $scope.house_hall = $stateParams.house_hall
        $scope.house_kitchen = $stateParams.house_kitchen
        $scope.house_toilet = $stateParams.house_toilet
        $scope.labor_price = $stateParams.labor_price
        $scope.series_index = $stateParams.series_index
        $scope.style_index = $stateParams.style_index
        $scope.area = $stateParams.area
        $scope.window = $stateParams.window
        $scope.worker_category = $stateParams.worker_category
        $scope.highCrtl = $stateParams.highCrtl
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.cur_stair = $stateParams.cur_stair
        //杂工数据
        $scope.twelve_dismantle = $stateParams.twelve_dismantle || ''
        $scope.twenty_four_dismantle = $stateParams.twenty_four_dismantle || ''
        $scope.repair = $stateParams.repair || ''
        $scope.twelve_new_construction = $stateParams.twelve_new_construction || ''
        $scope.twenty_four_new_construction = $stateParams.twenty_four_new_construction || ''
        $scope.building_scrap = $stateParams.building_scrap || false
        //获取分类
        let pid = $stateParams.stair[$stateParams.index].id
        $http.get('http://test.cdlhzz.cn:888/mall/categories-level3?pid=' + pid).then(function (response) {
            $scope.second_material = response.data.categories_level3
            console.log(response)
        }, function (error) {
            console.log(error)
        })
        $scope.curGoPrev = function () {
            $state.go('other', {
                    'cur_stair': $scope.cur_stair,
                    'level': $stateParams.level,
                    'stair': $stateParams.stair,
                    'index': $stateParams.index,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                    'level_copy': $stateParams.level,
                    'stair_copy': $stateParams.stair,
                    'labor_price': $scope.labor_price,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'worker_category': $scope.worker_category,
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'area': $scope.area,
                    'window': $scope.window,
                    'choose_stairs': $scope.choose_stairs,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                }
            )
        }
        $scope.go_details = function (item) {
            $state.go('commodity_details', {
                stair: $scope.stair,
                index: $scope.index,
                level: $scope.level,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                stair_copy: $stateParams.stair_copy,
                level_copy: $stateParams.level_copy,
                worker_category: $scope.worker_category,
                handyman_price: $scope.handyman_price,
                area: $scope.area,
                series_index: $scope.series_index,
                style_index: $scope.style_index,
                labor_price: $scope.labor_price,
                house_bedroom: $scope.house_bedroom,
                house_hall: $scope.house_hall,
                house_kitchen: $scope.house_kitchen,
                house_toilet: $scope.house_toilet,
                highCrtl: $scope.highCrtl,
                window: $scope.window,
                choose_stairs: $scope.choose_stairs,
                second_material: $scope.second_material,
                three_material: item,
                pid: item.id,
                prev_index: 0,
                'cur_stair': $scope.cur_stair,
            })
        }
    })
    .controller('commodity_details_ctrl', function ($scope, $stateParams, $state, $http) {//三级分类商品控制器
        console.log($stateParams)
        //获取传递的数据(固定)
        $scope.stair = $stateParams.stair
        $scope.level = $stateParams.level
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.stair_copy = $stateParams.stair_copy || $stateParams.stair
        $scope.level_copy = $stateParams.level_copy || $stateParams.level_copy
        $scope.index = $stateParams.index
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.house_bedroom = $stateParams.house_bedroom
        $scope.house_hall = $stateParams.house_hall
        $scope.house_kitchen = $stateParams.house_kitchen
        $scope.house_toilet = $stateParams.house_toilet
        $scope.labor_price = $stateParams.labor_price
        $scope.series_index = $stateParams.series_index
        $scope.style_index = $stateParams.style_index
        $scope.area = $stateParams.area
        $scope.window = $stateParams.window
        $scope.worker_category = $stateParams.worker_category
        $scope.highCrtl = $stateParams.highCrtl
        $scope.cur_stair = $stateParams.cur_stair
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.second_material = $stateParams.second_material//三级各项分类
        $scope.three_material = $stateParams.three_material//三级单项传递
        $scope.excluded_item = $stateParams.excluded_item || ''
        $scope.prev_index = $stateParams.prev_index
        $scope.pid = $stateParams.pid || +$stateParams.excluded_item.path.split(',')[2] || ''//传递选择的三级id
        //杂工数据
        $scope.twelve_dismantle = $stateParams.twelve_dismantle || ''
        $scope.twenty_four_dismantle = $stateParams.twenty_four_dismantle || ''
        $scope.repair = $stateParams.repair || ''
        $scope.twelve_new_construction = $stateParams.twelve_new_construction || ''
        $scope.twenty_four_new_construction = $stateParams.twenty_four_new_construction || ''
        $scope.building_scrap = $stateParams.building_scrap || false
        //获取指定id三级下面详细商品
        $http.get('http://test.cdlhzz.cn:888/mall/category-goods?category_id=' + $scope.pid).then(function (response) {
            console.log(response)
            $scope.three_material_details = response.data.data.category_goods
        }, function (error) {
            console.log(error)
        })
        $scope.curGoPrev = function () {
            if ($scope.prev_index == 0) {
                $state.go('second_level_material', {
                    stair: $scope.stair,
                    index: $scope.index,
                    level: $scope.level,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    stair_copy: $stateParams.stair_copy,
                    level_copy: $stateParams.level_copy,
                    worker_category: $scope.worker_category,
                    handyman_price: $scope.handyman_price,
                    area: $scope.area,
                    series_index: $scope.series_index,
                    style_index: $scope.style_index,
                    labor_price: $scope.labor_price,
                    house_bedroom: $scope.house_bedroom,
                    house_hall: $scope.house_hall,
                    house_kitchen: $scope.house_kitchen,
                    house_toilet: $scope.house_toilet,
                    highCrtl: $scope.highCrtl,
                    window: $scope.window,
                    choose_stairs: $scope.choose_stairs,
                    second_material: $scope.second_material,
                    three_material: $scope.three_material,
                    'cur_stair': $scope.cur_stair,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                })
            } else if ($scope.prev_index == 1) {
                $state.go('other', {
                    stair: $scope.stair,
                    index: $scope.index,
                    level: $scope.level,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    stair_copy: $stateParams.stair_copy,
                    level_copy: $stateParams.level_copy,
                    worker_category: $scope.worker_category,
                    handyman_price: $scope.handyman_price,
                    area: $scope.area,
                    series_index: $scope.series_index,
                    style_index: $scope.style_index,
                    labor_price: $scope.labor_price,
                    house_bedroom: $scope.house_bedroom,
                    house_hall: $scope.house_hall,
                    house_kitchen: $scope.house_kitchen,
                    house_toilet: $scope.house_toilet,
                    highCrtl: $scope.highCrtl,
                    window: $scope.window,
                    choose_stairs: $scope.choose_stairs,
                    second_material: $scope.second_material,
                    three_material: $scope.three_material,
                    'cur_stair': $scope.cur_stair,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                })
            } else {
                $state.go('main', {
                    stair: $scope.stair,
                    index: $scope.index,
                    level: $scope.level,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    stair_copy: $stateParams.stair_copy,
                    level_copy: $stateParams.level_copy,
                    worker_category: $scope.worker_category,
                    handyman_price: $scope.handyman_price,
                    area: $scope.area,
                    series_index: $scope.series_index,
                    style_index: $scope.style_index,
                    labor_price: $scope.labor_price,
                    house_bedroom: $scope.house_bedroom,
                    house_hall: $scope.house_hall,
                    house_kitchen: $scope.house_kitchen,
                    house_toilet: $scope.house_toilet,
                    highCrtl: $scope.highCrtl,
                    window: $scope.window,
                    choose_stairs: $scope.choose_stairs,
                    second_material: $scope.second_material,
                    three_material: $scope.three_material,
                    'cur_stair': $scope.cur_stair,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                })
            }
        }
        //传递数据到详情页
        $scope.go_three_details = function (item) {
            $state.go('product_details', {
                stair: $scope.stair,
                index: $scope.index,
                level: $scope.level,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                stair_copy: $stateParams.stair_copy,
                level_copy: $stateParams.level_copy,
                worker_category: $scope.worker_category,
                handyman_price: $scope.handyman_price,
                area: $scope.area,
                series_index: $scope.series_index,
                style_index: $scope.style_index,
                labor_price: $scope.labor_price,
                house_bedroom: $scope.house_bedroom,
                house_hall: $scope.house_hall,
                house_kitchen: $scope.house_kitchen,
                house_toilet: $scope.house_toilet,
                highCrtl: $scope.highCrtl,
                window: $scope.window,
                choose_stairs: $scope.choose_stairs,
                second_material: $scope.second_material,
                three_material: $scope.three_material,
                three_material_details: $scope.three_material_details,
                product_details: item,
                prev_index: $stateParams.prev_index,
                excluded_item: $scope.excluded_item,
                pid: $scope.pid,
                'cur_stair': $scope.cur_stair,
                'twelve_dismantle': $scope.twelve_dismantle,
                'twenty_four_dismantle': $scope.twenty_four_dismantle,
                'repair': $scope.repair,
                'twelve_new_construction': $scope.twelve_new_construction,
                'twenty_four_new_construction': $scope.twenty_four_new_construction,
                'building_scrap': $scope.building_scrap
            })
        }
    })
    .controller('product_details_ctrl', function ($scope, $stateParams, $http, $state) {//商品详情控制器
        console.log($stateParams)
        //获取传递的数据(固定)
        $scope.stair = $stateParams.stair
        $scope.level = $stateParams.level
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.stair_copy = angular.copy($stateParams.stair_copy)
        $scope.level_copy = angular.copy($stateParams.level_copy)
        $scope.index = $stateParams.index
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.house_bedroom = $stateParams.house_bedroom
        $scope.house_hall = $stateParams.house_hall
        $scope.house_kitchen = $stateParams.house_kitchen
        $scope.house_toilet = $stateParams.house_toilet
        $scope.labor_price = $stateParams.labor_price
        $scope.series_index = $stateParams.series_index
        $scope.style_index = $stateParams.style_index
        $scope.area = $stateParams.area
        $scope.window = $stateParams.window
        $scope.cur_stair = $stateParams.cur_stair
        $scope.worker_category = $stateParams.worker_category
        $scope.highCrtl = $stateParams.highCrtl
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.pid = $stateParams.pid
        $scope.excluded_item = $stateParams.excluded_item//更换商品详情
        $scope.second_material = $stateParams.second_material//三级各项分类
        $scope.three_material = $stateParams.three_material || $stateParams.excluded_item//三级单项传递
        $scope.current_good = $stateParams.product_details //当前商品信息
        $scope.goods_id = $stateParams.product_details.id//当前商品id
        $scope.prev_index = $stateParams.prev_index//判断是更换还是添加
        $scope.excluded_item = $stateParams.excluded_item
        $scope.add_quantity = 1//添加数量
        $scope.platform_price_copy = angular.copy($scope.platform_price)
        $scope.supply_price_copy = angular.copy($scope.supply_price)
        //杂工数据
        $scope.twelve_dismantle = $stateParams.twelve_dismantle || ''
        $scope.twenty_four_dismantle = $stateParams.twenty_four_dismantle || ''
        $scope.repair = $stateParams.repair || ''
        $scope.twelve_new_construction = $stateParams.twelve_new_construction || ''
        $scope.twenty_four_new_construction = $stateParams.twenty_four_new_construction || ''
        $scope.building_scrap = $stateParams.building_scrap || false
        $scope.tab_title = 0
        let good = angular.copy($scope.current_good)
        let category = angular.copy($scope.three_material)
        $scope.subtract = function () {
            if ($scope.add_quantity <= 1) {
                $scope.add_quantity = 1
            } else {
                $scope.add_quantity--
            }
        }
        $scope.change_tab = function(){
            if($scope.tab_title == 0){
                $scope.tab_title = 1
            }else{
                $scope.tab_title = 0
            }
        }
        $scope.add = function () {
            $scope.add_quantity++
        }
        $http.get('http://test.cdlhzz.cn:888/mall/goods-view?id=' + $scope.goods_id).then(function (response) {
            $scope.good_detail = response.data.data['goods-view']
            console.log(response)
        }, function (error) {
            console.log(error)
        })
        $scope.add_goods = function () {
            //整合一级二级三级
            console.log($scope.level_copy)
            for (let [key1, value1] of $scope.level_copy.entries()) {
                if (category.path.split(',')[1] == value1.id) {
                    if (value1.three_level.indexOf(category.path.split(',')[2]) == -1) {
                        value1.three_level.push(category.path.split(',')[2])
                        if (!value1[category.path.split(',')[2]]) {
                            value1[category.path.split(',')[2]] = {
                                'goods_detail': [], 'cost': 0,
                                'id': category.path.split(',')[2], 'title': good.title
                            }
                        }
                        value1[category.path.split(',')[2]][good.id] = {
                            'show_cost': good.platform_price * $scope.add_quantity,
                            'show_quantity': parseInt($scope.add_quantity),
                            'id': good.id,
                            'name': $scope.good_detail.brand_name,
                            'platform_price': good.platform_price,
                            'subtitle': good.subtitle,
                            'title': good.title,
                            'path': category.path
                        }
                        value1[category.path.split(',')[2]]['goods_detail'].push(good.id)
                        value1[category.path.split(',')[2]].cost = good.platform_price * $scope.add_quantity
                    } else {
                        if (value1[category.path.split(',')[2]]['goods_detail'].indexOf(good.id) == -1) {
                            value1[category.path.split(',')[2]]['goods_detail'].push(good.id)
                            value1[category.path.split(',')[2]][good.id] = {
                                'show_cost': good.platform_price * $scope.add_quantity,
                                'show_quantity': parseInt($scope.add_quantity),
                                'id': good.id,
                                'name': $scope.good_detail.brand_name,
                                'platform_price': good.platform_price,
                                'subtitle': good.subtitle,
                                'title': good.title,
                                'path': category.path
                            }
                            value1[category.path.split(',')[2]].cost += good.platform_price * $scope.add_quantity
                        } else {
                            value1[category.path.split(',')[2]][good.id].show_cost += good.platform_price * $scope.add_quantity
                            value1[category.path.split(',')[2]][good.id].show_quantity = +value1[category.path.split(',')[2]][good.id].show_quantity + parseInt($scope.add_quantity)
                            value1[category.path.split(',')[2]].cost += good.platform_price * $scope.add_quantity
                        }
                    }
                }
            }
            for (let [key1, value1] of $scope.stair_copy.entries()) {
                for (let [key2, value2] of $scope.level_copy.entries()) {
                    if (category.path.split(',')[0] == value1.id && category.path.split(',')[1] == value2.id) {
                        if (value1.second_level.indexOf(value2.id) == -1) {
                            value1.second_level.push(value2.id)
                            value1[value2.id] = value2
                        } else {
                            value1[value2.id] = value2
                        }
                        value1.cost += good.platform_price * $scope.add_quantity
                        value1['goods_count']++
                    }
                }
            }
            $scope.platform_price_copy += good.platform_price * $scope.add_quantity
            $scope.supply_price_copy += good.purchase_price_decoration_company * $scope.add_quantity
            console.log($scope.stair)
            console.log($scope.level)
            $state.go("other", {
                stair: $scope.stair,
                level: $scope.level,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.platform_price,
                'platform_price_copy':$scope.platform_price_copy,
                'supply_price_copy':$scope.supply_price_copy,
                stair_copy: $scope.stair_copy,
                level_copy: $scope.level_copy,
                index: $scope.index,
                worker_category: $scope.labor_category,
                handyman_price: $scope.handyman_price,
                area: $scope.area,
                series_index: $scope.series_index,
                style_index: $scope.style_index,
                labor_price: $scope.labor_price,
                house_bedroom: $scope.house_bedroom,
                house_hall: $scope.house_hall,
                house_kitchen: $scope.house_kitchen,
                house_toilet: $scope.house_toilet,
                highCrtl: $scope.highCrtl,
                window: $scope.window,
                choose_stairs: $scope.choose_stairs,
                'cur_stair': $scope.cur_stair,
                'twelve_dismantle': $scope.twelve_dismantle,
                'twenty_four_dismantle': $scope.twenty_four_dismantle,
                'repair': $scope.repair,
                'twelve_new_construction': $scope.twelve_new_construction,
                'twenty_four_new_construction': $scope.twenty_four_new_construction,
                'building_scrap': $scope.building_scrap
            })
        }
        $scope.replace_goods = function () {
            console.log(category)
            console.log($scope.excluded_item)
            //整合一级二级三级
            if ($scope.prev_index == 1) {
                for (let [key1, value1] of $scope.level_copy.entries()) {
                    if ($scope.excluded_item.path.split(',')[1] == value1.id) {
                        delete value1[$scope.excluded_item.path.split(',')[2]][$scope.excluded_item.id]
                        value1[$scope.excluded_item.path.split(',')[2]].goods_detail.splice(value1[$scope.excluded_item.path.split(',')[2]].goods_detail
                            .indexOf($scope.excluded_item.id), 1)
                    }
                    if (category.path.split(',')[1] == value1.id) {
                        if (value1.three_level.indexOf(category.path.split(',')[2]) == -1) {
                            value1.three_level.push(category.path.split(',')[2])
                            if (!value1[category.path.split(',')[2]]) {
                                value1[category.path.split(',')[2]] = {
                                    'goods_detail': [], 'cost': 0,
                                    'id': category.path.split(',')[2], 'title': good.title
                                }
                            }
                            value1[category.path.split(',')[2]][good.id] = {
                                'show_cost': good.platform_price * $scope.add_quantity,
                                'show_quantity': +$scope.add_quantity,
                                'id': good.id,
                                'name': $scope.good_detail.brand_name
                                ,
                                'platform_price': good.platform_price,
                                'subtitle': good.subtitle,
                                'title': good.title,
                                'path': category.path
                            }
                            value1[category.path.split(',')[2]]['goods_detail'].push(good.id)
                            value1[category.path.split(',')[2]].cost = good.platform_price * $scope.add_quantity
                        } else {
                            if (value1[category.path.split(',')[2]]['goods_detail'].indexOf(good.id) == -1) {
                                value1[category.path.split(',')[2]]['goods_detail'].push(good.id)
                                value1[category.path.split(',')[2]][good.id] = {
                                    'show_cost': good.platform_price * $scope.add_quantity,
                                    'show_quantity': +$scope.add_quantity,
                                    'id': good.id,
                                    'name': $scope.good_detail.brand_name
                                    ,
                                    'platform_price': good.platform_price,
                                    'subtitle': good.subtitle,
                                    'title': good.title,
                                    'path': category.path
                                }
                                value1[category.path.split(',')[2]].cost += good.platform_price * $scope.add_quantity
                            } else {
                                value1[category.path.split(',')[2]][good.id].show_cost += good.platform_price * $scope.add_quantity
                                value1[category.path.split(',')[2]][good.id].show_quantity += $scope.add_quantity
                                value1[category.path.split(',')[2]].cost += good.platform_price * $scope.add_quantity
                            }
                        }
                    }
                }
            } else {
                for (let [key1, value1] of $scope.level_copy.entries()) {
                    if ($scope.excluded_item.path.split(',')[1] == value1.id) {
                        delete value1[$scope.excluded_item.path.split(',')[2]][$scope.excluded_item.id]
                        value1[$scope.excluded_item.path.split(',')[2]].goods_detail.splice(value1[$scope.excluded_item.path.split(',')[2]].goods_detail
                            .indexOf($scope.excluded_item.id), 1)
                    }
                    if (category.path.split(',')[1] == value1.id) {
                        if (value1.three_level.indexOf(category.path.split(',')[2]) == -1) {
                            value1.three_level.push(category.path.split(',')[2])
                            if (!value1[category.path.split(',')[2]]) {
                                value1[category.path.split(',')[2]] = {
                                    'goods_detail': [], 'cost': 0,
                                    'id': category.path.split(',')[2], 'title': good.title
                                }
                            }
                            value1[category.path.split(',')[2]][good.id] = {
                                'cost': good.platform_price * $scope.add_quantity,
                                'quantity': $scope.add_quantity, 'id': good.id, 'name': $scope.good_detail.brand_name
                                , 'platform_price': good.platform_price, 'subtitle': good.subtitle, 'title': good.title,
                                'path': category.path
                            }
                            value1[category.path.split(',')[2]]['goods_detail'].push(good.id)
                            value1[category.path.split(',')[2]].cost = good.platform_price * $scope.add_quantity
                        } else {
                            if (value1[category.path.split(',')[2]]['goods_detail'].indexOf(good.id) == -1) {
                                value1[category.path.split(',')[2]]['goods_detail'].push(good.id)
                                value1[category.path.split(',')[2]][good.id] = {
                                    'cost': good.platform_price * $scope.add_quantity,
                                    'quantity': $scope.add_quantity,
                                    'id': good.id,
                                    'name': $scope.good_detail.brand_name,
                                    'platform_price': good.platform_price,
                                    'subtitle': good.subtitle,
                                    'title': good.title,
                                    'path': category.path
                                }
                                value1[category.path.split(',')[2]].cost += good.platform_price * $scope.add_quantity
                            } else {
                                value1[category.path.split(',')[2]][good.id].cost += good.platform_price * $scope.add_quantity
                                value1[category.path.split(',')[2]][good.id].quantity += $scope.add_quantity
                                value1[category.path.split(',')[2]].cost += good.platform_price * $scope.add_quantity
                            }
                        }
                    }
                }
            }

            for (let [key1, value1] of $scope.stair_copy.entries()) {
                for (let [key2, value2] of $scope.level_copy.entries()) {
                    if (category.path.split(',')[0] == value1.id && category.path.split(',')[1] == value2.id) {
                        if (value1.second_level.indexOf(value2.id) == -1) {
                            value1.second_level.push(value2.id)
                            value1[value2.id] = value2
                        } else {
                            value1[value2.id] = value2
                        }
                        if ($scope.prev_index == 1) {
                            value1.cost -= +category.show_cost
                            value2.cost -= +category.show_cost
                        } else {
                            value1.cost -= +category.cost
                            value2.cost -= +category.cost
                        }
                        value1.cost += good.platform_price * $scope.add_quantity

                        value2.cost += good.platform_price * $scope.add_quantity
                        value2[category.path.split(',')[2]].cost -= +category.show_cost
                    }
                }
            }
            if ($scope.prev_index == 1) {
                $scope.platform_price_copy -= +category.show_cost
                $scope.supply_price_copy -= +category.show_quantity*category.purchase_price_decoration_company
            } else {
                $scope.platform_price_copy -= +category.cost
                $scope.supply_price_copy -= +category.quantity*category.purchase_price_decoration_company
            }
            $scope.platform_price_copy += good.platform_price * $scope.add_quantity
            $scope.supply_price_copy += good.purchase_price_decoration_company * $scope.add_quantity
            console.log($scope.stair)
            console.log($scope.level)
            let goTitle = ''
            if ($scope.prev_index == 1) {
                goTitle = "other"
            } else if ($scope.prev_index == 2) {
                goTitle = 'main'
            }
            $state.go(goTitle, {
                stair: $scope.stair,
                stair_copy: $scope.stair_copy,
                index: $scope.index,
                level: $scope.level,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                'platform_price_copy':$scope.platform_price_copy,
                'supply_price_copy':$scope.supply_price_copy,
                level_copy: $scope.level_copy,
                worker_category: $scope.worker_category,
                handyman_price: $scope.handyman_price,
                area: $scope.area,
                series_index: $scope.series_index,
                style_index: $scope.style_index,
                labor_price: $scope.labor_price,
                house_bedroom: $scope.house_bedroom,
                house_hall: $scope.house_hall,
                house_kitchen: $scope.house_kitchen,
                house_toilet: $scope.house_toilet,
                highCrtl: $scope.highCrtl,
                window: $scope.window,
                choose_stairs: $scope.choose_stairs,
                'cur_stair': $scope.cur_stair,
                'twelve_dismantle': $scope.twelve_dismantle,
                'twenty_four_dismantle': $scope.twenty_four_dismantle,
                'repair': $scope.repair,
                'twelve_new_construction': $scope.twelve_new_construction,
                'twenty_four_new_construction': $scope.twenty_four_new_construction,
                'building_scrap': $scope.building_scrap
            })
        }
    })
    .controller('main_ctrl', function ($scope, $stateParams, $state) {//主要材料控制器
        console.log($stateParams)
        //获取传递的数据(固定)
        $scope.stair = $stateParams.stair
        $scope.level = $stateParams.level
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.platform_price_copy = $stateParams.platform_price_copy
        $scope.supply_price_copy = $stateParams.supply_price_copy
        $scope.stair_copy = angular.copy($stateParams.stair_copy)
        $scope.level_copy = angular.copy($stateParams.level_copy)
        $scope.index = $stateParams.index
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.house_bedroom = $stateParams.house_bedroom
        $scope.house_hall = $stateParams.house_hall
        $scope.house_kitchen = $stateParams.house_kitchen
        $scope.house_toilet = $stateParams.house_toilet
        $scope.labor_price = $stateParams.labor_price
        $scope.series_index = $stateParams.series_index
        $scope.style_index = $stateParams.style_index
        $scope.area = $stateParams.area
        $scope.window = $stateParams.window
        $scope.worker_category = $stateParams.worker_category
        $scope.highCrtl = $stateParams.highCrtl
        $scope.choose_stairs = $stateParams.choose_stairs
        $scope.cur_stair = $stateParams.cur_stair
        //杂工数据
        $scope.twelve_dismantle = $stateParams.twelve_dismantle || ''
        $scope.twenty_four_dismantle = $stateParams.twenty_four_dismantle || ''
        $scope.repair = $stateParams.repair || ''
        $scope.twelve_new_construction = $stateParams.twelve_new_construction || ''
        $scope.twenty_four_new_construction = $stateParams.twenty_four_new_construction || ''
        $scope.building_scrap = $stateParams.building_scrap || false
        $scope.goModalData = function (item) {
            $scope.modalData = item
            console.log($scope.modalData)
        }
        //更换商品
        $scope.replace_material = function (item) {
            $state.go('commodity_details', {
                'isBack': true,
                'level': $scope.level,
                'stair': $scope.stair,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                'level_copy': $scope.level_copy,
                'stair_copy': $scope.stair_copy,
                'labor_price': $scope.labor_price,
                'series_index': $scope.series_index,
                'style_index': $scope.style_index,
                'cur_stair': $scope.cur_stair,
                'worker_category': $scope.worker_category,
                'house_bedroom': $scope.house_bedroom,
                'house_hall': $scope.house_hall,
                'house_kitchen': $scope.house_kitchen,
                'house_toilet': $scope.house_toilet,
                'highCrtl': $scope.highCrtl,
                'area': $scope.area,
                'window': $scope.window,
                'index': $scope.index,
                'choose_stairs': $scope.choose_stairs,
                'excluded_item': item,
                'prev_index': 2,
                'twelve_dismantle': $scope.twelve_dismantle,
                'twenty_four_dismantle': $scope.twenty_four_dismantle,
                'repair': $scope.repair,
                'twelve_new_construction': $scope.twelve_new_construction,
                'twenty_four_new_construction': $scope.twenty_four_new_construction,
                'building_scrap': $scope.building_scrap
            })
        }
        //保存
        $scope.change_material = function () {
            $scope.stair = $scope.stair_copy
            $scope.level = $scope.level_copy
            $scope.platform_price = $scope.platform_price_copy
            $scope.supply_price = $scope.supply_price_copy
            $state.go('nodata', {
                'isBack': true,
                'level': $scope.level,
                'stair': $scope.stair,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                'level_copy': $scope.level_copy,
                'stair_copy': $scope.stair_copy,
                'labor_price': $scope.labor_price,
                'series_index': $scope.series_index,
                'style_index': $scope.style_index,
                'worker_category': $scope.worker_category,
                'house_bedroom': $scope.house_bedroom,
                'house_hall': $scope.house_hall,
                'house_kitchen': $scope.house_kitchen,
                'house_toilet': $scope.house_toilet,
                'highCrtl': $scope.highCrtl,
                'area': $scope.area,
                'cur_stair': $scope.cur_stair,
                'window': $scope.window,
                'choose_stairs': $scope.choose_stairs,
                'twelve_dismantle': $scope.twelve_dismantle,
                'twenty_four_dismantle': $scope.twenty_four_dismantle,
                'repair': $scope.repair,
                'twelve_new_construction': $scope.twelve_new_construction,
                'twenty_four_new_construction': $scope.twenty_four_new_construction,
                'building_scrap': $scope.building_scrap
            })
        }
        //返回上一页
        $scope.curGoPrev = function () {
            console.log($stateParams.stair)
            $state.go('nodata', {
                    'isBack': true,
                    'level': $stateParams.level,
                    'stair': $stateParams.stair,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                    'level_copy': $stateParams.level,
                    'stair_copy': $stateParams.stair,
                    'labor_price': $scope.labor_price,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'worker_category': $scope.worker_category,
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'cur_stair': $scope.cur_stair,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'area': $scope.area,
                    'window': $scope.window,
                    'choose_stairs': $scope.choose_stairs,
                    'twelve_dismantle': $scope.twelve_dismantle,
                    'twenty_four_dismantle': $scope.twenty_four_dismantle,
                    'repair': $scope.repair,
                    'twelve_new_construction': $scope.twelve_new_construction,
                    'twenty_four_new_construction': $scope.twenty_four_new_construction,
                    'building_scrap': $scope.building_scrap
                }
            )
        }
    })
