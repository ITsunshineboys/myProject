app.controller('login_ctrl',function ($scope,$state,$stateParams,$uibModal) {
    //初始化
    sessionStorage.clear()
    $scope.vm = $scope
    $scope.cur_tel = $stateParams.tel==undefined?'':$stateParams.tel
    //验证手机号
    $scope.get_true_tel = function () {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.btn_word = '确认'
            $scope.big_word = '手机号输入不正确'
            $scope.is_small = true
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(/^1[3|4|5|7|8][0-9]{9}$/.test($scope.cur_tel)){
            $scope.header_word = '填写验证码'
            $state.go('verification',{tel:$scope.cur_tel})
        }else{
            $uibModal.open({
                templateUrl: 'pages/cur_model.html',
                controller: all_modal,
                windowClass:'cur_modal',
                backdrop:'static'
            })
        }
    }
})