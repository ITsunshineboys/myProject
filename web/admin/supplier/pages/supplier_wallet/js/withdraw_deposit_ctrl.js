/**
 * Created by Administrator on 2017/9/25/025.
 */
let withdraw_deposit = angular.module("withdraw_depositModule", []);
withdraw_deposit.controller("withdraw_deposit_ctrl", function (_ajax, $rootScope,$scope, $http, $state, $anchorScroll, $location, $window) {
    $rootScope.crumbs = [{
        name: '钱包',
        icon: 'icon-qianbao',
        link: 'supplier_wallet'
    }, {
        name: '商家账户信息',
        link: 'supplier_account'
    },{
        name: '提现'
    }];

    let reg = /^\d+(\.\d{1,2})?$/; //金额正则
    $scope.psdwarning = false;
    $scope.moneywarning = false;
    $scope.alljudgefalse = false;
    $scope.moneyflag = false;
    $scope.pwdflag = false;
    $scope.money_num = '';
    $scope.password = '';


    totalMoney();

    /*可提现金额*/
    function totalMoney() {
        _ajax.post('/withdrawals/find-supplier-balance',{},function (res) {
            $scope.totalmoney = res.data;
        })
    }

    /*确认提现*/
    $scope.sureWithdraw = function (val, error) {
        $scope.moneyflag = false;
        $scope.moneywarning = false;
        $scope.psdwarning = false;
        $scope.pwdflag = false;
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
            let data = {money: +$scope.money_num, pay_pwd: +$scope.password};
            _ajax.post('/withdrawals/supplier-withdrawals-apply',data,function (res) {
                switch (res.code)
                {
                    case 1055:
                        $scope.psdwarning = true;
                        $scope.psdwrong = res.msg;
                        $scope.pwdflag = true;
                        break;
                    case 1054:
                        $scope.moneywarning = true;
                        $scope.moneywrong = res.msg;
                        $scope.moneyflag = true;
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
            $scope.moneyflag = true;
        }
    }


    /*成功后返回上一页面*/
    $scope.backpPage = () => {
        setTimeout(() => {
            $state.go("supplier_account")
        }, 200);
    }
})