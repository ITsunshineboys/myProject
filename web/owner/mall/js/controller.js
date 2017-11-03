angular.module("all_controller", [])
.controller("cell_search_ctrl", function ($scope, $http) {//小区搜索控制器
    $scope.data = ''
    // $scope.search_data = ''
    let arr = []
    let url = "http://test.cdlhzz.cn:888/owner/search"

    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    }
    $scope.getData = function () {
        $scope.search_data = ''
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
                $state.go("model_room", {
                    name: '今日花园',
                    address: '四川省成都市郫县高新西区泰山大道',
                    pic_one: '101135',
                    pic_two: '125280'
                })
            } else if (item == "花好月圆") {
                $state.go("model_room", {name: '花好月圆', address: '四川省成都市蜀汉路东89号', pic_one: '116688', pic_two: '138280'})
            }
            else if (item == "蓝光COCO时代") {
                $state.go("model_room", {
                    name: '蓝光COCO时代',
                    address: '四川省成都市青羊区清百路110号',
                    pic_one: '168135',
                    pic_two: '185280'
                })
            }
        }

    })
    .controller("intelligent_nodata_ctrl", function ($timeout, $scope, $stateParams, $http, $state, $location, $anchorScroll) { //无数据控制器
        // let all_url = 'http://test.cdlhzz.cn:888'
        let all_url = ""
        $scope.btn_msg = '生成3D/VR图和材料'
        console.log($stateParams)
        $scope.message = ''
        $scope.cur_labor = $stateParams.cur_labor || ''
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.nowStyle = '现代简约'
        $scope.nowStairs = $stateParams.cur_stair || '实木构造'
        $scope.nowSeries = '齐家'
        $scope.index = $stateParams.index || ''
        $scope.area = $stateParams.area || ''
        $scope.cur_labor = $stateParams.cur_labor || ''
        $scope.series_index = $stateParams.series_index || 0//系列编号
        $scope.style_index = $stateParams.style_index || 0//风格编号
        $scope.window = $stateParams.window || 0
        $scope.labor_price = $stateParams.labor_price || 0//工人总费用
        $scope.labor_category = $stateParams.worker_category || {}//工人详细费用
        $scope.toponymy = $stateParams.toponymy || ''
        $scope.choose_stairs = $stateParams.choose_stairs || false;//楼梯选择
        console.log($scope.choose_stairs)
        $scope.stair = $stateParams.stair//默认一级传递值
        $scope.level = $stateParams.level//默认二级传递值
        $scope.isClick = $stateParams.isBack || false
        $scope.handyman_price = $stateParams.worker_category['杂工'] || 0
        if ($stateParams.index !== '') {
            $anchorScroll.yOffset = 150
            console.log($scope.stair[$stateParams.index].id)
            $location.hash('bottom' + $scope.stair[$stateParams.index].id)
            $anchorScroll()
        }
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
                    if (+value.path.split(',')[0] == 1 || +value.path.split(',')[0] == 43) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else if (+value.path.split(',')[0] == 93 || +value.path.split(',')[0] == 102) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else if (+value.path.split(',')[0] == 144) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                    }
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
                    if (+value.path.split(',')[0] == 1 || +value.path.split(',')[0] == 43) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else if (+value.path.split(',')[0] == 93 || +value.path.split(',')[0] == 102) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else if (+value.path.split(',')[0] == 144) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                    }
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
                    if (+value.path.split(',')[0] == 1 || +value.path.split(',')[0] == 43) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else if (+value.path.split(',')[0] == 93 || +value.path.split(',')[0] == 102) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else if (+value.path.split(',')[0] == 144) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                    }
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
                let carpentry = response.data.data.waterproof_material[0]
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
                if (+carpentry.path.split(',')[0] == 1 || +carpentry.path.split(',')[0] == 43) {
                    $scope.supply_price += carpentry.purchase_price_decoration_company * carpentry.quantity / 1
                } else if (+value.path.split(',')[0] == 93 || +value.path.split(',')[0] == 102) {
                    $scope.supply_price += carpentry.purchase_price_decoration_company * carpentry.quantity / 1
                } else if (+value.path.split(',')[0] == 144) {
                    $scope.supply_price += carpentry.purchase_price_decoration_company * carpentry.quantity / 1
                } else {
                    $scope.supply_price += carpentry.purchase_price_decoration_company * carpentry.quantity
                }
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
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
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
                    if (value.path.split(',')[1] == 1) {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    } else {
                        $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
                    }

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
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity / 1
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
                let material_arr = []
                for (let [key, value] of  response.data.data.goods.entries()) {
                    if (value != null) {
                        material_arr.push(value)
                    }
                }
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
                let assort_arr = []
                for (let [key, value] of  response.data.data.goods.entries()) {
                    if (value != null) {
                        assort_arr.push(value)
                    }
                }
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
                let life_arr = []
                for (let [key, value] of  response.data.data.goods.entries()) {
                    if (value != null) {
                        life_arr.push(value)
                    }
                }
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
                let intelligence_arr = []
                for (let [key, value] of  response.data.data.goods.entries()) {
                    if (value != null) {
                        intelligence_arr.push(value)
                    }
                }
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
            $scope.btn_msg = '正在拼命计算中...'
            let a = $timeout(function () {
                $scope.isClick = true
                $scope.btn_msg = '生成3D/VR图和材料'
            }, 500)
            console.log($scope.isClick)
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
                    'cur_labor': $scope.cur_labor,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'level': $scope.level,
                    'stair_copy': angular.copy($scope.stair),
                    'level_copy': angular.copy($scope.level),
                    'supply_price_copy': angular.copy($scope.supply_price),
                    'platform_price_copy': angular.copy($scope.platform_price),
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
                    'cur_labor': $scope.cur_labor,
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
                    'cur_labor': $scope.cur_labor,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'supply_price_copy': angular.copy($scope.supply_price),
                    'platform_price_copy': angular.copy($scope.platform_price),
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
                    'cur_labor': $scope.cur_labor,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'supply_price_copy': angular.copy($scope.supply_price),
                    'platform_price_copy': angular.copy($scope.platform_price),
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
        $scope.cur_labor = $stateParams.cur_labor || ''
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
        // let url = 'http://test.cdlhzz.cn:888/owner/handyman'
        let url = '/owner/handyman'
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
                    'index': $scope.index,
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
                    'cur_labor': $scope.cur_labor,
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
            console.log($scope.repair)
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
                //平台价格处理
                for (let [key, value] of other_arr.entries()) {
                    $scope.platform_price += value.cost
                }
                if (!!$scope.cur_labor) {
                    for (let [key, value] of $scope.cur_labor.entries()) {
                        $scope.platform_price -= value.cost
                    }
                }
                //装修公司供货价处理
                for (let [key, value] of other_arr.entries()) {
                    $scope.supply_price += value.purchase_price_decoration_company * value.quantity
                }
                if (!!$scope.cur_labor) {
                    for (let [key, value] of $scope.cur_labor.entries()) {
                        $scope.supply_price -= value.purchase_price_decoration_company * value.quantity
                    }
                }
                //杂工一级二级三级处理
                if (!!$scope.cur_labor) {
                    for (let [key, value] of $scope.cur_labor.entries()) {
                        for (let [key1, value1] of $scope.level_copy.entries()) {
                            if (value.path.split(',')[1] == value1.id) {
                                value1.cost -= value.cost
                                value1[value.path.split(',')[2]].cost -= value.cost
                                value1[value.path.split(',')[2]][value.id].cost -= value.cost
                                value1[value.path.split(',')[2]][value.id].quantity -= value.quantity
                            }
                        }
                    }
                    for (let [key, value] of $scope.cur_labor.entries()) {
                        for (let [key1, value1] of $scope.stair_copy.entries()) {
                            for (let [key2, value2] of $scope.level_copy.entries()) {
                                if (value.path.split(',')[0] == value1.id && value.path.split(',')[1] == value2.id) {
                                    value1[value2.id] = value2
                                    value1.cost -= value.cost
                                }
                            }
                        }
                    }
                    for (let [key, value] of other_arr.entries()) {
                        for (let [key1, value1] of $scope.level_copy.entries()) {
                            if (value.path.split(',')[1] == value1.id) {
                                value1.cost += value.cost
                                if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                    value1.three_level.push(value.path.split(',')[2])
                                }
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'cost': value.cost, 'id': value.path.split(',')[2],
                                        'goods_detail': [value.id], 'title': value.title
                                    }
                                } else {
                                    value1[value.path.split(',')[2]].cost += value.cost
                                }
                                if (!value1[value.path.split(',')[2]][value.id]) {
                                    value1[value.path.split(',')[2]][value.id] = value
                                } else {
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
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
                                    value1[value2.id] = value2
                                    value1.cost += value.cost
                                }
                            }
                        }
                    }
                } else {
                    for (let [key, value] of other_arr.entries()) {
                        for (let [key1, value1] of $scope.level_copy.entries()) {
                            if (value.path.split(',')[1] == value1.id) {
                                value1.cost += value.cost
                                if (value1.three_level.indexOf(value.path.split(',')[2]) == -1) {
                                    value1.three_level.push(value.path.split(',')[2])
                                }
                                if (!value1[value.path.split(',')[2]]) {
                                    value1[value.path.split(',')[2]] = {
                                        'cost': value.cost, 'id': value.path.split(',')[2],
                                        'goods_detail': [value.id], 'title': value.title
                                    }
                                } else {
                                    value1[value.path.split(',')[2]].cost += value.cost
                                    // value1[value.path.split(',')[2]].three_level += value.cost
                                }
                                if (!value1[value.path.split(',')[2]][value.id]) {
                                    value1[value.path.split(',')[2]][value.id] = value
                                } else {
                                    value1[value.path.split(',')[2]][value.id].quantity += value.quantity
                                    value1[value.path.split(',')[2]][value.id].cost += value.cost
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
                                    value1[value2.id] = value2
                                    value1.cost += value.cost
                                }
                            }
                        }
                    }
                }
                console.log($scope.stair_copy)
                console.log($scope.level_copy)
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
                    'index': $scope.index,
                    'labor_price': $scope.labor_price,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'cur_labor': $scope.cur_labor,
                    'worker_category': $scope.worker_category,
                    'house_bedroom': $scope.house_bedroom,
                    'house_hall': $scope.house_hall,
                    'house_kitchen': $scope.house_kitchen,
                    'house_toilet': $scope.house_toilet,
                    'highCrtl': $scope.highCrtl,
                    'area': $scope.area,
                    'cur_labor': other_arr,
                    'choose_stairs': $scope.choose_stairs,
                    'window': $scope.window,
                    'cur_stair': $scope.cur_stair,
                    'twelve_dismantle': $scope.complete ? $scope.twelve_dismantle : '',
                    'twenty_four_dismantle': $scope.complete1 ? $scope.twenty_four_dismantle : '',
                    'repair': $scope.complete2 ? $scope.repair : '',
                    'twelve_new_construction': $scope.complete3 ? $scope.twelve_new_construction : '',
                    'twenty_four_new_construction': $scope.complete4 ? $scope.twenty_four_new_construction : '',
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
        $scope.cur_labor = $stateParams.cur_labor || ''
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
                    'index': $scope.index,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'level_copy': $stateParams.level,
                    'stair_copy': $stateParams.stair,
                    'labor_price': $scope.labor_price,
                    'series_index': $scope.series_index,
                    'style_index': $scope.style_index,
                    'cur_labor': $scope.cur_labor,
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
            if ($scope.platform_price_copy != 0 && $scope.supply_price_copy != 0) {
                $scope.platform_price = $scope.platform_price_copy
                $scope.supply_price = $scope.supply_price_copy
            }
            $state.go('nodata', {
                'isBack': true,
                'level': $scope.level,
                'stair': $scope.stair,
                'index': $scope.index,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                'level_copy': $scope.level_copy,
                'stair_copy': $scope.stair_copy,
                'labor_price': $scope.labor_price,
                'series_index': $scope.series_index,
                'style_index': $scope.style_index,
                'worker_category': $scope.worker_category,
                'house_bedroom': $scope.house_bedroom,
                'cur_labor': $scope.cur_labor,
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
                'cur_labor': $scope.cur_labor,
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
                'cur_labor': $scope.cur_labor,
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
            for (let [key, value] of $scope.level_copy.entries()) {
                if (item.path.split(',')[1] == value.id) {
                    value.cost -= item.show_cost
                    value[item.path.split(',')[2]].cost -= item.show_cost
                    value[item.path.split(',')[2]].goods_detail.splice(value[item.path.split(',')[2]]
                        .goods_detail.indexOf(item.id), 1)
                    delete value[item.path.split(',')[2]][item.id]
                    $scope.platform_price_copy -= item.show_cost
                    $scope.supply_price_copy -= item.show_quantity * item.purchase_price_decoration_company
                }
            }
            for (let [key, value] of $scope.stair_copy.entries()) {
                for (let [key1, value1] of $scope.level_copy.entries()) {
                    if (item.path.split(',')[1] == value1.id && item.path.split(',')[0] == value.id) {
                        value.cost -= item.show_cost
                        value[value1.id] = value1
                        value.goods_count--
                    }
                }
            }
            console.log($scope.stair)
        }
    })
    .controller('second_level_material_ctrl', function ($scope, $http, $stateParams, $state) {//添加二级分类选择控制器
        console.log($stateParams)
        //获取传递的数据(固定)
        $scope.stair = $stateParams.stair
        $scope.level = $stateParams.level
        $scope.cur_labor = $stateParams.cur_labor || ''
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
        $http.get('/mall/categories-level3?pid=' + pid).then(function (response) {
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
                    'cur_labor': $scope.cur_labor,
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
        $scope.cur_labor = $stateParams.cur_labor || ''
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
        $scope.three_material = $stateParams.three_material || ''//三级单项传递
        $scope.excluded_item = $stateParams.excluded_item
        $scope.prev_index = $stateParams.prev_index
        $scope.pid = $stateParams.pid || +$stateParams.excluded_item.path.split(',')[2] || ''//传递选择的三级id
        $scope.cur_three_title = ''
        if (!$stateParams.three_material) {
            for (let [key, value] of $scope.level.entries()) {
                if ($scope.excluded_item.path.split(',')[1] == value.id) {
                    $scope.cur_three_title = value.title
                }
            }
        } else {
            $scope.cur_three_title = $stateParams.three_material.title
        }
        //杂工数据
        $scope.twelve_dismantle = $stateParams.twelve_dismantle || ''
        $scope.twenty_four_dismantle = $stateParams.twenty_four_dismantle || ''
        $scope.repair = $stateParams.repair || ''
        $scope.twelve_new_construction = $stateParams.twelve_new_construction || ''
        $scope.twenty_four_new_construction = $stateParams.twenty_four_new_construction || ''
        $scope.building_scrap = $stateParams.building_scrap || false
        //获取指定id三级下面详细商品
        $http.get('/mall/category-goods?category_id=' + $scope.pid).then(function (response) {
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
                    'cur_labor': $scope.cur_labor,
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
                    'cur_labor': $scope.cur_labor,
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
                    'cur_labor': $scope.cur_labor,
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
                'cur_labor': $scope.cur_labor,
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
        $scope.cur_labor = $stateParams.cur_labor || ''
        $scope.level = $stateParams.level
        $scope.platform_price = $stateParams.platform_price || 0 //平台价格
        $scope.supply_price = $stateParams.supply_price || 0//装修公司供货价
        $scope.platform_price_copy = angular.copy($stateParams.platform_price)//平台价格
        $scope.supply_price_copy = angular.copy($stateParams.supply_price)//装修公司供货价
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
        $scope.add_quantity = $scope.excluded_item.show_quantity || $scope.excluded_item.quantity || 1//添加数量
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
        let cur_good = angular.copy($scope.current_good)
        let replace_good = angular.copy($scope.excluded_item)
        let category = angular.copy($scope.three_material)
        $scope.subtract = function () {
            if ($scope.add_quantity <= 1) {
                $scope.add_quantity = 1
            } else {
                $scope.add_quantity--
            }
        }
        $scope.change_tab = function () {
            if ($scope.tab_title == 0) {
                $scope.tab_title = 1
            } else {
                $scope.tab_title = 0
            }
        }
        $scope.add = function () {
            $scope.add_quantity++
        }
        $http.get('/mall/goods-view?id=' + $scope.goods_id).then(function (response) {
            $scope.good_detail = response.data.data['goods-view']
            console.log(response)
        }, function (error) {
            console.log(error)
        })
        $scope.add_goods = function () {
            //整合一级二级三级
            for (let [key, value] of $scope.level_copy.entries()) {
                if ($scope.three_material.path.split(',')[1] == value.id) {
                    value.cost += cur_good.platform_price * $scope.add_quantity
                    value[$scope.three_material.path.split(',')[2]].cost += cur_good.platform_price * $scope.add_quantity
                    value[$scope.three_material.path.split(',')[2]].goods_detail.push(cur_good.id)
                    value[$scope.three_material.path.split(',')[2]][cur_good.id] = cur_good
                    value[$scope.three_material.path.split(',')[2]][cur_good.id].name = '马可波罗'
                    value[$scope.three_material.path.split(',')[2]][cur_good.id].show_quantity = $scope.add_quantity
                    value[$scope.three_material.path.split(',')[2]][cur_good.id].show_cost = cur_good.platform_price * $scope.add_quantity
                    value[$scope.three_material.path.split(',')[2]][cur_good.id].path = $scope.three_material.path
                    $scope.platform_price_copy += cur_good.platform_price * $scope.add_quantity
                    $scope.supply_price_copy += $scope.add_quantity * cur_good.purchase_price_decoration_company
                }
            }
            for (let [key, value] of $scope.stair_copy.entries()) {
                for (let [key1, value1] of $scope.level_copy.entries()) {
                    if ($scope.three_material.path.split(',')[1] == value1.id && $scope.three_material.path.split(',')[0] == value.id) {
                        value.cost += cur_good.platform_price * $scope.add_quantity
                        value[value1.id] = value1
                    }
                }
            }
            $state.go("other", {
                stair: $scope.stair,
                level: $scope.level,
                'cur_labor': $scope.cur_labor,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.platform_price,
                'platform_price_copy': $scope.platform_price_copy,
                'supply_price_copy': $scope.supply_price_copy,
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
            if ($scope.prev_index == 2) {
                for (let [key, value] of $scope.level_copy.entries()) {
                    if (replace_good.path.split(',')[1] == value.id) {
                        value.cost -= replace_good.cost
                        value[replace_good.path.split(',')[2]].cost -= replace_good.cost
                        value[replace_good.path.split(',')[2]].goods_detail.splice(value[replace_good.path.split(',')[2]]
                            .goods_detail.indexOf(replace_good.id), 1)
                        delete value[replace_good.path.split(',')[2]][replace_good.id]
                        value.cost += cur_good.platform_price * $scope.add_quantity
                        value[replace_good.path.split(',')[2]].cost += cur_good.platform_price * $scope.add_quantity
                        value[replace_good.path.split(',')[2]].goods_detail.push(cur_good.id)
                        value[replace_good.path.split(',')[2]][cur_good.id] = cur_good
                        value[replace_good.path.split(',')[2]][cur_good.id].name = '马可波罗'
                        value[replace_good.path.split(',')[2]][cur_good.id].quantity = $scope.add_quantity
                        value[replace_good.path.split(',')[2]][cur_good.id].cost = cur_good.platform_price * $scope.add_quantity
                        value[replace_good.path.split(',')[2]][cur_good.id].path = replace_good.path
                        $scope.platform_price_copy -= replace_good.cost
                        $scope.platform_price_copy += cur_good.platform_price * $scope.add_quantity
                        $scope.supply_price_copy -= replace_good.quantity * replace_good.purchase_price_decoration_company
                        $scope.supply_price_copy += $scope.add_quantity * cur_good.purchase_price_decoration_company
                    }
                }
                for (let [key, value] of $scope.stair_copy.entries()) {
                    for (let [key1, value1] of $scope.level_copy.entries()) {
                        if (replace_good.path.split(',')[1] == value1.id && replace_good.path.split(',')[0] == value.id) {
                            value.cost -= replace_good.cost
                            value.cost += cur_good.platform_price * $scope.add_quantity
                            value[value1.id] = value1
                        }
                    }
                }
            } else {
                for (let [key, value] of $scope.level_copy.entries()) {
                    if (replace_good.path.split(',')[1] == value.id) {
                        value.cost -= replace_good.cost
                        value[replace_good.path.split(',')[2]].cost -= replace_good.cost
                        value[replace_good.path.split(',')[2]].goods_detail.splice(value[replace_good.path.split(',')[2]]
                            .goods_detail.indexOf(replace_good.id), 1)
                        delete value[replace_good.path.split(',')[2]][replace_good.id]
                        value.cost += cur_good.platform_price * $scope.add_quantity
                        value[replace_good.path.split(',')[2]].cost += cur_good.platform_price * $scope.add_quantity
                        value[replace_good.path.split(',')[2]].goods_detail.push(cur_good.id)
                        value[replace_good.path.split(',')[2]][cur_good.id] = cur_good
                        value[replace_good.path.split(',')[2]][cur_good.id].name = '马可波罗'
                        value[replace_good.path.split(',')[2]][cur_good.id].show_quantity = $scope.add_quantity
                        value[replace_good.path.split(',')[2]][cur_good.id].show_cost = cur_good.platform_price * $scope.add_quantity
                        value[replace_good.path.split(',')[2]][cur_good.id].path = replace_good.path
                        $scope.platform_price_copy -= replace_good.show_cost
                        $scope.platform_price_copy += cur_good.platform_price * $scope.add_quantity
                        $scope.supply_price_copy -= replace_good.show_quantity * replace_good.purchase_price_decoration_company
                        $scope.supply_price_copy += $scope.add_quantity * cur_good.purchase_price_decoration_company
                    }
                }
                for (let [key, value] of $scope.stair_copy.entries()) {
                    for (let [key1, value1] of $scope.level_copy.entries()) {
                        if (replace_good.path.split(',')[1] == value1.id && replace_good.path.split(',')[0] == value.id) {
                            value.cost -= replace_good.show_cost
                            value.cost += cur_good.platform_price * $scope.add_quantity
                            value[value1.id] = value1
                        }
                    }
                }
            }
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
                'cur_labor': $scope.cur_labor,
                'platform_price': $scope.platform_price,
                'supply_price': $scope.supply_price,
                'platform_price_copy': $scope.platform_price_copy,
                'supply_price_copy': $scope.supply_price_copy,
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
        $scope.platform_price_copy = angular.copy($stateParams.platform_price_copy) || angular.copy($stateParams.platform_price) || 0
        $scope.supply_price_copy = angular.copy($stateParams.supply_price_copy) || angular.copy($stateParams.supply_price) || 0
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
        $scope.cur_labor = $stateParams.cur_labor
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
                'cur_labor': $scope.cur_labor,
                'index': $scope.index,
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
            if ($scope.platform_price_copy != 0 && $scope.supply_price_copy != 0) {
                $scope.platform_price = $scope.platform_price_copy
                $scope.supply_price = $scope.supply_price_copy
            }
            $state.go('nodata', {
                'isBack': true,
                'level': $scope.level,
                'stair': $scope.stair,
                'cur_labor': $scope.cur_labor,
                'index': $scope.index,
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
                    'cur_labor': $scope.cur_labor,
                    'platform_price': $scope.platform_price,
                    'supply_price': $scope.supply_price,
                    'index': $scope.index,
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


    .controller('nodata_ctrl', function ($scope, $http, $state,$rootScope,$timeout,$stateParams) {
        console.log($stateParams)
        $scope.ctrlScope = $scope
        //post请求配置
        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        }
        $scope.toponymy = ''//小区名称
        $scope.message = ''//小区地址
        $scope.area = 60//房屋面积
        $scope.house_bedroom = 1//室
        $scope.house_hall = 1//厅
        $scope.house_toilet = 1 //卫
        $scope.house_kitchen = 1//厨
        $scope.highCrtl = 2.8//层高
        $scope.window = 0//飘窗
        $scope.choose_stairs = 0 //有无楼梯，默认无楼梯
        $scope.nowStairs = 0//楼梯结构,，默认无楼梯结构
        $scope.have_header = true//有无头部
        $scope.all_goods = []//生成材料
        $scope.all_workers = []//工种以及费用
        $scope.all_workers_cost = 0//工人总费用
        $scope.show_material = false//是否成功生成材料
        $scope.cur_header = '智能报价'
        $scope.cur_second_level = ''//二级名称保存
        $scope.cur_three_level = ''//三级名称保存
        $scope.cur_content = 'modal'//控制编辑状态无法点击弹出模态框
        $scope.is_city = true//是否显示城市定位
        $scope.is_edit = false//是否显示编辑按钮
        $scope.tab_title = 0//商品详情页tab切换
        $scope.cur_status = 0//查看0 更换1 添加2状态
        $scope.cur_title = ''//商品更换或者添加
        $scope.replaced_goods = []//被替换的商品
        $scope.goods_replaced = []//替换的商品
        $scope.all_delete_goods = []//其他材料删除的材料
        $scope.all_add_goods = []//其他材料添加的材料
        $scope.cur_project = 0//0为基础装修 1为主要材料 2是其他材料 3三级分类页
        $scope.cur_operate = '编辑'//其他材料编辑两种状态 编辑/完成
        $scope.is_delete_btn = false //切换编辑状态
        //请求后台数据
        $http.get(url + '/owner/series-and-style').then(function (response) {
            console.log(response)
            $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
            $scope.series = response.data.data.show.series;//系列数据
            $scope.style = response.data.data.show.style;//风格数据
            $scope.cur_series = $scope.series[0]//默认选择第一个系列
            $scope.cur_style = $scope.style[0]//默认选择第一个风格
            // $scope.nowStairs = $scope.stairs_details[0].id//楼梯结构
        }, function (error) {
            console.log(error)
        })
        //风格轮播图
        $scope.$watch('cur_style', function (newVal, oldVal) {
            if (newVal != '' || newVal != undefined) {
                var mySwiper = new Swiper('.swiper-container', {
                    direction: 'horizontal',
                    loop: true,
                    autoplay: 1000,
                    observe: true,
                    observeParents: true,

                    // 如果需要分页器
                    pagination: '.swiper-pagination'
                })
            }
        })
        //切换楼梯结构
        $scope.toggleStairs = function (item) {
            $scope.nowStairs = +item.id
        }
        //切换系列
        $scope.toggleSeries = function (item) {
            $scope.cur_series = item
        }
        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.cur_style = item
        }
        //室厅卫厨操作
        $scope.operate = function (type, is_add, limit, other) {
            console.log(other)
            if (!!is_add) {
                if ($scope[type] == limit) {
                    if (!!other) {
                        $scope[type][other] = limit
                    } else {
                        $scope[type] = limit
                    }
                } else {
                    if (!!other) {
                        $scope[type][other]++
                    } else {
                        $scope[type]++
                    }
                }
            } else {
                if ($scope[type] == limit) {
                    if (!!other) {
                        $scope[type][other] = limit
                    } else {
                        $scope[type] = limit
                    }
                } else {
                    if (!!other) {
                        $scope[type][other]--
                    } else {
                        $scope[type]--
                    }
                }
            }
        }
        //一级、二级分类
        $http.post(url + '/owner/classify', {}, config).then(function (response) {
            console.log(response)
            $scope.stair = response.data.data.pid.stair//一级
            $scope.level = response.data.data.pid.level//二级
        }, function (error) {
            console.log(error)
        })
        /*主页操作*/
        //主页推荐
        $http.get(url + '/owner/homepage').then(function (response) {
            $scope.recommend_list = response.data.data
            console.log(response)
        }, function (error) {
            console.log(error)
        })
        //跳转案例页
        if(Object.keys($stateParams).length!=0){
            $http.get('/owner/case-list', {
                params: {
                    code: $stateParams.item.district_code,
                    street: $stateParams.item.street,
                    toponymy: $stateParams.item.toponymy
                }
            }).then(function (response) {
                console.log(response)
                $scope.toponymy = response.data.data.case_effect.toponymy
                $scope.message = response.data.data.case_effect.city+response.data.data.case_effect.district+response.data.data.case_effect.street
                $scope.highCrtl = response.data.data.case_effect.high
                $scope.window = response.data.data.case_effect.window
                $scope.choose_stairs = response.data.data.case_effect.stairway
                $scope.nowStairs = response.data.data.case_effect.stair_id
                for(let [key,value] of $scope.series.entries()){
                    if(value.id == response.data.data.case_picture.series_id){
                        $scope.cur_series = value
                    }
                }
                for(let [key,value] of $scope.style.entries()){
                    if(value.id == response.data.data.case_picture.style_id){
                        $scope.cur_style = value
                    }
                }
            }, function (error) {
                console.log(error)
            })
            $http.get('/effect/getparticulars').then(function(response){
                console.log(response)
            },function(error){
                console.log(error)
            })
        }

        /*无资料操作*/
        //修改了基础表单数据
        $scope.$watch('toponymy', function (newVal, oldVal) {
            $scope.show_material = false
        })
        // $scope.$watch('message', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        $scope.$watch('area', function (newVal, oldVal) {
            $scope.show_material = false
        })
        $scope.$watch('house_bedroom', function (newVal, oldVal) {
            $scope.show_material = false
        })
        $scope.$watch('house_hall', function (newVal, oldVal) {
            $scope.show_material = false
        })
        $scope.$watch('house_toilet', function (newVal, oldVal) {
            $scope.show_material = false
        })
        $scope.$watch('house_kitchen', function (newVal, oldVal) {
            $scope.show_material = false
        })
        $scope.$watch('highCrtl', function (newVal, oldVal) {
            $scope.show_material = false
        })
        $scope.$watch('window', function (newVal, oldVal) {
            $scope.show_material = false
        })
        $scope.$watch('choose_stairs', function (newVal, oldVal) {
            $scope.show_material = false
        })
        $scope.$watch('nowStairs', function (newVal, oldVal) {
            $scope.show_material = false
        })
        //监听页面是否加载完成操作DOM
        $scope.$on('ngRepeatFinished', function () {
            let $grid = $('.grid')
            console.log($grid)
            let cur_height = [0, 0]
            $grid.each(function () {
                console.log(cur_height)
                let min = parseFloat(cur_height[0]) > parseFloat(cur_height[1]) ? cur_height[1] : cur_height[0]
                let minIndex = cur_height[0] > cur_height[1] ? 1 : 0
                $(this).css({
                    'top': min,
                    'left': minIndex * ($(window).width() * 0.471),
                })
                cur_height[minIndex] += $(this).outerHeight() + 5
                $('.basis_decoration').outerHeight(parseFloat(cur_height[0]) > parseFloat(cur_height[1]) ? cur_height[0] : cur_height[1])
            })
        })
        //跳转内页
        $scope.go_inner = function (item) {
            if (item.title == '辅材') {
                $state.go('nodata.basics_decoration')
                $scope.cur_header = '基础装修'
                $scope.inner_header = '基础装修'
                $scope.cur_project = 0
                $scope.is_city = false
                $scope.is_edit = false
            } else if (item.title == '主要材料') {
                $state.go('nodata.main_material')
                $scope.replaced_goods = []//被替换的商品
                $scope.goods_replaced = []//替换的商品
                $scope.cur_header = '主材料'
                $scope.inner_header = '主材料'
                $scope.cur_project = 1
                $scope.is_city = false
                $scope.is_edit = false
            } else {
                $state.go('nodata.other_material')
                $scope.cur_header = item.title
                $scope.inner_header = item.title
                $scope.replaced_goods = []//被替换的商品
                $scope.goods_replaced = []//替换的商品
                $scope.all_delete_goods = []//其他材料删除的材料
                $scope.all_add_goods = []
                $scope.cur_project = 2
                $scope.is_city = false
                $scope.is_edit = true
            }
            $scope.have_header = true
            $scope.cur_item = item
            $scope.cur_all_goods = angular.copy($scope.all_goods)
        }
        //模态框详情
        $scope.get_basic_details = function (item, three_level_name, three_level_id) {
            $scope.cur_goods_detail = item
            $scope.cur_second_level = $scope.cur_header
            $scope.cur_three_level = three_level_name
            $scope.cur_three_id = three_level_id
        }
        //查看详情
        $scope.go_details = function (item) {
            console.log(item)
            if ($scope.cur_status == 0) {
                $scope.check_goods = $scope.cur_goods_detail
            } else if ($scope.cur_status == 1) {
                $scope.check_goods = item
            } else {
                $scope.check_goods = item
            }
            console.log($scope.check_goods)
            $http.get('/mall/goods-view', {
                params: {
                    id: +$scope.check_goods.id
                }
            }).then(function (response) {
                console.log(response)
                if ($scope.cur_status == 1) {
                    $scope.cur_title = '更换'
                    // $scope.check_goods['shop_name'] = response.data.data.goods_view.supplier.shop_name
                    $scope.check_goods['name'] = response.data.data.goods_view.brand_name
                }else if($scope.cur_status == 2){
                    $scope.cur_title = '添加'
                    $scope.check_goods['name'] = response.data.data.goods_view.brand_name
                }
                $scope.sale_services = response.data.data.goods_view.after_sale_services
                $scope.supplier = response.data.data.goods_view.supplier
                $scope.cur_params = {
                    code: response.data.data.goods_view.sku,
                    title: response.data.data.goods_view.title,
                    attrs: response.data.data.goods_view.attrs,
                    left_number: response.data.data.goods_view.left_number
                }
                $('#myModal').modal('hide')
                $timeout(function () {
                    $scope.have_header = false
                    // $scope.cur_header = ''
                    // $scope.is_city = false
                    // $scope.is_edit = false
                    $state.go('nodata.product_detail')
                }, 300)
                console.log(response)
            }, function (error) {
                console.log(error)
            })
        }
        //监听商品数量输入
        $scope.$watch('check_goods.quantity', function (newVal, oldVal) {
            // onkeypress="return (/[\d]/.test(String.fromCharCode(event.keyCode)))"
            if ($scope.cur_params != undefined) {
                if (newVal === '0' || !(/(^[1-9]{1}\d{0,}$)|(^\s*$)/.test(newVal))) {
                    $scope.check_goods.quantity = 1
                } else if (newVal > $scope.cur_params.left_number) {
                    $scope.check_goods.quantity = +$scope.cur_params.left_number
                }
            }
        })
        //更换商品
        $scope.replace_material = function () {
            $scope.cur_status = 1
            // $scope.cur_project = 1
            $scope.cur_replace_material = []//所有可以替换的商品
            $http.get('/mall/category-goods', {
                params: {
                    category_id: $scope.cur_three_id,
                    style_id: $scope.cur_goods_detail.style_id,
                    series_id: $scope.cur_goods_detail.series_id
                }
            }).then(function (response) {
                console.log(response)
                for (let [key, value] of response.data.data.category_goods.entries()) {
                    $scope.cur_replace_material.push({
                        id: value.id,
                        image: value.cover_image,
                        cost: +value.platform_price,
                        // name: $scope.cur_goods_detail.name,
                        favourable_comment_rate: value.favourable_comment_rate,
                        sold_number: value.sold_number,
                        platform_price: value.platform_price,
                        profit_rate: value.profit_rate,
                        purchase_price_decoration_company: value.purchase_price_decoration_company,
                        quantity: 1,
                        series_id: $scope.cur_goods_detail.series_id,
                        style_id: $scope.cur_goods_detail.style_id,
                        subtitle: value.subtitle,
                        supplier_price: value.supplier_price,
                        title: value.title
                        // shop_name: value.shop_name
                    })
                }
                $('#myModal').modal('hide')
                $timeout(function () {
                    $scope.have_header = true
                    $scope.is_city = false
                    $scope.is_edit = false
                    $scope.cur_header = $scope.cur_three_level
                    $state.go('nodata.all_goods')
                }, 300)
            }, function (error) {
                console.log(error)
            })
        }
        //较正式更换或者添加商品
        $scope.first_replace = function () {
            console.log($scope.check_goods)
            console.log($scope.cur_project)
            $scope.have_header = true
            $scope.cur_header = $scope.cur_second_level
            $scope.is_city = false
            // $scope.cur_goods_detail['three_level'] = $scope.cur_three_id
            // $scope.check_goods['three_level'] = $scope.cur_three_id
            if($scope.cur_status == 1) {//更换
                $scope.check_goods.cost = $scope.check_goods.platform_price*$scope.check_goods.quantity
                $scope.replaced_goods.push($scope.cur_goods_detail)
                $scope.goods_replaced.push($scope.check_goods)
                for (let [key, value] of $scope.all_goods.entries()) {
                    for (let [key1, value1] of value.second_level.entries()) {
                        for (let [key2, value2] of value1.three_level.entries()) {
                            for (let [key3, value3] of value2.goods_detail.entries()) {
                                if (value2.id === $scope.cur_three_id && value3.id === $scope.cur_goods_detail.id) {
                                    value2.goods_detail.splice(key3, 1)
                                    value1.cost += $scope.check_goods.cost - $scope.cur_goods_detail.cost
                                    value.cost += $scope.check_goods.cost - $scope.cur_goods_detail.cost
                                    value2.goods_detail.push({
                                        id: $scope.check_goods.id,
                                        image: $scope.check_goods.cover_image,
                                        cost: $scope.check_goods.platform_price * $scope.check_goods.quantity,
                                        name: $scope.check_goods.name,
                                        platform_price: $scope.check_goods.platform_price,
                                        profit_rate: $scope.check_goods.profit_rate,
                                        purchase_price_decoration_company: $scope.check_goods.purchase_price_decoration_company,
                                        quantity: +$scope.check_goods.quantity,
                                        series_id: $scope.check_goods.series_id,
                                        style_id: $scope.check_goods.style_id,
                                        subtitle: $scope.check_goods.subtitle,
                                        supplier_price: $scope.check_goods.supplier_price,
                                        shop_name: $scope.check_goods.shop_name
                                    })
                                }
                            }
                        }
                    }
                }
            }else if($scope.cur_status == 2){
                console.log($scope.check_goods)
                $scope.all_add_goods.push($scope.check_goods)
                for(let [key,value] of $scope.all_goods.entries()){
                    if(value.id == $scope.cur_item.id){
                        value.cost += $scope.check_goods.platform_price * $scope.check_goods.quantity
                        let second_item = value.second_level.findIndex(function (item) {
                            return item.id == $scope.check_goods.path.split(',')[1]
                        })
                        if(second_item == -1){
                            value.second_level.push({
                                id:$scope.check_goods.path.split(',')[1],
                                three_level:[{
                                    id:$scope.cur_three_id,
                                    title:$scope.cur_three_level,
                                    goods_detail:[{
                                        id: $scope.check_goods.id,
                                        image: $scope.check_goods.cover_image,
                                        cost: $scope.check_goods.platform_price * $scope.check_goods.quantity,
                                        name: $scope.check_goods.name,
                                        platform_price: $scope.check_goods.platform_price,
                                        profit_rate: $scope.check_goods.profit_rate,
                                        purchase_price_decoration_company: $scope.check_goods.purchase_price_decoration_company,
                                        quantity: +$scope.check_goods.quantity,
                                        series_id: $scope.check_goods.series_id,
                                        style_id: $scope.check_goods.style_id,
                                        subtitle: $scope.check_goods.subtitle,
                                        supplier_price: $scope.check_goods.supplier_price,
                                        shop_name: $scope.check_goods.shop_name
                                    }]
                                }]
                            })
                        }else{
                            for(let [key1,value1] of value.second_level.entries()){
                                if(value1.id == $scope.check_goods.path.split(',')[1]){
                                    let three_item = value1.three_level.findIndex(function (item) {
                                        return item.id == $scope.cur_three_id
                                    })
                                    if(three_item == -1){
                                        value1.three_level.push({
                                            id:$scope.cur_three_id,
                                            title:$scope.cur_three_level,
                                            goods_detail:[{
                                                id: $scope.check_goods.id,
                                                image: $scope.check_goods.cover_image,
                                                cost: $scope.check_goods.platform_price * $scope.check_goods.quantity,
                                                name: $scope.check_goods.name,
                                                platform_price: $scope.check_goods.platform_price,
                                                profit_rate: $scope.check_goods.profit_rate,
                                                purchase_price_decoration_company: $scope.check_goods.purchase_price_decoration_company,
                                                quantity: +$scope.check_goods.quantity,
                                                series_id: $scope.check_goods.series_id,
                                                style_id: $scope.check_goods.style_id,
                                                subtitle: $scope.check_goods.subtitle,
                                                supplier_price: $scope.check_goods.supplier_price,
                                                shop_name: $scope.check_goods.shop_name
                                            }]
                                        })
                                    }else{
                                        for(let [key2,value2] of value1.three_level.entries()){
                                            if(value2.id == $scope.cur_three_id){
                                                let goods_item = value2.goods_detail.findIndex(function (item) {
                                                    return item.id == $scope.check_goods.id
                                                })
                                                if(goods_item == -1){
                                                    value2.goods_detail.push({
                                                        id: $scope.check_goods.id,
                                                        image: $scope.check_goods.cover_image,
                                                        cost: $scope.check_goods.platform_price * $scope.check_goods.quantity,
                                                        name: $scope.check_goods.name,
                                                        platform_price: $scope.check_goods.platform_price,
                                                        profit_rate: $scope.check_goods.profit_rate,
                                                        purchase_price_decoration_company: $scope.check_goods.purchase_price_decoration_company,
                                                        quantity: +$scope.check_goods.quantity,
                                                        series_id: $scope.check_goods.series_id,
                                                        style_id: $scope.check_goods.style_id,
                                                        subtitle: $scope.check_goods.subtitle,
                                                        supplier_price: $scope.check_goods.supplier_price,
                                                        shop_name: $scope.check_goods.shop_name
                                                    })
                                                }else{
                                                    value2.goods_detail[goods_item].cost += $scope.check_goods.platform_price * $scope.check_goods.quantity
                                                    value2.goods_detail[goods_item].quantity += +$scope.check_goods.quantity
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $scope.cur_status = 0
                console.log($scope.all_goods)
            }
            if($scope.cur_project == 1){
                $scope.is_edit = false
                $state.go('nodata.main_material')
            }else if ($scope.cur_project == 2){
                $scope.is_edit = true
                $scope.is_delete_btn = false
                $state.go('nodata.other_material')
            }
        }
        //智能报价无资料返回
        $scope.returnPrev = function () {
            console.log($rootScope.curState_name)
            if($rootScope.curState_name == 'nodata.product_detail'){
                $scope.have_header = true
            }else if($rootScope.curState_name == 'nodata.all_goods'){
                if($scope.cur_status == 2){
                    $scope.cur_header = $scope.inner_first_level
                    $rootScope.fromState_name = 'nodata.second_level'
                }else if($scope.cur_project == 2&&$scope.cur_status == 1){
                    $scope.cur_header = $scope.inner_header
                    $scope.is_edit = true
                    $rootScope.fromState_name = 'nodata.other_material'
                }else if($scope.cur_project == 1&&$scope.cur_status == 1){
                    $scope.cur_header = $scope.inner_header
                    $rootScope.fromState_name = 'nodata.main_material'
                }
            }else if($rootScope.curState_name == 'nodata.second_level'){
                $scope.cur_header = $scope.inner_header
                $scope.is_edit = true
                $rootScope.fromState_name = 'nodata.other_material'
            }else if($rootScope.curState_name == 'nodata.main_material'||$rootScope.curState_name == 'nodata.basics_decoration'||$rootScope.curState_name == 'nodata.other_material'){
                $scope.cur_header = '智能报价'
                $scope.is_edit = false
                $scope.is_city = true
                $rootScope.fromState_name = 'nodata.house_list'
            }else if($rootScope.curState_name == 'nodata.house_list'){
                $scope.have_header = false
                $rootScope.fromState_name = 'home'
            }
            $rootScope.goPrev()
        }
        //直接返回
        $scope.returnIntelligent = function () {
           $scope.all_goods = $scope.cur_all_goods
            $state.go('nodata.house_list')
        }
        // 保存返回
        $scope.save = function () {
            console.log($scope.all_goods)
                $scope.have_header = true
                $scope.is_city = true
                $scope.is_edit = false
                $scope.cur_header = '智能报价'
                $state.go('nodata.house_list')
        }
        //切换编辑/完成
        $scope.switch_delete = function () {
            if ($scope.cur_operate == '编辑') {
                $scope.cur_operate = '完成'
                $scope.is_delete_btn = true
                $scope.cur_content = ''
            } else {
                $scope.cur_operate = '编辑'
                $scope.is_delete_btn = false
                $scope.cur_content = 'modal'
            }
        }
        // 删除项
        $scope.delete_item = function (item, cur_id) {
            console.log(item)
            for (let [key, value] of $scope.all_goods.entries()) {
                for (let [key1, value1] of value.second_level.entries()) {
                    for (let [key2, value2] of value1.three_level.entries()) {
                        let cur_index = value2.goods_detail.findIndex(function (m) {
                            return m.id == item.id
                        })
                        console.log(cur_index)
                        if (cur_id == value2.id) {
                            if (cur_index != -1) {
                                value.cost -= item.cost
                                $scope.all_delete_goods.push(item)
                                value2.goods_detail.splice(cur_index, 1)
                            }
                        }
                    }
                }
            }
        }
        //添加一系列操作
        //添加按钮跳转选择三级页面
        $scope.go_three_item = function(){
            $scope.cur_status = 2
            $scope.cur_second_level = $scope.cur_header
            $http.get('/mall/categories-level3',{
                params:{
                    pid:$scope.cur_item.id
                }
            }).then(function(response){
                console.log(response)
                $scope.cur_header = $scope.cur_item.title
                $scope.inner_first_level = $scope.cur_item.title
                $scope.is_city = false
                $scope.is_edit = false
                $scope.all_three_level = response.data.categories_level3
                $state.go('nodata.second_level')
            },function(error){
                console.log(error)
            })
        }
        $scope.go_cur_goods = function (item) {
            $scope.cur_three_level = item.title
            $scope.cur_three_id = item.id
            $http.get('/mall/category-goods', {
                params: {
                    category_id: item.id,
                }
            }).then(function (response) {
                console.log(response)
                $http.get('/mall/category-brands-styles-series',{
                    params:{
                        category_id: item.id,
                    }
                }).then(function(response){
                    console.log(response)
                },function(error){
                    console.log(error)
                })
                $scope.cur_replace_material = []
                for (let [key, value] of response.data.data.category_goods.entries()) {
                    $scope.cur_replace_material.push({
                        id: value.id,
                        image: value.cover_image,
                        cost: +value.platform_price,
                        // name: $scope.cur_goods_detail.name,
                        favourable_comment_rate: value.favourable_comment_rate,
                        sold_number: value.sold_number,
                        platform_price: value.platform_price,
                        profit_rate: value.profit_rate,
                        purchase_price_decoration_company: value.purchase_price_decoration_company,
                        quantity: 1,
                        path:item.path,
                        // series_id: $scope.cur_goods_detail.series_id,
                        // style_id: $scope.cur_goods_detail.style_id,
                        subtitle: value.subtitle,
                        supplier_price: value.supplier_price,
                        title: value.title
                        // shop_name: value.shop_name
                    })
                }
                $('#myModal').modal('hide')
                $timeout(function () {
                    $scope.have_header = true
                    $scope.is_city = false
                    $scope.is_edit = false
                    $scope.cur_header = $scope.cur_three_level || item.title
                    $state.go('nodata.all_goods')
                }, 300)
            }, function (error) {
                console.log(error)
            })
        }
        //无资料计算
        $scope.get_goods = function (valid) {
            if (valid) {
                let data = {
                    bedroom: $scope.house_bedroom,//卧室
                    area: $scope.area,      //面积
                    hall: $scope.house_hall,       //餐厅
                    toilet: $scope.house_toilet,   // 卫生间
                    kitchen: $scope.house_kitchen,  //厨房
                    series: +$scope.cur_series.id,   //系列
                    style: +$scope.cur_style.id,  //风格
                    window: $scope.window,//飘窗
                    high: $scope.highCrtl, //层高
                    province: 510000,   //省编码
                    city: 510100,      // 市编码
                    stairway_id: $scope.choose_stairs,//有无楼梯
                    stairs: $scope.nowStairs//楼梯结构
                }
                let data1 = angular.copy(data)
                //弱电
                $http.post('/owner/weak-current', data, config).then(function (response) {
                    console.log('弱电')
                    console.log(response)
                    //整合一级
                    for (let [key, value] of $scope.stair.entries()) {
                        for (let [key1, value1] of response.data.data.weak_current_material.material.entries()) {
                            let cur_obj = {id: value.id, title: value.title, cost: 0, count: 0, second_level: []}
                            let cur_title = {title: value.title}
                            if (value1.path.split(',')[0] == value.id && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                $scope.all_goods.push(cur_obj)
                            }
                        }
                    }
                    //整合二级
                    for (let [key, value] of $scope.level.entries()) {
                        for (let [key1, value1] of  $scope.all_goods.entries())
                            for (let [key2, value2] of response.data.data.weak_current_material.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: []}
                                let cur_title = {title: value.title}
                                if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                    JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.second_level.push(cur_obj)
                                }
                            }
                    }
                    //整合三级
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of response.data.data.weak_current_material.material.entries()) {
                                let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                let cur_title = {title: value2.title}
                                if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.three_level.push(cur_obj)
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of response.data.data.weak_current_material.material.entries()) {
                                    let cur_obj = {
                                        id: value3.id,
                                        image: value3.cover_image,
                                        cost: +value3.cost,
                                        name: value3.name,
                                        platform_price: value3.platform_price,
                                        profit_rate: value3.profit_rate,
                                        purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                        quantity: +value3.quantity,
                                        series_id: value3.series_id,
                                        style_id: value3.style_id,
                                        subtitle: value3.subtitle,
                                        supplier_price: value3.supplier_price,
                                        shop_name: value3.shop_name
                                    }
                                    let cur_goods = {
                                        id: value3.id,
                                    }
                                    if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                        value3.path.split(',')[2] == value2.id) {
                                        value.cost += value3.cost
                                        value1.cost += value3.cost
                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                            value2.goods_detail.push(cur_obj)
                                            value.count++
                                        } else {
                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                if (value3.id == value4.id) {
                                                    value4.cost += value3.cost
                                                    value4.quantity +=  cur_obj.quantity
                                                    console.log(value4.quantity)
                                                    console.log(typeof value3.quantity)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //工人费用
                    let cur_worker = {worker_kind: response.data.data.weak_current_labor_price.worker_kind}
                    let cur_worker_price = response.data.data.weak_current_labor_price.price
                    if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                            JSON.stringify(cur_worker).length - 1)) == -1) {
                        $scope.all_workers.push({
                            worker_kind: cur_worker.worker_kind,
                            price: cur_worker_price
                        })
                    } else {
                        for (let [key, value] of $scope.all_workers.entries()) {
                            if (cur_worker.worker_kind == value.worker_kind) {
                                value.price += cur_worker_price
                            }
                        }
                    }
                    console.log($scope.all_workers)
                    console.log($scope.all_goods)
                }, function (error) {
                    console.log(error)
                })
                //强电
                $http.post('/owner/strong-current', data, config).then(function (response) {
                    console.log('强电')
                    console.log(response)
                    //整合一级
                    for (let [key, value] of $scope.stair.entries()) {
                        for (let [key1, value1] of response.data.data.strong_current_material.material.entries()) {
                            let cur_obj = {id: value.id, title: value.title, cost: 0, count: 0, second_level: []}
                            let cur_title = {title: value.title}
                            if (value1.path.split(',')[0] == value.id && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                $scope.all_goods.push(cur_obj)
                            }
                        }
                    }
                    //整合二级
                    for (let [key, value] of $scope.level.entries()) {
                        for (let [key1, value1] of  $scope.all_goods.entries())
                            for (let [key2, value2] of response.data.data.strong_current_material.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: []}
                                let cur_title = {title: value.title}
                                if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                    JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.second_level.push(cur_obj)
                                }
                            }
                    }
                    //整合三级
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of response.data.data.strong_current_material.material.entries()) {
                                let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                let cur_title = {title: value2.title}
                                if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.three_level.push(cur_obj)
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of response.data.data.strong_current_material.material.entries()) {
                                    let cur_obj = {
                                        id: value3.id,
                                        image: value3.cover_image,
                                        cost: +value3.cost,
                                        name: value3.name,
                                        platform_price: value3.platform_price,
                                        profit_rate: value3.profit_rate,
                                        purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                        quantity: +value3.quantity,
                                        series_id: value3.series_id,
                                        style_id: value3.style_id,
                                        subtitle: value3.subtitle,
                                        supplier_price: value3.supplier_price,
                                        shop_name: value3.shop_name
                                    }
                                    let cur_goods = {id: value3.id}
                                    if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                        value3.path.split(',')[2] == value2.id) {
                                        value.cost += value3.cost
                                        value1.cost += value3.cost
                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                            value2.goods_detail.push(cur_obj)
                                            value.count++
                                        } else {
                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                if (value3.id == value4.id) {
                                                    value4.cost += value3.cost
                                                    value4.quantity += cur_obj.quantity
                                                    console.log(value4.quantity)
                                                    console.log(typeof value3.quantity)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //工人费用
                    let cur_worker = {worker_kind: response.data.data.strong_current_labor_price.worker_kind}
                    let cur_worker_price = response.data.data.strong_current_labor_price.price
                    if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                            JSON.stringify(cur_worker).length - 1)) == -1) {
                        $scope.all_workers.push({
                            worker_kind: cur_worker.worker_kind,
                            price: cur_worker_price
                        })
                    } else {
                        for (let [key, value] of $scope.all_workers.entries()) {
                            if (cur_worker.worker_kind == value.worker_kind) {
                                value.price += cur_worker_price
                            }
                        }
                    }
                    console.log($scope.all_workers)
                    console.log($scope.all_goods)
                }, function (error) {
                    console.log(error)
                })
                //水路
                $http.post('/owner/waterway', data, config).then(function (response) {
                    console.log('水路')
                    console.log(response)
                    //整合一级
                    for (let [key, value] of $scope.stair.entries()) {
                        for (let [key1, value1] of response.data.data.waterway_material_price.material.entries()) {
                            let cur_obj = {id: value.id, title: value.title, cost: 0, count: 0, second_level: []}
                            let cur_title = {title: value.title}
                            if (value1.path.split(',')[0] == value.id && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                $scope.all_goods.push(cur_obj)
                            }
                        }
                    }
                    //整合二级
                    for (let [key, value] of $scope.level.entries()) {
                        for (let [key1, value1] of  $scope.all_goods.entries())
                            for (let [key2, value2] of response.data.data.waterway_material_price.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: []}
                                let cur_title = {title: value.title}
                                if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                    JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.second_level.push(cur_obj)
                                }
                            }
                    }
                    //整合三级
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of response.data.data.waterway_material_price.material.entries()) {
                                let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                let cur_title = {title: value2.title}
                                if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.three_level.push(cur_obj)
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of response.data.data.waterway_material_price.material.entries()) {
                                    let cur_obj = {
                                        id: value3.id,
                                        image: value3.cover_image,
                                        cost: +value3.cost,
                                        name: value3.name,
                                        platform_price: value3.platform_price,
                                        profit_rate: value3.profit_rate,
                                        purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                        quantity: +value3.quantity,
                                        series_id: value3.series_id,
                                        style_id: value3.style_id,
                                        subtitle: value3.subtitle,
                                        supplier_price: value3.supplier_price,
                                        shop_name: value3.shop_name
                                    }
                                    let cur_goods = {id: value3.id}
                                    if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                        value3.path.split(',')[2] == value2.id) {
                                        value.cost += value3.cost
                                        value1.cost += value3.cost
                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                            value2.goods_detail.push(cur_obj)
                                            value.count++
                                        } else {
                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                if (value3.id == value4.id) {
                                                    value4.cost += value3.cost
                                                    value4.quantity += cur_obj.quantity
                                                    console.log(value4.cost)
                                                    console.log(value3.cost)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //工人费用
                    let cur_worker = {worker_kind: response.data.data.waterway_labor_price.worker_kind}
                    let cur_worker_price = response.data.data.waterway_labor_price.price
                    if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                            JSON.stringify(cur_worker).length - 1)) == -1) {
                        $scope.all_workers.push({
                            worker_kind: cur_worker.worker_kind,
                            price: cur_worker_price
                        })
                    } else {
                        for (let [key, value] of $scope.all_workers.entries()) {
                            if (cur_worker.worker_kind == value.worker_kind) {
                                value.price += cur_worker_price
                            }
                        }
                    }
                    console.log($scope.all_workers)
                    console.log($scope.all_goods)
                }, function (error) {
                    console.log(error)
                })
                //防水
                $http.post('/owner/waterproof', data, config).then(function (response) {
                    console.log('防水')
                    console.log(response)
                    //整合一级
                    for (let [key, value] of $scope.stair.entries()) {
                        for (let [key1, value1] of response.data.data.waterproof_material.material.entries()) {
                            let cur_obj = {id: value.id, title: value.title, cost: 0, count: 0, second_level: []}
                            let cur_title = {title: value.title}
                            if (value1.path.split(',')[0] == value.id && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                $scope.all_goods.push(cur_obj)
                            }
                        }
                    }
                    //整合二级
                    for (let [key, value] of $scope.level.entries()) {
                        for (let [key1, value1] of  $scope.all_goods.entries())
                            for (let [key2, value2] of response.data.data.waterproof_material.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: []}
                                let cur_title = {title: value.title}
                                if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                    JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.second_level.push(cur_obj)
                                }
                            }
                    }
                    //整合三级
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of response.data.data.waterproof_material.material.entries()) {
                                let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                let cur_title = {title: value2.title}
                                if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.three_level.push(cur_obj)
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of response.data.data.waterproof_material.material.entries()) {
                                    let cur_obj = {
                                        id: value3.id,
                                        image: value3.cover_image,
                                        cost: value3.cost,
                                        name: value3.name,
                                        platform_price: value3.platform_price,
                                        profit_rate: value3.profit_rate,
                                        purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                        quantity: value3.quantity,
                                        series_id: value3.series_id,
                                        style_id: value3.style_id,
                                        subtitle: value3.subtitle,
                                        supplier_price: value3.supplier_price,
                                        shop_name: value3.shop_name
                                    }
                                    let cur_goods = {id: value3.id}
                                    if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                        value3.path.split(',')[2] == value2.id) {
                                        value.cost += value3.cost
                                        value1.cost += value3.cost
                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                            value2.goods_detail.push(cur_obj)
                                            value.count++
                                        } else {
                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                if (value3.id == value4.id) {
                                                    value4.cost += value3.cost
                                                    value4.quantity +=  cur_obj.quantity
                                                    console.log(value4.cost)
                                                    console.log(value3.cost)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //工人费用
                    let cur_worker = {worker_kind: response.data.data.waterproof_labor_price.worker_kind}
                    let cur_worker_price = response.data.data.waterproof_labor_price.price
                    if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                            JSON.stringify(cur_worker).length - 1)) == -1) {
                        $scope.all_workers.push({
                            worker_kind: cur_worker.worker_kind,
                            price: cur_worker_price
                        })
                    } else {
                        for (let [key, value] of $scope.all_workers.entries()) {
                            if (cur_worker.worker_kind == value.worker_kind) {
                                value.price += cur_worker_price
                            }
                        }
                    }
                    console.log($scope.all_workers)
                    console.log($scope.all_goods)
                    data1['waterproof_total_area'] = response.data.data.total_area
                    //泥作
                    $http.post('/owner/mud-make', data1, config).then(function (response) {
                        console.log('泥作')
                        console.log(response)
                        //整合一级
                        for (let [key, value] of $scope.stair.entries()) {
                            for (let [key1, value1] of response.data.data.mud_make_material.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, count: 0, second_level: []}
                                let cur_title = {title: value.title}
                                if (value1.path.split(',')[0] == value.id && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    $scope.all_goods.push(cur_obj)
                                }
                            }
                        }
                        //整合二级
                        for (let [key, value] of $scope.level.entries()) {
                            for (let [key1, value1] of  $scope.all_goods.entries())
                                for (let [key2, value2] of response.data.data.mud_make_material.material.entries()) {
                                    let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: []}
                                    let cur_title = {title: value.title}
                                    if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                        JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.second_level.push(cur_obj)
                                    }
                                }
                        }
                        //整合三级
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of response.data.data.mud_make_material.material.entries()) {
                                    let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                    let cur_title = {title: value2.title}
                                    if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                        JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.three_level.push(cur_obj)
                                    }
                                }
                            }
                        }
                        //整合商品
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of value1.three_level.entries()) {
                                    for (let [key3, value3] of response.data.data.mud_make_material.material.entries()) {
                                        let cur_obj = {
                                            id: value3.id,
                                            image: value3.cover_image,
                                            cost: value3.cost,
                                            name: value3.name,
                                            platform_price: value3.platform_price,
                                            profit_rate: value3.profit_rate,
                                            purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                            quantity: value3.quantity,
                                            series_id: value3.series_id,
                                            style_id: value3.style_id,
                                            subtitle: value3.subtitle,
                                            supplier_price: value3.supplier_price,
                                            shop_name: value3.shop_name
                                        }
                                        let cur_goods = {id: value3.id}
                                        if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                            value3.path.split(',')[2] == value2.id) {
                                            value.cost += value3.cost
                                            value1.cost += value3.cost
                                            if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                value2.goods_detail.push(cur_obj)
                                                value.count++
                                            } else {
                                                for (let [key4, value4] of value2.goods_detail.entries()) {
                                                    if (value3.id == value4.id) {
                                                        value4.cost += value3.cost
                                                        value4.quantity +=  cur_obj.quantity
                                                        console.log(value4.cost)
                                                        console.log(value3.cost)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //工人费用
                        let cur_worker = {worker_kind: response.data.data.mud_make_labor_price.worker_kind}
                        let cur_worker_price = response.data.data.mud_make_labor_price.price
                        if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                                JSON.stringify(cur_worker).length - 1)) == -1) {
                            $scope.all_workers.push({
                                worker_kind: cur_worker.worker_kind,
                                price: cur_worker_price
                            })
                        } else {
                            for (let [key, value] of $scope.all_workers.entries()) {
                                if (cur_worker.worker_kind == value.worker_kind) {
                                    value.price += cur_worker_price
                                }
                            }
                        }
                        console.log($scope.all_workers)
                        console.log($scope.all_goods)
                    }, function (error) {
                        console.log(error)
                    })
                }, function (error) {
                    console.log(error)
                })
                //木作
                $http.post('/owner/carpentry', data, config).then(function (response) {
                    console.log('木作')
                    console.log(response)
                    //整合一级
                    for (let [key, value] of $scope.stair.entries()) {
                        for (let [key1, value1] of response.data.data.carpentry_material.material.entries()) {
                            let cur_obj = {id: value.id, title: value.title, cost: 0, count: 0, second_level: []}
                            let cur_title = {title: value.title}
                            if (value1.path.split(',')[0] == value.id && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                $scope.all_goods.push(cur_obj)
                            }
                        }
                    }
                    //整合二级
                    for (let [key, value] of $scope.level.entries()) {
                        for (let [key1, value1] of  $scope.all_goods.entries())
                            for (let [key2, value2] of response.data.data.carpentry_material.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: []}
                                let cur_title = {title: value.title}
                                if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                    JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.second_level.push(cur_obj)
                                }
                            }
                    }
                    //整合三级
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of response.data.data.carpentry_material.material.entries()) {
                                let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                let cur_title = {title: value2.title}
                                if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.three_level.push(cur_obj)
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of response.data.data.carpentry_material.material.entries()) {
                                    let cur_obj = {
                                        id: value3.id,
                                        image: value3.cover_image,
                                        cost: value3.cost,
                                        name: value3.name,
                                        platform_price: value3.platform_price,
                                        profit_rate: value3.profit_rate,
                                        purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                        quantity: value3.quantity,
                                        series_id: value3.series_id,
                                        style_id: value3.style_id,
                                        subtitle: value3.subtitle,
                                        supplier_price: value3.supplier_price,
                                        shop_name: value3.shop_name
                                    }
                                    let cur_goods = {id: value3.id}
                                    if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                        value3.path.split(',')[2] == value2.id) {
                                        value.cost += value3.cost
                                        value1.cost += value3.cost
                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                            value2.goods_detail.push(cur_obj)
                                            value.count++
                                        } else {
                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                if (value3.id == value4.id) {
                                                    value4.cost += value3.cost
                                                    value4.quantity +=  cur_obj.quantity
                                                    console.log(value4.cost)
                                                    console.log(value3.cost)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //工人费用
                    let cur_worker = {worker_kind: response.data.data.carpentry_labor_price.worker_kind}
                    let cur_worker_price = response.data.data.carpentry_labor_price.price
                    if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                            JSON.stringify(cur_worker).length - 1)) == -1) {
                        $scope.all_workers.push({
                            worker_kind: cur_worker.worker_kind,
                            price: cur_worker_price
                        })
                    } else {
                        for (let [key, value] of $scope.all_workers.entries()) {
                            if (cur_worker.worker_kind == value.worker_kind) {
                                value.price += cur_worker_price
                            }
                        }
                    }
                    console.log($scope.all_workers)
                    console.log($scope.all_goods)
                }, function (error) {
                    console.log(error)
                })
                //乳胶漆
                $http.post('/owner/coating', data, config).then(function (response) {
                    console.log('乳胶漆')
                    console.log(response)
                    //整合一级
                    for (let [key, value] of $scope.stair.entries()) {
                        for (let [key1, value1] of response.data.data.coating_material.material.entries()) {
                            let cur_obj = {id: value.id, title: value.title, cost: 0, count: 0, second_level: []}
                            let cur_title = {title: value.title}
                            if (value1.path.split(',')[0] == value.id && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                $scope.all_goods.push(cur_obj)
                            }
                        }
                    }
                    //整合二级
                    for (let [key, value] of $scope.level.entries()) {
                        for (let [key1, value1] of  $scope.all_goods.entries())
                            for (let [key2, value2] of response.data.data.coating_material.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: []}
                                let cur_title = {title: value.title}
                                if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                    JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.second_level.push(cur_obj)
                                }
                            }
                    }
                    //整合三级
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of response.data.data.coating_material.material.entries()) {
                                let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                let cur_title = {title: value2.title}
                                if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.three_level.push(cur_obj)
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of response.data.data.coating_material.material.entries()) {
                                    let cur_obj = {
                                        id: value3.id,
                                        image: value3.cover_image,
                                        cost: value3.cost,
                                        name: value3.name,
                                        platform_price: value3.platform_price,
                                        profit_rate: value3.profit_rate,
                                        purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                        quantity: value3.quantity,
                                        series_id: value3.series_id,
                                        style_id: value3.style_id,
                                        subtitle: value3.subtitle,
                                        supplier_price: value3.supplier_price,
                                        shop_name: value3.shop_name
                                    }
                                    let cur_goods = {id: value3.id}
                                    if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                        value3.path.split(',')[2] == value2.id) {
                                        value.cost += value3.cost
                                        value1.cost += value3.cost
                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                            value2.goods_detail.push(cur_obj)
                                            value.count++
                                        } else {
                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                if (value3.id == value4.id) {
                                                    value4.cost += value3.cost
                                                    value4.quantity +=  cur_obj.quantity
                                                    console.log(value4.cost)
                                                    console.log(value3.cost)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //工人费用
                    let cur_worker = {worker_kind: response.data.data.coating_labor_price.worker_kind}
                    let cur_worker_price = response.data.data.coating_labor_price.price
                    if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                            JSON.stringify(cur_worker).length - 1)) == -1) {
                        $scope.all_workers.push({
                            worker_kind: cur_worker.worker_kind,
                            price: cur_worker_price
                        })
                    } else {
                        for (let [key, value] of $scope.all_workers.entries()) {
                            if (cur_worker.worker_kind == value.worker_kind) {
                                value.price += cur_worker_price
                            }
                        }
                    }
                    console.log($scope.all_workers)
                    console.log($scope.all_goods)
                }, function (error) {
                    console.log(error)
                })
                //主要材料以及其他
                $http.post('/owner/assort-facility', data, config).then(function (response) {
                    console.log('主要材料及其他')
                    console.log(response)
                    for (let [key, value] of response.data.data.goods.entries()) {
                        if (!!value) {
                            //整合一级
                            for (let [key2, value2] of $scope.stair.entries()) {
                                for (let [key1, value1] of value.entries()) {
                                    if (!!value1) {
                                        let cur_obj = {
                                            id: value2.id,
                                            title: value2.title,
                                            cost: 0,
                                            count: 0,
                                            second_level: []
                                        }
                                        let cur_title = {title: value2.title}
                                        if (value1.path.split(',')[0] == value2.id && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                            $scope.all_goods.push(cur_obj)
                                        }
                                    }
                                }
                            }
                            //整合二级
                            for (let [key3, value3] of $scope.level.entries()) {
                                for (let [key1, value1] of  $scope.all_goods.entries())
                                    for (let [key2, value2] of value.entries()) {
                                        if (!!value2) {
                                            let cur_obj = {id: value3.id, title: value3.title, cost: 0, three_level: []}
                                            let cur_title = {title: value3.title}
                                            if (value2.path.split(',')[1] == value3.id && value2.path.split(',')[0] == value1.id &&
                                                JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                                value1.second_level.push(cur_obj)
                                            }
                                        }
                                    }
                            }
                            //整合三级
                            for (let [key3, value3] of  $scope.all_goods.entries()) {
                                for (let [key1, value1] of value3.second_level.entries()) {
                                    for (let [key2, value2] of value.entries()) {
                                        if (!!value2) {
                                            let cur_obj = {
                                                id: value2.path.split(',')[2],
                                                title: value2.title,
                                                goods_detail: []
                                            }
                                            let cur_title = {title: value2.title}
                                            if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value3.id &&
                                                JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                                value1.three_level.push(cur_obj)
                                            }
                                        }
                                    }
                                }
                            }
                            console.log(1111)
                            //整合商品
                            for (let [key5, value5] of  $scope.all_goods.entries()) {
                                for (let [key1, value1] of value5.second_level.entries()) {
                                    for (let [key2, value2] of value1.three_level.entries()) {
                                        for (let [key3, value3] of value.entries()) {
                                            if (!!value3) {
                                                let cur_obj = {
                                                    id: value3.id,
                                                    image: value3.cover_image,
                                                    cost: value3.cost,
                                                    name: value3.name,
                                                    platform_price: value3.platform_price,
                                                    profit_rate: value3.profit_rate,
                                                    purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                                    quantity: value3.quantity,
                                                    series_id: value3.series_id,
                                                    style_id: value3.style_id,
                                                    subtitle: value3.subtitle,
                                                    supplier_price: value3.supplier_price,
                                                    shop_name: value3.shop_name
                                                }
                                                let cur_goods = {id: value3.id}
                                                if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value5.id &&
                                                    value3.path.split(',')[2] == value2.id) {
                                                    value5.cost += value3.cost
                                                    value1.cost += value3.cost
                                                    if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                        value2.goods_detail.push(cur_obj)
                                                        value5.count++
                                                    } else {
                                                        for (let [key4, value4] of value2.goods_detail.entries()) {
                                                            if (value3.id == value4.id) {
                                                                value4.cost += value3.cost
                                                                value4.quantity +=  cur_obj.quantity
                                                                console.log(value4.cost)
                                                                console.log(value3.cost)
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }, function (error) {
                    console.log(error)
                })
                $scope.show_material = true
            } else {
                $scope.submitted = true
            }
        }
        //跳转搜索页面
        $scope.go_search = function () {
            $state.go('nodata.cell_search')
            $scope.have_header = false
            $scope.cur_toponymy = $scope.toponymy
            console.log($scope.toponymy)
        }

        /*小区搜索*/
        //修改搜索小区字段实时请求小区数据
        $scope.$watch('toponymy', function (newVal, oldVal) {
            console.log(newVal)
            if (newVal != '') {
                $http.post('/owner/search', {
                    str: newVal
                }, config).then(function (response) {
                    console.log(response)
                    $scope.cur_all_house = response.data.data.list_effect
                    $scope.$watch('cur_all_house', function (newVal, oldVal) {
                        $scope.search_data = []//搜索出的小区
                        for (let [key, value] of response.data.data.list_effect.entries()) {
                            $scope.search_data.push({
                                id: value.id,
                                toponymy: value.toponymy,
                                site_particulars: value.site_particulars
                            })
                        }
                    })
                }, function (error) {
                    console.log(error)
                })
            } else {
                $scope.search_data = []
            }
        })
        //取消返回
        $scope.cancel = function () {
            $state.go('nodata.house_list')
            $scope.toponymy = $scope.cur_toponymy
            $scope.have_header = true
        }
        // 跳转到无资料
        $scope.go_nodata = function () {
            $state.go('nodata.house_list')
            $scope.have_header = true
            $scope.cur_header = '智能报价'
            $scope.is_city = true
            $scope.is_edit = false
        }

        /*基础装修内页*/
        //杂工选项
        $scope.twelve_dismantle = ''//12墙拆除
        $scope.twenty_four_dismantle = ''//24墙拆除
        $scope.repair = ''//补烂
        $scope.twelve_new_construction = ''//12墙新建
        $scope.twenty_four_new_construction = ''//24墙新建
        $scope.building_scrap = false//有无建渣点
        //请求杂工数据
        $scope.go_handyman_options = function () {
            //清理杂项原始数据
            if ($scope.cur_goods != undefined && $scope.cur_worker != undefined) {
                for (let [key, value] of $scope.all_goods.entries()) {
                    for (let [key1, value1] of value.second_level.entries()) {
                        for (let [key2, value2] of value1.three_level.entries()) {
                            for (let [key3, value3] of value2.goods_detail.entries()) {
                                for (let [key4, value4] of $scope.cur_goods.entries()) {
                                    if (value4.path.split(',')[0] == value.id && value4.path.split(',')[1] == value1.id && value4.path.split(',')[2]
                                        == value2.id && value4.id == value3.id) {
                                        value3.quantity -= value4.quantity
                                        value3.cost -= value4.cost
                                        value1.cost -= value4.cost
                                        value.cost -= value4.cost
                                        if (value3.cost == 0) {
                                            value2.goods_detail.splice(key3, 1)
                                        }
                                        if (value2.goods_detail.length == 0) {
                                            value1.three_level.splice(key2, 1)
                                        }
                                        if (value1.three_level.length == 0) {
                                            value.second_level.splice(key1, 1)
                                        }
                                    }
                                }
                                for (let [key, value] of $scope.all_workers.entries()) {
                                    if (value.worker_kind == $scope.cur_worker.worker_kind) {
                                        value.price -= $scope.cur_worker.price
                                    }
                                    if (value.price == 0) {
                                        $scope.all_workers.splice(key, 1)
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //保存并请求杂项数据
            if (!$scope.twelve_dismantle && !$scope.twenty_four_dismantle && !$scope.repair &&
                !$scope.twelve_new_construction && !$scope.twenty_four_new_construction) {
                console.log($scope.all_goods)
                console.log($scope.all_workers)
                $scope.cur_header = '智能报价'
                $state.go('nodata.house_list')
            } else {
                $http.post('/owner/handyman', {
                    'province': 510000,
                    'city': 510100,
                    '12_dismantle': +$scope.twelve_dismantle || 0,
                    '24_dismantle': +$scope.twenty_four_dismantle || 0,
                    'repair': +$scope.repair || 0,
                    '12_new_construction': +$scope.twelve_new_construction || 0,
                    '24_new_construction': +$scope.twenty_four_new_construction || 0,
                    'building_scrap': $scope.building_scrap,
                    'area': $scope.area,
                    'series': $scope.cur_series.id,
                    'style': $scope.cur_style.id,
                }, config).then(function (response) {
                    console.log('杂工')
                    console.log(response)
                    $scope.cur_goods = response.data.data.total_material.material
                    $scope.cur_worker = response.data.data.labor_cost
                    //整合一级
                    for (let [key, value] of $scope.stair.entries()) {
                        for (let [key1, value1] of response.data.data.total_material.material.entries()) {
                            let cur_obj = {id: value.id, title: value.title, cost: 0, second_level: []}
                            let cur_title = {title: value.title}
                            console.log(value1.cost)
                            if (value1.path.split(',')[0] == value.id && value1.cost != 0 && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                $scope.all_goods.push(cur_obj)
                            }
                        }
                    }
                    //整合二级
                    for (let [key, value] of $scope.level.entries()) {
                        for (let [key1, value1] of  $scope.all_goods.entries())
                            for (let [key2, value2] of response.data.data.total_material.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: []}
                                let cur_title = {title: value.title}
                                if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id && value2.cost != 0 &&
                                    JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.second_level.push(cur_obj)
                                }
                            }
                    }
                    //整合三级
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of response.data.data.total_material.material.entries()) {
                                let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                let cur_title = {title: value2.title}
                                if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id && value2.cost != 0 &&
                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.three_level.push(cur_obj)
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of response.data.data.total_material.material.entries()) {
                                    let cur_obj = {
                                        id: value3.id,
                                        image: value3.cover_image,
                                        cost: value3.cost,
                                        name: value3.name,
                                        platform_price: value3.platform_price,
                                        profit_rate: value3.profit_rate,
                                        purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                        quantity: value3.quantity,
                                        series_id: value3.series_id,
                                        style_id: value3.style_id,
                                        subtitle: value3.subtitle,
                                        supplier_price: value3.supplier_price,
                                        shop_name: value3.shop_name
                                    }
                                    let cur_goods = {
                                        id: value3.id,
                                    }
                                    if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id && value3.cost != 0 &&
                                        value3.path.split(',')[2] == value2.id) {
                                        value.cost += value3.cost
                                        value1.cost += value3.cost
                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                            value2.goods_detail.push(cur_obj)
                                        } else {
                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                if (value3.id == value4.id) {
                                                    value4.cost += value3.cost
                                                    value4.quantity += value3.quantity
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //工人费用
                    let cur_worker = {worker_kind: response.data.data.labor_cost.worker_kind}
                    let cur_worker_price = response.data.data.labor_cost.price
                    if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                            JSON.stringify(cur_worker).length - 1)) == -1) {
                        $scope.all_workers.push({
                            worker_kind: cur_worker.worker_kind,
                            price: cur_worker_price
                        })
                    } else {
                        for (let [key, value] of $scope.all_workers.entries()) {
                            if (cur_worker.worker_kind == value.worker_kind) {
                                value.price += cur_worker_price
                            }
                        }
                    }
                    $scope.cur_header = '智能报价'
                    $state.go('nodata.house_list')
                    console.log($scope.all_workers)
                    console.log($scope.all_goods)
                }, function (error) {
                    console.log(error)
                })
            }
        }
    /*样板间相关*/

    })
