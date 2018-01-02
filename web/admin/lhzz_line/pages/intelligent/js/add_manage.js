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
    $scope.recommend_name = ''
    $scope.vm = $scope
    let obj = JSON.parse(sessionStorage.getItem('area'))
    _ajax.get('/quote/homepage-district',{
        province:obj.province,
        city:obj.city
    },function (res) {
        console.log(res);
        $scope.district = res.list
        if($scope.district.length != 0){
            $scope.choose_district = $scope.district[0].district_code
            _ajax.post('/quote/homepage-toponymy',{
                province:obj.province,
                city:obj.city,
                district:$scope.choose_district
            },function (res) {
                console.log(res);
            })
        }
    })
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
        if(valid){

        }else{
            $scope.submitted = true
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})