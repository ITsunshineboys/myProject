/**
 * Created by Administrator on 2017/9/25/025.
 */
let supplier_account = angular.module("supplier_accountModule", []);
supplier_account.controller("supplier_account_ctrl", function ($scope, $http, $stateParams) {
    walletIndex();
    function walletIndex() {
        $http({
            method: "get",
            params:{id:81},
            url: "http://test.cdlhzz.cn:888/supplieraccount/account-view",
        }).then(function (res) {
            $scope.result =  res.data.data;
        })
    }
})