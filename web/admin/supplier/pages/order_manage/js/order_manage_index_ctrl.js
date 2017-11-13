/**
 * Created by Administrator on 2017/9/15/015.
 */
let ordermanage = angular.module("ordermanageModule", []);
ordermanage.controller("ordermanage_ctrl", function ($scope, $http, $stateParams, $state,_ajax) {
    let time_type;
    let tabflag;
    let allTableInit = {
        all_flag: allInit,
        waitpay_flag: waitpayInit,
        finish_flag: finishInit,
        cancel_flag: cancelInit,
    }
    $scope.myng=$scope;
    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    /*选项卡数字获取*/
    $http({
        method: "get",
        url: baseUrl+"/order/get-order-num",
    }).then((res) => {
        $scope.listcount = res.data.data;
        if($stateParams.tabflag=='waitreceive_flag' || $stateParams.tabflag=='waitsend_flag'){
            $scope.tabChange($stateParams.tabflag);
        }else if ($stateParams.tabflag=='waitpay_flag'||$stateParams.tabflag=='finish_flag'||$stateParams.tabflag=='cancel_flag') {
            $scope.tabFunc($stateParams.tabflag);
        } else {
            $scope.tabFunc('all_flag');
        }
    })

    /*选项卡切换方法*/
    $scope.tabChange = (obj) => {
        $scope.all_flag = false;
        $scope.waitpay_flag = false;
        $scope.waitsend_flag = false;
        $scope.waitreceive_flag = false;
        $scope.finish_flag = false;
        $scope.cancel_flag = false;
        $scope[obj] = true;
        if($scope.waitsend_flag==true){   //-----------待发货
            //初始化时间类型为all,和自定义开始结束时间为空
            $scope.sort_money_img='lib/images/arrow_default.png';
            $scope.sort_time_img='lib/images/arrow_down.png';
            $scope.wjparams.time_type='all';
            $scope.wjparams.type='unshipped';
            $scope.wjparams.start_time='';
            $scope.wjparams.end_time='';
            $scope.w_search='';
            $scope.wjparams.keyword='';
            $scope.wjparams.sort_money='';
            $scope.wjparams.sort_time=2;
            //待发货列表数据
            tablePages();
        }else if($scope.waitreceive_flag==true){//-----------待收货
            $scope.sort_money_img='lib/images/arrow_default.png';
            $scope.sort_time_img='lib/images/arrow_down.png';
            $scope.wjparams.time_type='all';
            $scope.wjparams.type='unreceived';
            $scope.wjparams.start_time='';
            $scope.wjparams.end_time='';
            $scope.w_search='';
            $scope.wjparams.keyword='';
            $scope.wjparams.sort_money='';
            $scope.wjparams.sort_time=2;
            //列表数据
            tablePages();
        }
    };

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
        $scope.pageConfig.currentPage = 1;
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
        $scope.pageConfig.currentPage = 1;
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
        $scope.pageConfig.currentPage = 1;
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
        $scope.pageConfig.currentPage = 1;
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
            url: baseUrl+'/order/find-supplier-order-list',
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
        } else if($scope.waitsend_flag){
            tabflag = 'waitsend_flag'
            $state.go('waitsend_detail',{order_no:order_no,sku:sku,tabflag:tabflag});
        } else if($scope.waitreceive_flag){
            tabflag = 'waitreceive_flag'
            $state.go('waitsend_detail',{order_no:order_no,sku:sku,tabflag:tabflag});
        } else if ($scope.all_flag) {
            tabflag = 'all_flag'
            if (status == "待付款") {
                $state.go('waitpay_detail', {order_no: order_no, sku: sku, tabflag: tabflag});
            } else if (status == '已完成') {
                $state.go('done_detail', {order_no: order_no, sku: sku, tabflag: tabflag})
            } else if (status == "已取消") {
                $state.go('cancel_detail', {order_no: order_no, sku: sku, tabflag: tabflag});
            }else if(status == '待发货'||status == '待收货'){
                $state.go('waitsend_detail',{order_no: order_no, sku: sku, tabflag: tabflag});
            }
        }
    }
    /*--------------------------------------------王杰开始----------------------------------------------*/
    /*------------------------待发货开始-----------------------------------*/
    $scope.waitsend_list={};
    $scope.wait_receive_list={};
    $scope.sort_time_img='lib/images/arrow_down.png';//时间默认图片
    $scope.sort_money_img='lib/images/arrow_default.png';//金额默认图片
    /*分页配置*/
    $scope.wjConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    }
    let tablePages=function () {
        $scope.wjparams.page=$scope.wjConfig.currentPage;//点击页数，传对应的参数
        _ajax.get('/order/find-supplier-order-list',$scope.wjparams,function (res) {
            console.log(res);
            if($scope.waitsend_flag==true){
                $scope.waitsend_list=res.data.details;
            }else{
                $scope.wait_receive_list=res.data.details;
            }
            $scope.wjConfig.totalItems = res.data.count;
        })
    };
    $scope.wjparams = {
        page: 1,                        // 当前页数
        time_type: 'all',               // 时间类型
        keyword: '',                    // 关键字查询
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
        type: 'unshipped',              // 类型选择
        sort_money:'',                  //金额默认
        sort_time:'2',                  //时间默认降序
    };

    //时间类型
    _ajax.get('/site/time-types',{},function (res) {
        $scope.time = res.data.time_types;
        $scope.wjparams.time_type = res.data.time_types[0].value;//待发货
    })
    //监听时间类型
    $scope.wait_send_type=function () {
        $scope.wjConfig.currentPage = 1; //页数跳转到第一页
        //恢复到默认图片
        $scope.sort_money_img='lib/images/arrow_default.png';
        $scope.sort_time_img='lib/images/arrow_down.png';
        $scope.w_search='';//清空搜索框内容
        $scope.wjparams.keyword=''
        tablePages();
    };
    //监听开始和结束时间
    $scope.wait_send_change_time=function () {
        $scope.wjConfig.currentPage = 1; //页数跳转到第一页
        tablePages();
    };
    //搜索
    $scope.wait_send_search_btn=function () {
        $scope.wjConfig.currentPage = 1; //页数跳转到第一页
        $scope.wjparams.keyword=$scope.w_search;
        console.log($scope.w_search)
        //初始化"全部时间"
        _ajax.get('/site/time-types',{},function (res) {
            $scope.time = res.data.time_types;
            $scope.wjparams.time_type = res.data.time_types[0].value;//待发货
        })
        //恢复到默认图片
        $scope.sort_money_img='lib/images/arrow_default.png';
        $scope.sort_time_img='lib/images/arrow_down.png';
        $scope.wjparams.sort_time=2;//默认按时间排序
        $scope.wjparams.sort_money='';//初始化金额排序
        tablePages();
    };
    //点击时间排序
    $scope.sort_time_click=function () {
        $scope.wjparams.sort_money='';//初始化金额排序
        $scope.sort_money_img='lib/images/arrow_default.png';//金额默认图片
        if($scope.sort_time_img=='lib/images/arrow_default.png'){
            $scope.sort_time_img='lib/images/arrow_down.png';
            $scope.wjparams.sort_time=2;
        }else if($scope.sort_time_img=='lib/images/arrow_down.png'){ //------> 升序
            $scope.sort_time_img='lib/images/arrow_up.png';
            $scope.wjparams.sort_time=1;
        }else{                                                //-------> 降序
            $scope.sort_time_img='lib/images/arrow_down.png';
            $scope.wjparams.sort_time=2;
        }
        tablePages();
    }
    //点击金额排序
    $scope.sort_money_click=function () {
        $scope.wjparams.sort_time='';//初始化时间排序
        $scope.sort_time_img='lib/images/arrow_default.png';//时间默认图片
        if($scope.sort_money_img=='lib/images/arrow_default.png'){  //-------> 默认降序
            $scope.sort_money_img='lib/images/arrow_down.png';
            $scope.wjparams.sort_money=2;
        }else if($scope.sort_money_img=='lib/images/arrow_down.png'){//------> 升序
            $scope.sort_money_img='lib/images/arrow_up.png';
            $scope.wjparams.sort_money=1;
        }else if($scope.sort_money_img=='lib/images/arrow_up.png'){//-------->降序
            $scope.sort_money_img='lib/images/arrow_down.png';
            $scope.wjparams.sort_money=2;
        }
        tablePages();
    }
    //发货按钮,判断弹出的模态框
    $scope.track_flag=false;
    $scope.wait_send_ship=function (shipping_type,order_no,sku) {
        //初始化状态
        $scope.track_flag=false;
        $scope.track_font='';
        $scope.delivery_input_model='';
        $scope.wait_send_order_no=order_no;
        $scope.wait_send_sku=sku;
        if(shipping_type==0){
            $('#track_confirm_modal').modal('show');
        }else{
            $('#wait_send_confirm_modal').modal('show');
        }
    };
    //直接发货确认按钮
    $scope.ship_confirm_btn=function () {
        _ajax.post('/order/supplierdelivery',{
            order_no:$scope.wait_send_order_no,
            sku:$scope.wait_send_sku,
            shipping_type:1
        },function (res) {
            tablePages();
        })
    }
    //快递单号发货模态框 确认按钮
    $scope.track_confirm_btn=function () {
        if(!!$scope.delivery_input_model){
            _ajax.post('/order/supplierdelivery',{
                order_no:$scope.wait_send_order_no,
                sku:$scope.wait_send_sku,
                shipping_type:0,
                waybillnumber:$scope.delivery_input_model
            },function (res) {
                if(res.code!=200){
                    $scope.track_flag=true;
                    $scope.track_font='快递单号错误，请重新输入';
                }else if(res.code==200){
                    $scope.track_flag=false;
                    $('#track_confirm_modal').modal('hide');
                    tablePages();
                }
            })
        }else{
            $scope.track_flag=true;
            $scope.track_font='快递单号不能为空'
        }

    }

    /*------------------------待发货结束-----------------------------------*/

    /*待发货表格Menu切换 开始*/
    $scope.waitsend_1 = true;
    $scope.waitsend_2 = true;
    $scope.waitsend_3 = true;
    $scope.waitsend_4 = true;
    $scope.waitsend_5 = true;
    $scope.waitsend_6 = true;
    $scope.waitsend_7 = false;
    $scope.waitsend_8 = true;
    $scope.waitsend_9 = true;
    $scope.waitsend_10 = false;
    $scope.waitsend_11 = true;
    $scope.waitsend_12 = true;
    /*待收货*/
    $scope.waitreceive_1 =true;
    $scope.waitreceive_2 =true;
    $scope.waitreceive_3 =true;
    $scope.waitreceive_4 =true;
    $scope.waitreceive_5 =true;
    $scope.waitreceive_6 =true;
    $scope.waitreceive_7 =false;
    $scope.waitreceive_8 =true;
    $scope.waitreceive_9 =true;
    $scope.waitreceive_10 =false;
    $scope.waitreceive_11 =true;
    $scope.waitreceive_12 =false;
    $scope.waitsend_all = function (m) {
        m === true ? $scope[m] = false : $scope[m] = true;
    };
    /*----------------------------王杰结束-----------------------------------*/
})


