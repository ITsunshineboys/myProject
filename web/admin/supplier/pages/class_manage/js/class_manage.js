/**
 * Created by Administrator on 2017/12/11/011.
 */
app.controller('class_manage', ['$rootScope', '$scope', '$stateParams', '_ajax', function ($rootScope, $scope, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '分类管理',
        icon: 'icon-shangchengguanli',
    }];

    /*默认参数*/
    $scope.params = {
        page: 1,  //当前页数
        sort_time: 2 //排序规则 默认按申请时间降序排列
    }

    /*分页配置*/
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tableList();
        }
    }

    //申请时间排序
    $scope.sortTime = function () {
        $scope.params.sort_time = $scope.params.sort_time == 2 ? 1 : 2;
        $scope.pageConfig.currentPage = 1;
        tableList();
    };

    $scope.showRemark = function (obj) {
        $scope.remark = obj;
    }


    function tableList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        _ajax.get('/supplieraccount/supplier-cate-list', $scope.params, function (res) {
            $scope.pageConfig.totalItems = res.data.total;
            $scope.datalist = res.data.details;
        })
    }

}]);

