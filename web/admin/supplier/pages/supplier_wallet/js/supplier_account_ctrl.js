/**
 * Created by Administrator on 2017/9/25/025.
 */
let supplier_account = angular.module("supplier_accountModule", []);
supplier_account.controller("supplier_account_ctrl", function ($scope, $http, $stateParams) {
        $http({
            method: "get",
            url: baseUrl+"/supplier-cash/mall-view",
        }).then(function (res) {
            $scope.result = res.data.data;
        })
})