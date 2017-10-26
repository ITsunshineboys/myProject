let style_index = angular.module("styleindexModule", []);
style_index.controller("style_index", function ($scope, $http, $stateParams) {
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
    let tablePages=function () {
        $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
        $http.get(baseUrl+'/mall/style-time-sort',{
            params:$scope.params
        }).then(function (res) {
            console.log(res);
            $scope.style_arr=res.data.list.details;
            $scope.Config.totalItems = res.data.list.total;
        },function (err) {
            console.log(err);
        })
    };
    $scope.params = {
        page: 1,                        // 当前页数
        sort:0
    };


    //POST请求的响应头
    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };

    let sortparam;
    //系列——展示数据数组
    $scope.series_arr = [];  //系列所有数据列表
    $scope.style_arr = [];  //风格所有数据列表
    /*属性管理*/
    $scope.handledesorder = true; //排序初始值
    $scope.handleascorder = false; //排序初始值

    $scope.changeTabbar = (function () {
        if ($stateParams.showstyle) {
            $scope.showseries = false;
            $scope.showstyle = true;
            $scope.showattr = false;
        } else if ($stateParams.showattr) {
            $scope.showseries = false;
            $scope.showstyle = false;
            $scope.showattr = true;
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
        $http.get('http://test.cdlhzz.cn:888/mall/series-time-sort',{
            params:{
                sort:0
            }
        }).then(function (res) {
            $scope.series_arr=res.data.list;
        },function (err) {
            console.log(err);
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
    };
    /************************系列开始*******************************/

//	系列——展示数据
    $http.get('http://test.cdlhzz.cn:888/mall/series-time-sort',{
        params:{
            sort:0
        }
    }).then(function (res) {
        $scope.series_arr=res.data.list;
        console.log("系列列表返回");
        console.log(res);
    },function (err) {
        console.log(err);
    });
    //开启操作
    $scope.open_status=function (item) {
        $scope.open_item=item;
    };
    //开启确认按钮
    $scope.open_btn_ok=function () {
        let url='http://test.cdlhzz.cn:888/mall/series-status';
        $http.post(url,{
            id:+$scope.open_item.id,
            status:1
        },config).then(function (res) {
            $http.get('http://test.cdlhzz.cn:888/mall/series-time-sort').then(function (res) {
                $scope.series_arr=res.data.list;
            },function (err) {
                console.log(err);
            });
        },function (err) {
            console.log(err);
        })
    };

    //关闭操作
    $scope.close_status=function (item) {
        $scope.close_item=item;
    };
    //关闭确认按钮
    $scope.close_btn_ok=function () {
        let url='http://test.cdlhzz.cn:888/mall/series-status';
        $http.post(url,{
            id:+$scope.close_item.id,
            status:0
        },config).then(function (res) {
            $http.get('http://test.cdlhzz.cn:888/mall/series-time-sort').then(function (res) {
                $scope.series_arr=res.data.list;
            },function (err) {
                console.log(err);
            });
        },function (err) {
            console.log(err);
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
        $http.get('http://test.cdlhzz.cn:888/mall/series-time-sort',{
            params:{
                sort:$scope.ser_sort_num
            }
        }).then(function (res) {
            $scope.series_arr=res.data.list;
            console.log(res);
        },function (err) {
            console.log(err);
        });
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
        let url='http://test.cdlhzz.cn:888/mall/style-status';
        $http.post(url,{
            id:+$scope.style_open_item.id,
            status:1
        },config).then(function (res) {
            $scope.Config.currentPage=1;
            tablePages();
        },function (err) {
            console.log(err);
        })
    };
//关闭操作
    $scope.style_close=function (item) {
        $scope.style_close_item=item;
    };
//关闭确认按钮
    $scope.style_close_btn_ok=function () {
        let url='http://test.cdlhzz.cn:888/mall/style-status';
        $http.post(url,{
            id:+$scope.style_close_item.id,
            status:0
        },config).then(function (res) {
            $scope.Config.currentPage=1;
            tablePages();
        },function (err) {
            console.log(err);
        })
    };


    /*********************************风格结束*******************************/

    /*********************************属性开始*******************************/
    /*分类选择下拉框*/
    /*分类选择一级下拉框*/
    $scope.firstClass = (function () {
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
        }).then(function (response) {
            $scope.firstclass = response.data.data.categories;
            $scope.firstselect = response.data.data.categories[0].id;
        })
    })()

    /*分类选择二级下拉框*/
    $scope.subClass = function (obj) {
        console.log(obj)
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
            params: {pid: obj}
        }).then(function (response) {
            $scope.secondclass = response.data.data.categories;
            $scope.secselect = response.data.data.categories[0].id;
        })
    }

    /*属性管理table*/
    $scope.allproperties = (function () {
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/goods-attr-list-admin",
            params: {"sort[]": "attr_op_time:3"}
        }).then(function (res) {
            $scope.proptable = res.data.data.goods_attr_list_admin.details;
        })
    })()

    /*属性分类选择*/
    $scope.$watch('firstselect', function (newVal,oldVal) {
        $scope.handledesorder = true;
        $scope.handleascorder = false;
        if (!$scope.secselect) {
            $http.get('http://test.cdlhzz.cn:888/mall/goods-attr-list-admin', {
                params: {pid: +newVal},
            }).then(function (res) {
                console.log('属性管理分类选择第一个下拉框')
                console.log(res);
                $scope.proptable = res.data.data.goods_attr_list_admin.details;
            }, function (err) {
                console.log(err);
            })
        } else {
            return;
        }
        ;
    })

    $scope.$watch('secselect', function (newVal,oldVal) {
        $scope.handledesorder = true;
        $scope.handleascorder = false;
        if (newVal != 0) {
            $http.get('http://test.cdlhzz.cn:888/mall/goods-attr-list-admin', {
                params: {pid: +newVal},
            }).then(function (res) {
                console.log(res);
                $scope.proptable = res.data.data.goods_attr_list_admin.details;
            }, function (err) {
                console.log(err);
            })
        } else {
            $http.get('http://test.cdlhzz.cn:888/mall/goods-attr-list-admin', {
                params: {pid: +$scope.firstselect},
            }).then(function (res) {
                $scope.proptable = res.data.data.goods_attr_list_admin.details;
            }, function (err) {
                console.log(err);
            })
        }
    });


    /*操作时间降序*/
    $scope.handleDesorder = () => {
        $scope.handledesorder = true;
        $scope.handleascorder = false;
        sortTime(3);
    }


    /*操作时间升序*/
    $scope.handleAsorder = () => {
        $scope.handledesorder = false;
        $scope.handleascorder = true;
        sortTime(4);
    }

    /*排序公共方法*/
    function sortTime(sortmethod) {
        $scope.firstselect == 0 && !$scope.secselect ? $scope.pid = $scope.firstselect : false; //第一个下拉框为全部
        $scope.firstselect != 0 && !$scope.secselect ? $scope.pid = $scope.firstselect : false; //二级下拉为全部
        $scope.firstselect != 0 && $scope.secselect ? $scope.pid = $scope.secselect : false; //两个都不为全部
        sortparam = 'attr_op_time:' + sortmethod;
        $http({
            method: "get",
            params: {"sort[]": sortparam, pid: $scope.pid || 0},
            url: "http://test.cdlhzz.cn:888/mall/goods-attr-list-admin",
        }).then(function (res) {
            $scope.proptable = res.data.data.goods_attr_list_admin.details;
            // $scope.selPage = 1;
        })
    }

    /*********************************属性结束*******************************/
});