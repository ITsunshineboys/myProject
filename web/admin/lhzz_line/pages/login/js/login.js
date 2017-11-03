;
let login= angular.module("login_module",[]);
login.controller('login_ctrl',function ($scope,$http,$state) {
    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    $scope.error_flag=false;
  $scope.login=function () {
    $http.post(baseUrl+'/site/admin-login',{
        role_id:1,
        username:$scope.my_username,
        password:$scope.my_password
    },config).then(function (res) {
      console.log(res);
      if(res.data.code==200){
           $state.go('home');
      }else{
        $scope.error_flag=true;
      }
    },function (error) {
      console.log(error)
    });

  }
});