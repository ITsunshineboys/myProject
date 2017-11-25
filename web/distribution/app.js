var app = angular.module("app",['ionic','distribution','angularCSS'])
app.config(function ($stateProvider,$urlRouterProvider) {
    $urlRouterProvider.otherwise('/index')
    $stateProvider
        .state('index',{
            url:'/',
            templateUrl:'pages/index.html',
            controller:'login_ctrl',
            css:'pages/css/index.css'
        })
        .state('index.login',{//登录首页
            url:'index',
            templateUrl:'pages/login.html',
            css:['pages/css/index.css','pages/css/login.css']
        })
        .state('index.verification',{//填写验证码
            url:'verification',
            templateUrl:'pages/verification.html',
            css:['pages/css/index.css','pages/css/login.css']
        })
        .state('personal_center',{//个人中心
            url:'/personal_center',
            templateUrl:'pages/personal_center.html',
            controller:'login_ctrl',
            css:'pages/css/personal_center.css'
        })
        .state('index.bind_tel',{//绑定手机号
            url:'bind_tel',
            templateUrl:'pages/bind_tel.html',
            css:['pages/css/index.css','pages/css/login.css']
        })
})
    .service('_ajax', function ($http, $state) {
        let baseUrl = ''
        this.get = function (url, params, callback) {
            $http({
                method: 'GET',
                url: baseUrl + url,
                params: params
            }).then(function (response) {
                let res = response.data;
                console.log(res)
                if (res.code === 403) {
                    $state.go('login')
                } else {
                    if (typeof callback === 'function') {
                        callback(res)
                    }
                }
            }, function (response) {
                console.log(response);
            })
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
                console.log(res)
                if (res.code === 403) {
                    $state.go('login')
                } else {
                    if (typeof callback === 'function') {
                        callback(res)
                    }
                }
            }, function (response) {
                console.log(response);
            })
        }
    })
    .run(["$rootScope","$state",function ($rootScope,$state) {
        $rootScope.$on("$stateChangeSuccess",function (event,toState,toParams,fromState,fromParams) {
            $rootScope.fromState_name = fromState.name;
            $rootScope.curState_name = toState.name
        });
        $rootScope.goPrev = function (obj) {
            console.log($rootScope.curState_name)
            console.log($rootScope.fromState_name)
            $state.go($rootScope.fromState_name,obj)
        }
    }]);