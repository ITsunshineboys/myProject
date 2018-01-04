app.controller('personal_center_ctrl',function ($uibModal,$scope,$state,$stateParams,_ajax) {
    sessionStorage.clear()
    //获取个人中心数据
    _ajax.get('/distribution/distribution-user-center',{},function (res) {
        console.log(res)
        $scope.all_data = res.data
        sessionStorage.setItem('all_data',JSON.stringify($scope.all_data))
        console.log($scope.all_data)
        sessionStorage.removeItem('basic_data')
        $state.go('personal_center')
    })
    //跳转绑定手机号
    $scope.go_bind_tel = function () {
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
            $state.go('bind_tel')
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