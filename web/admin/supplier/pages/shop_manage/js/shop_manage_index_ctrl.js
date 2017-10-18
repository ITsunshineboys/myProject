/**
 * Created by Administrator on 2017/9/18/018.
 */
let shopmanage = angular.module("shopmanageModule", []);
shopmanage.controller("shopmanage_ctrl", function ($scope, $state, $http, $stateParams, _ajax, Upload) {
    let result;
    let id;
    const picprefix = "http://test.cdlhzz.cn:888/";
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
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
            result = data.supplier_view_admin;
            $scope.result = data.supplier_view_admin;
            year = String(Number(result.create_time.substring(0, 4)) + 1);
            timearr = result.create_time.split('-');
            timearr.splice(0, 1, year);
            $scope.onemoreyear = timearr.join('-');//资质日期加一年
            $scope.iconpath = picprefix + result.icon;
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
            url: 'http://test.cdlhzz.cn:888/site/upload',
            data: {'UploadForm[file]': file}
        }).then((response) => {
            if (!response.data.data) {
                $scope.picwarning = true;
                $scope.iconpath = 'lib/images/default.png';
            } else {
                $scope.picwarning = false;
                $scope.iconpath = picprefix + response.data.data.file_path;
                $scope.classicon = response.data.data.file_path;
            }
        }, (error) => {
            console.log(error)
        })
    };
    /*重设商家logo*/
    $scope.resetLogo = () => {
        if (!$scope.picwarning) {
            let url = "http://test.cdlhzz.cn:888/mall/supplier-icon-reset";
            let data = {id: +id, icon: $scope.classicon || result.icon};
            $http.post(url, data, config).then(function (res) {
                console.log(res)
            });
            $scope.savemodal = '#suresave';
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
            console.log(res);
            let data = res.data;
            $scope.pageConfig.totalItems = data.brand_application_list_admin.total;
            $scope.authList = data.brand_application_list_admin.details;
        });
    }
});