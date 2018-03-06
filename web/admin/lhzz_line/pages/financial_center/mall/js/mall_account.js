app.controller('mall_account_ctrl', function ($scope, $rootScope, _ajax, $state, $stateParams, $uibModal) {
    let fromState = $rootScope.fromState_name === 'mall_account_detail' || $rootScope.fromState_name === 'mall_freeze_money'
        || $rootScope.fromState_name === 'mall_withdraw_list' || $rootScope.fromState_name === 'mall_freeze_list' ||
        $rootScope.fromState_name === 'mall_withdraw_detail' || $rootScope.fromState_name === 'mall_recorded_detail' ||
        $rootScope.fromState_name === 'mall_money_list';  // 判断页面是否从详情页进到当前页面
    if (!fromState) {
        sessionStorage.removeItem('mallAccountStatus');
        sessionStorage.removeItem('category')
    }
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '商城财务',
            link: function () {
                $state.go('mall_finance')
            }
        }, {
            name: '账户管理'
        }
    ]
    $scope.vm = $scope
    //店铺类型
    $scope.shop_type = [
        {name: '全部', num: '-1'},
        {name: '旗舰店', num: '0'},
        {name: '专卖店', num: '1'},
        {name: '专营店', num: '2'},
        {name: '自营店', num: '3'}
    ]
    //店铺状态
    $scope.shop_status = [
        {name: '全部', num: '-1'},
        {name: '正常营业', num: '1'},
        {name: '已闭店', num: '0'}
    ]
    $scope.params = {
        type_shop: $scope.shop_type[0].num,
        status: $scope.shop_status[0].num,
        keyword: ''
    };
    let mallAccount = sessionStorage.getItem('mallAccountStatus')
    //请求一级分类
    _ajax.get('/supplieraccount/category', {
        pid: 0
    }, function (res) {
        $scope.level_one = res.data
        $scope.level_one.unshift({id: '0', title: '全部'})
        $scope.first = $scope.level_one[0].id
        $scope.level_two = []
        $scope.level_three = []
        if(mallAccount!=null){
            let params = JSON.parse(mallAccount)
            $scope.params = params
            $scope.Config.currentPage = params.page
            $scope.keyword = params.keyword
            let category = JSON.parse(sessionStorage.getItem('category'))
            $scope.first = category.first
            if(category.first == 0){
                tablePages()
            }else{
                if(category.first == category.second){
                    _ajax.get('/supplieraccount/category', {
                        pid: category.first
                    }, function (res) {
                        $scope.level_two = res.data
                        $scope.level_two.unshift({id: category.first, title: '全部'})
                        $scope.second = category.second
                        $scope.level_three = []
                        tablePages()
                    })
                }else{
                    _ajax.get('/supplieraccount/category', {
                        pid: category.first
                    }, function (res) {
                        $scope.level_two = res.data
                        $scope.level_two.unshift({id: category.first, title: '全部'})
                        $scope.second = category.second
                        $scope.level_three = []
                        _ajax.get('/supplieraccount/category', {
                            pid: category.second
                        }, function (res) {
                            $scope.level_three = res.data
                            $scope.level_three.unshift({id: category.second, title: '全部'})
                            $scope.third = category.third
                            tablePages()
                        })
                    })
                }
            }
        }
    })
    /*分页配置*/
    $scope.Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages()
        }
    }
    let tablePages = function () {
        $scope.params.page = $scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/supplieraccount/account-list', $scope.params, function (res) {
            console.log(res);
            $scope.account_list = res.data.list
            $scope.Config.totalItems = res.data.count
        })
    };
    $scope.$watch('params', function (newVal, oldVal) {
        if (newVal.page != oldVal.page) {

        } else {
            if(newVal.keyword === oldVal.keyword){
                $scope.keyword = ''
            }
            console.log($rootScope.curState_name);
            console.log($rootScope.fromState_name);
            $scope.Config.currentPage = 1
            tablePages()
        }
    }, true)
    //修改分类
    $scope.getCategory = function (index) {
        if (index == 1) {
            if ($scope.first == $scope.level_one[0].id) {
                $scope.level_two = []
                $scope.level_three = []
            } else {
                _ajax.get('/supplieraccount/category', {
                    pid: $scope.first
                }, function (res) {
                    console.log(res);
                    $scope.level_two = res.data
                    $scope.level_two.unshift({id: $scope.first, title: '全部'})
                    $scope.second = $scope.level_two[0].id
                    $scope.level_three = []
                })
            }
            $scope.params.category_id = $scope.first
        } else if (index == 2) {
            if ($scope.second == $scope.level_two[0].id) {
                $scope.level_three = []
            } else {
                _ajax.get('/supplieraccount/category', {
                    pid: $scope.second
                }, function (res) {
                    console.log(res);
                    $scope.level_three = res.data
                    $scope.level_three.unshift({id: $scope.second, title: '全部'})
                    $scope.third = $scope.level_three[0].id
                })
            }
            $scope.params.category_id = $scope.second
        } else if (index == 3) {
            $scope.params.category_id = $scope.third
        }
    }
    // $scope.$watch('keyword', function (newVal, oldVal) {
    //     if (newVal === '' && oldVal != '' && $scope.params.category_id != '') {
    //         $scope.params.keyword = ''
            // $scope.Config.currentPage = 1
            // tablePages()
    //     }
    // })
    $scope.getAccountList = function () {
        if ($scope.keyword != '') {
            $scope.Config.currentPage = 1
            $scope.params.keyword = $scope.keyword
            $scope.params.category_id = $scope.level_one[0].id
            $scope.first = $scope.level_one[0].id
            $scope.level_two = []
            $scope.level_three = []
            $scope.params.status = $scope.shop_status[0].num
            $scope.params.type_shop = $scope.shop_type[0].num
            tablePages()
        }else if(($scope.params.category_id == 0||$scope.params.category_id == '')&&
            $scope.params.type_shop == $scope.shop_type[0].num&&$scope.params.status ==
            $scope.shop_status[0].num&&$scope.keyword == ''){
            $scope.params.keyword = ''
            $scope.Config.currentPage = 1
        }
    }
    //跳转详情页
    $scope.goAccountDetail = function () {
        sessionStorage.setItem('mallAccountStatus', JSON.stringify($scope.params))
        let obj = {}
        if ($scope.first == 0) {
            obj.first = $scope.first
        } else {
            if ($scope.first == $scope.second) {
                obj.first = $scope.first
                obj.second = $scope.second
            } else {
                obj.first = $scope.first
                obj.second = $scope.second
                obj.third = $scope.third
            }
        }
        sessionStorage.setItem('category', JSON.stringify(obj))
    }
})