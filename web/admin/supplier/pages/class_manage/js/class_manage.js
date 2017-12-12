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

    $scope.showRemark = function (obj) {
        $scope.remark = obj;
    }


    function tableList() {
        $scope.params.page = $scope.pageConfig.currentPage;
        let details = [{
            "id": "191",
            "title": "空心砖111",//分类名称
            "icon": "/uploads/2017/11/25/1511601040.jpg",//分类图片
            "pid": "1",
            "parent_title": "辅材",//所属分类
            "level": "二级",//等级
            "create_time": "2017-12-09 08:17",//申请时间
            "approve_time": "0",
            "reject_time": "0",
            "review_status": "等待审核",//状态
            "reason": "666666",//审核理由
            "path": "1,191,",
            "supplier_name": "",
            "titles": "辅材 - 空心砖111"
        },{
            "id": "192",
            "title": "空心砖111",//分类名称
            "icon": "/uploads/2017/11/25/1511601040.jpg",//分类图片
            "pid": "1",
            "parent_title": "辅材",//所属分类
            "level": "二级",//等级
            "create_time": "2017-12-09 08:17",//申请时间
            "approve_time": "0",
            "reject_time": "0",
            "review_status": "已通过",//状态
            "reason": "所属",//审核理由
            "path": "1,191,",
            "supplier_name": "",
            "titles": "辅材 - 空心砖111"
        }]
        // _ajax.get('/supplieraccount/supplier-cate-list',$scope.params,function (res) {
        //     $scope.pageConfig.totalItems = res.total;
            $scope.datalist = details;
        // })
    }

}]);

