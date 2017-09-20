;
let login = angular.module("login",[])
  .controller("login_ctrl",function ($scope,$http,$state,$stateParams) {

    $scope.supplier_login=function () {
      //确认身份
      let url= 'http://test.cdlhzz.cn:888/site/admin-login';
      let params= {
        role_id:6,
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
        console.log(response);
        $state.go('supplier_index')
      },function (error) {
        console.log(error)
      });
    }
  });