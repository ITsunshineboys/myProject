/**
 * Created by Administrator on 2017/8/8 0008.
 */
var comment= angular.module("storedetailModule",[])
    .controller("storedetail_ctrl",function ($rootScope,$scope,$http,$stateParams) {
        let result;
        let year;
        let timearr;
        const storeid =  $stateParams.store.id
        $rootScope.crumbs = [{
            name: '商城管理',
            icon: 'icon-shangchengguanli',
            link: 'merchant_index'
        }, {
            name: '商家管理',
            link: 'store_mag',
        },{
            name: '商家详情',
        }];
        $scope.store = $stateParams.store;
        $scope.category_name = $stateParams.store.category_name;
        $scope.type_shop = $stateParams.store.type_shop;
        $scope.shop_name = $stateParams.store.shop_name;
        /*选项卡切换方法*/
        $scope.tabFunc = (obj) => {
            $scope.basic_flag = false;
            $scope.aptitude_flag = false;
            $scope.authorize_flag = false;
            $scope[obj] = true;
        }

        /*默认参数*/
        $scope.params = {
            page:1,
            supplier_id:storeid,
            create_time:4
        }

        /*分页配置*/
        $scope.pageConfig = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                authorizeList();
            }
        }

        if($stateParams.authorize_flag){
            $scope.tabFunc('authorize_flag');
            authorizeList();
            storeDetail();
        }else {
            $scope.tabFunc('basic_flag');
            storeDetail();
        }




        function storeDetail() {
            $http({
                params:{id:storeid},
                method:"get",
                url:baseUrl+"/mall/supplier-view-admin",
            }).then(function (res) {
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
                $scope.identity_no = result.identity_no;
                $scope.identity_card_front_image = result.identity_card_front_image;
                $scope.identity_card_back_image = result.identity_card_back_image;
                $scope.type_org = result.type_org;//单位类型
                $scope.mobile = result.mobile;//登录账号
                $scope.support_offline_shop = result.support_offline_shop;
            })
        }


        /*品牌授权列表*/
        function authorizeList(){
            $scope.params.page = $scope.pageConfig.currentPage;
            $http({
                method:"get",
                params:$scope.params,
                url:baseUrl+"/mall/brand-application-list-admin"
            }).then((res)=>{
                $scope.listdata = res.data.data.brand_application_list_admin.details;
                $scope.pageConfig.totalItems = res.data.data.brand_application_list_admin.total;
            })
        }

        /*审核备注详情*/
        $scope.showNote = (obj) => {
            $scope.notedetail = obj;
        }
    })