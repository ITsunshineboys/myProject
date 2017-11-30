/**
 * Created by Administrator on 2017/9/21/021.
 */
let applybrand = angular.module("applybrandModule", []);
applybrand.controller("applybrand_ctrl", function ($scope, $rootScope, $state, Upload, _ajax) {
    $rootScope.crumbs = [{
        name: '店铺管理',
        icon: 'icon-dianpuguanli',
        link: 'shop_manage'
    },{
        name: '申请新品牌'
    }];
    $scope.uploadDisabled = false;  // 上传按钮禁用
    $scope.startTimeIsNull = false; // 初始日期是否为空
    $scope.endTimeIsNull = false;   // 结束日期是否为空
    // 请求参数
    $scope.params = {
        category_id: '',            // 店铺类目
        brand_id: '',               // 品牌
        authorization_start: '',    // 授权期限开始日期
        authorization_end: '',      // 授权期限结束日期
        authorization_names: [],    // 授权书名称
        images: []                  // 授权书图片
    };

    // 首张授权书上传 Obj
    $scope.licensed = {
        file: '',           // 上传文件
        name: '',           // 图片名称
        path: '',           // 图片路径
        text: '上传',       // 上传按钮文字
        isName: false,     // 是否填写名字
        isNull: false,     // 是否上传图片
        isPattern: false   // 上传图片格式是否正确
    };

    // 添加授权书上传 Array
    $scope.licensedArray = [];

    // 添加授权书 Function
    $scope.addLicensed = function () {
        $scope.licensedArray.push({
            file: '',           // 上传文件
            name: '',           // 图片名称
            path: '',           // 图片路径
            text: '上传',       // 上传按钮文字
            isName: false,     // 是否填写名字
            isNull: false,     // 是否上传图片
            isPattern: false   // 上传图片格式是否正确
        })
    };

    // 删除授权书
    $scope.delLicensed = function (idx) {
        $scope.licensedArray.splice(idx, 1)
    };

    // 提交申请
    $scope.licenseSubmit = function () {
        let licensedArray = $scope.licensedArray;
        if ($scope.params.authorization_start === '') {
            $scope.startTimeIsNull = true;
            return false;
        }
        if ($scope.params.authorization_end === '') {
            $scope.endTimeIsNull = true;
            return false;
        }
        if ($scope.licensed.name === '') {
            $scope.licensed.isName = true;
            return false;
        }
        if ($scope.licensed.path === '') {
            $scope.licensed.isNull = true;
            return false;
        }
        for (let i = 0; i < licensedArray.length; i++) {
            if (licensedArray[i].name === '') {
                licensedArray[i].isName = true;
                return false;
            }
            if (licensedArray[i].path === '') {
                licensedArray[i].isNull = true;
                return false;
            }
        }
        $scope.params.authorization_names.push($scope.licensed.name);   // 授权书名称
        $scope.params.images.push($scope.licensed.path);                // 授权书图片
        for (let i = 0; i < licensedArray.length; i++) {
            let obj = licensedArray[i];
            $scope.params.authorization_names.push(obj.name);   // 授权书名称
            $scope.params.images.push(obj.path);                // 授权书图片
        }

        _ajax.post('/mall/brand-application-add', $scope.params, function (res) {
            $('#sure_addbrand').modal('show')
        })
    };

    // 图片上传
    $scope.upload = function (file, valid, name, type, idx) {
        const baseUrl = $rootScope.baseUrl;
        // 判断是否初始化数据
        if (file === null && valid.length === 0) {
            return false
        }
        // 判断文件格式是否正确
        if (valid.length === 0) {   // 正确
            // 取消提示
            showTip(false, name, type, idx);
        } else {
            // 显示提示
            showTip(true, name, type, idx);
            return false
        }
        $scope.uploadDisabled = true;
        if (type !== 'array') {
            $scope[name].text = '上传中...';
        } else {
            $scope[name][idx].text = '上传中...';
        }
        Upload.upload({
            url: baseUrl + '/site/upload',
            data: {'UploadForm[file]': file}
        }).then((resp) => {
            // 上传成功
            if (type !== 'array') {
                $scope[name].text = '上传';
                $scope[name].path = resp.data.data.file_path;
            } else {
                $scope[name][idx].text = '上传';
                $scope[name][idx].path = resp.data.data.file_path;
            }
            $scope.uploadDisabled = false;
            console.log(resp, '上传成功');
        }, (resp) => {
            // 上传失败
            $scope[name].text = '上传';
            $scope[name][idx].text = '上传';
            $scope.uploadDisabled = false;
            alert('上传失败');
            console.log('Error status: ' + resp.status, '上传失败');
        }, (evt) => {
            // console.log(evt, '上传进度');
        })
    };
    // 所属分类-级联
    cascadeData();
    function cascadeData() {
        _ajax.get('/mall/categories-manage-admin', {}, function (res) {
            let data = res.data;
            $scope.firstclass = data.categories;
            $scope.firstselect = data.categories[0].id;
        });
        _ajax.get('/mall/categories-manage-admin', {pid: 1}, function (res) {
            let data = res.data;
            $scope.secondclass = data.categories;
            $scope.secselect = $scope.secondclass[0].id;
        });
        _ajax.get('/mall/categories-manage-admin', {pid: 2}, function (res) {
            let data = res.data;
            $scope.thirdclass = data.categories;
            $scope.params.category_id = $scope.thirdclass[0].id;
        })
    }
    // 一级联动
    $scope.subClass = (obj) => {
        /*二级下拉框内容*/
        _ajax.get('/mall/categories-manage-admin', {pid: obj}, function (res) {
            let data = res.data;
            $scope.secondclass = data.categories;
            $scope.secselect = data.categories[0].id;
            _ajax.get('/mall/categories-manage-admin', {pid: $scope.secselect}, function (res) {
                let data = res.data;
                $scope.thirdclass = data.categories;
                $scope.params.category_id = data.categories[0].id;
            })
        });
    };
    // 二级联动
    $scope.thirdClass = function (obj) {
        _ajax.get('/mall/categories-manage-admin', {pid: obj}, function (res) {
            let data = res.data;
            $scope.thirdclass = data.categories;
            $scope.params.category_id = data.categories[0].id;
        })
    };
    /*品牌*/
    $scope.$watch('params.category_id', function (newVal, oldVal) {
        if (newVal === oldVal) {
            return false
        }
        let params = {
            category_id: newVal,
            "fields[]": "brands",
        };
        _ajax.get('/mall/category-brands-styles-series', params, function (res) {
            let data = res.data;
            $scope.brandslist = data.category_brands_styles_series.brands;
            $scope.params.brand_id = data.category_brands_styles_series.brands[0].id;
        });
    });

    $scope.closeModal = function () {
        $('#sure_addbrand').modal('hide').on('hidden.bs.modal', function () {
            $state.go('shop_manage',{authorize_flag: true})
        });
    };

    /**
     * 显示上传文件格式错误提示
     * @param boolean
     * @param name  str     "licensed" or "licensedArray"
     * @param type  str     可不传 or "array"
     * @param idx   number  数组下标，和 type 一起传入
     */
    function showTip(boolean, name, type, idx) {
        if (type !== 'array') {
            $scope[name].isPattern = boolean;
            $scope[name].isNull = boolean
        } else {
            $scope[name][idx].isPattern = boolean;
            $scope[name][idx].isNull = boolean
        }
    }
});