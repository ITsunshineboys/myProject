/**
 * Created by xl on 2017/8/10 0010.
 */
var account_comment = angular.module("account_comment", [])
    .controller("account_comment_ctrl", function ($rootScope, $scope, $http, $state) {
        let command_acunt = JSON.parse(sessionStorage.getItem('comment_account'));
        $rootScope.crumbs = [{
            name: '账户管理',
            icon: 'icon-zhanghuguanli',
            link: $rootScope.account_click
        }, {
            name: '账户详情',
        }];
        $scope.icon = command_acunt.icon;
        $scope.nickname = command_acunt.nickname;
        $scope.old_nickname = command_acunt.old_nickname;
        $scope.district_name = command_acunt.district_name;
        $scope.birthday = command_acunt.birthday;
        $scope.signature = command_acunt.signature;
        $scope.mobile = command_acunt.mobile;
        $scope.aite_cube_no = command_acunt.aite_cube_no;
        $scope.create_time = command_acunt.create_time;
        $scope.names = command_acunt.names;
        $scope.id = command_acunt.id;
        $scope.status_remark = command_acunt.status_remark;
        $scope.status_operator = command_acunt.status_operator;
        $scope.review_status_desc = command_acunt.review_status_desc;
        $scope.legal_person = command_acunt.legal_person;
        $scope.identity_no = command_acunt.identity_no;
        $scope.identity_card_front_image = command_acunt.identity_card_front_image;
        $scope.identity_card_back_image = command_acunt.identity_card_back_image;
        $scope.review_status_desc = command_acunt.review_status_desc;
        $scope.review_time = command_acunt.review_time;
        $scope.a = command_acunt.a;
        $scope.goJump = function () {
            if ($scope.review_status_desc == '已认证') {
                console.log(11);
                let id = command_acunt.id
                $state.go("idcard_right", {id: id})
            }
            if ($scope.review_status_desc == '审核通过') {
                let id = command_acunt.id;
                $state.go("idcard_right", {id: id})
            }
            if ($scope.review_status_desc == '未认证') {
                console.log(222);

            }
        };

        $scope.back_page = function () {
            history.go(-1)
        }
    });