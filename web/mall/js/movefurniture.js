/**
 * Created by xl on 2017/6/20 0020.
 */
var myapp=angular.module('myapp',[]);
myapp.controller("commentCtrl",function ($scope,$http) {
    $http({
        method: 'post',
        url: url+"/categories"
    }).then(function successCallback(response) {
        $scope.message = response.data.data.categories;
        //alert(message)
    }, function errorCallback(response) {
    // «Î«Û ß∞‹÷¥––¥˙¬Î
    alert(url);

});
});