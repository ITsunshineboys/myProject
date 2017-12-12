/**
 * Created by Administrator on 2017/9/18/018.
 */
let shopmanage = angular.module("shopmanageModule", []);
shopmanage.controller("shopmanage_ctrl", function ($rootScope,$scope, $state, $http, $stateParams, _ajax, Upload) {
    let result;
    let id;
    $rootScope.crumbs = [{
        name: '店铺管理',
        icon: 'icon-dianpuguanli',
    }];


    /*选项卡切换方法*/
    $scope.tabFunc = (obj) => {
        $scope.basic_flag = false;
        $scope.level_flag = false;
        $scope.authorize_flag = false;
        $scope[obj] = true;
    };
    $scope.picwarning = false;
    /*logo 提示*/
    $scope.savemodal = '';

    /*选项卡初始状态*/
    changeTabbar();

    function changeTabbar() {
        if ($stateParams.authorize_flag) {
            $scope.tabFunc('authorize_flag');
        } else {
            $scope.tabFunc('basic_flag');
        }
    }


    allDetail();

    function allDetail() {
        _ajax.get('/mall/supplier-view-admin', {}, function (res) {
            let data = res.data;
            console.log(data)
            result = data.supplier_view_admin;
            $scope.result = data.supplier_view_admin;
            let year = String(Number(result.create_time.substring(0, 4)) + 1);
            let timearr = result.create_time.split('-');
            timearr.splice(0, 1, year);
            $scope.onemoreyear = timearr.join('-');//资质日期加一年
            $scope.iconpath = result.icon;
            id = result.id;
        });
    }
    //上传店铺logo
    $scope.data = {
        file: null
    };

    $scope.upload = (file) => {
        if (!$scope.data.file) {
            return
        }
        // console.log($scope.data)
        Upload.upload({
            url: baseUrl+'/site/upload',
            data: {'UploadForm[file]': file}
        }).then((response) => {
            if (!response.data.data) {
                $scope.picwarning = true;
                $scope.iconpath = 'lib/images/default.png';
            } else {
                $scope.picwarning = false;
                $scope.iconpath = response.data.data.file_path;
                $scope.classicon = response.data.data.file_path;
            }
        }, (error) => {
            console.log(error)
        })
    };
    /*重设商家logo*/
    $scope.resetLogo = () => {
        if (!$scope.picwarning) {
            let data = {id: +id, icon: $scope.classicon || result.icon};
            _ajax.post('/mall/supplier-icon-reset',data,function (res) {
                $('#suresave').modal('show');
            })
        }
    };
    /* -------------------- 品牌授权 -------------------- */
    // 分页配置
    $scope.pageConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            authorizeList();
        }
    };
    $scope.authorizeParams = {
        page: 1,
        size: 12
    };

    $scope.goDetails = function (obj) {
        sessionStorage.setItem('authorizeInfo', JSON.stringify(obj));
        $state.go('authorize_detail');
    };

    $scope.showInfo = function (info) {
        _alert('审核备注', info)
    };

    /*获取品牌授权列表*/
    function authorizeList() {
        $scope.authorizeParams.page = $scope.pageConfig.currentPage;
        _ajax.get('/mall/brand-application-list-admin', $scope.authorizeParams, function (res) {
            let data = res.data;
            $scope.pageConfig.totalItems = data.brand_application_list_admin.total;
            $scope.authList = data.brand_application_list_admin.details;
        });
    }
});