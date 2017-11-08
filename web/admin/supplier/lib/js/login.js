let app = angular.module("app",[]);
app.controller("login_ctrl",function ($scope,$http,$document) {
    let baseUrl = (function () {
        return '';
    })();
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    let reg =/^1[3|4|5|7|8][0-9]{9}$/;
    //登录角色
    $scope.login_rolo=[
        {id:6,value:'商家'},
    ];
    $scope.rolo_value=$scope.login_rolo[0].id;
    //登录按钮
    $scope.supplier_login=function () {
      $http.post(baseUrl+'/site/admin-login',{
          role_id:$scope.rolo_value,
          username:$scope.phone_number,
          password:$scope.password_number
      },config).then(function (res) {
          console.log(res);
          if(res.data.code==200){
              window.location.href='index.html';
          }else{
              $scope.error_flag=true;
          $scope.error_flag_txt='*用户名或密码不正确';
          }
      })
    };
    // //忘记密码发送验证码
    // //获取验证码
    $scope.Countdown=60;
    $scope.show_send=true;//发送按钮、倒计时
    $scope.show_prompt=false;//验证码提示字
    //发送按钮
    $scope.send_click=function () {
        if(!reg.test($scope.forget_mb)){ //----手机号不正常
            $scope.forget_mb_flag=true;//手机号红框
        }else{
            $scope.forget_mb_flag=false;//取消手机号红框
            $scope.show_send=false;
            //倒计时
             $scope.count_down= setInterval(function () {
                $scope.$apply(function(){
                    $scope.Countdown--;
                    //在这里去手动触发脏检查
                    if($scope.Countdown<0){
                        $scope.show_send=true;
                        clearInterval($scope.count_down);
                        $scope.Countdown=60;
                    }
                });
            },1000);
            //获取忘记密码的验证码
            $http.post(baseUrl+'/site/validation-code',{
                type:'forgetPassword',
                mobile:$scope.forget_mb
            },config).then(function (res) {
                console.log(res);
            })
        }
    };
    let new_pw_reg=/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,}/;//不少于6位的数字加字母
    //忘记密码确认按钮
    $scope.forget_pw_confirm=function () {
        $scope.forget_mb==''|| $scope.forget_mb==undefined?$scope.forget_mb_flag=true:$scope.forget_mb_flag=false;
        $scope.forget_v_code=='' || $scope.forget_v_code==undefined?$scope.forget_v_code_flag=true:$scope.forget_v_code_flag=false;
        $scope.forget_new_pw==''|| $scope.forget_new_pw==undefined?$scope.forget_new_pw_flag=true:$scope.forget_new_pw_flag=false;
        $scope.forget_v_txt='';
        $scope.forget_mb_prompt='';
        //输入框都不为空时，请求接口
        if(!$scope.forget_mb_flag&&!$scope.forget_v_code_flag&&!$scope.forget_new_pw_flag){
            //密码正则，不能少于6位，并且必须数字加字母
            if(!new_pw_reg.test($scope.forget_new_pw)){
                $scope.new_pw_txt='*该密码不符合规则，请重新设置';
            }else{
                $scope.new_pw_txt='';
                $http.post(baseUrl+'/site/admin-forget-password',{
                    mobile:$scope.forget_mb,
                    new_password:$scope.forget_new_pw,
                    validation_code:$scope.forget_v_code
                },config).then(function (res) {
                    console.log(res);
                    if(res.code==200){
                        $('#forget_pw_modal').modal('hide');
                        $('#modity_success').modal('show');
                    }else if(res.data.code==1002){
                        $scope.forget_v_txt='验证码错误，请重新输入';
                    }else if(res.data.code==1020){
                        $scope.forget_v_txt='验证码超时，请重新获取';
                    }else if(res.data.code==1010){
                        $scope.forget_mb_prompt='该手机号还未注册，请联系客服400-3948-398';
                    }else if(res.data.code==1040){
                        $scope.forget_mb_prompt='该手机号还未注册商家，请联系客服400-3948-398';
                    }else{
                        $scope.forget_mb_prompt='';
                        $scope.forget_v_txt='';
                    }
                })
            }
        }
    };
    //点击忘记密码，初始化状态
    $scope.forget_pw_click=function () {
        $scope.forget_v_txt='';
        $scope.forget_mb_prompt='';
        $scope.new_pw_txt='';
        $scope.forget_mb_flag=false;
        $scope.forget_v_code_flag=false;
        $scope.forget_new_pw_flag=false;
        $scope.forget_mb='';
        $scope.forget_v_code='';
        $scope.forget_new_pw='';
        clearInterval($scope.count_down);
        $scope.Countdown=60;
        $scope.show_send=true;//发送按钮、倒计时
        $scope.show_prompt=false;//验证码提示字
    };
    //Enter 键盘事件
    $document.bind("keypress", function(event) {
        $scope.$apply(function (){
            if(event.keyCode == 13){
                $scope.supplier_login();
            }
        })
    });
  });