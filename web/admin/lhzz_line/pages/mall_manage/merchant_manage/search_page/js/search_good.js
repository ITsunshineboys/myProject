/**
 * Created by Administrator on 2018/1/5/005.
 */
app.controller('searchGood', ['$rootScope', '$scope', '_ajax', function ($rootScope, $scope, _ajax){
    let sortway = "online_time"; //默认按上架时间降序排列
    /*默认参数配置*/
    $scope.params = {
        status: '4',     //全部
        page: 1,
        'sort[]': 'sold_number' + ':3',
        keyword: '',
    }

    $scope.basic = {
        keyword : ''  //输入框搜索值初始化
    }

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tableList();
        }
    }

    /*排序按钮样式控制*/
    $scope.sortStyleFunc = () => {
        return $scope.params['sort[]'].split(':')[1]
    }

    /*表格Menu切换 开始*/
    $scope.menu_list = [
        {name: '商品编号', value: true},
        {name: '商品名称', value: true},
        {name: '供货价格', value: true},
        {name: '市场价格', value: false},
        {name: '平台价格', value: true},
        {name: '装修公司采购价格', value: true},
        {name: '项目经理采购价格', value: false},
        {name: '设计师采购价格', value: false},
        {name: '库存', value: false},
        {name: '销量', value: false},
        {name: '状态', value: true},
        {name:  '操作人员',value:true},
        {name: '图片', value: true},
        {name: '原因及备注', value: true},
        {name: '详情', value: true},
        {name: '操作', value: true},
    ]
    /*表格Menu切换 结束*/

    // 商品状态筛选
    $scope.$watch('params.status', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
            $scope.params['sort[]'] = 'sold_number:3';       // 销量排序
            $scope.pageConfig.currentPage = 1;
            tableList();
    });



    /*本月销量排序*/
    $scope.sortVolumn =  () => {
        $scope.pageConfig.currentPage = 1;
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'sold_number:3' ? 'sold_number:4' : 'sold_number:3';
        tableList();
    }


    /*搜索*/
    $scope.search = function () {
        $scope.pageConfig.currentPage
        tableList();
    }

// =======================已上架===========================================

    let tempongoodid;  //单个商品id

    /*单个商品下架*/
    $scope.singlegoodOffline = function (id) {
        tempongoodid = id;
        $scope.offline_reason = '';
    }

    /*单个商品确认下架*/
    $scope.sureGoodOffline = function () {
        let data = {id: Number(tempongoodid), offline_reason:$scope.offline_reason||''};
        _ajax.post('/mall/goods-status-toggle',data,function (res) {
            $scope.offline_reason = '';
            $scope.pageConfig.currentPage = 1;
            $scope.basic.keyword = '';
            tableList()
        })
    }

    /*单个商品取消下架*/
    $scope.cancelSingleOffline = function () {
        $scope.offline_reason = '';
    }

// =======================已下架============================================
    let tempoffgoodid;  //单个商品id

    /*单个商品上架*/
    $scope.singlegoodOnline = function (id) {
        tempoffgoodid = id;
    }

    /*单个商品确认上架*/
    $scope.sureGoodOnline = function () {
        _ajax.post('/mall/goods-status-toggle',{id: Number(tempoffgoodid)},function (res) {
            /*由于某些原因不能上架*/
            if (res.code != 200) {
                // console.log(res)
                $('#up_shelves_modal').modal("hide");
                $("#up_not_shelves_modal").modal("show")
                $scope.cantonline = res.msg;
            } else {
                /*可以上架*/
                $('#up_shelves_modal').modal("hide");
                $scope.pageConfig.currentPage = 1;
                $scope.basic.keyword = '';
                tableList();
            }
        })
    }

    /*不能上架 确认*/
    $scope.sureCantOnline = function () {
        $scope.cantonlinemodal = ""
    }

    /*显示下架原因*/
    $scope.showOffReason = (obj) => {
        $scope.offreason = obj;
    }


// =======================等待上架===========================================
    /*更新审核备注*/
    $scope.checkReason = function (id,reason) {
        checkId = id;
        $scope.lastreason = reason;
    }


    /*确认更新审核备注*/
    $scope.sureCheckReason = function () {
        let data  = {id:Number(checkId),reason:$scope.lastreason||''};
        _ajax.post('/mall/goods-reason-reset',data,function (res) {
            tableList();
        })
    }



    let tempwaitgoodid;  //单个商品id


    /*等待上架 单个上架*/
    $scope.waitToOnline = function (id) {
        tempwaitgoodid = id;

    }

    /*等待上架 单个确认上架*/
    $scope.sureWaitToOnline = function () {
        _ajax.post('/mall/goods-status-toggle',{id: tempwaitgoodid},function (res) {
            /*由于某些原因不能上架*/
            if (res.code != 200) {
                // console.log(res)
                $('#waitup_shelves_modal').modal("hide");
                $("#waitup_not_shelves_modal").modal("show")
                $scope.waitcantonline = res.msg;
            } else {
                /*可以上架*/
                $('#waitup_shelves_modal').modal("hide");
                $scope.pageConfig.currentPage = 1;
                $scope.basic.keyword = '';
                tableList()
            }
        })
    }


    /*列表数据获取*/
    function tableList() {
        $scope.params.keyword = $scope.basic.keyword;
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/mall/goods-list-search',$scope.params,function (res) {
            $scope.tabledetail = res.data.goods_list_admin.details;
            $scope.pageConfig.totalItems = res.data.goods_list_admin.total;
        })
    }
}])