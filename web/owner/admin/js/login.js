var role="site/all-roles";
var logout="site/admin-login";
app.controller("admin_login",function($scope,$http){
    var num= /^[0-9]*$/;
    //获取角色列表
    $http.get(url+role)
        .success(function(data){
            $scope.allrole=data.data.roles;
            document.write(data);
        })
    $scope.name1=function(){
        $scope.name=$("input[name='name1']").val();
        $scope.psw=$("input[name='psw']").val();
        console.log("name==="+ $scope.name.length)
        if(!num.test( $scope.name)||$scope.name.length<4){
            $(".login").css({"background":"rgba(236, 184, 75, 0.44)"})
            $(".warm").text("请输入正确的纯数字的手机号/魔方号!");
        }
        else {
            $(".warm").text("");
            if($scope.psw.length>25||$scope.psw.length<6){
                $(".login").css({"background":"rgba(236, 184, 75, 1)"})
            }
        }
    };
    $scope.psw1=function(){
        $scope.psw=$("input[name='psw']").val();
        $scope.name=$("input[name='name1']").val();
        console.log("psw==="+ $scope.psw)
        console.log("psw22222==="+ $("input[name='psw']").val())
        console.log("psw_length==="+ $scope.psw.length);
        if($scope.psw.length>25||$scope.psw.length<4){
            $(".warm").text("请输入6-25位的密码！");
            $(".login").css({"background":"rgba(236, 184, 75, 0.44)"})
        }
        else{
            $(".warm").text("");
            if(num.test( $scope.name)){
                $(".login").css({"background":"rgba(236, 184, 75, 1)"})
            }
        }
    };

    $scope.$on('ngRepeatFinished', function (data) { //接收广播，一旦repeat结束就会执行
        $scope.login=function(){
            $scope.name=$("input[name='name1']").val();
            $scope.psw=$("input[name='psw']").val();
            $scope.myrole=$('#select1').val();
            if(num.test( $scope.name)&&$scope.name.length>=4&&($scope.psw.length<=25||$scope.psw.length>=4)){
                $.ajax({
                    url: url+logout,
                    type: 'POST',
                    data:{"role_id":$scope.myrole,"username":$scope.name,"password":$scope.psw},
                    dataType: "json",
                    contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                    success: function (data) {
                        $scope.loginout=data;
                        if($scope.loginout.code==200){
                            window.location.href=$scope.loginout.data.toUrl;
                            $(".warm").text($scope.loginout.msg);
                        }
                        else{
                            $(".warm").text($scope.loginout.msg);
                        }
                    }
                });
            }
        }
    });
});
app.controller('itemReaptCtrl', ['$scope', function ($scope) {
    $scope.$watch($scope.$last, function () {
        if($scope.$last){   //$scope.$last是来判断是否是最后一个ng-repeat对象， 如果是则$scope.$last的值为true ,反之则为false
            setTimeout(function(){$scope.$emit('ngRepeatFinished')},1); // 由于是向父控制器中发布广播，所有用$emit
        }
    })
}]);
