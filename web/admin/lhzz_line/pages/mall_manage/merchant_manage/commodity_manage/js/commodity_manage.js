let commodity_manage = angular.module("commodity_manage_module", []);
commodity_manage.controller("commodity_manage_ctrl", function ($scope, $http, $stateParams) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };

    let checkId;
    $scope.goods = '';
    $scope.cantonline = '';
    $scope.shangjiaarr = [];
    $scope.xiajiaarr = [];
    $scope.waitarr = [];
    $scope.changed = $scope;
    $scope.offline_search = '';
    $scope.online_search = '';
    $scope.offline_reason = ''
    $scope.defaultreason_arr = ["分类系列或风格移除","商家下架","品牌下架","分类下架","闭店下架"];
    /*已上架列表单个下架原因*/
    // console.log($stateParams.storeid)  /*目前商家ID默认传+$stateParams.storeid 后期替换为该值*/



    /*选项卡默认选中*/
    $scope.tabChange = (function () {
        if ($stateParams.offlineflag) {
            $scope.down_flag = true;
            $scope.on_flag = false;
            $scope.wait_flag = false;
            $scope.del_flag = false;
            offlinegoodsTable();
        } else if($stateParams.waitflag){
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = true;
            $scope.del_flag = false;
            waitgoodsTable();
        }else if($stateParams.deleteflag){
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.del_flag = true;
            deletegoodsTable();
        }else{
            $scope.on_flag = true;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.del_flag = false;
            onlinegoodsTable();
        }
    })()




    /*已上架销量默认排序*/
    $scope.onlinesold_ascorder = false;
    $scope.onlinesold_desorder = true;

    /*已上架上架时间排序*/
    $scope.onlinetime_ascorder = false;
    $scope.onlinetime_desorder = true;

    /*已下架销量默认排序*/
    $scope.offlinesold_ascorder = false;
    $scope.offlinesold_desorder = true;

    /*已下架下架时间排序*/
    $scope.offlinetime_ascorder = false;
    $scope.offlinetime_desorder = true;

    /*等待上架销量排序*/
    $scope.waitsold_ascorder = false;
    $scope.waitsold_desorder = true;

    /*等待上架创建时间排序*/
    $scope.waittime_ascorder = false;
    $scope.waittime_desorder = true;


    /*已删除销量排序*/
    $scope.deletedsold_ascorder = false;
    $scope.deletedsold_desorder = true;

    /*已删除删除时间排序*/
    $scope.deletedtime_ascorder = false;
    $scope.deletedtime_desorder = true;


    /*页面Menu切换 开始*/
    //页面初始化
    // $scope.on_flag = true;
    // $scope.down_flag = false;
    // $scope.wait_flag = false;
    // $scope.del_flag = false;
    //页面传值判断
    // $scope.on_flag=$stateParams.on_flag;
    // $scope.down_flag=$stateParams.down_flag;
    // $scope.wait_flag=$stateParams.wait_flag;
    // $scope.del_flag=$stateParams.del_flag;
    // if($scope.on_flag===true){
    //   $scope.down_flag=false;
    //   $scope.wait_flag=false;
    //   $scope.del_flag=false;
    // }
    // else if($scope.down_flag===true){
    //   $scope.on_flag=false;
    //   $scope.wait_flag=false;
    //   $scope.del_flag=false;
    // }else if($scope.wait_flag===true){
    //   $scope.on_flag=false;
    //   $scope.down_flag=false;
    //   $scope.del_flag=false;
    // }else if($scope.del_flag===true){
    //   $scope.on_flag=false;
    //   $scope.down_flag=false;
    //   $scope.wait_flag=false;
    // }
    //已上架
    $scope.on_shelves = function () {
        $scope.on_flag = true;
        $scope.down_flag = false;
        $scope.wait_flag = false;
        $scope.del_flag = false;
        onlinegoodsTable()
    };
    //已下架
    $scope.down_shelves = function () {
        $scope.down_flag = true;
        $scope.on_flag = false;
        $scope.wait_flag = false;
        $scope.del_flag = false;
        offlinegoodsTable();
    };
    //等待上架
    $scope.wait_shelves = function () {
        $scope.wait_flag = true;
        $scope.on_flag = false;
        $scope.down_flag = false;
        $scope.del_flag = false;
        waitgoodsTable();
    };
    //已删除
    $scope.logistics = function () {
        $scope.del_flag = true;
        $scope.on_flag = false;
        $scope.down_flag = false;
        $scope.wait_flag = false;
        deletegoodsTable();
    };
    /*页面Menu切换 结束*/


    /*已上架表格Menu切换 开始*/
    $scope.on_menu_flag = false;
    $scope.on_menu = function (m) {
        m === true ? $scope.on_menu_flag = false : $scope.on_menu_flag = true;
    };

    $scope.show_1 = true;
    $scope.show_a = function (m) {
        m === true ? $scope.show_1 = true : $scope.show_1 = false;
    };
    $scope.show_2 = true;
    $scope.show_b = function (m) {
        m === true ? $scope.show_2 = true : $scope.show_2 = false;
    };
    $scope.show_3 = true;
    $scope.show_c = function (m) {
        m === true ? $scope.show_3 = true : $scope.show_3 = false;
    };
    $scope.show_4 = false;
    $scope.show_d = function (m) {
        m === true ? $scope.show_4 = true : $scope.show_4 = false;
    };
    $scope.show_5 = false;
    $scope.show_e = function (m) {
        m === true ? $scope.show_5 = true : $scope.show_5 = false;
    };
    $scope.show_6 = true;
    $scope.show_f = function (m) {
        m === true ? $scope.show_6 = true : $scope.show_6 = false;
    };
    $scope.show_7 = false;
    $scope.show_g = function (m) {
        m === true ? $scope.show_7 = true : $scope.show_7 = false;
    };
    $scope.show_8 = false;
    $scope.show_h = function (m) {
        m === true ? $scope.show_8 = true : $scope.show_8 = false;
    };
    $scope.show_9 = true;
    $scope.show_i = function (m) {
        m === true ? $scope.show_9 = true : $scope.show_9 = false;
    };
    $scope.show_10 = true;
    $scope.show_j = function (m) {
        m === true ? $scope.show_10 = true : $scope.show_10 = false;
    };
    $scope.show_11 = true;
    $scope.show_k = function (m) {
        m === true ? $scope.show_11 = true : $scope.show_11 = false;
    };
    $scope.show_12 = true;
    $scope.show_l = function (m) {
        m === true ? $scope.show_12 = true : $scope.show_12 = false;
    };
    $scope.show_13 = true;
    $scope.show_m = function (m) {
        m === true ? $scope.show_13 = true : $scope.show_13 = false;
    };
    $scope.show_14 = true;
    $scope.show_n = function (m) {
        m === true ? $scope.show_14 = true : $scope.show_14 = false;
    };
    $scope.show_15 = true;
    $scope.show_n = function (m) {
        m === true ? $scope.show_15 = true : $scope.show_15 = false;
    };
    /*已上架表格Menu切换 结束*/

    /*已下架表格Menu切换 开始*/
    $scope.down_menu_flag = false;
    $scope.down_menu = function (m) {
        m === true ? $scope.down_menu_flag = false : $scope.down_menu_flag = true;
    };

    $scope.down_1 = true;
    $scope.down_a = function (m) {
        m === true ? $scope.down_1 = true : $scope.down_1 = false;
    };
    $scope.down_2 = true;
    $scope.down_b = function (m) {
        m === true ? $scope.down_2 = true : $scope.down_2 = false;
    };
    $scope.down_3 = true;
    $scope.down_c = function (m) {
        m === true ? $scope.down_3 = true : $scope.down_3 = false;
    };
    $scope.down_4 = true;
    $scope.down_d = function (m) {
        m === true ? $scope.down_4 = true : $scope.down_4 = false;
    };
    $scope.down_5 = true;
    $scope.down_e = function (m) {
        m === true ? $scope.down_5 = true : $scope.down_5 = false;
    };
    $scope.down_6 = false;
    $scope.down_f = function (m) {
        m === true ? $scope.down_6 = true : $scope.down_6 = false;
    };
    $scope.down_7 = false;
    $scope.down_g = function (m) {
        m === true ? $scope.down_7 = true : $scope.down_7 = false;
    };
    $scope.down_8 = false;
    $scope.down_h = function (m) {
        m === true ? $scope.down_8 = true : $scope.down_8 = false;
    };
    $scope.down_9 = true;
    $scope.down_i = function (m) {
        m === true ? $scope.down_9 = true : $scope.down_9 = false;
    };
    $scope.down_10 = false;
    $scope.down_j = function (m) {
        m === true ? $scope.down_10 = true : $scope.down_10 = false;
    };
    $scope.down_11 = true;
    $scope.down_k = function (m) {
        m === true ? $scope.down_11 = true : $scope.down_11 = false;
    };
    $scope.down_12 = true;
    $scope.down_l = function (m) {
        m === true ? $scope.down_12 = true : $scope.down_12 = false;
    };
    $scope.down_13 = true;
    $scope.down_m = function (m) {
        m === true ? $scope.down_13 = true : $scope.down_13 = false;
    };
    $scope.down_14 = true;
    $scope.down_n = function (m) {
        m === true ? $scope.down_14 = true : $scope.down_14 = false;
    };
    $scope.down_15 = true;
    $scope.down_o = function (m) {
        m === true ? $scope.down_15 = true : $scope.down_15 = false;
    };
    $scope.down_16 = true;
    $scope.down_p = function (m) {
        m === true ? $scope.down_16 = true : $scope.down_16 = false;
    };
    $scope.down_17 = true;
    $scope.down_q = function (m) {
        m === true ? $scope.down_17 = true : $scope.down_17 = false;
    };
    /*已下架表格Menu切换 结束*/

    /*等待上架表格Menu切换 开始*/
    $scope.wait_menu_flag = false;
    $scope.wait_menu = function (m) {
        m === true ? $scope.wait_menu_flag = false : $scope.wait_menu_flag = true;
    };

    $scope.wait_1 = true;
    $scope.wait_a = function (m) {
        m === true ? $scope.wait_1 = true : $scope.wait_1 = false;
    };
    $scope.wait_2 = true;
    $scope.wait_b = function (m) {
        m === true ? $scope.wait_2 = true : $scope.wait_2 = false;
    };
    $scope.wait_3 = true;
    $scope.wait_c = function (m) {
        m === true ? $scope.wait_3 = true : $scope.wait_3 = false;
    };
    $scope.wait_4 = false;
    $scope.wait_d = function (m) {
        m === true ? $scope.wait_4 = true : $scope.wait_4 = false;
    };
    $scope.wait_5 = false;
    $scope.wait_e = function (m) {
        m === true ? $scope.wait_5 = true : $scope.wait_5 = false;
    };
    $scope.wait_6 = true;
    $scope.wait_f = function (m) {
        m === true ? $scope.wait_6 = true : $scope.wait_6 = false;
    };
    $scope.wait_7 = true;
    $scope.wait_g = function (m) {
        m === true ? $scope.wait_7 = true : $scope.wait_7 = false;
    };
    $scope.wait_8 = false;
    $scope.wait_h = function (m) {
        m === true ? $scope.wait_8 = true : $scope.wait_8 = false;
    };
    $scope.wait_9 = false;
    $scope.wait_i = function (m) {
        m === true ? $scope.wait_9 = true : $scope.wait_9 = false;
    };
    $scope.wait_10 = true;
    $scope.wait_j = function (m) {
        m === true ? $scope.wait_10 = true : $scope.wait_10 = false;
    };
    $scope.wait_11 = true;
    $scope.wait_k = function (m) {
        m === true ? $scope.wait_11 = true : $scope.wait_11 = false;
    };
    $scope.wait_12 = true;
    $scope.wait_l = function (m) {
        m === true ? $scope.wait_12 = true : $scope.wait_12 = false;
    };
    $scope.wait_13 = true;
    $scope.wait_m = function (m) {
        m === true ? $scope.wait_13 = true : $scope.wait_13 = false;
    };
    $scope.wait_14 = true;
    $scope.wait_n = function (m) {
        m === true ? $scope.wait_14 = true : $scope.wait_14 = false;
    };
    $scope.wait_15 = true;
    $scope.wait_o = function (m) {
        m === true ? $scope.wait_15 = true : $scope.wait_15 = false;
    };
    $scope.wait_16 = true;
    $scope.wait_p = function (m) {
        m === true ? $scope.wait_16 = true : $scope.wait_16 = false;
    };
    /*等待上架表格Menu切换 结束*/

    /*已删除Menu切换 开始*/
    $scope.del_menu_flag = false;
    $scope.del_menu = function (m) {
        m === true ? $scope.del_menu_flag = false : $scope.del_menu_flag = true;
    };
    $scope.del_1 = true;
    $scope.del_2 = true;
    $scope.del_3 = true;
    $scope.del_6 = true;
    $scope.del_7 = true;
    $scope.del_9 = true;
    $scope.del_10 = true;
    $scope.del_11 = true;
    $scope.del_12 = true;
    $scope.del_13 = true;
    $scope.del_14 = true;
    $scope.del_4 = false;
    $scope.del_5 = false;
    $scope.del_8 = false;
    $scope.del_a = function (m) {
        m === true ? $scope.del_1 = true : $scope.del_1 = false;
    };
    $scope.del_b = function (m) {
        m === true ? $scope.del_2 = true : $scope.del_2 = false;
    };
    $scope.del_c = function (m) {
        m === true ? $scope.del_3 = true : $scope.del_3 = false;
    };
    $scope.del_d = function (m) {
        m === true ? $scope.del_4 = true : $scope.del_4 = false;
    };
    $scope.del_e = function (m) {
        m === true ? $scope.del_5 = true : $scope.del_5 = false;
    };
    $scope.del_f = function (m) {
        m === true ? $scope.del_6 = true : $scope.del_6 = false;
    };
    $scope.del_g = function (m) {
        m === true ? $scope.del_7 = true : $scope.del_7 = false;
    };
    $scope.del_h = function (m) {
        m === true ? $scope.del_8 = true : $scope.del_8 = false;
    };
    $scope.del_i = function (m) {
        m === true ? $scope.del_9 = true : $scope.del_9 = false;
    };
    $scope.del_j = function (m) {
        m === true ? $scope.del_10 = true : $scope.del_10 = false;
    };
    $scope.delt_k = function (m) {
        m === true ? $scope.del_11 = true : $scope.del_11 = false;
    };
    $scope.del_l = function (m) {
        m === true ? $scope.del_12 = true : $scope.del_12 = false;
    };
    $scope.del_m = function (m) {
        m === true ? $scope.del_13 = true : $scope.del_13 = false;
    };
    $scope.del_n = function (m) {
        m === true ? $scope.del_14 = true : $scope.del_14 = false;
    };

    /*已删除Menu切换 结束*/

    /*===========================================已上架开始=============================================*/
    /*已上架商品列表*/
    function onlinegoodsTable() {
        $scope.online_search = '';
        $scope.onlinetime_ascorder = false;
        $scope.onlinetime_desorder = true;
        $scope.onlinesold_ascorder = false;
        $scope.onlinesold_desorder = true;
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params: {status: 2, supplier_id: +$stateParams.storeid,"sort[]": "online_time:3"}
        }).then(function (res) {
            $scope.onlinegoods = res.data.data.goods_list_admin.details;
            $scope.onpages = Math.ceil(res.data.data.goods_list_admin.total / 12); //分页数
            $scope.onnewPages = $scope.onpages > 5 ? 5 : $scope.onpages;
            $scope.onpageList = [];
            $scope.onselPage = 1;
            for (var i = 0; i < $scope.onnewPages; i++) {
              $scope.onpageList.push(i + 1);
            }
        })
    }

    let newpageList = [];

    $scope.onSelectPage = function (page) {
        if (page < 1 || page > $scope.onpages) {
            return;
        } else {
            $http({
                method: "get",
                params: {status: 2, supplier_id: +$stateParams.storeid, page: page},
                url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            }).then(function (response) {
                $scope.onselPage = page;
                $scope.onlinegoods = response.data.data.goods_list_admin.details;
                $scope.onIsActivePage(page);
                console.log("选择的页：" + page);
            })
            if (page > 2) {
                newpageList.length = 0;
                for (var i = (page - 3); i < ((page + 2) > $scope.onpages ? $scope.onpages : (page + 2)); i++) {
                    newpageList.push(i + 1);
                }
                $scope.onpageList = newpageList;
            }
        }
    }

    $scope.onIsActivePage = function (page) {
        return $scope.onselPage == page;
   };

    /*单个商品下架*/
    $scope.singlegoodOffline = function (id) {
        $scope.tempoffgoodid = id;
    }


    /*单个商品确认下架*/
    $scope.sureGoodOffline = function () {
        let url = "http://test.cdlhzz.cn:888/mall/goods-status-toggle";
        let data = {id: Number($scope.tempoffgoodid), offline_reason: $scope.offline_reason||''};
        $http.post(url, data, config).then(function (res) {
            console.log(res);
            $scope.offline_reason = '';
        })
    }

    $scope.cancelSingleOffline = function () {
        $scope.offline_reason = '';
    }

    /*已上架列表全选*/
    $scope.allOffline = function (m) {
        for (let i = 0; i < $scope.onlinegoods.length; i++) {
            if (m === true) {
                $scope.onlinegoods[i].state = false;
                $scope.selectonlineAll = false;
            } else {
                $scope.onlinegoods[i].state = true;
                $scope.selectonlineAll = true;
            }
        }
    }


    /*已上架列表 批量下架*/
    $scope.piliangxiajia = function () {
        $scope.xiajiaarr.length = 0;
        for (let [key, value] of $scope.onlinegoods.entries()) {
            if (value.state) {
                $scope.xiajiaarr.push(value.id)
            }
        }
    }

    /*确认批量下架*/
    $scope.surepiliangoffline = function () {
        $scope.piliangoffids = $scope.xiajiaarr.join(',');
        let url = "http://test.cdlhzz.cn:888/mall/goods-disable-batch";
        let data = {ids: $scope.piliangoffids, offline_reason: $scope.piliangofflinereason};
        $http.post(url, data, config).then(function (res) {
            $scope.piliangofflinereason = ''
        })
    }

    /*取消批量下架*/
    $scope.cancelplliangoffline = function () {
        $scope.piliangofflinereason = '';
        for (let i = 0; i < $scope.onlinegoods.length; i++) {
                $scope.onlinegoods[i].state = false;
                $scope.selectonlineAll = false;
        }
    }


    /*已上架搜索*/
    $scope.onlineSearch = function () {
        $scope.onlinetime_ascorder = false;
        $scope.onlinetime_desorder = true;

        $scope.onlinesold_ascorder = false;
        $scope.onlinesold_desorder = true;

        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params: {status: 2, supplier_id: +$stateParams.storeid, keyword: $scope.online_search}
        }).then(function (res) {
            $scope.onlinegoods = res.data.data.goods_list_admin.details;
        })
    }

    /*已上架销量排序*/
    $scope.onlineSoldAscorder = function () {
        $scope.onlinetime_ascorder = false;
        $scope.onlinetime_desorder = true;

        $scope.onlinesold_ascorder = true;
        $scope.onlinesold_desorder = false;
        $http({
            method: "get",
            params: {status: 2, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:4"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.onlinegoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }

    $scope.onlineSoldDesorder = function () {
        $scope.onlinetime_ascorder = false;
        $scope.onlinetime_desorder = true;

        $scope.onlinesold_ascorder = false;
        $scope.onlinesold_desorder = true;
        $http({
            method: "get",
            params: {status: 2, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:3"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.onlinegoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }

    /*已上架上架时间排序*/
    $scope.onlineTimeAscorder = function () {
        $scope.onlinesold_ascorder = false;
        $scope.onlinesold_desorder = true;

        $scope.onlinetime_ascorder = true;
        $scope.onlinetime_desorder = false;
        $http({
            method: "get",
            params: {status: 2, supplier_id: +$stateParams.storeid, "sort[]": "online_time:4"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.onlinegoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })

    }

    $scope.onlineTimeDesorder = function () {
        $scope.onlinesold_ascorder = false;
        $scope.onlinesold_desorder = true;

        $scope.onlinetime_ascorder = false;
        $scope.onlinetime_desorder = true;
        $http({
            method: "get",
            params: {status: 2, supplier_id: +$stateParams.storeid},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.onlinegoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }


    /*===============================================已下架开始=======================================================*/
    /*已下架商品列表*/
  function offlinegoodsTable (){
       $scope.offline_search = '';
       $scope.offlinesold_ascorder = false;
       $scope.offlinesold_desorder = true;
       $scope.offlinetime_ascorder = false;
       $scope.offlinetime_desorder = true;
       $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "offline_time:3",size:999}
        }).then((res) => {
            $scope.offilinegoods = res.data.data.goods_list_admin.details;
        })
    }


    /*单个商品上架*/
    $scope.singlegoodOnline = function (id) {
        $scope.tempgoodid = id;
    }

    /*单个商品确认上架*/
    $scope.sureGoodOnline = function () {
        let url = "http://test.cdlhzz.cn:888/mall/goods-status-toggle";
        console.log($scope.tempgoodid)
        let data = {id: Number($scope.tempgoodid)};
        $http.post(url, data, config).then(function (res) {
            console.log(res)
            /*由于某些原因不能上架*/
            if (res.data.code != 200) {
                // console.log(res)
                $('#up_shelves_modal').modal("hide");
                $("#up_not_shelves_modal").modal("show")
                $scope.cantonline = res.data.msg;
            } else {
                /*可以上架*/
                $('#up_shelves_modal').modal("hide");
            }
        })
    }

    /*单个商品取消上架操作无*/

    /*不能上架 确认*/
    $scope.sureCantOnline = function () {
        $scope.cantonlinemodal = ""
    }

    /*已下架列表全选*/
    $scope.allOnline = function (m) {
        for (let i = 0; i < $scope.offilinegoods.length; i++) {
            if (m === true) {
                $scope.offilinegoods[i].state = false;
                $scope.selectoffAll = false;
            } else {
                $scope.offilinegoods[i].state = true;
                $scope.selectoffAll = true;
            }
        }
    }

    /*已下架列表 批量上架*/
    $scope.piliangshangjia = function () {
        $scope.shangjiaarr.length = 0;
        for (let [key, value] of $scope.offilinegoods.entries()) {
            if (value.state) {
                $scope.shangjiaarr.push(value.id)
            }
        }
    }

    /*确认批量上架*/
    $scope.surepiliangonline = function () {
        $scope.piliangonids = $scope.shangjiaarr.join(',');
        let url = "http://test.cdlhzz.cn:888/mall/goods-enable-batch";
        let data = {ids: $scope.piliangonids};
        $http.post(url, data, config).then(function (res) {
            /*由于某些原因不能上架*/
            if (res.data.code != 200) {
                $('#piliangonline_modal').modal("hide");
                $("#up_not_shelves_modal").modal("show")
                $scope.cantonline = res.data.msg;
            } else {
                /*可以上架*/
                $('#piliangonline_modal').modal("hide");
            }

        })
    }

    /*取消批量上架*/
    $scope.cancelplliangonline = function () {
        for (let i = 0; i < $scope.offilinegoods.length; i++) {
                $scope.offilinegoods[i].state = false;
                $scope.selectoffAll = false;
        }
    }



    /*已下架搜索*/
    $scope.offlineSearch = function () {
        $scope.offlinesold_ascorder = false;
        $scope.offlinesold_desorder = true;

        $scope.offlinetime_ascorder = false;
        $scope.offlinetime_desorder = true;
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params: {status: 0, supplier_id: +$stateParams.storeid, keyword: $scope.offline_search}
        }).then(function (res) {
            $scope.offilinegoods = res.data.data.goods_list_admin.details;
        })
    }

    /*已下架销量排序*/
    $scope.offlineSoldDesorder = function () {
        $scope.offlinetime_ascorder = false;
        $scope.offlinetime_desorder = true;

        $scope.offlinesold_ascorder = false;
        $scope.offlinesold_desorder = true;
        $http({
            method: "get",
            params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:3"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.offilinegoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }

    $scope.offlineSoldAscorder = function () {
        $scope.offlinetime_ascorder = false;
        $scope.offlinetime_desorder = true;

        $scope.offlinesold_ascorder = true;
        $scope.offlinesold_desorder = false;
        $http({
            method: "get",
            params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:4"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            console.log(response)
            $scope.offilinegoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }

    /*已下架 下架时间排序*/
    $scope.offlineTimeDesorder = function () {

        $scope.offlinesold_ascorder = false;
        $scope.offlinesold_desorder = true;
        $scope.offlinetime_ascorder = false;
        $scope.offlinetime_desorder = true;
        $http({
            method: "get",
            params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "offline_time:3"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.offilinegoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }

    $scope.offlineTimeAscorder = function () {
        $scope.offlinesold_ascorder = false;
        $scope.offlinesold_desorder = true;

        $scope.offlinetime_ascorder = true;
        $scope.offlinetime_desorder = false;
        $http({
            method: "get",
            params: {status: 0, supplier_id: +$stateParams.storeid, "sort[]": "offline_time:4"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.offilinegoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }

   $scope.showreason_modal = '';
    let tempId;

    /*可以编辑的已下架原因的处理*/
    $scope.editReason = (id,reason) => {
        $scope.modal_offreason = reason || '';
        tempId = id;
    }

    /*重设下架原因*/
    $scope.sureResetReason = () => {
        let url = "http://test.cdlhzz.cn:888/mall/goods-offline-reason-reset";
        let data = {id:tempId,offline_reason:$scope.modal_offreason};
        $http.post(url, data, config).then((res) => {
            offlinegoodsTable();
        })
    }
    /*======================================已下架结束=======================================================*/



    /*========================================等待上架开始================================================*/

    /*等待上架列表*/
    function waitgoodsTable() {
        $scope.wait_search = '';
        $scope.waittime_desorder = true;
        $scope.waittime_ascorder = false;
        $scope.waittime_desorder = true;
        $scope.waitsold_ascorder = false;
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params: {status: 1, supplier_id: +$stateParams.storeid,create_time:4}
        }).then(function (res) {
            $scope.waitgoods = res.data.data.goods_list_admin.details;

        })
    }

    /*等待上架 单个上架*/
    $scope.waitToOnline = function (id) {
        $scope.tempwaitgoodid = id;

    }

    /*等待上架 单个确认上架*/
    $scope.sureWaitToOnline = function () {
        let url = "http://test.cdlhzz.cn:888/mall/goods-status-toggle";
        let data = {id: $scope.tempwaitgoodid};
        $http.post(url, data, config).then(function (res) {
            /*由于某些原因不能上架*/
            if (res.data.code != 200) {
                // console.log(res)
                $('#waitup_shelves_modal').modal("hide");
                $("#waitup_not_shelves_modal").modal("show")
                $scope.waitcantonline = res.data.msg;
            } else {
                /*可以上架*/
                $('#waitup_shelves_modal').modal("hide");
            }
        })
    }

    /*等待上架 销量逆序*/
    $scope.waitSoldDesorder = function () {
        $scope.waittime_desorder = true;
        $scope.waittime_ascorder = false;
        $scope.waitsold_ascorder = false;
        $scope.waitsold_desorder = true;
        $http({
            method:"get",
            url:"http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params:{status: 1, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:3"}
        }).then(function (res) {
            $scope.waitgoods = res.data.data.goods_list_admin.details;
        })
    }

    /*等待上架 销量顺序*/
    $scope.waitSoldAscorder = function () {
        $scope.waittime_desorder = true;
        $scope.waittime_ascorder = false;

        $scope.waitsold_ascorder = true;
        $scope.waitsold_desorder = false;
        $http({
            method:"get",
            url:"http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params:{status: 1, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:4"}
        }).then(function (res) {
            $scope.waitgoods = res.data.data.goods_list_admin.details;
        })

    }



    /*等待上架 创建时间逆序*/
    $scope.waitTimeDesorder = function () {
        $scope.waittime_desorder = true;
        $scope.waittime_ascorder = false;

        $scope.waitsold_ascorder = false;
        $scope.waitsold_desorder = true;
        $http({
            method:"get",
            url:"http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params:{status: 1, supplier_id: +$stateParams.storeid, "sort[]": "publish_time:3"}
        }).then(function (res) {
            $scope.waitgoods = res.data.data.goods_list_admin.details;
        })
    }

    /*等待上架 创建时间顺序*/
    $scope.waitTimeAscorder = function () {
        $scope.waittime_desorder = false;
        $scope.waittime_ascorder = true;

        $scope.waitsold_ascorder = false;
        $scope.waitsold_desorder = true;
        $http({
            method:"get",
            url:"http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params:{status: 1, supplier_id: +$stateParams.storeid, "sort[]": "publish_time:4"}
        }).then(function (res) {
            $scope.waitgoods = res.data.data.goods_list_admin.details;
        })

    }


    /*等待上架 搜索*/
    $scope.waitSearch = function () {
        $scope.waittime_desorder = true;
        $scope.waittime_ascorder = false;
        $scope.waitsold_desorder = true;
        $scope.waitsold_ascorder = false;
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params: {status: 1, supplier_id: +$stateParams.storeid, keyword: $scope.wait_search}
        }).then(function (res) {
            $scope.waitgoods = res.data.data.goods_list_admin.details;
        })
    }

    /*等待上架全选*/
   $scope.allwait = function (m) {
       for (let i = 0; i < $scope.waitgoods.length; i++) {
           if (m === true) {
               $scope.waitgoods[i].state = false;
               $scope.selectwaitAll = false;
           } else {
               $scope.waitgoods[i].state = true;
               $scope.selectwaitAll = true;
           }
       }
   }
   
   /*等待上架批量上架*/
   $scope.allwaitToOnline = function () {
       $scope.waitarr.length = 0;
       for (let [key, value] of $scope.waitgoods.entries()) {
           if (value.state) {
               $scope.waitarr.push(value.id)
           }
       }
   }
    /*等待上架确认批量上架*/
    $scope.surewaitonline = function () {
        $scope.allwaitonids = $scope.waitarr.join(',');
        let url = "http://test.cdlhzz.cn:888/mall/goods-enable-batch";
        let data = {ids: $scope.allwaitonids};
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
            }
        })
    }

    /*等待上架 取消批量上架*/
    $scope.cancelWaitOnline = function () {
        for (let i = 0; i < $scope.waitgoods.length; i++) {
                $scope.waitgoods[i].state = false;
                $scope.selectwaitAll = false;
        }
    }


    /*更新审核备注*/
    $scope.checkReason = function (id,reason) {
        checkId = id;
        $scope.lastreason = reason;
    }


    /*确认更新审核备注*/
    $scope.sureCheckReason = function () {
        let url = "http://test.cdlhzz.cn:888/mall/goods-reason-reset";
        let data  = {id:Number(checkId),reason:$scope.lastreason||''};
        $http.post(url,data,config).then((res)=>{
            console.log(res);
            waitgoodsTable();
        })
    }


    /*=======================================已删除开始======================================================*/
    /*已删除列表*/
    function deletegoodsTable() {
        $scope.deleted_search = ''
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params: {status: 3, supplier_id: +$stateParams.storeid,"sort[]": "delete_time:3"}
        }).then(function (res) {
            $scope.deletedgoods = res.data.data.goods_list_admin.details;
        })
    }

    /*已删除搜索*/
    $scope.deletedSearch = function () {
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
            params: {status: 3, supplier_id: +$stateParams.storeid, keyword: $scope.deleted_search}
        }).then(function (res) {
            $scope.deletedgoods = res.data.data.goods_list_admin.details;
        })
    }

    /*已删除销量排序*/
    $scope.deletedSoldDesorder = function () {
        $scope.deletedsold_ascorder = false;
        $scope.deletedsold_desorder = true;
        $http({
            method: "get",
            params: {status: 3, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:3"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.deletedgoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }

    $scope.deletedSoldAscorder = function () {
        $scope.deletedsold_ascorder = true;
        $scope.deletedsold_desorder = false;
        $http({
            method: "get",
            params: {status: 3, supplier_id: +$stateParams.storeid, "sort[]": "sold_number:4"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.deletedgoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }


    /*已删除删除时间排序*/
    $scope.deletedTimeDesorder = function () {
        $scope.deletedtime_ascorder = false;
        $scope.deletedtime_desorder = true;
        $http({
            method: "get",
            params: {status: 3, supplier_id: +$stateParams.storeid, "sort[]": "delete_time:3"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.deletedgoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }

    $scope.deletedTimeAscorder = function () {
        $scope.deletedtime_ascorder = true;
        $scope.deletedtime_desorder = false;
        $http({
            method: "get",
            params: {status: 3, supplier_id: +$stateParams.storeid, "sort[]": "delete_time:4"},
            url: "http://test.cdlhzz.cn:888/mall/goods-list-admin",
        }).then(function (response) {
            $scope.deletedgoods = response.data.data.goods_list_admin.details;
            // $scope.selPage = 1;
        })
    }


});


