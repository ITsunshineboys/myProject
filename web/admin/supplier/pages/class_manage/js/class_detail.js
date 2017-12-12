/**
 * Created by Administrator on 2017/12/12/011.
 */
app.controller('class_detail', ['$state', '$rootScope', '$scope', '$stateParams', '_ajax', function ($state, $rootScope, $scope, $stateParams, _ajax) {
    $rootScope.crumbs = [{
        name: '分类管理',
        icon: 'icon-shangchengguanli',
        link: 'class_manage'
    },{
        name: '分类详情'
    }];

    $scope.id = $stateParams.id;
    _ajax.get('/supplieraccout/supplier-cate-view',{cate_id:$stateParams.id},function (res) {
            $scope.datail = res;
    })
}]);

