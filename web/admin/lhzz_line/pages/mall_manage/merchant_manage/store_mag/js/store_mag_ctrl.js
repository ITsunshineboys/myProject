/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*商家管理*/
angular.module("storemagModule", []).controller("store_mag", function ($scope, $http,$rootScope,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家管理',
    }];

    let tempshop_no;
    let sortway = 'sales_amount_month';
    firstClass();
    $scope.storetype_arr = [{storetype: "全部", id: -1}, {storetype: "旗舰店", id: 0}, {
        storetype: "专卖店", id: 1}, {storetype: "专营店", id: 2},{storetype: "自营店", id: 3}] //店铺类型
    $scope.status_arr = [{status: "全部", id: -1}, {status: "正常营业", id: 1}, {status: "已关闭", id: 0}]; //状态
    $scope.firstselect = 0;
    $scope.keyword = '';
    $scope.params = {
        category_id: 0, //分类id
        shop_type: -1,  //店铺类型
        status: -1,     //店铺状态
        keyword: "",     //关键词
        page: 1,          //当前页
        'sort[]': sortway + ":3" //默认按销售额降序
    }
    sortReset();

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tableList();
        }
    }

    /*已关闭状态样式*/
    $scope.isClosed = function (obj) {
        return obj == "已关闭";
    }

    /*分类选择下拉框*/
    //一级下拉框
    function firstClass() {
        _ajax.get('/mall/categories-manage-admin',{},function (res) {
            $scope.firstclass = res.data.categories;
            $scope.firstselect = res.data.categories[0].id;
        })
    }

    //二级下拉框
    $scope.subClass = function (obj) {
        _ajax.get('/mall/categories-manage-admin',{pid: obj},function (res) {
            $scope.secondclass = res.data.categories;
            $scope.secselect = res.data.categories[0].id;
        })
    }

    //三级下拉框
    $scope.thirdClass = function (obj) {
        _ajax.get('/mall/categories-manage-admin',{pid: obj},function (res) {
            $scope.thirdclass = res.data.categories;
            $scope.thirdselect = res.data.categories[0].id;
        })
    }

    /*筛选-下拉*/
    $scope.$watch('firstselect', function (newVal, oldVal) {
        if(newVal == oldVal) return;
        sortReset();
        $scope.keyword = '';
        $scope.params.keyword = '';
        $scope.pageConfig.currentPage = 1;
        $scope.params.category_id = +newVal;
        tableList();
    });

    $scope.$watch('secselect', function (newVal, oldVal) {
        if(newVal == oldVal) return;
        sortReset();
        $scope.keyword = '';
        $scope.params.keyword = '';
        $scope.pageConfig.currentPage = 1;
        if (!!newVal) {
            $scope.params.category_id = +newVal;
        }
        tableList();
    });

    $scope.$watch('thirdselect', function (newVal, oldVal) {
        if(newVal == oldVal) return;
        sortReset();
        $scope.keyword = '';
        $scope.params.keyword = '';
        $scope.pageConfig.currentPage = 1;
        if (!!newVal) {
            $scope.params.category_id = +newVal;
        }
        tableList();
    });


    /*筛选-店铺类型*/
    $scope.$watch('params.shop_type', function (newVal, oldVal) {
        if(newVal == oldVal) return;
        sortReset();
        $scope.keyword = '';
        $scope.pageConfig.currentPage = 1;
        tableList()
    });

    /*筛选-状态*/
    $scope.$watch('params.status', function (newVal, oldVal) {
        if(newVal == oldVal) return;
        sortReset();
        $scope.keyword = '';
        $scope.pageConfig.currentPage = 1;
        tableList();
    });

    // 本月销售额排序
    $scope.sortAmount = function () {
        $scope.pageConfig.currentPage = 1;
        $scope.volumn_ascorder = false;
        $scope.volumn_desorder = false;
        sortway = 'sales_amount_month';
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'sales_amount_month:3' ? 'sales_amount_month:4' : 'sales_amount_month:3';
        if($scope.params['sort[]']=='sales_amount_month:3'){
            $scope.amount_desorder = true;
            $scope.amount_ascorder = false;
        }else{
            $scope.amount_ascorder = true;
            $scope.amount_desorder = false;
        }
        $scope.pageConfig.currentPage = 1;
        tableList();
    }

    /*本月销量排序*/
    $scope.sortVolumn = function () {
        $scope.pageConfig.currentPage = 1;
        $scope.amount_desorder = false;
        $scope.amount_ascorder = false;
        sortway = 'sales_volumn_month';
        $scope.params['sort[]'] = $scope.params['sort[]'] == 'sales_volumn_month:3' ? 'sales_volumn_month:4' : 'sales_volumn_month:3';
        if($scope.params['sort[]']=='sales_volumn_month:3'){
            $scope.volumn_desorder = true;
            $scope.volumn_ascorder = false;
        }else{
            $scope.volumn_desorder = false;
            $scope.volumn_ascorder = true;
        }
        $scope.pageConfig.currentPage = 1;
        tableList();
    }

    /*搜索店铺*/
    $scope.searchStore = function () {
        sortReset();
        $scope.params.keyword = $scope.keyword,
            $scope.category_id = 0, //分类id
            $scope.shop_type = -1,  //店铺类型
            $scope.status = -1,     //店铺状态
            $scope.pageConfig.currentPage = 1;
        tableList();
    }


    /*列表数据获取*/
    function tableList() {
        $scope.params.keyword = $scope.keyword;
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/mall/supplier-list',$scope.params,function (res) {
            $scope.pageConfig.totalItems = res.data.supplier_list.total;
            $scope.stores = res.data.supplier_list.details;
        })
    }

    /*默认排序方法*/
    function sortReset() {
        /*本月销售额默认排序*/
        $scope.amount_desorder = true;
        $scope.amount_ascorder = false;
        /*本月销量默认排序*/
        $scope.volumn_ascorder = false;
        $scope.volumn_desorder = false;
        $scope.params['sort[]'] == 'sales_amount_month:3'
    }


    /*开店/闭店*/
    $scope.changeStatus = function (id, status) {
        $scope.storestatus = status;
        tempshop_no = id;
    }

    /*确认开店/闭店*/
    $scope.sureCloseStore = function () {
        tempshop_no = Number(tempshop_no)
        _ajax.post('/mall/supplier-status-toggle',{supplier_id: tempshop_no},function (res) {
            if(res.code==1037){
                $("#unblock_modal").modal('show');  //手动开启
            }else {
                $scope.pageConfig.currentPage = 1;
                tableList();
            }
        })
    }
});