angular.module('all_controller', [])
    .controller("intelligent_index_ctrl", function ($scope, $http, _ajax) {//主页控制器
        sessionStorage.clear();
        $scope.baseUrl = '';
        // $scope.baseUrl = 'http://ac.cdlhzz.cn/'
        //主页推荐
        _ajax.get('/owner/homepage', {}, function (res) {
            console.log(res);
            $scope.recommend_list = res.data
        });
    })
    .controller('nodata_ctrl', function (_ajax, $q, $scope, $http, $state, $rootScope, $timeout, $stateParams, $anchorScroll, $location, $window) {
        console.log(JSON.parse(sessionStorage.getItem('materials')));
        $scope.ctrlScope = $scope;
        //post请求配置
        let config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        //商品列表部分
        /*分页配置*/
        $scope.Config = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages();
            }
        };
        let tablePages = function () {
            console.log($scope.params);
            // $scope.params.page = $scope.Config.currentPage;//点击页数，传对应的参数
            _ajax.get('/mall/category-goods', $scope.params, function (res) {
                console.log(res);
                $scope.cur_replace_material = [];
                for (let [key, value] of res.data.category_goods.entries()) {
                    $scope.cur_replace_material.push({
                        id: value.id,
                        cover_image: value.cover_image,
                        cost: +value.platform_price,
                        favourable_comment_rate: value.favourable_comment_rate,
                        sold_number: value.sold_number,
                        platform_price: value.platform_price,
                        profit_rate: value.profit_rate,
                        purchase_price_decoration_company: value.purchase_price_decoration_company,
                        quantity: 1,
                        series_id: !!$scope.cur_goods_detail ? $scope.cur_goods_detail.series_id : '',
                        style_id: !!$scope.cur_goods_detail ? $scope.cur_goods_detail.style_id : '',
                        subtitle: value.subtitle,
                        supplier_price: value.supplier_price,
                        title: value.title
                    })
                }
                $scope.Config.totalItems = $scope.cur_replace_material.length
                _ajax.get('/mall/category-brands-styles-series', {
                    category_id: $scope.params.category_id,
                }, function (res) {
                    console.log(res)
                    $scope.all_goods_series = res.data.category_brands_styles_series.series
                    $scope.all_goods_style = res.data.category_brands_styles_series.styles
                    $scope.all_goods_brands = res.data.category_brands_styles_series.brands
                })
            })
        };
        $scope.params = {
            category_id: '',
            platform_price_min: '',
            platform_price_max: '',
            'sort[]': '',
            brand_id: '',
            style_id: '',
            series_id: ''
        };

        window.addEventListener("hashchange", function () {
            // 注册返回按键事件
            $('.modal-backdrop').remove()
            $('body').removeClass('modal-open')
            console.log($rootScope.curState_name)
            if(sessionStorage.getItem('all_status')==null) {
                if ($rootScope.curState_name == 'nodata.house_list') {
                    $scope.have_header = true
                    $scope.cur_header = '智能报价'
                    $scope.is_city = true
                    $scope.is_edit = false
                } else if ($rootScope.curState_name == 'nodata.main_material') {
                    $scope.have_header = true
                    $scope.cur_header = '主材料'
                    $scope.is_city = false
                    $scope.is_edit = false
                } else if ($rootScope.curState_name == 'nodata.basics_decoration') {
                    $scope.have_header = true
                    $scope.cur_header = '基础装修'
                    $scope.is_city = false
                    $scope.is_edit = false
                } else if ($rootScope.curState_name == 'nodata.other_material') {
                    $scope.have_header = true
                    $scope.cur_header = $scope.inner_header
                    $scope.is_city = false
                    $scope.is_edit = true
                } else if ($rootScope.curState_name == 'nodata.all_goods') {
                    $scope.have_header = true
                    $scope.cur_header = $scope.cur_three_level
                    $scope.is_city = false
                    $scope.is_edit = false
                } else if ($rootScope.curState_name == 'nodata.second_level') {
                    $scope.have_header = true
                    $scope.cur_header = $scope.inner_first_level
                    $scope.is_city = false
                    $scope.is_edit = false
                }
            }
        });
      
        $scope.special_request = ''//特殊要求
        $scope.toponymy = ''//小区名称
        $scope.message = ''//小区地址
        $scope.area = 60//房屋面积
        $scope.house_bedroom = 1//室
        $scope.house_hall = 1//厅
        $scope.house_toilet = 1 //卫
        $scope.house_kitchen = 1//厨
        $scope.highCrtl = 2.8//层高
        $scope.window = 0//飘窗
        $scope.choose_stairs = 0 //有无楼梯，默认无楼梯
        $scope.nowStairs = {'id': 0, 'attribute': ''}//楼梯结构,，默认无楼梯结构
        $scope.have_header = true//有无头部
        $scope.all_goods = []//生成材料
        $scope.all_workers = []//工种以及费用
        $scope.all_workers_cost = 0//工人总费用
        $scope.show_material = false//是否成功生成材料
        $scope.cur_header = '智能报价'
        $scope.cur_second_level = ''//二级名称保存
        $scope.cur_three_level = ''//三级名称保存
        $scope.cur_content = 'modal'//控制编辑状态无法点击弹出模态框
        $scope.is_city = true//是否显示城市定位
        $scope.is_edit = false//是否显示编辑按钮
        $scope.tab_title = 0//商品详情页tab切换
        $scope.cur_status = 0//查看0 更换1 添加2状态
        $scope.cur_title = ''//商品更换或者添加
        $scope.replaced_goods = []//被替换的商品
        $scope.goods_replaced = []//替换的商品
        $scope.all_delete_goods = []//其他材料删除的材料
        $scope.all_add_goods = []//其他材料添加的材料
        $scope.cur_project = 0//0为基础装修 1为主要材料 2是其他材料 3三级分类页
        $scope.cur_operate = '编辑'//其他材料编辑两种状态 编辑/完成
        $scope.is_delete_btn = false //切换编辑状态
        $scope.platform_status = 0//价格状态
        $scope.rate_status = 0//好评率状态
        if (sessionStorage.getItem('materials')!= null) {
            $scope.all_goods = JSON.parse(sessionStorage.getItem('materials'))
            console.log($scope.all_goods)
            for (let [key, value] of $scope.all_goods.entries()) {
                value['cost'] = value['totalMoney']
                value['count'] = value.goods.length
            }
            if ($rootScope.curState_name == 'nodata.basics_decoration') {
                $scope.cur_item = $scope.all_goods[0]
                $scope.cur_project = 0
                $scope.cur_header = $scope.inner_header = $scope.cur_item.title
            } else if ($rootScope.curState_name == 'nodata.main_material') {
                $scope.cur_item = $scope.all_goods[1]
                $scope.cur_project = 1
                $scope.cur_header = $scope.inner_header = $scope.cur_item.title
            } else if ($rootScope.curState_name == 'nodata.house_list') {
                $scope.all_goods = []
                sessionStorage.removeItem('materials')
            } else if ($rootScope.curState_name == 'nodata.other_material') {
                $scope.cur_item = $scope.all_goods[$stateParams.index]
                $scope.cur_project = 2
                $scope.cur_header = $scope.inner_header = $scope.cur_item.title
            }
            console.log($scope.cur_item)
        }
        console.log($scope.all_goods)
        //请求风格系列以及楼梯数据
        _ajax.get('/owner/series-and-style', {}, function (res) {
            console.log(res)
            $scope.stairs_details = res.data.show.stairs_details;//楼梯数据
            $scope.series = res.data.show.series;//系列数据
            $scope.style = res.data.show.style;//风格数据
            $scope.cur_series = $scope.series[0]//默认选择第一个系列
            $scope.cur_style = $scope.style[0]//默认选择第一个风格
            $timeout.cancel($scope.time)
            $scope.time = $timeout(function () {
                var mySwiper = new Swiper('.swiper-container', {
                    direction: 'horizontal',
                    loop: true,
                    autoplay: 1000,
                    autoplayDisableOnInteraction: false,
                    observer:true,
                    observeParents:true,
                    effect: 'slide',

                    // 如果需要分页器
                    pagination: '.swiper-pagination',
                })
            }, 0)
        })
        if ($rootScope.fromState_name == 'modelRoom' && $rootScope.curState_name == 'nodata.cell_search') {
            $scope.have_header = false
        }
        //切换楼梯结构
        $scope.toggleStairs = function (item) {
            $scope.nowStairs = item
        }
        //切换系列
        $scope.toggleSeries = function (item) {
            $scope.cur_series = item
            console.log(item)
        }
        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.cur_style = item
        }
        //室厅卫厨操作
        $scope.operate = function (type, is_add, limit,other) {
            if (!!is_add) {
                if(other!=undefined){
                    if ($scope[type][other] == limit) {
                        $scope[type][other] = limit
                    } else {
                        $scope[type][other]++
                    }
                }else{
                    if ($scope[type] == limit) {
                        $scope[type] = limit
                    } else {
                        $scope.recalculate()
                        $scope[type]++
                    }
                }
            } else {
                if(other!=undefined){
                    if ($scope[type][other] == limit) {
                        $scope[type][other] = limit
                    } else {
                        $scope[type][other]--
                    }
                }else{
                    if ($scope[type] == limit) {
                        $scope[type] = limit
                    } else {
                        $scope.recalculate()
                        $scope[type]--
                    }
                }
            }
        }
        //一级、二级分类数据请求
        _ajax.post('/owner/classify', {}, function (res) {
            console.log(res)
            $scope.stair = res.data.pid.stair//一级
            $scope.level = res.data.pid.level//二级
        })

        /*无资料操作*/
        //修改了基础表单数据
        $scope.recalculate = function (item,num) {
            console.log(item)
            console.log(num)
            if(item!=undefined){
                if($scope[item]!=num){
                    $scope[item] = num
                    $scope.show_material = false
                }
                if(item == 'cur_style'){
                    $timeout.cancel($scope.time)
                        $scope.time = $timeout(function () {
                            var mySwiper = new Swiper('.swiper-container', {
                                direction: 'horizontal',
                                loop: true,
                                autoplay: 1000,
                                autoplayDisableOnInteraction: false,
                                observer:true,
                                observeParents:true,
                                effect: 'slide',

                                // 如果需要分页器
                                pagination: '.swiper-pagination',
                            })
                        }, 0)
                }
            }else{
                $scope.show_material = false
            }
        }
        // $scope.$watch('toponymy', function (newVal, oldVal) {
        //     if(sessionStorage.getItem('basic_nodata') == null){
        //         $scope.show_material = false
        //     }
        // })
        // $scope.$watch('message', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('area', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('house_bedroom', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('house_hall', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('house_toilet', function (newVal, oldVal) {
        //     if(sessionStorage.getItem('basic_nodata') == null){
        //         $scope.show_material = false
        //     }
        // })
        // $scope.$watch('house_kitchen', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('highCrtl', function (newVal, oldVal) {
        //     if(sessionStorage.getItem('basic_nodata') == null){
        //         $scope.show_material = false
        //     }
        // })
        // $scope.$watch('window', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('choose_stairs', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('nowStairs', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('cur_series', function (newVal, oldVal) {
        //     $scope.show_material = false
        // })
        // $scope.$watch('cur_style', function (newVal, oldVal) {
        //     $scope.show_material = false
        //     $timeout.cancel($scope.time)
        //     $scope.time = $timeout(function () {
        //         var mySwiper = new Swiper('.swiper-container', {
        //             direction: 'horizontal',
        //             loop: true,
        //             autoplay: 1000,
        //             autoplayDisableOnInteraction: false,
        //             observer:true,
        //             observeParents:true,
        //             effect: 'slide',
        //
        //             // 如果需要分页器
        //             pagination: '.swiper-pagination',
        //         })
        //     }, 0)
        // })
        //监听页面是否加载完成操作DOM
        $scope.$on('ngRepeatFinished', function () {
            let $grid = $('.grid')
            console.log($grid)
            let cur_height = [0, 0]
            $grid.each(function () {
                console.log(cur_height)
                let min = parseFloat(cur_height[0]) > parseFloat(cur_height[1]) ? cur_height[1] : cur_height[0]
                let minIndex = cur_height[0] > cur_height[1] ? 1 : 0
                $(this).css({
                    'top': min,
                    'left': minIndex * ($(window).width() * 0.471),
                })
                cur_height[minIndex] += $(this).outerHeight() + 20
                $('.basis_decoration').outerHeight(parseFloat(cur_height[0]) > parseFloat(cur_height[1]) ? cur_height[0] : cur_height[1])
            })
        })
        //跳转内页
        $scope.go_inner = function (item,index) {
            if (item.title == '辅材') {
                $state.go('nodata.basics_decoration')
                $scope.cur_header = '基础装修'
                $scope.inner_header = '基础装修'
                $scope.cur_project = 0
                $scope.is_city = false
                $scope.is_edit = false
                if (sessionStorage.getItem('huxing')!=null) {
                    console.log(JSON.parse(sessionStorage.getItem('huxing')))
                    $scope.area = JSON.parse(sessionStorage.getItem('huxing')).area
                    $scope.cur_series = JSON.parse(sessionStorage.getItem('huxing')).series
                    $scope.cur_style = JSON.parse(sessionStorage.getItem('huxing')).style
                }
                if (sessionStorage.getItem('backman')!=null) {
                    console.log(JSON.parse(sessionStorage.getItem('backman')))
                    for (let [key, value] of JSON.parse(sessionStorage.getItem('backman')).entries()) {
                        if (value.backman_option == '12墙拆除') {
                            $scope.twelve_dismantle = value.backman_value//12墙拆除
                        } else if (value.backman_option == '24墙拆除') {
                            $scope.twenty_four_dismantle = value.backman_value//24墙拆除
                        } else if (value.backman_option == '补烂') {
                            $scope.repair = value.backman_value//补烂
                        } else if (value.backman_option == '12墙新建(含双面抹灰)') {
                            $scope.twelve_new_construction = value.backman_value//12墙新建
                        } else if (value.backman_option == '24墙新建(含双面抹灰)') {
                            $scope.twenty_four_new_construction = value.backman_value//24墙新建
                        } else if (value.backman_option == '有无建渣点') {
                            $scope.building_scrap = !!value.backman_value//有无建渣点
                        }
                    }
                }
            } else if (item.title == '主要材料') {
                $state.go('nodata.main_material')
                $scope.replaced_goods = []//被替换的商品
                $scope.goods_replaced = []//替换的商品
                $scope.cur_header = '主材料'
                $scope.inner_header = '主材料'
                $scope.cur_project = 1
                $scope.is_city = false
                $scope.is_edit = false
            } else {
                $state.go('nodata.other_material')
                $scope.cur_header = item.title
                $scope.inner_header = item.title
                $scope.replaced_goods = []//被替换的商品
                $scope.goods_replaced = []//替换的商品
                $scope.all_delete_goods = []//其他材料删除的材料
                $scope.all_add_goods = []
                $scope.cur_project = 2
                $scope.is_city = false
                $scope.is_edit = true

            }
            $scope.have_header = true
            $scope.cur_item = item
            sessionStorage.setItem('cur_index',index)
            sessionStorage.setItem('all_status',JSON.stringify({
                have_header:$scope.have_header,
                cur_header:$scope.cur_header,
                inner_header:$scope.inner_header,
                cur_project:$scope.cur_project,
                is_city:$scope.is_city,
                is_edit:$scope.is_edit
            }))
            $scope.cur_all_goods = angular.copy($scope.all_goods)
        }
            if ($rootScope.curState_name == 'nodata.basics_decoration') {
                $scope.is_city = false
                $scope.is_edit = false
            } else if ($rootScope.curState_name == 'nodata.main_material') {
                $scope.is_city = false
                $scope.is_edit = false
            } else if ($rootScope.curState_name == 'nodata.other_material') {
                $scope.is_city = false
                $scope.is_edit = true
            }
        //模态框详情
        $scope.get_basic_details = function (item, three_level_name, three_level_id) {
            console.log(item)
            $scope.cur_goods_detail = item
            for (let [key, value] of $scope.series.entries()) {
                if ($scope.cur_goods_detail.series_id == value.id) {
                    $scope.cur_goods_detail['series_name'] = value.series
                }
            }
            for (let [key, value] of $scope.style.entries()) {
                if ($scope.cur_goods_detail.style_id == value.id) {
                    $scope.cur_goods_detail['style_name'] = value.style
                }
            }
            sessionStorage.setItem('cur_goods_detail',JSON.stringify($scope.cur_goods_detail))
            console.log($scope.cur_goods_detail)
            $scope.cur_status = 0
            $scope.cur_second_level = $scope.cur_header
            $scope.cur_three_level = three_level_name
            $scope.cur_three_id = sessionStorage.getItem('materials')==null?three_level_id:item.category_id
        }
        $scope.$watch('cur_params',function (newVal,oldVal) {
            $timeout(function () {
                var mySwiper = new Swiper('.swiper-container', {
                    direction: 'horizontal',
                    loop: true,
                    autoplay: 1000,
                    autoplayDisableOnInteraction: false,
                    observer:true,
                    observeParents:true,
                    effect: 'slide',

                    // 如果需要分页器
                    pagination: '.swiper-pagination',
                })
            }, 100)
        },true)
        //查看详情
        $scope.go_details = function (item) {
            console.log($scope.cur_title)
            console.log($scope.cur_status)
            console.log(item)
            if ($scope.cur_status == 0) {
                $scope.check_goods = $scope.cur_goods_detail
            } else if ($scope.cur_status == 1) {
                $scope.check_goods = item
            } else {
                $scope.check_goods = item
                $scope.check_goods['path'] = $scope.cur_three_item.path
            }
            console.log($scope.check_goods)
            sessionStorage.setItem('check_goods',JSON.stringify($scope.check_goods))
            _ajax.get('/mall/goods-view', {
                id: +$scope.check_goods.id
            }, function (res) {
                console.log(res)
                if ($scope.cur_status == 1) {
                    $scope.cur_title = '更换'
                    $scope.check_goods['name'] = res.data.goods_view.brand_name
                    $scope.check_goods['goods_name'] = res.data.goods_view.title
                    $scope.check_goods['series_name'] = res.data.goods_view.series_name
                    $scope.check_goods['style_name'] = res.data.goods_view.style_name
                } else if ($scope.cur_status == 2) {
                    $scope.cur_title = '添加'
                    $scope.check_goods['name'] = res.data.goods_view.brand_name
                    $scope.check_goods['goods_name'] = res.data.goods_view.title
                    $scope.check_goods['series_name'] = res.data.goods_view.series_name
                    $scope.check_goods['style_name'] = res.data.goods_view.style_name
                }
                $scope.sale_services = res.data.goods_view.after_sale_services
                $scope.aftermarket = []
                $scope.protection = []
                for (let [key, value] of $scope.sale_services.entries()) {
                    if (value == '提供发票' || value == '上门安装') {
                        $scope.protection.push(value)
                    } else {
                        $scope.aftermarket.push(value)
                    }
                }
                $scope.description = res.data.goods_view.description
                $scope.supplier = res.data.goods_view.supplier
                $scope.cur_params = {
                    code: res.data.goods_view.sku,
                    title: res.data.goods_view.title,
                    attrs: res.data.goods_view.attrs,
                    left_number: res.data.goods_view.left_number,
                    series_name: res.data.goods_view.series_name,
                    style_name: res.data.goods_view.style_name,
                    images:res.data.goods_view.images
                }
                $('#myModal').modal('hide')
                $timeout(function () {
                    $scope.have_header = false
                    sessionStorage.setItem('all_status',JSON.stringify({
                        have_header:$scope.have_header,
                        cur_header:$scope.cur_header,
                        inner_header:$scope.inner_header,
                        cur_project:$scope.cur_project,
                        cur_status:$scope.cur_status,
                        is_city:$scope.is_city,
                        is_edit:$scope.is_edit
                    }))
                    $state.go('nodata.product_detail')
                }, 300)
            })
        }
        if($rootScope.curState_name == 'nodata.product_detail'){
            var mySwiper = new Swiper('.swiper-container', {
                direction: 'horizontal',
                loop: true,
                autoplay: 1000,
                autoplayDisableOnInteraction: false,
                observer:true,
                observeParents:true,
                effect: 'slide',

                // 如果需要分页器
                pagination: '.swiper-pagination',
            })
        }
        //监听商品数量输入
        $scope.$watch('check_goods.quantity', function (newVal, oldVal) {
            if ($scope.cur_params != undefined) {
                if (newVal === '0' || !(/(^[1-9]{1}\d{0,}$)|(^\s*$)/.test(newVal))) {
                    $scope.check_goods.quantity = 1
                } else if (newVal > $scope.cur_params.left_number) {
                    $scope.check_goods.quantity = +$scope.cur_params.left_number
                }
            }
        })
        console.log($scope.is_edit)
        //更换商品
        $scope.replace_material = function () {
            $scope.cur_status = 1
            // $scope.cur_project = 1
            // $scope.cur_replace_material = []//所有可以替换的商品
            $scope.params.category_id = $scope.cur_three_id
            $scope.cur_series_arr = []
            $scope.cur_style_arr = []
            $scope.cur_brand_arr = []
            $scope.platform_status = 0
            $scope.rate_status = 0
            $scope.params.platform_price_min = ''
            $scope.params.platform_price_max = ''
            $scope.params.brand_id = ''
            $scope.params.style_id = ''
            $scope.params.series_id = ''
            $scope.price_min = ''
            $scope.price_max = ''
            $scope.params['sort[]'] = 'sold_number:3'
            sessionStorage.setItem('params',JSON.stringify($scope.params))
            tablePages()
            $('#myModal').modal('hide')
            $timeout(function () {
                $scope.have_header = true
                $scope.is_city = false
                $scope.is_edit = false
                $scope.cur_header = $scope.cur_three_level || item.title
                sessionStorage.setItem('all_status',JSON.stringify({
                    have_header:$scope.have_header,
                    cur_header:$scope.cur_header,
                    inner_header:$scope.inner_header,
                    cur_project:$scope.cur_project,
                    cur_status:$scope.cur_status,
                    is_city:$scope.is_city,
                    is_edit:$scope.is_edit
                }))
                $state.go('nodata.all_goods')
            }, 300)
        }
        //较正式更换或者添加商品
        $scope.first_replace = function () {
            $timeout(function () {
                console.log($scope.check_goods)
                console.log($scope.cur_project)
                $scope.have_header = true
                $scope.cur_header = $scope.cur_second_level
                $scope.is_city = false
                if ($scope.cur_status == 1) {//更换
                    $scope.check_goods.cost = $scope.check_goods.platform_price * $scope.check_goods.quantity
                    $scope.check_goods.procurement = $scope.check_goods.purchase_price_decoration_company * $scope.check_goods.quantity
                    $scope.replaced_goods.push($scope.cur_goods_detail)
                    $scope.goods_replaced.push($scope.check_goods)
                    console.log($scope.cur_three_id)
                    console.log($scope.cur_goods_detail)
                    console.log($scope.all_goods)
                    for (let [key, value] of $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of value2.goods_detail.entries()) {
                                    if (value2.id == $scope.cur_three_id && value3.id == $scope.cur_goods_detail.id) {
                                        value2.goods_detail.splice(key3, 1)
                                        value1.cost += $scope.check_goods.cost - $scope.cur_goods_detail.cost
                                        value.cost += $scope.check_goods.cost - $scope.cur_goods_detail.cost
                                        value1.procurement += $scope.check_goods.procurement - $scope.cur_goods_detail.procurement
                                        value.procurement += $scope.check_goods.procurement - $scope.cur_goods_detail.procurement
                                        value2.goods_detail.push({
                                            id: $scope.check_goods.id,
                                            image: $scope.check_goods.image,
                                            cost: $scope.check_goods.platform_price * $scope.check_goods.quantity,
                                            name: $scope.check_goods.name,
                                            procurement:$scope.check_goods.purchase_price_decoration_company * $scope.check_goods.quantity,
                                            platform_price: $scope.check_goods.platform_price,
                                            profit_rate: $scope.check_goods.profit_rate,
                                            purchase_price_decoration_company: $scope.check_goods.purchase_price_decoration_company,
                                            quantity: +$scope.check_goods.quantity,
                                            series_id: $scope.check_goods.series_id,
                                            style_id: $scope.check_goods.style_id,
                                            subtitle: $scope.check_goods.subtitle,
                                            supplier_price: $scope.check_goods.supplier_price,
                                            shop_name: $scope.check_goods.shop_name,
                                            goods_name: $scope.check_goods.goods_name,
                                            series_name: $scope.check_goods.series_name,
                                            style_name: $scope.check_goods.style_name
                                        })
                                    }
                                }
                            }
                        }
                    }
                } else if ($scope.cur_status == 2) {//添加
                    console.log($scope.check_goods)
                    $scope.all_add_goods.push($scope.check_goods)
                    for (let [key, value] of $scope.all_goods.entries()) {
                        if (value.id == $scope.check_goods.path.split(',')[0]) {
                            value.cost += $scope.check_goods.platform_price * $scope.check_goods.quantity
                            value.procurement += $scope.check_goods.purchase_price_decoration_company * $scope.check_goods.quantity
                            value.count++
                            let second_item = value.second_level.findIndex(function (item) {
                                return item.id == $scope.check_goods.path.split(',')[1]
                            })
                            if (second_item == -1) {
                                value.second_level.push({
                                    id: $scope.check_goods.path.split(',')[1],
                                    three_level: [{
                                        id: $scope.cur_three_id,
                                        title: $scope.cur_three_level,
                                        goods_detail: [{
                                            id: $scope.check_goods.id,
                                            image: $scope.check_goods.image,
                                            cost: $scope.check_goods.platform_price * $scope.check_goods.quantity,
                                            name: $scope.check_goods.name,
                                            procurement:$scope.check_goods.purchase_price_decoration_company * $scope.check_goods.quantity,
                                            platform_price: $scope.check_goods.platform_price,
                                            profit_rate: $scope.check_goods.profit_rate,
                                            purchase_price_decoration_company: $scope.check_goods.purchase_price_decoration_company,
                                            quantity: +$scope.check_goods.quantity,
                                            series_id: $scope.check_goods.series_id,
                                            style_id: $scope.check_goods.style_id,
                                            subtitle: $scope.check_goods.subtitle,
                                            supplier_price: $scope.check_goods.supplier_price,
                                            shop_name: $scope.check_goods.shop_name,
                                            goods_name: $scope.check_goods.goods_name,
                                            series_name: $scope.check_goods.series_name,
                                            style_name: $scope.check_goods.style_name
                                        }]
                                    }]
                                })
                            } else {
                                for (let [key1, value1] of value.second_level.entries()) {
                                    if (value1.id == $scope.check_goods.path.split(',')[1]) {
                                        let three_item = value1.three_level.findIndex(function (item) {
                                            return item.id == $scope.check_goods.path.split(',')[2]
                                        })
                                        if (three_item == -1) {
                                            value1.three_level.push({
                                                id: $scope.cur_three_id,
                                                title: $scope.cur_three_level,
                                                goods_detail: [{
                                                    id: $scope.check_goods.id,
                                                    image: $scope.check_goods.image,
                                                    cost: $scope.check_goods.platform_price * $scope.check_goods.quantity,
                                                    procurement: $scope.check_goods.purchase_price_decoration_company * $scope.check_goods.quantity,
                                                    name: $scope.check_goods.name,
                                                    platform_price: $scope.check_goods.platform_price,
                                                    profit_rate: $scope.check_goods.profit_rate,
                                                    purchase_price_decoration_company: $scope.check_goods.purchase_price_decoration_company,
                                                    quantity: +$scope.check_goods.quantity,
                                                    series_id: $scope.check_goods.series_id,
                                                    style_id: $scope.check_goods.style_id,
                                                    subtitle: $scope.check_goods.subtitle,
                                                    supplier_price: $scope.check_goods.supplier_price,
                                                    shop_name: $scope.check_goods.shop_name,
                                                    goods_name: $scope.check_goods.goods_name,
                                                    series_name: $scope.check_goods.series_name,
                                                    style_name: $scope.check_goods.style_name
                                                }]
                                            })
                                        } else {
                                            for (let [key2, value2] of value1.three_level.entries()) {
                                                if (value2.id == $scope.cur_three_id) {
                                                    let goods_item = value2.goods_detail.findIndex(function (item) {
                                                        return item.id == $scope.check_goods.id
                                                    })
                                                    if (goods_item == -1) {
                                                        value2.goods_detail.push({
                                                            id: $scope.check_goods.id,
                                                            image: $scope.check_goods.image,
                                                            cost: $scope.check_goods.platform_price * $scope.check_goods.quantity,
                                                            procurement: $scope.check_goods.purchase_price_decoration_company * $scope.check_goods.quantity,
                                                            name: $scope.check_goods.name,
                                                            platform_price: $scope.check_goods.platform_price,
                                                            profit_rate: $scope.check_goods.profit_rate,
                                                            purchase_price_decoration_company: $scope.check_goods.purchase_price_decoration_company,
                                                            quantity: +$scope.check_goods.quantity,
                                                            series_id: $scope.check_goods.series_id,
                                                            style_id: $scope.check_goods.style_id,
                                                            subtitle: $scope.check_goods.subtitle,
                                                            supplier_price: $scope.check_goods.supplier_price,
                                                            shop_name: $scope.check_goods.shop_name,
                                                            goods_name: $scope.check_goods.goods_name,
                                                            series_name: $scope.check_goods.series_name,
                                                            style_name: $scope.check_goods.style_name
                                                        })
                                                    } else {
                                                        console.log($scope.check_goods)
                                                        console.log(value2)
                                                        value.count--
                                                        value2.goods_detail[goods_item].cost += $scope.check_goods.platform_price * $scope.check_goods.quantity
                                                        value2.goods_detail[goods_item].procurement += $scope.check_goods.purchase_price_decoration_company * $scope.check_goods.quantity
                                                        value2.goods_detail[goods_item].quantity = +value2.goods_detail[goods_item].quantity + $scope.check_goods.quantity
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $scope.cur_status = 0
                    console.log($scope.all_goods)
                }
                if ($scope.cur_project == 1) {
                    $scope.is_edit = false
                    $state.go('nodata.main_material')
                } else if ($scope.cur_project == 2) {
                    $scope.is_edit = true
                    $scope.is_delete_btn = false
                    $state.go('nodata.other_material')
                }
            }, 300)
        }
        //智能报价无资料返回
        $scope.returnPrev = function () {
            console.log($scope.all_goods)
            console.log(JSON.parse(sessionStorage.getItem('nodata')))
            console.log($rootScope.curState_name)
            console.log($scope.cur_header)
            console.log($scope.inner_header)
            console.log($scope.cur_status)
            console.log($scope.cur_project)
            if ($rootScope.curState_name == 'nodata.product_detail') {
                $scope.have_header = true
                // if(sessionStorage.getItem('check_goods')!=null){
                    if ($scope.cur_status == 2||$scope.cur_status == 1) {
                        // $scope.cur_header = $scope.inner_first_level
                        $rootScope.fromState_name = 'nodata.all_goods'
                        sessionStorage.setItem('all_status',JSON.stringify({
                            have_header:true,
                            cur_header:$scope.cur_header,
                            inner_header:$scope.inner_header,
                            cur_project:$scope.cur_project,
                            cur_status:$scope.cur_status,
                            is_city:$scope.is_city,
                            is_edit:$scope.is_edit
                        }))
                    } else {
                        if($scope.cur_project == 0){
                            $rootScope.fromState_name = 'nodata.basics_decoration'
                        }else if($scope.cur_project == 1){
                            $rootScope.fromState_name = 'nodata.main_material'
                        }else{
                            $rootScope.fromState_name = 'nodata.other_material'
                        }
                        sessionStorage.setItem('all_status',JSON.stringify({
                            have_header:$scope.have_header,
                            cur_header:$scope.cur_header,
                            inner_header:$scope.inner_header,
                            cur_project:$scope.cur_project,
                            cur_status:0,
                            is_city:$scope.is_city,
                            is_edit:$scope.is_edit
                        }))
                    }
                // }
                sessionStorage.removeItem('check_goods')
            } else if ($rootScope.curState_name == 'nodata.all_goods') {
                if ($scope.cur_status == 2) {
                    $scope.cur_header = $scope.inner_first_level
                    $rootScope.fromState_name = 'nodata.second_level'
                } else if ($scope.cur_project == 2 && $scope.cur_status == 1) {
                    $scope.cur_header = $scope.inner_header
                    $scope.is_edit = true
                    $rootScope.fromState_name = 'nodata.other_material'
                } else if ($scope.cur_project == 1 && $scope.cur_status == 1) {
                    $scope.cur_header = $scope.inner_header
                    $rootScope.fromState_name = 'nodata.main_material'
                }
                sessionStorage.setItem('all_status',JSON.stringify({
                    have_header:$scope.have_header,
                    cur_header:$scope.cur_header,
                    inner_header:$scope.inner_header,
                    cur_project:$scope.cur_project,
                    cur_status:0,
                    is_city:$scope.is_city,
                    is_edit:$scope.is_edit
                }))
            } else if ($rootScope.curState_name == 'nodata.second_level') {
                $scope.cur_header = $scope.inner_header
                $scope.is_edit = true
                sessionStorage.setItem('all_status',JSON.stringify({
                    have_header:$scope.have_header,
                    cur_header:$scope.cur_header,
                    inner_header:$scope.inner_header,
                    cur_project:$scope.cur_project,
                    cur_status:0,
                    is_city:$scope.is_city,
                    is_edit:true
                }))
                $rootScope.fromState_name = 'nodata.other_material'
            } else if ($rootScope.curState_name == 'nodata.main_material' || $rootScope.curState_name == 'nodata.basics_decoration' || $rootScope.curState_name == 'nodata.other_material') {
                console.log($scope.is_delete_btn)
                if (!$scope.is_delete_btn) {
                    $scope.cur_header = '智能报价'
                    $scope.is_city = true
                    $scope.is_edit = false
                    if($scope.cur_all_goods!=undefined){
                        $scope.all_goods = $scope.cur_all_goods
                    }
                    // sessionStorage.removeItem('basic_nodata')
                    sessionStorage.removeItem('all_status')
                    sessionStorage.removeItem('cur_index')
                    sessionStorage.removeItem('params')
                    // sessionStorage.removeItem('nodata')
                    $rootScope.fromState_name = !!sessionStorage.getItem('materials') ? 'modelRoom' : 'nodata.house_list'
                } else {
                    $rootScope.fromState_name = 'nodata.other_material'
                }
            } else if ($rootScope.curState_name == 'nodata.house_list') {
                $scope.have_header = false
                $rootScope.fromState_name = 'home'
            }
            if (!!sessionStorage.getItem('huxingParams') && ($rootScope.curState_name == 'nodata.main_material' || $rootScope.curState_name == 'nodata.basics_decoration' || $rootScope.curState_name == 'nodata.other_material')) {
                $rootScope.goPrev(JSON.parse(sessionStorage.getItem('huxingParams')))
            } else {
                $rootScope.goPrev()
            }
        }
        // 保存返回
        $scope.save = function () {
            console.log($scope.all_goods)
            $scope.have_header = true
            $scope.is_city = true
            $scope.is_edit = false
            $scope.cur_header = '智能报价'
            let arr = []
            if (sessionStorage.getItem('materials')!=null) {
                for (let [key, value] of $scope.all_goods.entries()) {
                    arr.push({
                        id: value.id,
                        title: value.title,
                        goods: []
                    })
                }
                for (let [key, value] of $scope.all_goods.entries()) {
                    for (let [key1, value1] of value.second_level.entries()) {
                        for (let [key2, value2] of value1.three_level.entries()) {
                            for (let [key3, value3] of value2.goods_detail.entries()) {
                                for (let [key4, value4] of arr.entries()) {
                                    if (value.id == value4.id) {
                                        let item = $scope.level.find(function (item1) {
                                            return item1.id === value1.id
                                        })
                                        if (value3.goods_id == undefined) {
                                            value4.goods.push({
                                                cost: value3.cost,
                                                goods_id: value3.id,
                                                name: value3.name,
                                                id: value3.id,
                                                procurement:value3.procurement,
                                                goods_three: value2.title,
                                                goods_second: item.title,
                                                goods_first: value.title,
                                                quantity: value3.quantity,
                                                platform_price: value3.platform_price,
                                                image: value3.image,
                                                goods_name: value3.goods_name,
                                                series_name: value3.series_name,
                                                style_name: value3.style_name,
                                                category_id:value2.id
                                            })
                                        } else {
                                            value4.goods.push(value3)
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                console.log(arr)
                sessionStorage.setItem('materials', JSON.stringify(arr))
                sessionStorage.removeItem('all_status')
                $state.go('modelRoom', JSON.parse(sessionStorage.getItem('huxingParams')))
            } else {
                get_all_price()
                $state.go('nodata.house_list')
            }
        }
        //切换编辑/完成
        $scope.switch_delete = function () {
            if ($scope.cur_operate == '编辑') {
                $scope.cur_operate = '完成'
                $scope.is_delete_btn = true
                $scope.cur_content = ''
            } else {
                $scope.cur_operate = '编辑'
                $scope.is_delete_btn = false
                $scope.cur_content = 'modal'
            }
        }
        // 删除项
        $scope.delete_item = function (item, cur_id) {
            console.log(item)
            for (let [key, value] of $scope.all_goods.entries()) {
                for (let [key1, value1] of value.second_level.entries()) {
                    for (let [key2, value2] of value1.three_level.entries()) {
                        let cur_index = value2.goods_detail.findIndex(function (m) {
                            return m.id == item.id
                        })
                        console.log(cur_index)
                        if (cur_id == value2.id) {
                            if (cur_index != -1) {
                                value.cost -= item.cost
                                value.count--
                                $scope.all_delete_goods.push(item)
                                value2.goods_detail.splice(cur_index, 1)
                            }
                        }
                        if (value2.goods_detail.length == 0) {
                            value1.three_level.splice(key2, 1)
                        }
                        if (value1.three_level.length == 0) {
                            value.second_level.splice(key1, 1)
                        }
                    }
                }
            }
            console.log($scope.all_goods)
        }
        //添加一系列操作
        //添加按钮跳转选择三级页面
        $scope.go_three_item = function () {
            $scope.cur_status = 2
            $scope.cur_second_level = $scope.cur_header
            sessionStorage.setItem('all_status',JSON.stringify({
                have_header:$scope.have_header,
                cur_header:$scope.cur_header,
                inner_header:$scope.inner_header,
                cur_project:$scope.cur_project,
                cur_status:$scope.cur_status,
                is_city:false,
                is_edit:false
            }))
            _ajax.get('/mall/categories-level3', {
                pid: $scope.cur_item.id
            }, function (res) {
                console.log(res)
                $scope.cur_header = $scope.cur_item.title
                $scope.inner_first_level = $scope.cur_item.title
                $scope.is_city = false
                $scope.is_edit = false
                $scope.all_three_level = res.categories_level3
                $state.go('nodata.second_level')
            })
        }
        //通过三级获取商品
        $scope.go_cur_goods = function (item) {
            $scope.cur_three_level = item.title
            $scope.cur_three_id = item.id
            $scope.cur_three_item = item
            $scope.platform_status = 0
            $scope.rate_status = 0
            $scope.params.category_id = item.id
            $scope.params['sort[]'] = 'sold_number:3'
            $scope.cur_series_arr = []
            $scope.cur_style_arr = []
            $scope.cur_brand_arr = []
            $scope.price_min = ''
            $scope.price_max = ''
            $scope.params.platform_price_min = ''
            $scope.params.platform_price_max = ''
            $scope.params.brand_id = ''
            $scope.params.style_id = ''
            $scope.params.series_id = ''
            sessionStorage.setItem('cur_three',JSON.stringify({
                cur_three_item:$scope.cur_three_item
            }))
            tablePages()
            sessionStorage.setItem('params',JSON.stringify($scope.params))
            $('#myModal').modal('hide')
            $timeout(function () {
                $scope.have_header = true
                $scope.is_city = false
                $scope.is_edit = false
                $scope.cur_header = $scope.cur_three_level || item.title
                sessionStorage.setItem('all_status',JSON.stringify({
                    have_header:$scope.have_header,
                    cur_header:$scope.cur_header,
                    inner_header:$scope.inner_header,
                    cur_project:$scope.cur_project,
                    cur_status:$scope.cur_status,
                    is_city:$scope.is_city,
                    is_edit:$scope.is_edit
                }))
                $state.go('nodata.all_goods')
            }, 300)
        }
        //重置筛选
        $scope.reset_filter = function () {
            $scope.cur_series_arr = []
            $scope.cur_style_arr = []
            $scope.cur_brand_arr = []
            $scope.price_min = ''
            $scope.price_max = ''
            $scope.params.platform_price_min = ''
            $scope.params.platform_price_max = ''
            $scope.params.brand_id = ''
            $scope.params.style_id = ''
            $scope.params.series_id = ''
        }
        //商品排序
        $scope.sort = function (str) {
            console.log($scope.platform_status)
            if (str == 'sold_number') {
                $scope.platform_status = 0
                $scope.rate_status = 0
            } else if (str == 'platform_price') {
                if ($scope.platform_status == 0 || $scope.platform_status == 2) {
                    $scope.platform_status = 1
                } else if ($scope.platform_status == 1) {
                    $scope.platform_status = 2
                }
                $scope.rate_status = 0
            } else if (str == 'favourable_comment_rate') {
                if ($scope.rate_status == 0 || $scope.rate_status == 2) {
                    $scope.rate_status = 1
                } else if ($scope.rate_status == 1) {
                    $scope.rate_status = 2
                }
                $scope.platform_status = 0
            }
            $scope.params.category_id = $scope.cur_three_id
            $scope.params['sort[]'] = str + ($scope.platform_status == 0 ? ($scope.rate_status == 0 ? '' : ($scope.rate_status == 1 ? ':3' : ':4')) : ($scope.platform_status == 1 ? ':3' : ':4'))
            tablePages()
        }
        //填写筛选价格区间
        $scope.get_price = function (item) {
            console.log($scope.price_min)
            console.log($scope.price_max)
            if (item == 1) {
                if ($scope.price_max != '') {
                    if (+$scope.price_min > +$scope.price_max) {
                        let cur_item = $scope.price_min
                        $scope.price_min = $scope.price_max
                        $scope.price_max = cur_item
                    }
                }
            } else {
                if ($scope.price_min != '') {
                    if (+$scope.price_min > +$scope.price_max) {
                        let cur_item = $scope.price_min
                        $scope.price_min = $scope.price_max
                        $scope.price_max = cur_item
                    }
                }
            }
        }
        //改变风格系列以及品牌
        $scope.all_change = function (item, cur_item) {
            if (item == 1) {
                let index = $scope.cur_style_arr.findIndex(function (item) {
                    return item === cur_item.id
                })
                if (index != -1) {
                    $scope.cur_style_arr.splice(index, 1)
                    $scope.params.style_id = $scope.cur_style_arr.join(',')
                } else {
                    $scope.cur_style_arr.push(cur_item.id)
                    $scope.params.style_id = $scope.cur_style_arr.join(',')
                }
            } else if (item == 2) {
                let index = $scope.cur_series_arr.findIndex(function (item) {
                    return item === cur_item.id
                })
                if (index != -1) {
                    $scope.cur_series_arr.splice(index, 1)
                    $scope.params.series_id = $scope.cur_series_arr.join(',')
                } else {
                    $scope.cur_series_arr.push(cur_item.id)
                    $scope.params.series_id = $scope.cur_series_arr.join(',')
                }
            } else if (item == 3) {
                let index = $scope.cur_brand_arr.findIndex(function (item) {
                    return item === cur_item.id
                })
                if (index != -1) {
                    $scope.cur_brand_arr.splice(index, 1)
                    $scope.params.brand_id = $scope.cur_brand_arr.join(',')
                } else {
                    $scope.cur_brand_arr.push(cur_item.id)
                    $scope.params.brand_id = $scope.cur_brand_arr.join(',')
                }
            } else if (item == 4) {
                let index = $scope.cur_brand_copy.findIndex(function (item) {
                    return item === cur_item.id
                })
                if (index != -1) {
                    $scope.cur_brand_copy.splice(index, 1)
                } else {
                    $scope.cur_brand_copy.push(cur_item.id)
                }
            }
        }
        //跳转内层模态框
        $scope.go_inner_data = function () {
            $scope.cur_brand_copy = angular.copy($scope.cur_brand_arr)
            $scope.all_brand_copy = angular.copy($scope.all_goods_brands)
        }
        //保存内层数据
        $scope.save_inner_data = function () {
            $scope.cur_brand_arr = $scope.cur_brand_copy
            $scope.params.brand_id = $scope.cur_brand_arr.join(',')
        }
        //完成筛选
        $scope.complete_filter = function () {
            $scope.params.platform_price_min = $scope.price_min * 100
            $scope.params.platform_price_max = $scope.price_max * 100
            tablePages()
        }
        //筛选关键字
        $scope.$watch('keyword', function (newVal, oldVal) {
            console.log(newVal)
            if (newVal != '') {
                let arr = []
                if (!!$scope.all_goods_brands) {
                    for (let [key, value] of $scope.all_goods_brands.entries()) {
                        if (value.name.indexOf(newVal) != -1) {
                            arr.push(value)
                        }
                    }
                }
                $scope.all_goods_brands = arr
            } else {
                $scope.all_goods_brands = $scope.all_brand_copy
            }
        })
        //点击模态框其他区域
        // $(document).mouseup(function(e){
        //     var _con = $(' #myModal8 .modal-dialog ');   // 设置目标区域
        //     if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
        //         if($rootScope.curState_name == 'nodata.all_goods'){
        //             tablePages()
        //         }
        //     }
        // })
        //无资料计算
        console.log(JSON.parse(sessionStorage.getItem('nodata')))
        if(sessionStorage.getItem('basic_nodata')!=null){
            let basic_nodata = JSON.parse(sessionStorage.getItem('basic_nodata'))
            $scope.special_request = basic_nodata.special_request
            $scope.toponymy = basic_nodata.toponymy
            $scope.message = basic_nodata.message
            $scope.area = basic_nodata.area
            $scope.house_bedroom = basic_nodata.house_bedroom
            $scope.house_hall = basic_nodata.house_hall
            $scope.house_toilet = basic_nodata.house_toilet
            $scope.house_kitchen = basic_nodata.house_kitchen
            $scope.highCrtl = basic_nodata.highCrtl
            $scope.window = basic_nodata.window
            $scope.choose_stairs = basic_nodata.choose_stairs
            $scope.nowStairs = basic_nodata.nowStairs
        }
        if(sessionStorage.getItem('nodata')!=null){
            let nodata = JSON.parse(sessionStorage.getItem('nodata'))
            console.log(nodata)
            $scope.all_workers = nodata.all_workers
            $scope.all_price = nodata.all_price
            $scope.discount_price = nodata.discount_price
            $scope.show_material = nodata.show_material
            $scope.all_goods = nodata.all_goods
            if(sessionStorage.getItem('cur_index')!=null){
                $scope.cur_item = $scope.all_goods[sessionStorage.getItem('cur_index')]
                _ajax.get('/mall/categories-level3', {
                    pid: $scope.cur_item.id
                }, function (res) {
                    console.log(res)
                    $scope.inner_first_level = $scope.cur_item.title
                    $scope.all_three_level = res.categories_level3
                })
            }
        }
        if(sessionStorage.getItem('all_status')!=null){
            let all_status = JSON.parse(sessionStorage.getItem('all_status'))
            console.log(all_status)
            $scope.have_header = all_status.have_header
            $scope.cur_header = all_status.cur_header
            $scope.inner_header = all_status.inner_header
            $scope.cur_project = all_status.cur_project
            $scope.cur_status = all_status.cur_status
            $scope.is_city = all_status.is_city
            $scope.is_edit = all_status.is_edit
        }
        if(sessionStorage.getItem('params')!=null){
            $scope.params = JSON.parse(sessionStorage.getItem('params'))
            $scope.cur_three_id = $scope.params.category_id
            console.log($scope.params)
            tablePages()
        }
        if(sessionStorage.getItem('check_goods')!=null){
            $scope.check_goods = JSON.parse(sessionStorage.getItem('check_goods'))
            _ajax.get('/mall/goods-view', {
                id: +$scope.check_goods.id
            }, function (res) {
                console.log(res)
                if ($scope.cur_status == 1) {
                    $scope.cur_title = '更换'
                    $scope.check_goods['name'] = res.data.goods_view.brand_name
                    $scope.check_goods['goods_name'] = res.data.goods_view.title
                    $scope.check_goods['series_name'] = res.data.goods_view.series_name
                    $scope.check_goods['style_name'] = res.data.goods_view.style_name
                } else if ($scope.cur_status == 2) {
                    $scope.cur_title = '添加'
                    $scope.check_goods['name'] = res.data.goods_view.brand_name
                    $scope.check_goods['goods_name'] = res.data.goods_view.title
                    $scope.check_goods['series_name'] = res.data.goods_view.series_name
                    $scope.check_goods['style_name'] = res.data.goods_view.style_name
                }
                $scope.sale_services = res.data.goods_view.after_sale_services
                $scope.aftermarket = []
                $scope.protection = []
                for (let [key, value] of $scope.sale_services.entries()) {
                    if (value == '提供发票' || value == '上门安装') {
                        $scope.protection.push(value)
                    } else {
                        $scope.aftermarket.push(value)
                    }
                }
                $scope.description = res.data.goods_view.description
                $scope.supplier = res.data.goods_view.supplier
                $scope.cur_params = {
                    code: res.data.goods_view.sku,
                    title: res.data.goods_view.title,
                    attrs: res.data.goods_view.attrs,
                    left_number: res.data.goods_view.left_number,
                    series_name: res.data.goods_view.series_name,
                    style_name: res.data.goods_view.style_name,
                    images:res.data.goods_view.images
                }
                $('#myModal').modal('hide')
                $scope.have_header = false
                $timeout(function () {
                    var mySwiper = new Swiper('.swiper-container', {
                        direction: 'horizontal',
                        loop: true,
                        autoplay: 1000,
                        autoplayDisableOnInteraction: false,
                        observer:true,
                        observeParents:true,
                        effect: 'slide',

                        // 如果需要分页器
                        pagination: '.swiper-pagination',
                    })
                }, 0)
            })
            if(sessionStorage.getItem('cur_three')!=null){
                let cur_three = JSON.parse(sessionStorage.getItem('cur_three'))
                $scope.cur_three_item = cur_three.cur_three_item
                $scope.cur_three_id = $scope.cur_three_item.id
                $scope.check_goods['path'] = $scope.cur_three_item.path
            }
            if(sessionStorage.getItem('cur_goods_detail')!=null){
                $scope.cur_goods_detail = JSON.parse(sessionStorage.getItem('cur_goods_detail'))
            }
        }
        $scope.get_goods = function (valid, error) {
            console.log($scope.nodata_params)
            console.log(error)
            console.log($scope.nowStairs)
            $scope.all_workers = []
            $scope.discount_price = 0
            $scope.all_price = 0
            if (valid) {
                let data = {
                    bedroom: $scope.house_bedroom,//卧室
                    area: $scope.area,      //面积
                    hall: $scope.house_hall,       //餐厅
                    toilet: $scope.house_toilet,   // 卫生间
                    kitchen: $scope.house_kitchen,  //厨房
                    series: +$scope.cur_series.id,   //系列
                    style: +$scope.cur_style.id,  //风格
                    window: $scope.window,//飘窗
                    high: $scope.highCrtl, //层高
                    province: 510000,   //省编码
                    city: 510100,      // 市编码
                    stairway_id: $scope.choose_stairs,//有无楼梯
                    stairs: $scope.nowStairs.attribute//楼梯结构
                }
                let data1 = angular.copy(data)
                let data2 = angular.copy(data)
                $scope.all_goods = angular.copy($scope.stair)
                for (let [key, value] of $scope.all_goods.entries()) {
                    value['cost'] = 0
                    value['count'] = 0
                    value['second_level'] = []
                    value['procurement'] = 0
                }
                //生成材料数据(同步请求)
                //弱电
                $q.all([$http.get(baseUrl + '/owner/weak-current', {
                    params: data
                }).then(function (response) {
                    console.log('弱电')
                    console.log(response)
                    if(response.data.code == 200){
                    //整合二级
                    for (let [key, value] of $scope.level.entries()) {
                        for (let [key1, value1] of  $scope.all_goods.entries())
                            for (let [key2, value2] of response.data.data.weak_current_material.material.entries()) {
                                let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: [],procurement:0}
                                let cur_title = {title: value.title}
                                if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                    JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.second_level.push(cur_obj)
                                }
                            }
                    }
                    //整合三级
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of response.data.data.weak_current_material.material.entries()) {
                                let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                let cur_title = {title: value2.title}
                                if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                    value1.three_level.push(cur_obj)
                                }
                            }
                        }
                    }
                    //整合商品
                    for (let [key, value] of  $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of response.data.data.weak_current_material.material.entries()) {
                                    let cur_obj = {
                                        id: value3.id,
                                        cover_image: value3.cover_image,
                                        goods_name: value3.goods_name,
                                        cost: +value3.cost,
                                        name: value3.name,
                                        procurement:+value3.procurement,
                                        platform_price: value3.platform_price,
                                        profit_rate: value3.profit_rate,
                                        purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                        quantity: +value3.quantity,
                                        series_id: value3.series_id,
                                        style_id: value3.style_id,
                                        subtitle: value3.subtitle,
                                        supplier_price: value3.supplier_price,
                                        shop_name: value3.shop_name
                                    }
                                    let cur_goods = {
                                        id: value3.id,
                                    }
                                    if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                        value3.path.split(',')[2] == value2.id) {
                                        value.cost += value3.cost
                                        value1.cost += value3.cost
                                        value.procurement += value3.procurement
                                        value1.procurement += value3.procurement
                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                            value2.goods_detail.push(cur_obj)
                                            value.count++
                                        } else {
                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                if (value3.id == value4.id) {
                                                    value4.cost += value3.cost
                                                    value4.procurement += value3.procurement
                                                    value4.quantity += cur_obj.quantity
                                                    console.log(value4.quantity)
                                                    console.log(typeof value3.quantity)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }}
                    console.log($scope.all_workers)
                    console.log($scope.all_goods)
                }),
                    //强电
                    $http.get(baseUrl + '/owner/strong-current', {
                        params: data
                    }).then(function (response) {
                        console.log('强电')
                        console.log(response)
                        if(response.data.code == 200){
                        //整合二级
                        for (let [key, value] of $scope.level.entries()) {
                            for (let [key1, value1] of  $scope.all_goods.entries())
                                for (let [key2, value2] of response.data.data.strong_current_material.material.entries()) {
                                    let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: [],procurement:0}
                                    let cur_title = {title: value.title}
                                    if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                        JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.second_level.push(cur_obj)
                                    }
                                }
                        }
                        //整合三级
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of response.data.data.strong_current_material.material.entries()) {
                                    let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                    let cur_title = {title: value2.title}
                                    if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                        JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.three_level.push(cur_obj)
                                    }
                                }
                            }
                        }
                        //整合商品
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of value1.three_level.entries()) {
                                    for (let [key3, value3] of response.data.data.strong_current_material.material.entries()) {
                                        let cur_obj = {
                                            id: value3.id,
                                            cover_image: value3.cover_image,
                                            cost: +value3.cost,
                                            goods_name: value3.goods_name,
                                            name: value3.name,
                                            procurement: +value3.procurement,
                                            platform_price: value3.platform_price,
                                            profit_rate: value3.profit_rate,
                                            purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                            quantity: +value3.quantity,
                                            series_id: value3.series_id,
                                            style_id: value3.style_id,
                                            subtitle: value3.subtitle,
                                            supplier_price: value3.supplier_price,
                                            shop_name: value3.shop_name
                                        }
                                        let cur_goods = {id: value3.id}
                                        if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                            value3.path.split(',')[2] == value2.id) {
                                            value.cost += value3.cost
                                            value1.cost += value3.cost
                                            value.procurement += value3.procurement
                                            value1.procurement += value3.procurement
                                            if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                value2.goods_detail.push(cur_obj)
                                                value.count++
                                            } else {
                                                for (let [key4, value4] of value2.goods_detail.entries()) {
                                                    if (value3.id == value4.id) {
                                                        value4.cost += value3.cost
                                                        value4.procurement += value3.procurement
                                                        value4.quantity += cur_obj.quantity
                                                        console.log(value4.quantity)
                                                        console.log(typeof value3.quantity)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }}
                        console.log($scope.all_workers)
                        console.log($scope.all_goods)
                    }),
                    //水路
                    $http.get(baseUrl + '/owner/waterway', {
                        params: data
                    }).then(function (response) {
                        console.log('水路')
                        console.log(response)
                        if(response.data.code == 200){
                        //整合二级
                        for (let [key, value] of $scope.level.entries()) {
                            for (let [key1, value1] of  $scope.all_goods.entries())
                                for (let [key2, value2] of response.data.data.waterway_material_price.material.entries()) {
                                    let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: [],procurement:0}
                                    let cur_title = {title: value.title}
                                    if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                        JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.second_level.push(cur_obj)
                                    }
                                }
                        }
                        //整合三级
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of response.data.data.waterway_material_price.material.entries()) {
                                    let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                    let cur_title = {title: value2.title}
                                    if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                        JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.three_level.push(cur_obj)
                                    }
                                }
                            }
                        }
                        //整合商品
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of value1.three_level.entries()) {
                                    for (let [key3, value3] of response.data.data.waterway_material_price.material.entries()) {
                                        let cur_obj = {
                                            id: value3.id,
                                            cover_image: value3.cover_image,
                                            cost: +value3.cost,
                                            goods_name: value3.goods_name,
                                            name: value3.name,
                                            procurement:value3.procurement,
                                            platform_price: value3.platform_price,
                                            profit_rate: value3.profit_rate,
                                            purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                            quantity: +value3.quantity,
                                            series_id: value3.series_id,
                                            style_id: value3.style_id,
                                            subtitle: value3.subtitle,
                                            supplier_price: value3.supplier_price,
                                            shop_name: value3.shop_name
                                        }
                                        let cur_goods = {id: value3.id}
                                        if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                            value3.path.split(',')[2] == value2.id) {
                                            value.cost += value3.cost
                                            value1.cost += value3.cost
                                            value.procurement += value3.procurement
                                            value1.procurement += value3.procurement
                                            if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                value2.goods_detail.push(cur_obj)
                                                value.count++
                                            } else {
                                                for (let [key4, value4] of value2.goods_detail.entries()) {
                                                    if (value3.id == value4.id) {
                                                        value4.cost += value3.cost
                                                        value4.procurement += value3.procurement
                                                        value4.quantity += cur_obj.quantity
                                                        console.log(value4.cost)
                                                        console.log(value3.cost)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //工人费用
                        let cur_worker = {worker_kind: response.data.data.waterway_labor_price.worker_kind}
                        let cur_worker_price = response.data.data.waterway_labor_price.price
                        if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                                JSON.stringify(cur_worker).length - 1)) == -1) {
                            $scope.all_workers.push({
                                worker_kind: cur_worker.worker_kind,
                                price: cur_worker_price
                            })
                        } else {
                            for (let [key, value] of $scope.all_workers.entries()) {
                                if (cur_worker.worker_kind == value.worker_kind) {
                                    value.price += cur_worker_price
                                }
                            }
                        }}
                        console.log($scope.all_workers)
                        console.log($scope.all_goods)
                    }),
                    //防水
                    $http.get(baseUrl + '/owner/waterproof', {
                        params: data
                    }).then(function (response) {
                        console.log('防水')
                        console.log(response)
                        if(response.data.code == 200){
                        //整合二级
                        for (let [key, value] of $scope.level.entries()) {
                            for (let [key1, value1] of  $scope.all_goods.entries())
                                for (let [key2, value2] of response.data.data.waterproof_material.material.entries()) {
                                    let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: [],procurement:0}
                                    let cur_title = {title: value.title}
                                    if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                        JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.second_level.push(cur_obj)
                                    }
                                }
                        }
                        //整合三级
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of response.data.data.waterproof_material.material.entries()) {
                                    let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                    let cur_title = {title: value2.title}
                                    if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                        JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.three_level.push(cur_obj)
                                    }
                                }
                            }
                        }
                        //整合商品
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of value1.three_level.entries()) {
                                    for (let [key3, value3] of response.data.data.waterproof_material.material.entries()) {
                                        let cur_obj = {
                                            id: value3.id,
                                            cover_image: value3.cover_image,
                                            cost: value3.cost,
                                            goods_name: value3.goods_name,
                                            name: value3.name,
                                            procurement:value3.procurement,
                                            platform_price: value3.platform_price,
                                            profit_rate: value3.profit_rate,
                                            purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                            quantity: value3.quantity,
                                            series_id: value3.series_id,
                                            style_id: value3.style_id,
                                            subtitle: value3.subtitle,
                                            supplier_price: value3.supplier_price,
                                            shop_name: value3.shop_name
                                        }
                                        let cur_goods = {id: value3.id}
                                        if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                            value3.path.split(',')[2] == value2.id) {
                                            value.cost += value3.cost
                                            value1.cost += value3.cost
                                            value.procurement += value3.procurement
                                            value1.procurement += value3.procurement
                                            if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                value2.goods_detail.push(cur_obj)
                                                value.count++
                                            } else {
                                                for (let [key4, value4] of value2.goods_detail.entries()) {
                                                    if (value3.id == value4.id) {
                                                        value4.cost += value3.cost
                                                        value4.procurement += value3.procurement
                                                        value4.quantity += cur_obj.quantity
                                                        console.log(value4.cost)
                                                        console.log(value3.cost)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //工人费用
                        let cur_worker = {worker_kind: response.data.data.waterproof_labor_price.worker_kind}
                        let cur_worker_price = response.data.data.waterproof_labor_price.price
                        if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                                JSON.stringify(cur_worker).length - 1)) == -1) {
                            $scope.all_workers.push({
                                worker_kind: cur_worker.worker_kind,
                                price: cur_worker_price
                            })
                        } else {
                            for (let [key, value] of $scope.all_workers.entries()) {
                                if (cur_worker.worker_kind == value.worker_kind) {
                                    value.price += cur_worker_price
                                }
                            }
                        }
                        console.log($scope.all_workers)
                        console.log($scope.all_goods)
                        data1['waterproof_total_area'] = response.data.data.total_area}

                    }),
                    //木作
                    $http.get(baseUrl + '/owner/carpentry', {
                        params: data
                    }).then(function (response) {
                        console.log('木作')
                        console.log(response)
                        if(response.data.code == 200){
                        //整合二级
                        for (let [key, value] of $scope.level.entries()) {
                            for (let [key1, value1] of  $scope.all_goods.entries())
                                for (let [key2, value2] of response.data.data.carpentry_material.material.entries()) {
                                    let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: [],procurement:0}
                                    let cur_title = {title: value.title}
                                    if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                        JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.second_level.push(cur_obj)
                                    }
                                }
                        }
                        //整合三级
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of response.data.data.carpentry_material.material.entries()) {
                                    let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                    let cur_title = {title: value2.title}
                                    if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                        JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.three_level.push(cur_obj)
                                    }
                                }
                            }
                        }
                        //整合商品
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of value1.three_level.entries()) {
                                    for (let [key3, value3] of response.data.data.carpentry_material.material.entries()) {
                                        let cur_obj = {
                                            id: value3.id,
                                            cover_image: value3.cover_image,
                                            cost: value3.cost,
                                            goods_name: value3.goods_name,
                                            name: value3.name,
                                            procurement:value3.procurement,
                                            platform_price: value3.platform_price,
                                            profit_rate: value3.profit_rate,
                                            purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                            quantity: value3.quantity,
                                            series_id: value3.series_id,
                                            style_id: value3.style_id,
                                            subtitle: value3.subtitle,
                                            supplier_price: value3.supplier_price,
                                            shop_name: value3.shop_name
                                        }
                                        let cur_goods = {id: value3.id}
                                        if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                            value3.path.split(',')[2] == value2.id) {
                                            value.cost += value3.cost
                                            value1.cost += value3.cost
                                            value.procurement += value3.procurement
                                            value1.procurement += value3.procurement
                                            if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                value2.goods_detail.push(cur_obj)
                                                value.count++
                                            } else {
                                                for (let [key4, value4] of value2.goods_detail.entries()) {
                                                    if (value3.id == value4.id) {
                                                        value4.cost += value3.cost
                                                        value4.procurement += value3.procurement
                                                        value4.quantity += cur_obj.quantity
                                                        console.log(value4.cost)
                                                        console.log(value3.cost)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //工人费用
                        let cur_worker = {worker_kind: response.data.data.carpentry_labor_price.worker_kind}
                        let cur_worker_price = response.data.data.carpentry_labor_price.price
                        if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                                JSON.stringify(cur_worker).length - 1)) == -1) {
                            $scope.all_workers.push({
                                worker_kind: cur_worker.worker_kind,
                                price: cur_worker_price
                            })
                        } else {
                            for (let [key, value] of $scope.all_workers.entries()) {
                                if (cur_worker.worker_kind == value.worker_kind) {
                                    value.price += cur_worker_price
                                }
                            }
                        }}
                        console.log($scope.all_workers)
                        console.log($scope.all_goods)
                    }),
                    //乳胶漆
                    $http.get(baseUrl + '/owner/coating', {
                        params: data
                    }).then(function (response) {
                        console.log('乳胶漆')
                        console.log(response)
                        if(response.data.code == 200){
                        //整合二级
                        for (let [key, value] of $scope.level.entries()) {
                            for (let [key1, value1] of  $scope.all_goods.entries())
                                for (let [key2, value2] of response.data.data.coating_material.material.entries()) {
                                    let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: [],procurement:0}
                                    let cur_title = {title: value.title}
                                    if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                        JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.second_level.push(cur_obj)
                                    }
                                }
                        }
                        //整合三级
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of response.data.data.coating_material.material.entries()) {
                                    let cur_obj = {id: value2.path.split(',')[2], title: value2.title, goods_detail: []}
                                    let cur_title = {title: value2.title}
                                    if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                        JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        value1.three_level.push(cur_obj)
                                    }
                                }
                            }
                        }
                        //整合商品
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of value1.three_level.entries()) {
                                    for (let [key3, value3] of response.data.data.coating_material.material.entries()) {
                                        let cur_obj = {
                                            id: value3.id,
                                            cover_image: value3.cover_image,
                                            cost: value3.cost,
                                            goods_name: value3.goods_name,
                                            name: value3.name,
                                            procurement:value3.procurement,
                                            platform_price: value3.platform_price,
                                            profit_rate: value3.profit_rate,
                                            purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                            quantity: value3.quantity,
                                            series_id: value3.series_id,
                                            style_id: value3.style_id,
                                            subtitle: value3.subtitle,
                                            supplier_price: value3.supplier_price,
                                            shop_name: value3.shop_name
                                        }
                                        let cur_goods = {id: value3.id}
                                        if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                            value3.path.split(',')[2] == value2.id) {
                                            value.cost += value3.cost
                                            value1.cost += value3.cost
                                            value.procurement += value3.procurement
                                            value1.procurement += value3.procurement
                                            if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                value2.goods_detail.push(cur_obj)
                                                value.count++
                                            } else {
                                                for (let [key4, value4] of value2.goods_detail.entries()) {
                                                    if (value3.id == value4.id) {
                                                        value4.cost += value3.cost
                                                        value4.procurement += value3.procurement
                                                        value4.quantity += cur_obj.quantity
                                                        console.log(value4.cost)
                                                        console.log(value3.cost)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //工人费用
                        let cur_worker = {worker_kind: response.data.data.coating_labor_price.worker_kind}
                        let cur_worker_price = response.data.data.coating_labor_price.price
                        if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                                JSON.stringify(cur_worker).length - 1)) == -1) {
                            $scope.all_workers.push({
                                worker_kind: cur_worker.worker_kind,
                                price: cur_worker_price
                            })
                        } else {
                            for (let [key, value] of $scope.all_workers.entries()) {
                                if (cur_worker.worker_kind == value.worker_kind) {
                                    value.price += cur_worker_price
                                }
                            }
                        }
                        data2['bedroom_area'] = response.data.data.bedroom_area}
                        console.log($scope.all_workers)
                        console.log($scope.all_goods)
                    }),
                    $http.post('/owner/add-materials',{
                        area: $scope.area,      //面积
                        series: +$scope.cur_series.id,   //系列
                        style: +$scope.cur_style.id,  //风格
                        code: 510100      // 市编码
                    },config).then(function (res) {
                        console.log('添加材料项')
                        console.log(res)
                        if(res.data.code == 200){
                        for (let [key, value] of res.data.add_list.entries()) {
                            if (!!value) {
                                //整合二级
                                for (let [key3, value3] of $scope.level.entries()) {
                                    for (let [key1, value1] of  $scope.all_goods.entries()) {
                                        let cur_obj = {
                                            id: value3.id,
                                            title: value3.title,
                                            cost: 0,
                                            three_level: [],
                                            procurement:0
                                        }
                                        let cur_title = {title: value3.title}
                                        if (value.path.split(',')[1] == value3.id && value.path.split(',')[0] == value1.id &&
                                            JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                            value1.second_level.push(cur_obj)
                                        }
                                    }
                                }
                                //整合三级
                                for (let [key3, value3] of  $scope.all_goods.entries()) {
                                    for (let [key1, value1] of value3.second_level.entries()) {
                                                let cur_obj = {
                                                    id: value.path.split(',')[2],
                                                    title: value.title,
                                                    goods_detail: []
                                                }
                                                let cur_title = {title: value.title}
                                                if (value.path.split(',')[1] == value1.id && value.path.split(',')[0] == value3.id &&
                                                    JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                                    value1.three_level.push(cur_obj)
                                                }
                                    }
                                }
                                //整合商品
                                for (let [key5, value5] of  $scope.all_goods.entries()) {
                                    for (let [key1, value1] of value5.second_level.entries()) {
                                        for (let [key2, value2] of value1.three_level.entries()) {
                                                    let cur_obj = {
                                                        id: value.id,
                                                        cover_image: value.cover_image,
                                                        cost: value.cost,
                                                        goods_name: value.goods_name,
                                                        name: value.name,
                                                        procurement:value.procurement,
                                                        platform_price: value.platform_price,
                                                        profit_rate: value.profit_rate,
                                                        purchase_price_decoration_company: value.purchase_price_decoration_company,
                                                        quantity: value.quantity,
                                                        series_id: value.series_id,
                                                        style_id: value.style_id,
                                                        subtitle: value.subtitle,
                                                        supplier_price: value.supplier_price,
                                                        shop_name: value.shop_name
                                                    }
                                                    let cur_goods = {id: value.id}
                                                    if (value.path.split(',')[1] == value1.id && value.path.split(',')[0] == value5.id &&
                                                        value.path.split(',')[2] == value2.id) {
                                                        value5.cost += value.cost
                                                        value1.cost += value.cost
                                                        value5.procurement += value.procurement
                                                        value1.procurement += value.procurement
                                                        if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                            value2.goods_detail.push(cur_obj)
                                                            value5.count++
                                                        } else {
                                                            for (let [key4, value4] of value2.goods_detail.entries()) {
                                                                if (value.id == value4.id) {
                                                                    value4.cost += value.cost
                                                                    value4.procurement += value.procurement
                                                                    value4.quantity += cur_obj.quantity
                                                                    console.log(value4.cost)
                                                                    console.log(value.cost)
                                                                }
                                                            }
                                                        }
                                                    }
                                        }
                                    }
                                }
                            }
                        }}
                        console.log($scope.all_goods)
                        console.log($scope.all_workers)
                    })
                ]).then(function () {//计算总费用
                    $q.all([
                        //主要材料以及其他
                        $http.get(baseUrl + '/owner/assort-facility', {
                            params: data2
                        }).then(function (response) {
                            console.log('主要材料及其他')
                            console.log(response)
                            if(response.data.code == 200){
                            for (let [key, value] of response.data.data.goods.entries()) {
                                if (!!value) {
                                    //整合二级
                                    for (let [key3, value3] of $scope.level.entries()) {
                                        for (let [key1, value1] of  $scope.all_goods.entries())
                                            for (let [key2, value2] of value.entries()) {
                                                if (!!value2) {
                                                    let cur_obj = {
                                                        id: value3.id,
                                                        title: value3.title,
                                                        cost: 0,
                                                        three_level: [],
                                                        procurement:0
                                                    }
                                                    let cur_title = {title: value3.title}
                                                    if (value2.path.split(',')[1] == value3.id && value2.path.split(',')[0] == value1.id &&
                                                        JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                                        value1.second_level.push(cur_obj)
                                                    }
                                                }
                                            }
                                    }
                                    //整合三级
                                    for (let [key3, value3] of  $scope.all_goods.entries()) {
                                        for (let [key1, value1] of value3.second_level.entries()) {
                                            for (let [key2, value2] of value.entries()) {
                                                if (!!value2) {
                                                    let cur_obj = {
                                                        id: value2.path.split(',')[2],
                                                        title: value2.title,
                                                        goods_detail: []
                                                    }
                                                    let cur_title = {title: value2.title}
                                                    if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value3.id &&
                                                        JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                                        value1.three_level.push(cur_obj)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    //整合商品
                                    for (let [key5, value5] of  $scope.all_goods.entries()) {
                                        for (let [key1, value1] of value5.second_level.entries()) {
                                            for (let [key2, value2] of value1.three_level.entries()) {
                                                for (let [key3, value3] of value.entries()) {
                                                    if (!!value3) {
                                                        let cur_obj = {
                                                            id: value3.id,
                                                            cover_image: value3.cover_image,
                                                            cost: value3.cost,
                                                            goods_name: value3.goods_name,
                                                            name: value3.name,
                                                            procurement:value3.procurement,
                                                            platform_price: value3.platform_price,
                                                            profit_rate: value3.profit_rate,
                                                            purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                                            quantity: value3.quantity,
                                                            series_id: value3.series_id,
                                                            style_id: value3.style_id,
                                                            subtitle: value3.subtitle,
                                                            supplier_price: value3.supplier_price,
                                                            shop_name: value3.shop_name
                                                        }
                                                        let cur_goods = {id: value3.id}
                                                        if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value5.id &&
                                                            value3.path.split(',')[2] == value2.id) {
                                                            value5.cost += value3.cost
                                                            value1.cost += value3.cost
                                                            value5.procurement += value3.procurement
                                                            value1.procurement += value3.procurement
                                                            if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                                value2.goods_detail.push(cur_obj)
                                                                value5.count++
                                                            } else {
                                                                for (let [key4, value4] of value2.goods_detail.entries()) {
                                                                    if (value3.id == value4.id) {
                                                                        value4.cost += value3.cost
                                                                        value4.procurement += value3.procurement
                                                                        value4.quantity += cur_obj.quantity
                                                                        console.log(value4.cost)
                                                                        console.log(value3.cost)
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }}
                            console.log($scope.all_goods)
                            console.log($scope.all_workers)
                        }),
                        //泥作
                        $http.get(baseUrl + '/owner/mud-make', {
                            params: data1
                        }).then(function (response) {
                            console.log('泥作')
                            console.log(response)
                            if(response.data.code == 200){
                            //整合二级
                            for (let [key, value] of $scope.level.entries()) {
                                for (let [key1, value1] of  $scope.all_goods.entries())
                                    for (let [key2, value2] of response.data.data.mud_make_material.material.entries()) {
                                        let cur_obj = {id: value.id, title: value.title, cost: 0, three_level: [],procurement:0}
                                        let cur_title = {title: value.title}
                                        if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id &&
                                            JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                            value1.second_level.push(cur_obj)
                                        }
                                    }
                            }
                            //整合三级
                            for (let [key, value] of  $scope.all_goods.entries()) {
                                for (let [key1, value1] of value.second_level.entries()) {
                                    for (let [key2, value2] of response.data.data.mud_make_material.material.entries()) {
                                        let cur_obj = {
                                            id: value2.path.split(',')[2],
                                            title: value2.title,
                                            goods_detail: []
                                        }
                                        let cur_title = {title: value2.title}
                                        if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id &&
                                            JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                            value1.three_level.push(cur_obj)
                                        }
                                    }
                                }
                            }
                            //整合商品
                            for (let [key, value] of  $scope.all_goods.entries()) {
                                for (let [key1, value1] of value.second_level.entries()) {
                                    for (let [key2, value2] of value1.three_level.entries()) {
                                        for (let [key3, value3] of response.data.data.mud_make_material.material.entries()) {
                                            let cur_obj = {
                                                id: value3.id,
                                                cover_image: value3.cover_image,
                                                cost: value3.cost,
                                                goods_name: value3.goods_name,
                                                name: value3.name,
                                                procurement:value3.procurement,
                                                platform_price: value3.platform_price,
                                                profit_rate: value3.profit_rate,
                                                purchase_price_decoration_company: value3.purchase_price_decoration_company,
                                                quantity: value3.quantity,
                                                series_id: value3.series_id,
                                                style_id: value3.style_id,
                                                subtitle: value3.subtitle,
                                                supplier_price: value3.supplier_price,
                                                shop_name: value3.shop_name
                                            }
                                            let cur_goods = {id: value3.id}
                                            if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id &&
                                                value3.path.split(',')[2] == value2.id) {
                                                value.cost += value3.cost
                                                value1.cost += value3.cost
                                                value.procurement += value3.procurement
                                                value1.procurement += value3.procurement
                                                if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                    value2.goods_detail.push(cur_obj)
                                                    value.count++
                                                } else {
                                                    for (let [key4, value4] of value2.goods_detail.entries()) {
                                                        if (value3.id == value4.id) {
                                                            value4.cost += value3.cost
                                                            value4.procurement += value3.procurement
                                                            value4.quantity += cur_obj.quantity
                                                            console.log(value4.cost)
                                                            console.log(value3.cost)
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            //工人费用
                            let cur_worker = {worker_kind: response.data.data.mud_make_labor_price.worker_kind}
                            let cur_worker_price = response.data.data.mud_make_labor_price.price
                            if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                                    JSON.stringify(cur_worker).length - 1)) == -1) {
                                $scope.all_workers.push({
                                    worker_kind: cur_worker.worker_kind,
                                    price: cur_worker_price
                                })
                            } else {
                                for (let [key, value] of $scope.all_workers.entries()) {
                                    if (cur_worker.worker_kind == value.worker_kind) {
                                        value.price += cur_worker_price
                                    }
                                }
                            }}
                            console.log($scope.all_workers)
                            console.log($scope.all_goods)
                        })
                    ]).then(function () {
                        get_all_price()
                        sessionStorage.setItem('basic_nodata',JSON.stringify({
                            special_request:$scope.special_request,
                            toponymy:$scope.toponymy,
                            message:$scope.message,
                            area:$scope.area,
                            house_bedroom:$scope.house_bedroom,
                            house_hall:$scope.house_hall,
                            house_toilet:$scope.house_toilet,
                            house_kitchen:$scope.house_kitchen,
                            highCrtl:$scope.highCrtl,
                            window:$scope.window,
                            choose_stairs:$scope.choose_stairs,
                            nowStairs:$scope.nowStairs,
                        }))
                        $scope.show_material = true
                    })
                })

            } else {
                $scope.submitted = true
                for (let [key, value] of error.entries()) {
                    if (value.$invalid) {
                        $anchorScroll.yOffset = 300
                        $location.hash(value.$name)
                        $anchorScroll()
                        // $window.document.getElementById(value.$name).focus()
                        break
                    }
                }
            }
        }

        //获取价格
        function get_all_price() {
            let arr = [], arr1 = []
            $scope.all_price = 0
            $scope.discount_price = 0
            for (let [key, value] of $scope.all_goods.entries()) {
                arr1.push({
                    one_title: value.title,
                    price: value.cost,
                    procurement:value.procurement
                })
                for (let [key1, value1] of value.second_level.entries()) {
                    for (let [key2, value2] of value1.three_level.entries()) {
                        for (let [key3, value3] of value2.goods_detail.entries()) {
                            arr.push({
                                goods_id: value3.id,
                                num: value3.quantity
                            })
                        }
                    }
                }
            }
            $q.all([$http.post(baseUrl + '/order/calculation-freight', {
                goods: arr
            }, config).then(function (res) {
                console.log(res)
                if(res.data.code == 200) {
                    $scope.all_price += +res.data.data
                    $scope.discount_price += +res.data.data
                    console.log($scope.all_price)
                    console.log($scope.discount_price)
                }
            }), $http.post(baseUrl + '/owner/coefficient', {
                list: arr1
            }, config).then(function (res) {
                console.log(res)
                if(res.data.code == 200) {
                    $scope.all_price += +res.data.data.total_prices
                    $scope.discount_price += +res.data.data.special_offer
                    console.log($scope.all_price)
                    console.log($scope.discount_price)
                }
            })]).then(function () {
                let all_worker_price = $scope.all_workers.reduce(function (prev, cur) {
                    return prev + cur.price
                }, 0)
                console.log($scope.all_workers)
                console.log(all_worker_price)
                $scope.all_price += all_worker_price
                $scope.discount_price += all_worker_price
                sessionStorage.setItem('nodata',JSON.stringify({
                    all_goods:$scope.all_goods,
                    all_workers:$scope.all_workers,
                    all_price:$scope.all_price,
                    discount_price:$scope.discount_price,
                    show_material:$scope.show_material
                }))
                $scope.twelve_dismantle = ''//12墙拆除
                $scope.twenty_four_dismantle = ''//24墙拆除
                $scope.repair = ''//补烂
                $scope.twelve_new_construction = ''//12墙新建
                $scope.twenty_four_new_construction = ''//24墙新建
                $scope.building_scrap = false//有无建渣点
                console.log($scope.all_price)
                console.log($scope.discount_price)
            })
        }

        //跳转搜索页面
        $scope.go_search = function () {
            $state.go('nodata.cell_search')
            $scope.have_header = false
            $scope.cur_toponymy = $scope.toponymy//保存数据,防止跳转后前面值消失
        }

        /*小区搜索*/
        //修改搜索小区字段实时请求小区数据
        $scope.$watch('toponymy', function (newVal, oldVal) {
            console.log(newVal)
            if (newVal != ''&&newVal!=undefined) {
                _ajax.get('/owner/search', {
                    str: newVal
                }, function (res) {
                    console.log(res)
                    $scope.cur_all_house = res.data.list_effect
                    $scope.search_data = []//搜索出的小区
                    for (let [key, value] of res.data.list_effect.entries()) {
                        $scope.search_data.push({
                            id: value.id,
                            toponymy: value.toponymy,
                            site_particulars: value.site_particulars,
                            district_code: value.district_code,
                            street: value.street
                        })
                    }
                })
            } else {
                $scope.search_data = []
            }
        })
        //取消返回
        $scope.cancel = function () {
            $scope.toponymy = $scope.cur_toponymy
            $scope.have_header = true
            if (sessionStorage.getItem('huxingParams')!= null) {
                $rootScope.goPrev(JSON.parse(sessionStorage.getItem('huxingParams')))
            } else {
                history.go(-1)
            }
            // $timeout.cancel($scope.time)
            $scope.time = $timeout(function () {
                var mySwiper = new Swiper('.swiper-container', {
                    direction: 'horizontal',
                    loop: true,
                    autoplay: 1000,
                    autoplayDisableOnInteraction: false,
                    observer:true,
                    observeParents:true,
                    effect: 'slide',

                    // 如果需要分页器
                    pagination: '.swiper-pagination',
                })
            }, 0)
        }
        // 跳转到无资料
        $scope.go_nodata = function () {
            $state.go('nodata.house_list')
            sessionStorage.removeItem('huxing')
            $scope.have_header = true
            $scope.cur_header = '智能报价'
            $scope.is_city = true
            $scope.is_edit = false
            $timeout.cancel($scope.time)
            $scope.time = $timeout(function () {
                var mySwiper = new Swiper('.swiper-container', {
                    direction: 'horizontal',
                    loop: true,
                    autoplay: 1000,
                    autoplayDisableOnInteraction: false,
                    observer:true,
                    observeParents:true,
                    effect: 'slide',

                    // 如果需要分页器
                    pagination: '.swiper-pagination',
                })
            }, 0)
        }

        /*基础装修内页*/
        //杂工选项
        if (sessionStorage.getItem('huxing')!=null) {
            console.log(JSON.parse(sessionStorage.getItem('huxing')))
            $scope.area = JSON.parse(sessionStorage.getItem('huxing')).area
            $scope.cur_series = JSON.parse(sessionStorage.getItem('huxing')).series
            $scope.cur_style = JSON.parse(sessionStorage.getItem('huxing')).style
        }
        if (sessionStorage.getItem('backman')!=null) {
            console.log(JSON.parse(sessionStorage.getItem('backman')))
            for (let [key, value] of JSON.parse(sessionStorage.getItem('backman')).entries()) {
                if (value.backman_option == '12墙拆除') {
                    $scope.twelve_dismantle = value.backman_value//12墙拆除
                } else if (value.backman_option == '24墙拆除') {
                    $scope.twenty_four_dismantle = value.backman_value//24墙拆除
                } else if (value.backman_option == '补烂') {
                    $scope.repair = value.backman_value//补烂
                } else if (value.backman_option == '12墙新建(含双面抹灰)') {
                    $scope.twelve_new_construction = value.backman_value//12墙新建
                } else if (value.backman_option == '24墙新建(含双面抹灰)') {
                    $scope.twenty_four_new_construction = value.backman_value//24墙新建
                } else if (value.backman_option == '有无建渣点') {
                    $scope.building_scrap = !!value.backman_value//有无建渣点
                }
            }
        }
        //请求杂工数据
        $scope.go_handyman_options = function (valid) {
            console.log(JSON.parse(sessionStorage.getItem('materials')))
            console.log($scope.all_goods)
            if (sessionStorage.getItem('cur_goods')!=null) {
                $scope.cur_goods = JSON.parse(sessionStorage.getItem('cur_goods'))
                console.log($scope.cur_goods)
            }
            if (sessionStorage.getItem('worker')!=null) {
                $scope.all_worker = JSON.parse(sessionStorage.getItem('worker'))
            }
            if (sessionStorage.getItem('backman')!=null) {
                $scope.all_backman = JSON.parse(sessionStorage.getItem('backman'))
                console.log($scope.all_backman)
            }
            if (sessionStorage.getItem('materials')!=null) {
                $scope.all_goods = JSON.parse(sessionStorage.getItem('materials'))
            }
            //清理杂项原始数据
            if (valid) {
                if ($scope.cur_goods != undefined) {
                    for (let [key, value] of $scope.all_goods.entries()) {
                        for (let [key1, value1] of value.second_level.entries()) {
                            for (let [key2, value2] of value1.three_level.entries()) {
                                for (let [key3, value3] of value2.goods_detail.entries()) {
                                    for (let [key4, value4] of $scope.cur_goods.entries()) {
                                        if (!!sessionStorage.getItem('materials')) {
                                            console.log(value3)
                                            console.log(value4)
                                            if (value4.id == value3.goods_id) {
                                                value3.quantity -= value4.quantity
                                                value3.cost -= value4.cost
                                                value1.cost -= value4.cost
                                                value.cost -= value4.cost
                                            }
                                        } else {
                                            if (value4.path.split(',')[0] == value.id && value4.path.split(',')[1] == value1.id && value4.path.split(',')[2]
                                                == value2.id && value4.id == value3.id) {
                                                value3.quantity -= value4.quantity
                                                value3.cost -= value4.cost
                                                value1.cost -= value4.cost
                                                value.cost -= value4.cost
                                                if (value3.cost == 0) {
                                                    value2.goods_detail.splice(key3, 1)
                                                    console.log(value2.goods_detail)
                                                }
                                                if (value2.goods_detail.length == 0) {
                                                    value1.three_level.splice(key2, 1)
                                                }
                                                if (value1.three_level.length == 0) {
                                                    value.second_level.splice(key1, 1)
                                                }
                                            }
                                        }
                                    }
                                    for (let [key, value] of $scope.all_workers.entries()) {
                                        if ($scope.cur_worker != undefined) {
                                            if (value.worker_kind == $scope.cur_worker.worker_kind) {
                                                value.price -= $scope.cur_worker.price
                                            }
                                            if (value.price == 0) {
                                                $scope.all_workers.splice(key, 1)
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    console.log($scope.all_goods)
                }
                if (sessionStorage.getItem('backman')!=null) {
                    for (let [key, value] of $scope.all_backman.entries()) {
                        if (value.backman_option == '12墙拆除') {
                            value.backman_value = $scope.twelve_dismantle
                        } else if (value.backman_option == '24墙拆除') {
                            value.backman_value = $scope.twenty_four_dismantle
                        } else if (value.backman_option == '补烂') {
                            value.backman_value = $scope.repair
                        } else if (value.backman_option == '12墙新建(含双面抹灰)') {
                            value.backman_value = $scope.twelve_new_construction
                        } else if (value.backman_option == '24墙新建(含双面抹灰)') {
                            value.backman_value = $scope.twenty_four_new_construction
                        } else if (value.backman_option == '有无建渣点') {
                            value.backman_value = $scope.building_scrap
                        }
                    }
                    console.log($scope.all_backman)
                    sessionStorage.setItem('backman', JSON.stringify($scope.all_backman))
                }
                //保存并请求杂项数据
                if ($scope.twelve_dismantle=='' && $scope.twenty_four_dismantle=='' && $scope.repair=='' &&
                    $scope.twelve_new_construction=='' && $scope.twenty_four_new_construction=='') {
                    console.log($scope.all_goods)
                    console.log($scope.all_workers)
                    get_all_price()
                    $scope.cur_header = '智能报价'
                    if (sessionStorage.getItem('materials')!=null) {
                        let index = $scope.all_worker.findIndex(function (item) {
                            return item.worker_kind == '杂工'
                        })
                        if(index == -1){
                            $scope.all_workers.push({
                                'worker_kind':'杂工',
                                'worker_price':0
                            })
                        }else{
                            $scope.all_worker[index].worker_price = 0
                        }
                        sessionStorage.setItem('worker', JSON.stringify($scope.all_worker))
                        sessionStorage.removeItem('cur_goods')
                        let arr = []
                        for (let [key, value] of $scope.all_goods.entries()) {
                            arr.push({
                                id: value.id,
                                title: value.title,
                                goods: []
                            })
                        }
                        for (let [key, value] of $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of value1.three_level.entries()) {
                                    for (let [key3, value3] of value2.goods_detail.entries()) {
                                        for (let [key4, value4] of arr.entries()) {
                                            if (value3.quantity != 0) {
                                                if (value.id == value4.id && value3.quantity != 0) {
                                                    if (value3.goods_id == undefined) {
                                                        value4.goods.push({
                                                            cost: value3.cost,
                                                            goods_id: value3.id,
                                                            name: value3.name,
                                                            id: value3.id,
                                                            goods_three: value2.title,
                                                            goods_second: value1.title,
                                                            goods_first: value.title,
                                                            quantity: value3.quantity,
                                                            category_id:value2.id
                                                        })
                                                    } else {
                                                        value4.goods.push(value3)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        console.log(arr)
                        sessionStorage.setItem('materials', JSON.stringify(arr))
                        $state.go('modelRoom')
                    } else {
                        $state.go('nodata.house_list')
                    }
                } else {
                    _ajax.post('/owner/handyman', {
                        'province': 510000,
                        'city': 510100,
                        '12_dismantle': +$scope.twelve_dismantle || 0,
                        '24_dismantle': +$scope.twenty_four_dismantle || 0,
                        'repair': +$scope.repair || 0,
                        '12_new_construction': +$scope.twelve_new_construction || 0,
                        '24_new_construction': +$scope.twenty_four_new_construction || 0,
                        'building_scrap': $scope.building_scrap,
                        'area': $scope.area,
                        'series': (typeof $scope.cur_series === 'object') ? $scope.cur_series.id : $scope.cur_series,
                        'style': (typeof $scope.cur_style === 'object') ? $scope.cur_style.id : $scope.cur_style,
                    }, function (res) {
                        console.log('杂工')
                        console.log(res)
                        console.log($scope.all_goods)
                        $scope.cur_goods = res.data.total_material.material
                        $scope.cur_worker = res.data.labor_cost
                        sessionStorage.setItem('cur_goods', JSON.stringify($scope.cur_goods))
                        if(sessionStorage.getItem('materials')!=null) {
                            //整合一级
                            for (let [key, value] of $scope.stair.entries()) {
                                for (let [key1, value1] of res.data.total_material.material.entries()) {
                                    let cur_obj = {
                                        id: value.id,
                                        title: value.title,
                                        cost: 0,
                                        second_level: [],
                                        procurement: 0
                                    }
                                    let cur_title = {title: value.title}
                                    console.log(value1.cost)
                                    console.log($scope.all_goods)
                                    if (value1.path.split(',')[0] == value.id && value1.cost != 0 && JSON.stringify($scope.all_goods).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                        $scope.all_goods.push(cur_obj)
                                    }
                                }
                            }
                            //整合二级
                            for (let [key, value] of $scope.level.entries()) {
                                for (let [key1, value1] of  $scope.all_goods.entries())
                                    for (let [key2, value2] of res.data.total_material.material.entries()) {
                                        let cur_obj = {
                                            id: value.id,
                                            title: value.title,
                                            cost: 0,
                                            three_level: [],
                                            procurement: 0
                                        }
                                        let cur_title = {title: value.title}
                                        if (value2.path.split(',')[1] == value.id && value2.path.split(',')[0] == value1.id && value2.cost != 0 &&
                                            JSON.stringify(value1.second_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                            value1.second_level.push(cur_obj)
                                        }
                                    }
                            }
                            //整合三级
                            for (let [key, value] of  $scope.all_goods.entries()) {
                                for (let [key1, value1] of value.second_level.entries()) {
                                    for (let [key2, value2] of res.data.total_material.material.entries()) {
                                        let cur_obj = {
                                            id: value2.path.split(',')[2],
                                            title: value2.title,
                                            goods_detail: []
                                        }
                                        let cur_title = {title: value2.title}
                                        if (value2.path.split(',')[1] == value1.id && value2.path.split(',')[0] == value.id && value2.cost != 0 &&
                                            JSON.stringify(value1.three_level).indexOf(JSON.stringify(cur_title).slice(1, JSON.stringify(cur_title).length - 1)) == -1) {
                                            value1.three_level.push(cur_obj)
                                        }
                                    }
                                }
                            }
                        }
                        //整合商品
                        for (let [key, value] of  $scope.all_goods.entries()) {
                            for (let [key1, value1] of value.second_level.entries()) {
                                for (let [key2, value2] of value1.three_level.entries()) {
                                    for (let [key3, value3] of res.data.total_material.material.entries()) {
                                        let cur_obj = {
                                            id: value3.id,
                                            cover_image: value3.cover_image,
                                            cost: +value3.cost,
                                            name: value3.name,
                                            procurement:+value3.procurement,
                                            platform_price: value3.platform_price,
                                            profit_rate: value3.profit_rate,
                                            purchase_price_decoration_company: +value3.purchase_price_decoration_company,
                                            quantity: +value3.quantity,
                                            series_id: value3.series_id,
                                            style_id: value3.style_id,
                                            subtitle: value3.subtitle,
                                            supplier_price: value3.supplier_price,
                                            shop_name: value3.shop_name
                                        }
                                        let cur_goods = {
                                            id: value3.id,
                                        }
                                        if (value3.path.split(',')[1] == value1.id && value3.path.split(',')[0] == value.id && value3.cost != 0&&
                                            value3.path.split(',')[2] == value2.id) {
                                            value.cost += value3.cost
                                            value1.cost += value3.cost
                                            value.procurement += value3.procurement
                                            value1.procurement += value3.procurement
                                            if (JSON.stringify(value2.goods_detail).indexOf(JSON.stringify(cur_goods).slice(1, JSON.stringify(cur_goods).length - 1)) == -1) {
                                                value2.goods_detail.push(cur_obj)
                                            } else {
                                                for (let [key4, value4] of value2.goods_detail.entries()) {
                                                    if (sessionStorage.getItem('materials')!=null) {
                                                        if (value3.id == value4.id) {
                                                            value4.cost += +value3.cost
                                                            value4.procurement += +value3.procurement
                                                            value4.quantity += +value3.quantity
                                                        }
                                                    } else {
                                                        if (value3.id == value4.id) {
                                                            value4.cost += value3.cost
                                                            value4.procurement += value3.procurement
                                                            value4.quantity += value3.quantity
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //工人费用
                        let cur_worker = {worker_kind: res.data.labor_cost.worker_kind}
                        let cur_worker_price = res.data.labor_cost.price
                        if (JSON.stringify($scope.all_workers).indexOf(JSON.stringify(cur_worker).slice(1,
                                JSON.stringify(cur_worker).length - 1)) == -1) {
                            $scope.all_workers.push({
                                worker_kind: cur_worker.worker_kind,
                                price: cur_worker_price
                            })
                        } else {
                            for (let [key, value] of $scope.all_workers.entries()) {
                                if (cur_worker.worker_kind == value.worker_kind) {
                                    value.price += cur_worker_price
                                }
                            }
                        }
                        sessionStorage.setItem('backman',JSON.stringify([{
                            backman_option:'12墙拆除',
                            backman_value:$scope.twelve_dismantle==undefined?'':$scope.twelve_dismantle
                        },{
                            backman_option:'24墙拆除',
                            backman_value:$scope.twenty_four_dismantle==undefined?"":$scope.twenty_four_dismantle
                        },{
                            backman_option:'补烂',
                            backman_value:$scope.repair==undefined?"":$scope.repair
                        },{
                            backman_option:'12墙新建(含双面抹灰)',
                            backman_value:$scope.twelve_new_construction==undefined?"":$scope.twelve_new_construction
                        },{
                            backman_option:'24墙新建(含双面抹灰)',
                            backman_value:$scope.twenty_four_new_construction==undefined?"":$scope.twenty_four_new_construction
                        },{
                            backman_option:'有无建渣点',
                            backman_value:$scope.building_scrap
                        }]))
                        if (sessionStorage.getItem('materials')!=null) {
                            let arr = []
                            for (let [key, value] of $scope.all_goods.entries()) {
                                arr.push({
                                    id: value.id,
                                    title: value.title,
                                    goods: []
                                })
                            }
                            for (let [key, value] of $scope.all_goods.entries()) {
                                for (let [key1, value1] of value.second_level.entries()) {
                                    for (let [key2, value2] of value1.three_level.entries()) {
                                        for (let [key3, value3] of value2.goods_detail.entries()) {
                                            for (let [key4, value4] of arr.entries()) {
                                                if (value.id == value4.id && value3.quantity != 0) {
                                                    if (value3.goods_id == undefined) {
                                                        value4.goods.push({
                                                            cost: value3.cost,
                                                            procurement:value3.procurement,
                                                            goods_id: value3.id,
                                                            name: value3.name,
                                                            id: value2.id,
                                                            goods_three: value2.title,
                                                            goods_second: value1.title,
                                                            goods_first: value.title,
                                                            quantity: value3.quantity
                                                        })
                                                    } else {
                                                        value4.goods.push(value3)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            console.log(arr)
                            sessionStorage.setItem('materials', JSON.stringify(arr))
                            if (!!sessionStorage.getItem('huxingParams')) {
                                $state.go('modelRoom', JSON.parse(sessionStorage.getItem('huxingParams')))
                            }
                        } else {
                            $scope.cur_header = '智能报价'
                            get_all_price()
                            $state.go('nodata.house_list')
                        }
                    })
                }
            }

        }
        //申请样板间
        $scope.go_apply_case = function () {
            let obj = {
                province_code: 510000,//省编码
                city_code: 510100,//市编码
                bedroom: $scope.house_bedroom,//室
                toilet: $scope.house_toilet,//卫
                kitchen: $scope.house_kitchen,//厨
                high: $scope.highCrtl,//层高
                window: $scope.window,//飘窗
                sittingRoom_diningRoom: $scope.house_hall,//厅
                area: $scope.area,//面积
                requirement: $scope.special_request,//特殊要求
                toponymy: $scope.toponymy,//小区名称
                series: $scope.cur_series.id,//系列
                style: $scope.cur_style.id,//风格
                street: $scope.message,//小区地址
                stairway: $scope.choose_stairs,//是否有楼梯
                stair_id: $scope.nowStairs.id,//楼梯结构
                original_price: $scope.all_price,//原价
                sale_price: $scope.discount_price//折扣价
            }
            let arr = []
            for (let [key, value] of $scope.all_goods.entries()) {
                for (let [key1, value1] of value.second_level.entries()) {
                    for (let [key2, value2] of value1.three_level.entries()) {
                        for (let [key3, value3] of value2.goods_detail.entries()) {
                            arr.push({
                                'goods_id': value3.id,
                                'count': value3.quantity,
                                'price': value3.cost,
                                'first_cate_id': value.id
                            })
                        }
                    }
                }
            }
            obj['material'] = arr
            sessionStorage.setItem('payParams', JSON.stringify(obj))
            $state.go('deposit')
            console.log(obj)
        }
        console.log($scope.cur_header)
    })
    .filter("toHtml", ["$sce", function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        }
    }]);
