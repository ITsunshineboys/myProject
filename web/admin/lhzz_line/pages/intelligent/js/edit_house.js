app.controller('edit_house_ctrl', function ($window,$uibModal,$anchorScroll,$location,Upload, $scope, $rootScope, _ajax, $state, $stateParams) {
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
    //风格、系列以及楼梯结构
    _ajax.get('/quote/series-and-style', {}, function (res) {
        console.log(res)
        $scope.all_series = res.series
        $scope.all_style = res.style
        $scope.all_stair = res.stairs_details
        $scope.cur_house.series = $scope.all_series[0].id
        $scope.cur_house.style = $scope.all_style[0].id
        $scope.cur_house.stair = $scope.all_stair[0].id
    })
    //获取数据
    $scope.house_informations = JSON.parse(sessionStorage.getItem('houseInformation'))
    $scope.cur_house = $scope.house_informations[$stateParams.cur_index]
    console.log($scope.cur_house);
    if ($scope.cur_house.house_type_name == '') {
        Object.assign($scope.cur_house, {
            area: '',
            cur_room: 1,
            cur_hall: 1,
            cur_toilet: 1,
            cur_kitchen: 1,
            cur_imgSrc: '',
            have_stair: 1,
            stair: 1,
            high: 2.8,
            window: '',
            hall_area: '',
            hall_girth: '',
            room_area: '',
            room_girth: '',
            toilet_area: '',
            toilet_girth: '',
            kitchen_area: '',
            kitchen_girth: '',
            other_length: '',
            flattop_area: '',
            balcony_area: '',
            drawing_list:[]
        })
    } else {

    }
    //改变户型数据
    $scope.changeQuantity = function (item, limit, index) {
        if (index == 1) {
            if ($scope[item.split('.')[0]][item.split('.')[1]] >= limit) {
                $scope[item.split('.')[0]][item.split('.')[1]] = limit
            } else {
                $scope[item.split('.')[0]][item.split('.')[1]]++
            }
        } else {
            if ($scope[item.split('.')[0]][item.split('.')[1]] <= limit) {
                $scope[item.split('.')[0]][item.split('.')[1]] = limit
            } else {
                $scope[item.split('.')[0]][item.split('.')[1]]--
            }
        }
    }
    /*上传图片*/
    $scope.img_error = ''
    $scope.data = {
        file: null
    }
    $scope.upload_txt = '上传'
    //上传
    $scope.upload = function (file) {
        $scope.img_error = ''
        if (file != null) {
            $scope.upload_txt = '上传中...'
            Upload.upload({
                url: '/site/upload',
                data: {'UploadForm[file]': file}
            }).then(function (res) {
                if (res.data.code == 200) {
                    $scope.cur_house.cur_imgSrc = res.data.data.file_path
                    $scope.upload_txt = '上传'
                    $scope.img_error = ''
                } else {
                    index == 0 ? $scope.img_error = '上传图片格式不正确或尺寸不匹配，请重新上传' : $scope.drawing_error = '上传图片格式不正确或尺寸不匹配，请重新上传'

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

    //保存普通户型
    $scope.saveHouse = function (valid,error) {
        console.log(error);
        let all_modal = function ($scope, $uibModalInstance) {
            $scope.cur_title = '保存成功'
            $scope.common_house = function () {
                $uibModalInstance.close()
                history.go(-1)
            }
        }
        all_modal.$inject = ['$scope', '$uibModalInstance']
        if(valid&&$scope.cur_house.cur_imgSrc != ''){
            $scope.submitted = false
            $scope.house_informations[$stateParams.cur_index] = $scope.cur_house
            sessionStorage.setItem('houseInformation',JSON.stringify($scope.house_informations))
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: all_modal
            })
        }else if($scope.cur_house.cur_imgSrc == ''){
            $scope.img_error = '请上传图片'
            $anchorScroll.yOffset = 150
            $location.hash('imgSrc')
            $anchorScroll()
        }else{
            $scope.submitted = true
            if (!valid) {
                for (let [key, value] of error.entries()) {
                    if (value.$invalid) {
                        $anchorScroll.yOffset = 150
                        $location.hash(value.$name)
                        $anchorScroll()
                        $window.document.getElementById(value.$name).focus()
                        break
                    }
                }
            }
        }
    }
    //返回上一页
    $scope.goPrev = function () {
        $scope.submitted = false
        history.go(-1)
    }
})