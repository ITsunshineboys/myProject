(function (angular) {
    angular.module('distribution',['ui.bootstrap'])
        .controller('login_ctrl',function ($scope,$uibModal,$state,_ajax,$interval) {
            $scope.vm = $scope
            $scope.cur_tel = ''//输入手机号
            $scope.verfication_code = ''//输入验证码
            $scope.countdown = 0//倒计时
            $scope.first_click = 0//是否第一次请求
            //验证手机号
            $scope.get_true_tel = function () {
                let all_modal = function ($scope, $uibModalInstance) {
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                        // $state.go('intelligent.intelligent_index')
                    }
                }
                all_modal.$inject = ['$scope', '$uibModalInstance']
                    if(/^1[3|4|5|7|8][0-9]{9}$/.test($scope.cur_tel)){
                    $state.go('index.verification')
                    }else{
                        $uibModal.open({
                            templateUrl: 'pages/cur_model.html',
                            controller: all_modal,
                            windowClass:'cur_modal'
                        })
                    }
            }
            //发送验证码
            $scope.get_verification_code = function () {
                _ajax.post('/distribution/distribution-login-mobile',{
                    mobile:$scope.cur_tel
                },function (res) {
                    console.log(res)
                    $scope.first_click = 1
                    $scope.countdown = 60
                    if($scope.countdown != 0){
                       $scope.timer = $interval(function () {
                            $scope.countdown -= 10
                           if($scope.countdown == 0){
                               $interval.cancel($scope.timer)
                           }
                        },1000)
                    }
                })
            }
        })
})(angular)