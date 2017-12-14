/**
 * Created by Administrator on 2017/12/13/013.
 */
app.controller('verify_detail', ['$rootScope', '$scope', '$state', '$stateParams','_ajax', function ($rootScope, $scope, $state, $stateParams, _ajax) {
    $scope.id = $stateParams.id;    // 商家ID
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家入驻审核',
        link: -1
    },{
        name: '审核'
    }];

    /*待审核状态的参数*/
    $scope.params = {
        supplier_id: String($stateParams.id), // 商家id
        status: '2',         // 默认审核通过
        review_remark: ''           // 审核备注
    }

    // 审核详情
    _ajax.get('/supplier/supplier-be-audited-detail', {supplier_id: $stateParams.id}, function (res) {
        $scope.detail = res.data;
    })

    // 待审核状态 确认审核
    $scope.verifyHandle = function () {
        _ajax.post('/supplier/supplier-be-audited-apply-handle', $scope.params, function (res) {
            $('#handle-success').modal('show');
        })
    }

    // 返回
    $scope.backPage = function (obj) {
        if(arguments[0]=='modal'){
            setTimeout(function () {
                $state.go('settle_verify.wait');
            },200)
        }else{
            history.go(-1);
        }

    }

}]);