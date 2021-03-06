angular.module("addstoreModule", ['ngFileUpload']).controller("addstore", function ($scope, $http, Upload, $location, $anchorScroll, $window, $state,$rootScope,_ajax) {
    cascadeData();
    const picpath = 'pages/mall_manage/merchant_manage/add_store/images/default.png'
    const phone_pattern = /^1[3|4|5|7|8][0-9]{9}$/;
    const legal_pattern=/^[\u0391-\uFFE5A-Za-z]+$/;
    const id_pattern = /(^[0-9]{15}$)|(^[0-9]{18}$)|(^[0-9]{17}([0-9]|X|x)$)/;
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '商家管理',
        link: 'store_mag',
    },{
        name: '添加商家',
    }];

    $scope.type_org_arr = [{type: "个体工商户", id: 0}, {type: "企业", id: 1}]; //单位类型数组
    $scope.type_shop_arr = [{storetype: "旗舰店", id: 0}, {storetype: "专营店", id: 2}, {storetype: "专卖店", id: 1},{storetype: "自营店", id: 3}]; //店铺类型数组
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
    $scope.legalwarning = false;     //法人名称错误提示

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
        _ajax.get('/mall/categories-manage-admin',{},function (res) {
            let data = res.data;
            data.categories.splice(0, 1);
            $scope.firstclass = data.categories;
            $scope.firstselect = data.categories[0].id;
        })

        _ajax.get('/mall/categories-manage-admin',{pid: 1},function (res) {
            let data = res.data;
            data.categories.splice(0, 1);
            $scope.secondclass = data.categories;
            $scope.secselect = $scope.secondclass[0].id;
        })

        _ajax.get('/mall/categories-manage-admin',{pid: 2},function (res) {
            let data = res.data;
            data.categories.splice(0, 1);
            $scope.thirdclass = data.categories;
            $scope.params.category_id = $scope.thirdclass[0].id;
        })
    }


    // 一级联动
    $scope.subClass = (obj) => {
        _ajax.get('/mall/categories-manage-admin',{pid: obj},function (res) {
            let data = res.data;
            data.categories.splice(0, 1);
            $scope.secondclass = data.categories;
            $scope.secselect = data.categories[0].id;
            _ajax.get('/mall/categories-manage-admin',{pid: $scope.secselect},function (res) {
                let data = res.data;
                data.categories.splice(0, 1);
                $scope.thirdclass = data.categories;
                $scope.params.category_id = data.categories[0].id;
            })
        })
    };


    // 二级联动
    $scope.thirdClass = function (obj) {
        _ajax.get('/mall/categories-manage-admin',{pid: obj},function (res) {
            let data = res.data;
            data.categories.splice(0, 1);
            $scope.thirdclass = data.categories;
            $scope.params.category_id = data.categories[0].id;
        })
    }

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
            case 3:
                $scope.store_type = '自营店';
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
                $scope.licencepath = response.data.data.file_path;
            }
        }, function (error) {
            console.log(error)
        })
    }


    /*登录账户判断*/
    $scope.accountCheck = function () {
        $scope.defaultshow = false;
        $scope.legalwarning = false;
        $scope.idwarning = false;
        if(phone_pattern.test($scope.params.mobile)){
            _ajax.get('/mall/check-role-get-identity',{mobile: Number($scope.params.mobile)},function (res) {
                if (res.code == 1011 || res.code == 1010) {
                    //已注册的商家和未成为平台用户
                    $scope.showwarning = true;
                    $scope.show_account_warning = true;
                    $scope.account_warning = res.msg;
                } else if (res.code == 200) {
                    $scope.showwarning = false;
                    $scope.result = res.data;
                    $scope.defaultshow = true;
                    $scope.show_account_warning = false;
                    /*未实名认证的商家*/
                    if (!$scope.result.identity.identity_no) {
                        $scope.with_au = false;
                    } else {
                        /*已实名认证的商家*/
                        $scope.with_au = true;
                        $scope._frontpath = $scope.result.identity.identity_card_front_image;
                        $scope._backpath = $scope.result.identity.identity_card_front_image;
                    }
                }
            })
        }else{
            $scope.showwarning = true;
            $scope.show_account_warning = true;
            $scope.account_warning = '请输入正确的11位手机号'
        }
    }

    /*法人名称规范判断*/
    $scope.legalCheck = function () {
        if((!(legal_pattern.test($scope.params.legal_person)))||(!$scope.params.legal_person)){
            $scope.legalwarning = true;
        }else{
            $scope.legalwarning = false;
        }
    }

    /*身份证号判断*/
    $scope.idCheck = function () {
        if((!(id_pattern.test($scope.params.identity_card_no)))||(!$scope.params.identity_card_no)){
            $scope.idwarning = true;
        }else{
            $scope.idwarning = false;
        }
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
                $scope.params.identity_card_front_image = response.data.data.file_path;
                $scope.front_warning = false;
                $scope.frontpath = response.data.data.file_path;
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
                $scope.backpath = response.data.data.file_path;
            }
        }, function (error) {
            console.log(error)
        })
    }


    /*确认添加商家*/
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
            if ((!$scope.with_au) && val && (!($scope.licencepath == picpath || $scope.frontpath == picpath || $scope.backpath == picpath))&&(!$scope.legalwarning)&&(!$scope.idwarning)&&(!$scope.showwarning)) {
                _ajax.post('/mall/supplier-add',$scope.params,function (res) {
                    wrongBack(Number(res.code),res.msg);
                })
                /*已认证的情况*/
            } else if ($scope.with_au && val && $scope.licencepath != picpath&&(!$scope.showwarning)) {
                $scope.params.legal_person = '';
                $scope.identity_card_no = '';
                $scope.identity_card_front_image = '';
                $scope.identity_card_back_image = '';
                _ajax.post('/mall/supplier-add',$scope.params,function (res) {
                    wrongBack(Number(res.code),res.msg);
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
    

    //重复判断
    function wrongBack(obj,msg) {
        switch (Number(obj)) {
            case 1010:
                $scope.show_account_warning = true;
                $scope.account_warning = msg;
                break;
            case 1011:
                $scope.show_account_warning = true;
                $scope.account_warning = msg;
                break;
            case 1028:
                $scope.storename_repeat = true;
                $scope.storename_repeatInfo = msg;
                wrongScroll('storename');
                break;
            case 1029:
                $scope.registercode_repeat = true;
                $scope.registercode_repeatInfo = msg;
                wrongScroll('registercode');
                break;
            case 1030:
                $scope.companyname_repeat = true;
                $scope.companyname_repeatInfo = msg;
                wrongScroll('companyname');
                break;
            case 200:
                $("#suremodal").modal("show");
                break;
            case 1038:
                $scope.idwarning = true;
                $scope.id_repeatInfo = msg;
                wrongScroll('idcard');
                break;
            default:
                return;
        }
    }


    //名称判断错误回滚
    function wrongScroll(obj) {
        $anchorScroll.yOffset = 150;
        $location.hash(obj);
        $anchorScroll();
        $window.document.getElementById(obj).focus();
    }


    /*确认添加成功*/
    $scope.sure = function () {
        setTimeout(() => {
            $state.go("store_mag");
        }, 200)
    }
});