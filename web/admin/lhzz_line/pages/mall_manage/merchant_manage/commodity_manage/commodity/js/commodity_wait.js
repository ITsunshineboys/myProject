/**
 * Created by Administrator on 2017/10/25/025.
 */
app.controller('commodity_wait', ['$scope', '$stateParams','$http', function ($scope, $stateParams,$http) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    }

    $scope.storeid = $stateParams.id;
    let sortway = "publish_time"; //默认按创建时间降序排列
    let checkId;
    /*默认参数配置*/
    $scope.params = {
        status: 1,     //等待上架
        page: 1,
        'sort[]': sortway + ':3',
        keyword: '',
        supplier_id: +$stateParams.id
    }

//     /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            $scope.table.roles = [];
            tableList();
        }
    }

    /*全选ID数组*/
    $scope.table = {
        roles: [],
    };

    sortReset();


    /*默认排序方法*/
    function sortReset() {
        /*本月销量默认排序*/
        $scope.volumn_desorder = false;
        $scope.volumn_ascorder = false;
        /*本月销量默认排序*/
        $scope.time_ascorder = false;
        $scope.time_desorder = true;
        $scope.params['sort[]'] == 'publish_time:3'
    }


    /*表格Menu切换 开始*/
    $scope.menu_list = [
        {name: '商品编号', value: true},
        {name: '商品名称', value: true},
        {name: '供货价格', value: true},
        {name: '市场价格', value: false},
        {name: '平台价格', value: false},
        {name: '装饰公司采购价格', value: true},
        {name: '项目经理采购价格', value: true},
        {name: '设计师采购价格', value: false},
        {name: '库存', value: false},
        {name: '销量', value: true},
        {name: '状态', value: true},
        {name: '发布时间', value: true},
        {name: '操作人员', value: true},
        {name: '图片', value: true},
        {name: '审核备注', value: true},
        {name: '详情', value: true},
        {name: '操作', value: true},
    ]
    /*表格Menu切换 结束*/



    /*本月销量排序*/
    $scope.sortVolumn =  () => {
        $scope.table.roles.length = 0;
        $scope.pageConfig.currentPage = 1;
        $scope.time_ascorder = false;
        $scope.time_desorder = false;
        sortway = 'sold_number';
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'sold_number:3' ? 'sold_number:4' : 'sold_number:3';
        if($scope.params['sort[]']=='sold_number:3'){
            $scope.volumn_desorder = true;
            $scope.volumn_ascorder = false;
        }else{
            $scope.volumn_desorder = false;
            $scope.volumn_ascorder = true;
        }
        $scope.pageConfig.currentPage = 1;
        tableList();
    }


    //上架时间排序
    $scope.sortTime = () => {
        $scope.table.roles.length = 0;
        $scope.pageConfig.currentPage = 1;
        $scope.volumn_ascorder = false;
        $scope.volumn_desorder = false;
        sortway = 'publish_time';
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'publish_time:3' ? 'publish_time:4' : 'publish_time:3';
        if($scope.params['sort[]']=='publish_time:3'){
            $scope.time_desorder = true;
            $scope.time_ascorder = false;
        }else{
            $scope.time_desorder = false;
            $scope.time_ascorder = true;
        }
        $scope.pageConfig.currentPage = 1;
        tableList();
    }


    /*搜索*/
    $scope.search = function () {
        sortReset();
        $scope.table.roles.length = 0;
        $scope.params.keyword = $scope.keyword,
            $scope.pageConfig.currentPage = 1;
        tableList();
    }


    /*全选*/
    $scope.checkAll = function () {
        !$scope.table.roles.length ? $scope.table.roles = $scope.tabledetail.map(function (item) {
            return item.id;
        }) : $scope.table.roles.length = 0;
        $scope.keyword = '';
        $scope.params.keyword = '';
    };


    let tempwaitgoodid;  //单个商品id


    /*等待上架 单个上架*/
    $scope.waitToOnline = function (id) {
        tempwaitgoodid = id;

    }

    /*等待上架 单个确认上架*/
    $scope.sureWaitToOnline = function () {
        let url = baseUrl+"/mall/goods-status-toggle";
        let data = {id: tempwaitgoodid};
        $http.post(url, data, config).then(function (res) {
            console.log(res);
            /*由于某些原因不能上架*/
            if (res.data.code != 200) {
                // console.log(res)
                $('#waitup_shelves_modal').modal("hide");
                $("#waitup_not_shelves_modal").modal("show")
                $scope.waitcantonline = res.data.msg;
            } else {
                /*可以上架*/
                $('#waitup_shelves_modal').modal("hide");
                $scope.pageConfig.currentPage = 1;
                $scope.keyword = '';
                $scope.params.keyword = '';
                tableList()
            }
        })
    }


    /*等待上架确认批量上架*/
    $scope.surewaitonline = function () {
        let batchids = $scope.table.roles.join(',');
        let url = baseUrl+"/mall/goods-enable-batch";
        let data = {ids: batchids};
        $http.post(url, data, config).then(function (res) {
            console.log(res);
            /*由于某些原因不能上架*/
            if (res.data.code != 200) {
                $('#allwaitonline_modal').modal("hide");
                $("#waitup_not_shelves_modal").modal("show")
                $scope.waitcantonline = res.data.msg;
            } else {
                /*可以上架*/
                $('#allwaitonline_modal').modal("hide");
                $scope.pageConfig.currentPage = 1;
                $scope.keyword = '';
                $scope.params.keyword = '';
                tableList()
            }
        })
    }

    /*等待上架 取消批量上架*/
    $scope.cancelWaitOnline = function () {
       $scope.table.roles.length = 0
    }


    /*更新审核备注*/
    $scope.checkReason = function (id,reason) {
        checkId = id;
        $scope.lastreason = reason;
    }


    /*确认更新审核备注*/
    $scope.sureCheckReason = function () {
        console.log(123456);
        let url = baseUrl+"/mall/goods-reason-reset";
        let data  = {id:Number(checkId),reason:$scope.lastreason||''};
        $http.post(url,data,config).then((res)=>{
            console.log(res);
           tableList();
        })
    }


    /*列表数据获取*/
    function tableList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        $http({
            method: "get",
            url: baseUrl+"/mall/goods-list-admin",
            params: $scope.params,
        }).then(function (res) {
            console.log('等待上架'+res);
            $scope.tabledetail = res.data.data.goods_list_admin.details;
            $scope.pageConfig.totalItems = res.data.data.goods_list_admin.total;
        })
    }


}]);
;