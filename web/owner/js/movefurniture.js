/**
 * Created by xl on 2017/6/20 0020.
 */
var myapp=angular.module('myapp',[]);
myapp.controller("commentCtrl",function ($scope,$http) {
    $http({
        method: 'get',
        url:"/mall/categories"
    }).then(function successCallback(response) {
        $scope.message = response.data.data.categories;
        console.log(data);
        // alert(222)
    }, function errorCallback(data) {

        alert(111);

    });
});