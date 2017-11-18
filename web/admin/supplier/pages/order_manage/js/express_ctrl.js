/**
 * Created by Administrator on 2017/9/26/026.
 */
let express = angular.module("expressModule", []);
express.controller("express_ctrl", function ($rootScope,$scope, $http, $stateParams,$state) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };

    $scope.order_no = $stateParams.express_params.order_no; //订单号
    $scope.sku = $stateParams.express_params.sku; //商品编号
    $scope.tabflag = $stateParams.express_params.tabflag; //
    console.log($scope.tabflag+'物流页面跳转flag');
    let statename = $stateParams.express_params.statename;



    /*获取物流信息*/
    expressDetail();
    function expressDetail() {
        let url = baseUrl+"/order/getexpress";
        let data = {
            order_no: $scope.order_no,
            sku: +$scope.sku
        };
        $http.post(url, data, config).then(function (res) {
           console.log(res);
            $scope.order_info = res.data.data;
            $scope.shipping_way = res.data.data.shipping_type; //判断是送货上门or快递

        })
    }

    /*返回上一个页面*/
    $scope.backPage = function () {
        if(statename=='waitsend_detail'){
            $state.go('waitsend_detail',{order_no:$scope.order_no,sku:$scope.sku,tabflag:$scope.tabflag})
        }else{
            $state.go(statename,{order_no:$scope.order_no,sku:$scope.sku,tabflag:$scope.tabflag})
        }
    }


    /*面包屑参数*/
    $rootScope.crumbs = [{
        name: '订单管理',
        icon: 'icon-dingdanguanli',
        link: 'order_manage',
        params:{tabflag:$scope.tabflag}
    },{
        name: '订单详情',
        link:  $scope.backPage,
    },{
        name: '物流详情'
    }];

})