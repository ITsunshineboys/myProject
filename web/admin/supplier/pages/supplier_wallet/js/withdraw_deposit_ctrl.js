/**
 * Created by Administrator on 2017/9/25/025.
 */
let withdraw_deposit = angular.module("withdraw_depositModule", []);
withdraw_deposit.controller("withdraw_deposit_ctrl", function ($scope, $http, $state, $anchorScroll, $location, $window) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };

    let reg = /^\d+(\.\d{1,2})?$/; //金额正则
    $scope.psdwarning = false;
    $scope.moneywarning = false;
    $scope.alljudgefalse = false;
    $scope.test = false;


    totalMoney();

    /*可提现金额*/
    function totalMoney() {
        let url = baseUrl+"/withdrawals/find-supplier-balance";
        $http.post(url, {}, config).then(function (res) {
            $scope.totalmoney = res.data.data;
        })
    }


       // $scope.moneyCheck = () => {
    //     if(!(reg.test($scope.money_num)&&$scope.money_num>0)){
    //         $scope.money_num = '';
    //     }
    // }
    //





    /*确认提现*/
    $scope.sureWithdraw = function (val, error) {
        $scope.test = false;
        $scope.moneywarning = false;
        $scope.psdwarning = false;
        $scope.test2 = false;
        /*默认的情况*/
        if (!val) {
            $scope.alljudgefalse = true;
            //循环错误，定位到第一次错误，并聚焦
            for (let [key, value] of error.entries()) {
                if (value.$invalid) {
                    $anchorScroll.yOffset = 150;
                    $location.hash(value.$name);
                    $anchorScroll();
                    $window.document.getElementById(value.$name).focus();
                    break;
                }
            }
            return;
        }

        if(reg.test($scope.money_num)&&Number($scope.money_num)>0){
            let url = baseUrl+"/withdrawals/supplier-withdrawals-apply";
            let data = {money: +$scope.money_num, pay_pwd: +$scope.password};
            $http.post(url, data, config).then(function (res) {
                switch (res.data.code)
                {
                    case 1055:
                        $scope.psdwarning = true;
                        $scope.psdwrong = res.data.msg;
                        $scope.test2 = true;
                        break;
                    case 1054:
                        $scope.moneywarning = true;
                        $scope.moneywrong = res.data.msg;
                        $scope.test = true;
                        break;
                    case 1000:
                        $scope.failwarning = "您尚未绑定银行卡";
                        $("#withdraw_warning").modal('show');
                        $scope.backpage = false;
                        break;
                    case 200:
                        $scope.failwarning = "提现已提交，到账时间一般是3-5个工作日，如提现失败，请重新提现";
                        $("#withdraw_warning").modal('show');
                        $scope.backpage = true;
                        break;
                }
            })
        }else{
            $scope.moneywarning = true;
            $scope.moneywrong = '您的输入不正确，请重新输入';
            $scope.test = true;
        }
    }


    /*成功后返回上一页面*/
    $scope.backpPage = () => {
        setTimeout(() => {
            $state.go("supplier_account")
        }, 200);
    }
})