angular.module('mall_finance', ['ui.bootstrap'])
    .controller('mall_finance_ctrl', function ($scope, $http,$rootScope, $state,_ajax,$uibModal,$location) {
        $rootScope.crumbs = [
            {
                name:'财务中心',
                icon:'icon-caiwu'
            },{
            name:'商城财务'
            }
        ]
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
            _ajax.get('/supplier-cash/cash-list-today',$scope.params,function (res) {
                console.log(res);
                $scope.shop_withdraw_list = res.data.list
                $scope.Config.totalItems = res.data.count;
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
                $scope.params.time_start = ''
                $scope.params.time_end = ''
                tablePages()
            }
        }
        $scope.$watch('keyword',function (newVal,oldVal) {
            if(newVal == ''){
                $scope.Config.currentPage = 1
                $scope.params.search = newVal
                tablePages()
            }
        })
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
            _ajax.get('/supplier-cash/order-list-today',$scope.params1,function (res) {
                console.log(res);
                $scope.recorded_list = res.data.list
                $scope.Config1.totalItems = res.data.count
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
                $scope.params1.time_start = ''
                $scope.params1.time_end = ''
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
            _ajax.get('/supplieraccount/account-list',$scope.params2,function (res) {
                console.log(res);
                $scope.account_list = res.data.list
                $scope.Config2.totalItems = res.data.count
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
                    _ajax.get('/supplieraccount/category',{
                        pid: $scope.cur_first_level
                    },function (res) {
                        console.log(res)
                        $scope.second_level = res.data
                        $scope.second_level.unshift({id: $scope.cur_first_level, title: "全部"})
                        $scope.cur_second_level = $scope.second_level[0].id
                    })
                    $scope.third_level = []
                }else{
                    $scope.second_level = []
                    $scope.third_level = []
                }
            }else if(num == 2){
                $scope.params2.category_id = $scope.cur_second_level
                if($scope.cur_second_level!=$scope.second_level[0].id){
                    _ajax.get('/supplieraccount/category',{
                        pid: $scope.cur_second_level
                    },function (res) {
                        console.log(res)
                        $scope.third_level = res.data
                        $scope.third_level.unshift({id: $scope.cur_second_level, title: "全部"})
                        $scope.cur_third_level = $scope.third_level[0].id
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
            _ajax.get('/supplieraccount/freeze-list',$scope.params3,function (res) {
                console.log(res);
                $scope.freeze_list = res.data.list
                $scope.Config3.totalItems = res.data.count
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
            _ajax.get('/supplieraccount/cashed-list',$scope.params4,function (res) {
                console.log(res)
                $scope.withdraw_list = res.data.list
                $scope.Config4.totalItems = res.data.count
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
        //返回前一页
        $scope.go_prev1 = function () {
            // if($rootScope.curState_name == 'mall_finance.account_detail'){
            //     $rootScope.fromState_name = 'mall_finance.account'
                $rootScope.crumbs = [
                    {
                        name:'财务中心',
                        icon:'icon-caiwu'
                    },{
                        name:'商城财务',
                        link:function () {
                            $state.go('mall_finance.index')
                            $rootScope.crumbs.splice(2,4)
                        }
                    },{
                    name:'账户管理'
                    }
                ]
            tablePages2()
            $state.go('mall_finance.account')
            // }else if($rootScope.curState_name == 'mall_finance.account_detail'){
            //
            // }
        }
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
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'商家提现管理'
                }
            ]
            $scope.Config.currentPage = 1
            $scope.params.search = ''
            $scope.keyword = ''
            $scope.params.time_start = ''
            $scope.params.time_end = ''
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
        //跳转商家提现管理详情
        $scope.go_withdraw_manageDetail = function (item) {
            console.log(item)
            $scope.cur_status = item.status
            $scope.cur_account_money = ''
            $scope.withdraw_remark = ''
            $scope.cur_deal_style = $scope.deal_style[0]
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'商家提现管理',
                    link:function () {
                        $state.go('mall_finance.withdraw')
                        $rootScope.crumbs.splice(3,3)
                    }
                },{
                name:'商家提现管理详情'
                }
            ]
            _ajax.get('/supplier-cash/cash-action-detail',{
                transaction_no:item.transaction_no
            },function (res) {
                console.log(res)
                $scope.all_withdraw_detail = res.data
                $state.go('mall_finance.withdraw_manage_detail')
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
                    $rootScope.crumbs = [
                        {
                            name:'财务中心',
                            icon:'icon-caiwu'
                        },{
                            name:'商城财务',
                            link:function () {
                                $state.go('mall_finance.index')
                                $rootScope.crumbs.splice(2,4)
                            }
                        },{
                            name:'商家提现管理',
                        }
                    ]
                    $state.go('mall_finance.withdraw')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if(valid){
                _ajax.post('/supplier-cash/cash-deal',data,function (res) {
                    console.log(res)
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                    $scope.submitted = false
                })
            }else{
                $scope.submitted = true
            }
        }
        //跳转账户管理
        $scope.go_account = function () {
            $scope.three_title = '账户管理'
            $scope.four_title = ''
            $scope.five_title = ''
            $scope.six_title = ''
            $scope.keyword = ''
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'账户管理'
                }
            ]
            //请求一级分类
            _ajax.get('/supplieraccount/category',{
                pid: 0
            },function (res) {
                $scope.first_level = res.data
                $scope.first_level.unshift({id: "0", title: "全部"})
                $scope.cur_first_level = $scope.first_level[0].id
                $scope.second_level = []
                $scope.third_level = []
                tablePages2()
                $state.go('mall_finance.account')
            })
        }
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
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'账户管理',
                    link:function () {
                        $state.go('mall_finance.account')
                        $rootScope.crumbs.splice(3,3)
                    }
                },{
                name:'详情'
                }
            ]
            $scope.cur_account = item
            _ajax.get('/supplieraccount/account-view',{
                id:item.id
            },function (res) {
                $scope.cur_account_detail = res.data
                $state.go('mall_finance.account_detail')
            })
        }
        // 跳转冻结金额
        $scope.go_freeze_money = function () {
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'账户管理',
                    link:function () {
                        $state.go('mall_finance.account')
                        $rootScope.crumbs.splice(3,3)
                    }
                },{
                    name:'详情',
                    link:function () {
                        $state.go('mall_finance.account_detail')
                        $rootScope.crumbs.splice(4,2)
                    }
                },{
                name:'冻结金额'
                }
            ]
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
                    $rootScope.crumbs = [
                        {
                            name:'财务中心',
                            icon:'icon-caiwu'
                        },{
                            name:'商城财务',
                            link:function () {
                                $state.go('mall_finance.index')
                                $rootScope.crumbs.splice(2,4)
                            }
                        },{
                            name:'账户管理',
                            link:function () {
                                $state.go('mall_finance.account')
                                $rootScope.crumbs.splice(3,3)
                            }
                        },{
                            name:'详情'
                        }
                    ]
                    _ajax.get('/supplieraccount/account-view',{
                        id: $scope.cur_account.id
                    },function (res) {
                        $scope.cur_account_detail = res.data
                        $state.go('mall_finance.account_detail')
                    })
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
                _ajax.post('/supplieraccount/apply-freeze',obj,function (res) {
                    console.log(res)
                    _ajax.get('/supplieraccount/account-view',{
                        id:$scope.cur_account_detail.id
                    },function (res) {
                        console.log(res)
                        $scope.submitted = false
                        $scope.cur_account_detail = res.data.data
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    })
                })
            }else{
                $scope.submitted = true
            }
        }
        //跳转账户管理提现列表
        $scope.go_withdraw_list = function () {
            $scope.cur_index == 3
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'账户管理',
                    link:function () {
                        $state.go('mall_finance.account')
                        $rootScope.crumbs.splice(3,3)
                    }
                },{
                    name:'详情',
                    link:function () {
                        $state.go('mall_finance.account_detail')
                        $rootScope.crumbs.splice(4,2)
                    }
                },{
                    name:'提现列表'
                }
            ]
            $scope.cur_time_type = $scope.time_type[0]
            $scope.params4.supplier_id = $scope.cur_account_detail.id
            tablePages4()
            $state.go('mall_finance.withdraw_list')

        }
        //跳转冻结金额列表
        $scope.go_freeze_list = function () {
            $scope.cur_index == 2
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'账户管理',
                    link:function () {
                        $state.go('mall_finance.account')
                        $rootScope.crumbs.splice(3,3)
                    }
                },{
                    name:'详情',
                    link:function () {
                        $state.go('mall_finance.account_detail')
                        $rootScope.crumbs.splice(4,2)
                    }
                },{
                    name:'冻结金额列表'
                }
            ]
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
                    _ajax.get('/supplieraccount/account-thaw',{
                        freeze_id:item.id
                    },function (res) {
                        console.log(res)
                        _ajax.get('/supplieraccount/freeze-list',{
                            supplier_id:cur.cur_account_detail.id
                        },function (res) {
                            console.log(res)
                            cur.freeze_list = res.data.list
                            $uibModalInstance.close()
                        })
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
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'账户管理',
                    link:function () {
                        $state.go('mall_finance.account')
                        $rootScope.crumbs.splice(3,3)
                    }
                },{
                    name:'详情',
                    link:function () {
                        $state.go('mall_finance.account_detail')
                        $rootScope.crumbs.splice(4,2)
                    }
                },{
                    name:'提现列表',
                    link:function () {
                        $state.go('mmall_finance.withdraw_list')
                        $rootScope.crumbs.splice(5,1)
                    }
                },{
                name:'详情'
                }
            ]
            _ajax.get('/supplieraccount/cashed-view',{
                'id':item.id
            },function (res) {
                console.log(res)
                $scope.withdraw_detail = res.data
                $state.go('mall_finance.withdraw_detail')
            })
        }
        //跳转入账列表
        $scope.go_recorded_detail = function () {
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'入账详情',
                }
            ]
            $scope.cur_index = 0
            $scope.params1.search = ''
            $scope.params1.time_type = 'today'
            $scope.params1.time_end = ''
            $scope.params1.time_start = ''
            $scope.keyword1 = ''
            tablePages1()
            $state.go('mall_finance.recorded_detail')
        }
        //入账列表搜索
        $scope.get_recorded_list = function () {
            if($scope.keyword1!=''){
                $scope.Config1.currentPage = 1
                $scope.params1.time_type = 'all'
                $scope.params1.time_end  = ''
                $scope.params1.time_end = ''
                $scope.params1.search = $scope.keyword1
                tablePages1()
            }
        }
        $scope.$watch('keyword1',function (newVal,oldVal) {
            if(newVal== ''){
                $scope.Config1.currentPage = 1
                $scope.params1.search = newVal
                tablePages1()
            }
        })
        //跳转订单详情
        $scope.go_list_detail = function (item) {
            console.log(item)
            $state.go('order_details',{orderNo:item.order_no,sku:item.sku})
        }
        //商家提现管理详情返回
        $scope.go_prev = function () {
            $scope.submitted = false
            $rootScope.crumbs = [
                {
                    name:'财务中心',
                    icon:'icon-caiwu'
                },{
                    name:'商城财务',
                    link:function () {
                        $state.go('mall_finance.index')
                        $rootScope.crumbs.splice(2,4)
                    }
                },{
                    name:'商家提现管理',
                }
            ]
            $state.go('mall_finance.withdraw')
        }
    })
