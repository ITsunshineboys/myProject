app.controller('bind_tel_ctrl',function ($uibModal,_ajax,$scope,$state,$stateParams) {
    //初始化
    $scope.vm = $scope
    $scope.cur_bind_tel = ''
    //绑定手机号
    $scope.add_bind_tel = function () {
        let big_word = ''
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.btn_word = '确认'
            $scope.big_word = '手机号输入不正确'
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        let all_modal1 = function ($scope, $uibModalInstance) {
            $scope.btn_word = '确认'
            $scope.big_word = '该手机号未加入分销系统'
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal1.$inject = ['$scope', '$uibModalInstance']
        let all_modal2 = function ($scope, $uibModalInstance) {
            $scope.btn_word = '确认'
            $scope.big_word = big_word
            $scope.is_small = false
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal2.$inject = ['$scope', '$uibModalInstance']
        if(/^1[3|4|5|7|8][0-9]{9}$/.test($scope.cur_bind_tel)){
            _ajax.post('/distribution/distribution-binding-mobile',{
                mobile:$scope.cur_bind_tel
            },function (res) {
                console.log(res)
                big_word = res.msg
                if(res.code == 1010){
                    $uibModal.open({
                        templateUrl: 'pages/cur_model.html',
                        controller: all_modal1,
                        windowClass:'cur_modal',
                        backdrop:'static'
                    })
                }else if(res.code == 1084){
                    $uibModal.open({
                        templateUrl: 'pages/cur_model.html',
                        controller: all_modal2,
                        windowClass:'cur_modal',
                        backdrop:'static'
                    })
                } else if(res.code == 200){
                    $state.go('personal_center')
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
    //返回上一页
    $scope.goPrev = function () {
        history.go(-1)
    }
})