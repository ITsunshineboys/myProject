/**
 * Created by xl on 2017/8/10 0010.
 */
var account_comment= angular.module("account_comment",[])
    .controller("account_comment_ctrl",function ($scope,$http,$state,$stateParams) {
        console.log($stateParams);
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
        $scope.id = $stateParams.id;
        $scope.status_remark = $stateParams.status_remark;
        $scope.status_operator = $stateParams.status_operator;
        $scope.review_status_desc = $stateParams.review_status_desc;
        $scope.legal_person = $stateParams.legal_person;
        $scope.identity_no = $stateParams.identity_no;
        $scope.identity_card_front_image = $stateParams.identity_card_front_image;
        $scope.identity_card_back_image = $stateParams.identity_card_back_image;
        $scope.review_status_desc = $stateParams.review_status_desc;
        $scope.review_time = $stateParams.review_time;
        $scope.a = $stateParams.a;

        console.log($scope.id);
        console.log($scope.a);
        console.log($scope.review_time);
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
            if($scope.review_status_desc == '已认证') {
                console.log(11);
                let id = $stateParams.id
                $state.go("idcard_right",{id:id})
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
            if($scope.review_status_desc == '审核通过') {
                let id = $stateParams.id
                $state.go("idcard_right",{id:id})
            }
            if($scope.review_status_desc == '未认证') {
                console.log(222);

        };
        $scope.back_page = function () {
            console.log(11);
            $state.go("account_index")
        }
    }});