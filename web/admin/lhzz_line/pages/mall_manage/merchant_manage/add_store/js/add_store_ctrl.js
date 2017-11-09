var add_store = angular.module("addstoreModule", ['ngFileUpload']);
add_store.controller("addstore", function ($scope, $http, Upload, $location, $anchorScroll, $window, $state) {
    cascadeData();
    const picpath = 'pages/mall_manage/merchant_manage/add_store/images/default.png'
    const picprefix = baseUrl + "/";
    const pattern = /^1[3|4|5|7|8][0-9]{9}$/;
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    $scope.type_org_arr = [{type: "个体工商户", id: 0}, {type: "企业", id: 1}]; //单位类型数组
    $scope.type_shop_arr = [{storetype: "旗舰店", id: 0}, {storetype: "专营店", id: 2}, {storetype: "专卖店", id: 1}]; //店铺类型数组
    $scope.store_type = '旗舰店';
    $scope.alljudgefalse = false;
    $scope.showwarning = false;
    $scope.licenceflag = false;
    $scope.frontpathflag = false;
    $scope.backpathflag = false;
    $scope.licencepath = picpath;
    $scope.frontpath = picpath;
    $scope.backpath = picpath;
    $scope.show_account_warning = false; //显示账户错误提示
    $scope.account_warning = '';  //账户错误提示
    $scope.defaultshow = false;  //显示账号之后的内容
    $scope.with_au = false;     //已认证内容显示
    $scope.storename_repeat = false;  //店铺名称重复
    $scope.registercode_repeat = false; //公司注册码重复
    $scope.companyname_repeat = false;//公司名称重复
    $scope.licence_warning = false;  //营业执照格式错误提示
    $scope.front_warning = false;    //身份证正面错误提示
    $scope.back_warning = false;     //身份证背面错误提示

    // test();
    // function test() {
    //     let url = baseUrl+"/mall/user-add";
    //     let data = {
    //        mobile:13881237458,
    //        password:'123456'
    //     };
    //     $http.post(url, data, config).then(function (res) {
    //         console.log(res)
    //         // $scope.suremodal = '#suremodal';
    //     })
    // }

    // 参数
    $scope.params = {
        type_org: '',                    //单位类型
        category_id: '',                 //店铺分类id
        type_shop: '',                   //店铺类型
        shop_name: '',                   //店铺名称
        name: '',                        //公司名称
        licence: '',                     //社会信用代码
        licence_image: '',               //营业执照
        legal_person: '',                //法人名称
        identity_card_no: '',            //身份证号
        identity_card_front_image: '',   //身份证正面啊
        identity_card_back_image: '',    //身份证背面
        mobile: ''                       //手机号
    }


    // 所属分类-级联
    function cascadeData() {
        $http.get(baseUrl + '/mall/categories-manage-admin', {}).then(function (res) {
            let data = res.data.data;
            data.categories.splice(0, 1);
            $scope.firstclass = data.categories;
            $scope.firstselect = data.categories[0].id;
        })
        $http.get(baseUrl + '/mall/categories-manage-admin', {params: {pid: 1}}).then(function (res) {
            let data = res.data.data;
            data.categories.splice(0, 1);
            $scope.secondclass = data.categories;
            $scope.secselect = $scope.secondclass[0].id;
        })
        $http.get(baseUrl + '/mall/categories-manage-admin', {params: {pid: 2}}).then(function (res) {
            let data = res.data.data;
            data.categories.splice(0, 1);
            $scope.thirdclass = data.categories;
            $scope.params.category_id = $scope.thirdclass[0].id;
        })
    }


    // 一级联动
    $scope.subClass = (obj) => {
        $http.get(baseUrl + '/mall/categories-manage-admin', {params: {pid: obj}}).then(function (res) {
            let data = res.data.data;
            data.categories.splice(0, 1);
            $scope.secondclass = data.categories;
            $scope.secselect = data.categories[0].id;
            $http.get(baseUrl + '/mall/categories-manage-admin', {params: {pid: $scope.secselect}}).then(function (res) {
                let data = res.data.data;
                data.categories.splice(0, 1);
                $scope.thirdclass = data.categories;
                $scope.params.category_id = data.categories[0].id;
            })
        })
    };


    // 二级联动
    $scope.thirdClass = function (obj) {
        $http.get(baseUrl + '/mall/categories-manage-admin', {params: {pid: obj}}).then(function (res) {
            let data = res.data.data;
            data.categories.splice(0, 1);
            $scope.thirdclass = data.categories;
            $scope.params.category_id = data.categories[0].id;
        })
    };

    // 店铺类型判断
    $scope.typeshopChange = (obj) => {
        switch (obj) {
            case 0:
                $scope.store_type = '旗舰店';
                break;
            case 1:
                $scope.store_type = '专卖店';
                break;
            case 2:
                $scope.store_type = '专营店';
                break;
            default:
                $scope.store_type = '旗舰店';
        }
    }

    /*上传图片*/
    $scope.data = {
        filefirst: null,
        filefront: null,
        fileback: null
    }

    /*上传营业执照*/
    $scope.licenseUpload = function (file) {
        $scope.licenceflag = false;
        if (!$scope.data.filefirst) {
            return
        }
        Upload.upload({
            url: baseUrl + '/site/upload',
            data: {'UploadForm[file]': file}
        }).then(function (response) {
            if (!response.data.data) {
                $scope.licence_warning = true;
                $scope.licencepath = 'pages/mall_manage/merchant_manage/add_store/images/default.png';
            } else {
                $scope.params.licence_image = response.data.data.file_path;
                $scope.licence_warning = false;
                $scope.licencepath = picprefix + response.data.data.file_path;
            }
        }, function (error) {
            console.log(error)
        })
    }


    /*登录账户判断*/
    $scope.accountCheck = function () {
        $scope.defaultshow = false;
        $http({
            method: "get",
            url: baseUrl + "/mall/check-role-get-identity",
            params: {mobile: Number($scope.params.mobile)}
        }).then(function (res) {
            console.log(res);
            if (res.data.code == 1011 || res.data.code == 1010) {
                //已注册的商家和未成为平台用户
                $scope.show_account_warning = true;
                $scope.account_warning = res.data.msg;
            } else if (res.data.code == 200) {
                $scope.result = res.data.data;
                $scope.defaultshow = true;
                $scope.show_account_warning = false;
                /*未实名认证的商家*/
                if (!$scope.result.identity.identity_no) {
                    $scope.with_au = false;
                } else {
                    /*已实名认证的商家*/
                    $scope.with_au = true;
                    $scope._frontpath = picprefix + $scope.result.identity.identity_card_front_image;
                    $scope._backpath = picprefix + $scope.result.identity.identity_card_front_image;
                }
            }
        })
    }


    /*身份证正面上传*/
    $scope.frontUpload = function (file) {
        $scope.frontpathflag = false;
        if (!$scope.data.filefront) {
            return
        }

        Upload.upload({
            url: baseUrl + '/site/upload',
            data: {'UploadForm[file]': file}
        }).then(function (response) {
            if (!response.data.data) {
                $scope.front_warning = true;
                $scope.frontpath = 'pages/mall_manage/merchant_manage/add_store/images/default.png';
            } else {
                // $scope.
                $scope.params.identity_card_front_image = response.data.data.file_path;
                $scope.front_warning = false;
                $scope.frontpath = picprefix + response.data.data.file_path;
            }
        }, function (error) {
            console.log(error)
        })
    }

    /*身份证背面上传*/
    $scope.backUpload = function (file) {
        $scope.backpathflag = false;
        if (!$scope.data.fileback) {
            return
        }

        Upload.upload({
            url: baseUrl + '/site/upload',
            data: {'UploadForm[file]': file}
        }).then(function (response) {
            if (!response.data.data) {
                $scope.back_warning = true;
                $scope.backpath = 'pages/mall_manage/merchant_manage/add_store/images/default.png';
            } else {
                $scope.params.identity_card_back_image = response.data.data.file_path;
                $scope.back_warning = false;
                $scope.backpath = picprefix + response.data.data.file_path;
            }
        }, function (error) {
            console.log(error)
        })
    }


    $scope.sureAddStore = function (val, error) {
        $scope.licence_warning = false;  //营业执照格式错误提示
        $scope.front_warning = false;    //身份证正面错误提示
        $scope.back_warning = false;     //身份证背面错误提示
        $scope.storename_repeat = false;
        $scope.registercode_repeat = false;
        $scope.companyname_repeat = false;
        /*图片上传判断*/
        if ($scope.licencepath == picpath || $scope.frontpath == picpath || $scope.backpath == picpath) {
            if ($scope.licencepath == picpath) {
                $scope.licenceflag = true;
            }
            if ($scope.frontpath == picpath) {
                $scope.frontpathflag = true;
            }
            if ($scope.backpath == picpath) {
                $scope.backpathflag = true;
            }
        }

        if (val) {
            /*未认证的情况*/
            if ((!$scope.with_au) && val && !($scope.licencepath == picpath || $scope.frontpath == picpath || $scope.backpath == picpath)) {
                let url = baseUrl + "/mall/supplier-add";
                let data = $scope.params;
                $http.post(url, data, config).then(function (res) {
                    console.log(res);
                    switch (Number(res.data.code)) {
                        case 1010:
                            $scope.show_account_warning = true;
                            $scope.account_warning = res.data.msg;
                            break;
                        case 1011:
                            $scope.show_account_warning = true;
                            $scope.account_warning = res.data.msg;
                            break;
                        case 1028:
                            $scope.storename_repeat = true;
                            $scope.storename_repeatInfo = res.data.msg;
                            break;
                        case 1029:
                            $scope.registercode_repeat = true;
                            $scope.registercode_repeatInfo = res.data.msg;
                        case 1030:
                            $scope.companyname_repeat = true;
                            $scope.companyname_repeatInfo = res.data.msg;
                        case 200:
                            $("#suremodal").modal("show");
                            break;
                        default:
                            return;
                    }
                })
                /*已认证的情况*/
            } else if ($scope.with_au && val && $scope.licencepath != picpath) {
                $scope.params.legal_person = '';
                $scope.identity_card_no = '';
                $scope.identity_card_front_image = '';
                $scope.identity_card_back_image = '';
                let url = baseUrl + "/mall/supplier-add";
                let data = $scope.params;
                $http.post(url, data, config).then(function (res) {
                    switch (Number(res.data.code)) {
                        case 1010:
                            $scope.show_account_warning = true;
                            $scope.account_warning = res.data.msg;
                            break;
                        case 1011:
                            $scope.show_account_warning = true;
                            $scope.account_warning = res.data.msg;
                            break;
                        case 1028:
                            $scope.storename_repeat = true;
                            $scope.storename_repeatInfo = res.data.msg;
                            break;
                        case 1029:
                            $scope.registercode_repeat = true;
                            $scope.registercode_repeatInfo = res.data.msg;
                        case 1030:
                            $scope.companyname_repeat = true;
                            $scope.companyname_repeatInfo = res.data.msg;
                        case 200:
                            $("#suremodal").modal("show");
                            break;
                        default:
                            return;
                    }
                })
            }
        } else {
            $scope.alljudgefalse = true;
            //循环错误，定位到第一次错误，并聚焦
            for (let [key, value] of error.entries()) {
                if (value.$invalid) {
                    $anchorScroll.yOffset = 150;
                    $location.hash(value.$name);
                    $anchorScroll();
                    $window.document.getElementById(value.$name).focus();
                    break;
                }
            }
        }
    }


    /*确认添加成功*/
    $scope.sure = function () {
        setTimeout(() => {
            $state.go("store_mag");
        }, 200)
    }
});