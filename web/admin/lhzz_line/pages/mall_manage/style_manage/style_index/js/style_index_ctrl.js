let style_index = angular.module("styleindexModule", []);
style_index.controller("style_index", function ($rootScope,$scope, $http, $stateParams,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '系列/风格/属性管理'
    }];
    $scope.page = $stateParams.page;
    if ($scope.page == '') {
        $scope.page = 1;
    }
    /*分页配置*/
    $scope.Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    };
    $scope.params = {
        page: 1,                        // 当前页数
        sort:0
    };
    let tablePages=function () {
        $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/mall/style-time-sort',$scope.params,function (res) {
            console.log(res);
            $scope.style_arr=res.list.details;
            $scope.Config.totalItems = res.list.total;
        })
    };


    let sortparam;
    //系列——展示数据数组
    $scope.series_arr = [];  //系列所有数据列表
    $scope.style_arr = [];  //风格所有数据列表
    /*属性管理*/
    $scope.handledesorder = true; //排序初始值
    $scope.handleascorder = false; //排序初始值

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tableList();
        }
    }

    /*分类选择下拉框初始化*/
    $scope.dropdown = {
        firstselect: 0,
        secselect: 0,
        keyword: ''
    }

    /*分类选择一级下拉框*/
    function firstClass() {
        _ajax.get('/mall/categories-manage-admin',{},function (res) {
            $scope.firstclass = res.data.categories;
            $scope.dropdown.firstselect = res.data.categories[0].id;
        })
    }


    $scope.changeTabbar = (function () {
        console.log($stateParams)
        if ($stateParams.showstyle) {
            $scope.showseries = false;
            $scope.showstyle = true;
            $scope.showattr = false;
        } else if ($stateParams.showattr) {
            $scope.showseries = false;
            $scope.showstyle = false;
            $scope.showattr = true;
            firstClass();
        } else {
            $scope.showseries = true;
            $scope.showstyle = false;
            $scope.showattr = false;
        }
    })()

    /*选项卡切换方法*/
    //系列
    $scope.changeToseries = function () {
        $scope.showseries = true;
        $scope.showstyle = false;
        $scope.showattr = false;
        $scope.ser_time_img='lib/images/sort_down.png';
        _ajax.get('/mall/series-time-sort',{sort:0},function (res) {
            console.log(res);
            $scope.series_arr=res.list;
        });
    };
    //风格
    $scope.changeTostyle = function () {
        $scope.showseries = false;
        $scope.showstyle = true;
        $scope.showattr = false;
        $scope.params.page=1;
        $scope.params.sort=0;
        $scope.style_time_img='lib/images/sort_down.png';
        $scope.Config.currentPage=1;
        tablePages();
    };

    //属性
    $scope.changeToattr = function () {
        $scope.showseries = false;
        $scope.showstyle = false;
        $scope.showattr = true;
        $scope.attr_params = {
            pid: 0,   //父分类id
            page: 1,  //当前页数
            'sort[]': "attr_op_time:3" //排序规则 默认按最后操作时间降序排列
        }
        $scope.pageConfig.currentPage = 1;
        firstClass()
        tableList();

    };


    /************************系列开始*******************************/

//	系列——展示数据
    _ajax.get('/mall/series-time-sort',{sort:0},function (res) {
        $scope.series_arr=res.list;
    })
    //开启操作
    $scope.open_status=function (item) {
        $scope.open_item=item;
    };
    //开启确认按钮
    $scope.open_btn_ok=function () {
        _ajax.post('/mall/series-status',{
            id:+$scope.open_item.id,
            status:1
        },function (res) {
            _ajax.get('/mall/series-time-sort',{},function (res) {
                $scope.series_arr=res.list;
            })
        })
    };

    //关闭操作
    $scope.close_status=function (item) {
        $scope.close_item=item;
    };
    //关闭确认按钮
    $scope.close_btn_ok=function () {
        _ajax.post('/mall/series-status',{
            id:+$scope.close_item.id,
            status:0
        },function (res) {
            _ajax.get('/mall/series-time-sort',{},function (res) {
                $scope.series_arr=res.list;
            })
        })
    };
    //系类时间排序
    $scope.ser_time_img='lib/images/sort_down.png';
    $scope.ser_time_sort=function () {
        if($scope.ser_time_img=='lib/images/sort_down.png'){
            $scope.ser_time_img='lib/images/sort_up.png';
            $scope.ser_sort_num=1;
        }else{
            $scope.ser_time_img='lib/images/sort_down.png';
            $scope.ser_sort_num=0;
        }
        _ajax.get('/mall/series-time-sort',{sort:$scope.ser_sort_num},function (res) {
            $scope.series_arr=res.list;
        })
    };
    /******************************系列结束******************************/

    /*********************************风格开始*******************************/
    //风格时间排序
    $scope.style_time_img='lib/images/sort_down.png';
    $scope.style_time_sort=function () {
        if($scope.style_time_img=='lib/images/sort_down.png'){
            $scope.style_time_img='lib/images/sort_up.png';
            $scope.params.sort=1;
            $scope.Config.currentPage=1;
            tablePages();
        }else{
            $scope.style_time_img='lib/images/sort_down.png';
            $scope.params.sort=0;
            $scope.Config.currentPage=1;
            tablePages();
        }
    }
//开启操作
    $scope.style_open=function (item) {
        $scope.style_open_item=item;
    };
    //开启确认按钮
    $scope.style_open_btn_ok=function () {
        _ajax.post('/mall/style-status',{
            id:+$scope.style_open_item.id,
            status:1
        },function (res) {
            $scope.Config.currentPage=1;
            tablePages();
        })
    };
//关闭操作
    $scope.style_close=function (item) {
        $scope.style_close_item=item;
    };
//关闭确认按钮
    $scope.style_close_btn_ok=function () {
        _ajax.post('/mall/style-status',{
            id:+$scope.style_close_item.id,
            status:0
        },function (res) {
            $scope.Config.currentPage=1;
            tablePages();
        })
    };


    /*********************************风格结束*******************************/

    /*********************************属性开始*******************************/
    /*默认参数*/
    $scope.attr_params = {
        pid: 0,   //父分类id
        page: 1,  //当前页数
        'sort[]': "attr_op_time:3", //排序规则 默认按最后操作时间降序排列
        keyword:''
    }

    /*排序按钮样式控制*/
    $scope.sortStyleFunc = () => {
        return $scope.attr_params['sort[]'].split(':')[1]
    }


    /*分类选择二级下拉框*/
    function subClass(obj) {
        _ajax.get('/mall/categories-manage-admin',{pid: obj},function (res) {
            $scope.secondclass = res.data.categories;
            $scope.dropdown.secselect = res.data.categories[0].id;
        })
    }

    /*分类筛选方法*/
    $scope.$watch('dropdown.firstselect', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        $scope.attr_params['sort[]'] = 'attr_op_time:3';      // 最后操作时间排序
        $scope.dropdown.keyword = '';
        $scope.pageConfig.currentPage = 1;
        subClass(value);
        $scope.attr_params.pid = value;
        tableList()
    });


    $scope.$watch('dropdown.secselect', function (value, oldValue) {
        $scope.attr_params['sort[]'] = 'attr_op_time:3';      // 最后操作时间排序
        $scope.dropdown.keyword = '';
        $scope.pageConfig.currentPage = 1;
        if (value == oldValue) {
            return
        }
        if (value) {
            $scope.attr_params.pid = value;
            tableList()
        } else {
            //二级分类id为0
            $scope.attr_params.pid = $scope.dropdown.firstselect;
            tableList()
        }
    });

    // 最后操作时间排序
    $scope.sortTime = function () {
        $scope.attr_params['sort[]'] = $scope.attr_params['sort[]'] == 'attr_op_time:3' ? 'attr_op_time:4' : 'attr_op_time:3';
        $scope.pageConfig.currentPage = 1;
        tableList();
    }

    // 搜索
    $scope.search = function () {
        $scope.attr_params.page = 1;
        $scope.attr_params['sort[]'] = 'attr_op_time:3';      // 最后操作时间排序
        // $scope.dropdown.firstselect = 0;
        $scope.attr_params.page = 1;
        tableList();
    }


    /*列表数据获取*/
    function tableList() {
        $scope.attr_params.keyword = $scope.dropdown.keyword;
        $scope.attr_params.page = $scope.pageConfig.currentPage;
        _ajax.get('/mall/goods-attr-list-admin',$scope.attr_params,function (res) {
            console.log(res);
            $scope.pageConfig.totalItems = res.data.goods_attr_list_admin.total;
            $scope.listdata = res.data.goods_attr_list_admin.details;
        })
    }
    /*********************************属性结束******************************/
});