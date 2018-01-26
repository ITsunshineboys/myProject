app.controller('home_ctrl',function ($scope,_ajax) {
    sessionStorage.removeItem('materials')
    sessionStorage.removeItem('params')
    sessionStorage.removeItem('toponymy')
    sessionStorage.removeItem('worker_list')
    sessionStorage.removeItem('other_data')
    sessionStorage.removeItem('quotation_materials')
    sessionStorage.removeItem('options')
    //主页推荐
    _ajax.get('/owner/homepage', {}, function (res) {
        console.log('首页推荐');
        console.log(res);
        $scope.recommend_list = res.data
    });
    window.AndroidWebView.showTable()
})