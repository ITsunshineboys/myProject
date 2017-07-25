angular.module("app", ["ui.router","ngAnimate","directives", "all_controller"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/")
        $stateProvider
            .state("home", {  //��ҳ
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
            .state("have_data",{   //������
                url:"/have_data",
                views:{
                    "":{templateUrl:"Intelligent_quotation.html"}
                },
                controller:"intelligent_quotation_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("all_comment",{  //����ҳ
                url:"/all_comment",
                views:{
                    "":{templateUrl:"all_comment.html"}
                },
                controller:"all_comment_ctrl",
                params:{"id":""}
            })
            .state("product_details",{  //��Ʒ����ҳ
                url:"/product_details",
                views:{
                    "":{templateUrl:"product_details.html"}
                },
                controller:"product_details_ctrl",
                params:{"id":""}
            })
            //.state("basics_decoration",{  //����װ��ҳ
            //    url:"/basics_decoration",
            //    views:{
            //        "":{templateUrl:"basics_decoration.html"}
            //    },
            //    controller:"basics_decoration_ctrl",
            //    params:{name:'',address:'','pic_one':"",'pic_two':""}
            //})

            .state("main_material",{  //������ҳ
                url:"/main_material",
                views:{
                    "":{templateUrl:"main_material.html"}
                },
                controller:"main_material_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("other_materials",{  //��������ҳ
                url:"/other_materials",
                views:{
                    "":{templateUrl:"other_materials.html"}
                },
                controller:"other_materials_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })

            .state("fixed_home",{  //�̶��Ҿ�
                url:"/fixed_home",
                views:{
                    "":{templateUrl:"fixed_home.html"}
                },
                controller:"fixed_home_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("house_hold",{  //�̶��Ҿ�
                url:"/house_hold",
                views:{
                    "":{templateUrl:"house_hold.html"}
                },
                controller:"house_hold_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("soft_house",{  //��װ�Ҿ�
                url:"/soft_house",
                views:{
                    "":{templateUrl:"soft_house.html"}
                },
                controller:"soft_house_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("life_house",{  //����Ҿ�
                url:"/life_house",
                views:{
                    "":{templateUrl:"life_house.html"}
                },
                controller:"soft_house_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("add_main",{  //����Ҿ�
                url:"/add_main",
                views:{
                    "":{templateUrl:"add_main.html"}
                },
                controller:"add_main_ctrl",
                params:{name:'',address:'','pic_one':"",'pic_two':""}
            })
            .state("have_search",{  //����Ҿ�
                url:"/have_search",
                views:{
                    "":{templateUrl:"have_search.html"}
                },
                controller:"have_search_ctrl",
                params:{'name':'','address':"",'pic_one':"",'pic_two':""}
            })
            .state("basics_decoration",{  //����ҳ
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

