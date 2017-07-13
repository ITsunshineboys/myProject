angular.module("all_controller", [])
    .controller("cell_search_ctrl", function ($scope, $http) {//小区搜索控制器
        $scope.data = ''
        $scope.getData = function () {
            let arr = []
            let url = "/owner/search"
            let data = {
                str:$scope.data
            }
            let config = {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest:function (data) {
                    return $.param(data)
                }
            }
            $http.post(url,data,config).then(function (response) {
                console.log(response)
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
    })
    .controller("intelligent_index_ctrl", function ($scope,$http) {//主页控制器
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
    .controller("intelligent_nodata_ctrl", function ($scope,$stateParams, $http) { //无数据控制器
        $scope.message = ''
        $scope.nowStyle = '现代简约'
        $scope.nowStairs = '实木结构'
        $scope.nowSeries = '齐家'
        $scope.toponymy =''|| $stateParams.toponymy
        $scope.choose_stairs = true;
        //生成材料变量
        $scope.house_bedroom = 0
        $scope.house_hall = 0
        $scope.house_kitchen = 0
        $scope.house_toilet = 0
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
        //生成材料方法
        $scope.getData = function () {
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
                stairs_details_id:+$scope.choose_stairs, //楼梯
                structure:$scope.nowStairs,
                series:$scope.nowSeries,   //系列
                style:$scope.nowStyle,  //风格
                window:$scope.window,//飘窗
                high:$scope.highCrtl, //层高
                province:510000,   //省编码
                city:510100      // 市编码
            }

            let config = {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest:function (data) {
                    return $.param(data)
                }
            }
            //发数据给后台
            //第一个 弱电接口
            $http.post(url,data,config).then(function (response) {
                $scope.data=response.data
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第二个 强电接口
            $http.post(strong,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第三个 水路接口
            $http.post(waterway,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第四个 防水接口
            $http.post(waterproof,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第五个 木作接口
            $http.post(carpentry,data,config).then(function (response) {
                console.log(response)
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
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第八个 主材接口
            $http.post(material,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
              //第九个 软装接口
            $http.post(soft,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第十个 固定家居接口
            $http.post(fixation,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
             //第十一个 移动家具接口
            $http.post(move,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
            //第十二个 家电配套接口
            $http.post(assort,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
             //第十三个 生活配套接口
            $http.post(life,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
              //第十四个 智能配套接口
            $http.post(intelligence,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })

        }
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
            console.log($scope.style_picture)
        }, function (response) {

        })
        //切换楼梯
        $scope.toggleStairs = function (item) {
            $scope.nowStairs = item;
        }
        //切换系列
        $scope.toggleSeries = function (item,index) {
            $scope.nowSeries = item;
            $scope.swiperImg = $scope.style_picture.slice(index,index*3)
        }
        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.nowStyle = item;
        }
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
    .controller("location_city_ctrl",function ($scope) {//城市选择控制器
        // $scope.goPrev = function () {
        //     window.history.back()
        // }
    })
    .controller("intelligent_quotation_ctrl",function ($scope,$http,$stateParams) {//有资料选择器
        $scope.nowSeries ='齐家'; //系列
        $scope.nowStyle = '现代简约'; //风格
        $scope.nowStairs = '实木结构'; //有楼梯风格
        $scope.choose_stairs = true; //是否有楼梯
        $scope.index = 0;
        $scope.house_index = 0;//户型
        $scope.id = $stateParams.id;//主页传过来的id
        let url = "/owner/search";//搜索url
        let data = {
            id:$stateParams.id//有资料请求id
        }
        let config = {//所有post请求必须的请求头
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            transformRequest:function (data) {
                return $.param(data)
            }
        }
        $http.post(url,data,config).then(function (response) {//小区房型基本信息
            console.log(response);
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
            // let data1 = {}
            // for(let i in data){
            //     data1[i] = data[i]
            // }
            // data1["waterproof_total_area"] = 60
            // //一系列请求
            $http.post("/owner/weak-current", data, config).then(function (response) {//弱电请求
                console.log("弱电请求"+response)
                console.log(response)
                $scope.weak_current_category = response.data.data.weak_current
                $scope.weak_current_bottom_case = response.data.data.weak_current_bottom_case
                $scope.weak_current_labor_price = response.data.data.weak_current_labor_price
                $scope.weak_current_material_price = response.data.data.weak_current_material_price
                $scope.weak_current_reticle_cost = response.data.data.weak_current_reticle_cost
                $scope.weak_current_reticle_quantity = response.data.data.weak_current_reticle_quantity
                $scope.weak_current_spool_cost = response.data.data.weak_current_spool_cost
                $scope.weak_current_spool_quantity = response.data.data.weak_current_spool_quantity
                $scope.labor_price += $scope.weak_current_labor_price
            }, function (error) {
                console.log(error)
            })
            // $http.post("/owner/strong-current", data, config).then(function (response) {//强电请求
            //     console.log("强电请求"+response)
            //     console.log(response)
            //     $scope.strong_current_category = response.data.data.strong_current
            //     $scope.strong_current_bottom_case = response.data.data.strong_current_bottom_case
            //     $scope.strong_current_labor_price = response.data.data.strong_current_labor_price
            //     $scope.strong_current_material_price = response.data.data.strong_current_material_price
            //     $scope.strong_current_wire_cost = response.data.data.strong_current_wire_cost
            //     $scope.strong_current_wire_quantity = response.data.data.strong_current_wire_quantity
            //     $scope.strong_current_spool_cost = response.data.data.strong_current_spool_cost
            //     $scope.strong_current_spool_quantity = response.data.data.strong_current_spool_quantity
            //     $scope.labor_price += $scope.strong_current_labor_price
            // }, function (error) {
            //     console.log(error)
            // })
            // $http.post("/owner/waterway", data, config).then(function (response) {//水路请求
            //     console.log("水路请求"+response)
            //     console.log(response)
            //     $scope.waterway_current = response.data.data.waterway_current
            //     $scope.waterway_labor_price = response.data.data.waterway_labor_price
            //     $scope.waterway_material_price = response.data.data.waterway_material_price
            //     $scope.waterway_ppr_cost = response.data.data.waterway_ppr_cost
            //     $scope.waterway_ppr_quantity = response.data.data.waterway_ppr_quantity
            //     $scope.waterway_pvc_cost = response.data.data.waterway_pvc_cost
            //     $scope.waterway_pvc_quantity = response.data.data.waterway_pvc_quantity
            //     $scope.labor_price += $scope.waterway_labor_price
            // }, function (error) {
            //     console.log(error)
            // })
            // $http.post("/owner/carpentry", data, config).then(function (response) {//木作请求
            //     console.log("木作请求"+response)
            //     console.log(response)
            //     $scope.carpentry_current = response.data.data.goods_price
            //     $scope.carpentry_labor_price = response.data.data.carpentry_labor_price
            //     $scope.carpentry_material_price = response.data.data.carpentry_material_price
            //     $scope.keel_cost = response.data.data.keel_cost
            //     $scope.plasterboard_cost = response.data.data.plasterboard_cost
            //     $scope.pole_cost = response.data.data.pole_cost
            //     $scope.labor_price += $scope.carpentry_labor_price
            // }, function (error) {
            //     console.log(error)
            // })
            // $http.post("/owner/coating", data, config).then(function (response) {//乳胶漆请求
            //     console.log("乳胶漆请求"+response)
            //     console.log(response)
            //     $scope.coating_current = response.data.data.goods_price
            //     $scope.coating_labor_price = response.data.data.coating_labor_price
            //     $scope.coating_material_price = response.data.data.coating_material_price
            //     $scope.concave_line_cost = response.data.data.concave_line_cost
            //     $scope.finishing_coat_cost = response.data.data.finishing_coat_cost
            //     $scope.gypsum_powder_cost = response.data.data.gypsum_powder_cost
            //     $scope.primer_cost = response.data.data.primer_cost
            //     $scope.putty_cost = response.data.data.putty_cost
            //     $scope.labor_price += $scope.coating_labor_price
            // }, function (error) {
            //     console.log(error)
            // })
            // $http.post("/owner/mud-make", data1, config).then(function (response) {//泥作请求
            //     console.log("泥作请求"+response)
            //     console.log(response)
            //     $scope.mud_make_current = response.data.data.goods_price
            //     $scope.mud_make_labor_price = response.data.data.mud_make_labor_price
            //     $scope.mud_make_material_price = response.data.data.mud_make_material_price
            //     $scope.cement_cost = response.data.data.cement_cost
            //     $scope.drawing_room_cost = response.data.data.drawing_room_cost
            //     $scope.kitchen_and_toilet_floor_tile = response.data.data.kitchen_and_toilet_floor_tile
            //     $scope.river_sand_cost = response.data.data.river_sand_cost
            //     $scope.self_leveling_cost = response.data.data.self_leveling_cost
            //     $scope.wall_brick_cost = response.data.data.wall_brick_cost
            //     $scope.labor_price += $scope.mud_make_labor_price
            // }, function (error) {
            //     console.log(error)
            // })
            //系列风格请求
            $http.get('/owner/series-and-style').then(function (response) {
                $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
                $scope.series = response.data.data.show.series;//系列数据
                for(let i = 0;i<$scope.series.length;i++){
                    if($scope.series[i].id == $scope.series_id){
                        $scope.nowSeries = $scope.series[i].series;
                    }
                }
                $scope.style = response.data.data.show.style;//风格数据
                for(let i = 0;i< $scope.style.length;i++){
                    if($scope.style[i].id == $scope.series_id){
                        $scope.nowStyle = $scope.style[i].style;
                    }
                }
                $scope.style_picture = response.data.data.show.style_picture;//轮播图片数据
                console.log(response)
            }, function (response) {

            })
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
    })
     .controller("all_comment_ctrl",function (){

    })