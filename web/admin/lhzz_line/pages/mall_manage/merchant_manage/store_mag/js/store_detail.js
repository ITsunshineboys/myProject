/**
 * Created by Administrator on 2017/8/8 0008.
 */
var comment= angular.module("storedetailModule",[])
    .controller("storedetail_ctrl",function ($scope,$http,$stateParams) {
        let vm = $scope.vm = {};
        let result;
        let year;
        let timearr;
        const storeid = $stateParams.store.id;
        console.log($stateParams.store);
        $scope.category_name = $stateParams.store.category_name;
        $scope.type_shop = $stateParams.store.type_shop;
        $scope.shop_name = $stateParams.store.shop_name;
        storeDetail();


        function storeDetail() {
            $http({
                params:{id:storeid},
                method:"get",
                url:"http://test.cdlhzz.cn:888/mall/supplier-view-admin",
            }).then(function (res) {
                console.log(res);
                result = res.data.data.supplier_view_admin;
                $scope.name = result.name;
                $scope.shop_no = result.shop_no;
                $scope.create_time = result.create_time;
                year = String(Number(result.create_time.substring(0,4))+1);
                timearr = result.create_time.split('-');
                timearr.splice(0,1,year);
                $scope.onemoreyear = timearr.join('-');//资质日期加一年
                $scope.status = result.status;
                $scope.icon = result.icon;
                $scope.comprehensive_score = result.comprehensive_score;
                $scope.store_service_score = result.store_service_score;
                $scope.logistics_speed_score = result.logistics_speed_score;
                $scope.delivery_service_score = result.delivery_service_score;
                $scope.follower_number = result.follower_number;
                $scope.quality_guarantee_deposit = result.quality_guarantee_deposit;
                $scope.licence = result.licence;
                $scope.licence_image = result.licence_image;
                $scope.legal_person = result.legal_person;
                $scope.identity_card_no = result.identity_card_no;
                $scope.identity_card_front_image = result.identity_card_front_image;
                $scope.identity_card_back_image = result.identity_card_back_image;
                $scope.type_org = result.type_org;//单位类型
                $scope.mobile = result.mobile;//登录账号
                $scope.support_offline_shop = result.support_offline_shop;
            })
        }

    })