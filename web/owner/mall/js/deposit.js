app.controller('depositCtrl', ['$scope', '$state', '_ajax', function ($scope, $state, _ajax) {
    let payParams = JSON.parse(sessionStorage.getItem('payParams'));
    console.log(payParams, '请求参数');
    $scope.isShow = false;
    $scope.isPay = false;
    $scope.user = {
        name: '',
        phone: ''
    };
    $scope.$watch('user.name', function (n, o) {
        $scope.isPay = n !== "" && $scope.user.phone !== "";
    });
    $scope.$watch('user.phone', function (n, o) {
        $scope.isPay = n !== "" && $scope.user.name !== "";
    });
    $scope.payMoney = function () {
        let reg = /(13|14|15|17|18)[0-9]{9}/;
        if (!reg.test($scope.user.phone)) {
            $scope.isShow = true;
            return false;
        }
        payParams.name = $scope.user.name;
        payParams.phone = $scope.user.phone;
        // $state.go('pay_success');
        _ajax.post('/order/effect-earnst-alipay-sub', payParams, function (res) {
            angular.element('body').append(res);
        })
    }
}]);