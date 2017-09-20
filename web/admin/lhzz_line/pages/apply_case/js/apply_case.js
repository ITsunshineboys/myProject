angular.module('apply_case',[])
        .controller('apply_case_ctrl',function ($scope,$http,$state) {
            //post请求配置
            const config = {
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data)
                }
            };
            //默认登录状态(后期会删除)
            $http.post('/site/login', {
                'username': 13551201821,
                'password': 'demo123'
            }, config).then(function (response) {
                console.log(response)
            }, function (error) {
                console.log(error)
            })
            $scope.ctrlScope = $scope
            $scope.keyword = ''
            $scope.start_time = ''
            $scope.end_time = ''
            $scope.time_type = [
                {name:'全部时间',str:'all'},
                {name:'今天',str:'today'},
                {name:'本周',str:'week'},
                {name:'本月',str:'month'},
                {name:'本年',str:'year'},
                {name:'自定义',str:'custom'}
            ]
            $scope.cur_time_type = $scope.time_type[0]
            //获取样板间列表
            //修改全部时间
            $scope.$watch('cur_time_type',function (newVal,oldVal) {
                // $scope.keyword = ''
                if(newVal.str!='custom'){
                    $http.get('/effect/effect-list',{
                        params:{
                            time_type:$scope.cur_time_type.str
                        }
                    }).then(function (response) {
                        console.log(response)
                        $scope.cur_today_apply = response.data.data.today_apply
                        $scope.cur_today_earnest = response.data.data.today_earnest
                        $scope.cur_all_apply = response.data.data.all_apply
                        $scope.cur_all_earnest = response.data.data.all_earnest
                        $scope.apply_list = response.data.data['0'].list
                        $scope.cur_page = response.data.data['0'].page
                    },function (error) {
                        console.log(error)
                    })
                }else{
                   $scope.start_time = ''
                   $scope.end_time = ''
                }
            })
            //开始时间修改
            $scope.$watch('start_time',function (newVal,oldVal) {
                console.log(newVal)
                let obj = ''
                    if($scope.end_time == ''){
                        obj = {
                            time_type:'custom',
                            start_time:newVal
                        }
                    }else{
                        if(new Date(newVal).getTime()<new Date($scope.end_time).getTime()) {
                            obj = {
                                time_type: 'custom',
                                start_time: newVal,
                                end_time: $scope.end_time
                            }
                        }
                    }
                    console.log(obj)
                    if(obj!=''){
                    $http.get('/effect/effect-list',{
                        params:obj
                    }).then(function (response) {
                        console.log(response)
                        $scope.cur_today_apply = response.data.data.today_apply
                        $scope.cur_today_earnest = response.data.data.today_earnest
                        $scope.cur_all_apply = response.data.data.all_apply
                        $scope.cur_all_earnest = response.data.data.all_earnest
                        $scope.apply_list = response.data.data['0'].list
                        $scope.cur_page = response.data.data['0'].page
                    },function (error) {
                        console.log(error)
                    })
                }
            })
            //结束时间修改
            $scope.$watch('end_time',function (newVal,oldVal) {
                console.log(newVal)
                let obj = ''
                    if($scope.start_time == ''){
                         obj = {
                            time_type:'custom',
                            end_time:newVal
                        }
                    }else {
                        if (new Date(newVal).getTime() > new Date($scope.start_time).getTime()) {
                             obj = {
                                time_type: 'custom',
                                start_time: $scope.start_time,
                                end_time: newVal
                            }
                        }
                    }
                    console.log(obj)
                    if(obj!=''){
                    $http.get('/effect/effect-list',{
                        params:obj
                    }).then(function (response) {
                        console.log(response)
                        $scope.cur_today_apply = response.data.data.today_apply
                        $scope.cur_today_earnest = response.data.data.today_earnest
                        $scope.cur_all_apply = response.data.data.all_apply
                        $scope.cur_all_earnest = response.data.data.all_earnest
                        $scope.apply_list = response.data.data['0'].list
                        $scope.cur_page = response.data.data['0'].page
                    },function (error) {
                        console.log(error)
                    })
                }
            })
            //修改样板间备注
            //弹出备注模态框
            $scope.replace_remarks = function (item) {
                $scope.cur_item = angular.copy(item)
            }
            //修改备注请求
            $scope.edit_remarks = function () {
                console.log($scope.cur_item)
                $http.post('/effect/edit-remark',{
                    id:$scope.cur_item.id,
                    remark:$scope.cur_item.remark
                },config).then(function (response) {
                    console.log(response)
                    $http.get('/effect/effect-list',{
                        params:{
                            time_type:$scope.cur_time_type.str,
                            page:$scope.cur_page
                        }
                    }).then(function (response) {
                        console.log(response)
                        $scope.cur_today_apply = response.data.data.today_apply
                        $scope.cur_today_earnest = response.data.data.today_earnest
                        $scope.cur_all_apply = response.data.data.all_apply
                        $scope.cur_all_earnest = response.data.data.all_earnest
                        $scope.apply_list = response.data.data['0'].list
                    },function (error) {
                        console.log(error)
                    })
                },function (error) {
                    console.log(error)
                })
            }
            //搜索手机号或姓名
            $scope.update_keyword = function () {
                // $scope.cur_time_type = $scope.time_type[0]
                $http.get('/effect/effect-list',{
                    params:{
                        keyword:$scope.keyword
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.cur_today_apply = response.data.data.today_apply
                    $scope.cur_today_earnest = response.data.data.today_earnest
                    $scope.cur_all_apply = response.data.data.all_apply
                    $scope.cur_all_earnest = response.data.data.all_earnest
                    $scope.apply_list = response.data.data['0'].list
                    $scope.cur_page = response.data.data['0'].page
                },function (error) {
                    console.log(error)
                })
            }
            //查看详情
            //获取详情
            $scope.get_detail = function (item) {
                $scope.cur_item = item
                $http.post('/effect/effect-view',{
                        id:item.id
                },config).then(function (response) {
                    console.log(response)
                    $scope.case_detail = response.data.data
                    $state.go('apply_case.case_detail')
                },function (error) {
                    console.log(error)
                })
            }
            //保存备注
            $scope.save_remark = function () {
                $http.post('/effect/effect-view',{
                    id:$scope.cur_item.id,
                    remark:$scope.case_detail.remark
                },config).then(function (response) {
                    console.log(response)
                    $http.get('/effect/effect-list',{
                        params:{
                            time_type:$scope.cur_time_type.str,
                            page:$scope.cur_page
                        }
                    }).then(function (response) {
                        console.log(response)
                        $scope.cur_today_apply = response.data.data.today_apply
                        $scope.cur_today_earnest = response.data.data.today_earnest
                        $scope.cur_all_apply = response.data.data.all_apply
                        $scope.cur_all_earnest = response.data.data.all_earnest
                        $scope.apply_list = response.data.data['0'].list
                        $scope.second_title =  '详情'
                        $state.go('apply_case.index')
                    },function (error) {
                        console.log(error)
                    })
                },function (error) {
                    console.log(error)
                })
            }
            //返回前页
            $scope.go_index = function () {
                $scope.second_title =  ''
                $state.go('apply_case.index')
            }
        })