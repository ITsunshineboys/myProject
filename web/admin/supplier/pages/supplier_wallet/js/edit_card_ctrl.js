/**
 * Created by Administrator on 2017/9/25/025.
 */
let edit_card = angular.module("edit_cardModule", []);
edit_card.controller("edit_card_ctrl", function ($rootScope,$scope,$http,$anchorScroll,$location,$window,$state) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };

    $rootScope.crumbs = [{
        name: '钱包',
        icon: 'icon-qianbao',
        link: 'supplier_wallet'
    }, {
        name: '商家账户信息',
        link: 'supplier_account'
    },{
        name: '添加/修改银行卡'
    }];

    $scope.alljudgefalse = false;
    defaultCard();

    function defaultCard() {
        $http({
            method: "get",
            params: {role_id: 6},
            url: baseUrl+"/withdrawals/find-bank-card"
        }).then(function (res) {
            if (Object.keys(res.data.data).length == 0) {
                return;
            } else {
                $scope.carddetail = {
                    username: res.data.data.username,
                    bankcard: res.data.data.bankcard,
                    bankname: res.data.data.bankname,
                    position: res.data.data.position,
                    bankbranch: res.data.data.bankbranch,
                    role_id:+res.data.data.role_id
                }
            }
        })
    }

    $scope.sureEditCard = (val, error) => {
        /*默认的情况*/
        if (val) {
            $scope.successmodal = "#edit_card"
            let url = baseUrl+"/withdrawals/set-bank-card";
            let data =  $scope.carddetail;
            // $scope.suremodal = '#suremodal';
            $http.post(url, data, config).then(function (res) {
                    console.log(res);
            })
        }

        if (!val) {
            $scope.successmodal = "";
            // console.log(val);
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
        }

    }

    /*确认添加成功*/
    $scope.successSure = function () {
        setTimeout(() => {
            $state.go("supplier_account");
        }, 200)
    }

})