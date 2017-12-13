let add_user=angular.module("add_user_module",[]);
add_user.controller("add_user_ctrl",function ($rootScope,$scope,$http,$stateParams,$state, _ajax) {
    $scope.flag = false;
    $scope.strat =false;
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-zhanghuguanli',
        link: -1
    },{
        name: '添加账户',
    }];
    let reg =/^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/;
    let password = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/;

    //确定按钮 添加号码
    $scope.new_num = '';
    $scope.new_password = '';
    $scope.add_model = '';
    $scope.comment = '';
    $scope.changeNum = function () {
        if($scope.new_num == '' || $scope.new_password =='' ){
            $('#ok_modal').modal('show');
            $scope.comment = '请填写完整信息';
        }
        if($scope.new_num != '' && $scope.new_password !=''){
            console.log(111);
            console.log($scope.new_num);
            _ajax.get('/site/check-mobile-registered',{mobile:$scope.new_num},function (res) {
                    console.log(res);
                    $scope.codeMobile = res.code;
                    if($scope.codeMobile == 200) {
                        _ajax.post('/mall/user-add',{
                            mobile:$scope.new_num,
                            password:$scope.new_password
                        },function (res) {
                            console.log(res);
                            $('#ok_modal').modal('show');
                            // $scope.add_model = '#ok_modal';
                            $scope.comment = '添加成功';
                        })
                    }else{
                        $('#ok_modal').modal('show');
                        $scope.comment = '该手机号已被注册，请重新输入';
                    }
            })
        }

        $scope.getBack = function () {
            if($scope.codeMobile == 200){
                setTimeout(function () {
                    $state.go('account_user_list.normal')
                },300)
            }
        }
        // _ajax.get('/site/check-mobile-registered',$scope.new_num,function (res) {
        //     console.log(res);
        //     $scope.codeMobile = res.data.code;
        //     if($scope.codeMobile == 1019) {
        //         $scope.flag = true;
        //         $scope.error = "该手机号已被注册，请重新输入";
        //     }else{
        //         $scope.flag = false;
        //         console.log(11122222);
        //
        //     }
        //
        // })
        // console.log($scope.codeMobile);
        // if ($scope.flag == false &&  $scope.strat == false) {
        //
        //     $http({
        //         headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        //         transformRequest: function (data) {
        //             return $.param(data)
        //         },
        //         method: 'post',
        //         url: baseUrl+'/mall/user-add',
        //         data:{
        //             mobile:$scope.new_num,
        //             password:$scope.new_password
        //         }
        //     }).then(function successCallback(response) {
        //         console.log(response);
        //     });
        // }


    };
    // $scope.getBack = function  ()  {
    //     setTimeout(function () {
    //         $state.go("account_index")
    //     },300);
    // };
    // $scope.cancel = function () {
    //     $state.go("account_index")
    // };
       // //手机号失去焦点判断
    // $scope.getBlur = function  () {
    //     if (!reg.test($scope.new_num)) {
    //         $scope.error = "请输入11位手机号";
    //         $scope.flag = true;
    //     }else{
    //         $scope.flag = false;
    //         $http({
    //             method: 'get',
    //             url: baseUrl+'/mall/user-list'
    //         }).then(function successCallback(response) {
    //             let arr= [];
    //             $scope.old_num = response.data.data.user_list.details;
    //             for( let [key, value] of $scope.old_num.entries()) {
    //                 arr.push(value.mobile)
    //             }
    //             $scope.oldNum = arr;
    //
    //
    //
    //         });
    //     }
    // };
    // //密码判断
    // $scope.passwordBlur = function () {
    //     console.log($scope.new_password);
    //     if (!password.test($scope.new_password)) {
    //         $scope.comment = "该密码填写不符合规则，请重新填写";
    //         $scope.strat = true;
    //     }else{
    //         $scope.strat = false;
    //         //return {
    //         //    right:true
    //         //}
    //     }
    //

    // }
});