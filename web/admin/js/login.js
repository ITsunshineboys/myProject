var url="http://test.cdlhzz.cn:888/";
//var url="http://localhost:888/";
var role="site/all-roles";
var logout="site/admin-login"
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
        else{
            $(".warm").text("");
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
        else{
            $(".warm").text("");
        }
    };

    $scope.$on('ngRepeatFinished', function (data) { //接收广播，一旦repeat结束就会执行
        $scope.login=function(){
            $scope.name=$("input[name='name1']").val();
            $scope.psw=$("input[name='psw']").val();
            $scope.myrole=$('#select1').val();
            console.log("$scope.myrole=="+$scope.myrole);
            $.ajax({
                url: url+logout+"?role_id="+$scope.myrole+"&username="+$scope.name+"&password="+$scope.psw,
                type: 'POST',
                dataType: "json",
                contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                success: function (data) {
                    $scope.loginout=data;
                    if($scope.loginout.code==200){
                        alert("登录成功！")
                    }
                }
            });



            //$http.post(url+logout+"?role_id="+$scope.myrole+"&username="+$scope.name+"&password="+$scope.psw)
            //    .success(function(data){
            //        $scope.loginout=data;
            //        if($scope.loginout.code==200){
            //            alert("登录成功！")
            //        }
            //
            //
            //    });
            //alert("你点击了登录按钮")

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
