/**
 * Created by xl on 2017/8/10 0010.
 */
var account_comment = angular.module("account_comment", [])
    .controller("account_comment_ctrl", function ($rootScope,$scope, $http, $state) {
        let command_acunt =  JSON.parse(sessionStorage.getItem('comment_account'));
        $rootScope.crumbs = [{
            name: '账户管理',
            icon: 'icon-zhanghuguanli',
            link: 'account_index'
        },{
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

        //$http({
        //    method: 'get',
        //    url: baseUrl+'/mall/user-list'
        //}).then(function successCallback(response) {
        //    $scope.account = response.data.data.user_list.details;
        //    for(let [key,value] of $scope.account.entries()){
        //        value['names'] = value.role_names.join(',')
        //    }
        //    console.log(response);
        //    console.log($scope.account);
        //    //console.log($scope.second)
        //});
        $scope.goJump = function () {
            if ($scope.review_status_desc == '已认证') {
                console.log(11);
                let id = command_acunt.id
                $state.go("idcard_right", {id: id})
                //     $state.go("idcard_right",{'id':$scope.id,'icon':$scope.icon,
                //         'nickname':$scope.nickname,'old_nickname':$scope.old_nickname,
                //         'district_name':$scope.district_name,'birthday':$scope.birthday,
                //         'signature':$scope.signature,'mobile':$scope.mobile,'aite_cube_no':$scope.aite_cube_no,
                //         'create_time':$scope.create_time,'names':$scope.names,'review_status_desc':$scope.review_status_desc,
                //         'legal_person':$scope.legal_person,'identity_no':$scope.identity_no
                //         ,'identity_card_front_imagen':$scope.identity_card_front_image,'identity_card_back_image':
                //         $scope.identity_card_back_image,'review_time':$scope.review_time,
                //         'status_remark':$scope.status_remark,'status_operator':$scope.status_operator
                //         ,'a':$scope.a})
                // }
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
            console.log(11);
            $state.go("account_index")
        }
    });