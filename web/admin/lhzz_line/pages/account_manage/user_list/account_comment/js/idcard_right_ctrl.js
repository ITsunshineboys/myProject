/**
 * Created by xl on 2017/8/10 0010.
 */
var idcard_right= angular.module("idcard_right",[])
    .controller("card_right_ctrl",function ($rootScope,$scope,$http,$state,$stateParams,_ajax) {
        $rootScope.crumbs = [{
            name: '账户管理',
            icon: 'icon-zhanghuguanli',
            link: $rootScope.account_click
        }];
        $scope.id = $stateParams.id;
        $scope.icon = $stateParams.icon;
        $scope.nickname = $stateParams.nickname;
        $scope.old_nickname = $stateParams.old_nickname;
        $scope.district_name = $stateParams.district_name;
        $scope.birthday = $stateParams.birthday;
        $scope.signature = $stateParams.signature;
        $scope.mobile = $stateParams.mobile;
        $scope.aite_cube_no = $stateParams.aite_cube_no;
        $scope.create_time = $stateParams.create_time;
        $scope.names = $stateParams.names;
        $scope.review_status_desc = $stateParams.review_status_desc;
        $scope.legal_person = $stateParams.legal_person;
        $scope.identity_no = $stateParams.identity_no;
        $scope.identity_card_front_image = $stateParams.identity_card_front_image;
        $scope.identity_card_back_image = $stateParams.identity_card_back_image;
        $scope.review_status_desc = $stateParams.review_status_desc;
        $scope.review_time = $stateParams.review_time;
        $scope.status_remark = $stateParams.status_remark;
        $scope.status_operator = $stateParams.status_operator;
        $scope.a = $stateParams.a;
        console.log($scope.id);
        console.log($scope.a);
        console.log($scope.status_remark);
        console.log($scope.review_time);
        _ajax.get('/mall/user-identity',{user_id:$scope.id},function (response) {
            console.log(response);
            $scope.legal_person = response.data.user_identity.legal_person;
            console.log($scope.legal_person);
            $scope.identity_no = response.data.user_identity.identity_no;
            $scope.identity_card_front_image = response.data.user_identity.identity_card_front_image;
            $scope.identity_card_back_image = response.data.user_identity.identity_card_back_image;
            $scope.review_status_desc = response.data.user_identity.review_status_desc;
            $scope.review_time = response.data.user_identity.review_time;
        });

        $scope.getBack = function () {
            $state.go("account_comment",{
                'id':$scope.id,'icon':$scope.icon,
                'nickname':$scope.nickname,'old_nickname':$scope.old_nickname,
                'district_name':$scope.district_name,'birthday':$scope.birthday,
                'signature':$scope.signature,'mobile':$scope.mobile,'aite_cube_no':$scope.aite_cube_no,
                'create_time':$scope.create_time,'names':$scope.names,'review_status_desc':$scope.review_status_desc,
                'legal_person':$scope.legal_person,'identity_no':$scope.identity_no,
                'identity_card_front_image':$scope.identity_card_front_image,
                'identity_card_back_image':$scope.identity_card_back_image,
                'review_time':$scope.review_time,'status_remark':$scope.status_remark,
                'status_operator':$scope.status_operator,'a':$scope.a
            })
        }
    });