app.controller('associate_list_ctrl',function ($scope,$rootScope,$state,$stateParams,_ajax,$uibModal) {
    //面包屑
    $rootScope.crumbs = [
        {
            name:'分销',
            icon:'icon-fenxiao',
            link:function () {
                $state.go('distribution.index'),
                    $rootScope.crumbs.splice(1,2)
            }
        },
        {
            name:'详情',
            link:-1
        },
        {
            name:'相关联交易订单'
        }
    ]
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
        _ajax.get('/distribution/correlate-order',$scope.params,function (res) {
            console.log(res);
            $scope.total_amount = res.data.total_amount
            $scope.total_orders = res.data.total_orders
            $scope.all_order_detail = res.data.details
            $scope.Config.totalItems = res.data.count;
        })
    };
    $scope.params = {
        mobile:$stateParams.mobile
    };
    //修改备注
    $scope.getRemark = function (item) {
        $scope.cur_item = item
    }
    $scope.editRemark = function () {
        _ajax.post('/distribution/add-remarks',{
            order_no:$scope.cur_item.order_no,
            remarks:$scope.cur_item.remarks
        },function (res) {
            console.log(res);
            tablePages()
        })
    }
})