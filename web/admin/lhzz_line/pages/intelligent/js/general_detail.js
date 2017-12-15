app.controller('general_detail_ctrl',function (_ajax,$scope,$rootScope,$stateParams,$http,$uibModal,$state) {
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
            name: '通用管理',
            link:-1
        },{
        name:'通用管理详情'
        }
    ]
    //请求点位数据
    _ajax.get('/quote/commonality-title',{
        id:$stateParams.id
    },function (res) {
        console.log(res);
        $scope.total_count = res.count
        $scope.one_title = res.list.one_title
        for(let [key,value] of $scope.one_title.entries()){
            for(let [key1,value1] of res.list.two_title.entries()){
                if(value1.pid == value.id){
                    if(value.two_title == undefined){
                        value.two_title = [value1]
                    }else{
                        value.two_title.push(value)
                    }
                }
            }
        }
        console.log($scope.one_title);
    })
    //添加一级标题
    $scope.addOneTitle = function () {
        $scope.one_title.push({
            title:'',
            differentiate:'1',
            two_title:[{
                title:'',
                count:''
            }]
        })
    }
    //移除一级标题
    $scope.removeOneTitle = function (index) {
        $scope.one_title.splice(index,1)
    }
    //添加二级标题
    $scope.addTwoTitle = function (index) {
        $scope.one_title[index].two_title.push({
            title:'',
            count:''
        })
    }
    //移除二级标题
    $scope.removeTwoTitle = function (parent_index,index) {
        $scope.one_title[parent_index].two_title.splice(index,1)
    }
    //生成二级标题
    $scope.getTwoTitle = function (item) {
        let obj = ''
        if(item.id == undefined){
            obj = {
                id:$scope.total_count.id,
                title:item.title
            }
        }else{
            obj = {
                id:item.id,
                title:item.title
            }
        }
        console.log(obj);
        if(item.title!=''){
            _ajax.post('/quote/commonality-title-add',{
                one_title:obj
            },function (res) {
                console.log(res);
            })
        }
    }
})