app.controller('add_manage_ctrl', function (Upload,$scope, $rootScope, _ajax, $http, $state, $stateParams, $uibModal) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
            link: function () {
                $state.go('intelligent_index')
                $rootScope.crumbs.splice(1, 4)
            }
        }, {
            name: '首页管理',
            link: -1
        }, {
            name: $stateParams.index == 1 ? '编辑推荐' : '添加推荐'
        }
    ]
    $scope.recommend_name = ''//推荐名
    $scope.vm = $scope
    $scope.district_list = []//区列表
    $scope.toponymy_list = []//小区列表
    let obj = JSON.parse(sessionStorage.getItem('area'))
    //获取推荐信息
    _ajax.get('/quote/homepage-district',{
        city:obj.city
    },function (res) {
        console.log(res);
        $scope.district_list = res.list
        if($scope.district_list.length != 0){
            $scope.choose_district = $scope.district_list[0].district_code
            _ajax.get('/quote/homepage-toponymy',{
                district:$scope.choose_district
            },function (res) {
                console.log(res);
                $scope.toponymy_list = res.list
                $scope.choose_toponymy = $scope.toponymy_list[0]
                _ajax.get('/quote/homepage-case',{
                    toponymy_id:$scope.choose_toponymy.id
                },function (res) {
                    console.log(res);
                    $scope.case_list = res.list
                    $scope.choose_case = $scope.case_list[0].id//我认为传文字肯定有问题
                })
            })
        }
    })
    //修改获取户型数据
    $scope.getCase = function (str) {
        if(str == 'district'){
            if($scope.district_list.length != 0){
                _ajax.get('/quote/homepage-toponymy',{
                    district:$scope.choose_district
                },function (res) {
                    console.log(res);
                    $scope.toponymy_list = res.list
                    $scope.choose_toponymy = $scope.toponymy_list[0]
                    _ajax.get('/quote/homepage-case',{
                        toponymy_id:$scope.choose_toponymy.id
                    },function (res) {
                        console.log(res);
                        $scope.case_list = res.list
                        $scope.choose_case = $scope.case_list[0].id
                    })
                })
            }
        }else if(str == 'toponymy'){
            if($scope.toponymy_list.length != 0){
                _ajax.get('/quote/homepage-case',{
                    toponymy_id:$scope.choose_toponymy.id
                },function (res) {
                    console.log(res);
                    $scope.case_list = res.list
                    $scope.choose_case = $scope.case_list[0].id
                })
            }
        }
    }
    $scope.img_error = ''
    $scope.data = {
        file: null
    }
    $scope.upload_txt = '上传'
    $scope.cur_imgSrc = ''
    //上传
    $scope.upload = function (file) {
        $scope.img_error = ''
        if (file != null) {
            $scope.upload_txt = '上传中...'
            Upload.upload({
                url: '/site/upload',
                data: {'UploadForm[file]': file}
            }).then(function (res) {
                if(res.data.code == 200){
                        $scope.cur_imgSrc = res.data.data.file_path
                        $scope.upload_txt = '上传'
                        $scope.img_error = ''
                }else{
                    $scope.img_error = '上传图片格式不正确或尺寸不匹配，请重新上传'
                    // $timeout(function () {
                    //     $scope.upload_txt = '上传'
                    // },3000)
                }
                console.log(res)
            }, function (error) {
                console.log(error)
            })
        }
    }
    //保存推荐
    $scope.saveManage = function (valid) {
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if($scope.cur_imgSrc != ''){
            if(valid){
                _ajax.post('/quote/homepage-add',{
                    recommend_name:$scope.recommend_name,
                    effect_id:$scope.choose_case,
                    image:$scope.cur_imgSrc
                },function (res) {
                    console.log(res);
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                })
            }else{
                $scope.submitted = true
            }
        }else{
            $scope.img_error = '请上传图片'
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})