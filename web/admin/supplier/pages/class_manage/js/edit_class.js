/**
 * Created by Administrator on 2017/12/12/011.
 */
app.controller('edit_class', ['$state', '$rootScope', '$scope', '$stateParams', '_ajax','Upload', function ($state, $rootScope, $scope, $stateParams, _ajax, Upload) {
    $rootScope.crumbs = [{
        name: '分类管理',
        icon: 'icon-classification',
        link: 'class_manage'
    },{
        name: '分类详情',
        link: -1,
    },{
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

    /*分类名称是否存在的判断*/
    $scope.checkClassName = function () {
        if (!pattern.test($scope.class.title)||!$scope.class.title) {
            $scope.tishi = "您的输入不满足条件,请重新输入"
            $scope.showtishi = true;
        }else{
            $scope.showtishi = false;
        }
    }

    /*分类所属 下拉框默认*/
    $scope.defaultClass =  (function () {
        _ajax.get('/mall/categories-manage-admin',{},function (res) {
            $scope.firstclass = res.data.categories;
            $scope.firstselect = '1';
        })

        _ajax.get('/mall/categories-manage-admin',{pid:1},function (res) {
            $scope.secondclass = res.data.categories;
            $scope.secselect = '0';
        })
    })()


    /*一级选择后的二级*/
    $scope.subClass = function (obj) {
        if (obj != '') {
            $scope.showsub = true;
            _ajax.get('/mall/categories-manage-admin',{pid: obj},function (res) {
                $scope.secondclass = res.data.categories;
                $scope.secselect = '0';
            })
        }
    }

    //上传图片
    $scope.data = {
        file:null
    }
    $scope.upload = function (file) {
        $scope.picwarning = false;
        if(!$scope.data.file){
            return
        }
        Upload.upload({
            url:baseUrl+'/site/upload',
            data:{'UploadForm[file]':file}
        }).then(function (response) {
            if(!response.data.data){
                $scope.picwarningtext = '上传图片格式不正确或尺寸不匹配，请重新上传';
                $scope.picwarning = true;
                $scope.iconpath = iconpath;
            }else{
                $scope.picwarning = false;
                $scope.iconpath = response.data.data.file_path;
                $scope.classicon = response.data.data.file_path;
            }
        },function (error) {
            console.log(error)
        })
    }

    /*确认添加分类*/
    $scope.sureaddclass = function () {
        if (!pattern.test($scope.class.title)||!$scope.class.title) {
            $scope.tishi = "您的输入不满足条件,请重新输入"
            $scope.showtishi = true;
            return;
        }

        if($scope.iconpath == iconpath){
            $scope.picwarningtext = '请上传图片';
            $scope.picwarning = true;
            return;
        }

        if($scope.showtishi==false&&$scope.picwarning==false&&$scope.classicon!=''){
            let description = UE.getEditor('editor').getContent();
            $scope.firstselect != 0 && $scope.secselect == 0? pid = $scope.firstselect : pid = $scope.secselect;
            let data =  {
                cate_id:$stateParams.id,
                title:$scope.class.title,
                pid:pid,
                icon:$scope.classicon,
                description:description
            };
            _ajax.post('/supplieraccount/supplier-cate-edit',data,function (res) {
                $("#save_tishi").modal("show");
                if(res.code == 200){
                    $scope.save_msg="保存成功"
                    $scope.success_flag = true;
                }else if(res.code == 1006){
                    $scope.save_msg = res.msg;
                    $scope.success_flag = false;
                }
            })
        }
    }

    //*保存模态框确认*/
    $scope.suresave = function () {
        setTimeout(function () {
            $state.go("class_detail",{id:$scope.id});
        },200)
    }
}]);

