app.controller('home_ctrl',function ($scope,_ajax) {
    sessionStorage.removeItem('materials')
    sessionStorage.removeItem('params')
    sessionStorage.removeItem('toponymy')
    //主页推荐
    _ajax.get('/owner/homepage', {}, function (res) {
        console.log(res);
        $scope.recommend_list = res.data
    });
})