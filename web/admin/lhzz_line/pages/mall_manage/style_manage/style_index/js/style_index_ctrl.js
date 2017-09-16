let style_index = angular.module("styleindexModule", []);
style_index.controller("style_index", function ($scope, $http, $stateParams) {
    //POST请求的响应头
    let config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    //系列——展示数据数组
    $scope.series_arr = [];  //系列所有数据列表
    $scope.style_arr = [];  //风格所有数据列表

    /*属性管理*/
    $scope.handledesorder = true; //排序初始值
    $scope.handleascorder = false; //排序初始值
    let sortparam;

    if ($stateParams.showstyle) {
        $scope.changeTostyle();
    } else if ($stateParams.showattr) {
        $scope.changeToattr();
    } else {
        $scope.changeToseries
    }

    /*选项卡切换方法*/
    $scope.changeToseries = function () {
        $scope.showseries = true;
        $scope.showstyle = false;
        $scope.showattr = false;
    };

    $scope.changeTostyle = function () {
        $scope.showseries = false;
        $scope.showstyle = true;
        $scope.showattr = false;
    };

    $scope.changeToattr = function () {
        $scope.showseries = false;
        $scope.showstyle = false;
        $scope.showattr = true;
    };
    /************************系列开始*******************************/

//	系列——展示数据
    $http.get('http://test.cdlhzz.cn:888/mall/series-list').then(function (res) {
        $scope.series_arr = res.data.data.series_list.details;
        console.log("系列列表返回");
        console.log(res);
    }, function (err) {
        console.log(err);
    });
    //开启操作
    $scope.open_status = function (item) {
        $scope.open_item = item;
    };
    //开启确认按钮
    $scope.open_btn_ok = function () {
        let url = 'http://test.cdlhzz.cn:888/mall/series-status';
        $http.post(url, {
            id: +$scope.open_item.id,
            status: 1
        }, config).then(function (res) {
            $http.get('http://test.cdlhzz.cn:888/mall/series-list').then(function (res) {
                $scope.series_arr = res.data.data.series_list.details;
            }, function (err) {
                console.log(err);
            });
        }, function (err) {
            console.log(err);
        })
    };

    //关闭操作
    $scope.close_status = function (item) {
        $scope.close_item = item;
    };
    //关闭确认按钮
    $scope.close_btn_ok = function () {
        let url = 'http://test.cdlhzz.cn:888/mall/series-status';
        $http.post(url, {
            id: +$scope.close_item.id,
            status: 0
        }, config).then(function (res) {
            $http.get('http://test.cdlhzz.cn:888/mall/series-list').then(function (res) {
                $scope.series_arr = res.data.data.series_list.details;
            }, function (err) {
                console.log(err);
            });
        }, function (err) {
            console.log(err);
        })
    };
    //系类时间排序
    $scope.ser_time_sort_on_flag = true;
    $scope.ser_time_sort_down_flag = false;
    $scope.ser_time_sort = function (num) {
        if (num == 0) {
            $scope.ser_time_sort_on_flag = false;
            $scope.ser_time_sort_down_flag = true;
        } else {
            $scope.ser_time_sort_on_flag = true;
            $scope.ser_time_sort_down_flag = false;
        }
        $http.get('http://test.cdlhzz.cn:888/mall/series-time-sort', {
            params: {
                sort: +num
            }
        }).then(function (res) {
            $scope.series_arr = res.data.list;
            console.log(res);
        }, function (err) {
            console.log(err);
        });
    };
    //风格时间排序
    $scope.style_time_sort_on_flag = true;
    $scope.style_time_sort_down_flag = false;
    $scope.style_time_sort = function (num) {
        if (num == 0) {
            $scope.style_time_sort_on_flag = false;
            $scope.style_time_sort_down_flag = true;
        } else {
            $scope.style_time_sort_on_flag = true;
            $scope.style_time_sort_down_flag = false;
        }
        $http.get('http://test.cdlhzz.cn:888/mall/style-time-sort', {
            params: {
                sort: +num
            }
        }).then(function (res) {
            $scope.style_arr = res.data.list;
            console.log(res);
        }, function (err) {
            console.log(err);
        });
    };
    /******************************系列结束******************************/

    /*********************************风格开始*******************************/

//列表数据展示
    $http.get('http://test.cdlhzz.cn:888/mall/style-list').then(function (res) {
        console.log("风格列表返回");
        console.log(res);
        $scope.style_arr = res.data.data.series_list.details;
        //分页
        /*--------------------分页------------------------*/
        $scope.style_history_list = [];
        $scope.style_history_all_page = Math.ceil(res.data.data.series_list.total / 12);//获取总页数
        let all_num = $scope.style_history_all_page;//循环总页数
        for (let i = 0; i < all_num; i++) {
            $scope.style_history_list.push(i + 1)
        }
        console.log();
        //点击数字，跳转到多少页
        $scope.style_choosePage = function (page) {
            //判断输入的页数有没有，如果没有，不进行请求
            if ($scope.style_history_list.indexOf(parseInt(page)) != -1) {
                $scope.page = page;
                $http.get('http://test.cdlhzz.cn:888/mall/style-list', {params: {page: $scope.page}}).then(function (res) {
                    console.log(res);
                    $scope.style_arr = res.data.data.series_list.details;
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
        $scope.style_Previous = function () {
            if ($scope.page > 1) {                //当页数大于1时，执行
                $scope.page--;
                $scope.style_choosePage($scope.page);
            }
        };
        //下一页
        $scope.style_Next = function () {
            if ($scope.page < $scope.style_history_all_page) { //判断是否为最后一页，如果不是，页数+1,
                $scope.page++;
                $scope.style_choosePage($scope.page);
            }
        }
    }, function (err) {
        console.log(err);
    });
//开启操作
    $scope.style_open = function (item) {
        $scope.style_open_item = item;
    };
    //开启确认按钮
    $scope.style_open_btn_ok = function () {

        let url = 'http://test.cdlhzz.cn:888/mall/style-status';
        $http.post(url, {
            id: +$scope.style_open_item.id,
            status: 1
        }, config).then(function (res) {
            $http.get('http://test.cdlhzz.cn:888/mall/style-list').then(function (res) {
                $scope.style_arr = res.data.data.series_list.details;
            }, function (err) {
                console.log(err);
            });
            $scope.page = 1;
        }, function (err) {
            console.log(err);
        })
    };
//关闭操作
    $scope.style_close = function (item) {
        $scope.style_close_item = item;
    };
//关闭确认按钮
    $scope.style_close_btn_ok = function () {

        let url = 'http://test.cdlhzz.cn:888/mall/style-status';
        $http.post(url, {
            id: +$scope.style_close_item.id,
            status: 0
        }, config).then(function (res) {
            $http.get('http://test.cdlhzz.cn:888/mall/style-list').then(function (res) {
                $scope.style_arr = res.data.data.series_list.details;
            }, function (err) {
                console.log(err);
            });
            $scope.page = 1;
        }, function (err) {
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
            // params:{"sort[]":"id:3"}
        }).then(function (res) {
            $scope.proptable = res.data.data.goods_attr_list_admin.details;
        })
    })()

    /*属性分类选择*/
    $scope.attrselect = function () {
        /*只有一级下拉的全部*/
        if (($scope.firstselect == 0 && $scope.secselect == 0) || ($scope.firstselect == 0 && $scope.secselect == undefined)) {
            $http({
                method: "get",
                url: "http://test.cdlhzz.cn:888/mall/goods-attr-list-admin",
            }).then(function (res) {
                $scope.proptable = res.data.data.goods_attr_list_admin.details;
            })

            /*二级下拉为全部*/
        } else if ($scope.firstselect != 0 && $scope.secselect == 0) {
            $http({
                method: "get",
                url: "http://test.cdlhzz.cn:888/mall/goods-attr-list-admin",
                params: {pid: $scope.firstselect},
            }).then(function (res) {
                $scope.proptable = res.data.data.goods_attr_list_admin.details;
            })

            /*两个都不为全部*/
        } else if ($scope.firstselect != 0 && $scope.secselect != 0) {
            $http({
                method: "get",
                url: "http://test.cdlhzz.cn:888/mall/goods-attr-list-admin",
                params: {pid: $scope.secselect},
            }).then(function (res) {
                $scope.proptable = res.data.data.goods_attr_list_admin.details;
            })
        }
    }

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