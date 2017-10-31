;
let commodity_manage = angular.module("commodity_manage", [])
    .controller("commodity_manage_ctrl", function ($scope, $http, $state, $stateParams) {
        $scope.myng = $scope;
        /*POST请求头*/
        const config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        //初始化已上架Menu状态
        $scope.up_menu_init = function () {
            $scope.show_1 = true;
            $scope.show_2 = true;
            $scope.show_3 = true;
            $scope.show_4 = true;
            $scope.show_5 = true;
            $scope.show_6 = false;
            $scope.show_7 = false;
            $scope.show_8 = false;
            $scope.show_9 = true;
            $scope.show_10 = true;
            $scope.show_11 = true;
            $scope.show_12 = true;
            $scope.show_13 = true;
            $scope.show_14 = true;
            $scope.show_15 = true;
        }
        $scope.up_menu_init();
        /*已下架表格Menu切换 开始*/
        $scope.down_menu_init = function () {
            $scope.down_1 = true;
            $scope.down_2 = true;
            $scope.down_3 = true;
            $scope.down_4 = true;
            $scope.down_5 = true;
            $scope.down_6 = false;
            $scope.down_7 = false;
            $scope.down_8 = false;
            $scope.down_9 = true;
            $scope.down_10 = false;
            $scope.down_11 = true;
            $scope.down_12 = true;
            $scope.down_13 = true;
            $scope.down_14 = true;
            $scope.down_15 = true;
            $scope.down_16 = true;
            $scope.down_17 = true;
        }
        $scope.down_menu_init();
        $scope.down_list_arr = [];
        $scope.up_list_arr = [];
        /*页面Menu切换 开始*/
        $scope.on_flag = true;
        $scope.down_flag = false;
        $scope.wait_flag = false;
        $scope.logistics_flag = false;

        /*分页配置*/
        $scope.wjConfig = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                $scope.table.roles=[];//清空全选状态
                tablePages();
            }
        }
        let tablePages = function () {
            $scope.params.page = $scope.wjConfig.currentPage;//点击页数，传对应的参数
            $http.get(baseUrl + '/mall/goods-list-admin', {
                params: $scope.params
            }).then(function (res) {
                console.log(res);
                if ($scope.on_flag == true) {
                    $scope.up_list_arr = res.data.data.goods_list_admin.details;
                } else if ($scope.down_flag == true) {
                    $scope.down_list_arr = res.data.data.goods_list_admin.details;
                }
                $scope.wjConfig.totalItems = res.data.data.goods_list_admin.total;
            }, function (err) {
                console.log(err);
            })
        };
        $scope.params = {
            status: 2,                       //状态
            'sort[]': 'online_time:3',      //默认排序
            keyword: '',                    // 关键字查询
        };

        //    全选
        //全选ID数组
        $scope.table = {
            roles: [],
        };
        $scope.checkAll = function () {
            !$scope.table.roles.length ? $scope.table.roles = $scope.up_list_arr.map(function (item) {
                return item.id;
            }) : $scope.table.roles.length = 0;
        };

        //已上架
        if ($stateParams.on_flag == true) {
            $scope.on_flag = true;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;
            // 初始化已上架搜索
            $scope.up_search_value = '';//搜索输入框的值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'online_time:3';
            $scope.params.status = 2;
            $scope.up_menu_init();
            tablePages();
        }
        //已下架
        if ($stateParams.down_flag == true) {
            $scope.on_flag = false;
            $scope.down_flag = true;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;
            /*初始化已下架的搜索*/
            $scope.down_search_value = '';//清空输入框值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'offline_time:3';
            $scope.params.status = 0;
            $scope.down_menu_init();
            tablePages();
        }

        //已上架
        $scope.on_shelves = function () {
            $scope.on_flag = true;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;

            // 初始化已上架搜索
            $scope.up_search_value = '';//搜索输入框的值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'online_time:3';
            $scope.params.status = 2;
            $scope.up_menu_init();
            $scope.table.roles=[];//清空全选状态
            tablePages();
        };
        //已下架
        $scope.down_shelves = function () {
            $scope.down_flag = true;
            $scope.on_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;
            /*初始化已下架的搜索*/
            $scope.down_search_value = '';//清空输入框值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'offline_time:3';
            $scope.params.status = 0;
            $scope.down_menu_init();
            $scope.table.roles=[];//清空全选状态
            tablePages();
        };
        //等待下架
        $scope.wait_shelves = function () {
            $scope.wait_flag = true;
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.logistics_flag = false;
        };
        //物流模块
        $scope.logistics = function () {
            $scope.logistics_flag = true;
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = false;
        };
        /*页面Menu切换 结束*/


        /*已上架表格Menu切换 开始*/


        $scope.show_a = function (m) {
            m === true ? $scope.show_1 = true : $scope.show_1 = false;
        };
        $scope.show_b = function (m) {
            m === true ? $scope.show_2 = true : $scope.show_2 = false;
        };
        $scope.show_c = function (m) {
            m === true ? $scope.show_3 = true : $scope.show_3 = false;
        };
        $scope.show_d = function (m) {
            m === true ? $scope.show_4 = true : $scope.show_4 = false;
        };
        $scope.show_e = function (m) {
            m === true ? $scope.show_5 = true : $scope.show_5 = false;
        };
        $scope.show_f = function (m) {
            m === true ? $scope.show_6 = true : $scope.show_6 = false;
        };
        $scope.show_g = function (m) {
            m === true ? $scope.show_7 = true : $scope.show_7 = false;
        };
        $scope.show_h = function (m) {
            m === true ? $scope.show_8 = true : $scope.show_8 = false;
        };
        $scope.show_i = function (m) {
            m === true ? $scope.show_9 = true : $scope.show_9 = false;
        };
        $scope.show_j = function (m) {
            m === true ? $scope.show_10 = true : $scope.show_10 = false;
        };
        $scope.show_k = function (m) {
            m === true ? $scope.show_11 = true : $scope.show_11 = false;
        };
        $scope.show_l = function (m) {
            m === true ? $scope.show_12 = true : $scope.show_12 = false;
        };
        $scope.show_m = function (m) {
            m === true ? $scope.show_13 = true : $scope.show_13 = false;
        };
        $scope.show_n = function (m) {
            m === true ? $scope.show_14 = true : $scope.show_14 = false;
        };
        $scope.show_n = function (m) {
            m === true ? $scope.show_15 = true : $scope.show_15 = false;
        };
        /*已上架表格Menu切换 结束*/


        $scope.down_a = function (m) {
            m === true ? $scope.down_1 = true : $scope.down_1 = false;
        };
        $scope.down_b = function (m) {
            m === true ? $scope.down_2 = true : $scope.down_2 = false;
        };
        $scope.down_c = function (m) {
            m === true ? $scope.down_3 = true : $scope.down_3 = false;
        };
        $scope.down_d = function (m) {
            m === true ? $scope.down_4 = true : $scope.down_4 = false;
        };
        $scope.down_e = function (m) {
            m === true ? $scope.down_5 = true : $scope.down_5 = false;
        };
        $scope.down_f = function (m) {
            m === true ? $scope.down_6 = true : $scope.down_6 = false;
        };
        $scope.down_g = function (m) {
            m === true ? $scope.down_7 = true : $scope.down_7 = false;
        };
        $scope.down_h = function (m) {
            m === true ? $scope.down_8 = true : $scope.down_8 = false;
        };
        $scope.down_i = function (m) {
            m === true ? $scope.down_9 = true : $scope.down_9 = false;
        };
        $scope.down_j = function (m) {
            m === true ? $scope.down_10 = true : $scope.down_10 = false;
        };
        $scope.down_k = function (m) {
            m === true ? $scope.down_11 = true : $scope.down_11 = false;
        };
        $scope.down_l = function (m) {
            m === true ? $scope.down_12 = true : $scope.down_12 = false;
        };
        $scope.down_m = function (m) {
            m === true ? $scope.down_13 = true : $scope.down_13 = false;
        };
        $scope.down_n = function (m) {
            m === true ? $scope.down_14 = true : $scope.down_14 = false;
        };
        $scope.down_o = function (m) {
            m === true ? $scope.down_15 = true : $scope.down_15 = false;
        };
        $scope.down_p = function (m) {
            m === true ? $scope.down_16 = true : $scope.down_16 = false;
        };
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
        $scope.wait_4 = true;
        $scope.wait_d = function (m) {
            m === true ? $scope.wait_4 = true : $scope.wait_4 = false;
        };
        $scope.wait_5 = true;
        $scope.wait_e = function (m) {
            m === true ? $scope.wait_5 = true : $scope.wait_5 = false;
        };
        $scope.wait_6 = false;
        $scope.wait_f = function (m) {
            m === true ? $scope.wait_6 = true : $scope.wait_6 = false;
        };
        $scope.wait_7 = false;
        $scope.wait_g = function (m) {
            m === true ? $scope.wait_7 = true : $scope.wait_7 = false;
        };
        $scope.wait_8 = false;
        $scope.wait_h = function (m) {
            m === true ? $scope.wait_8 = true : $scope.wait_8 = false;
        };
        $scope.wait_9 = true;
        $scope.wait_i = function (m) {
            m === true ? $scope.wait_9 = true : $scope.wait_9 = false;
        };
        $scope.wait_10 = false;
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
        /*等待上架表格Menu切换 结束*/

        /*-------------------公共功能 开始---------------------------*/

        //实时监听库存并修改
        $scope.change_left_number = function (id, left_num) {
            $http.post('http://test.cdlhzz.cn:888/mall/goods-inventory-reset', {
                id: +id,
                left_number: +left_num
            }, config).then(function (res) {
                console.log(res);
            }, function (err) {
                console.log(err);
            })
        };

        /*-------------------公共功能 结束---------------------------*/
        /*--------------------已上架 开始-------------------------*/
        //$scope.up_list_arr=[]

        /*-------------------销量排序-----------------------*/
        $scope.up_sort_sale_img = 'lib/images/arrow_default.png';
        $scope.up_sort_sale_click = function () {
            $scope.up_sort_time_img = 'lib/images/arrow_default.png';
            if ($scope.up_sort_sale_img == 'lib/images/arrow_default.png') {
                $scope.up_sort_sale_img = 'lib/images/arrow_down.png';
                $scope.params['sort[]'] = 'sold_number:3';
            } else if ($scope.up_sort_sale_img == 'lib/images/arrow_down.png') { //------> 升序
                $scope.up_sort_sale_img = 'lib/images/arrow_up.png';
                $scope.params['sort[]'] = 'sold_number:4';
            } else {                                                //-------> 降序
                $scope.up_sort_sale_img = 'lib/images/arrow_down.png';
                $scope.params['sort[]'] = 'sold_number:3';
            }
            $scope.table.roles=[];//清空全选状态
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            tablePages();
        }
        /*-----------------时间排序-----------------------*/
        $scope.up_sort_time_img = 'lib/images/arrow_down.png';
        $scope.up_sort_time_click = function () {
            $scope.up_sort_sale_img = 'lib/images/arrow_default.png'
            if ($scope.up_sort_time_img == 'lib/images/arrow_default.png') {
                $scope.up_sort_time_img = 'lib/images/arrow_down.png';
                $scope.params['sort[]'] = 'online_time:3';
            } else if ($scope.up_sort_time_img == 'lib/images/arrow_down.png') { //------> 升序
                $scope.up_sort_time_img = 'lib/images/arrow_up.png';
                $scope.params['sort[]'] = 'online_time:4';
            } else {                                                //-------> 降序
                $scope.up_sort_time_img = 'lib/images/arrow_down.png';
                $scope.params['sort[]'] = 'online_time:3';
            }
            $scope.table.roles=[];//清空全选状态
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            tablePages();
        }

        /*------------单个下架商品------------*/
        //点击下架
        $scope.offline_solo = function (id) {
            $scope.offline_id = id;
        };
        //确认下架按钮
        $scope.offline_solo_btn = function () {
            $http.post('http://test.cdlhzz.cn:888/mall/goods-status-toggle', {
                id: $scope.offline_id
            }, config).then(function (res) {
                console.log(res);
                tablePages();
            }, function (err) {
                console.log(err);
            })
        };

        /*------------批量下架商品------------------*/
        //点击下架按钮
        $scope.all_off_shelf = function () {
            if($scope.table.roles.length!=0){
                $scope.prompt = '#down_shelves_modal'
            }else{
                $scope.prompt = '#please_check';
            }
        };
        //下架确认按钮
        $scope.all_off_shelf_confirm = function () {
            $http.post('http://test.cdlhzz.cn:888/mall/goods-disable-batch', {
                ids: $scope.table.roles.join(',')
            }, config).then(function (res) {
                /*重新请求数据，达到刷新的效果*/
                $scope.table.roles=[];//清空全选状态
                tablePages();
            }, function (err) {
                console.log(err);
            })
        };

        /*----------------搜索---------------*/
        $scope.search_btn = function () {
            $scope.up_sort_time_img = 'lib/images/arrow_down.png';
            $scope.up_sort_sale_img = 'lib/images/arrow_default.png';
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            $scope.params.keyword = $scope.up_search_value;
            $scope.params['sort[]'] = 'online_time:3';
            $scope.table.roles=[];//清空全选状态
            tablePages();
        };


        /*-----------添加分类--------------*/
        $scope.item_check = [];
        //获取一级
        $http({
            method: 'get',
            url: 'http://test.cdlhzz.cn:888/mall/categories'
        }).then(function successCallback(response) {
            $scope.details = response.data.data.categories;
            $scope.oneColor = $scope.details[0];
        });
        //获取二级
        $http({
            method: 'get',
            url: 'http://test.cdlhzz.cn:888/mall/categories?pid=1'
        }).then(function successCallback(response) {
            $scope.second = response.data.data.categories;
            $scope.twoColor = $scope.second[0];
        });
        //获取三级
        $http({
            method: 'get',
            url: 'http://test.cdlhzz.cn:888/mall/categories?pid=2'
        }).then(function successCallback(response) {
            $scope.three = response.data.data.categories;
            for (let [key, value] of $scope.three.entries()) {
                if ($scope.item_check.length == 0) {
                    value['complete'] = false
                } else {
                    for (let [key1, value1] of $scope.item_check.entries()) {
                        if (value.id == value1.id) {
                            value.complete = true
                        }
                    }
                }
            }
        });
        //点击一级 获取相对应的二级
        $scope.getMore = function (n) {
            $scope.oneColor = n;
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/categories?pid=' + n.id
            }).then(function successCallback(response) {
                $scope.second = response.data.data.categories;
                //console.log(response.data.data.categories[0].id);
                console.log(response);
                $scope.twoColor = $scope.second[0];
                $http({
                    method: 'get',
                    url: 'http://test.cdlhzz.cn:888/mall/categories?pid=' + $scope.second[0].id
                }).then(function successCallback(response) {
                    $scope.three = response.data.data.categories;
                    //console.log(response.data.data.categories[0].id);
                    for (let [key, value] of $scope.three.entries()) {
                        if ($scope.item_check.length == 0) {
                            value['complete'] = false
                        } else {
                            for (let [key1, value1] of $scope.item_check.entries()) {
                                if (value.id == value1.id) {
                                    value.complete = true
                                }
                            }
                        }
                    }
                });
            });
        };
        //点击二级 获取相对应的三级
        $scope.getMoreThree = function (n) {
            $scope.id = n;
            $scope.twoColor = n;
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/categories?pid=' + n.id
            }).then(function successCallback(response) {
                $scope.three = response.data.data.categories;
                for (let [key, value] of $scope.three.entries()) {
                    if ($scope.item_check.length == 0) {
                        value['complete'] = false
                    } else {
                        for (let [key1, value1] of $scope.item_check.entries()) {
                            if (value.id == value1.id) {
                                value.complete = true
                            }
                        }
                    }
                }
            });
        };
        //添加拥有系列的三级
        $scope.check_item = function (item) {
            $scope.item_check = [];
            $scope.threeColor = item;
            $scope.item_check.push(item);
        };

        /*------------分类确定----------------*/
        $scope.category_id = '';
        $scope.add_confirm_red = false;//提示文字默认为false
        $scope.shop_style_go = function () {
            $scope.category_id = '';
            if ($scope.item_check.length != 0) {
                $scope.add_confirm_modal = 'modal';
                $scope.category_id = $scope.item_check[0].id;
                setTimeout(function () {
                    $state.go('shop_style', ({
                            category_id: $scope.category_id,
                            first_category_title: $scope.oneColor.title,
                            second_category_title: $scope.twoColor.title,
                            third_category_title: $scope.threeColor.title
                        })
                    );
                }, 300);
                $scope.add_confirm_red = false;
            } else {
                $scope.add_confirm_red = true;
            }
        };
        //取消初始化
        $scope.shop_add_close = function () {
            $scope.item_check = [];
            //获取一级
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/categories'
            }).then(function successCallback(response) {
                $scope.details = response.data.data.categories;
                $scope.oneColor = $scope.details[0];
                // console.log(response);
                // console.log($scope.details)
            });
            //获取二级
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/categories?pid=1'
            }).then(function successCallback(response) {
                $scope.second = response.data.data.categories;
                $scope.twoColor = $scope.second[0];
                // console.log($scope.second)
            });
            //获取三级
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/categories?pid=2'
            }).then(function successCallback(response) {
                // console.log(response)
                $scope.three = response.data.data.categories;
                for (let [key, value] of $scope.three.entries()) {
                    if ($scope.item_check.length == 0) {
                        value['complete'] = false
                    } else {
                        for (let [key1, value1] of $scope.item_check.entries()) {
                            if (value.id == value1.id) {
                                value.complete = true
                            }
                        }
                    }
                }
            });
        };
        /*--------------------已上架 结束-------------------------*/

        /*--------------------已下架 开始-------------------------*/

        $scope.down_list_arr = [];
        /*-----------------时间排序-----------------------*/
        $scope.down_sort_time_img = 'lib/images/arrow_down.png';
        $scope.down_sort_time_click = function () {
            //$scope.down_sort_time_img='lib/images/arrow_default.png'
            if ($scope.down_sort_time_img == 'lib/images/arrow_default.png') {
                $scope.down_sort_time_img = 'lib/images/arrow_down.png';
                $scope.params['sort[]'] = 'offline_time:3';
            } else if ($scope.down_sort_time_img == 'lib/images/arrow_down.png') { //------> 升序
                $scope.down_sort_time_img = 'lib/images/arrow_up.png';
                $scope.params['sort[]'] = 'offline_time:4';
            } else {                                                //-------> 降序
                $scope.down_sort_time_img = 'lib/images/arrow_down.png';
                $scope.params['sort[]'] = 'offline_time:3';
            }
            console.log($scope.down_sort_time_img)
            tablePages();
        }

        //单个上架
        $scope.sole_on_shelf = function (id) {
            $scope.on_shelf_id = id;
        };
        //上架确认按钮
        $scope.all_on_shelf_confirm = function () {
            $http.post('http://test.cdlhzz.cn:888/mall/goods-status-toggle', {
                id: +$scope.on_shelf_id
            }, config).then(function (res) {
                console.log(res);
            }, function (err) {
                console.log(err)
            })
        };

        /*----------------搜索---------------*/
        $scope.down_search_btn = function () {
            $scope.down_sort_time_img = 'lib/images/arrow_down.png';
            $scope.params['sort[]'] = 'offline_time:3';
            tablePages();
        };

        //单个删除
        $scope.solo_del_off = function (id) {
            $scope.batch_del = [];
            $scope.batch_del.push(id);
        };
        //删除确认按钮
        $scope.off_del_confirm = function () {
            $http.post('http://test.cdlhzz.cn:888/mall/goods-delete-batch', {
                ids: $scope.batch_del.join(',')
            }, config).then(function (res) {
                console.log(res);
                tablePages();
            }, function (err) {
                console.log(err);
            })
        };
        //下架原因
        $scope.reason_click = function (reason) {
            $scope.down_reason = reason;
        };
        /*--------------------已下架 结束-------------------------*/

        /*--------------------等待上架 开始-------------------------*/
        //实时监听库存并修改
        $scope.change_right_number = function (id, left_num) {
            $http.post('http://test.cdlhzz.cn:888/mall/goods-inventory-reset', {
                id: +id,
                left_number: +left_num
            }, config).then(function (res) {
                console.log(res);
            }, function (err) {
                console.log(err);
            })
        };
        $scope.myng = $scope;
        $scope.wait_list_arr = [];
        $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin', {
                params: {
                    status: 1
                }
            }
        ).then(function (res) {
            console.log('等待上架');
            console.log(res);
            $scope.wait_list_arr = res.data.data.goods_list_admin.details;
            /*--------------------分页------------------------*/
            $scope.wait_history_list = [];
            $scope.wait_history_all_page = Math.ceil(res.data.data.goods_list_admin.total / 12);//获取总页数
            let all_num = $scope.wait_history_all_page;//循环总页数
            for (let i = 0; i < all_num; i++) {
                $scope.wait_history_list.push(i + 1)
            }
            $scope.page = 1;
            //点击数字，跳转到多少页
            $scope.wait_choosePage = function (page) {
                if ($scope.wait_history_list.indexOf(parseInt(page)) != -1) {
                    $scope.page = page;
                    $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin', {
                        params: {
                            status: 1,
                            page: $scope.page,
                            'sort[]': $scope.sort_status
                        }
                    }).then(function (res) {
                        //console.log(res);
                        $scope.wait_list_arr = res.data.data.goods_list_admin.details;
                    }, function (err) {
                        console.log(err);
                    });
                }
            };
            //显示当前是第几页的样式
            $scope.isActivePage = function (page) {
                return $scope.page == page;
            };
            //进入页面，默认设置为第一页
            if ($scope.page === undefined) {
                $scope.page = 1;
            }
            //上一页
            $scope.wait_Previous = function () {
                if ($scope.page > 1) {                //当页数大于1时，执行
                    $scope.page--;
                    $scope.wait_choosePage($scope.page);
                }
            };
            //下一页
            $scope.wait_Next = function () {
                if ($scope.page < $scope.wait_history_all_page) { //判断是否为最后一页，如果不是，页数+1,
                    $scope.page++;
                    $scope.wait_choosePage($scope.page);
                }
            }
        }, function (err) {
            console.log(err)
        });
        //查看获取审核备注
        $scope.getRest = function (item) {
            $scope.reset = item
        }

        /*----------------搜索---------------*/
        $scope.wait_search_btn = function () {
            console.log($scope.wait_search_content)
            $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin', {
                params: {
                    status: 1,
                    keyword: $scope.wait_search_content
                }
            }).then(function (res) {
                console.log(res);
                $scope.wait_list_arr = res.data.data.goods_list_admin.details;
                /*--------------------分页------------------------*/
                $scope.wait_history_list = [];
                $scope.wait_history_all_page = Math.ceil(res.data.data.goods_list_admin.total / 12);//获取总页数
                let all_num = $scope.wait_history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.wait_history_list.push(i + 1)
                }
                $scope.page = 1;
            }, function (err) {
                console.log(err);
            })
        };
        //监听搜索框的值为空时，返回最初的值
        $scope.$watch("off_search_content", function (newVal, oldVal) {
            if (newVal == "") {
                $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin', {
                        params: {
                            status: 1
                        }
                    }
                ).then(function (res) {
                    console.log('等待上架');
                    console.log(res);
                    $scope.wait_list_arr = res.data.data.goods_list_admin.details;
                    /*--------------------分页------------------------*/
                    $scope.wait_history_list = [];
                    $scope.wait_history_all_page = Math.ceil(res.data.data.goods_list_admin.total / 12);//获取总页数
                    let all_num = $scope.wait_history_all_page;//循环总页数
                    for (let i = 0; i < all_num; i++) {
                        $scope.wait_history_list.push(i + 1)
                    }
                    $scope.page = 1;
                    //点击数字，跳转到多少页
                    $scope.wait_choosePage = function (page) {
                        if ($scope.wait_history_list.indexOf(parseInt(page)) != -1) {
                            $scope.page = page;
                            $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin', {
                                params: {
                                    status: 1,
                                    page: $scope.page,
                                    'sort[]': $scope.sort_status
                                }
                            }).then(function (res) {
                                //console.log(res);
                                $scope.wait_list_arr = res.data.data.goods_list_admin.details;
                            }, function (err) {
                                console.log(err);
                            });
                        }
                    };
                    //显示当前是第几页的样式
                    $scope.isActivePage = function (page) {
                        return $scope.page == page;
                    };
                    //进入页面，默认设置为第一页
                    if ($scope.page === undefined) {
                        $scope.page = 1;
                    }
                    //上一页
                    $scope.wait_Previous = function () {
                        if ($scope.page > 1) {                //当页数大于1时，执行
                            $scope.page--;
                            $scope.wait_choosePage($scope.page);
                        }
                    };
                    //下一页
                    $scope.wait_Next = function () {
                        if ($scope.page < $scope.wait_history_all_page) { //判断是否为最后一页，如果不是，页数+1,
                            $scope.page++;
                            $scope.wait_choosePage($scope.page);
                        }
                    }
                })
            }
        });

        /*=======降序=====*/
        $scope.wait_time_sort = function () {
            $scope.sort_status = 'publish_time:3';
            $scope.on_time_flag = false;
            $scope.down_time_flag = true;
            $scope.page = 1;
            $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin', {
                params: {
                    status: 1,
                    page: $scope.page,
                    'sort[]': $scope.sort_status
                }
            }).then(function (res) {
                console.log(res);
                $scope.wait_list_arr = res.data.data.goods_list_admin.details;
                /*--------------------分页------------------------*/
                $scope.wait_history_list = [];
                $scope.wait_history_all_page = Math.ceil(res.data.data.goods_list_admin.total / 12);//获取总页数
                let all_num = $scope.wait_history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.wait_history_list.push(i + 1)
                }
                $scope.page = 1;
            }, function (err) {
                console.log(err)
            })
        };
        /*============升序==================*/
        $scope.on_time_flag = true;
        $scope.down_time_flag = false;
        $scope.wait_time_sort = function (status) {
            $scope.sort_status = 'publish_time:4';
            $scope.on_time_flag = true;
            $scope.down_time_flag = false;
            $scope.page = 1;
            $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin', {
                params: {
                    status: 1,
                    page: $scope.page,
                    'sort[]': $scope.sort_status
                }
            }).then(function (res) {
                console.log(res);
                $scope.wait_list_arr = res.data.data.goods_list_admin.details;
                /*--------------------分页------------------------*/
                $scope.wait_history_list = [];
                $scope.wait_history_all_page = Math.ceil(res.data.data.goods_list_admin.total / 12);//获取总页数
                let all_num = $scope.wait_history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.wait_history_list.push(i + 1)
                }

                $scope.page = 1;
            }, function (err) {
                console.log(err)
            })
        };

        /*--------------------等待下架 结束-------------------------*/

        //物流模板开始
        $http({
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            //transformRequest: function (data) {
            //  return $.param(data)
            //},
            method: 'POST',
            url: 'http://test.cdlhzz.cn:888/mall/logistics-templates-supplier'
        }).then(function successCallback(response) {
            console.log(response);
            $scope.contentMore = response.data.data.logistics_templates_supplier;
            console.log($scope.contentMore);

        });

        //删除获取ID
        $scope.getId = function (item) {
            console.log(item);
            $scope.id = item;
            //删除物流模板
            $scope.deleteTemplate = function () {
                console.log($scope.id);
                $http({
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    transformRequest: function (data) {
                        return $.param(data)
                    },
                    method: 'POST',
                    url: 'http://test.cdlhzz.cn:888/mall/logistics-template-status-toggle',
                    data: {
                        id: +$scope.id
                    }
                }).then(function successCallback(response) {
                    $http({
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        //transformRequest: function (data) {
                        //  return $.param(data)
                        //},
                        method: 'POST',
                        url: 'http://test.cdlhzz.cn:888/mall/logistics-templates-supplier'
                    }).then(function successCallback(response) {
                        $scope.contentMore = response.data.data.logistics_templates_supplier;
                        console.log($scope.contentMore);
                    });
                    console.log(response);
                });
            }
        };

        //查看物流模板详情
        $scope.getDetails = function (item) {
            $scope.id = item.id;
            $scope.name = item.name;
            console.log($scope.id);
            $state.go('template_details', {'id': $scope.id, 'name': $scope.name})
        }
    })

    .directive('stringToNumber2', function () {
        return {
            require: 'ngModel',
            link: function (scope, element, attrs, ngModel) {
                ngModel.$parsers.push(function (value) {
                    return '' + value;
                });
                ngModel.$formatters.push(function (value) {
                    return parseInt(value);
                });
            }
        };
    });
