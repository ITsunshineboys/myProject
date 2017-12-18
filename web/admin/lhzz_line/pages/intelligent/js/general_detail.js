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
    $scope.del_id = []//移除二级id列表
    //请求点位数据
    _ajax.get('/quote/commonality-title',{
        id:$stateParams.id
    },function (res) {
        console.log(res);
        $scope.total_count = res.count
        $scope.one_title = res.list.one_title
        for(let [key,value] of $scope.one_title.entries()){
            for(let [key1,value1] of res.list.two_title.entries()){
                let index = res.list.two_title.findIndex(function (item1) {
                    return item1.pid == value.id
                })
                if(index!=-1){
                    if(value1.pid == value.id){
                        if(value.two_title == undefined){
                            value.two_title = [value1]
                        }else{
                            value.two_title.push(value1)
                        }
                    }
                }else{
                    value.two_title = [{
                        title:'',
                        count:'',
                        differentiate:1
                    }]
                }
            }
        }
        console.log($scope.one_title);
    })
    //添加一级标题
    $scope.addOneTitle = function () {
        $scope.one_title.push({
            title:'',
            differentiate:'1'
        })
    }
    //移除一级标题
    $scope.removeOneTitle = function (item,index) {
        if(item.id == undefined){
            $scope.one_title.splice(index,1)
        }else{
            _ajax.post('/quote/commonality-title-add',{
                del_id:item.id
            },function (res) {
                console.log(res);
                //请求点位数据
                _ajax.get('/quote/commonality-title',{
                    id:$stateParams.id
                },function (res) {
                    console.log(res);
                    $scope.total_count = res.count
                    $scope.one_title = res.list.one_title
                    for(let [key,value] of $scope.one_title.entries()){
                        for(let [key1,value1] of res.list.two_title.entries()){
                            let index = res.list.two_title.findIndex(function (item1) {
                                return item1.pid == value.id
                            })
                            if(index!=-1){
                                if(value1.pid == value.id){
                                    if(value.two_title == undefined){
                                        value.two_title = [value1]
                                    }else{
                                        value.two_title.push(value1)
                                    }
                                }
                            }else{
                                value.two_title = [{
                                    title:'',
                                    count:'',
                                    differentiate:1
                                }]
                            }
                        }
                    }
                    $scope.total_count.count -= +item.count
                    console.log($scope.one_title);
                })
            })
        }
    }
    //添加二级标题
    $scope.addTwoTitle = function (index) {
        $scope.one_title[index].two_title.push({
            title:'',
            count:'',
            differentiate:1
        })
    }
    //移除二级标题
    $scope.removeTwoTitle = function (parent_index,index) {
        if($scope.one_title[parent_index].two_title[index].id!=undefined){
            $scope.del_id.push($scope.one_title[parent_index].two_title[index].id)
        }
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
                edit_id:item.id,
                title:item.title
            }
        }
        console.log(obj);
        if(item.title!=''){
            _ajax.post('/quote/commonality-title-add',{
                one_title:obj
            },function (res) {
                console.log(res);
                //请求点位数据
                _ajax.get('/quote/commonality-title',{
                    id:$stateParams.id
                },function (res) {
                    console.log(res);
                    $scope.total_count = res.count
                    $scope.one_title = res.list.one_title
                    for(let [key,value] of $scope.one_title.entries()){
                        for(let [key1,value1] of res.list.two_title.entries()){
                            let index = res.list.two_title.findIndex(function (item1) {
                                return item1.pid == value.id
                            })
                            if(index!=-1){
                                if(value1.pid == value.id){
                                    if(value.two_title == undefined){
                                        value.two_title = [value1]
                                    }else{
                                        value.two_title.push(value1)
                                    }
                                }
                            }else{
                                value.two_title = [{
                                    title:'',
                                    count:'',
                                    differentiate:1
                                }]
                            }
                        }
                    }
                    console.log($scope.one_title);
                })
            })
        }
    }
    //改变二级点位
    $scope.changeCount = function (parent_index) {
        $scope.one_title[parent_index].count = 0
        $scope.total_count.count = 0
        for(let [key,value] of $scope.one_title[parent_index].two_title.entries()){

            $scope.one_title[parent_index].count += isNaN(value.count)?0:+value.count
        }
        for(let [key,value] of $scope.one_title.entries()){
            $scope.total_count.count += isNaN(value.count)?0:+value.count
        }
        console.log($scope.one_title);
    }
    //保存二级标题
    $scope.saveTwoTitle = function (valid) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        let arr = [],arr1 = [],obj = {}
        //获取一级总点位
        for(let [key,value] of $scope.one_title.entries()){
            arr.push({
                two_id:value.id,
                count:0,
                title:value.title
            })
        }
        //获取二级数据
        for(let [key,value] of $scope.one_title.entries()){
            for(let [key1,value1] of value.two_title.entries()){
                for(let [key2,value2] of arr.entries()){
                    if(value.id == value2.two_id){
                        value2.count += +value1.count
                    }
                }
                if(value1.id!=undefined){
                    arr1.push({
                        edit_id:value1.id,
                        count:value1.count,
                        title:value1.title
                    })
                }else{
                    arr1.push({
                        id:value.id,
                        count:value1.count,
                        title:value1.title
                    })
                }
            }
        }
        //传参数据
        if($scope.del_id.length!=0){
            obj = {
                two_title: arr1,
                del_id: $scope.del_id,
                count: {
                    id: $scope.total_count.id,
                    count: $scope.total_count.count,
                },
                two_count:arr
            }
        }else{
            obj = {
                two_title: arr1,
                count: {
                    id: $scope.total_count.id,
                    count: $scope.total_count.count
                },
                two_count:arr
            }
        }
        if(valid){
            _ajax.post('/quote/commonality-title-two-add',obj,function (res) {
                console.log(res);
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            })
        }else{
            $scope.submitted = true
        }
    }
    //返回前一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})