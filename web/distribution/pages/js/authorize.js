app.controller('authorize_ctrl',function ($scope,$state,_ajax) {
    $scope.addDistribution = function () {
        _ajax.post('/distribution/authorized-join-distribution',{},function (res) {
            if(res.code == 200){
                $state.go('personal_center')
            }
        })
    }
    $scope.goPrev = function () {
        window.AndroidWebView.webfinish()
    }
})