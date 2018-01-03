var app = angular.module("app", ["ui.router","ngAnimate",'angularCSS'])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/");
        $stateProvider
            .state("home", {
                url: "/",
                templateUrl: "home.html",
                css:['css/intelligent_index.css','zui-1.7.0-dist/dist/css/zui.min.css'],
                controller:'home_ctrl'
            })
            .state('nodata',{
                url:'/nodata',
                templateUrl:'nodata.html',
                css:['css/inteligent_nodata.css','zui-1.7.0-dist/dist/css/zui.min.css','css/all.css'],
                controller:'nodata_ctrl'
            })
            .state('search',{
                url:'/search',
                templateUrl:'search.html',
                css:'css/cell_search.css',
                controller:'search_ctrl'
            })
            // .state('nodata.house_list',{
            //     url:'/nodata_house_list',
            //     templateUrl:'nodata.html',
            //     css:['css/inteligent_nodata.css','zui-1.7.0-dist/dist/css/zui.min.css','css/all.css'],
            // })
            // .state('nodata.basics_decoration',{ // 基础装修
            //     url:'/basics_decoration',
            //     templateUrl:'all_basics.html',
            //     css:['css/basics.css','zui-1.7.0-dist/dist/css/zui.min.css','css/all.css'],
            // })
            // .state('nodata.main_material',{ // 主要材料
            //     url:'/main_material',
            //     templateUrl:'main_goods.html',
            //     css:['css/main.css','zui-1.7.0-dist/dist/css/zui.min.css','css/all.css']
            // })
            // .state('nodata.other_material',{    // 其他
            //     url:'/other_material?index',
            //     templateUrl:'other_goods.html',
            //     css:['css/other.css','css/all.css']
            // })
            // .state('nodata.product_detail',{
            //     url:'/product_detail?num',
            //     templateUrl:'cur_product_detail.html',
            //     css:['css/product_details.css','css/all.css'],
            // })
            // .state('nodata.all_goods',{
            //     url:'/all_goods',
            //     templateUrl:'get_all_goods.html',
            //     css:['css/commodify.css','css/all.css']
            // })
            // .state('nodata.second_level',{
            //     url:'/second_level',
            //     templateUrl:'second_level.html',
            //     css:['css/movefurniture.css','css/all.css']
            // })
            .state('modelRoom',{ // 样板间
                url:'/quotation?roomCode&toponymy&street',
                templateUrl:'model_room.html',
                css: ['css/model_room.css', 'zui-1.7.0-dist/dist/css/zui.min.css'],
                controller: 'modelRoomCtrl'
            })
            .state('deposit', { // 支付定金
                url: '/deposit',
                templateUrl: 'deposit.html',
                css: ['css/deposit.css', 'zui-1.7.0-dist/dist/css/zui.min.css'],
                controller: 'depositCtrl'
            })
            .state('pay_success', { // 支付成功
                url: '/pay_success',
                templateUrl: 'pay-success.html',
                css: ['css/pay-success.css', 'zui-1.7.0-dist/dist/css/zui.min.css']
            })

    }])
    .service('_ajax', function ($q,$http, $state) {
        this.get = function (url, params, callback) {
            let deferred = $q.defer()
            $http({
                method: 'GET',
                url: baseUrl + url,
                params: params
            }).then(function (response) {
                deferred.resolve(response)
                let res = response.data;
                if (res.code === 403) {
                    $state.go('login')
                } else if (res.code === 200 || res.code === 201 || res.code === 1068) {
                    if (typeof callback === 'function') {
                            callback(res)
                    }
                } else {
                    alert(res.msg)
                }
            }, function (response) {
                console.log(response);
                alert(response.statusText)
            })
            return deferred.promise
        };
        this.post = function (url, params, callback) {
            $http({
                method: 'post',
                url: baseUrl + url,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                data: params,
                transformRequest: function (data) {
                    return $.param(data);
                }
            }).then(function (response) {
                let res = response.data;
                if (res.code === 403) {
                    $state.go('login')
                } else if (res.code === 200 || res.code === 1068 || res.code === undefined) {
                    if (typeof callback === 'function') {
                            callback(res)
                    }
                } else {
                    alert(res.msg)
                }
                deferred.resolve(response)
            }, function (response) {
                console.log(response);
                alert(response.statusText)
            })
        }
    })
    .run(["$rootScope","$state",function ($rootScope,$state) {
        $rootScope.$on("$stateChangeSuccess",function (event,toState,toParams,fromState,fromParams) {
            document.body.scrollTop = document.documentElement.scrollTop = 0;
            $rootScope.fromState_name = fromState.name;
            $rootScope.curState_name = toState.name
        });
        $rootScope.goPrev = function (obj) {
            $state.go($rootScope.fromState_name,obj)
        }
    }]);