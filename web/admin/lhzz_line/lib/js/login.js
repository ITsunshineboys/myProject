let app1= angular.module("app",[]);
app1.controller('login_ctrl',function ($scope,$http,$document) {
    $scope.isLoading = false;
    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    //Enter 键盘事件
    $document.bind("keypress", function(event) {
        $scope.$apply(function (){
            if(event.keyCode == 13){
                $scope.login();
            }
        })
    });

    $scope.error_flag=false;
    $scope.login=function () {
      $scope.isLoading = true;
      $http.post(baseUrl+'/site/admin-login', {
          role_id: 1,
          username: $scope.my_username,
          password: $scope.my_password
      }, config).then(function (res) {
          console.log(res);
          if (res.data.code == 200) {
              window.location.href="index.html"
          } else {
              $scope.error_flag = true;
              $scope.error_txt=res.data.msg;
              $scope.isLoading = false;
          }
      }, function (error) {
          console.log(error)
      });
  }
});