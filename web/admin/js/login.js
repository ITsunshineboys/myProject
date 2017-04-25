var url="http://test.cdlhzz.cn:888/";
var role="site/all-roles";
var logout="site/logout"
app.controller("admin_login",function($scope,$http){
    var num= /^[0-9]*$/;


    //获取角色列表
    $http.get(url+role)
        .success(function(data){
            $scope.allrole=data.data.roles;
        })
    $scope.name1=function(){
        $scope.name=$("input[name='name1']").val();
        console.log("name==="+ $scope.name)
        if(!num.test( $scope.name)){
            $(".warm").text("请输入正确的纯数字的手机号/魔方号!");
        }
    };
    $scope.psw1=function(){
        $scope.psw=$("input[name='psw']").val();
        console.log("psw==="+ $scope.psw)
        console.log("psw22222==="+ $("input[name='psw']").val())
        console.log("psw_length==="+ $scope.psw.length)
        if($scope.psw.length>25||$scope.psw.length<6){
            $(".warm").text("请输入6-25位的密码！");
        }
    };
    $scope.login=function(){
        $scope.name=$("input[name='name1']").val();
        $scope.psw=$("input[name='psw']").val();
        //$http.get(url+logout)
        //    .success(function(data){
        //        $scope.loginout=data;
        //    })
        alert("你点击了登录按钮")

    }

});