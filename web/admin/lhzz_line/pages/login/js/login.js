;
let login= angular.module("login_module",[]);
login.controller('login_ctrl',function ($scope,$http,$state) {
  $scope.login=function () {
    //确认身份
    let url= 'http://test.cdlhzz.cn:888/site/admin-login';
    let params= {
      role_id:1,
      username:13551201821,
      password:"demo123"
    };
    let config = {
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      transformRequest: function (data) {
        return $.param(data)
      }
    };
    $http.post(url,params,config).then(function (response) {
      $state.go('banner_recommend')
    },function (error) {
      console.log(error)
    })
  }

});