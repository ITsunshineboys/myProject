let cla_mag = angular.module("clamagModule", []);
cla_mag.controller("cla_mag_tabbar", function ($scope, $http, $stateParams) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };

    let singleoffid;   //单个下架分类id
    let singleonid;    //单个上架分类id

    /*默认参数*/
    $scope.params = {
        status: 1, //已上架
        pid: 0,   //父分类id
        page: 1,  //当前页数
        'sort[]': "online_time:3" //排序规则 默认按上架时间降序排列
    }
    /*全选ID数组*/
    $scope.table = {
        roles: [],
    };
    /*已上架单个下架初始化下架原因*/
    $scope.offlinereason = '';
    /*已上架批量下架初始化下架原因*/
    $scope.piliangofflinereason = '';
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

    /*选项卡切换方法*/
    $scope.tabFunc = (obj) => {
        $scope.onsale_flag = false;
        $scope.offsale_flag = false;
        $scope[obj] = true;
        initFunc(obj);
    }

    /*根据参数执行选项卡方法*/
    if ($stateParams.offsale_flag) {
        $scope.tabFunc('offsale_flag');
    } else {
        $scope.tabFunc('onsale_flag');
    }


    /*排序按钮样式控制*/
    $scope.sortStyleFunc = () => {
        return $scope.params['sort[]'].split(':')[1]
    }


    firstClass();
    /*分类选择一级下拉框*/
    function firstClass() {
        $http({
            method: "get",
            url: baseUrl+"/mall/categories-manage-admin",
        }).then((response) => {
            $scope.firstclass = response.data.data.categories;
            $scope.dropdown.firstselect = response.data.data.categories[0].id;
        })
    }

    /*分类选择二级下拉框*/
    function subClass(obj) {
        $http({
            method: "get",
            url: baseUrl+"/mall/categories-manage-admin",
            params: {pid: obj}
        }).then(function (response) {
            $scope.secondclass = response.data.data.categories;
            $scope.dropdown.secselect = response.data.data.categories[0].id;
        })
    }


    /*列表初始化方法*/
    function initFunc(obj) {
        $scope.table.roles.length = 0;
        let tab = obj == 'onsale_flag' ? 1 : 0;
        let sortflag = obj == 'onsale_flag' ? "online_time:3":"offline_time:3"
        $scope.params = {
            status: tab, //已上架
            pid: 0,   //父分类id
            page: 1,  //当前页数
            'sort[]': sortflag //排序规则
        }
        $scope.dropdown = {
            firstselect: 0,
            secselect: 0
        }
        tableList();
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
        $scope.params['sort[]'] = $scope.onsale_flag? 'online_time:3':'offline_time:3'
        subClass(value);
        $scope.params.pid = value;
        tableList()
    });


    $scope.$watch('dropdown.secselect', function (value, oldValue) {
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
        $http({
            method: "get",
            url: baseUrl+"/mall/category-list-admin",
            params: $scope.params,
        }).then(function (res) {
            $scope.pageConfig.totalItems = res.data.data.category_list_admin.total;
            $scope.listdata = res.data.data.category_list_admin.details;
        })
    }


    /*全选*/
    $scope.checkAll = function () {
        !$scope.table.roles.length ? $scope.table.roles = $scope.listdata.map(function (item) {
            return item.id;
        }) : $scope.table.roles.length = 0;
    };


    /*-----------------------已上架操作--------------------*/

    /*已上架单个分类下架种类统计*/
    $scope.singleOffline = function (id) {
        singleoffid = id;
    }

    /*单个确认下架*/
    $scope.sureOffline = function () {
        let url = baseUrl+"/mall/category-status-toggle";
        let data = {id: singleoffid, offline_reason: $scope.offlinereason};
        $http.post(url, data, config).then(function (res) {
            $scope.offlinereason = '';
            $scope.pageConfig.currentPage = 1;
            tableList();
        })
    }

    /*单个取消下架*/
    $scope.cancelOffline = function () {
        $scope.offlinereason = '';
    }

    /*确认批量下架*/
    $scope.sureBatchOffline = function () {
        let batchoffids = $scope.table.roles.join(',');
        let url = baseUrl+"/mall/category-disable-batch";
        let data = {ids: batchoffids, offline_reason: $scope.batchoffline_reason};
        $http.post(url, data, config).then(function (res) {
            $scope.batchoffline_reason = '';
            $scope.pageConfig.currentPage = 1;
            tableList()
        })
    }

    /*取消批量下架*/
    $scope.cancelBatchOffline = function () {
        $scope.batchoffline_reason = '';

    }

    /*-----------------------已下架操作--------------------*/

    /*已下架单个分类上架种类统计*/
    $scope.singleOnline = function (id) {
        singleonid = id;
    }


    /*单个确认上架*/
    $scope.sureOnline = function () {
        let url = baseUrl+"/mall/category-status-toggle";
        let data = {id: singleonid};
        $http.post(url, data, config).then(function (res) {
            $scope.pageConfig.currentPage = 1;
            tableList();
        })
    }


    /*单个取消上架无操作*/
    $scope.surepiliangonline = function () {
        $scope.piliangonids = $scope.table.roles.join(',');
        let url = baseUrl+"/mall/category-enable-batch";
        let data = {ids: $scope.piliangonids};
        $http.post(url, data, config).then(function (res) {
            $scope.pageConfig.currentPage = 1;
            tableList()
        })
    }

    /*重设下架原因*/
    $scope.resetOffReason = function (id, offline_reason) {
        $scope.resetid = Number(id);
        $scope.original_reason  = offline_reason;
    }

    $scope.surereset = function () {
        let url = baseUrl+"/mall/category-offline-reason-reset";
        let data = {id: $scope.resetid, offline_reason: $scope.original_reason};
        $http.post(url, data, config).then(function (res) {
            if(res.data.code==200){
                $scope.original_reason = '';
                tableList()
            }
        })
    }

    $scope.cancelReset = function () {
        $scope.original_reason= '';
    }
})





