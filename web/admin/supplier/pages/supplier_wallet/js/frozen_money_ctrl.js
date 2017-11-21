/**
 * Created by Administrator on 2017/9/25/025.
 */
let frozen_money = angular.module("frozen_moneyModule", []);
frozen_money.controller("frozen_money_ctrl", function (_ajax, $rootScope, $scope) {
    let time_type;
    /*获取冻结余额列表*/
    $rootScope.crumbs = [{
        name: '钱包',
        icon: 'icon-qianbao',
        link: 'supplier_wallet'
    }, {
        name: '商家账户信息',
        link: 'supplier_account'
    },{
        name: '冻结余额列表'
    }];


    /*请求参数*/
    $scope.params = {
        page: 1,                        // 当前页数
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
        time_type: 'all',               // 时间类型
        sort_time: 2,                  // 操作时间排序
    };

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tableList();
        }
    }

    // 时间筛选器
    $scope.$watch('params.time_type', function (value, oldValue) {
        if (value == oldValue) {
            return
        }

        if (value != 'custom') {
            $scope.params.start_time = '';     // 自定义开始时间
            $scope.params.end_time = '';       // 自定义结束时间
            $scope.params.sort_time = 2;      // 创建时间排序
            $scope.pageConfig.currentPage = 1;
            tableList();
        }
    });

    //自定义时间筛选
    // 开始时间
    $scope.$watch('params.start_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.end_time != '') {
            $scope.params.sort_time = 2;      // 下单时间排序
            $scope.pageConfig.currentPage = 1;
            tableList()
        }
    });

    // 结束时间
    $scope.$watch('params.end_time', function (value, oldValue) {
        if (value == oldValue) {
            return
        }
        if ($scope.params.start_time != '') {
            $scope.params.sort_time = 2;      // 下单时间排序
            $scope.pageConfig.currentPage = 1;
            tableList()
        }
    });


    // 操作时间排序
    $scope.sortTime = function () {
        $scope.params.sort_time = $scope.params.sort_time == 2 ? 1 : 2;
        $scope.params.sort_money = '';      // 订单金额排序
        $scope.pageConfig.currentPage = 1;
        tableList()
    };


    /*列表数据获取方法*/
    function tableList() {
        _ajax.get('/withdrawals/find-supplier-freeze-list',$scope.params,function (res) {
            $scope.pageConfig.totalItems = +res.data.count;
            $scope.frozendetail = res.data.list;
        })
    }



    $scope.showReason = (reason) => {
        $scope.reason = reason;
    }
})