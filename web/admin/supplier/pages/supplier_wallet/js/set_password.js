angular.module('set_password_module',[])
.controller('set_password_ctrl',function ($rootScope,$scope,$http,$state,$stateParams,$timeout,$location,$anchorScroll,$window,_ajax) {
  $scope.myng=$scope;
    $rootScope.crumbs = [{
        name: '钱包',
        icon: 'icon-qianbao',
        link: 'supplier_wallet'
    }, {
        name: '设置交易密码',
    }];
  let reg=/^\d{6}$/;
  //判断 first or unfirst
  _ajax.post('/withdrawals/check-isset-pay-pwd',{},function (res) {
      console.log(res);
      $scope.pw_key=res.data.key;
      $scope.mobile=res.data.mobile.toString();
      $scope.head_m=$scope.mobile.substring(0,3)
      $scope.foot_m=$scope.mobile.substring(7)
      if(res.data.type=='unfirst'){
          $scope.show_unfirst=true;
      }else{
          $scope.show_first=true;
      }
  });

  //获取验证码
  $scope.Countdown=60;
  $scope.show_send=true;//发送按钮、倒计时
  $scope.show_prompt=false;//验证码提示字
  //发送按钮
  $scope.send_click=function () {
    $scope.show_send=false;
    //倒计时
    let count_down= setInterval(function () {
      $scope.$apply(function(){
        $scope.Countdown--;
        //在这里去手动触发脏检查
        if($scope.Countdown<0){
          $scope.show_send=true;
          $scope.Countdown=60;
          clearInterval(count_down);
        }
      });
    },1000);
    _ajax.post('/withdrawals/send-pay-code',{},function (res) {
        console.log(res);
    });
  };
  $scope.confirm_btn=function (valid,error) {
    if($scope.show_first==true){
      //判断两次密码是否一致
      $scope.new_pw!=$scope.again_pw?$scope.pw_flag=true:$scope.pw_flag=false;
      if(valid){
        let new_pw_f=reg.test($scope.new_pw);
        let again_pw_f=reg.test($scope.again_pw);
        if(new_pw_f&&again_pw_f){
            if($scope.new_pw==$scope.again_pw && !$scope.pw_flag){
                _ajax.post('/withdrawals/set-pay-pwd',{
                    key:$scope.pw_key,
                    pay_pwd_first:$scope.new_pw,
                    pay_pwd_secend:$scope.again_pw
                },function (res) {
                    if(res.code==200){
                        $('#save_modal').modal('show');
                    }
                });
            }else{
                $scope.pw_two_txt='第二次与第一次密码不一致，请重新填写';
                $scope.pw_two_flag=true;
            }
        }else{
            $scope.pw_flag=true;
            $scope.pw_two_txt='请输入6位整数数字';
        }
      }else{
        $scope.submitted=true;
      }
    }else{
      if(valid){
          let pw_reg=reg.test($scope.new_pw);
          if(pw_reg){
              _ajax.post('/withdrawals/set-pay-pwd',{
                  key:$scope.pw_key,
                  pay_pwd:$scope.new_pw,
                  sms_code:$scope.v_code
              },function (res) {
                  console.log(res);
                  if(res.code == 200){
                      $('#save_modal').modal('show');
                      $scope.v_code_flag=false;
                  }else if(res.code == 1002){
                      $scope.pw_flag=false;
                      $scope.v_code_flag=true;
                      $scope.unfirst_txt='验证码错误，请重新输入';
                  }
              })
          }else{
              $scope.pw_flag=true;
              $scope.unfirst_txt='请输入6位整数数字';
          }
      }else{
        $scope.submitted=true;
      }
    }

    //名称输入框为空， 文本框变红，并跳转到对于的位置
    if(!valid){
      $scope.submitted = true;
      // if(value.$invalid=true){
      for (let [key, value] of error.entries()) {
        if (value.$invalid) {
          $anchorScroll.yOffset = 150;
          $location.hash(value.$name);
          $anchorScroll();
          $window.document.getElementById(value.$name).focus();
          break
        }
      }
    }
  };
  //模态框确认按钮
  $scope.save_confirm=function () {
    $timeout(function () {
      $state.go('supplier_wallet')
    },300);
  }
});