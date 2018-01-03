app.controller('home_ctrl',function ($scope,_ajax) {
    //主页推荐
    _ajax.get('/owner/homepage', {}, function (res) {
        console.log(res);
        $scope.recommend_list = res.data
    });
})