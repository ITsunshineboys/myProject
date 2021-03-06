/**
 * Created by Administrator on 2017/10/25/025.
 */
app.controller('commodity_online', ['_ajax','$scope', '$stateParams','$http', function (_ajax,$scope, $stateParams,$http) {
    let sortway = "online_time"; //默认按上架时间降序排列
    $scope.storeid = $stateParams.id //商家id
    /*默认参数配置*/
    $scope.params = {
        status: 2,     //已上架
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
        $scope.params['sort[]'] == 'online_time:3'
    }


    /*表格Menu切换 开始*/
    $scope.menu_list = [
        {name: '商品编号', value: true},
        {name: '商品名称', value: true},
        {name: '供货价格', value: true},
        {name: '市场价格', value: false},
        {name: '平台价格', value: true},
        {name: '装饰公司采购价格', value: false},
        {name: '项目经理采购价格', value: false},
        {name: '设计师采购价格', value: false},
        {name: '库存', value: true},
        {name: '销量', value: true},
        {name: '状态', value: true},
        {name: '上架时间', value: true},
        {name:  '操作人员',value:true},
        {name: '图片', value: true},
        {name: '详情', value: true},
        {name: '操作', value: true},
    ]
    /*表格Menu切换 结束*/



    /*本月销量排序*/
    $scope.sortVolumn =  () => {
        $scope.table.roles = [];
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
        $scope.table.roles = [];
        $scope.pageConfig.currentPage = 1;
        $scope.volumn_ascorder = false;
        $scope.volumn_desorder = false;
        sortway = 'online_time';
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'online_time:3' ? 'online_time:4' : 'online_time:3';
        if($scope.params['sort[]']=='online_time:3'){
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
        $scope.table.roles.length !==  $scope.tabledetail.length ? $scope.table.roles = $scope.tabledetail.map(function (item) {
            return item.id;
        }) : $scope.table.roles.length = 0;
        $scope.keyword = '';
        $scope.params.keyword = '';
    };


    let tempoffgoodid;  //单个商品id

    /*单个商品下架*/
    $scope.singlegoodOffline = function (id) {
        tempoffgoodid = id;
        $scope.offline_reason = '';
    }

    /*单个商品确认下架*/
    $scope.sureGoodOffline = function () {
        let data = {id: Number(tempoffgoodid), offline_reason:$scope.offline_reason||''};
        _ajax.post('/mall/goods-status-toggle',data,function (res) {
            $scope.offline_reason = '';
            $scope.pageConfig.currentPage = 1;
            $scope.keyword = '';
            $scope.params.keyword = '';
            tableList()
        })
    }

    /*单个商品取消下架*/
    $scope.cancelSingleOffline = function () {
        $scope.offline_reason = '';
    }


    //批量下架
    $scope.batchOffline = () => {
        $scope.batch_offlinereason = '';
    }

    /*确认批量下架*/
    $scope.surepiliangoffline = function () {
        let batchoffids = $scope.table.roles.join(',');
        let data = {ids:batchoffids, offline_reason: $scope.batch_offlinereason};
        _ajax.post('/mall/goods-disable-batch',data,function (res) {
            $scope.batch_offlinereason = ''
            $scope.pageConfig.currentPage = 1;
            $scope.keyword = '';
            $scope.params.keyword = '';
            tableList()
        })
    }

    /*取消批量下架*/
    $scope.cancelplliangoffline = function () {
        $scope.batch_offlinereason = '';
    }


    /*列表数据获取*/
    function tableList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/mall/goods-list-admin',$scope.params,function (res) {
            $scope.tabledetail = res.data.goods_list_admin.details;
            $scope.pageConfig.totalItems = res.data.goods_list_admin.total;
        })
    }
}]);
;