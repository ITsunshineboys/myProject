app.controller('class_offline', ['$scope', '$stateParams', '_ajax', function ($scope, $stateParams, _ajax) {
    let singleonid;    //单个上架分类id
    /*默认参数*/
    $scope.params = {
        status: 0, //已上架
        pid: 0,   //父分类id
        page: 1,  //当前页数
        'sort[]': "online_time:3" //排序规则 默认按下架时间降序排列
    }
    /*全选ID数组*/
    $scope.table = {
        roles: [],
    };
    /*已上架单个下架初始化下架原因*/
    $scope.offlinereason = '';

    /*分类选择下拉框初始化*/
    $scope.dropdown = {
        firstselect: 0,
        secselect: 0
    }

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            $scope.table.roles = [];
            tableList();
        }
    }


    /*排序按钮样式控制*/
    $scope.sortStyleFunc = () => {
        return $scope.params['sort[]'].split(':')[1]
    }

    firstClass();
    /*分类选择一级下拉框*/
    function firstClass() {
        _ajax.get('/mall/categories-manage-admin',{},function (res) {
            $scope.firstclass = res.data.categories;
            $scope.dropdown.firstselect = res.data.categories[0].id;
        })
    }

    /*分类选择二级下拉框*/
    function subClass(obj) {
        _ajax.get('/mall/categories-manage-admin',{pid:obj},function (res) {
            $scope.secondclass = res.data.categories;
            $scope.dropdown.secselect = res.data.categories[0].id;
        })
    }


    // 时间排序
    $scope.sortTime = function () {
        if($scope.onsale_flag){
            $scope.params['sort[]'] = $scope.params['sort[]'] == 'online_time:3' ? 'online_time:4' : 'online_time:3';
        }else {
            $scope.params['sort[]'] = $scope.params['sort[]'] == 'offline_time:3' ? 'offline_time:4' : 'offline_time:3';
        }
        $scope.table.roles.length = 0;
        $scope.pageConfig.currentPage = 1;
        tableList();
    }


    /*分类筛选方法*/
    $scope.$watch('dropdown.firstselect', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        $scope.params['sort[]'] = $scope.onsale_flag? 'online_time:3':'offline_time:3'
        subClass(value);
        $scope.params.pid = value;
        tableList()
    });


    $scope.$watch('dropdown.secselect', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        $scope.params['sort[]'] = $scope.onsale_flag? 'online_time:3':'offline_time:3'
        if (value == oldValue) {
            return
        }
        if (value) {
            $scope.params.pid = value;
            tableList()
        } else {
            //二级分类id为0
            $scope.params.pid = $scope.dropdown.firstselect;
            tableList()
        }
    });


    /*列表数据获取*/
    function tableList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/mall/category-list-admin',$scope.params,function (res) {
            $scope.pageConfig.totalItems = res.data.category_list_admin.total;
            $scope.listdata = res.data.category_list_admin.details;
        })
    }


    /*全选*/
    $scope.checkAll = function () {
        !$scope.table.roles.length ? $scope.table.roles = $scope.listdata.map(function (item) {
            return item.id;
        }) : $scope.table.roles.length = 0;
    };


    /*-----------------------已下架操作--------------------*/

    /*已下架单个分类上架种类统计*/
    $scope.singleOnline = function (id) {
        singleonid = id;
    }


    /*单个确认上架*/
    $scope.sureOnline = function () {
        _ajax.post('/mall/category-status-toggle',{id: singleonid},function (res) {
            $scope.pageConfig.currentPage = 1;
            tableList();
        })
    }


    /*批量上架*/
    $scope.surepiliangonline = function () {
        $scope.piliangonids = $scope.table.roles.join(',');
        _ajax.post('/mall/category-enable-batch',{ids: $scope.piliangonids},function (res) {
            $scope.pageConfig.currentPage = 1;
            tableList();
        })
    }

    /*重设下架原因*/
    $scope.resetOffReason = function (id, offline_reason) {
        $scope.resetid = Number(id);
        $scope.original_reason  = offline_reason;
    }

    $scope.surereset = function () {
        let data = {id: $scope.resetid, offline_reason: $scope.original_reason};
        _ajax.post('/mall/category-offline-reason-reset', data,function (res) {
            $scope.original_reason = '';
            tableList()
        })
    }

    $scope.cancelReset = function () {
        $scope.original_reason= '';
    }

}]);
