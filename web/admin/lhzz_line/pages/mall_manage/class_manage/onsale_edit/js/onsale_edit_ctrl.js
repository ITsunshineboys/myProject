/**
 * Created by hulingfangzi on 2017/7/27.
 */
app.controller('onsale_edit', ['$state','$scope', '$stateParams','$http', '$rootScope', 'Upload', '_ajax', function ($state, $scope, $stateParams, $http, $rootScope, Upload, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '分类管理',
        link: 'class.online',
    }, {
        name: '分类详情'
    }];

    let pid;
    let pattern = /^[\u4E00-\u9FA5A-Za-z0-9]+$/;
    $scope.changescope = $scope;
    $scope.show_class_warning = false;
    $scope.picwarning = false;
    $scope.idarr = [];
    $scope.firstclass = [];
    /*一级下拉框*/
    $scope.subClass = [];
    /*二级下拉框*/
    $scope.item = $stateParams.item;
    $scope.iconpath = $stateParams.item.icon;
    /*图片路径*/
    $scope.savemodal = '';
    //分类路径处理
    let onlinepath = $stateParams.item.path.split(",");
    onlinepath.splice(onlinepath.length - 1, onlinepath.length);
    $scope.finalpatharr = onlinepath;
    $scope.offlinereason = '';
    $scope.config = $rootScope.config; //富文本编辑器配置


    /*分类名称是否存在的判断*/
    $scope.addClassName = function () {
        if ((!pattern.test($scope.item.title)) || $scope.item.title == '') {
            $scope.class_warning = "您的输入不满足条件,请重新输入"
            $scope.show_class_warning = true;
        } else {
            $scope.show_class_warning = false;
        }
    }

    /*分类所属 第一个下拉框的值*/
    $scope.findParentClass = (function () {
        _ajax.get('/mall/categories-manage-admin', {}, function (res) {
            $scope.firstclass = res.data.categories.splice(1);
            for (let i = 0; i < $scope.firstclass.length; i++) {
                if ($scope.firstclass[i].id == $scope.finalpatharr[0]) {
                    $scope.firstselect = $scope.firstclass[i].id;
                    break;
                }
            }
        })
    })()

    /*二级默认*/
    $scope.subClassDefault = (function () {
        _ajax.get('/mall/categories-manage-admin', {pid: $scope.finalpatharr[0]}, function (res) {
            $scope.secondclass = res.data.categories.splice(1);
            for (let i = 0; i < $scope.secondclass.length; i++) {
                if ($scope.secondclass[i].id == $scope.finalpatharr[1]) {
                    $scope.secselect = $scope.secondclass[i].id;
                    break;
                }
            }
        })
    })()

    /*一级选择后的二级*/
    $scope.subClass = function (obj) {
        _ajax.get('/mall/categories-manage-admin', {pid: obj}, function (res) {
            $scope.secondclass = res.data.categories.splice(1);
            $scope.secselect = $scope.secondclass[0].id
        })
    }

    //上传图片
    $scope.data = {
        file: null
    }
    $scope.upload = function (file) {
        if (!$scope.data.file) {
            return
        }

        Upload.upload({
            url: baseUrl + '/site/upload',
            data: {'UploadForm[file]': file}
        }).then(function (response) {
            if (!response.data.data) {
                $scope.picwarning = true;
                $scope.iconpath = 'pages/mall_manage/class_manage/onsale_edit/images/default.png';
            } else {
                $scope.picwarning = false;
                $scope.iconpath = response.data.data.file_path;
                $scope.classicon = response.data.data.file_path;
            }
        }, function (error) {
            console.log(error)
        })
    }


    /*编辑里的下架*/
    $scope.sureoffline = function () {
        console.log(123123123123)
        _ajax.post('/mall/category-status-toggle', {
            id: $scope.item.id,
            offline_reason: $scope.offlinereason
        }, function (res) {
            $scope.offlinereason = '';
            setTimeout(function () {
                $state.go("class.offline");
            }, 200)
        })
    }

    /*编辑里的下架 取消下架*/
    $scope.cancelOffInEdit = function () {
        $scope.offlinereason = '';
    }

    /*保存编辑*/
    $scope.saveclass = function () {
        if (!pattern.test($scope.item.title) || $scope.item.title == '') {
            $scope.class_warning = "您的输入不满足条件,请重新输入"
            $scope.show_class_warning = true;
            return;
        }


        if ($scope.iconpath == 'pages/mall_manage/class_manage/offsale_edit/images/default.png') {
            $scope.picwarning = true;
            return;
        }

        if ($scope.show_class_warning == false && $scope.picwarning == false) {
            let description = UE.getEditor('editor').getContent();
            if ($scope.finalpatharr.length == 3) {
                pid = $scope.secselect
            } else if ($scope.finalpatharr.length == 2) {
                pid = $scope.firstselect;
            } else {
                pid = 0;
            }

            let data = {
                id: $scope.item.id,
                title: $scope.item.title,
                pid: pid,
                icon: $scope.classicon || $stateParams.item.icon,
                description: description
            };
            _ajax.post('/mall/category-edit', data, function (res) {
                res.code == 1006 ? $scope.save_msg = res.msg : $scope.save_msg = "保存成功";
                _alert('提示', $scope.save_msg, function () {
                    if (res.code == 200) {
                        $state.go("class.online");
                    }
                })
            })
        }
    }
}])