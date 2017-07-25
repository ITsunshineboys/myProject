angular.module("app", ["ui.router","ngAnimate","directives", "all_controller"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/")
        $stateProvider
            .state("home", {  //首页
                url: "/",
                views: {
                    "": {templateUrl: "intelligent_index.html"}
                },
                controller: "intelligent_index_ctrl"
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
            .state("product_details",{  //产品详情页
                url:"/product_details",
                views:{
                    "":{templateUrl:"product_details.html"}
                },
                controller:"product_details_ctrl",
                params:{"id":""}
            })
            //.state("basics_decoration",{  //基础装修页
            //    url:"/basics_decoration",
            //    views:{
            //        "":{templateUrl:"basics_decoration.html"}
            //    },
            //    controller:"basics_decoration_ctrl",
            //    params:{name:'',address:'','pic_one':"",'pic_two':""}
            //})

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



    }])
    .controller("intelligent_index_ctrl",function ($scope) {
        $scope.pageClass="intelligent_index"
    })
    .controller("intelligent_nodata_ctrl",function ($scope) {
        $scope.pageClass="Intelligent_nodata"
    })
    .controller("move_furniture_ctrl",function ($scope) {
        $scope.pageClass="movefurniture"
    })
    .controller("location_city_ctrl",function ($scope) {
        $scope.pageClass="location_city"
    })
    .run(["$rootScope","$state",function ($rootScope,$state) {
        $rootScope.$on("$stateChangeSuccess",function (event,toState,toParams,fromState,fromParams) {
            document.body.scrollTop = document.documentElement.scrollTop = 0
            $rootScope.goPrev = function (obj) {
                $state.go(fromState.name,obj)
            }
        })
    }]);

