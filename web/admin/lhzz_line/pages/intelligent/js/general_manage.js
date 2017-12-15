app.controller('general_manage_ctrl',function ($rootScope,_ajax,$scope,$stateParams,$http,$uibModal,$state) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
            link: function () {
                $state.go('intelligent.intelligent_index')
                $rootScope.crumbs.splice(1, 4)
            }
        }, {
            name: '通用管理'
        }
    ]
    _ajax.get('/quote/commonality-list',{},function (res) {
        console.log(res);
        $scope.general_list = res.post
    })
    //跳转
    $scope.goGeneralDetail = function (item) {
        if(item.title.indexOf('点位')!=-1){
            $state.go('general_detail',{id:item.id})
        }else if(item.title == '户型面积'){

        }else{
            $state.go('else_general_manage')
        }
    }
})