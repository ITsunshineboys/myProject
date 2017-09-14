let add_user=angular.module("add_user_module",[]);
add_user.controller("add_user_ctrl",function ($scope,$http,$stateParams,$state) {
    $scope.flag = false;
    $scope.strat =false;
    let reg =/^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/;
    let password = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/;

    //确定按钮 添加号码
    $scope.changeNum = function () {
        if ($scope.flag == false &&  $scope.flag == false) {
            console.log(112222222222);
            $http({
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data)
                },
                method: 'post',
                url: 'http://test.cdlhzz.cn:888/mall/user-add',
                data:{
                    mobile:$scope.new_num,
                    password:$scope.new_password
                }
            }).then(function successCallback(response) {
                console.log(response);
            });
        }


    };
    $scope.getBack = function  ()  {
        setTimeout(function () {
            $state.go("account_index")
        },300);
    };
    $scope.cancel = function () {
        $state.go("account_index")
    };
    //手机号失去焦点判断
    $scope.getBlur = function  () {
        if (!reg.test($scope.new_num)) {
            $scope.error = "请输入11位手机号";
            $scope.flag = true;
        }else{
            $scope.flag = false;
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/user-list'
            }).then(function successCallback(response) {
                let arr= [];
                $scope.old_num = response.data.data.user_list.details;
                for( let [key, value] of $scope.old_num.entries()) {
                    arr.push(value.mobile)
                }
                $scope.oldNum = arr;
                for( let [key1, value1] of arr.entries()){
                    if($scope.new_num == value1) {
                        $scope.flag = true;
                        $scope.error = "该手机号已被注册，请重新输入";
                        break;
                    }else{
                        $scope.flag = false;
                        console.log(11122222);
                        //return {
                        //    mobile:true
                        //};
                        break;
                    }
                }
            });
        }
    };
    //密码判断
    $scope.passwordBlur = function () {
        console.log($scope.new_password);
        if (!password.test($scope.new_password)) {
            $scope.comment = "该密码填写不符合规则，请重新填写";
            $scope.strat = true;
        }else{
            $scope.strat = false;
            //return {
            //    right:true
            //}
        }


    }
});