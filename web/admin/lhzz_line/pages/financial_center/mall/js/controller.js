angular.module('mall_finance', ['ui.bootstrap'])
    .controller('mall_finance_ctrl', function ($scope, $http, $state,$uibModal,$location) {
        $scope.second_title = '商城财务'
        $scope.three_title = ''
        $scope.four_title = ''
        $scope.five_title = ''
        $scope.six_title = ''
        $scope.ctrlScope = $scope
        $scope.keyword = ''
        $scope.keyword1 = ''
        $scope.keyword2 = ''
        //提现部分
        /*分页配置*/
        $scope.Config = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages();
            }
        }
        let tablePages=function () {
            $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
            $http.get('/supplier-cash/cash-list-today',{
                params:$scope.params
            }).then(function (res) {
                console.log(res);
                $scope.shop_withdraw_list = res.data.data.list
                $scope.Config.totalItems = res.data.data.count;
            },function (err) {
                console.log(err);
            })
        };
        $scope.params = {
            time_type:'all',
            status:2,
            time_start:'',
            time_end:'',
            search:''
        };
        $scope.getWithdraw = function () {
            $scope.Config.currentPage = 1
            $scope.params.search = ''
            $scope.keyword = ''
            if($scope.params.time_type == 'custom'){
                if($scope.params.time_start!=''||$scope.params.time_end!=''){
                    tablePages()
                }
            }else{
                tablePages()
            }
        }
        //入账详情
        /*分页配置*/
        $scope.Config1 = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages1();
            }
        }
        let tablePages1=function () {
            $scope.params1.page=$scope.Config1.currentPage;//点击页数，传对应的参数
            $http.get('/supplier-cash/order-list-today',{
               params:$scope.params1
            }).then(function (res) {
                console.log(res);
                $scope.recorded_list = res.data.data.list
                $scope.Config1.totalItems = res.data.data.count
            },function (err) {
                console.log(err);
            })
        };
        $scope.params1 = {
            time_type:'today',
            time_start:'',
            time_end:'',
            search:''
        };
        $scope.getRecord = function () {
            $scope.Config1.currentPage = 1
            $scope.params1.search = ''
            $scope.keyword1 = ''
            if($scope.params1.time_type == 'custom'){
                if($scope.params1.time_start!=''||$scope.params1.time_end!=''){
                    tablePages1()
                }
            }else{
                tablePages1()
            }
        }
        //账户管理
        /*分页配置*/
        $scope.Config2 = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages2();
            }
        }
        let tablePages2=function () {
            $scope.params2.page=$scope.Config2.currentPage;//点击页数，传对应的参数
            $http.get('/supplieraccount/account-list',{
               params:$scope.params2
            }).then(function (res) {
                console.log(res);
                $scope.account_list = res.data.data.list
                $scope.Config2.totalItems = res.data.data.count
            },function (err) {
                console.log(err);
            })
        };
        $scope.params2 = {
            category_id:0,
            type_shop:-1,
            status:-1,
            keyword:''
        };
        $scope.getCategory = function (num) {
            $scope.Config2.currentPage = 1
            $scope.params2.keyword = ''
            $scope.keyword2 = ''
            if(num == 1){
                $scope.params2.category_id = $scope.cur_first_level
                if($scope.cur_first_level!= 0){
                    $http.get('/supplieraccount/category', {
                        params: {
                            pid: $scope.cur_first_level
                        }
                    }).then(function (res) {
                        console.log(res)
                        $scope.second_level = res.data.data
                        $scope.second_level.unshift({id: $scope.cur_first_level, title: "全部"})
                        $scope.cur_second_level = $scope.second_level[0].id
                    }, function (error) {
                        console.log(error)
                    })
                    $scope.third_level = []
                }else{
                    $scope.second_level = []
                    $scope.third_level = []
                }
            }else if(num == 2){
                $scope.params2.category_id = $scope.cur_second_level
                if($scope.cur_second_level!=$scope.second_level[0].id){
                    $http.get('/supplieraccount/category', {
                        params: {
                            pid: $scope.cur_second_level
                        }
                    }).then(function (res) {
                        console.log(res)
                        $scope.third_level = res.data.data
                        $scope.third_level.unshift({id: $scope.cur_second_level, title: "全部"})
                        $scope.cur_third_level = $scope.third_level[0].id
                    }, function (error) {
                        console.log(error)
                    })
                }else{
                    $scope.third_level = []
                }
            }else if(num == 3){
                $scope.params2.category_id = $scope.cur_third_level
            }
            tablePages2()
        }
        //冻结金额列表
        /*分页配置*/
        $scope.Config3 = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages3();
            }
        }
        let tablePages3=function () {
            $scope.params3.page=$scope.Config3.currentPage;//点击页数，传对应的参数
            $http.get('/supplieraccount/freeze-list',{
                params:$scope.params3
            }).then(function (res) {
                console.log(res);
                $scope.freeze_list = res.data.data.list
                $scope.Config3.totalItems = res.data.data.count
            },function (err) {
                console.log(err);
            })
        };
        $scope.params3 = {
            time_type:'all',
            start_time:'',
            end_time:'',
            supplier_id:''
        };
        $scope.getFreeze = function () {
            $scope.Config3.currentPage = 1
            if($scope.params3.time_type == 'custom'){
                if($scope.params3.start_time!=''||$scope.params3.end_time!=''){
                    tablePages3()
                }
            }else{
                tablePages3()
            }
        }
        //提现金额列表
        /*分页配置*/
        $scope.Config4 = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages4();
            }
        }
        let tablePages4=function () {
            $scope.params4.page=$scope.Config4.currentPage;//点击页数，传对应的参数
            $http.get('/supplieraccount/cashed-list',{
                params:$scope.params4
            }).then(function (res) {
                console.log(res)
                $scope.withdraw_list = res.data.data.list
                $scope.Config4.totalItems = res.data.data.count
            },function (error) {
                console.log(error)
            })
        };
        $scope.params4 = {
            time_type:'all',
            start_time:'',
            end_time:'',
            supplier_id:''
        };
        $scope.getWithdrawList = function () {
            $scope.Config4.currentPage = 1
            if($scope.params4.time_type == 'custom'){
                if($scope.params4.start_time!=''||$scope.params4.end_time!=''){
                    tablePages4()
                }
            }else{
                tablePages4()
            }
        }
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
            if(num == 1){
                $scope.params.time_type = 'today'
                $scope.params.status = 2
            }else if(num == 2){
                $scope.params.time_type = 'all'
                $scope.params.status = 2
            }else{
                $scope.params.time_type = 'all'
                $scope.params.status = 1
            }
            tablePages()
            $state.go('mall_finance.withdraw')
        }
        //跳转二级页面
        $scope.go_second = function () {
            $scope.three_title = ''
            $scope.four_title = ''
            $scope.five_title = ''
            $scope.six_title = ''
            console.log($scope.second_title)
            if ($scope.second_title == '商城财务') {
                $state.go('mall_finance.index')
            }
        }
        //跳转三级页面
        $scope.go_three = function () {
            $scope.four_title = ''
            $scope.five_title = ''
            $scope.six_title = ''
            if ($scope.three_title == '入账详情') {
                $state.go('intelligent.add_house')
            }else if($scope.three_title == '商家提现管理'){
                $state.go('mall_finance.withdraw')
            }else if($scope.three_title == '账户管理'){
                $state.go('mall_finance.account')
            }
        }
        //跳转四级页面
        $scope.go_four = function () {
            $scope.five_title = ''
            $scope.six_title = ''
            if ($scope.four_title == '商家提现管理详情') {
                $state.go('mall_finance.withdraw_manage_detail')
            }else if($scope.four_title == '详情'){
                $state.go('mall_finance.account_detail')
            }
        }
        //跳转五级页面
        $scope.go_five = function () {
            $scope.six_title = ''
            if ($scope.five_title == '已提现') {
                $state.go('')
            }
        }
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
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '提交成功'

                $scope.common_house = function () {
                    $uibModalInstance.close()
                    tablePages()
                    $state.go('mall_finance.withdraw')
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
                $scope.cur_first_level = $scope.first_level[0].id
                $scope.second_level = []
                $scope.third_level = []
                tablePages2()
                $state.go('mall_finance.account')
            }, function (error) {
                console.log(error)
            })
        }
        // 改变店铺类型
        // $scope.$watch('cur_shop_type',function (newVal,oldVal) {
        //     if(oldVal!=''){
        //         $http.get('/supplieraccount/account-list', {
        //             params: {
        //                 category_id: $scope.cur_category_id,
        //                 type_shop: $scope.cur_shop_type.num,
        //                 status: $scope.cur_shop_status.num
        //             }
        //         }).then(function (res) {
        //             console.log(res)
        //             $scope.account_list = res.data.data.list
        //         }, function (error) {
        //             console.log(error)
        //         })
        //     }
        // })
        // 改变店铺状态
        // $scope.$watch('cur_shop_status',function (newVal,oldVal) {
        //     if(oldVal!=0){
        //         $http.get('/supplieraccount/account-list', {
        //             params: {
        //                 category_id: $scope.cur_category_id,
        //                 type_shop: $scope.cur_shop_type.num,
        //                 status: $scope.cur_shop_status.num
        //             }
        //         }).then(function (res) {
        //             console.log(res)
        //             $scope.account_list = res.data.data.list
        //         }, function (error) {
        //             console.log(error)
        //         })
        //     }
        // })
        //提现管理关键词搜索
        $scope.get_withdraw_list = function () {
            if($scope.keyword!=''){
                $scope.params.search = $scope.keyword
                $scope.Config.currentPage = 1
                $scope.params.time_type = 'all'
                $scope.params.status = 0
                tablePages()
            }
        }
        //账户管理关键词搜索
        $scope.get_account_list = function () {
            if($scope.keyword2!=''){
                $scope.params2.keyword = $scope.keyword2
                $scope.Config2.currentPage = 1
                $scope.params2.category_id = 0
                $scope.params2.type_shop = -1
                $scope.params2.status = -1
                tablePages2()
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
            $scope.params4.supplier_id = $scope.cur_account_detail.id
            tablePages4()
            $state.go('mall_finance.withdraw_list')

        }
        //跳转冻结金额列表
        $scope.go_freeze_list = function () {
            $scope.cur_index == 2
            $scope.three_title = '账户管理'
            $scope.four_title = '详情'
            $scope.five_title = '冻结金额列表'
            $scope.six_title = ''
            $scope.params3.supplier_id = $scope.cur_account_detail.id
            tablePages3()
            $state.go('mall_finance.freeze_list')
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
            console.log(item)
            $scope.three_title = '账户管理'
            $scope.four_title = '详情'
            $scope.five_title = '提现列表'
            $scope.six_title='详情'
            $http.get('/supplieraccount/cashed-view',{
                params:{
                    'id':item.id
                }
            }).then(function (res) {
                console.log(res)
                $scope.withdraw_detail = res.data.data
                $state.go('mall_finance.withdraw_detail')
            })
        }
        //跳转入账列表
        $scope.go_recorded_detail = function () {
            $scope.three_title = '入账详情'
            $scope.four_title = ''
            $scope.five_title = ''
            $scope.six_title=''
            $scope.cur_index = 0
            $scope.keyword1 = ''
            tablePages1()
            $state.go('mall_finance.recorded_detail')
        }
        //入账列表搜索
        $scope.get_recorded_list = function () {
            if($scope.keyword1!=''){
                $scope.params1.search = $scope.keyword1
                tablePages1()
            }
        }
    })
