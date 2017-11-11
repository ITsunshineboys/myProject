let account_index = angular.module("account_index_module", []);
account_index.controller("account_index_ctrl", function ($scope, $http, $state, $stateParams) {
    $scope.normal_flag = true;
    $scope.close_flag = false;
    $scope.mm = $scope;
    $scope.myng = $scope;
    $scope.normal = function () {
        $scope.normal_flag = true;
        $scope.close_flag = false;
    };
    $scope.close = function () {
        $scope.close_flag = true;
        $scope.normal_flag = false;
    };
    $scope.flag = true;
    $scope.strat = false;

    // 点击筛选降序
    $scope.changePic = function () {
        $scope.flag = false;
        $scope.strat = true;
        //console.log(1112)
        $http({
            method: 'get',
            url: baseUrl + '/mall/user-list',
            params: {
                "sort[]": "id:3",
                status: 1
            }

        }).then(function successCallback(response) {
            $scope.account = response.data.data.user_list.details;
            console.log(response);
            console.log($scope.account);
        });

    };
    //点击筛选升序
    $scope.changePicse = function () {
        $scope.strat = false;
        $scope.flag = true;
        $http({
            method: 'get',
            url: baseUrl + '/mall/user-list',
            params: {
                "sort[]": "id:4",
                status: 1
            }
        }).then(function successCallback(response) {
            $scope.account = response.data.data.user_list.details;
            console.log(response);
            console.log($scope.account);
        });
    };


    //点击搜索
    //$scope.search_text = "";
    $scope.getSearch = function () {
        console.log($scope.search_text);
        $http({
            method: 'get',
            url: baseUrl + '/mall/user-list',
            params: {
                keyword: $scope.search_text,
                status: 1,
                time_type: "all",

            }
        }).then(function successCallback(response) {
            console.log(response);
            $scope.history_list = [];
            $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
            let all_num = $scope.history_all_page;//循环总页数
            for (let i = 0; i < all_num; i++) {
                $scope.history_list.push(i + 1)
            }
            $scope.account = response.data.data.user_list.details;
            console.log(response);
        });
    };
    //监听搜索的内容为空时，恢复初始状态
    $scope.$watch("search_text", function (newVal, oldVal) {

        if (newVal == "") {
            $http({
                method: 'get',
                url: baseUrl + '/mall/user-list',
                params: {
                    status: 1
                }
            }).then(function successCallback(response) {

                $scope.history_list = [];
                $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
                console.log($scope.history_all_page);
                let all_num = $scope.history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.history_list.push(i + 1);
                    console.log($scope.history_list)
                }
                $scope.account = response.data.data.user_list.details;
                for (let [key, value] of $scope.account_colse.entries()) {
                    value['names'] = value.role_names.join(',')
                }
            });
        }
    })

    //获取账户管理列表 正常状态
    $http({
        method: 'get',
        url: baseUrl + '/mall/user-list',
        params: {
            status: 1
        }
    }).then(function successCallback(response) {
        $scope.account = response.data.data.user_list.details;
        for (let [key, value] of $scope.account.entries()) {
            value['names'] = value.role_names.join(',')
        }
    });

    //单个关闭原因
    $scope.getId = function (item) {
        $scope.id = item;
        $scope.getReson = function () {
            $http({
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data)
                },
                method: 'post',
                url: baseUrl + '/mall/user-status-toggle',
                data: {
                    user_id: +$scope.id,
                    remark: $scope.text
                }
            }).then(function successCallback(response) {
                console.log(response);
                $http({
                    method: 'get',
                    url: baseUrl + '/mall/user-list',
                    params: {
                        status: 1
                    }
                }).then(function successCallback(response) {
                    $scope.account = response.data.data.user_list.details;
                    for (let [key, value] of $scope.account.entries()) {
                        value['names'] = value.role_names.join(',')
                    }
                    console.log(response);
                    console.log($scope.account);
                });
            });
        }
    };

    //点击关闭
    $scope.close_num_arr = [];
    $scope.change_model = function () {
        for (let [key, value] of $scope.account.entries()) {
            if (JSON.stringify($scope.account).indexOf('"state":true') === -1) {  //提示请勾选再删除
                $scope.more_modal = 'prompt_modal';
            }
            if (value.state) {  //直接删除
                $scope.more_modal = 'closemore_modal';
                $scope.close_num_arr.push(value.id);
            }
        }
    }

    //批量关闭原因
    let arr = [];
    $scope.changeStar = function (item) {
        arr.push(item);
        $scope.nemArr = arr.join(',');
        console.log($scope.nemArr)
    };
    //点击确定保存原因
    $scope.closeReset = function () {
        $http({
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            },
            method: 'post',
            url: baseUrl + '/mall/user-disable-batch',
            data: {
                user_ids: $scope.nemArr,
                remark: $scope.more
            }
        }).then(function successCallback(response) {
            console.log(response);
            $http({
                method: 'get',
                url: baseUrl + '/mall/user-list',
                params: {
                    status: 1
                }
            }).then(function successCallback(response) {
                $scope.account = response.data.data.user_list.details;
                for (let [key, value] of $scope.account.entries()) {
                    value['names'] = value.role_names.join(',')
                }
                console.log(response);
                console.log($scope.account);
            });
        });
    };


    //正常状态 选择时间排顺序  选择自定义 显示开始结束框

    console.log($scope.search_text);
    $http.get(baseUrl + '/site/time-types').then(function (response) {
        $scope.time = response.data.data.time_types;
        $scope.selectValue = response.data.data.time_types[0];
        $http.get(baseUrl + '/mall/user-list', {
            params: {
                'time_type': $scope.selectValue.value,
                'status': 1
            }
        }).then(function (response) {
            $scope.account = response.data.data.user_list.details;
        }, function (error) {
            console.log(error)
        })
    }, function (error) {
        console.log(error)
    });
    //============监听下拉框值的变化===========
    $scope.$watch('selectValue', function (newVal, oldVal) {
        if (!!newVal) {
            $http.get(baseUrl + '/mall/user-list', {
                params: {
                    'time_type': newVal.value,
                    'status': 1
                }
            }).then(function (response) {
                console.log(response);
                $scope.account = response.data.data.user_list.details;

                /*-----------------------------分页--------------*/
                $scope.history_list = [];
                $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
                console.log($scope.history_all_page);
                let all_num = $scope.history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.history_list.push(i + 1);
                    console.log($scope.history_list)
                }
                //点击数字，跳转到多少页
                $scope.choosePage = function (page) {
                    $scope.page = page;
                    //判断 搜索后的内容 为空分页为最初状态
                    if ($scope.search_text == "") {
                        $http({
                            method: 'get',
                            url: baseUrl + '/mall/user-list',
                            params: {
                                status: 1
                            }
                        }).then(function successCallback(response) {
                            $scope.account = response.data.data.user_list.details;
                            console.log(response);
                        });
                    }
                    //不为空时分页处理
                    else {
                        $http.get(baseUrl + '/mall/user-list', {
                            params: {
                                'time_type': newVal.value,
                                'page': $scope.page,
                                'status': 1,
                                'keyword': $scope.search_text,
                                'start_time': $scope.begin_time,
                                'end_time': $scope.end_time
                            }
                        }).then(function (response) {
                            // console.log(response);
                            $scope.account = response.data.data.user_list.details;
                        })
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
                $scope.Previous = function () {
                    if ($scope.page > 1) {                //当页数大于1时，执行
                        $scope.page--;
                        $scope.choosePage($scope.page);
                    }
                };
                //下一页
                $scope.Next = function () {
                    if ($scope.page < $scope.history_all_page) { //判断是否为最后一页，如果不是，页数+1,
                        $scope.page++;
                        $scope.choosePage($scope.page);
                    }
                }
            }, function (error) {
                console.log(error)
            })
        }
    });
    //监听开始时间
    $scope.$watch('begin_time', function (newVal, oldVal) {
        $scope.page = 1;//默认第一页
        if (newVal != undefined && newVal != '' && $scope.begin_time != undefined && $scope.end_time != undefined) {
            let url = baseUrl + '/mall/user-list';
            $http.get(url, {
                params: {
                    'time_type': 'custom',
                    'start_time': newVal,
                    //'keyword':$scope.search_text,
                    'status': 1,
                    'end_time': $scope.end_time
                }
            }).then(function (response) {
                $scope.history_list = [];
                $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
                let all_num = $scope.history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.history_list.push(i + 1)
                }
                $scope.account = response.data.data.user_list.details;
                console.log(response);
            }, function (err) {
                console.log(err)
            })
        }
    });
    //监听结束时间
    $scope.$watch('end_time', function (newVal, oldVal) {
        $scope.page = 1;//默认第一页
        if (newVal != undefined && newVal != '' && $scope.begin_time != undefined && $scope.end_time != undefined) {
            let url = baseUrl + '/mall/user-list';
            $http.get(url, {
                params: {
                    'time_type': 'custom',
                    //'keyword':$scope.search_text,
                    'status': 1,
                    'start_time': $scope.begin_time,
                    'end_time': newVal
                }
            }).then(function (response) {
                $scope.history_list = [];
                $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
                let all_num = $scope.history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.history_list.push(i + 1)
                }
                $scope.account = response.data.data.user_list.details;
                console.log(response);

            }, function (err) {
                console.log(err)
            })
        }
    });


    //切换关闭的内容  第二部分
    //获取账户管理列表 关闭状态
    $http({
        method: 'get',
        url: baseUrl + '/mall/user-list',
        params: {
            status: 0
        }
    }).then(function successCallback(response) {
        $scope.account_colse = response.data.data.user_list.details;
        for (let [key, value] of $scope.account_colse.entries()) {
            value['names'] = value.role_names.join(',')
        }
        console.log(response);
        console.log($scope.account_colse);
    });

    //获取关闭原因
    $scope.getRemark = function (item) {
        $scope.remark = item.status_remark;
    };

    //单个选择开启
    $scope.getOpen = function (item) {
        console.log(item.id);
        $scope.getColse = function () {
            $http({
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data)
                },
                method: 'POST',
                url: baseUrl + '/mall/user-status-toggle',
                data: {
                    user_id: item.id,
                    remark: item.status_remark
                }
            }).then(function successCallback(response) {
                $http({
                    method: 'get',
                    url: baseUrl + '/mall/user-list',
                    params: {
                        status: 0
                    }
                }).then(function successCallback(response) {
                    $scope.account_colse = response.data.data.user_list.details;
                    for (let [key, value] of $scope.account_colse.entries()) {
                        value['names'] = value.role_names.join(',')
                    }
                    console.log(response);
                    console.log($scope.account_colse);
                });
                console.log(response);
                //console.log($scope.account_colse);
            });
        };
    };

    //批量开启
    //点击关闭
    $scope.open_num_arr = [];
    $scope.change_open_model = function () {
        for (let [key, value] of $scope.account_colse.entries()) {
            if (JSON.stringify($scope.account_colse).indexOf('"state":true') === -1) {  //提示请勾选再删除
                $scope.open_modal = 'sed_modal';
            }
            if (value.state) {  //直接删除
                $scope.open_modal = 'open_modal';
                $scope.open_num_arr.push(value.id);
            }
        }
    };
    //批量关闭原因
    let open_arr = [];
    $scope.changeOpen = function (item) {
        open_arr.push(item);
        $scope.nemArr = open_arr.join(',');
        console.log($scope.nemArr)
    };
    //点击确定保存原因
    $scope.openReset = function () {
        $http({
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            },
            method: 'post',
            url: baseUrl + '/mall/user-enable-batch',
            data: {
                user_ids: $scope.nemArr
            }
        }).then(function successCallback(response) {
            console.log(response);
            $http({
                method: 'get',
                url: baseUrl + '/mall/user-list',
                params: {
                    status: 0
                }
            }).then(function successCallback(response) {
                $scope.account_colse = response.data.data.user_list.details;
                for (let [key, value] of $scope.account.entries()) {
                    value['names'] = value.role_names.join(',')
                }
                console.log(response);
                console.log($scope.account);
            });
        });
    };

    //关闭状态 选择时间排顺序  选择自定义 显示开始结束框
    $http.get(baseUrl + '/site/time-types').then(function (response) {
        $scope.timeClose = response.data.data.time_types;
        $scope.selectValueClose = response.data.data.time_types[0];
        // console.log($scope.selectValue.value)
        $http.get(baseUrl + '/mall/user-list', {
            params: {
                'time_type': $scope.selectValueClose.value,
                'status': 0
            }
        }).then(function (response) {
            $scope.account_colse = response.data.data.user_list.details;
        }, function (error) {
            console.log(error)
        })
    }, function (error) {
        console.log(error)
    });
    //============监听下拉框值的变化===========
    $scope.$watch('selectValueClose', function (newVal, oldVal) {
        if (!!newVal) {
            $http.get(baseUrl + '/mall/user-list', {
                params: {
                    'time_type': newVal.value,
                    'status': 0
                }
            }).then(function (response) {

                console.log(response);
                $scope.account_colse = response.data.data.user_list.details;

                /*-----------------------------分页-----------------------*/
                $scope.history_list_colse = [];
                $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
                //console.log($scope.history_all_page);
                let all_num = $scope.history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.history_list_colse.push(i + 1);
                    //console.log($scope.history_list_colse)
                }
                //点击数字，跳转到多少页
                $scope.choosePageColse = function (page) {
                    $scope.page = page;
                    // $scope.isActivePage=function (now_page) {
                    //console.log($scope.page);
                    // };
                    $http.get(baseUrl + '/mall/user-list', {
                        params: {
                            'time_type': newVal.value,
                            'page': $scope.page,
                            'status': 0,
                            'start_time': $scope.begin_time,
                            'end_time': $scope.end_time
                        }
                    }).then(function (response) {
                        // console.log(res);
                        $scope.account_colse = response.data.data.user_list.details;

                    }, function (err) {
                        console.log(err);
                    });
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
                $scope.Previous = function () {
                    if ($scope.page > 1) {                //当页数大于1时，执行
                        $scope.page--;
                        $scope.choosePageColse($scope.page);
                    }
                };
                //下一页
                $scope.Next = function () {
                    if ($scope.page < $scope.history_all_page) { //判断是否为最后一页，如果不是，页数+1,
                        $scope.page++;
                        $scope.choosePageColse($scope.page);
                    }
                }

            }, function (error) {
                console.log(error)
            })
        }
    });
    //监听开始时间
    $scope.mm = $scope;
    $scope.myng = $scope;
    $scope.$watch('begin_time_more', function (newVal, oldVal) {
        console.log(1111);
        $scope.page = 1;//默认第一页
        if (newVal != undefined && newVal != '' && $scope.begin_time_more != undefined && $scope.end_time_more != undefined) {
            console.log("测试判断");
            let url = baseUrl + '/mall/user-list';
            $http.get(url, {
                params: {
                    'time_type': 'custom',
                    'start_time': newVal,
                    'status': 0,
                    'end_time': $scope.end_time_more
                }
            }).then(function (response) {
                $scope.history_list_colse = [];
                $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
                let all_num = $scope.history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.history_list_colse.push(i + 1)
                }
                $scope.account_colse = response.data.data.user_list.details;
                console.log(response);
            }, function (err) {
                console.log(err)
            })
        }
    });
    //监听结束时间
    $scope.$watch('end_time_more', function (newVal, oldVal) {
        $scope.page = 1;//默认第一页
        if (newVal != undefined && newVal != '' && $scope.begin_time_more != undefined && $scope.end_time_more != undefined) {
            let url = baseUrl + '/mall/user-list';
            $http.get(url, {
                params: {
                    'time_type': 'custom',
                    'status': 0,
                    'start_time': $scope.begin_time_more,
                    'end_time': newVal
                }
            }).then(function (response) {
                $scope.history_list_colse = [];
                $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
                let all_num = $scope.history_all_page;//循环总页数
                for (let i = 0; i < all_num; i++) {
                    $scope.history_list_colse.push(i + 1)
                }
                $scope.account_colse = response.data.data.user_list.details;

                console.log(response);

            }, function (err) {
                console.log(err)
            })
        }
    });


    //关闭状态下 点击搜索
    $scope.getSearchClose = function () {
        console.log($scope.name_num);
        $http({
            method: 'get',
            url: baseUrl + '/mall/user-list',
            params: {
                keyword: $scope.name_num,
                status: 0
            }
        }).then(function successCallback(response) {
            $scope.history_list_colse = [];
            $scope.history_all_page = Math.ceil(response.data.data.user_list.total / 12);//获取总页数
            let all_num = $scope.history_all_page;//循环总页数
            for (let i = 0; i < all_num; i++) {
                $scope.history_list_colse.push(i + 1)
            }
            $scope.account_colse = response.data.data.user_list.details;
            console.log(response);

        });
    };
    //监听搜索的内容为空时，恢复初始状态
    $scope.$watch("name_num", function (newVal, oldVal) {
        console.log(22222);
        if (newVal == "") {
            $http({
                method: 'get',
                url: baseUrl + '/mall/user-list',
                params: {
                    status: 0
                }
            }).then(function successCallback(response) {
                $scope.account_colse = response.data.data.user_list.details;
                for (let [key, value] of $scope.account_colse.entries()) {
                    value['names'] = value.role_names.join(',')
                }
            });
        }
    })
});