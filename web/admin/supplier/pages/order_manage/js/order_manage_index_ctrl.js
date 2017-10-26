/**
 * Created by Administrator on 2017/9/15/015.
 */
let ordermanage = angular.module("ordermanageModule", []);
ordermanage.controller("ordermanage_ctrl", function ($scope, $http, $stateParams,$state) {
    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    $scope.myng=$scope;
    /*选项卡切换方法*/
    $scope.tabFunc = (obj) => {
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

    /*选项卡初始状态*/
    if($stateParams.waitpay_flag){
        $scope.tabFunc('waitpay_flag');
    }else{
        $scope.tabFunc('all_flag');
    }

    //
    /*全部表格Menu切换 结束*/
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
        $http.get(baseUrl+'/order/find-supplier-order-list',{
            params:$scope.wjparams
        }).then(function (res) {
            console.log(res);
            if($scope.waitsend_flag==true){
                $scope.waitsend_list=res.data.data.details;
            }else{
                $scope.wait_receive_list=res.data.data.details;
            }
            $scope.wjConfig.totalItems = res.data.data.count;
        },function (err) {
            console.log(err);
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

    if($stateParams.wait_send_flag){
        $scope.tabFunc('waitsend_flag');
    }else if($stateParams.wait_receive_flag){
        $scope.tabFunc('waitreceive_flag');
    }

    //时间类型
    $http.get(baseUrl+'/site/time-types').then(function (response) {
        $scope.time = response.data.data.time_types;
        $scope.wjparams.time_type = response.data.data.time_types[0].value;//待发货
    });
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
        //初始化"全部时间"
        $http.get(baseUrl+'/site/time-types').then(function (response) {
            $scope.time = response.data.data.time_types;
            $scope.wjparams.time_type = response.data.data.time_types[0].value;//待发货
        });
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
    //跳转详情页
    $scope.wait_send_detail=function (order_no,sku,wait_receive) {
        $http.post(baseUrl+'/order/getsupplierorderdetails',{
            order_no:order_no,
            sku:sku
        },config).then(function (res) {
            $scope.waitsend_detail_list=res.data.data;
            $state.go('waitsend_detail',{item:$scope.waitsend_detail_list,sku:sku,wait_receive:wait_receive})
        },function (err) {
            console.log(err);
        });
    };
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
        $http.post(baseUrl+'/order/supplierdelivery',{
            order_no:$scope.wait_send_order_no,
            sku:$scope.wait_send_sku,
            shipping_type:1
        },config).then(function (res) {
            console.log(res);
            tablePages();
        },function (err) {
            console.log(err);
        })
    }
    //快递单号发货模态框 确认按钮
    $scope.track_confirm_btn=function () {
        if(!!$scope.delivery_input_model){
            $http.post(baseUrl+'/order/supplierdelivery',{
                order_no:$scope.wait_send_order_no,
                sku:$scope.wait_send_sku,
                shipping_type:0,
                waybillnumber:$scope.delivery_input_model
            },config).then(function (res) {
                console.log(res);
                if(res.data.code!=200){
                    $scope.track_flag=true;
                    $scope.track_font='快递单号错误，请重新输入';
                }else if(res.data.code==200){
                    $scope.track_flag=false;
                    $('#track_confirm_modal').modal('hide');
                    tablePages();
                }
            },function (err) {
                console.log(err);
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
    //
    /*待发货表格Menu切换 结束*/


    /*------------------------------待收货 开始-----------------------------*/

    //监听时间类型
    // $scope.wait_receive_type=function (value) {
    //   //搜索过后，时间和金额默认图片
    //   $scope.sort_money_img='lib/images/arrow_default.png';
    //   $scope.sort_time_img='lib/images/arrow_down.png';
    //   $scope.wait_receive_search='';//清空搜索输入框
    //   $scope.wait_receive_search_flag=false;//清除搜索状态
    //   $http.get(baseUrl+'/order/find-supplier-order-list',{
    //     params:{
    //       type:'unreceived',
    //       time_type:value
    //     }
    //   }).then(function (res) {
    //     console.log(res);
    //     $scope.wait_receive_list=res.data.data.details;
    //   },function (err) {
    //     console.log(err);
    //   });
    // };
    //监听开始和结束时间
    // $scope.wait_receive_change_time=function () {
    //   $http.get(baseUrl+'/order/find-supplier-order-list',{
    //     params:{
    //       type:'unreceived',
    //       time_type:'custom',
    //       start_time:$scope.wait_receive_begin_time,
    //       end_time:$scope.wait_receive_end_time
    //     }
    //   }).then(function (res) {
    //     console.log(res);
    //     $scope.wait_receive_list=res.data.data.details;
    //   },function (err) {
    //     console.log(err);
    //   })
    // };
    //搜索
    //   $scope.wait_receive_search_btn=function () {
    //       $scope.wait_receive_search_flag=true;//表示点击搜索过后
    //       //搜索过后，时间和金额默认图片
    //       $scope.sort_money_img='lib/images/arrow_default.png';
    //       $scope.sort_time_img='lib/images/arrow_down.png';
    //       $http.get(baseUrl+'/order/find-supplier-order-list',{
    //           params:{
    //               type:'unreceived',
    //               keyword:$scope.wait_receive_search
    //           }
    //       }).then(function (res) {
    //           console.log(res);
    //           $scope.wait_receive_list=res.data.data.details;
    //       },function (err) {
    //           console.log(err);
    //       })
    //   };
    /*------------------------------待收货 结束-----------------------------*/

    /*--------------------------------------------王杰开始----------------------------------------------*/
});