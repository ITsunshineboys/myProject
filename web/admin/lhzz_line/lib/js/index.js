let index = angular.module("index_module",[]);
index.controller("index_ctrl",function ($scope,$http) {
  //确认身份
  let url= '/site/admin-login';
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
    // console.log("主页登录：");
    // console.log(response);
  },function (error) {
    console.log(error)
  })
});