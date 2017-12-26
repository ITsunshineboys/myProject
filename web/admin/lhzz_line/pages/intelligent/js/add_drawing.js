app.controller('add_drawing_ctrl',function ($window,$uibModal,$anchorScroll,$location,Upload, $scope, $rootScope, _ajax, $state, $stateParams) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
            link: function () {
                $state.go('intelligent.intelligent_index')
                $rootScope.crumbs.splice(1, 4)
            }
        }, {
            name: '小区列表页',
            link: -1
        }, {
            name: $stateParams.index == 1 ? '编辑小区信息' : '添加小区信息',
            link: -1
        }, {
            name: '户型详情'
        }
    ]
    $scope.house_type = []
    for(let [key,value] of JSON.parse(sessionStorage.getItem('houseInformation')).entries()){
        value['index'] = key
        if(value.is_ordinary == 0&&value.house_type_name!=''){
            console.log(value);
            $scope.house_type.push(value)
        }
    }
    //风格、系列以及楼梯结构
    _ajax.get('/quote/series-and-style', {}, function (res) {
        console.log(res)
        $scope.all_series = res.series
        $scope.all_style = res.style
        if($scope.cur_drawing.drawing_name == ''){
            $scope.cur_drawing.series = $scope.all_series[0].id
            $scope.cur_drawing.style = $scope.all_style[0].id
        }
    })
    //获取数据
    $scope.house_informations = JSON.parse(sessionStorage.getItem('houseInformation'))
    $scope.drawing_informations = JSON.parse(sessionStorage.getItem('drawingInformation'))
    $scope.cur_drawing = $scope.drawing_informations[$stateParams.cur_index]

    if($scope.cur_drawing.drawing_name == ''){
        console.log($scope.house_type);
        Object.assign($scope.cur_drawing,{
            all_drawing:[],
            series:'',
            style:'',
            index:$scope.house_type[0].index
        })
    }else{

    }
    /*上传图片*/
    $scope.drawing_error = ''
    $scope.data = {
        file: null
    }
    //上传
    $scope.upload = function (file) {
        $scope.drawing_error = ''
        if (file != null) {
            Upload.upload({
                url: '/site/upload',
                data: {'UploadForm[file]': file}
            }).then(function (res) {
                if(res.data.code == 200){
                        $scope.cur_drawing.all_drawing.push(res.data.data.file_path)
                        $scope.drawing_error = ''
                }else{
                    $scope.drawing_error = '上传图片格式不正确或尺寸不匹配，请重新上传'

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
    //删除图纸图片
    $scope.deleteDrawing = function (index) {
        $scope.cur_drawing.all_drawing.splice(index,1)
    }
    //保存图纸
    $scope.saveDrawing = function (valid) {
        let index = $scope.drawing_informations.findIndex(function (item) {
            return item.index == $scope.cur_drawing.index&&item.series == $scope.cur_drawing.series&&item.style == $scope.cur_drawing.style
        })
        let index1 = angular.copy($scope.drawing_informations).findIndex(function (item) {
            return item.index == $scope.cur_drawing.index&&item.series == $scope.cur_drawing.series&&item.style == $scope.cur_drawing.style
        })
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        let all_modal1 = function ($scope, $uibModalInstance) {
            $scope.cur_title = '图纸内容重复,请重新选择'
            $scope.common_house = function () {
                $uibModalInstance.close()
            }
        }
        all_modal1.$inject = ['$scope', '$uibModalInstance']
        if(index == index1){
            if($scope.cur_drawing.all_drawing.length > 0&&valid){
                $scope.drawing_informations[$stateParams.cur_index] = $scope.cur_drawing
                sessionStorage.setItem('drawingInformation',JSON.stringify($scope.drawing_informations))
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            }else if($scope.cur_drawing.all_drawing.length == 0){
                $scope.drawing_error = '请上传图片'
            }else{
                $scope.submitted = true
            }
        }else{
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: all_modal1
            })
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})