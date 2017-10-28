angular.module('set_password_module',[])
.controller('set_password_ctrl',function ($scope,$http,$state,$stateParams,$timeout,$location,$anchorScroll,$window) {
  $scope.myng=$scope;
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  //判断 first or unfirst
  $http.post('http://test.cdlhzz.cn:888/withdrawals/check-isset-pay-pwd',{},config).then(function (res) {
    console.log(res);
    $scope.pw_key=res.data.data.key;
    $scope.mobile=res.data.data.mobile.toString();
    $scope.head_m=$scope.mobile.substring(0,3)
    $scope.foot_m=$scope.mobile.substring(7)

    if(res.data.data.type=='unfirst'){
      $scope.show_unfirst=true;
    }else{
      $scope.show_first=true;
    }
  },function (err) {
    console.log(err);
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
          clearInterval(count_down);
        }
      });
    },1000);
    $http.post(baseUrl+'/withdrawals/send-pay-code',{},config).then(function (res) {
      console.log(res);
    },function (err) {
      console.log(err);
    })
  };
  $scope.confirm_btn=function (valid,error) {
    console.log($scope.new_pw==$scope.again_pw);
    if($scope.show_first==true){
      if(valid && $scope.new_pw==$scope.again_pw && !$scope.pw_flag){
        $scope.modal_v='#save_modal';
        $http.post('http://test.cdlhzz.cn:888/withdrawals/set-pay-pwd',{
          key:$scope.pw_key,
          pay_pwd_first:$scope.new_pw,
          pay_pwd_secend:$scope.again_pw
        },config).then(function (res) {
          console.log(res);
        },function (err) {
          console.log(err);
        })
      }else{
        $scope.submitted=true;
      }
      //判断两次密码是否一致
      $scope.new_pw!=$scope.again_pw?$scope.pw_flag=true:$scope.pw_flag=false;
    }else{
      if(valid){
        $http.post('http://test.cdlhzz.cn:888/withdrawals/set-pay-pwd',{
          key:$scope.pw_key,
          pay_pwd:$scope.new_pw,
          sms_code:$scope.v_code
        },config).then(function (res) {
          console.log(res);
          res.data.code=1002?$scope.show_prompt=true:$scope.show_prompt=false;
            if(res.data.code=200){
              $scope.modal_v='#save_modal';
            }
        },function (err) {
          console.log(err);
        })
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