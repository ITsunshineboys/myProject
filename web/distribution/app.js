var app = angular.module("app",['ionic'])
app.config(function ($stateProvider,$urlRouterProvider) {
    $urlRouterProvider.otherwise('/')
    $stateProvider
        .state('home',{
            url:'/',
            templateUrl:'pages/personal_center/login.html',
            controller:'personal_center_ctrl'
        })
})