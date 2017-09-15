/**
 * Created by hulingfangzi on 2017/7/27.
 */
/*商家管理*/
var store_mag = angular.module("storemagModule", []);
store_mag.controller("store_mag", function ($scope, $http) {
    // chooseNot();
    chooseNot();
    firstClass();
    $scope.storetype_arr = [{storetype: "全部", id:-1}, {storetype: "旗舰店", id:0},{storetype: "专卖店", id:1}, {storetype: "专营店", id:2}]
    $scope.status_arr = [{status: "全部", id: -1}, {status: "正常营业", id: 1}, {status: "已关闭", id: 0}];
    $scope.typeselect = $scope.storetype_arr[0].id;
    $scope.statusselect = $scope.status_arr[0].id;
    $scope.firstselect = 0;
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    $scope.tempclass = 0;
    $scope.temptype = -1;
    $scope.tempstatus = -1;
    $scope.searchcontent = '';
    /*本月销售额排序*/
    $scope.soldnum_ascorder = false;
    $scope.soldnum_desorder = true;

    /*本月销量排序*/
    $scope.sold_ascorder = false;
    $scope.sold_desorder = true;
    let tempshop_no;

    /*已关闭状态样式*/
    $scope.isClosed = function (obj) {
        return obj == "已关闭";
    }

    function firstClass() {
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
        }).then(function (response) {
            $scope.firstclass = response.data.data.categories;
            $scope.firstselect = response.data.data.categories[0].id;
        })
    }



    //监听第一个下拉
    $scope.$watch('firstselect',function (newVal,oldVal) {
        $scope.searchcontent = '';
        $scope.temp = newVal;
            $http.get('http://test.cdlhzz.cn:888/mall/supplier-list',{
                params:{
                    shop_type:+$scope.typeselect,
                    status:+$scope.statusselect,
                    category_id:+newVal
                }
            }).then(function (res) {
                $scope.stores = res.data.data.supplier_list.details;
            },function (err) {
                console.log(err);
            })
    });

    //监听第二个下拉
    $scope.$watch('secselect',function (newVal,oldVal) {
        $scope.searchcontent = '';
        $scope.temp = newVal;
        if(!!newVal){
            $http.get('http://test.cdlhzz.cn:888/mall/supplier-list',{
                params:{
                    shop_type:+$scope.typeselect,
                    status:+$scope.statusselect,
                    category_id:+newVal
                }
            }).then(function (res) {
                $scope.stores = res.data.data.supplier_list.details;
            },function (err) {
                console.log(err);
            })
        }
    });

    //监听第三个下拉
    $scope.$watch('thirdselect',function (newVal,oldVal) {
        $scope.searchcontent = '';
        $scope.temp = newVal;
        if(!!newVal){
            $http.get('http://test.cdlhzz.cn:888/mall/supplier-list',{
                params:{
                    shop_type:+$scope.typeselect,
                    status:+$scope.statusselect,
                    category_id:+newVal
                }
            }).then(function (res) {
                $scope.stores = res.data.data.supplier_list.details;
            },function (err) {
                console.log(err);
            })
        }
    });

    /*监听店铺类型下拉*/
    $scope.$watch('typeselect',function (newVal,oldVal) {
        $scope.searchcontent = '';
            $http.get('http://test.cdlhzz.cn:888/mall/supplier-list',{
                params:{
                    shop_type:+newVal,
                    status:+$scope.statusselect,
                    category_id:$scope.temp||0
                }
            }).then(function (res) {
                $scope.stores = res.data.data.supplier_list.details;
            },function (err) {
                console.log(err);
            })
    });

    /*监听状态下拉*/
    $scope.$watch('statusselect',function (newVal,oldVal) {
        $scope.searchcontent = '';
        $http.get('http://test.cdlhzz.cn:888/mall/supplier-list',{
            params:{
                shop_type:+$scope.typeselect,
                status:+newVal,
                category_id:$scope.temp
            }
        }).then(function (res) {
            $scope.stores = res.data.data.supplier_list.details;
        },function (err) {
            console.log(err);
        })
    });





    /*分类选择二级下拉框*/
    $scope.subClass = function (obj) {
        /*二级下拉框内容*/
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
            params: {pid: obj}
        }).then(function (response) {
            $scope.secondclass = response.data.data.categories;
            $scope.secselect = response.data.data.categories[0].id;
        })
    }

    /*分类选择三级下拉框*/
    $scope.thirdClass = function (obj) {
        /*选择了第二个下拉框时表格的内容*/
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
            params: {pid: obj}
        }).then(function (response) {
            $scope.thirdclass = response.data.data.categories;
            $scope.thirdselect = response.data.data.categories[0].id;
        })
    }



    /*筛选*/
    /*1.都为全部*/
    function chooseNot() {
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/supplier-list",
            params:{size:999}
        }).then(function (res) {
            $scope.stores = res.data.data.supplier_list.details;
        })
    }


    /*搜索店铺*/
    $scope.searchStore = function () {
        console.log( $scope.searchcontent)
        $http({
            method: "get",
            url: "http://test.cdlhzz.cn:888/mall/supplier-list",
            params: {keyword:$scope.searchcontent}
        }).then(function (res) {
            console.log(res)
            $scope.stores = res.data.data.supplier_list.details;
        })
    }

    /*本月销售额排序*/
    $scope.storeSoldNumDesorder = function () {
        $scope.soldnum_desorder = true;
        $scope.soldnum_ascorder = false;
        $http({
            method: "get",
            params: {"sort[]": "sales_amount_month:3"},
            url: "http://test.cdlhzz.cn:888/mall/supplier-list",
        }).then(function (response) {
            $scope.stores = response.data.data.supplier_list.details;
            // $scope.selPage = 1;
        })
    }

    $scope.storeSoldNumAscorder = function () {
        $scope.soldnum_desorder = false;
        $scope.soldnum_ascorder = true;
        $http({
            method: "get",
            params: {"sort[]": "sales_amount_month:4"},
            url: "http://test.cdlhzz.cn:888/mall/supplier-list",
        }).then(function (response) {
            $scope.stores = response.data.data.supplier_list.details;
            // $scope.selPage = 1;
        })
    }

    /*本月销量排序*/
    $scope.storeSoldDesorder = function () {
        $scope.sold_ascorder = false;
        $scope.sold_desorder = true;
        $http({
            method: "get",
            params: {"sort[]": "sales_amount_month:3"},
            url: "http://test.cdlhzz.cn:888/mall/supplier-list",
        }).then(function (response) {
            console.log(response)
            $scope.stores = response.data.data.supplier_list.details;
            // $scope.selPage = 1;
        })
    }

    $scope.storeSoldAscorder = function () {
        $scope.sold_ascorder = true;
        $scope.sold_desorder = false;
        $http({
            method: "get",
            params: {"sort[]": "sales_amount_month:4"},
            url: "http://test.cdlhzz.cn:888/mall/supplier-list",
        }).then(function (response) {
            console.log(response)
            $scope.stores = response.data.data.supplier_list.details;
            // $scope.selPage = 1;
        })
    }




    /*开店/闭店*/
    $scope.changeStatus = function (id,status) {
        $scope.storestatus = status;
        tempshop_no = id;
    }

    /*确认开店/闭店*/
    $scope.sureCloseStore = function () {
        tempshop_no = Number(tempshop_no)
        let url = "http://test.cdlhzz.cn:888/mall/supplier-status-toggle";
        let data = {supplier_id: tempshop_no};
        $http.post(url, data, config).then(function (res) {
            console.log(res)
            tempshop_no = '';
        })
    }
});