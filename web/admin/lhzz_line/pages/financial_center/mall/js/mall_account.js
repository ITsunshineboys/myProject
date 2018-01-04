app.controller('mall_account_ctrl', function ($scope, $rootScope, _ajax, $state, $stateParams, $uibModal) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '财务中心',
            icon: 'icon-caiwu',
            link: $rootScope.finance_click
        }, {
            name: '商城财务',
            link: -1
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
    //请求一级分类
    _ajax.get('/supplieraccount/category', {
        pid: 0
    }, function (res) {
        $scope.level_one = res.data
        $scope.level_one.unshift({id: '0', title: '全部'})
        $scope.first = $scope.level_one[0].id
        $scope.level_two = []
        $scope.level_three = []
        $scope.params.category_id = $scope.level_one[0].id
        $scope.params.type_shop = $scope.shop_type[0].num
        $scope.params.status = $scope.shop_status[0].num
        tablePages()
    })
    /*分页配置*/
    $scope.Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            $scope.params.category_id!=''?tablePages():'';
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
    $scope.params = {
        category_id: '',
        type_shop: '',
        status: '',
        keyword: ''
    };
    //修改分类
    $scope.getCategory = function (index) {
        if($scope.params.category_id!=''){
            if (index == 1) {
                $scope.params.category_id = $scope.first
                if($scope.first == $scope.level_one[0].id){
                    $scope.level_two = []
                    $scope.level_three = []
                }else{
                    _ajax.get('/supplieraccount/category',{
                        pid:$scope.first
                    },function (res) {
                        console.log(res);
                        $scope.level_two = res.data
                        $scope.level_two.unshift({id:$scope.first,title:'全部'})
                        $scope.second = $scope.level_two[0].id
                        $scope.level_three = []
                    })
                }
            }else if(index == 2){
                $scope.params.category_id = $scope.second
                if($scope.second == $scope.level_two[0].id){
                    $scope.level_three = []
                }else{
                    _ajax.get('/supplieraccount/category',{
                        pid:$scope.second
                    },function (res) {
                        console.log(res);
                        $scope.level_three = res.data
                        $scope.level_three.unshift({id:$scope.second,title:'全部'})
                        $scope.third = $scope.level_three[0].id
                    })
                }
            }else{
                $scope.params.category_id = $scope.third
            }
            $scope.Config.currentPage = 1
            tablePages()
        }
    }
    $scope.$watch('keyword',function (newVal,oldVal) {
        if(newVal == ''&&oldVal!=''&&$scope.params.category_id!=''){
            $scope.params.keyword = ''
            $scope.Config.currentPage = 1
            tablePages()
        }
    })
    $scope.getAccountList = function () {
        if($scope.keyword!=''){
            $scope.Config.currentPage = 1
            $scope.params.keyword = $scope.keyword
            $scope.params.category_id = $scope.level_one[0].id
            $scope.first = $scope.level_one[0].id
            $scope.level_two = []
            $scope.level_three = []
            $scope.params.status = $scope.shop_status[0].num
            $scope.params.type_shop = $scope.shop_type[0].num
            tablePages()
        }
    }
})