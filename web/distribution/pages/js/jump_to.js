app.controller('jump_ctrl',function ($scope,$state,_ajax) {
    console.log(111);
    _ajax.get('/distribution/judge-whether-join-distribution',{},function (res) {
        console.log(res);
        if(res.code == 200){
            $state.go('personal_center')
        }else if (res.code == 1097){
            $state.go('authorize')
        }
    })
})