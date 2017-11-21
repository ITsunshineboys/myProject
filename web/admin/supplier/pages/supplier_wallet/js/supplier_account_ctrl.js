/**
 * Created by Administrator on 2017/9/25/025.
 */
let supplier_account = angular.module("supplier_accountModule", []);
supplier_account.controller("supplier_account_ctrl", function (_ajax, $rootScope, $scope) {
   _ajax.get('/supplier-cash/mall-view',{},function (res) {
       $scope.result = res.data;
   })

    $rootScope.crumbs = [{
        name: '钱包',
        icon: 'icon-qianbao',
        link: 'supplier_wallet'
    }, {
        name: '商家账户信息',
    }];
})