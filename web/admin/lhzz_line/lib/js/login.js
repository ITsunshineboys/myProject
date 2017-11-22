let app1= angular.module("app",[]);
app1.controller('login_ctrl',function ($scope,$http,$document) {
    $scope.isLoading = false;
    $scope.error_flag=false;
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
    //登录按钮
    $scope.login=function () {
        if($scope.username===''|| $scope.username===undefined){
            $scope.error_flag = true;
            $scope.error_txt='请输入账号';
        }else if($scope.password==='' || $scope.password===undefined){
            $scope.error_flag = true;
            $scope.error_txt='请输入密码';
        }else{
            $scope.isLoading = true;
            $http.post(baseUrl+'/site/admin-login', {
                role_id: 1,
                username: $scope.username,
                password: $scope.password
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
  }
});