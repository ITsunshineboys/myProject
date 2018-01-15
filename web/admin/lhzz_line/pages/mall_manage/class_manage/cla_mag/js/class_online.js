app.controller('class_online', ['$scope', '$stateParams', '_ajax', function ($scope, $stateParams, _ajax) {
    let singleoffid;   //单个下架分类id

    /*默认参数*/
    $scope.params = {
        status: 1, //已上架
        pid: 0,   //父分类id
        page: 1,  //当前页数
        keyword: '', //关键字
        'sort[]': "online_time:3" //排序规则 默认按上架时间降序排列
    }
    /*全选ID数组*/
    $scope.table = {
        roles: [],
        keyword: ''
    };


    /*下架原因初始化*/
    $scope.offlinereason = {
        single:'',
        batch:''
    }

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
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'online_time:3' ? 'online_time:4' : 'online_time:3';
        $scope.table.roles.length = 0;
        $scope.pageConfig.currentPage = 1;
        tableList();
    }


    /*分类筛选方法*/
    $scope.$watch('dropdown.firstselect', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        $scope.params['sort[]'] = 'online_time:3';
        subClass(value);
        $scope.params.pid = value;
        $scope.table.keyword = '';
        $scope.params.keyword = '';
        $scope.pageConfig.currentPage = 1;
        tableList()
    });


    $scope.$watch('dropdown.secselect', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        $scope.params['sort[]'] = 'online_time:3';
        if (value == oldValue) {
            return
        }
        if (value) {
            $scope.params.pid = value;
            $scope.table.keyword = '';
            $scope.params.keyword = '';
            $scope.pageConfig.currentPage = 1;
            tableList()
        } else {
            //二级分类id为0
            $scope.params.pid = $scope.dropdown.firstselect;
            $scope.table.keyword = '';
            $scope.params.keyword = '';
            $scope.pageConfig.currentPage = 1;
            tableList()
        }
    });

    /*搜索*/
    $scope.search = function () {
        $scope.table.roles.length = 0;
        $scope.dropdown.firstselect = 0;
        $scope.params['sort[]'] = 'online_time:3';
        $scope.pageConfig.currentPage = 1;
        $scope.params.keyword = $scope.table.keyword;
        tableList();
    }


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



    /*已上架单个分类下架种类统计*/
    $scope.singleOffline = function (id) {
        singleoffid = id;
    }

    /*单个确认下架*/
    $scope.sureOffline = function () {
        let data = {id: singleoffid, offline_reason: $scope.offlinereason.single};
        _ajax.post('/mall/category-status-toggle',data,function (res) {
            $scope.offlinereason.single = '';
            $scope.pageConfig.currentPage = 1;
            tableList();
        })
    }

    /*单个取消下架*/
    $scope.cancelOffline = function () {
        $scope.offlinereason.single = '';
    }

    /*确认批量下架*/
    $scope.sureBatchOffline = function () {
        let batchoffids = $scope.table.roles.join(',');
        let data = {ids: batchoffids, offline_reason: $scope.offlinereason.batch};
        _ajax.post('/mall/category-disable-batch',data,function (res) {
            $scope.offlinereason.batch = '';
            $scope.pageConfig.currentPage = 1;
            tableList()
        })
    }

    /*取消批量下架*/
    $scope.cancelBatchOffline = function () {
        $scope.offlinereason.batch = '';
    }
}]);

