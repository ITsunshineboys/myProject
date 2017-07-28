angular.module("app", ["ui.router","ngAnimate", "all_controller"])
.config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise("/");
    $stateProvider
        .state("home", {  //��ҳ
            url: "/",
            views: {
                "": {templateUrl:"mall_index.html"}
        },
            controller:"mall_index_ctrl"
        })
        .state("minute_class", {   //����ҳ
            url: "/minute_class",
            views: {
                "": {templateUrl: "minute_class.html"}
            },
            controller: "minute_class_ctrl",
            params:{"pid":"","title":""}
        })
        .state("search", {   //С������ҳ
            url: "/search",
            views: {
                "": {templateUrl: "search.html"}
            },
            controller: "search_ctrl"
            //params:{"pid":"","title":""}
        })
        .state("commodity_search", {   //��Ʒ����ҳ
            url: "/commodity_search",
            views: {
                "": {templateUrl: "commodity_search.html"}
            },
            controller: "commodity_search_ctrl",
            params:{"pid":"","title":""}
        })
        .state("details", {   //ĳ����Ʒ��ϸ�б�
            url: "/details",
            views: {
                "": {templateUrl: "details.html"}
            },
            controller: "details_ctrl",
            params:{"id":""}
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
