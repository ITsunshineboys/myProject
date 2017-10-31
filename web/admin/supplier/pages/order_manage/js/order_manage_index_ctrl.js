/**
 * Created by Administrator on 2017/9/15/015.
 */
let ordermanage = angular.module("ordermanageModule", []);
ordermanage.controller("ordermanage_ctrl", function ($scope, $http, $stateParams, $state) {
    let time_type;
    let tabflag;
    let allTableInit = {
        all_flag: allInit,
        waitpay_flag: waitpayInit,
        finish_flag: finishInit,
        cancel_flag: cancelInit,
    }

    /*选项卡数字获取*/
    $http({
        method: "get",
        url: "http://test.cdlhzz.cn:888/order/get-order-num",
    }).then((res) => {
        $scope.listcount = res.data.data;
        if ($stateParams.tabflag) {
            $scope.tabFunc($stateParams.tabflag);
        } else {
            $scope.tabFunc('all_flag');
        }
    })

    /*选项卡切换方法*/
    $scope.tabFunc = (obj) => {
        $scope.all_flag = false;
        $scope.waitpay_flag = false;
        $scope.waitsend_flag = false;
        $scope.waitreceive_flag = false;
        $scope.finish_flag = false;
        $scope.cancel_flag = false;
        $scope[obj] = true;
        allTableInit[obj]();
    }


    /*请求参数*/
    $scope.params = {
        page: 1,                        // 当前页数
        time_type: 'all',               // 时间类型
        keyword: '',                    // 关键字查询
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
        sort_money: '',                  // 订单金额排序
        sort_time: 2,                  // 下单时间排序
        type: 'all'                  // 订单类型
    };

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tableList();
}
    }


    /*表格Menu切换 开始*/
    $scope.menu_list = [
        {name: '订单编号', value: true},
        {name: '商品编号', value: true},
        {name: '商品名称', value: true},
        {name: '订单金额', value: true},
        {name: '下单时间', value: true},
        {name: '用户', value: true},
        {name: '绑定手机', value: false},
        {name: '订单状态', value: true},
        {name: '异常', value: true},
        {name: '评论', value: true},
        {name: '详情', value: true},
        {name: '操作', value: true}
    ]
    /*表格Menu切换 结束*/

    /*全部列表*/
    function allInit() {
        $scope.menu_list = [
            {name: '订单编号', value: true},
            {name: '商品编号', value: true},
            {name: '商品名称', value: true},
            {name: '订单金额', value: true},
            {name: '下单时间', value: true},
            {name: '用户', value: true},
            {name: '绑定手机', value: false},
            {name: '订单状态', value: true},
            {name: '异常', value: true},
            {name: '评论', value: true},
            {name: '详情', value: true},
            {name: '操作', value: true}]


        /*参数初始化*/
        $scope.keyword = '';
        $scope.params = {
            page: 1,                        // 当前页数
            time_type: 'all',               // 时间类型
            keyword: '',                    // 关键字查询
            start_time: '',                 // 自定义开始时间
            end_time: '',                   // 自定义结束时间
            sort_money: '',                  // 订单金额排序
            sort_time: 2,                  // 下单时间排序
            type: 'all'                  // 订单类型
        };
        tableList();
    }

    /*待付款列表*/
    function waitpayInit() {
        $scope.menu_list = [
            {name: '订单编号', value: true},
            {name: '商品编号', value: true},
            {name: '商品名称', value: true},
            {name: '订单金额', value: true},
            {name: '下单时间', value: true},
            {name: '用户', value: false},
            {name: '绑定手机', value: false},
            {name: '订单状态', value: true},
            {name: '异常', value: false},
            {name: '评论', value: false},
            {name: '详情', value: true},
            {name: '操作', value: false}
        ]
        /*参数初始化*/
        $scope.keyword = '';
        $scope.params = {
            page: 1,                        // 当前页数
            time_type: 'all',               // 时间类型
            keyword: '',                    // 关键字查询
            start_time: '',                 // 自定义开始时间
            end_time: '',                   // 自定义结束时间
            sort_money: '',                  // 订单金额排序
            sort_time: 2,                  // 下单时间排序
            type: 'unpaid'                  // 订单类型
        };

        tableList();
    }

    /*已完成列表*/
    function finishInit() {
        $scope.menu_list = [
            {name: '订单编号', value: true},
            {name: '商品编号', value: true},
            {name: '商品名称', value: true},
            {name: '订单金额', value: true},
            {name: '下单时间', value: true},
            {name: '用户', value: true},
            {name: '绑定手机', value: false},
            {name: '订单状态', value: true},
            {name: '异常', value: false},
            {name: '评论', value: true},
            {name: '详情', value: true},
            {name: '操作', value: false}
        ]
        /*参数初始化*/
        $scope.keyword = '';
        $scope.params = {
            page: 1,                        // 当前页数
            time_type: 'all',               // 时间类型
            keyword: '',                    // 关键字查询
            start_time: '',                 // 自定义开始时间
            end_time: '',                   // 自定义结束时间
            sort_money: '',                  // 订单金额排序
            sort_time: 2,                  // 下单时间排序
            type: 'completed'                  // 订单类型
        };
        tableList();
    }

    /*已取消列表*/
    function cancelInit() {
        $scope.menu_list = [
            {name: '订单编号', value: true},
            {name: '商品编号', value: true},
            {name: '商品名称', value: true},
            {name: '订单金额', value: true},
            {name: '下单时间', value: true},
            {name: '用户', value: true},
            {name: '绑定手机', value: false},
            {name: '订单状态', value: true},
            {name: '异常', value: false},
            {name: '评论', value: false},
            {name: '详情', value: true},
            {name: '操作', value: false}
        ]
        /*参数初始化*/
        $scope.keyword = '';
        $scope.params = {
            page: 1,                        // 当前页数
            time_type: 'all',               // 时间类型
            keyword: '',                    // 关键字查询
            start_time: '',                 // 自定义开始时间
            end_time: '',                   // 自定义结束时间
            sort_money: '',                  // 订单金额排序
            sort_time: 2,                  // 下单时间排序
            type: 'cancel'                  // 订单类型
        };
        tableList();
    }



    // 时间筛选器
    $scope.$watch('params.time_type', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if (value == 'all' && $scope.params.keyword != '') {
            return
        }
        if (value != 'custom') {
            $scope.keyword = '';
            $scope.params.keyword = '';        // 关键字查询
            $scope.params.start_time = '';     // 自定义开始时间
            $scope.params.end_time = '';       // 自定义结束时间
            $scope.params.sort_money = '';      // 订单金额排序
            $scope.params.sort_time = 2;      // 下单时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });



    /*搜索*/
    $scope.search = () => {
        $scope.params.keyword = $scope.keyword;
        $scope.params.time_type = 'all';   // 时间类型
        $scope.params.start_time = '';     // 自定义开始时间
        $scope.params.end_time = '';       // 自定义结束时间
        $scope.params.sort_money = '';      // 订单金额排序
        $scope.params.sort_time = 2;      // 下单时间排序
        $scope.pageConfig.currentPage = 1;
        tableList()
        // defaultReset();
    }


    //自定义时间筛选
    // 开始时间
    $scope.$watch('params.start_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.end_time != '') {
            $scope.keyword = '';
            $scope.params.keyword = '';        // 关键字查询
            $scope.params.sort_money = '';      // 订单金额排序
            $scope.params.sort_time = 2;      // 下单时间排序
            $scope.pageConfig.currentPage = 1;
            tableList()
        }
    });

    // 结束时间
    $scope.$watch('params.end_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.start_time != '') {
            $scope.keyword = '';
            $scope.params.keyword = '';        // 关键字查询
            $scope.params.sort_money = '';      // 订单金额排序
            $scope.params.sort_time = 2;      // 下单时间排序
            $scope.pageConfig.currentPage = 1;
            tableList()
        }
    });


    // 订单金额排序
    $scope.sortMoney = function () {
        $scope.params.sort_money = $scope.params.sort_money == 2 ? 1 : 2;
        $scope.params.sort_time = '';      // 下单时间排序
        $scope.pageConfig.currentPage = 1;
        tableList()
    };
    // 下单时间排序
    $scope.sortTime = function () {
        $scope.params.sort_time = $scope.params.sort_time == 2 ? 1 : 2;
        $scope.params.sort_money = '';      // 订单金额排序
        $scope.pageConfig.currentPage = 1;
        tableList()
    };




    /*列表数据获取方法*/
    function tableList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        $http({
            method: "get",
            url: 'http://test.cdlhzz.cn:888/order/find-supplier-order-list',
            params: $scope.params
        }).then((res) => {
            console.log(res);
            $scope.alltabledetail = res.data.data.details;
            $scope.pageConfig.totalItems = res.data.data.count;
        })

    }


    /*
     查看详情跳转至不同的详情页面
     */
    $scope.viewDetail = (order_no, sku, status) => {
        if ($scope.finish_flag) {
            tabflag = 'finish_flag'
            $state.go('done_detail', {order_no: order_no, sku: sku, tabflag: tabflag})
        } else if ($scope.cancel_flag) {
            tabflag = 'cancel_flag'
            $state.go('cancel_detail', {order_no: order_no, sku: sku, tabflag: tabflag});
        } else if ($scope.waitpay_flag) {
            tabflag = 'waitpay_flag'
            $state.go('waitpay_detail', {order_no: order_no, sku: sku, tabflag: tabflag});
        } else if ($scope.all_flag) {
            tabflag = 'all_flag'
            if (status == "待付款") {
                $state.go('waitpay_detail', {order_no: order_no, sku: sku, tabflag: tabflag});
            } else if (status == '已完成') {
                $state.go('done_detail', {order_no: order_no, sku: sku, tabflag: tabflag})
            } else if (status == "已取消") {
                $state.go('cancel_detail', {order_no: order_no, sku: sku, tabflag: tabflag});
            }
        }
    }

    /*----------------------------王杰开始-----------------------------------*/

    /*----------------------------王杰结束-----------------------------------*/
})


