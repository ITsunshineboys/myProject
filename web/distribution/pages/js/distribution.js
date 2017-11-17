(function (angular) {
    angular.module('distribution',['ui.bootstrap'])
        .controller('login_ctrl',function ($scope,$uibModal,$state,_ajax,$interval,$rootScope) {
            $scope.vm = $scope
            $scope.cur_tel = ''//输入手机号
            $scope.verfication_code = ''//输入验证码
            $scope.countdown = 0//倒计时
            $scope.first_click = 0//是否第一次请求
            $scope.btn_word = ''//按钮文字
            $scope.big_word = ''//大字提示
            $scope.is_small = true//是否有小字提示
            $scope.header_word = '登录'//头部文字
            $scope.cur_bind_tel = ''//绑定手机号
            if(!!sessionStorage.getItem('basic_data')){
                $scope.header_word = JSON.parse(sessionStorage.getItem('basic_data')).header_word
                $scope.countdown = JSON.parse(sessionStorage.getItem('basic_data')).countdown
                $scope.first_click = JSON.parse(sessionStorage.getItem('basic_data')).first_click
                if($scope.countdown != 0){
                    $scope.timer = $interval(function () {
                        sessionStorage.setItem('basic_data',JSON.stringify({header_word:$scope.header_word,countdown:$scope.countdown,first_click:$scope.countdown}))
                        $scope.countdown --
                        if($scope.countdown == 0){
                            $interval.cancel($scope.timer)
                        }
                    },1000)
                }
            }
            //跳转个人中心页获取数据
            if(!!sessionStorage.getItem('all_data')){
                $scope.all_data = JSON.parse(sessionStorage.getItem('all_data'))
            }
            //判断返回
            $scope.cur_return = function () {
                if($rootScope.curState_name == 'index.verification'){
                    $rootScope.fromState_name = 'index.login'
                    $scope.header_word = '登录'
                    sessionStorage.setItem('basic_data',JSON.stringify({header_word:$scope.header_word,countdown:$scope.countdown,first_click:$scope.countdown}))
                }else if($rootScope.curState_name == 'index.login'){
                    $rootScope.fromState_name = 'index.login'
                }else if($rootScope.curState_name == 'index.bind_tel'){
                    $rootScope.fromState_name = 'personal_center'
                }
                $rootScope.goPrev()
            }
            $scope.$watch('cur_tel',function (newVal,oldVal) {
                $interval.cancel($scope.timer)
                $scope.countdown = 0
                $scope.first_click = 0
                sessionStorage.setItem('basic_data',JSON.stringify({header_word:$scope.header_word,countdown:$scope.countdown,first_click:$scope.first_click}))
            })
            //验证手机号
            $scope.get_true_tel = function () {
                let all_modal = function ($scope, $uibModalInstance) {
                    $scope.btn_word = '确认'
                    $scope.big_word = '手机号输入不正确'
                    $scope.is_small = true
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                        // $state.go('intelligent.intelligent_index')
                    }
                }
                all_modal.$inject = ['$scope', '$uibModalInstance']
                    if(/^1[3|4|5|7|8][0-9]{9}$/.test($scope.cur_tel)){
                    $scope.header_word = '填写验证码'
                        sessionStorage.setItem('basic_data',JSON.stringify({header_word:$scope.header_word,countdown:$scope.countdown,first_click:$scope.countdown}))
                    $state.go('index.verification')
                    }else{
                        $uibModal.open({
                            templateUrl: 'pages/cur_model.html',
                            controller: all_modal,
                            windowClass:'cur_modal',
                            backdrop:'static'
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
                           sessionStorage.setItem('basic_data',JSON.stringify({header_word:$scope.header_word,countdown:$scope.countdown,first_click:$scope.countdown}))
                            $scope.countdown --
                           if($scope.countdown == 0){
                               $interval.cancel($scope.timer)
                           }
                        },1000)
                    }
                })
            }
            //完成分销登录
            $scope.complete = function () {
                let big_word = ''
                let all_modal = function ($scope, $uibModalInstance) {
                    $scope.big_word = big_word
                    $scope.btn_word = '确认'
                    $scope.is_small = true
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                        // $state.go('intelligent.intelligent_index')
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
                            mobile:$scope.cur_tel
                        },function (res) {
                            console.log(res)
                            $scope.first_click = 1
                            $scope.countdown = 60
                            if($scope.countdown != 0){
                                $scope.timer = $interval(function () {
                                    $scope.countdown --
                                    if($scope.countdown == 0){
                                        $interval.cancel($scope.timer)
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
                        _ajax.get('/distribution/distribution-user-center',{},function (res) {
                            console.log(res)
                            $scope.all_data = res.data
                            sessionStorage.setItem('all_data',JSON.stringify($scope.all_data))
                            console.log($scope.all_data)
                            sessionStorage.removeItem('basic_data')
                            $state.go('personal_center')
                        })
                    }
                })
            }
            //跳转绑定手机号
            $scope.go_bind_tel = function () {
                $scope.header_word = '绑定手机号'
                sessionStorage.setItem('basic_data',JSON.stringify({header_word:$scope.header_word,countdown:$scope.countdown,first_click:$scope.countdown}))
                let all_modal = function ($scope, $uibModalInstance) {
                    $scope.btn_word = '确认'
                    $scope.big_word = '您已为首级用户，不能再绑定上级'
                    $scope.is_small = false
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                    }
                }
                all_modal.$inject = ['$scope', '$uibModalInstance']
                if($scope.all_data.son.length == 0){
                    $state.go('index.bind_tel')
                }else{
                    $uibModal.open({
                        templateUrl: 'pages/cur_model.html',
                        controller: all_modal,
                        windowClass:'cur_modal',
                        backdrop:'static'
                    })
                }
            }
            //绑定手机号
            $scope.add_bind_tel = function () {
                let big_word = ''
                let all_modal = function ($scope, $uibModalInstance) {
                    $scope.btn_word = '确认'
                    $scope.big_word = '手机号输入不正确'
                    $scope.is_small = true
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                    }
                }
                all_modal.$inject = ['$scope', '$uibModalInstance']
                let all_modal1 = function ($scope, $uibModalInstance) {
                    $scope.btn_word = '确认'
                    $scope.big_word = '该手机号未加入分销系统'
                    $scope.is_small = true
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                    }
                }
                all_modal1.$inject = ['$scope', '$uibModalInstance']
                if(/^1[3|4|5|7|8][0-9]{9}$/.test($scope.cur_bind_tel)){
                    _ajax.post('/distribution/distribution-binding-mobile',{
                        mobile:$scope.cur_bind_tel
                    },function (res) {
                        console.log(res)
                        if(res.code == 1010){
                            $uibModal.open({
                                templateUrl: 'pages/cur_model.html',
                                controller: all_modal1,
                                windowClass:'cur_modal',
                                backdrop:'static'
                            })
                        }else{
                            _ajax.get('/distribution/distribution-user-center',{},function (res) {
                                console.log(res)
                                $scope.all_data = res.data
                                sessionStorage.setItem('all_data',JSON.stringify($scope.all_data))
                                sessionStorage.removeItem('basic_data')
                                console.log($scope.all_data)
                                $state.go('personal_center')
                            })
                        }
                    })
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
})(angular)