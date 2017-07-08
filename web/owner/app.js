angular.module("app", ["ui.router","directives", "all_controller","ngAnimate"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/")
        $stateProvider
            .state("home", {
                url: "/",
                views: {
                    "": {templateUrl: "intelligent_index.html"}
                },
                controller: "intelligent_index_ctrl"
            })
            .state("nodata", {
                url: "/nodata",
                views: {
                    "": {templateUrl: "Intelligent_nodata.html"}
                },
                controller: "intelligent_nodata_ctrl",
                params:{"toponymy":""}
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
                    "": {templateUrl: "movefurniture.html"}
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
            .state("have_data",{
                url:"/have_data",
                views:{
                    "":{templateUrl:"Intelligent_quotation.html"}
                },
                controller:"intelligent_quotation_ctrl"
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
