/**
 * Created by Administrator on 2017/12/12/011.
 */
app.controller('edit_class', ['$state', '$rootScope', '$scope', '$stateParams', '_ajax', 'Upload', function ($state, $rootScope, $scope, $stateParams, _ajax, Upload) {
    $rootScope.crumbs = [{
        name: '分类管理',
        icon: 'icon-classification',
        link: 'class_manage'
    }, {
        name: '分类详情',
        link: -1,
    }, {
        name: '编辑分类'
    }];

    $scope.id = $stateParams.id;
    $scope.class = {
        title: $stateParams.title
    }

    let pid;
    let pattern = /^[\u4E00-\u9FA5A-Za-z0-9]+$/;
    let iconpath = 'pages/class_manage/images/default.png';
    $scope.showtishi = false;
    $scope.idarr = [];
    $scope.picwarning = false;
    $scope.iconpath = 'pages/class_manage/images/default.png';
    $scope.picwarningtext = '';
    $scope.classicon = '';

    /*富文本编辑器初始化*/
    $scope.config = $rootScope.config;


    // 默认商品详情
    _ajax.get('/supplieraccount/supplier-cate-view', {cate_id: $stateParams.id}, function (res) {
        $scope.defaultDetail = res.data;
        let onlinepath = $scope.defaultDetail.path.split(",");    // 分类路径处理
        $scope.finalpatharr = onlinepath;
        selectDefault();
    })

    // 分类所属 默认一二级下拉框
    function selectDefault() {
        _ajax.get('/mall/categories-manage-admin', {}, function (res) {
            $scope.firstclass = res.data.categories.splice(1);
            for (let i = 0; i < $scope.firstclass.length; i++) {
                if ($scope.firstclass[i].id == $scope.finalpatharr[0]) {
                    $scope.firstselect = $scope.firstclass[i].id;
                    break;
                }
            }
        })

        _ajax.get('/mall/categories-manage-admin', {pid: $scope.finalpatharr[0]}, function (res) {
            $scope.secondclass = res.data.categories.splice(1);
            for (let i = 0; i < $scope.secondclass.length; i++) {
                if ($scope.secondclass[i].id == $scope.finalpatharr[1]) {
                    $scope.secselect = $scope.secondclass[i].id;
                    break;
                }else{
                    $scope.secselect = '0';
                }
            }
        })

    }


    /*分类名称是否存在的判断*/
    $scope.checkClassName = function () {
        if (!pattern.test($scope.class.title) || !$scope.class.title) {
            $scope.tishi = "您的输入不满足条件,请重新输入"
            $scope.showtishi = true;
        } else {
            $scope.showtishi = false;
        }
    }


    /*一级选择后的二级*/
    $scope.subClass = function (obj) {
        _ajax.get('/mall/categories-manage-admin', {pid: obj}, function (res) {
            $scope.secondclass = res.data.categories.splice(1);
            $scope.secselect = '0';
        })
    }


    //上传图片
    $scope.data = {
        file: null
    }
    $scope.upload = function (file) {
        $scope.picwarning = false;
        if (!$scope.data.file) {
            return
        }
        Upload.upload({
            url: baseUrl + '/site/upload',
            data: {'UploadForm[file]': file}
        }).then(function (response) {
            if (!response.data.data) {
                $scope.picwarningtext = '上传图片格式不正确或尺寸不匹配，请重新上传';
                $scope.picwarning = true;
                $scope.defaultDetail.icon = iconpath;
            } else {
                $scope.picwarning = false;
                $scope.defaultDetail.icon = response.data.data.file_path;
                // $scope.classicon = response.data.data.file_path;
            }
        }, function (error) {
            console.log(error)
        })
    }

    /*确认添加分类*/
    $scope.sureaddclass = function () {
        if (!pattern.test($scope.class.title) || !$scope.class.title) {
            $scope.tishi = "您的输入不满足条件,请重新输入"
            $scope.showtishi = true;
            return;
        }

        if ($scope.defaultDetail.icon == iconpath) {
            $scope.picwarningtext = '请上传图片';
            $scope.picwarning = true;
            return;
        }

        if ($scope.showtishi == false && $scope.picwarning == false) {
            let description = UE.getEditor('editor').getContent();
            $scope.firstselect != 0 && $scope.secselect == 0 ? pid = $scope.firstselect : pid = $scope.secselect;
            let data = {
                cate_id: $stateParams.id,
                title: $scope.class.title,
                pid: pid,
                icon: $scope.defaultDetail.icon,
                description: description
            };
            _ajax.post('/supplieraccount/supplier-cate-edit', data, function (res) {
                $("#save_tishi").modal("show");
                if (res.code == 200) {
                    $scope.save_msg = "保存成功"
                    $scope.success_flag = true;
                } else {
                    $scope.save_msg = res.msg;
                    $scope.success_flag = false;
                }
            })
        }
    }

    //*保存模态框确认*/
    $scope.suresave = function () {
        setTimeout(function () {
            $state.go("class_detail", {id: $scope.id});
        }, 200)
    }
}]);

