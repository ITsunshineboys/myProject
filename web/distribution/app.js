var app = angular.module("app",['ionic','distribution','angularCSS'])
app.config(function ($stateProvider,$urlRouterProvider) {
    $urlRouterProvider.otherwise('index')
    $stateProvider
        .state('index',{
            url:'/',
            templateUrl:'pages/index.html',
            controller:'login_ctrl',
            css:'pages/css/index.css'
        })
        .state('index.login',{
            url:'index',
            templateUrl:'pages/login.html',
            css:['pages/css/index.css','pages/css/login.css']
        })
        .state('index.verification',{
            url:'index',
            templateUrl:'pages/verification.html',
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
                if (res.code === 403) {
                    $state.go('login')
                } else if (res.code === 200 || res.code === 201) {
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
            }, function (response) {
                console.log(response);
                alert(response.statusText)
            })
        }
    })