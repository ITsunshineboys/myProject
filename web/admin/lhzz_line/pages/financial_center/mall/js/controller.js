angular.module('mall_finance', [])
    .controller('mall_finance_ctrl', function ($scope, $http, $state,$uibModal) {
        $scope.second_title = '商城财务'
        $scope.three_title = ''
        $scope.four_title = ''
        $scope.five_title = ''
        $scope.six_title = ''
        $scope.ctrlScope = $scope
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
        $scope.time_type = [
            {name: '全部时间', str: 'all'},
            {name: '今天', str: 'today'},
            {name: '本周', str: 'week'},
            {name: '本月', str: 'month'},
            {name: '本年', str: 'year'},
            {name: '自定义', str: 'custom'}
        ]
        $scope.cur_time_type = $scope.time_type[0]
        //处理方式
        $scope.deal_style = [
            {name:'提现',num:2},
            {name:'驳回',num:3}
        ]
        $scope.cur_deal_style = $scope.deal_style[0]
        //提现状态
        $scope.withdraw_status = [
            {name: '全部', num: 0},
            {name: '提现中', num: 1},
            {name: '已提现', num: 2},
            {name: '驳回', num: 3}
        ]
        $scope.cur_withdraw_status = $scope.withdraw_status[0]
        //店铺类型
        $scope.shop_type = [
            {name: '全部', num: -1},
            {name: '旗舰店', num: 0},
            {name: '专卖店', num: 1},
            {name: '专营店', num: 2},
            {name: '自营店', num: 3}
        ]
        $scope.cur_shop_type = $scope.shop_type[0]
        //店铺状态
        $scope.shop_status = [
            {name: '全部', num: -1},
            {name: '正常营业', num: 1},
            {name: '已闭店', num: 0}
        ]
        $scope.cur_shop_status = $scope.shop_status[0]
        //账户管理初始化
        $scope.cur_first_level = ''//当前一级初始化
        $scope.cur_second_level = ''//当前二级初始化
        $scope.cur_third_level = ''//当前三级初始化
        $scope.keyword = ''//初始化
        $scope.overrun = false//冻结金额是否超额
        $scope.cur_freeze_money = ''//当前冻结金额
        $scope.cur_freeze_remark = ''//当前冻结理由
        $scope.cur_index = 1//控制当前start_time和end_time控制的页面 1为提现管理 2为冻结 3为提现
        //商城财务主页请求
        $http.get('/supplier-cash/cash-index').then(function (res) {
            console.log(res)
            $scope.all_cash_data = res.data.data
        }, function (error) {
            console.log(error)
        })
        //跳转商家提现管理
        $scope.go_withdraw = function (num) {
            $scope.three_title = '商家提现管理'
            $scope.four_title = ''
            $scope.five_title = ''
            $scope.six_title = ''
            $scope.cur_index = 1
            if(num!=3){
                if(num==1){
                    for(let [key,value] of $scope.time_type.entries()){
                        if(value.name == '今天'){
                            $scope.cur_time_type = value
                        }
                    }
                }else{
                    for(let [key,value] of $scope.time_type.entries()){
                        if(value.name == '全部时间'){
                            $scope.cur_time_type = value
                        }
                    }
                }
                for(let [key,value] of $scope.withdraw_status.entries()){
                    if(value.name == '已提现'){
                        $scope.cur_withdraw_status = value
                    }
                }
            }else{
                for(let [key,value] of $scope.withdraw_status.entries()){
                    if(value.name == '提现中'){
                        $scope.cur_withdraw_status = value
                    }
                }
                for(let [key,value] of $scope.time_type.entries()){
                    if(value.name == '全部时间'){
                        $scope.cur_time_type = value
                    }
                }
            }
            $http.post('/supplier-cash/cash-list-today', {
                time_type:$scope.cur_time_type.str,
                status:$scope.cur_withdraw_status.num
            }, config).then(function (res) {
                console.log(res)
                $scope.shop_withdraw_list = res.data.data.list
                $state.go('mall_finance.withdraw')
            }, function (error) {
                console.log(error)
            })
        }
        //提现管理改变监听
        $scope.$watch('cur_time_type',function (newVal,oldVal) {
            if(newVal.str!='custom'){
                if($scope.cur_index == 1){
                    $http.post('/supplier-cash/cash-list-today', {
                        time_type:newVal.str,
                        status:$scope.cur_withdraw_status.num
                    }, config).then(function (res) {
                        console.log(res)
                        $scope.shop_withdraw_list = res.data.data.list
                    }, function (error) {
                        console.log(error)
                    })
                }else if($scope.cur_index == 2){
                    $http.get('/supplieraccount/freeze-list',{
                        params:{
                            supplier_id:$scope.cur_account_detail.id,
                            time_type:newVal.str
                        }
                    }).then(function (res) {
                        console.log(res)
                    },function (error) {
                        console.log(error)
                    })
                }else if($scope.cur_index == 3){
                    $http.get('/supplieraccount/cashed-list',{
                        params:{
                            supplier_id:$scope.cur_account_detail.id,
                            time_type:newVal.str
                        }
                    }).then(function (res) {
                        console.log(res)
                        $state.go('mall_finance.withdraw_list')
                    },function (error) {
                        console.log(error)
                    })
                }else{
                    $http.post('/supplier-cash/order-list-today',{
                        time_type:newVal.str
                    }).then(function (res) {
                        console.log(res)
                    },function (error) {
                        console.log(error)
                    })
                }
            }else{
                $scope.start_time = ''
                $scope.end_time = ''
            }
        })
        //自定义开始时间监听
        $scope.$watch('start_time',function (newVal,oldVal) {
            let obj = ''
            if(oldVal!=undefined){
                if($scope.cur_index == 1){
                    if($scope.end_time == ''){
                        obj = {
                            time_type:'custom',
                            [$scope.cur_index == 1?'time_start':'start_time']:newVal,
                            status:$scope.cur_withdraw_status.num
                        }
                    }else{
                        if(new Date(newVal).getTime()>new Date($scope.start_time).getTime()) {
                            obj = {
                                time_type: 'custom',
                                [$scope.cur_index == 1 ? 'time_start' : 'start_time']: newVal,
                                [$scope.cur_index == 1 ? 'time_end' : 'end_time']: $scope.end_time,
                                status: $scope.cur_withdraw_status.num
                            }
                        }
                    }
                }else if($scope.cur_index==2||$scope.cur_index==3){
                    if($scope.end_time == ''){
                        obj = {
                            time_type:'custom',
                            [$scope.cur_index == 1?'time_start':'start_time']:newVal,
                            supplier_id:$scope.cur_account_detail.id
                        }
                    }else{
                        if(new Date(newVal).getTime()>new Date($scope.start_time).getTime()) {
                            obj = {
                                time_type: 'custom',
                                [$scope.cur_index == 1 ? 'time_start' : 'start_time']: newVal,
                                [$scope.cur_index == 1 ? 'time_end' : 'end_time']: $scope.end_time,
                                supplier_id: $scope.cur_account_detail.id
                            }
                        }
                    }
                }else{
                    if($scope.end_time == ''){
                        obj = {
                            time_type:'custom',
                            [$scope.cur_index == 1?'time_start':'start_time']:newVal
                        }
                    }else{
                        if(new Date(newVal).getTime()>new Date($scope.start_time).getTime()) {
                            obj = {
                                time_type: 'custom',
                                [$scope.cur_index == 1 ? 'time_start' : 'start_time']: newVal,
                                [$scope.cur_index == 1 ? 'time_end' : 'end_time']: $scope.end_time
                            }
                        }
                    }
                }
            }
            if(obj!=''){
                if($scope.cur_index == 1){
                    $http.post('/supplier-cash/cash-list-today',obj,config).then(function (res) {
                        console.log(res)
                        $scope.shop_withdraw_list = res.data.data.list
                    },function (error) {
                        console.log(error)
                    })
                } else if($scope.cur_index == 2){
                    $http.get('/supplieraccount/freeze-list',{
                        params:obj
                    }).then(function (res) {
                        console.log(res)
                    },function (error) {
                        console.log(error)
                    })
                }else if($scope.cur_index==3){
                    $http.get('/supplieraccount/cashed-list',{
                        params:obj
                    }).then(function (res) {
                        console.log(res)
                        // $state.go('mall_finance.withdraw_list')
                    },function (error) {
                        console.log(error)
                    })
                }else{
                    $http.post('/supplier-cash/order-list-today',obj,config).then(function (res) {
                        console.log(res)
                    },function (error) {
                        console.log(error)
                    })
                }
            }
        })
        //自定义结束时间监听
        $scope.$watch('end_time',function (newVal,oldVal) {
            let obj = ''
            if(oldVal!=undefined){
                if($scope.cur_index == 1){
                    if($scope.start_time == ''){
                        obj = {
                            time_type:'custom',
                            [$scope.cur_index == 1?'time_end':'end_time']:newVal,
                            status:$scope.cur_withdraw_status.num
                        }
                    }else{
                        if(new Date(newVal).getTime()>new Date($scope.start_time).getTime()) {
                            obj = {
                                time_type: 'custom',
                                [$scope.cur_index == 1 ? 'time_end' : 'end_time']: newVal,
                                [$scope.cur_index == 1 ? 'time_start' : 'start_time']: $scope.start_time,
                                status: $scope.cur_withdraw_status.num
                            }
                        }
                    }
                }else if($scope.cur_index == 2 || $scope.cur_index == 3){
                    if($scope.end_time == ''){
                        obj = {
                            time_type:'custom',
                            [$scope.cur_index == 1?'time_end':'end_time']:newVal,
                            supplier_id:$scope.cur_account_detail.id
                        }
                    }else{
                        if(new Date(newVal).getTime()>new Date($scope.start_time).getTime()) {
                            obj = {
                                time_type: 'custom',
                                [$scope.cur_index == 1 ? 'time_end' : 'end_time']: newVal,
                                [$scope.cur_index == 1 ? 'time_start' : 'start_time']: $scope.start_time,
                                supplier_id: $scope.cur_account_detail.id
                            }
                        }
                    }
                }else{
                    if($scope.end_time == ''){
                        obj = {
                            time_type:'custom',
                            [$scope.cur_index == 1?'time_end':'end_time']:newVal,
                        }
                    }else{
                        if(new Date(newVal).getTime()>new Date($scope.start_time).getTime()) {
                            obj = {
                                time_type: 'custom',
                                [$scope.cur_index == 1 ? 'time_end' : 'end_time']: newVal,
                                [$scope.cur_index == 1 ? 'time_start' : 'start_time']: $scope.start_time,
                            }
                        }
                    }
                }
            }
            if(obj!=''){
                if($scope.cur_index == 1){
                    $http.post('/supplier-cash/cash-list-today',obj,config).then(function (res) {
                        console.log(res)
                        $scope.shop_withdraw_list = res.data.data.list
                    },function (error) {
                        console.log(error)
                    })
                } else if($scope.cur_index == 2){
                    $http.get('/supplieraccount/freeze-list',{
                        params:obj
                    }).then(function (res) {
                        console.log(res)
                    },function (error) {
                        console.log(error)
                    })
                }else if($scope.cur_index == 3){
                    $http.get('/supplieraccount/cashed-list',{
                        params:obj
                    }).then(function (res) {
                        console.log(res)
                        // $state.go('mall_finance.withdraw_list')
                    },function (error) {
                        console.log(error)
                    })
                }else{
                    $http.post('/supplier-cash/order-list-today',obj,config).then(function (res) {
                        console.log(res)
                    },function (error) {
                        console.log(error)
                    })
                }
            }
        })
        //提现状态监听
        $scope.$watch('cur_withdraw_status',function (newVal,oldVal) {
            let obj = ''
            if(oldVal!=undefined){
                if($scope.cur_time_type.str=='custom'){
                    if($scope.start_time!=''){
                        if($scope.end_time!=''){
                            obj={
                                time_type:'custom',
                                time_start:$scope.start_time,
                                end_time:$scope.end_time,
                                status:newVal.num
                            }
                        }else{
                            obj={
                                time_type:'custom',
                                time_start:$scope.start_time,
                                status:newVal.num
                            }
                        }
                    }else{
                        if($scope.end_time!=''){
                            obj={
                                time_type:'custom',
                                end_time:$scope.end_time,
                                status:newVal.num
                            }
                        }
                    }
                }else{
                    obj={
                        time_type:$scope.cur_time_type.str,
                        status:newVal.num
                    }
                }
                if(obj!=''){
                    $http.post('/supplier-cash/cash-list-today',obj,config).then(function (res) {
                        console.log(res)
                        $scope.shop_withdraw_list = res.data.data.list
                    },function (error) {
                        console.log(error)
                    })
                }
            }
        })
        //跳转商家提现管理详情
        $scope.go_withdraw_manageDetail = function (item) {
            console.log(item)
            $scope.cur_status = item.status
            $scope.cur_account_money = ''
            $scope.withdraw_remark = ''
            $scope.three_title = '商家提现管理'
            $scope.four_title = '商家提现管理详情'
            $scope.cur_deal_style = $scope.deal_style[0]
            $scope.five_title = ''
            $scope.six_title = ''
            $http.get('/supplier-cash/cash-action-detail',{
                params:{
                    transaction_no:item.transaction_no
                }
            }).then(function (res) {
                console.log(res)
                $scope.all_withdraw_detail = res.data.data
                $state.go('mall_finance.withdraw_manage_detail')
            },function (error) {
                console.log(error)
            })
        }
        //监听提现金额
        $scope.$watch('cur_account_money',function (newVal,oldVal) {
            if($scope.all_withdraw_detail!=undefined && oldVal!=undefined){
                if(+newVal > +$scope.all_withdraw_detail.cash_money){
                    $scope.cur_account_money = $scope.all_withdraw_detail.cash_money
                }
            }
        })
        //保存商家提现管理
        $scope.save_withdraw_detail = function (valid) {
            let data = '',str = '',data1=''
            if(+$scope.cur_deal_style.num == 2){
                data = {
                    cash_id:$scope.all_withdraw_detail.id,
                    status:2,
                    reason:$scope.withdraw_remark,
                    real_money:$scope.cur_account_money
                }
            }else{
                data = {
                    cash_id:$scope.all_withdraw_detail.id,
                    status:3,
                    reason:$scope.withdraw_remark
                }
            }
            if($scope.cur_time_type.str == 'custom'){
                if($scope.start_time!=''&&$scope.end_time!=''){
                    data1 = {
                        time_type:$scope.cur_time_type.str,
                        status:$scope.cur_withdraw_status.num,
                        time_start:$scope.start_time,
                        time_end:$socpe.end_time
                    }
                }else if($scope.start_time!=''&&$scope.end_time==''){
                    data1 = {
                        time_type:$scope.cur_time_type.str,
                        status:$scope.cur_withdraw_status.num,
                        time_start:$scope.start_time,
                    }
                }else if($scope.start_time==''&&$scope.end_time!=''){
                    data1 = {
                        time_type:$scope.cur_time_type.str,
                        status:$scope.cur_withdraw_status.num,
                        time_end:$socpe.end_time
                    }
                }
            }else{
                data1 = {
                    time_type:$scope.cur_time_type.str,
                    status:$scope.cur_withdraw_status.num,
                }
            }
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '提交成功'

                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $http.post('/supplier-cash/cash-list-today',data1, config).then(function (res) {
                        console.log(res)
                        str.shop_withdraw_list = res.data.data.list
                        $state.go('mall_finance.withdraw')
                    }, function (error) {
                        console.log(error)
                    })
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if(valid){
                $http.post('/supplier-cash/cash-deal',data,config).then(function (res) {
                    console.log(res)
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                },function (error) {
                    console.log(error)
                })
            }
        }
        //跳转账户管理
        $scope.go_account = function () {
            $scope.three_title = '账户管理'
            $scope.four_title = ''
            $scope.five_title = ''
            $scope.six_title = ''
            $scope.keyword = ''
            //请求一级分类
            $http.get('/supplieraccount/category', {
                params: {
                    pid: 0
                }
            }).then(function (res) {
                console.log(res)
                $scope.first_level = res.data.data
                $scope.first_level.unshift({id: "0", title: "全部"})
                $scope.cur_first_level = $scope.first_level[0]
                console.log($scope.cur_first_level)
                $http.get('/supplieraccount/account-list', {
                    params: {
                        category_id: $scope.cur_first_level.id,
                        type_shop: $scope.cur_shop_type.num,
                        status: $scope.cur_shop_status.num
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.account_list = res.data.data.list
                    $state.go('mall_finance.account')
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
        }
        //改变一级获取二级
        $scope.$watch('cur_first_level', function (newVal, oldVal) {
            $scope.cur_category_id = newVal.id
            if(oldVal!=''){
                $http.get('/supplieraccount/account-list', {
                    params: {
                        category_id: newVal.id,
                        type_shop: $scope.cur_shop_type.num,
                        status: $scope.cur_shop_status.num
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.account_list = res.data.data.list
                }, function (error) {
                    console.log(error)
                })
            }
            if (oldVal != '' && newVal.title != '全部') {
                $http.get('/supplieraccount/category', {
                    params: {
                        pid: newVal.id
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.second_level = res.data.data
                    $scope.second_level.unshift({'id': newVal.id, title: '全部'})
                    $scope.cur_second_level = $scope.second_level[0]
                }, function (error) {
                    console.log(error)
                })
            }
            if (newVal.title == '全部') {
                $scope.cur_second_level = ''
                $scope.cur_third_level = ''
            }
        })
        //改变二级获取三级
        $scope.$watch('cur_second_level', function (newVal, oldVal) {
            $scope.cur_category_id = newVal.id
            if(oldVal!=''){
                $http.get('/supplieraccount/account-list', {
                    params: {
                        category_id: newVal.id,
                        type_shop: $scope.cur_shop_type.num,
                        status: $scope.cur_shop_status.num
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.account_list = res.data.data.list
                }, function (error) {
                    console.log(error)
                })
            }
            if (oldVal != '' && newVal.title != '全部') {
                $http.get('/supplieraccount/category', {
                    params: {
                        pid: newVal.id
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.third_level = res.data.data
                    $scope.third_level.unshift({'id': newVal.id, title: '全部'})
                    $scope.cur_third_level = $scope.third_level[0]
                }, function (error) {
                    console.log(error)
                })
            }
            if (newVal.title == '全部') {
                $scope.cur_third_level = ''
            }
        })
        //改变三级
        $scope.$watch('cur_third_level', function (newVal, oldVal) {
            $scope.cur_category_id = newVal.id
            if(oldVal!=''){
                $http.get('/supplieraccount/account-list', {
                    params: {
                        category_id: newVal.id,
                        type_shop: $scope.cur_shop_type.num,
                        status: $scope.cur_shop_status.num
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.account_list = res.data.data.list
                }, function (error) {
                    console.log(error)
                })
            }
        })
        // 改变店铺类型
        $scope.$watch('cur_shop_type',function (newVal,oldVal) {
            if(oldVal!=''){
                $http.get('/supplieraccount/account-list', {
                    params: {
                        category_id: $scope.cur_category_id,
                        type_shop: $scope.cur_shop_type.num,
                        status: $scope.cur_shop_status.num
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.account_list = res.data.data.list
                }, function (error) {
                    console.log(error)
                })
            }
        })
        // 改变店铺状态
        $scope.$watch('cur_shop_status',function (newVal,oldVal) {
            if(oldVal!=0){
                $http.get('/supplieraccount/account-list', {
                    params: {
                        category_id: $scope.cur_category_id,
                        type_shop: $scope.cur_shop_type.num,
                        status: $scope.cur_shop_status.num
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.account_list = res.data.data.list
                }, function (error) {
                    console.log(error)
                })
            }
        })
        //关键词搜索
        $scope.get_list = function () {
            if($scope.keyword!=''){
                $http.get('/supplieraccount/account-list', {
                    params: {
                        keyword:$scope.keyword
                    }
                }).then(function (res) {
                    console.log(res)
                    $scope.account_list = res.data.data.list
                }, function (error) {
                    console.log(error)
                })
            }
        }
        //跳转账户详情页
        $scope.go_account_detail = function (item) {
            $scope.three_title = '账户管理'
            $scope.four_title = '详情'
            $scope.five_title = ''
            $scope.six_title = ''
            $http.get('/supplieraccount/account-view',{
                params:{
                    id:item.id
                }
            }).then(function (res) {
                console.log(res)
                $scope.cur_account_detail = res.data.data
                $state.go('mall_finance.account_detail')
            },function (error) {
                console.log(error)
            })
        }
        // 跳转冻结金额
        $scope.go_freeze_money = function () {
            $scope.three_title = '账户管理'
            $scope.four_title = '详情'
            $scope.five_title = '冻结金额'
            $scope.six_title = ''
            $state.go('mall_finance.freeze_money')
        }
        //监听冻结金额
        $scope.$watch('cur_freeze_money',function (newVal,oldVal) {
            if(!!newVal){
                if(+newVal>$scope.cur_account_detail.cashwithdrawal_money){
                    $scope.overrun = true
                }else{
                    $scope.overrun = false
                }
            }
        })
        //保存冻结金额
        $scope.save_freeze_money = function (valid) {
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '冻结成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $state.go('mall_finance.account_detail')
                }
            }
            let obj = {}
            if($scope.cur_freeze_remark == ''){
                obj = {
                    freeze_money:$scope.cur_freeze_money,
                    supplier_id:$scope.cur_account_detail.id
                }
            }else{
                obj = {
                    freeze_money:$scope.cur_freeze_money,
                    freeze_reason:$scope.cur_freeze_remark,
                    supplier_id:$scope.cur_account_detail.id
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if(valid&&!$scope.overrun){
                $http.post('/supplieraccount/apply-freeze',obj,config).then(function (res) {
                    console.log(res)
                    $http.get('/supplieraccount/account-view',{
                        params:{
                            id:$scope.cur_account_detail.id
                        }
                    }).then(function (res) {
                        console.log(res)
                        $scope.cur_account_detail = res.data.data
                        $scope.three_title = '账户管理'
                        $scope.four_title = '详情'
                        $scope.five_title = ''
                        $scope.six_title = ''
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    },function (error) {
                        console.log(error)
                    })
                },function (error) {
                    console.log(error)
                })
            }else{
                $scope.submitted = true
            }
        }
        //跳转账户管理提现列表
        $scope.go_withdraw_list = function () {
            $scope.cur_index == 3
            $scope.three_title = '账户管理'
            $scope.four_title = '详情'
            $scope.five_title = '提现列表'
            $scope.six_title = ''
            $scope.cur_time_type = $scope.time_type[0]
            $http.get('/supplieraccount/cashed-list',{
                params:{
                    supplier_id:$scope.cur_account_detail.id
                }
            }).then(function (res) {
                console.log(res)
                $state.go('mall_finance.withdraw_list')
            },function (error) {
                console.log(error)
            })
        }
        //跳转冻结金额列表
        $scope.go_freeze_list = function () {
            $scope.cur_index == 2
            $scope.cur_time_type = $scope.time_type[0]
            $http.get('/supplieraccount/freeze-list',{
                params:{
                    supplier_id:$scope.cur_account_detail.id
                }
            }).then(function (res) {
                console.log(res)
                $scope.freeze_list = res.data.data.list
                $state.go('mall_finance.freeze_list')
            },function (error) {
                console.log(error)
            })
        }
        //查看冻结原因
        $scope.show_freeze_reason = function (item) {
            $scope.freeze_reason = item.freeze_reason
        }
        //解冻
        $scope.remove_freeze = function (item) {
            let cur = $scope
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '是否确认解冻？'
                $scope.save_freeze = function () {
                    $http.get('/supplieraccount/account-thaw',{
                        params:{
                            freeze_id:item.id
                        }
                    }).then(function (res) {
                        console.log(res)
                        $http.get('/supplieraccount/freeze-list',{
                            params:{
                                supplier_id:cur.cur_account_detail.id
                            }
                        }).then(function (res) {
                            console.log(res)
                            cur.freeze_list = res.data.data.list
                            $uibModalInstance.close()
                        },function (error) {
                            console.log(error)
                        })
                    },function (error) {
                        console.log(error)
                    })
                }
                $scope.cancel_freeze = function () {
                    $uibModalInstance.close()
                }
            }
            $uibModal.open({
                templateUrl: 'pages/financial_center/mall/cur_modal.html',
                controller: all_modal
            })
        }
        //跳转提现详情页
        $scope.go_withdraw_detail = function (item) {
            $scope.three_title = '账户管理'
            $scope.four_title = '详情'
            $scope.five_title = '提现列表'
            $scope.six_title='详情'
            $state.go('mall_finance.withdraw_detail')
        }
        //跳转入账列表
        $scope.go_recorded_detail = function () {
            $scope.three_title = '入账详情'
            $scope.four_title = ''
            $scope.five_title = ''
            $scope.six_title=''
            $scope.cur_index = 0
            $scope.keyword = ''
            // $scope.cur_time_type = $scope.time_type[0]
            $http.get('/supplier-cash/order-list-today',{
                params:{
                    // time_type:$scope.time_type[1].str
                }
            }).then(function (res) {
                console.log(res)
                $scope.recorded_list = res.data.data.list
                $state.go('mall_finance.recorded_detail')
            },function (error) {
                console.log(error)
            })
        }
        //入账列表搜索
        $scope.get_recorded_list = function () {
            if($scope.keyword!=''){
                $http.post('/supplier-cash/order-list-today',{
                    keyword:$scope.keyword
                },config).then(function (res) {
                    console.log(res)
                },function (error) {
                    console.log(error)
                })
            }
        }
    })
