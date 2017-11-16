;
let commodity_manage = angular.module("commodity_manage", [])
    .controller("commodity_manage_ctrl", function ($scope, $http, $state, $stateParams,_ajax) {
        $scope.myng = $scope;
        /*POST请求头*/
        const config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        /*初始化已上架Menu状态 开始*/
        //已上架
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
        //已下架
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
        /*初始化已上架Menu状态 结束*/
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
                $scope.table.roles = [];//清空全选状态
                tablePages();
            }
        }
        let tablePages = function () {
            $scope.params.page = $scope.wjConfig.currentPage;//点击页数，传对应的参数
            _ajax.get('/mall/goods-list-admin', $scope.params,function (res) {
                console.log(res)
                if ($scope.on_flag == true) {
                   $scope.up_list_arr = res.data.goods_list_admin.details;
                   } else if ($scope.down_flag == true) {
                    $scope.down_list_arr = res.data.goods_list_admin.details;
                     }
                    $scope.wjConfig.totalItems = res.data.goods_list_admin.total;
            })
        };
        $scope.params = {
            status: 2,                       //状态
            'sort[]': 'online_time:3',      //默认排序
            keyword: '',                    // 关键字查询
        };

        /*--------------全选---------------------*/
        //全选ID数组
        $scope.table = {
            roles: [],
        };
        $scope.checkAll = function () {
            !$scope.table.roles.length ? $scope.table.roles = $scope.up_list_arr.map(function (item) {
                return item.id;
            }) : $scope.table.roles.length = 0;
        };
        /*--------------页面返回，TAB判断--------------------*/
        //已上架
        if ($stateParams.on_flag == true) {
            $scope.on_flag = true;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;
            // 初始化已上架搜索
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            $scope.up_search_value = '';//搜索输入框的值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'online_time:3';
            $scope.params.status = 2;
            $scope.up_sort_sale_img = 'lib/images/arrow_default.png';//销量默认图片
            $scope.up_sort_time_img = 'lib/images/arrow_down.png';   //时间默认图片
            $scope.table.roles = [];//清空全选状态
            tablePages();
        }
        //已下架

        if ($stateParams.down_flag == true) {
            $scope.on_flag = false;
            $scope.down_flag = true;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;
            /*初始化已下架的搜索*/
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            $scope.down_search_value = '';//清空输入框值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'offline_time:3';
            $scope.params.status = 0;
            tablePages();
        }
        //等待上架
        if ($stateParams.wait_flag == true) {
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = true;
            $scope.logistics_flag = false;
            /*初始化已下架的搜索*/
            // $scope.ConfigWait.currentPage = 1; //页数跳转到第一页
            $scope.down_search_value = '';//清空输入框值
            // $scope.params.keyword = '';
            $scope.params['sort[]'] = 'publish_time:3';
            $scope.params.status = 1;
            tablePages()
        }
        //物流模板
        $scope.logistics_flag = $stateParams.logistics_flag;
        console.log($scope.logistics_flag);
        if ($stateParams.logistics_flag == true) {
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = true;
            /*初始化已下架的搜索*/
            // $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            // $scope.down_search_value = '';//清空输入框值
            // // $scope.params.keyword = '';
            // $scope.params['sort[]'] = 'publish_time:3';
            // $scope.params.status = 1;
            // tablePages()
        }
        /*---------------点击TAB  开始---------------------*/
        //已上架
        $scope.on_shelves = function () {
            $scope.on_flag = true;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;

            // 初始化已上架搜索
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            $scope.up_search_value = '';//搜索输入框的值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'online_time:3';
            $scope.params.status = 2;
            $scope.up_sort_sale_img = 'lib/images/arrow_default.png';//销量默认图片
            $scope.up_sort_time_img = 'lib/images/arrow_down.png';   //时间默认图片
            $scope.table.roles = [];//清空全选状态
            tablePages();
        };
        //已下架
        $scope.down_shelves = function () {
            $scope.down_flag = true;
            $scope.on_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;
            /*初始化已下架的搜索*/
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            $scope.down_search_value = '';//清空输入框值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'offline_time:3';
            $scope.params.status = 0;
            tablePages();
        };
        //等待下架

        $scope.wait_shelves = function () {
            $scope.wait_flag = true;
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.logistics_flag = false;
            $scope.params.status = 1;
            $scope.params['sort[]'] = 'publish_time:3'
            tablePagesWait()
        };
        //物流模块
        $scope.logistics = function () {
            $scope.logistics_flag = true;
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = false;
        };
        /*---------------点击TAB  结束---------------------*/

        /*已上架和已下架表格Menu切换 开始*/
        $scope.show_all = function (m) {
            m === true ? $scope[m] = false : $scope[m] = true;
        };
        /*已上架和已下架表格Menu切换 结束*/

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
            _ajax.post('/mall/goods-inventory-reset', {
                id: +id,
                left_number: +left_num
            },function (res) {
              console.log(res);
            })
        };
        /*-------------------公共功能 结束---------------------------*/

        /*--------------------已上架 开始-------------------------*/

        /*--------销量排序-------*/
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
            $scope.table.roles = [];//清空全选状态
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
            $scope.table.roles = [];//清空全选状态
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
            _ajax.post('/mall/goods-status-toggle', {
                id: $scope.offline_id
            },function (res) {
                $scope.wjConfig.currentPage = 1; //页数跳转到第一页
                tablePages();
            })
        };

        /*------------批量下架商品------------------*/
        //点击下架按钮
        $scope.all_off_shelf = function () {
            if ($scope.table.roles.length != 0) {
                $scope.prompt = '#down_shelves_modal'
            } else {
                $scope.prompt = '#please_check';
            }
        };
        //下架确认按钮
        $scope.all_off_shelf_confirm = function () {
            _ajax.post('/mall/goods-disable-batch', {
                ids: $scope.table.roles.join(',')
            },function (res) {
                $scope.table.roles = [];//清空全选状态
                $scope.wjConfig.currentPage = 1; //页数跳转到第一页
                tablePages();
            })
        };

        /*----------------搜索---------------*/
        $scope.search_btn = function () {
            $scope.up_sort_time_img = 'lib/images/arrow_down.png';
            $scope.up_sort_sale_img = 'lib/images/arrow_default.png';
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            $scope.params.keyword = $scope.up_search_value;
            $scope.params['sort[]'] = 'online_time:3';
            $scope.table.roles = [];//清空全选状态
            tablePages();
        };


        /*-----------添加分类--------------*/
        $scope.item_check = [];
        //获取一级
        _ajax.get('/mall/categories',{},function (res) {
            $scope.details = res.data.categories;
            $scope.oneColor = $scope.details[0];
        });
        //获取二级
        _ajax.get('/mall/categories',{pid:1},function (res) {
            $scope.second = res.data.categories;
            $scope.twoColor = $scope.second[0];
        });
        //获取三级
        _ajax.get('/mall/categories',{pid:2},function (res) {
            $scope.three = res.data.categories;
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
        })
        //点击一级 获取相对应的二级
        $scope.getMore = function (n) {
            $scope.oneColor = n;
            _ajax.get('/mall/categories',{pid:+n.id},function (res) {
                $scope.second = res.data.categories;
                $scope.twoColor = $scope.second[0];
                _ajax.get('/mall/categories',{pid:+ $scope.second[0].id},function (res) {
                    $scope.three = res.data.categories;
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
                })
            })
            $scope.threeColor='';
        };
        //点击二级 获取相对应的三级
        $scope.getMoreThree = function (n) {
            $scope.id = n;
            $scope.twoColor = n;
            _ajax.get('/mall/categories',{pid:+n.id},function (res) {
                $scope.three = res.data.categories;
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
            })
            $scope.threeColor='';
        };
        //添加拥有系列的三级
        $scope.check_item = function (item) {
            $scope.item_check = [];
            $scope.threeColor = item;
            $scope.item_check.push(item);
            console.log(item);
        };

        /*------------分类确定----------------*/
        $scope.category_id = '';
        $scope.add_confirm_red = false;//提示文字默认为false
        $scope.shop_style_go = function () {
            $scope.category_id = '';
            if ($scope.item_check.length != 0 && $scope.threeColor!='') {
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
            $scope.add_confirm_red = false;//提示文字默认为false
            $scope.item_check = [];
            //获取一级
            _ajax.get('/mall/categories',{},function (res) {
                $scope.details = res.data.categories;
                $scope.oneColor = $scope.details[0];
            })
            //获取二级
            _ajax.get('/mall/categories',{pid:1},function (res) {
                $scope.second = res.data.categories;
                $scope.twoColor = $scope.second[0];
            })
            //获取三级
            _ajax.get('/mall/categories',{pid:2},function (res) {
                $scope.three = res.data.categories;
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
            })
        };
        /*--------------------已上架 结束-------------------------*/

        /*--------------------已下架 开始-------------------------*/

        /*-----------------时间排序-----------------------*/
        $scope.down_sort_time_img = 'lib/images/arrow_down.png';
        $scope.down_sort_time_click = function () {
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
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            tablePages();
        }

        //单个上架
        $scope.sole_on_shelf = function (id) {
            $scope.on_shelf_id = id;
        };
        //上架确认按钮
        $scope.all_on_shelf_confirm = function () {
            _ajax.post('/mall/goods-status-toggle',{
                id: +$scope.on_shelf_id
            },function (res) {
                $scope.down_search_value = '';//清空输入框值
                $scope.params.keyword = '';
                $scope.params['sort[]'] = 'offline_time:3';
                $scope.params.status = 0;
                $scope.down_sort_time_img = 'lib/images/arrow_down.png';
                $scope.wjConfig.currentPage = 1; //页数跳转到第一页
                tablePages();
            })
        };

        /*----------------搜索---------------*/
        $scope.down_search_btn = function () {
            $scope.params.keyword = $scope.down_search_value;
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
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
            _ajax.post('/mall/goods-delete-batch',{
                ids: $scope.batch_del.join(',')
            },function (res) {
                $scope.wjConfig.currentPage = 1; //页数跳转到第一页
                tablePages();
            })
        };
        //下架原因
        $scope.reason_click = function (reason) {
            $scope.down_reason = reason;
        };
        /*--------------------已下架 结束-------------------------*/

        /*--------------------等待上架 开始-------------------------*/
        /*分页配置*/
        $scope.ConfigWait = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePagesWait();
            }
        };
        let tablePagesWait = function () {
            $scope.paramsWait.page = $scope.ConfigWait.currentPage;//点击页数，传对应的参数
            _ajax.get('/mall/goods-list-admin', $scope.paramsWait,function (res) {
                console.log(res);
                $scope.wait_list_arr = res.data.goods_list_admin.details;
                $scope.ConfigWait.totalItems = res.data.goods_list_admin.total
            });

        };
        $scope.paramsWait = {
            status: 1,                       //状态
            'sort[]': 'online_time:3',      //默认排序
            keyword: '',                    // 关键字查询

        };
        // $scope.getWaitChange = function () {
        //     $scope.ConfigWait.currentPage = 1 ;
        //     tablePagesWait()
        // };
        //实时监听库存并修改
        $scope.change_right_number = function (id, left_num) {
            $http.post(baseUrl+'/mall/goods-inventory-reset', {
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


            //查看获取审核备注
             $scope.waiteModel = '';
            $scope.getRest = function (item) {
                $scope.reset = item;
                if ($scope.reset == '') {
                    $scope.waiteModel = ''
                }else {
                    $scope.waiteModel = '#wait_shelves_remarks_modal'
                }

            };

            /*----------------搜索---------------*/

            $scope.wait_search_btn = function () {
                $scope.paramsWait.keyword = $scope.wait_search_content;
                tablePagesWait()
            };
            //监听搜索框的值为空时，返回最初的值
            $scope.$watch("off_search_content", function (newVal, oldVal) {
                if (newVal == "") {
                    $scope.paramsWait.status = 1;
                    tablePagesWait()
                }
            });
        $scope.on_time_flag = true;
        $scope.down_time_flag = false;
            /*=======降序=====*/
            $scope.wait_time_sort = function () {
                // $scope.sort_status = 'publish_time:3';
                $scope.on_time_flag = false;
                $scope.down_time_flag = true;
                // $scope.page = 1;
                $scope.params.status = 1;
                $scope.params['sort[]'] = 'publish_time:3'
                tablePagesWait()
            };
            /*============升序==================*/
            $scope.on_time_flag = true;
            $scope.down_time_flag = false;
            $scope.wait_time_sort = function (status) {
                // $scope.sort_status = 'publish_time:4';
                $scope.on_time_flag = true;
                $scope.down_time_flag = false;
                // $scope.page = 1;
                $scope.params.status = 1;
                $scope.params['sort[]'] = 'publish_time:4'
                tablePagesWait()
            };
        // })

            /*--------------------等待下架 结束-------------------------*/

            //=====================物流模板开始==========================



             _ajax.post('/mall/logistics-templates-supplier',{},function (response) {
                 console.log(response);
                 $scope.contentMore = response.data.logistics_templates_supplier;
             });
            // $http({
        //     headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        //     //transformRequest: function (data) {
        //     //  return $.param(data)
        //     //},
        //     method: 'POST',
        //     url: baseUrl+'/mall/logistics-templates-supplier'
        // }).then(function successCallback(response) {
        //     console.log(response);
        //     $scope.contentMore = response.data.data.logistics_templates_supplier;
        //     console.log($scope.contentMore);
        //
        // });

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
                        url: baseUrl+'/mall/logistics-template-status-toggle',
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
                            url: baseUrl+'/mall/logistics-templates-supplier'
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
                $state.go('template_details', {'id': $scope.id, 'name': $scope.name,'wait_flag':true})
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
