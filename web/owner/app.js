angular.module("app", ["ui.router","directives", "all_controller","ngAnimate"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/")
        $stateProvider
            .state("home", {
                url: "/",
                views: {
                    "": {templateUrl: "intelligent_index.html"}
                },
                controller: "intelligent_index_ctrl",
            })
            .state("have_data",{   //有资料
                url:"/have_data",
                views:{
                    "":{templateUrl:"Intelligent_quotation.html"}
                },
                controller:"intelligent_quotation_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("all_comment",{  //评论页
                url:"/all_comment",
                views:{
                    "":{templateUrl:"all_comment.html"}
                },
                controller:"all_comment_ctrl",
                params:{"id":""}
            })

            .state("main_material",{  //主材料页
                url:"/main_material",
                views:{
                    "":{templateUrl:"main_material.html"}
                },
                controller:"main_material_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("other_materials",{  //其他材料页
                url:"/other_materials",
                views:{
                    "":{templateUrl:"other_materials.html"}
                },
                controller:"other_materials_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })

            .state("fixed_home",{  //固定家居
                url:"/fixed_home",
                views:{
                    "":{templateUrl:"fixed_home.html"}
                },
                controller:"fixed_home_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("house_hold",{  //固定家居
                url:"/house_hold",
                views:{
                    "":{templateUrl:"house_hold.html"}
                },
                controller:"house_hold_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("soft_house",{  //软装家居
                url:"/soft_house",
                views:{
                    "":{templateUrl:"soft_house.html"}
                },
                controller:"soft_house_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("life_house",{  //生活家居
                url:"/life_house",
                views:{
                    "":{templateUrl:"life_house.html"}
                },
                controller:"soft_house_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("add_main",{  //生活家居
                url:"/add_main",
                views:{
                    "":{templateUrl:"add_main.html"}
                },
                controller:"add_main_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("have_search",{  //生活家居
                url:"/have_search",
                views:{
                    "":{templateUrl:"have_search.html"}
                },
                controller:"have_search_ctrl",
                params:{'name':'','address':"",'pic_one':"",'pic_two':""}
            })
            .state("basics_decoration",{  //辅材页
                url:"/basics_decoration",
                views:{
                    "":{templateUrl:"basics_decoration.html"}
                },
                controller:"basics_decoration_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("nodata", {
                url: "/nodata",
                views: {
                    "": {templateUrl: "Intelligent_nodata.html"}
                },
                controller: "intelligent_nodata_ctrl",
                // resolve:{
                //     load:['$ocLazyLoad',function ($ocLazyLoad) {
                //         return $ocLazyLoad.load(['css/inteligent_nodata.css','zui-1.7.0-dist/dist/css/zui.min.css'])
                //     }]
                // },
                params:{"toponymy":"","handyman_price":0,'isBack':'','stair':'','labor_price':'','series_index':'',
                'style_index':'','worker_category':'','level':'','house_bedroom':'','house_hall':'',
                    'house_kitchen':'','house_toilet':'','highCrtl':'',"window":'',"area":"",'choose_stairs':''
                    ,'stair_copy':'','level_copy':'', 'twelve_dismantle':'' , 'twenty_four_dismantle':'',
                    'repair':'', 'twelve_new_construction':'' , 'twenty_four_new_construction':'', 'building_scrap':'',
                    'cur_stair' :'','platform_price':'','supply_price':'','cur_labor':''}
            })
            .state("cell_search", {
                url: "/cell_search",
                views: {
                    "": {templateUrl: "cell_search.html"}
                },
                controller: "cell_search_ctrl"
            })
            .state("move_furniture", {
                url: "/move_furniture",
                views: {
                    "": {templateUrl: "second_level_material.html"}
                },
                controller: "move_furniture_ctrl"
            })
            .state("city_choose",{
                url:"/choose_city",
                views:{
                    "":{templateUrl:"location_city.html"}
                },
                controller:"location_city_ctrl"
            })
            .state("basics",{  //辅材页
                url:"/basics",
                views:{
                    "":{templateUrl:"basics.html"}
                },
                controller:"basics_ctrl",
                params:{"stair":"","index":"","worker_category":"","handyman_price":"","style_index":"",
                "series_index":"","area":"","labor_price":"","level":"",'house_bedroom':'','house_hall':'',
                    'house_kitchen':'','house_toilet':'','highCrtl':'',"window":'','choose_stairs':'',
                    'stair_copy':'','level_copy':'', 'twelve_dismantle':'' , 'twenty_four_dismantle':'',
                    'repair':'', 'twelve_new_construction':'' , 'twenty_four_new_construction':'', 'building_scrap':'',
                    'cur_stair' :'','platform_price':'','supply_price':'','cur_labor':''}
            })
            .state("other",{  //其他材料页
                url:"/other",
                views:{
                    "":{templateUrl:"other.html"}
                },
                controller:"other_ctrl",
                params:{'stair':'','index':'',"worker_category":"","handyman_price":"","style_index":"",
                    "series_index":"","area":"","labor_price":"","level":"",'house_bedroom':'','house_hall':'',
                    'house_kitchen':'','house_toilet':'','highCrtl':'',"window":'','choose_stairs':'',
                    'stair_copy':'','level_copy':'', 'twelve_dismantle':'' , 'twenty_four_dismantle':'',
                    'repair':'', 'twelve_new_construction':'' , 'twenty_four_new_construction':'', 'building_scrap':'',
                    'cur_stair' :'','platform_price':'','supply_price':'','platform_price_copy':'','supply_price_copy':''}
            })
            .state("second_level_material",{  //某二级分类页
                url:"/second_level_material",
                views:{
                    "":{templateUrl:"second_level_material.html"}
                },
                controller:"second_level_material_ctrl",
                params:{'stair':'','index':'',"worker_category":"","handyman_price":"","style_index":"",
                    "series_index":"","area":"","labor_price":"","level":"",'house_bedroom':'','house_hall':'',
                    'house_kitchen':'','house_toilet':'','highCrtl':'',"window":'','choose_stairs':'',
                    'stair_copy':'','level_copy':'', 'twelve_dismantle':'' , 'twenty_four_dismantle':'',
                    'repair':'', 'twelve_new_construction':'' , 'twenty_four_new_construction':'', 'building_scrap':'',
                    'cur_stair' :'','platform_price':'','supply_price':''}
            })
            .state("commodity_details",{  //某三级分类页
                url:"/commodity_details",
                views:{
                    "":{templateUrl:"commodity_details.html"}
                },
                controller:"commodity_details_ctrl",
                params:{'stair':'','index':'',"worker_category":"","handyman_price":"","style_index":"",
                    "series_index":"","area":"","labor_price":"","level":"",'house_bedroom':'','house_hall':'',
                    'house_kitchen':'','house_toilet':'','highCrtl':'',"window":'','choose_stairs':'',
                    'second_material':'','three_material':'','pid':'','excluded_item':'','prev_index':'',
                    'stair_copy':'','level_copy':'', 'twelve_dismantle':'' , 'twenty_four_dismantle':'',
                    'repair':'', 'twelve_new_construction':'' , 'twenty_four_new_construction':'', 'building_scrap':'',
                    'cur_stair' :'','platform_price':'','supply_price':''}
            })
            .state("product_details",{  //单项详情页
                url:"/product_details",
                views:{
                    "":{templateUrl:"product_details.html"}
                },
                controller:"product_details_ctrl",
                params:{'stair':'','index':'',"worker_category":"","handyman_price":"","style_index":"",
                    "series_index":"","area":"","labor_price":"","level":"",'house_bedroom':'','house_hall':'',
                    'house_kitchen':'','house_toilet':'','highCrtl':'',"window":'','choose_stairs':'',
                    'second_material':'','three_material':'','three_material_details':'','product_details':'',
                'prev_index':'','excluded_item':'','stair_copy':'','level_copy':'','pid':'', 'twelve_dismantle':'' , 'twenty_four_dismantle':'',
                    'repair':'', 'twelve_new_construction':'' , 'twenty_four_new_construction':'', 'building_scrap':'',
                    'cur_stair' :'','platform_price':'','supply_price':''}
            })
            .state("main",{  //主要材料详情页
                url:"/main",
                views:{
                    "":{templateUrl:"main.html"}
                },
                controller:"main_ctrl",
                params:{'stair':'','index':'',"worker_category":"","handyman_price":"","style_index":"",
                    "series_index":"","area":"","labor_price":"","level":"",'house_bedroom':'','house_hall':'',
                    'house_kitchen':'','house_toilet':'','highCrtl':'',"window":'','choose_stairs':'',
                    'second_material':'','three_material':'','three_material_details':'','product_details':'',
                'prev_index':'','excluded_item':'','stair_copy':'','level_copy':'','pid':'', 'twelve_dismantle':'' , 'twenty_four_dismantle':'',
                    'repair':'', 'twelve_new_construction':'' , 'twenty_four_new_construction':'', 'building_scrap':'',
                    'cur_stair' :'','platform_price':'','supply_price':'','platform_price_copy':'','supply_price_copy':''}
            })
    }])
    .run(["$rootScope","$state",function ($rootScope,$state) {
        $rootScope.$on("$stateChangeSuccess",function (event,toState,toParams,fromState,fromParams) {
            document.body.scrollTop = document.documentElement.scrollTop = 0
            $rootScope.goPrev = function (obj) {
                $state.go(fromState.name,obj)
            }
        })
    }])

