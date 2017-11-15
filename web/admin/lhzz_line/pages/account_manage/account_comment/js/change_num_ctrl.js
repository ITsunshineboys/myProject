/**
 * Created by xl on 2017/8/10 0010.
 */
var change_num= angular.module("change_num",[])
    .controller("change_num_ctrl",function ($scope,$http,$state,$stateParams) {
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
        $scope.status_remark=$stateParams.status_remark;
        $scope.status_operator=$stateParams.status_operator;
        $scope.a=$stateParams.a;
        console.log($scope.status_remark);
        console.log($scope.status_operator);
        console.log($scope.a);
        $scope.$watch('new_num', function (newVal,oldVal) {
            $scope.tel_error = ''
        });
        //点击确认验证表单
       $scope.getRight = function (valid) {
           if(valid){
               $http({
                   method: 'get',
                   url: baseUrl+'/mall/user-list'
               }).then(function successCallback(response) {
                   console.log(response);
                   $scope.old_num = response.data.data.user_list.details;
                   $http({
                       method: 'get',
                       url: baseUrl+'/site/check-mobile-registered',
                       params:{
                           mobile:$scope.new_num
                       }
                   }).then(function successCallback(response) {
                       console.log(response);
                       $scope.codeMobile = response.data.code;
                       console.log($scope.codeMobile);
                       if( $scope.codeMobile == 1019) {
                           $scope.tel_error = '*该手机号已被注册，请重新输入';
                           $scope.change_success = "";
                       } else {
                           $scope.change_success = 'modal'
                       }
                   })
                   // if( JSON.stringify( $scope.old_num).indexOf('"mobile":'+$scope.new_num)!=-1) {
                   //     $scope.tel_error = '*该手机号已被注册，请重新输入';
                   //     $scope.change_success = "";
                   // } else {
                   //     $scope.change_success = 'modal'
                   // }
               })
           }
       };

        //验证成功后更改号码
        $scope.changeNum = function () {
            $http({
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data)
                },
                method: 'post',
                url: baseUrl+'/mall/reset-mobile',
                data:{
                    mobile: $scope.new_num,
                    user_id: $scope.id
                }
            }).then(function successCallback(response) {
                //$scope.account = response.data.data.user_list.details;
                console.log(response);
                setTimeout(function () {
                    $state.go("account_comment",{'id':$scope.id,'icon':$scope.icon,
                        'nickname':$scope.nickname,'old_nickname':$scope.old_nickname,
                        'district_name':$scope.district_name,'birthday':$scope.birthday,
                        'signature':$scope.signature,'mobile':$scope.new_num,'aite_cube_no':$scope.aite_cube_no,
                        'create_time':$scope.create_time,'names':$scope.names,'review_status_desc':$scope.review_status_desc,
                        'status_remark':$scope.status_remar,'status_operator':$scope.status_operator,'a':$scope.a

                    })
                },300)
            })
        }
        //点击返回  保存上个页面的值
        $scope.returnBack = function () {
            $state.go("account_comment",{'id':$scope.id,'icon':$scope.icon,
            'nickname':$scope.nickname,'old_nickname':$scope.old_nickname,
             'district_name':$scope.district_name,'birthday':$scope.birthday,
              'signature':$scope.signature,'mobile':$scope.mobile,'aite_cube_no':$scope.aite_cube_no,
               'create_time':$scope.create_time,'names':$scope.names,'review_status_desc':$scope.review_status_desc,
                'status_remark':$scope.status_remark,'status_operator':$scope.status_operator,
                'a':$scope.a


            })
        }
    });