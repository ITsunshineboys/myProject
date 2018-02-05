let app = angular.module("app", ['ionic', 'angularCSS','ui.bootstrap'])
app.config(function ($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/authorize')
    $stateProvider
        .state('login', {//登录
            url: '/login?tel',
            templateUrl: 'pages/login.html',
            css: 'pages/css/login.css',
            controller: 'login_ctrl'
        })
        .state('verification', {//填写验证码
            url: '/verification?tel',
            templateUrl: 'pages/verification.html',
            css: 'pages/css/login.css',
            controller: 'verification_ctrl'
        })
        .state('personal_center', {//个人中心
            url: '/personal_center',
            templateUrl: 'pages/personal_center.html',
            css: ['pages/css/personal_center.css','//at.alicdn.com/t/font_499455_m8vh0qf9xb1hh0k9.css'],
            controller: 'personal_center_ctrl'
        })
        .state('bind_tel', {//绑定手机号
            url: '/bind_tel',
            templateUrl: 'pages/bind_tel.html',
            css:'pages/css/login.css',
            controller:'bind_tel_ctrl'
        })
        .state('authorize',{
            url:'/authorize',
            templateUrl:'pages/authorize.html',
            css:'pages/css/authorize.css',
            controller:'authorize_ctrl'
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
    .run(["$rootScope", "$state", function ($rootScope, $state) {
        $rootScope.$on("$stateChangeSuccess", function (event, toState, toParams, fromState, fromParams) {
            $rootScope.fromState_name = fromState.name;
            $rootScope.curState_name = toState.name
        });
        $rootScope.goPrev = function (obj) {
            console.log($rootScope.curState_name)
            console.log($rootScope.fromState_name)
            $state.go($rootScope.fromState_name, obj)
        }
    }]);