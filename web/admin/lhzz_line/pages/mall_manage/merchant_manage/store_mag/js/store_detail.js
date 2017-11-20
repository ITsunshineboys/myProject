/**
 * Created by Administrator on 2017/8/8 0008.
 */
var comment= angular.module("storedetailModule",[])
    .controller("storedetail_ctrl",function ($rootScope,$scope,$http,$stateParams,_ajax) {
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
            _ajax.get('/mall/supplier-view-admin', {id: storeid}, function (res) {
                $scope.result = res.data.supplier_view_admin;
                year = String(Number($scope.result.create_time.substring(0, 4)) + 1);
                timearr = $scope.result.create_time.split('-');
                timearr.splice(0, 1, year);
                $scope.onemoreyear = timearr.join('-');//资质日期加一年
            })
        }

        /*品牌授权列表*/
        function authorizeList(){
            $scope.params.page = $scope.pageConfig.currentPage;
            _ajax.get('/mall/brand-application-list-admin',$scope.params,function (res) {
                $scope.listdata = res.data.brand_application_list_admin.details;
                $scope.pageConfig.totalItems = res.data.brand_application_list_admin.total;
            })
        }

        /*审核备注详情*/
        $scope.showNote = (obj) => {
            $scope.notedetail = obj;
        }


    })