app.controller('commodity', ['$scope', '$state', '$stateParams', function ($scope, $state, $stateParams) {
    $scope.id = $stateParams.id;    // 商家ID
    // $scope.showDel = $state.is('order.all');    // 判断是否处于全部订单位置
}]);


// let commodity_manage = angular.module("commodity_manage_module", []);
// commodity_manage.controller("commodity_manage_ctrl", function ($scope, $http, $stateParams) {
//     const config = {
//         headers: {'Content-Type': 'application/x-www-form-urlencoded'},
//         transformRequest: function (data) {
//             return $.param(data)
//         }
//     };
//
//         // let allTableInit = {
//     //     on_flag: onShelvesInit,
//     //     off_flag: offShelvesInit,
//     //     wait_flag: waitShelvesInit,
//     //     del_flag: deleteInit
//     // }
//
//     let checkId;
//     let sortway = "online_time"; //默认按上架时间降序排列
//     $scope.goods = '';
//     $scope.cantonline = '';
//     $scope.shangjiaarr = [];
//     $scope.xiajiaarr = [];
//     $scope.waitarr = [];
//     $scope.changed = $scope;
//     $scope.offline_search = '';
//     $scope.online_search = '';
//     $scope.offline_reason = ''
//     $scope.defaultreason_arr = ["分类系列或风格移除","商家下架","品牌下架","分类下架","闭店下架"];
//
//     /*默认参数配置*/
//     $scope.params = {
//         status:2,     //已上架
//         page:1,
//         'sort[]':sortway + ':3',
//         keyword:'',
//         supplier_id:+$stateParams.storeid
//     }
//
//     /*分页配置*/
//     $scope.pageConfig = {
//         showJump: true,
//         itemsPerPage: 12,
//         currentPage: 1,
//         onChange: function () {
//             tableList();
//         }
//     }
//
//
//     /*已上架列表单个下架原因*/
//     // console.log($stateParams.storeid)  /*目前商家ID默认传+$stateParams.storeid 后期替换为该值*/
//
//
//     /*选项卡切换方法*/
//     $scope.tabFunc = (obj) => {
//         $scope.on_flag = false;
//         $scope.off_flag = false;
//         $scope.wait_flag = false;
//         $scope.del_flag = false;
//         $scope[obj] = true;
//         // allTableInit[obj]();
//     }
//
//
//
//     /*选项卡默认选中*/
//     // $scope.tabChange = (function () {
//         if ($stateParams.offlineflag) {
//             $scope.tabFunc('off_flag')
//             // offlinegoodsTable();
//         } else if($stateParams.waitflag){
//             $scope.tabFunc('wait_flag')
//             // waitgoodsTable();
//         }else if($stateParams.deleteflag){
//             $scope.tabFunc('del_flag')
//             // deletegoodsTable();
//         }else{
//             $scope.tabFunc('on_flag')
//             // onlinegoodsTable();
//         }
//     // })()
//
//
//
//
//     /*已上架销量默认排序*/
//     $scope.onlinesold_ascorder = false;
//     $scope.onlinesold_desorder = true;
//
//     /*已上架上架时间排序*/
//     $scope.onlinetime_ascorder = false;
//     $scope.onlinetime_desorder = true;
//
//     /*已下架销量默认排序*/
//     $scope.offlinesold_ascorder = false;
//     $scope.offlinesold_desorder = true;
//
//     /*已下架下架时间排序*/
//     $scope.offlinetime_ascorder = false;
//     $scope.offlinetime_desorder = true;
//
//     /*等待上架销量排序*/
//     $scope.waitsold_ascorder = false;
//     $scope.waitsold_desorder = true;
//
//     /*等待上架创建时间排序*/
//     $scope.waittime_ascorder = false;
//     $scope.waittime_desorder = true;
//
//
//     /*已删除销量排序*/
//     $scope.deletedsold_ascorder = false;
//     $scope.deletedsold_desorder = true;
//
//     /*已删除删除时间排序*/
//     $scope.deletedtime_ascorder = false;
//     $scope.deletedtime_desorder = true;
//
//
//     /*页面Menu切换 开始*/
//     //页面初始化
//     // $scope.on_flag = true;
//     // $scope.down_flag = false;
//     // $scope.wait_flag = false;
//     // $scope.del_flag = false;
//     //页面传值判断
//     // $scope.on_flag=$stateParams.on_flag;
//     // $scope.down_flag=$stateParams.down_flag;
//     // $scope.wait_flag=$stateParams.wait_flag;
//     // $scope.del_flag=$stateParams.del_flag;
//     // if($scope.on_flag===true){
//     //   $scope.down_flag=false;
//     //   $scope.wait_flag=false;
//     //   $scope.del_flag=false;
//     // }
//     // else if($scope.down_flag===true){
//     //   $scope.on_flag=false;
//     //   $scope.wait_flag=false;
//     //   $scope.del_flag=false;
//     // }else if($scope.wait_flag===true){
//     //   $scope.on_flag=false;
//     //   $scope.down_flag=false;
//     //   $scope.del_flag=false;
//     // }else if($scope.del_flag===true){
//     //   $scope.on_flag=false;
//     //   $scope.down_flag=false;
//     //   $scope.wait_flag=false;
//     // }
//     // //已上架
//     // $scope.on_shelves = function () {
//     //     $scope.on_flag = true;
//     //     $scope.down_flag = false;
//     //     $scope.wait_flag = false;
//     //     $scope.del_flag = false;
//     //     onlinegoodsTable()
//     // };
//     // //已下架
//     // $scope.down_shelves = function () {
//     //     $scope.down_flag = true;
//     //     $scope.on_flag = false;
//     //     $scope.wait_flag = false;
//     //     $scope.del_flag = false;
//     //     offlinegoodsTable();
//     // };
//     // //等待上架
//     // $scope.wait_shelves = function () {
//     //     $scope.wait_flag = true;
//     //     $scope.on_flag = false;
//     //     $scope.down_flag = false;
//     //     $scope.del_flag = false;
//     //     waitgoodsTable();
//     // };
//     // //已删除
//     // $scope.logistics = function () {
//     //     $scope.del_flag = true;
//     //     $scope.on_flag = false;
//     //     $scope.down_flag = false;
//     //     $scope.wait_flag = false;
//     //     deletegoodsTable();
//     // };
//     /*页面Menu切换 结束*/
//
//
//     /*表格Menu切换 开始*/
//     $scope.menu_list = [
//         {name: '商品编号', value: true},
//         {name: '商品名称', value: true},
//         {name: '供货价格', value: true},
//         {name: '市场价格', value: true},
//         {name: '平台价格', value: true},
//         {name: '装饰公司采购价格', value: true},
//         {name: '项目经理采购价格', value: false},
//         {name: '设计师采购价格', value: true},
//         {name: '库存', value: true},
//         {name: '销量', value: true},
//         {name: '状态', value: true},
//         {name: '发布时间', value: true},
//         {name: '图片', value: true},
//         {name: '详情', value: true}
//     ]
//     /*表格Menu切换 结束*/
//
//
//
//
//
//     /*===========================================已上架开始=============================================*/
//
//
//     /*===============================================已下架开始=======================================================*/
//     /*已下架商品列表*/
//   function offlinegoodsTable (){
//        $scope.offline_search = '';
//        $scope.offlinesold_ascorder = false;
//        $scope.offlinesold_desorder = true;
//        $scope.offlinetime_ascorder = false;
//        $scope.offlinetime_desorder = true;
//        $http({
//             method: "get",
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "offline_time:3",size:999}
//         }).then((res) => {
//             $scope.offilinegoods = res.data.data.goods_list_admin.details;
//         })
//     }
//
//
//     /*单个商品上架*/
//     $scope.singlegoodOnline = function (id) {
//         $scope.tempgoodid = id;
//     }
//
//     /*单个商品确认上架*/
//     $scope.sureGoodOnline = function () {
//         let url = "http://test.cdlhzz.cn:888/mall/goods-status-toggle";
//         console.log($scope.tempgoodid)
//         let data = {id: Number($scope.tempgoodid)};
//         $http.post(url, data, config).then(function (res) {
//             console.log(res)
//             /*由于某些原因不能上架*/
//             if (res.data.code != 200) {
//                 // console.log(res)
//                 $('#up_shelves_modal').modal("hide");
//                 $("#up_not_shelves_modal").modal("show")
//                 $scope.cantonline = res.data.msg;
//             } else {
//                 /*可以上架*/
//                 $('#up_shelves_modal').modal("hide");
//             }
//         })
//     }
//
//     /*单个商品取消上架操作无*/
//
//     /*不能上架 确认*/
//     $scope.sureCantOnline = function () {
//         $scope.cantonlinemodal = ""
//     }
//
//     /*已下架列表全选*/
//     $scope.allOnline = function (m) {
//         for (let i = 0; i < $scope.offilinegoods.length; i++) {
//             if (m === true) {
//                 $scope.offilinegoods[i].state = false;
//                 $scope.selectoffAll = false;
//             } else {
//                 $scope.offilinegoods[i].state = true;
//                 $scope.selectoffAll = true;
//             }
//         }
//     }
//
//     /*已下架列表 批量上架*/
//     $scope.piliangshangjia = function () {
//         $scope.shangjiaarr.length = 0;
//         for (let [key, value] of $scope.offilinegoods.entries()) {
//             if (value.state) {
//                 $scope.shangjiaarr.push(value.id)
//             }
//         }
//     }
//
//     /*确认批量上架*/
//     $scope.surepiliangonline = function () {
//         $scope.piliangonids = $scope.shangjiaarr.join(',');
//         let url = "http://test.cdlhzz.cn:888/mall/goods-enable-batch";
//         let data = {ids: $scope.piliangonids};
//         $http.post(url, data, config).then(function (res) {
//             /*由于某些原因不能上架*/
//             if (res.data.code != 200) {
//                 $('#piliangonline_modal').modal("hide");
//                 $("#up_not_shelves_modal").modal("show")
//                 $scope.cantonline = res.data.msg;
//             } else {
//                 /*可以上架*/
//                 $('#piliangonline_modal').modal("hide");
//             }
//
//         })
//     }
//
//     /*取消批量上架*/
//     $scope.cancelplliangonline = function () {
//         for (let i = 0; i < $scope.offilinegoods.length; i++) {
//                 $scope.offilinegoods[i].state = false;
//                 $scope.selectoffAll = false;
//         }
//     }
//
//
//
//     /*已下架搜索*/
//     $scope.offlineSearch = function () {
//         $scope.offlinesold_ascorder = false;
//         $scope.offlinesold_desorder = true;
//
//         $scope.offlinetime_ascorder = false;
//         $scope.offlinetime_desorder = true;
//         $http({
//             method: "get",
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params: {status: 0, supplier_id: +$stateParams.storeid, keyword: $scope.offline_search}
//         }).then(function (res) {
//             $scope.offilinegoods = res.data.data.goods_list_admin.details;
//         })
//     }
//
//     /*已下架销量排序*/
//     $scope.offlineSoldDesorder = function () {
//         $scope.offlinetime_ascorder = false;
//         $scope.offlinetime_desorder = true;
//
//         $scope.offlinesold_ascorder = false;
//         $scope.offlinesold_desorder = true;
//         $http({
//             method: "get",
//             params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:3"},
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//         }).then(function (response) {
//             $scope.offilinegoods = response.data.data.goods_list_admin.details;
//             // $scope.selPage = 1;
//         })
//     }
//
//     $scope.offlineSoldAscorder = function () {
//         $scope.offlinetime_ascorder = false;
//         $scope.offlinetime_desorder = true;
//
//         $scope.offlinesold_ascorder = true;
//         $scope.offlinesold_desorder = false;
//         $http({
//             method: "get",
//             params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:4"},
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//         }).then(function (response) {
//             console.log(response)
//             $scope.offilinegoods = response.data.data.goods_list_admin.details;
//             // $scope.selPage = 1;
//         })
//     }
//
//     /*已下架 下架时间排序*/
//     $scope.offlineTimeDesorder = function () {
//
//         $scope.offlinesold_ascorder = false;
//         $scope.offlinesold_desorder = true;
//         $scope.offlinetime_ascorder = false;
//         $scope.offlinetime_desorder = true;
//         $http({
//             method: "get",
//             params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "offline_time:3"},
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//         }).then(function (response) {
//             $scope.offilinegoods = response.data.data.goods_list_admin.details;
//             // $scope.selPage = 1;
//         })
//     }
//
//     $scope.offlineTimeAscorder = function () {
//         $scope.offlinesold_ascorder = false;
//         $scope.offlinesold_desorder = true;
//
//         $scope.offlinetime_ascorder = true;
//         $scope.offlinetime_desorder = false;
//         $http({
//             method: "get",
//             params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "offline_time:4"},
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//         }).then(function (response) {
//             $scope.offilinegoods = response.data.data.goods_list_admin.details;
//             // $scope.selPage = 1;
//         })
//     }
//
//    $scope.showreason_modal = '';
//     let tempId;
//
//     /*可以编辑的已下架原因的处理*/
//     $scope.editReason = (id,reason) => {
//         $scope.modal_offreason = reason || '';
//         tempId = id;
//     }
//
//     /*重设下架原因*/
//     $scope.sureResetReason = () => {
//         let url = "http://test.cdlhzz.cn:888/mall/goods-offline-reason-reset";
//         let data = {id:tempId,offline_reason:$scope.modal_offreason};
//         $http.post(url, data, config).then((res) => {
//             offlinegoodsTable();
//         })
//     }
//     /*======================================已下架结束=======================================================*/
//
//
//
//     /*========================================等待上架开始================================================*/
//
//     /*等待上架列表*/
//     function waitgoodsTable() {
//         $scope.wait_search = '';
//         $scope.waittime_desorder = true;
//         $scope.waittime_ascorder = false;
//         $scope.waittime_desorder = true;
//         $scope.waitsold_ascorder = false;
//         $http({
//             method: "get",
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params: {status: 1, supplier_id: +$stateParams.storeid,create_time:4}
//         }).then(function (res) {
//             $scope.waitgoods = res.data.data.goods_list_admin.details;
//
//         })
//     }
//
//     /*等待上架 单个上架*/
//     $scope.waitToOnline = function (id) {
//         $scope.tempwaitgoodid = id;
//
//     }
//
//     /*等待上架 单个确认上架*/
//     $scope.sureWaitToOnline = function () {
//         let url = "http://test.cdlhzz.cn:888/mall/goods-status-toggle";
//         let data = {id: $scope.tempwaitgoodid};
//         $http.post(url, data, config).then(function (res) {
//             /*由于某些原因不能上架*/
//             if (res.data.code != 200) {
//                 // console.log(res)
//                 $('#waitup_shelves_modal').modal("hide");
//                 $("#waitup_not_shelves_modal").modal("show")
//                 $scope.waitcantonline = res.data.msg;
//             } else {
//                 /*可以上架*/
//                 $('#waitup_shelves_modal').modal("hide");
//             }
//         })
//     }
//
//     /*等待上架 销量逆序*/
//     $scope.waitSoldDesorder = function () {
//         $scope.waittime_desorder = true;
//         $scope.waittime_ascorder = false;
//         $scope.waitsold_ascorder = false;
//         $scope.waitsold_desorder = true;
//         $http({
//             method:"get",
//             url:"http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params:{status: 1, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:3"}
//         }).then(function (res) {
//             $scope.waitgoods = res.data.data.goods_list_admin.details;
//         })
//     }
//
//     /*等待上架 销量顺序*/
//     $scope.waitSoldAscorder = function () {
//         $scope.waittime_desorder = true;
//         $scope.waittime_ascorder = false;
//
//         $scope.waitsold_ascorder = true;
//         $scope.waitsold_desorder = false;
//         $http({
//             method:"get",
//             url:"http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params:{status: 1, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:4"}
//         }).then(function (res) {
//             $scope.waitgoods = res.data.data.goods_list_admin.details;
//         })
//
//     }
//
//
//
//     /*等待上架 创建时间逆序*/
//     $scope.waitTimeDesorder = function () {
//         $scope.waittime_desorder = true;
//         $scope.waittime_ascorder = false;
//
//         $scope.waitsold_ascorder = false;
//         $scope.waitsold_desorder = true;
//         $http({
//             method:"get",
//             url:"http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params:{status: 1, supplier_id: +$stateParams.storeid, "sort[]": "publish_time:3"}
//         }).then(function (res) {
//             $scope.waitgoods = res.data.data.goods_list_admin.details;
//         })
//     }
//
//     /*等待上架 创建时间顺序*/
//     $scope.waitTimeAscorder = function () {
//         $scope.waittime_desorder = false;
//         $scope.waittime_ascorder = true;
//
//         $scope.waitsold_ascorder = false;
//         $scope.waitsold_desorder = true;
//         $http({
//             method:"get",
//             url:"http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params:{status: 1, supplier_id: +$stateParams.storeid, "sort[]": "publish_time:4"}
//         }).then(function (res) {
//             $scope.waitgoods = res.data.data.goods_list_admin.details;
//         })
//
//     }
//
//
//     /*等待上架 搜索*/
//     $scope.waitSearch = function () {
//         $scope.waittime_desorder = true;
//         $scope.waittime_ascorder = false;
//         $scope.waitsold_desorder = true;
//         $scope.waitsold_ascorder = false;
//         $http({
//             method: "get",
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params: {status: 1, supplier_id: +$stateParams.storeid, keyword: $scope.wait_search}
//         }).then(function (res) {
//             $scope.waitgoods = res.data.data.goods_list_admin.details;
//         })
//     }
//
//     /*等待上架全选*/
//    $scope.allwait = function (m) {
//        for (let i = 0; i < $scope.waitgoods.length; i++) {
//            if (m === true) {
//                $scope.waitgoods[i].state = false;
//                $scope.selectwaitAll = false;
//            } else {
//                $scope.waitgoods[i].state = true;
//                $scope.selectwaitAll = true;
//            }
//        }
//    }
//
//    /*等待上架批量上架*/
//    $scope.allwaitToOnline = function () {
//        $scope.waitarr.length = 0;
//        for (let [key, value] of $scope.waitgoods.entries()) {
//            if (value.state) {
//                $scope.waitarr.push(value.id)
//            }
//        }
//    }
//     /*等待上架确认批量上架*/
//     $scope.surewaitonline = function () {
//         $scope.allwaitonids = $scope.waitarr.join(',');
//         let url = "http://test.cdlhzz.cn:888/mall/goods-enable-batch";
//         let data = {ids: $scope.allwaitonids};
//         $http.post(url, data, config).then(function (res) {
//             console.log(res);
//             /*由于某些原因不能上架*/
//             if (res.data.code != 200) {
//                 $('#allwaitonline_modal').modal("hide");
//                 $("#waitup_not_shelves_modal").modal("show")
//                 $scope.waitcantonline = res.data.msg;
//             } else {
//                 /*可以上架*/
//                 $('#allwaitonline_modal').modal("hide");
//             }
//         })
//     }
//
//     /*等待上架 取消批量上架*/
//     $scope.cancelWaitOnline = function () {
//         for (let i = 0; i < $scope.waitgoods.length; i++) {
//                 $scope.waitgoods[i].state = false;
//                 $scope.selectwaitAll = false;
//         }
//     }
//
//
//     /*更新审核备注*/
//     $scope.checkReason = function (id,reason) {
//         checkId = id;
//         $scope.lastreason = reason;
//     }
//
//
//     /*确认更新审核备注*/
//     $scope.sureCheckReason = function () {
//         let url = "http://test.cdlhzz.cn:888/mall/goods-reason-reset";
//         let data  = {id:Number(checkId),reason:$scope.lastreason||''};
//         $http.post(url,data,config).then((res)=>{
//             console.log(res);
//             waitgoodsTable();
//         })
//     }
//
//
//     /*=======================================已删除开始======================================================*/
//     /*已删除列表*/
//     function deletegoodsTable() {
//         $scope.deleted_search = ''
//         $http({
//             method: "get",
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params: {status: 3, supplier_id: +$stateParams.storeid,"sort[]": "delete_time:3"}
//         }).then(function (res) {
//             $scope.deletedgoods = res.data.data.goods_list_admin.details;
//         })
//     }
//
//     /*已删除搜索*/
//     $scope.deletedSearch = function () {
//         $http({
//             method: "get",
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//             params: {status: 3, supplier_id: +$stateParams.storeid, keyword: $scope.deleted_search}
//         }).then(function (res) {
//             $scope.deletedgoods = res.data.data.goods_list_admin.details;
//         })
//     }
//
//     /*已删除销量排序*/
//     $scope.deletedSoldDesorder = function () {
//         $scope.deletedsold_ascorder = false;
//         $scope.deletedsold_desorder = true;
//         $http({
//             method: "get",
//             params: {status: 3, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:3"},
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//         }).then(function (response) {
//             $scope.deletedgoods = response.data.data.goods_list_admin.details;
//             // $scope.selPage = 1;
//         })
//     }
//
//     $scope.deletedSoldAscorder = function () {
//         $scope.deletedsold_ascorder = true;
//         $scope.deletedsold_desorder = false;
//         $http({
//             method: "get",
//             params: {status: 3, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:4"},
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//         }).then(function (response) {
//             $scope.deletedgoods = response.data.data.goods_list_admin.details;
//             // $scope.selPage = 1;
//         })
//     }
//
//
//     /*已删除删除时间排序*/
//     $scope.deletedTimeDesorder = function () {
//         $scope.deletedtime_ascorder = false;
//         $scope.deletedtime_desorder = true;
//         $http({
//             method: "get",
//             params: {status: 3, supplier_id: +$stateParams.storeid, "sort[]": "delete_time:3"},
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//         }).then(function (response) {
//             $scope.deletedgoods = response.data.data.goods_list_admin.details;
//             // $scope.selPage = 1;
//         })
//     }
//
//     $scope.deletedTimeAscorder = function () {
//         $scope.deletedtime_ascorder = true;
//         $scope.deletedtime_desorder = false;
//         $http({
//             method: "get",
//             params: {status: 3, supplier_id: +$stateParams.storeid, "sort[]": "delete_time:4"},
//             url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
//         }).then(function (response) {
//             $scope.deletedgoods = response.data.data.goods_list_admin.details;
//             // $scope.selPage = 1;
//         })
//     }
//
//
// });
//
//
