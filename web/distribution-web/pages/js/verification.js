app.controller('verification_ctrl',function ($state,$scope,$stateParams,_ajax,$uibModal,$interval) {
    //初始化
    $scope.vm = $scope
    $scope.verfication_code = ''
    $scope.countdown = 0
    $scope.words = sessionStorage.getItem('words')==null?'发送验证码':sessionStorage.getItem('words')
    if(sessionStorage.getItem('timer')!=null){
        $scope.countdown = sessionStorage.getItem('timer')
        if($scope.countdown != 0){
            $scope.timer = $interval(function () {
                $scope.countdown --
                if($scope.countdown == 0){
                    $scope.words = '重新发送'
                    sessionStorage.setItem('words',$scope.words)
                    $interval.cancel($scope.timer)
                    sessionStorage.removeItem('timer')
                }else{
                    sessionStorage.setItem('timer',$scope.countdown)
                }
            },1000)
        }
    }
    //发送验证码
    $scope.get_verification_code = function () {
        _ajax.post('/distribution/distribution-login-mobile',{
            mobile:$stateParams.tel
        },function (res) {
            console.log(res)
            $scope.countdown = 60
            if($scope.countdown != 0){
                $scope.timer = $interval(function () {
                    $scope.countdown --
                    if($scope.countdown == 0){
                        $scope.words = '重新发送'
                        sessionStorage.setItem('words',$scope.words)
                        $interval.cancel($scope.timer)
                        sessionStorage.removeItem('timer')
                    }else{
                        sessionStorage.setItem('timer',$scope.countdown)
                    }
                },1000)
            }
        })
    }
    //完成分销登录
    $scope.complete = function () {
        let vm = $scope
        let big_word = ''
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.big_word = big_word
            $scope.btn_word = '确认'
            $scope.is_small = true
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        let all_modal1 = function ($scope, $uibModalInstance) {
            $scope.big_word = big_word
            $scope.btn_word = '重新发送'
            $scope.is_small = false
            $scope.common_house = function () {
                $uibModalInstance.close()
                _ajax.post('/distribution/distribution-login-mobile',{
                    mobile:vm.cur_tel
                },function (res) {
                    console.log(res)
                    vm.first_click = 1
                    vm.countdown = 60
                    if(vm.countdown != 0){
                        vm.timer = $interval(function () {
                            vm.countdown --
                            if(vm.countdown == 0){
                                $interval.cancel(vm.timer)
                            }
                        },1000)
                    }
                })
            }
        }
        all_modal1.$inject = ['$scope', '$uibModalInstance']
        _ajax.post('/distribution/distribution-login',{
            code:$scope.verfication_code
        },function (res) {
            console.log(res)
            big_word = res.msg
            if(res.code == 1002){
                $uibModal.open({
                    templateUrl: 'pages/cur_model.html',
                    controller: all_modal,
                    windowClass:'cur_modal',
                    backdrop:'static'
                })
            }else if(res.code == 1020){
                $uibModal.open({
                    templateUrl: 'pages/cur_model.html',
                    controller: all_modal1,
                    windowClass:'cur_modal',
                    backdrop:'static'
                })
            }else{
                $state.go('personal_center')
            }
        })
    }
    //返回上一页
    $scope.goPrev = function () {
        history.go(-1)
    }
})