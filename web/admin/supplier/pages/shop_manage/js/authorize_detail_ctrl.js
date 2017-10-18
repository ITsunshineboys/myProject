/**
 * Created by Administrator on 2017/9/21/021.
 */
let authorizedetail = angular.module("authorizedetailModule", []);
authorizedetail.controller("authorizedetail_ctrl", function ($scope) {
    let data = sessionStorage.getItem('authorizeInfo');
    if (data !== null) {
        $scope.data = JSON.parse(data);
    }
});