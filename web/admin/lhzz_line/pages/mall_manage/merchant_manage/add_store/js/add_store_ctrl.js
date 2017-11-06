var add_store = angular.module("addstoreModule",['ngFileUpload']);
add_store.controller("addstore",function ($scope,$http,Upload,$location,$anchorScroll,$window,$state) {
	/*分类选择一级下拉框*/
    firstDefault();
    secDefault();
    thirdDefault();
    // allstore();
    // const storename_arr = [];
    const picpath = 'pages/mall_manage/merchant_manage/add_store/images/default.png'
    const picprefix = baseUrl+"/";
    const pattern = /^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/;
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    };
    $scope.companytype_arr = [{type:"个体工商户",id:0},{type:"企业",id:1}];
    $scope.unittype = $scope.companytype_arr[0].id; /*单位类型初始*/
    $scope.storetype_arr = [{storetype: "旗舰店", id: 0}, {storetype: "专营店", id: 2}, {storetype: "专卖店", id: 1}];
    $scope.typeselect = $scope.storetype_arr[0];
    $scope.change = false;
    $scope.alljudgefalse = false;
    $scope.showwarning = false;
    $scope.licenceflag = false;
    $scope.frontpathflag = false;
    $scope.backpathflag = false;
    $scope.login_account = '';
    $scope.legal_person ='';
    $scope.identity_no ='';
    $scope.front_image = '';
    $scope.back_image = '';
    $scope.accountdefault = false;
    $scope.licencepath = picpath;
    $scope.frontpath = picpath;
    $scope.backpath = picpath;
    $scope.support = 0;
    
    // test();
    // function test() {
    //     let url = baseUrl+"/mall/user-add";
    //     let data = {
    //        mobile:13093355262,
    //         password:'123456'
    //     };
    //     $http.post(url, data, config).then(function (res) {
    //         console.log(res)
    //         // $scope.suremodal = '#suremodal';
    //     })
    // }

 function firstDefault() {
        $http({
            method: "get",
            url: baseUrl+"/mall/categories-manage-admin",
        }).then(function (response) {
            response.data.data.categories.splice(0,1);
            $scope.firstclass = response.data.data.categories;
            $scope.firstselect = response.data.data.categories[0].id;
        })
    }

    function secDefault() {
        $http({
            method: "get",
            url: baseUrl+"/mall/categories-manage-admin",
            params: {pid: 1}
        }).then(function (res) {
            res.data.data.categories.splice(0,1);
            $scope.secondclass = res.data.data.categories;
            $scope.secselect=$scope.secondclass[0].id;
        })
    }

    function thirdDefault() {
        $http({
            method: "get",
            url: baseUrl+"/mall/categories-manage-admin",
            params: {pid: 2}
        }).then(function (res) {
            res.data.data.categories.splice(0,1);
            $scope.thirdclass = res.data.data.categories;
            $scope.thirdselect=$scope.thirdclass[0].id;
        })
    }


    $scope.subClass = function (obj) {
		/*二级下拉框内容*/
        $http({
            method: "get",
            url: baseUrl+"/mall/categories-manage-admin",
            params: {pid: obj}
        }).then(function (response) {
           	response.data.data.categories.splice(0,1);
            $scope.secondclass = response.data.data.categories
			$scope.secselect = response.data.data.categories[0].id;
            $http({
                method: "get",
                url: baseUrl+"/mall/categories-manage-admin",
                params: {pid: $scope.secselect}
            }).then(function (response) {
                response.data.data.categories.splice(0,1);
                $scope.thirdclass = response.data.data.categories;
                $scope.thirdselect = response.data.data.categories[0].id;
            })
        })
    }

    $scope.thirdClass = function (obj) {
        $http({
            method: "get",
            url: baseUrl+"/mall/categories-manage-admin",
            params: {pid: obj}
        }).then(function (response) {
            response.data.data.categories.splice(0,1);
            $scope.thirdclass = response.data.data.categories;
            $scope.thirdselect = response.data.data.categories[0].id;
        })
    }

        /*上传图片*/
        $scope.data = {
            filefirst: null,
            filefront: null,
            fileback: null
        }

        /*营业执照上传*/
        $scope.licenseUpload = function (file) {
            $scope.licenceflag = false;
            if (!$scope.data.filefirst) {
                return
            }
            Upload.upload({
                url: baseUrl+'/site/upload',
                data: {'UploadForm[file]': file}
            }).then(function (response) {
                if (!response.data.data) {
                    $scope.licence_warning = true;
                    $scope.licencepath = 'pages/mall_manage/merchant_manage/add_store/images/default.png';
                } else {
                    $scope.addlicense = response.data.data.file_path;
                    $scope.licence_warning = false;
                    $scope.licencepath = picprefix + response.data.data.file_path;
                }
            }, function (error) {
                console.log(error)
            })
        }


        /*身份证正面上传*/
        $scope.frontUpload = function (file) {
            $scope.frontpathflag = false;
            if (!$scope.data.filefront) {
                return
            }

            Upload.upload({
                url: baseUrl+'/site/upload',
                data: {'UploadForm[file]': file}
            }).then(function (response) {
                if (!response.data.data) {
                    $scope.front_warning = true;
                    $scope.frontpath = 'pages/mall_manage/merchant_manage/add_store/images/default.png';
                } else {
                    // $scope.
                    $scope.frontpic =  response.data.data.file_path;
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
                url: baseUrl+'/site/upload',
                data: {'UploadForm[file]': file}
            }).then(function (response) {
                if (!response.data.data) {
                    $scope.back_warning = true;
                    $scope.backpath = 'pages/mall_manage/merchant_manage/add_store/images/default.png';
                } else {
                    $scope.backpic = response.data.data.file_path;
                    $scope.back_warning = false;
                    $scope.backpath = picprefix + response.data.data.file_path;
                }
            }, function (error) {
                console.log(error)
            })
        }

        /*登录账户判断*/
        $scope.accountCheck = function () {
            // console.log($scope.thirdselect)
            $scope.accountnumjudge = false;
            $scope.accountwarning = '';
            $scope.showwarning = false;
            $scope.defaultshow = false;
            if(!$scope.login_account){
                /*未输入和初始化的处理*/
                return $scope.accountnumjudge=true;
            }else{
                if(pattern.test($scope.login_account)){
                    $http({
                        method: "get",
                        url: baseUrl+"/mall/check-role-get-identity",
                        params: {mobile: Number($scope.login_account)}
                    }).then(function (res) {
                        console.log(res);
                        //  if (!res.data.data) {
                       //      /*未注册平台用户 或已成为商家的处理*/
                       //      $scope.showwarning = true;
                       //      $scope.accountwarning = res.data.msg;
                       //  }else{
                       //      /*输入的是手机号 未实名认证的商家*/
                       //      if(!res.data.data.identity.identity_no){
                       //          $scope.defaultshow = true;
                       //          $scope.accountdefault = false;
                       //      }else{
                       //          /*输入的是手机号  已实名认证的商家*/
                       //          $scope.legal_person = res.data.data.identity.legal_person;
                       //          $scope.identity_no = res.data.data.identity.identity_no;
                       //          $scope.frontdefault = res.data.data.identity.identity_card_front_image;
                       //          $scope.backdefault = res.data.data.identity.identity_card_front_image;
                       //          $scope._frontpath = picprefix + res.data.data.identity.identity_card_front_image;
                       //          $scope._backpath =  picprefix + res.data.data.identity.identity_card_front_image;
                       //          $scope.defaultshow = true;
                       //          $scope.accountdefault = true;
                       //      }
                       // }
                    })
                }else{
                    /*输入的不是手机号的处理*/
                    $scope.showwarning = true;
                    $scope.accountwarning = "请输入正确的11位手机号";
                }
            }
    }

    $scope.sureAddStore = function (val,error) {
        $scope.licence_warning = false;
        $scope.front_warning = false;
        $scope.back_warning = false;
        /*图片上传判断*/
        if ($scope.licencepath == picpath || $scope.frontpath == picpath || $scope.backpath == picpath) {
            console.log(1231231)
            $scope.picflag = false;
            if ($scope.licencepath == picpath) {
                $scope.licenceflag = true;
            }
            if ($scope.frontpath == picpath) {

                $scope.frontpathflag = true;
            }
            if ($scope.backpath == picpath) {
                $scope.backpathflag = true;
            }
        } else {
            $scope.picflag = true;
        }

             /*默认的情况*/
        if ($scope.accountdefault) {
            $scope.legalperson = 'default';
            $scope.idcard = 622626199403253024;
            if (val) {
                let url = baseUrl+"/mall/supplier-add";
                let data = {
                    type_org: +$scope.unittype, //单位类型
                    category_id: +$scope.thirdselect, //店铺分类
                    type_shop: +$scope.typeselect.id,//店铺类型
                    shop_name: $scope.storename,//店铺名称
                    name: $scope.companyname,//公司名称
                    licence: $scope.registercode,//社会信用代码
                    licence_image: $scope.addlicense,//营业执照
                    legal_person: $scope.legal_person,//法人名称
                    identity_card_no: $scope.identity_no,//IDcard
                    identity_card_front_image: $scope.frontdefault,//身份证正面
                    identity_card_back_image: $scope.backdefault,//身份证背面
                    mobile: +$scope.login_account,//登录账号
                    support_offline_shop: $scope.support//是否支持无登录购买
                };
                $scope.suremodal = '#suremodal';
                $http.post(url, data, config).then(function (res) {
                    console.log(res)
                    // if(res.data.code==200){

                    // setTimeout(()=>{
                    //     $state.go("store_mag");
                    // },200)
                    // }
                })
            } else {
                $scope.alljudgefalse = true;
                $scope.suremodal = '';
            }

            if (!val) {
                // console.log(val);
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

        } else {
            if (val && $scope.picflag) {

                /*right*/
                let url = baseUrl+"/mall/supplier-add";
                let data = {
                    type_org: +$scope.unittype, //单位类型
                    category_id: +$scope.thirdselect, //店铺分类
                    type_shop: +$scope.typeselect.id,//店铺类型
                    shop_name: $scope.storename,//店铺名称
                    name: $scope.companyname,//公司名称
                    licence: $scope.registercode,//社会信用代码
                    licence_image: $scope.addlicense,//营业执照
                    legal_person: $scope.legalperson,//法人名称
                    identity_card_no: $scope.idcard,//IDcard
                    identity_card_front_image: $scope.frontpic,//身份证正面
                    identity_card_back_image: $scope.backpic,//身份证背面
                    mobile: +$scope.login_account,//登录账号
                    support_offline_shop: $scope.support//是否支持无登录购买
                };
                $scope.suremodal = '#suremodal';
                $http.post(url, data, config).then(function (res) {

                    // console.log(res);
                    // if(res.data.code==200){
                    //
                    //
                    // }
                })
            } else {
                $scope.alljudgefalse = true;
                $scope.suremodal = '';
            }

            if (!val) {
                // console.log(val);
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
             $scope.sureAddStore = function () {
                 setTimeout(()=>{
                     $state.go("store_mag");
                 },200)
             }

    }

});