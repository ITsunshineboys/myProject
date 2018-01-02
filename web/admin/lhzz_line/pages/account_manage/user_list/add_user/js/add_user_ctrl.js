let add_user=angular.module("add_user_module",[]);
add_user.controller("add_user_ctrl",function ($rootScope,$scope,$http,$stateParams,$state, _ajax) {
    $scope.flag = false;
    $scope.strat =false;
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-zhanghuguanli',
        link: $rootScope.account_click
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

    };

});