/**
 * Created by Administrator on 2017/12/12/011.
 */
app.controller('class_detail', ['$state', '$rootScope', '$scope', '$stateParams', '_ajax', function ($state, $rootScope, $scope, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '分类管理',
        icon: 'icon-classification',
        link: 'class_manage'
    },{
        name: '分类详情'
    }];

    $scope.id = $stateParams.id;

    //详情数据
    _ajax.get('/supplieraccount/supplier-cate-view',{cate_id:$stateParams.id},function (res) {
        $scope.detail = res.data;
        $scope.title = res.data.title; //分类名称
    })
}]);

