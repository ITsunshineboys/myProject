app.controller('search_ctrl',function ($scope,_ajax,$state) {
    //初始化数据
    $scope.toponymy = {
        name:'',
        address:''
    }
    if(sessionStorage.getItem('toponymy')!=null){
        $scope.toponymy = JSON.parse(sessionStorage.getItem('toponymy'))
    }
    //返回上一页
    $scope.goPrev = function(){
        history.go(-1)
    }
    //获取小区信息
    $scope.$watch('toponymy.name',function (newVal,oldVal) {
        if(newVal!=''){
            _ajax.get('/owner/search',{
                str:newVal
            },function (res) {
                console.log(res);
            })
        }
    })
})