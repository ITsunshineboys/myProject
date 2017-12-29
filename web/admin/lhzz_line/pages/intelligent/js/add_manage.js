app.controller('add_manage_ctrl', function ($scope, $rootScope, _ajax, $http, $state, $stateParams, $uibModal) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
            link: function () {
                $state.go('intelligent_index')
                $rootScope.crumbs.splice(1, 4)
            }
        }, {
            name: '首页管理',
            link: -1
        }, {
            name: $stateParams.index == 1 ? '编辑推荐' : '添加推荐'
        }
    ]
})