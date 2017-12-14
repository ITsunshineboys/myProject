/**
 * Created by tiger on 2017/10/25/025.
 */
app.controller('commodity_deleted', ['_ajax','$scope', '$stateParams','$http', function (_ajax, $scope, $stateParams,$http) {
    $scope.storeid = $stateParams.id;
    let sortway = "online_time"; //默认按上架时间降序排列
    /*默认参数配置*/
    $scope.params = {
        status: 3,     //已删除
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
        {name: '项目经理采购价格', value: true},
        {name: '设计师采购价格', value: false},
        {name: '库存', value: true},
        {name: '销量', value: true},
        {name: '状态', value: true},
        {name: '删除时间', value: true},
        {name: '操作人员', value: true},
        {name: '图片', value: true},
        {name: '详情', value: true},
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