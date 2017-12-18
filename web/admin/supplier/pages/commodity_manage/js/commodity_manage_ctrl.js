;
let commodity_manage = angular.module("commodity_manage", [])
    .controller("commodity_manage_ctrl", function ($rootScope,$scope, $http, $state, $stateParams,_ajax) {
	    /*初始化已上架Menu状态 结束*/
	    $scope.down_list_arr = [];
	    $scope.up_list_arr = [];
	    /*页面Menu切换 开始*/
	    $scope.on_flag = true;
	    $scope.down_flag = false;
	    $scope.wait_flag = false;
	    $scope.logistics_flag = false;
	    $scope.myng = $scope;
	    $rootScope.crumbs = [{
            name: '商品管理',
            icon: 'icon-shangpinguanli'
        }];
        /*初始化已上架Menu状态 开始*/
        //已上架
        $scope.show_1 = true;$scope.show_2 = true;$scope.show_3 = true;$scope.show_4 = true;
        $scope.show_5 = true;$scope.show_6 = false;$scope.show_7 = false;$scope.show_8 = false;$scope.show_9 = true;
        $scope.show_10 = true;$scope.show_11 = true;$scope.show_12 = true;$scope.show_13 = true;$scope.show_14 = true;
        $scope.show_15 = true;
        //已下架
        $scope.down_1 = true;$scope.down_2 = true;$scope.down_3 = true;$scope.down_4 = true;$scope.down_5 = true;
        $scope.down_6 = false;$scope.down_7 = false;$scope.down_8 = false;$scope.down_9 = true;$scope.down_10 = false;
        $scope.down_11 = true;$scope.down_12 = true;$scope.down_13 = true;$scope.down_14 = true;$scope.down_15 = true;
        $scope.down_16 = true;$scope.down_17 = true;
	      //等待上架
        $scope.wait_1 = true;$scope.wait_2 = true;$scope.wait_3 = true;$scope.wait_4 = true;$scope.wait_5 = true;
        $scope.wait_6 = false;$scope.wait_7 = false;$scope.wait_8 = false;$scope.wait_9 = true;$scope.wait_10 = false;
        $scope.wait_11 = true;$scope.wait_12 = true;$scope.wait_13 = true;$scope.wait_14 = true;$scope.wait_15 = true;
        //menu
        $scope.show_all = function (m) {
          m === true ? $scope[m] = false : $scope[m] = true;
        };
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
        $scope.params = {
          status: 2,                       //状态
          'sort[]': 'online_time:3',      //默认排序
          keyword: '',                    // 关键字查询
        };
        let tablePages = function () {
            $scope.params.page = $scope.wjConfig.currentPage;//点击页数，传对应的参数
            _ajax.get('/mall/goods-list-admin', $scope.params,function (res) {
              console.log(res)
              if ($scope.on_flag == true) {
                 $scope.up_list_arr = res.data.goods_list_admin.details;
                 } else if ($scope.down_flag == true) {
                  $scope.down_list_arr = res.data.goods_list_admin.details;
                }else{
	                $scope.wait_list_arr = res.data.goods_list_admin.details;
              }
                  $scope.wjConfig.totalItems = res.data.goods_list_admin.total;
            })
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
        }
        //等待上架
        if ($stateParams.wait_flag == true) {
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = true;
            $scope.logistics_flag = false;
            /*初始化已下架的搜索*/
	          $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            $scope.down_search_value = '';//清空输入框值
            $scope.params.keyword = '';
            $scope.params['sort[]'] = 'publish_time:3';
            $scope.params.status = 1;
        }
        //物流模板
        if ($stateParams.logistics_flag == true) {
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = true;
        }
        /*---------------点击TAB  开始---------------------*/
        //已上架
        $scope.on_shelves = function () {
            $scope.on_flag = true;
            $scope.down_flag = false;
            $scope.wait_flag = false;
            $scope.logistics_flag = false;

            // 初始化已上架状态
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
            /*初始化已下架的状态*/
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
	          /*初始化已下架的状态*/
	          $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            $scope.wait_search_content='';
	          $scope.params.keyword = '';
            $scope.params.status = 1;
            $scope.params['sort[]'] = 'publish_time:3';
	          tablePages()
        };
        //物流模块
        $scope.logistics = function () {
            $scope.logistics_flag = true;
            $scope.on_flag = false;
            $scope.down_flag = false;
            $scope.wait_flag = false;
        };
        /*---------------点击TAB  结束---------------------*/

        //实时监听库存并修改
        $scope.change_left_number = function (id, left_num) {
            _ajax.post('/mall/goods-inventory-reset', {
                id: +id,
                left_number: +left_num
            },function (res) {
              console.log(res);
            })
        };

        /*--------------------已上架 开始-------------------------*/
        /*--------销量排序-------*/
        $scope.up_sort_sale_click = function () {
	        $scope.params['sort[]'] == 'sold_number:3'?$scope.params['sort[]'] = 'sold_number:4':$scope.params['sort[]'] = 'sold_number:3';
            $scope.table.roles = [];//清空全选状态
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            tablePages();
        }
        /*-----------------时间排序-----------------------*/
	      $scope.params['sort[]'] = 'online_time:3';
        $scope.up_sort_time_click = function () {
	        $scope.params['sort[]'] == 'online_time:3'?$scope.params['sort[]'] = 'online_time:4':$scope.params['sort[]'] = 'online_time:3';
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

        /*-----------------销量排序-----------------------*/
        $scope.down_sort_sale_click = function () {
	        $scope.params['sort[]'] == 'sold_number:3'?$scope.params['sort[]'] = 'sold_number:4':$scope.params['sort[]'] = 'sold_number:3';
            $scope.table.roles = [];//清空全选状态
            $scope.wjConfig.currentPage = 1; //页数跳转到第一页
            tablePages();
        }

        /*-----------------时间排序-----------------------*/
        $scope.down_sort_time_click = function () {
	        $scope.params['sort[]'] == 'offline_time:3'?$scope.params['sort[]'] = 'offline_time:4':$scope.params['sort[]'] = 'offline_time:3';
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

        //实时监听库存并修改
        $scope.change_right_number = function (id, left_num) {
          _ajax.post('/mall/goods-inventory-reset',{
	          id: +id,
	          left_number: +left_num
          },function (res) {
	          console.log(res);
          })
        };
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
          $scope.params.keyword = $scope.wait_search_content;
          $scope.params['sort[]'] = 'publish_time:3';
          $scope.wjConfig.currentPage = 1; //页数跳转到第一页
          tablePages()
        };
        $scope.wait_sort_sale_click = function () {
          $scope.params['sort[]'] == 'sold_number:3'?$scope.params['sort[]'] = 'sold_number:4':$scope.params['sort[]'] = 'sold_number:3';
          $scope.wjConfig.currentPage = 1; //页数跳转到第一页
          tablePages();
        }
        $scope.wait_time_sort = function () {
	        $scope.params['sort[]'] == 'publish_time:3'?$scope.params['sort[]'] = 'publish_time:4':$scope.params['sort[]'] = 'publish_time:3';
	        $scope.wjConfig.currentPage = 1; //页数跳转到第一页
	        tablePages()
        };
            /*--------------------等待下架 结束-------------------------*/

           /*-----------------------------物流模板-----------------------------------*/
             _ajax.post('/mall/logistics-templates-supplier',{},function (response) {
                 console.log(response);
                 $scope.contentMore = response.data.logistics_templates_supplier;
             });

            //删除获取ID
            $scope.getId = function (item) {
                $scope.id = item;
                //删除物流模板
                $scope.deleteTemplate = function () {
                  _ajax.post('/mall/logistics-template-status-toggle',{
	                  id: +$scope.id
                  },function (res) {
	                  console.log(res);
	                  _ajax.post('/mall/logistics-templates-supplier',{},function (res) {
		                  $scope.contentMore = res.data.logistics_templates_supplier;
		                  console.log(res);
	                  })
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

    });
