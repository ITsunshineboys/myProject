/**
 * Created by Administrator on 2017/9/25/025.
 */
let edit_card = angular.module("edit_cardModule", []);
edit_card.controller("edit_card_ctrl", function (_ajax,$rootScope,$scope,$http,$anchorScroll,$location,$window,$state) {
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
        _ajax.get('/withdrawals/find-bank-card',{role_id: 6},function (res) {
            if (Object.keys(res.data).length == 0) {
                return;
            } else {
                $scope.carddetail = {
                    username: res.data.username,
                    bankcard: res.data.bankcard,
                    bankname: res.data.bankname,
                    position: res.data.position,
                    bankbranch: res.data.bankbranch,
                    role_id:6
                }
            }
        })
    }

    $scope.sureEditCard = (val, error) => {
        /*默认的情况*/
        if (val) {
            _ajax.post('/withdrawals/set-bank-card',$scope.carddetail,function (res) {
                $('#edit_card').modal('show');
            })
        }

        if (!val) {
            $scope.successmodal = "";
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