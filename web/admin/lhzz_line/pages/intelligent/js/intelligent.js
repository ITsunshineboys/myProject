angular.module('intelligent_index', ['ngFileUpload', 'ui.bootstrap', 'ngDraggable'])
    .controller('intelligent_ctrl', function ($scope, $state, $stateParams, $uibModal, $http, $timeout, Upload, $location, $anchorScroll, $window) {
        //公共配置以及一些变量初始化
        //post请求配置
        const config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        alert(111)
        //添加小区部分
        /*分页配置*/
        $scope.Config = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages();
            }
        }
        let tablePages=function () {
            $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
            $http.get('/quote/plot-list',{
                params:$scope.params
            }).then(function (res) {
                console.log(res);
                $scope.house_detail = res.data.model.details
                $scope.Config.totalItems = res.data.model.total
            },function (err) {
                console.log(err);
            })
        };
        $scope.params = {
            post:'',
            city:'',
            min:'',
            max:'',
            toponymy:''
        };
        $scope.getHouseList = function () {
            $scope.Config.currentPage = 1
            $scope.params.toponymy = ''
            $scope.search_txt = ''
            tablePages()
        }
        //添加材料项部分
        /*分页配置*/
        $scope.Config1 = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages1();
            }
        }
        let tablePages1=function () {
            $scope.params1.page=$scope.Config1.currentPage;//点击页数，传对应的参数
            $http.post('/quote/decoration-list',$scope.params1,config).then(function (res) {
                console.log(res);
                $scope.material_list = res.data.list.details
                $scope.Config1.totalItems = res.data.list.total
            },function (err) {
                console.log(err);
            })
        };
        $scope.params1 = {};
        //首页管理部分
        /*分页配置*/
        $scope.Config2 = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages2();
            }
        }
        let tablePages2=function () {
            $scope.params2.page=$scope.Config2.currentPage;//点击页数，传对应的参数
            $http.get('/quote/homepage-list',{
                params:$scope.params
            }).then(function (res) {
                console.log(res);
                $scope.material_list = res.data.list.details
                $scope.Config1.totalItems = res.data.list.total
            },function (err) {
                console.log(err);
            })
        };
        $scope.params2 = {};

        $scope.ctrlScope = $scope//解决ngModel无法双向绑定
        $scope.second_title = ''//二级列表项初始化
        $scope.three_title = ''//三级列表项初始化
        $scope.search_txt = ''//小区搜索文本
        // $scope.pic_error = ''
        $scope.cur_num = 0
        $scope.cur_page = 1
        $scope.is_add_manage = 1//是否跳转添加推荐页面 1是 0不是
        $scope.address = ''//小区详细地址
        $scope.house_name = ''//小区名称
        $scope.all_num = ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十"]//中文排序
        let all_level = ['白银', '黄金', '钻石']
        //默认登录状态(后期会删除)
        $http.post('/site/login', {
            'username': 13551201821,
            'password': 'demo123'
        }, config).then(function (response) {
            console.log(response)
        }, function (error) {
            console.log(error)
        })

        //获取案例需要添加的商品编号和数量
        function get_case_goods(){
            let arr = []
            $http.get('/quote/assort-goods-list').then(function (response) {
                console.log(response)
                // $scope.auxiliary_materials = []
                //整合一级
                for (let [key, value] of response.data.list.entries()) {
                    for (let [key1, value1] of response.data.classify.entries()) {
                        let cur_obj = {id: value1.id, title: value1.title, second_level: []}
                        if (value.path.split(',')[0] === value1.id && JSON.stringify(arr)
                                .indexOf(JSON.stringify(cur_obj)) === -1) {
                            arr.push(cur_obj)
                        }
                    }
                }
                //整合二级
                for (let [key, value] of response.data.list.entries()) {
                    for (let [key1, value1] of arr.entries()) {
                        for (let [key2, value2] of response.data.classify.entries()) {
                            if (value.path.split(',')[0] === value1.id && value.pid === value2.id && JSON.stringify(value1.second_level).indexOf(JSON.stringify({
                                    id: value2.id,
                                    title: value2.title,
                                    three_level: []
                                })) === -1) {
                                value1.second_level.push({id: value2.id, title: value2.title, three_level: []})
                            }
                        }
                    }
                }
                //整合三级
                for (let [key, value] of response.data.list.entries()) {
                    for (let [key1, value1] of arr.entries()) {
                        for (let [key2, value2] of value1.second_level.entries()) {
                            if (value.pid === value2.id && JSON.stringify(value2.three_level).indexOf(JSON.stringify({
                                    id: value.id,
                                    title: value.title,
                                    path: value.path,
                                    good_id: '',
                                    good_quantity: ''
                                })) === -1) {
                                value2.three_level.push({
                                    id: value.id,
                                    title: value.title,
                                    path: value.path,
                                    good_id: '',
                                    good_quantity: ''
                                })
                            }
                        }
                    }
                }
                console.log(arr)
                $scope.all_materials = arr.sort(function (prev, next) {
                    return +prev.id - next.id
                })
                $scope.all_materials_copy = angular.copy($scope.all_materials)
                console.log($scope.all_materials)
            }, function (error) {
                console.log(error)
            })
        }
        get_case_goods()
        //案例页工人
        $http.get('/quote/labor-list').then(function (response) {
            console.log(response)
            $scope.common_worker = angular.copy(response.data.labor_list)
            for (let [key, value] of $scope.common_worker.entries()) {
                if (value.worker_kind == '杂工' && key != $scope.common_worker.length - 1) {
                    value['price'] = ''
                }
                    $scope.common_worker.splice(key, 1)
                    $scope.common_worker.push(value)
            }
        }, function (error) {
            console.log(error)
        })
        //上传图片
        $scope.data = {
            file: null
        }
        //上传一张图片
        $scope.upload = function (file) {
            console.log($scope.data)
            if (file != null) {
                Upload.upload({
                    url: '/site/upload',
                    data: {'UploadForm[file]': file}
                }).then(function (response) {
                    if (!!response.data.data) {
                        if ($scope.cur_house_information != undefined) {
                            $scope.cur_house_information.cur_imgSrc = response.data.data.file_path
                        } else if ($scope.cur_image != undefined) {
                            $scope.cur_image = response.data.data.file_path
                        }
                        $scope.a_pic_error = ''
                    } else {
                        if ($scope.cur_house_information != undefined) {
                            $scope.cur_house_information.cur_imgSrc = ''
                        } else if ($scope.cur_image != undefined) {
                            $scope.cur_image = ''
                        }
                        $scope.a_pic_error = '上传图片格式不正确或尺寸不匹配,请重新上传'
                    }
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }
        }
        //上传几张图片
        $scope.arr_upload = function (file) {
            console.log($scope.all_drawing)
            if (file != null) {
                Upload.upload({
                    url: '/site/upload',
                    data: {'UploadForm[file]': file}
                }).then(function (response) {
                    console.log(response)
                    if (!response.data.data) {
                        $scope.pic_error = '上传图片格式不正确或尺寸不匹配，请重新上传'
                    } else {
                        $scope.pic_error = ''
                        $scope.all_drawing.push(response.data.data.file_path)
                    }
                }, function (error) {
                    console.log(error)
                })
            }
        }
        //删除上传图片
        $scope.upload_delete = function (item) {
            $http.post('/site/upload-delete', {
                'file_path': item
            }, config).then(function (response) {
                console.log(response)
                $scope.all_drawing.splice($scope.all_drawing.indexOf(item), 1)
            }, function (error) {
                console.log(error)
            })
        }
        //风格、系列以及楼梯结构
        $http.post('/quote/series-and-style', {}, config).then(function (response) {
            $scope.cur_all_series = response.data.series
            $scope.cur_all_style = response.data.style
            $scope.cur_all_stair = response.data.stairs_details
            console.log(response)
        }, function (error) {
            console.log(error)
        })
        //获取省市县数据(JSON)
        //初始化省市区县
        $http.get('districts2.json').then(function (response) {
            console.log((response.data[0]['86']))
            let arr = []
            let arr1 = []
            let arr2 = []
            for (let [key, value] of Object.entries(response.data[0]['86'])) {
                arr.push({'id': key, 'name': value})
            }
            $scope.province = arr
            $scope.cur_province = $scope.province[22]
            for (let [key, value] of Object.entries(response.data[0][$scope.cur_province.id])) {
                arr1.push({'id': key, 'name': value})
            }
            $scope.city = arr1
            $scope.cur_city = $scope.city[0]
            for (let [key, value] of Object.entries(response.data[0][$scope.cur_city.id])) {
                arr2.push({'id': key, 'name': value})
            }
            $scope.house_county = angular.copy(arr2)
            $scope.cur_house_county = $scope.house_county[0]
            $scope.county = arr2
            $scope.county.unshift({'id': $scope.cur_city.id, 'name': '全市'})
            $scope.cur_county = angular.copy($scope.county)[0]
            $scope.params.city = $scope.county[0].id
            $scope.params.post = $scope.county[0].id
            tablePages()
        }, function (error) {
            console.log(error)
        })
        //根据省动态获取市、区县
        $scope.getCity = function (item) {
            console.log(item)
            $scope.cur_province = item
            $http.get('districts2.json').then(function (response) {
                let arr1 = []
                let arr2 = []
                for (let [key, value] of Object.entries(response.data[0][item.id])) {
                    arr1.push({'id': key, 'name': value})
                }
                $scope.city = arr1
                $scope.cur_city = $scope.city[0]
                for (let [key, value] of Object.entries(response.data[0][$scope.cur_city.id])) {
                    arr2.push({'id': key, 'name': value})
                }
                $scope.house_county = angular.copy(arr2)
                $scope.cur_house_county = $scope.house_county[0]
                $scope.county = arr2
                $scope.county.unshift({'id': $scope.cur_city.id, 'name': '全市'})
                $scope.cur_county = angular.copy($scope.county)[0]
                $scope.params.city = $scope.county[0].id
                $scope.params.post = $scope.county[0].id
                tablePages()
            }, function (error) {
                console.log(error)
            })
        }
        //根据市动态获取区县
        $scope.getCounty = function (item) {
            $http.get('districts2.json').then(function (response) {
                let arr2 = []
                for (let [key, value] of Object.entries(response.data[0][item.id])) {
                    arr2.push({'id': key, 'name': value})
                }
                $scope.house_county = angular.copy(arr2)
                $scope.cur_house_county = $scope.house_county[0]
                $scope.county = arr2
                $scope.county.unshift({'id': item.id, 'name': '全市'})
                $scope.cur_county = angular.copy($scope.county)[0]
                $scope.params.city = $scope.county[0].id
                $scope.params.post = $scope.county[0].id
                tablePages()
            })
        }
        //输入小区名模糊筛选小区
        $scope.search_house = function () {
            $scope.start_time = ''
            $scope.end_time = ''
            $scope.Config.currentPage = 1
            $scope.params.toponymy = $scope.search_txt
            tablePages()
        }

        //页面跳转
        //跳转小区列表页
        $scope.goHouseList = function () {
            $state.go('intelligent.house_list')
            $scope.second_title = '小区列表页'
            console.log($scope.cur_county)
            console.log($scope.startTime)
        }
        //跳转添加小区页
        $scope.go_to_add = function () {
            $scope.is_add = 1
            $scope.house_name = ''
            $scope.address = ''
            //房屋信息数据初始化
            $scope.house_informations = [{
                'house_type_name': '',//户型名
                'area': '',//房屋面积
                'cur_room': 1,//室
                'cur_hall': 1,//厅
                'cur_toilet': 1,//卫
                'cur_kitchen': 1,//厨
                'cur_imgSrc': '',//户型图
                'have_stair': 1,//是否有楼梯
                'high': 2.8,//层高
                'window': '',//飘窗长度
                'hall_area': '',//
                'hall_girth': '',
                'room_area': '',
                'room_girth': '',
                'toilet_area': '',
                'toilet_girth': '',
                'kitchen_area': '',
                'kitchen_girth': '',
                'other_length': '',
                'flattop_area': '',
                'balcony_area': '',
                'index': 0,
                'drawing_list': [],
                'is_ordinary': 0
            },
                {
                    'house_type_name': '',
                    'area': '',
                    'cur_room': 1,
                    'cur_hall': 1,
                    'cur_toilet': 1,
                    'cur_kitchen': 1,
                    'cur_imgSrc': '',
                    'have_stair': 1,
                    'stair': 1,
                    'high': 2.8,
                    'window': '',
                    'series': '',
                    'style': '',
                    'index': 1,
                    'all_materials': '',
                    'drawing_list': [],
                    'backman_option': [
                        {name: '12墙拆除', num: ''},
                        {name: '24墙拆除', num: ''},
                        {name: '补烂', num: ''},
                        {name: '12墙新建(含双面抹灰)', num: ''},
                        {name: '24墙新建(含双面抹灰)', num: ''},
                        {name: '有无建渣点', num: 1},
                    ],
                    'is_ordinary': 1
                }]
            //图纸信息数据初始化
            $scope.drawing_informations = [{
                'drawing_name': '',
                'house_type_name': '',
                'series': '',
                'style': '',
                'drawing_list': []
            }]
            $scope.submitted = false
            $state.go('intelligent.add_house')
            $scope.three_title = '添加小区信息'
        }
        //跳转编辑户型(判断普通户型/案例)
        $scope.go_to_edit = function (item, index) {
            //数据初始化
            $scope.submitted = false
            $scope.index = index
            $scope.pic_error = ''//多张图片上传错误提示
            $scope.a_pic_error = ''//单张图片上传错误提示
            if (!item.is_ordinary) {
                $state.go('intelligent.edit_house')
                $scope.four_title = '户型详情'
            } else {
                $scope.four_title = '案例详情'
                console.log(item)
                $scope.cur_series = $scope.cur_all_series[0]//当前案例图纸系列
                for (let [key, value] of $scope.cur_all_series.entries()) {
                    if (value.id == item.series.id) {
                        $scope.cur_series = value
                    }
                }
                $scope.cur_style = $scope.cur_all_style[0]//当前案例图纸风格
                for (let [key, value] of $scope.cur_all_style.entries()) {
                    if (value.id == item.style.id) {
                        $scope.cur_style = value
                    }
                }
                $scope.all_drawing = item.drawing_list//当前案例所有图纸
                $scope.backman_option = item.backman_option//杂工选项
                $scope.all_materials = item.all_materials || angular.copy($scope.all_materials_copy)
                if (!item['worker_list']) {
                    item['worker_list'] = angular.copy($scope.common_worker)
                }
                $scope.worker_list = item['worker_list']
                $state.go('intelligent.add_case')
            }
            $scope.cur_house_information = item
            $scope.same_house = angular.copy(item)
        }

        //添加的所有页面配置
        //监听地址输入
        $scope.$watch('address', function (newVal, oldVal) {
            if (newVal.length <= 45) {
                $scope.cur_num = newVal.length
            } else {
                $scope.address = newVal.slice(0, 45)
            }
        })
        //添加房屋信息编辑
        //添加普通户型
        $scope.add_ordinary_house = function () {
            $scope.house_informations.push({
                'house_type_name': '',
                'area': '',
                'cur_room': 1,
                'cur_hall': 1,
                'cur_toilet': 1,
                'cur_kitchen': 1,
                'cur_imgSrc': '',
                'have_stair': 1,
                'high': 2.8,
                'window': '',
                'hall_area': '',
                'hall_girth': '',
                'room_area': '',
                'room_girth': '',
                'toilet_area': '',
                'toilet_girth': '',
                'kitchen_area': '',
                'kitchen_girth': '',
                'drawing_list': [],
                'index': $scope.house_informations.length,
                'other_length': '',
                'flattop_area': '',
                'balcony_area': '',
                'is_ordinary': 0
            })
        }
        //添加案例户型
        $scope.add_case = function () {
            $scope.house_informations.push({
                'house_type_name': '',
                'cur_room': 1,
                'cur_hall': 1,
                'cur_toilet': 1,
                'cur_kitchen': 1,
                'cur_imgSrc': '',
                'have_stair': 1,
                'stair': 1,
                'high': 2.8,
                'window': '',
                'series': '',
                'style': '',
                'index': $scope.house_informations.length,
                'all_materials': '',
                'drawing_list': [],
                'backman_option': [
                    {name: '12墙拆除', num: ''},
                    {name: '24墙拆除', num: ''},
                    {name: '补烂', num: ''},
                    {name: '12墙新建(含双面抹灰)', num: ''},
                    {name: '24墙新建(含双面抹灰)', num: ''},
                    {name: '有无建渣点', num: 1},
                ],
                'is_ordinary': 1
            })
        }
        //上移/下移排序
        $scope.house_sort = function (item, sort) {
            let index = $scope.house_informations.indexOf(item)
            if (sort == 'up') {
                let next = $scope.house_informations[index]
                let prev = $scope.house_informations[index - 1]
                $scope.house_informations.splice(index, 1, prev)
                $scope.house_informations.splice(index - 1, 1, next)
            } else if (sort == 'down') {
                let next = $scope.house_informations[index + 1]
                let prev = $scope.house_informations[index]
                $scope.house_informations.splice(index, 1, next)
                $scope.house_informations.splice(index + 1, 1, prev)
            }
        }
        //增加/减少厅室厨卫
        $scope.change_cur_quantity = function (type, quantity, operation) {
            console.log($scope[type.split('.')[0]][type.split('.')[1]])
            if (!!operation) {
                if ($scope[type.split('.')[0]][type.split('.')[1]] < quantity) {
                    $scope[type.split('.')[0]][type.split('.')[1]]++
                } else {
                    $scope[type.split('.')[0]][type.split('.')[1]] = quantity
                }
            } else {
                if ($scope[type.split('.')[0]][type.split('.')[1]] > quantity) {
                    $scope[type.split('.')[0]][type.split('.')[1]]--
                } else {
                    $scope[type.split('.')[0]][type.split('.')[1]] = quantity
                }
            }
        }
        //删除小区户型
        $scope.delete_house1 = function (item) {
            console.log(item)
            $scope.house_informations.splice($scope.house_informations.indexOf(item), 1)
            if (item.id != undefined) {
                $scope.delete_house_list.push(item.id)
            }
        }
        //添加户型弹出提示框
        $scope.get_tips = function (valid, error) {
            console.log($scope.cur_imgSrc)
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $state.go('intelligent.add_house')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if (valid && !!$scope.cur_house_information.cur_imgSrc) {
                $scope.four_title = ''
                $scope.submitted = false
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            } else {
                $scope.submitted = true//判断是否提交
            }
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
            if (!$scope.cur_house_information.cur_imgSrc) {
                $scope.a_pic_error = '请上传图片'
            }
            console.log($scope.house_informations)
        }
        //添加案例弹出提示框
        $scope.get_case_tips = function (valid, error) {
            console.log($scope.all_materials)
            console.log($scope.house_informations)
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $state.go('intelligent.add_house')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if (valid && !!$scope.cur_house_information.cur_imgSrc && $scope.all_drawing.length != 0) {
                $scope.four_title = ''
                $scope.submitted = false
                $scope.house_informations[$scope.index].all_materials = $scope.all_materials
                $scope.house_informations[$scope.index]['worker_list'] = $scope.worker_list
                $scope.house_informations[$scope.index].series = $scope.cur_series
                $scope.house_informations[$scope.index].style = $scope.cur_style
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            } else {
                $scope.submitted = true//判断是否提交
            }
            console.log(error)
            if (!valid) {
                if (!!error.required) {
                    for (let [key, value] of error.required.entries()) {
                        if (value.$invalid) {
                            $anchorScroll.yOffset = 150
                            $location.hash(value.$name)
                            $anchorScroll()
                            $window.document.getElementById(value.$name).focus()
                            break
                        }
                    }
                } else if (!!error.pattern) {
                    for (let [key, value] of error.pattern.entries()) {
                        if (value.$invalid) {
                            $anchorScroll.yOffset = 150
                            $location.hash(value.$name)
                            $anchorScroll()
                            document.getElementById(value.$name).onfocus()
                            break
                        }
                    }
                }
            }
            if (!$scope.cur_house_information.cur_imgSrc) {
                $scope.a_pic_error = '请上传图片'
            }
            if ($scope.all_drawing.length == 0) {
                console.log(111)
                $scope.pic_error = '请上传图片'
            }
        }
        //跳转编辑图纸(只有普通户型)
        $scope.go_to_drawing = function (item, index) {
            console.log(item)
            $scope.cur_all_house = []
            if (!!$scope.house_informations) {
                for (let [key, value] of angular.copy($scope.house_informations).entries()) {
                    if (!value.is_ordinary && value.house_type_name != '') {
                        value['index'] = key
                        $scope.cur_all_house.push(value)
                    }
                }
            }
            console.log($scope.cur_all_house)
            console.log(item)
            $scope.cur_drawing_name = item.drawing_name
            $scope.cur_series = $scope.cur_all_series[0]//当前图纸系列
            for (let [key, value] of $scope.cur_all_series.entries()) {
                if (item.series.id == value.id || item.series == value.id) {
                    $scope.cur_series = value
                }
            }
            $scope.cur_style = $scope.cur_all_style[0]//当前图纸风格
            for (let [key, value] of $scope.cur_all_style.entries()) {
                if (item.style.id == value.id || item.style == value.id) {
                    $scope.cur_style = value
                }
            }
            $scope.cur_house = $scope.cur_all_house[0]//当前图纸所属户型
            for (let [key, value] of $scope.cur_all_house.entries()) {
                if (item.index == value.index) {
                    $scope.cur_house = value
                }
            }
            $scope.same_drawing = angular.copy(item)
            $scope.pic_error = ''
            $scope.a_pic_error = ''

            $scope.drawing_index = index
            $scope.cur_drawing_information = item
            $scope.all_drawing = $scope.drawing_informations[index].drawing_list//当前所有上传的图纸
            $state.go('intelligent.add_drawing')//跳转添加图纸页面
            $scope.four_title = '图纸详情'
        }
        //初始化图纸数据
        // $scope.cur_drawing_name = ''
        //添加图纸
        $scope.add_drawing = function () {
            $scope.drawing_informations.push({
                'index': '',
                'drawing_name': '',
                'house_type_name': '',
                'series': '',
                'style': '',
                'drawing_list': []
            })
        }
        //删除图纸
        $scope.delete_drawing = function (item) {
            console.log(item)
            $scope.drawing_informations.splice($scope.drawing_informations.indexOf(item), 1)
            if (item.id != undefined) {
                $scope.delete_drawing_list.push(item.id)
            }
        }
        //添加图纸弹出框
        $scope.get_drawing_tips = function (valid) {
            for (let [key, value] of $scope.drawing_informations.entries()) {
                if (value.house_type_name == $scope.cur_house && value.series == $scope.cur_series &&
                    value.style == $scope.cur_style && $scope.drawing_informations.indexOf($scope.cur_drawing_information)
                    != key) {
                    console.log(key)
                    console.log($scope.drawing_informations.indexOf($scope.cur_drawing_information))
                    $scope.is_repeat = true
                    break
                } else {
                    $scope.is_repeat = false
                }
            }
            console.log($scope.is_repeat)
            console.log($scope.cur_series)
            if (valid && $scope.all_drawing.length != 0) {
                if ($scope.is_repeat) {
                    let all_modal = function ($scope, $uibModalInstance) {
                        $scope.cur_title = '图纸内容重复，请重新选择'
                        $scope.common_house = function () {
                            console.log($scope.cur_title)
                            $uibModalInstance.close()
                            // $state.go('intelligent.add_house')
                        }
                    }
                    all_modal.$inject = ['$scope', '$uibModalInstance']
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal,
                        resolve: {
                            cur_title: function () {
                                return $scope.cur_title
                            }
                        }
                    })
                } else {
                    $scope.submitted = false
                    $scope.drawing_informations[$scope.drawing_index] = {
                        'index': $scope.cur_house.index,
                        'drawing_name': $scope.cur_drawing_name,
                        'house_type_name': $scope.cur_house,
                        'series': $scope.cur_series,
                        'style': $scope.cur_style,
                        'drawing_list': $scope.all_drawing
                    }
                    let all_modal = function ($scope, $uibModalInstance) {
                        $scope.cur_title = '保存成功'
                        $scope.common_house = function () {
                            console.log($scope.cur_title)
                            $uibModalInstance.close()
                            $state.go('intelligent.add_house')
                        }
                    }
                    all_modal.$inject = ['$scope', '$uibModalInstance']
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal,
                        resolve: {
                            cur_title: function () {
                                return $scope.cur_title
                            }
                        }
                    })
                }
                console.log($scope.house_informations)
            } else {
                $scope.submitted = true//判断是否提交
            }
            if ($scope.all_drawing.length == 0) {
                $scope.pic_error = '请上传图片'
            }
        }
        //户型页面返回按钮
        $scope.houseReturn = function () {
            if ($scope.index != undefined) {
                $scope.house_informations[$scope.index] = $scope.same_house
            }
            $state.go('intelligent.add_house')
            $scope.four_title = ''
            $scope.submitted = false
        }
        //所有添加页面返回按钮
        $scope.drawingReturn = function () {
            if ($scope.drawing_index != undefined) {
                $scope.drawing_informations[$scope.drawing_index] = $scope.same_drawing
            }
            $state.go('intelligent.add_house')
            $scope.four_title = ''
            $scope.submitted = false
        }
        //添加或编辑页面返回
        $scope.addHouseReturn = function () {
            $state.go('intelligent.house_list')
        }
        //返回首页
        $scope.go_index = function () {
            $state.go('intelligent.intelligent_index')
        }
        //跳转智能报价首页
        $scope.go_index = function () {
            $scope.second_title = ''
            $scope.three_title = ''
            $scope.four_title = ''
            $state.go('intelligent.intelligent_index')
        }
        //跳转二级页面
        $scope.go_second = function () {
            $scope.three_title = ''
            $scope.four_title = ''
            console.log($scope.second_title)
            if ($scope.second_title == '案例/社区配套商品管理') {
                $state.go('intelligent.add_support_goods')
            } else if ($scope.second_title == '小区列表页') {
                $state.go('intelligent.house_list')
            } else if ($scope.second_title == '首页管理') {
                $state.go('intelligent.home_manage')
            } else if ($scope.second_title == '资费/做工标准') {
                $state.go('intelligent.worker_price_list')
            } else if ($scope.second_title == '工程标准') {
                $state.go('intelligent.engineering_standards')
            } else if($scope.second_title == '通用管理'){
                $state.go('intelligent.general_manage')
            } else if($scope.second_title == '添加材料项'){
                $state.go('intelligent.add_material')
            }
        }
        //跳转三级页面
        $scope.go_three = function () {
            $scope.four_title = ''
            if ($scope.three_title == '添加小区信息' || $scope.three_title == '编辑小区信息') {
                $state.go('intelligent.add_house')
            }
        }

        //跳转案例社区配套商品管理
        $scope.check_item = []
        $scope.goSupportGoods = function () {
            $scope.check_item = []
            $scope.second_title = '案例/社区配套商品管理'
            //初始化数据
            $http.get('/quote/assort-goods-list').then(function (response) {//已选中三级
                console.log(response)
                $scope.check_item = response.data.list
            }, function (error) {
                console.log(error)
            })
            $http.get('/quote/assort-goods').then(function (response) {//默认一级
                console.log(response)
                $scope.level_one = response.data.data.categories
                $scope.cur_level_one = $scope.level_one[0]
                $http.get('/quote/assort-goods', {//默认二级
                    params: {
                        pid: $scope.cur_level_one.id
                    }
                }).then(function (response) {
                    $scope.level_two = response.data.data.categories
                    $scope.cur_level_two = $scope.level_two[0]
                    $http.get('/quote/assort-goods', {//默认三级
                        params: {
                            pid: $scope.cur_level_two.id
                        }
                    }).then(function (response) {
                        $scope.level_three = response.data.data.categories
                        for (let [key, value] of $scope.level_three.entries()) {
                            if ($scope.check_item.length == 0) {
                                value['complete'] = false
                            } else {
                                for (let [key1, value1] of $scope.check_item.entries()) {
                                    if (value.id == value1.id) {
                                        value['complete'] = true
                                    }
                                }
                            }
                        }
                        console.log($scope.level_three)
                        console.log(response)
                    }, function (error) {
                        console.log(error)
                    })
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })

            $state.go('intelligent.add_support_goods')
        }
        //请求社区配套商品数据

        //点击请求接口数据
        $scope.go_detail = function (item, index) {
            $http.get('/quote/assort-goods', {
                params: {
                    pid: item.id
                }
            }).then(function (response) {
                if (index == 1 && $scope.cur_level_one != item) {
                    $scope.cur_level_one = item
                    $scope.level_two = response.data.data.categories
                    $scope.cur_level_one = item
                    $scope.cur_level_two = $scope.level_two[0]
                    $http.get('/quote/assort-goods', {
                        params: {
                            pid: response.data.data.categories[0].id
                        }
                    }).then(function (response) {
                        console.log($scope.check_item)
                        $scope.level_three = response.data.data.categories
                        for (let [key, value] of $scope.level_three.entries()) {
                            if ($scope.check_item.length == 0) {
                                value['complete'] = false
                            } else {
                                let cur_index = $scope.check_item.findIndex(function (item) {
                                    return item.id == value.id
                                })
                                    if (cur_index != -1) {
                                        value['complete'] = true
                                    }
                            }
                        }
                        console.log($scope.level_three)
                    }, function (error) {
                        console.log(error)
                    })
                } else if (index == 2) {
                    console.log($scope.check_item)
                    $scope.cur_level_two = item
                    $http.get('/quote/assort-goods', {
                        params: {
                            pid: item.id
                        }
                    }).then(function (response) {
                        console.log($scope.check_item)
                        console.log(response)
                        $scope.level_three = response.data.data.categories
                        for (let [key, value] of $scope.level_three.entries()) {
                            if ($scope.check_item.length == 0) {
                                value['complete'] = false
                            } else {
                                let cur_index = $scope.check_item.findIndex(function (item) {
                                    return item.id == value.id
                                })
                                if (cur_index != -1) {
                                    value['complete'] = true
                                }
                            }
                        }
                        console.log($scope.level_three)
                        console.log(response)
                    }, function (error) {
                        console.log(error)
                    })
                }
            }, function (error) {
                console.log(error)
            })
        }
        //保存社区配套商品管理
        $scope.save_support_goods = function (valid, error) {
            for (let [key, value] of $scope.check_item.entries()) {
                $scope.check_item[key] = {id: value.id, pid: value.pid, title: value.title, path: value.path}
            }
            console.log($scope.check_item)
            $http.post('/quote/assort-goods-add', {
                'assort': $scope.check_item
            }, config).then(function (response) {
                get_case_goods()
                let all_modal = function ($scope, $uibModalInstance) {
                    $scope.cur_title = '保存成功'
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                        $state.go('intelligent.intelligent_index')
                    }
                }
                all_modal.$inject = ['$scope', '$uibModalInstance']
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal,
                    backdrop:'static'
                })
                console.log(response)
            }, function (error) {
                console.log(error)
            })
        }
        //勾选添加/删除三级
        $scope.item_check = function (item,index) {
            console.log($scope.check_item)
            if (item.complete) {
                if(index === 0){
                    item.count = ''
                    item.flag = false
                }
                $scope.check_item.push(item)
            } else {
                let cur_index = $scope.check_item.findIndex(function (item1) {
                    return item1.id == item.id
                })
                    if (cur_index != -1) {
                        $scope.check_item.splice(cur_index, 1)
                    }
            }
            console.log($scope.check_item)
        }
        //close删除三级
        $scope.delete_check = function (item) {
            for (let [key, value] of $scope.level_three.entries()) {
                if (item.id == value.id) {
                    value['complete'] = false
                }
            }
            $scope.check_item.splice($scope.check_item.indexOf(item), 1)
        }

        //保存添加小区信息
        $scope.add_house = function (valid, error) {
            console.log($scope.drawing_informations)
            console.log($scope.house_informations)
            console.log($scope.delete_house_list)
            console.log($scope.delete_drawing_list)
            let arr = []
            //添加小区保存数据处理
            //处理案例商品数据
            if ($scope.is_add) {
                // 处理商品信息
                for (let [key, value] of $scope.house_informations.entries()) {
                    let goods = []
                    if (value.is_ordinary && !!value.all_materials) {
                        for (let [key1, value1] of value.all_materials.entries()) {
                            for (let [key2, value2] of value1.second_level.entries()) {
                                for (let [key3, value3] of value2.three_level.entries()) {
                                    if (!!value3.good_id && !!value3.good_quantity) {
                                        goods.push({
                                            'first_name': value1.title,
                                            'second_name': value2.title,
                                            'three_name': value3.title,
                                            'good_code': value3.good_id,
                                            'good_quantity': value3.good_quantity,
                                            'three_id':value3.id
                                        })
                                        // value['all_goods'] = goods
                                    }
                                }
                            }
                        }
                        value['all_goods'] = goods
                    }
                }
                //处理工人数据
                for (let [key, value] of $scope.house_informations.entries()) {
                    let worker = []
                    if (value.is_ordinary && !!value.worker_list) {
                        for (let [key1, value1] of value.worker_list.entries()) {
                            if (!!value1.price) {
                                worker.push(value1)
                            }
                        }
                        value['worker_list'] = worker
                    }
                }
                //处理杂工选项
                for (let [key, value] of $scope.house_informations.entries()) {
                    let backman_list = []
                    if (value.is_ordinary) {
                        for (let [key1, value1] of value.backman_option.entries()) {
                            if (!!value1.num || value1.num === 0) {
                                backman_list.push(value1)
                                value['backman_list'] = backman_list
                            }
                        }
                    }
                }
                //处理普通户型图纸信息
                for (let [key, value] of $scope.house_informations.entries()) {
                    if (!value.is_ordinary) {
                        for (let [key1, value1] of $scope.drawing_informations.entries()) {
                            if (value1.house_type_name.index == value.index) {
                                console.log(value.drawing_list)
                                if (!!value1.cur_id) {
                                    value.drawing_list.push({
                                        'id': value1.cur_id,
                                        'all_drawing': value1.drawing_list.join(','),
                                        'series': value1.series.id,
                                        'style': value1.style.id,
                                        'drawing_name': value1.drawing_name
                                    })
                                } else {
                                    value.drawing_list.push({
                                        'all_drawing': value1.drawing_list.join(','),
                                        'series': value1.series.id,
                                        'style': value1.style.id,
                                        'drawing_name': value1.drawing_name
                                    })
                                }
                                console.log(1111)
                            }
                        }
                    }
                }
                for (let [key, value] of $scope.house_informations.entries()) {
                    if (!value.is_ordinary) {
                        // for (let [key1, value1] of $scope.drawing_informations.entries()) {
                        //     if (value1.index == key) {
                        arr.push({
                            'house_type_name': value.house_type_name,//户型名
                            'area': value.area,//房屋面积
                            'cur_room': value.cur_room,//室
                            'cur_hall': value.cur_hall,//厅
                            'cur_toilet': value.cur_toilet,//卫
                            'cur_kitchen': value.cur_kitchen,//厨
                            'cur_imgSrc': value.cur_imgSrc,//户型图
                            'have_stair': value.have_stair,//是否有楼梯
                            'stair':value.stair,//楼梯结构
                            'high': value.high,//层高
                            'sort_id': arr.length + 1,
                            'window': value.window,//飘窗长度
                            'hall_area': value.hall_area,//
                            'hall_girth': value.hall_girth,
                            'room_area': value.room_area,
                            'room_girth': value.room_girth,
                            'toilet_area': value.toilet_area,
                            'toilet_girth': value.toilet_girth,
                            'kitchen_area': value.kitchen_area,
                            'kitchen_girth': value.kitchen_girth,
                            'other_length': value.other_length,
                            'flattop_area': value.flattop_area,
                            'balcony_area': value.balcony_area,
                            'drawing_list': value.drawing_list || [],
                            // 'drawing_list': value1.drawing_list.join(','),
                            // 'series': value1.series.id,
                            // 'style': value1.style.id,
                            // 'drawing_name': value1.drawing_name,
                            'is_ordinary': 0
                        })
                        // value1.cur_all_drawing.push({'drawing_list':value.drawing_list.join(','),'series':value.series.id,'style':value.style.id,
                        //     'drawing_name':value.drawing_name, 'is_ordinary': 0})
                        // }

                        // }
                    } else {
                        arr.push({
                            'house_type_name': value.house_type_name,
                            'area': value.area,
                            'cur_room': value.cur_room,
                            'cur_hall': value.cur_hall,
                            'cur_toilet': value.cur_toilet,
                            'cur_kitchen': value.cur_kitchen,
                            'cur_imgSrc': value.cur_imgSrc,
                            'have_stair': value.have_stair,
                            'sort_id': arr.length + 1,
                            'stair': value.stair,
                            'high': value.high,
                            'window': value.window,
                            'series': value.series.id,
                            'style': value.style.id,
                            'all_goods': value.all_goods || [],
                            'drawing_list': value.drawing_list.join(','),
                            'worker_list': value.worker_list || [],
                            'backman_option': value.backman_list || [],
                            'is_ordinary': 1
                        })
                    }
                }
            } else {//编辑小区保存
                // 处理商品信息
                for (let [key, value] of $scope.house_informations.entries()) {
                    let goods = []
                    value['delete_goods'] = []
                    value['delete_workers'] = []
                    value['delete_backman'] = []
                    if (value.is_ordinary && !!value.all_materials) {
                        for (let [key1, value1] of value.all_materials.entries()) {
                            for (let [key2, value2] of value1.second_level.entries()) {
                                for (let [key3, value3] of value2.three_level.entries()) {
                                    if (!!value3.cur_id || value3.cur_id == 0) {
                                        if (!!value3.good_id && !!value3.good_quantity) {
                                            goods.push({
                                                'id': value3.cur_id,
                                                'first_name': value1.title,
                                                'second_name': value2.title,
                                                'three_name': value3.title,
                                                'good_code': value3.good_id,
                                                'good_quantity': value3.good_quantity,
                                                'three_id':value3.id
                                            })
                                            value['all_goods'] = goods
                                        } else {
                                            value.delete_goods.push(value3.cur_id)
                                        }
                                    } else {
                                        if (!!value3.good_id && !!value3.good_quantity) {
                                            goods.push({
                                                'first_name': value1.title,
                                                'second_name': value2.title,
                                                'three_name': value3.title,
                                                'good_code': value3.good_id,
                                                'good_quantity': value3.good_quantity,
                                                'three_id':value3.id
                                            })
                                            value['all_goods'] = goods
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //处理工人数据
                for (let [key, value] of $scope.house_informations.entries()) {
                    let worker = []
                    if (value.is_ordinary && !!value.worker_list) {
                        for (let [key1, value1] of value.worker_list.entries()) {
                            if (!!value1.cur_id) {
                                if (!!value1.price) {
                                    worker.push({
                                        'worker_kind': value1.worker_kind,
                                        'price': value1.worker_kind,
                                        'id': value1.cur_id
                                    })
                                } else {
                                    value.delete_workers.push(value1.cur_id)
                                }
                            } else {
                                if (!!value1.price) {
                                    worker.push(value1)
                                }
                            }
                        }
                        value['worker_list'] = worker
                    }
                }
                //处理杂工选项
                for (let [key, value] of $scope.house_informations.entries()) {
                    let backman_list = []
                    if (value.is_ordinary) {
                        for (let [key1, value1] of value.backman_option.entries()) {
                            if (!!value1.cur_id) {
                                if (!!value1.num || (value1.num == 0 && value1.name == '有无建渣点')) {
                                    backman_list.push({
                                        'name': value1.name,
                                        'num': value1.num,
                                        'id': value1.cur_id
                                    })
                                    value['backman_list'] = backman_list
                                } else {
                                    value.delete_backman.push(value1.cur_id)
                                }
                            } else {
                                if (!!value1.num || (value1.num == 0 && value1.name == '有无建渣点')) {
                                    backman_list.push(value1)
                                    value['backman_list'] = backman_list
                                }
                            }
                        }
                    }
                }
                //处理普通户型图纸信息
                for (let [key, value] of $scope.house_informations.entries()) {
                    if (!value.is_ordinary) {
                        for (let [key1, value1] of $scope.drawing_informations.entries()) {
                            if (value1.house_type_name.index == value.index) {
                                console.log($scope.drawing_informations)
                                if(value1.id!=undefined){
                                    value.drawing_list.push({
                                        'id':value1.id,
                                        'all_drawing': value1.drawing_list.join(','),
                                        'series': value1.series.id,
                                        'style': value1.style.id,
                                        'drawing_name': value1.drawing_name
                                    })
                                }else{
                                    value.drawing_list.push({
                                        'all_drawing': value1.drawing_list.join(','),
                                        'series': value1.series.id,
                                        'style': value1.style.id,
                                        'drawing_name': value1.drawing_name
                                    })
                                }
                                console.log(1111)
                            }
                        }
                    }
                }
                console.log($scope.house_informations)
                for (let [key, value] of $scope.house_informations.entries()) {
                    console.log(value.id)
                    if (!!value.id) {
                        if (!value.is_ordinary) {
                            // for (let [key1, value1] of $scope.drawing_informations.entries()) {
                            //     if (value1.index == key) {
                            arr.push({
                                'id': value.id,
                                'house_type_name': value.house_type_name,//户型名
                                'other_id': value.other_id,
                                'area': value.area,//房屋面积
                                'cur_room': value.cur_room,//室
                                'cur_hall': value.cur_hall,//厅
                                'cur_toilet': value.cur_toilet,//卫
                                'cur_kitchen': value.cur_kitchen,//厨
                                'cur_imgSrc': value.cur_imgSrc,//户型图
                                'sort_id': arr.length + 1,
                                'have_stair': value.have_stair,//是否有楼梯
                                'high': value.high,//层高
                                'window': value.window,//飘窗长度
                                'hall_area': value.hall_area,//
                                'hall_girth': value.hall_girth,
                                'room_area': value.room_area,
                                'room_girth': value.room_girth,
                                'toilet_area': value.toilet_area,
                                'toilet_girth': value.toilet_girth,
                                'kitchen_area': value.kitchen_area,
                                'kitchen_girth': value.kitchen_girth,
                                'other_length': value.other_length,
                                'flattop_area': value.flattop_area,
                                'balcony_area': value.balcony_area,
                                'drawing_list': value.drawing_list || [],
                                // 'drawing_list': value1.drawing_list.join(','),
                                // 'series': value1.series.id,
                                // 'style': value1.style.id,
                                // 'drawing_name': value1.drawing_name,
                                'is_ordinary': 0
                            })
                            // value1.cur_all_drawing.push({'drawing_list':value.drawing_list.join(','),'series':value.series.id,'style':value.style.id,
                            //     'drawing_name':value.drawing_name, 'is_ordinary': 0})
                            // }

                            // }
                        } else {
                            console.log($scope.house_informations)
                            arr.push({
                                'id': value.id,
                                'house_type_name': value.house_type_name,
                                'area': value.area,
                                'cur_room': value.cur_room,
                                'cur_hall': value.cur_hall,
                                'cur_toilet': value.cur_toilet,
                                'cur_kitchen': value.cur_kitchen,
                                'cur_imgSrc': value.cur_imgSrc,
                                'have_stair': value.have_stair,
                                'stair': value.stair,
                                'high': value.high,
                                'window': value.window,
                                'series': value.series.id,
                                'style': value.style.id,
                                'sort_id': arr.length + 1,
                                'delete_goods': value.delete_goods,
                                'delete_workers': value.delete_workers,
                                'delete_backman': value.delete_backman,
                                'drawing_id': value.drawing_id,
                                'all_goods': value.all_goods || [],
                                'drawing_list': value.drawing_list.join(','),
                                'worker_list': value.worker_list || [],
                                'backman_option': value.backman_list || [],
                                'is_ordinary': 1
                            })
                            console.log(111)
                        }
                    } else {
                        if (!value.is_ordinary) {
                            // for (let [key1, value1] of $scope.drawing_informations.entries()) {
                            //     if (value1.index == key) {
                            arr.push({
                                'house_type_name': value.house_type_name,//户型名
                                'area': value.area,//房屋面积
                                'cur_room': value.cur_room,//室
                                'cur_hall': value.cur_hall,//厅
                                'cur_toilet': value.cur_toilet,//卫
                                'cur_kitchen': value.cur_kitchen,//厨
                                'cur_imgSrc': value.cur_imgSrc,//户型图
                                'have_stair': value.have_stair,//是否有楼梯
                                'high': value.high,//层高
                                'window': value.window,//飘窗长度
                                'hall_area': value.hall_area,//
                                'hall_girth': value.hall_girth,
                                'room_area': value.room_area,
                                'room_girth': value.room_girth,
                                'toilet_area': value.toilet_area,
                                'toilet_girth': value.toilet_girth,
                                'kitchen_area': value.kitchen_area,
                                'kitchen_girth': value.kitchen_girth,
                                'sort_id': arr.length + 1,
                                'other_length': value.other_length,
                                'flattop_area': value.flattop_area,
                                'balcony_area': value.balcony_area,
                                'drawing_list': value.drawing_list || [],
                                // 'drawing_list': value1.drawing_list.join(','),
                                // 'series': value1.series.id,
                                // 'style': value1.style.id,
                                // 'drawing_name': value1.drawing_name,
                                'is_ordinary': 0
                            })
                            // value1.cur_all_drawing.push({'drawing_list':value.drawing_list.join(','),'series':value.series.id,'style':value.style.id,
                            //     'drawing_name':value.drawing_name, 'is_ordinary': 0})
                            // }

                            // }
                        } else {
                            arr.push({
                                'house_type_name': value.house_type_name,
                                'area': value.area,
                                'cur_room': value.cur_room,
                                'cur_hall': value.cur_hall,
                                'cur_toilet': value.cur_toilet,
                                'cur_kitchen': value.cur_kitchen,
                                'cur_imgSrc': value.cur_imgSrc,
                                'have_stair': value.have_stair,
                                'stair': value.stair,
                                'high': value.high,
                                'sort_id': arr.length + 1,
                                'window': value.window,
                                'series': value.series.id,
                                'style': value.style.id,
                                'all_goods': value.all_goods || [],
                                'drawing_list': value.drawing_list.join(','),
                                'worker_list': value.worker_list || [],
                                'backman_option': value.backman_list || [],
                                'is_ordinary': 1
                            })
                        }
                    }
                }
            }
            //编辑小区保存数据处理
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $state.go('intelligent.house_list')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if (valid) {
                console.log(arr)
                if ($scope.is_add) {
                    $http.post('/quote/plot-add', {
                        'province_code': $scope.cur_province.id,
                        'city_code': $scope.cur_city.id,
                        'house_name': $scope.house_name,
                        'cur_county_id': $scope.cur_house_county.id,
                        'address': $scope.address,
                        'house_informations': arr
                    }, config).then(function (response) {
                        console.log($scope.cur_county)
                        console.log(response)
                        //请求小区数据
                        tablePages()
                        //弹出保存成功模态框
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    }, function (error) {
                        console.log(error)
                    })
                } else {
                    $http.post('/quote/plot-edit', {
                        'province_code': $scope.cur_province.id,
                        'city_code': $scope.cur_city.id,
                        'house_name': $scope.house_name,
                        'cur_county_id': $scope.cur_house_county.id,
                        'address': $scope.address,
                        'house_informations': arr,
                        'delete_house': $scope.delete_house_list,
                        'delete_drawing': $scope.delete_drawing_list
                    }, config).then(function (response) {
                        console.log(response)
                        //请求小区数据
                        tablePages()
                        console.log(response)
                        //弹出保存成功模态框
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    }, function (error) {
                        console.log(error)
                    })
                }

            } else {
                $scope.submitted = true//判断是否提交
            }
            if (!valid) {
                if (!!error.required) {
                    for (let [key, value] of error.required.entries()) {
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
            console.log($scope.drawing_informations)
            console.log($scope.house_informations)
            console.log(arr)
        }
        //删除小区数据
        $scope.delete_house = function (item) {
            let data = $scope
            //删除小区判断
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '是否删除小区？'
                $scope.is_cancel = true
                $scope.common_house = function () {
                    $http.post('/quote/plot-del', {
                        del_id: item.id
                    }, config).then(function (res) {
                        console.log(res)
                        $scope = data
                        tablePages()
                        $uibModalInstance.close()
                        $scope.is_cancel = false
                    }, function (error) {
                        console.log(error)
                    })
                }
                $scope.cancel_delete = function () {
                    $uibModalInstance.close()
                    $scope.is_cancel = false
                }
            }
            //弹出保存成功模态框
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: all_modal
            })
        }
        //编辑所有页面配置
        $scope.go_edit_house = function (item) {
            $scope.three_title = '编辑小区信息'
            $scope.delete_house_list = []
            $scope.delete_drawing_list = []
            $scope.is_add = 0
            console.log($scope.all_materials_copy)
            $scope.all_materials_copy1 = angular.copy($scope.all_materials_copy)
            $scope.house_informations = []
            $scope.drawing_informations = []
            $scope.cur_all_house = []
            $http.post('/quote/plot-edit-view', {
                'district': item.district,
                'street': item.street,
                'toponymy': item.toponymy
            }, config).then(function (response) {
                console.log(response)
                $scope.cur_all_worker = response.data.effect.works_worker_data
                //获取基本信息数据
                $scope.house_name = response.data.effect.toponymy//小区名称
                //小区所在区
                for (let [key, value] of $scope.house_county.entries()) {
                    if (value.id === response.data.effect.district_code) {
                        $scope.cur_house_county = value
                    }
                }
                $scope.address = response.data.effect.street//小区详细地址
                //获取小区户型信息
                for (let [key, value] of response.data.effect.effect.entries()) {
                    if (!+value.type) {
                        for (let [key1, value1] of response.data.effect.decoration_particulars.entries()) {
                            if (!+value.type && value.id == value1.effect_id) {
                                $scope.house_informations.push({
                                    'id': value.id,
                                    'other_id': value1.id,
                                    'house_type_name': value.particulars,//户型名
                                    'area': value.area,//房屋面积
                                    'cur_room': value.bedroom,//室
                                    'cur_hall': value.sittingRoom_diningRoom,//厅
                                    'cur_toilet': value.toilet,//卫
                                    'cur_kitchen': value.kitchen,//厨
                                    'cur_imgSrc': value.house_image,//户型图
                                    'have_stair': value.stairway,//是否有楼梯
                                    'high': value.high,//层高
                                    'window': value.window,//飘窗长度
                                    'hall_area': value1.hall_area,
                                    'hall_girth': value1.hall_perimeter,
                                    'room_area': value1.bedroom_area,
                                    'room_girth': value1.bedroom_perimeter,
                                    'toilet_area': value1.toilet_area,
                                    'toilet_girth': value1.toilet_perimeter,
                                    'kitchen_area': value1.kitchen_area,
                                    'kitchen_girth': value1.kitchen_perimeter,
                                    'other_length': value1.modelling_length,
                                    'index': $scope.house_informations.length,
                                    'drawing_list': [],
                                    'flattop_area': value1.flat_area,
                                    'balcony_area': value1.balcony_area,
                                    'is_ordinary': 0
                                })
                            }
                        }
                    } else if (!!+value.type) {
                        // console.log(value.effect_images.split(','))
                        $scope.house_informations.push({
                            'id': value.id,
                            'house_type_name': value.particulars,
                            'area': value.area,
                            'cur_room': value.bedroom,
                            'cur_hall': value.sittingRoom_diningRoom,
                            'cur_toilet': value.toilet,
                            'cur_kitchen': value.kitchen,
                            'cur_imgSrc': value.house_image,
                            'have_stair': value.stairway,
                            'stair': +value.stair_id,
                            'all_materials': angular.copy($scope.all_materials_copy),
                            'worker_list': angular.copy($scope.common_worker),
                            'backman_option': [
                                {name: '12墙拆除', num: ''},
                                {name: '24墙拆除', num: ''},
                                {name: '补烂', num: ''},
                                {name: '12墙新建(含双面抹灰)', num: ''},
                                {name: '24墙新建(含双面抹灰)', num: ''},
                                {name: '有无建渣点', num: 1},
                            ],
                            'high': value.high,
                            'window': value.window,
                            'is_ordinary': 1
                        })
                        console.log($scope.house_informations)
                    }
                }
                //获取普通户型图纸信息
                for (let [key, value] of $scope.house_informations.entries()) {
                    for (let [key1, value1] of response.data.effect.images.entries()) {
                        if (value.id == value1.effect_id) {
                            if (!value.is_ordinary) {
                                $scope.drawing_informations.push({
                                    'index': value.index,
                                    'id': value1.id,
                                    'drawing_name': value1.images_user,
                                    'house_type_name': value,
                                    'series': value1.series_id,
                                    'style': value1.style_id,
                                    'drawing_list': value1.effect_images.split(',')
                                })
                            } else {
                                value['drawing_id'] = value1.id
                                value['drawing_list'] = value1.effect_images.split(',')
                                value['series'] = value1.series_id
                                value['style'] = value1.style_id
                                for (let [key2, value2] of $scope.cur_all_series.entries()) {
                                    if (value.series == value2.id) {
                                        value.series = value2
                                    }
                                }
                                for (let [key2, value2] of $scope.cur_all_style.entries()) {
                                    if (value.style == value2.id) {
                                        value.style = value2
                                    }
                                }
                            }
                        }

                    }
                }
                //整合普通户型图纸信息
                for (let [key, value] of $scope.drawing_informations.entries()) {
                    for (let [key1, value1] of $scope.cur_all_series.entries()) {
                        if (value.series == value1.id) {
                            value.series = value1
                        }
                    }
                    for (let [key1, value1] of $scope.cur_all_style.entries()) {
                        if (value.style == value1.id) {
                            value.style = value1
                        }
                    }
                }
                //获取案例户型材料及费用
                for (let [key, value] of $scope.house_informations.entries()) {
                    if (value.is_ordinary) {
                        for (let [key1, value1] of value.all_materials.entries()) {
                            for (let [key2, value2] of value1.second_level.entries()) {
                                for (let [key3, value3] of value2.three_level.entries()) {
                                    for (let [key4, value4] of response.data.effect.works_data.entries()) {
                                        if (value3.title == value4.goods_three) {
                                            value3.good_id = value4.goods_code
                                            value3.good_quantity = value4.goods_quantity
                                            value3['cur_id'] = value4.id
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //获取案例工人费用
                for (let [key, value] of $scope.house_informations.entries()) {
                    if (value.is_ordinary) {
                        for (let [key1, value1] of value.worker_list.entries()) {
                            for (let [key2, value2] of response.data.effect.works_worker_data.entries()) {
                                if (value1.worker_kind == value2.worker_kind) {
                                    value1.price = value2.worker_price
                                    value1['cur_id'] = value2.id
                                }
                            }
                        }
                    }
                }
                //获取案例杂工选项
                for (let [key, value] of $scope.house_informations.entries()) {
                    if (value.is_ordinary) {
                        for (let [key1, value1] of value.backman_option.entries()) {
                            for (let [key2, value2] of response.data.effect.works_backman_data.entries()) {
                                if (value1.name == value2.backman_option) {
                                    value1.num = value2.backman_value
                                    value1['cur_id'] = value2.id
                                }
                            }
                        }
                    }
                }
                console.log(angular.copy($scope.all_materials_copy))
                console.log($scope.drawing_informations)
                console.log($scope.house_informations)
                console.log($scope.drawing_informations)
                console.log($scope.house_informations)
                $state.go('intelligent.add_house')
            }, function (error) {
                console.log(error)
            })
        }

        /*资费管理*/
        //跳转资费/做工标准页面
        $scope.go_worker_list = function () {
            $scope.second_title = '资费/做工标准'
            $scope.three_title = ''
            $scope.four_title = ''
            $http.get('/quote/labor-cost-list').then(function (response) {
                console.log(response)
                $scope.cur_worker_list = response.data.list
                $state.go('intelligent.worker_price_list')
            }, function (error) {
                console.log(error)
            })
        }
        // //添加工种状态
        // $scope.add_worker = function(){
        //     for(let [key,value] of $scope.cur_worker_list.entries()){
        //         // if(value.)
        //     }
        // }
        //计算各种工种资费
        $scope.edit_worker = function (item) {
            console.log($scope.cur_worker_list)
            $scope.submitted = false
            $scope.cur_worker = item
            $scope.second_title = '资费/做工标准'
            $scope.three_title = '资费/做工标准详情'
            $scope.four_title = ''
            $http.get('/quote/labor-cost-edit-list', {
                params: {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id,
                    worker_kind: item.worker_kind
                }
            }).then(function (response) {
                console.log(response)
                $scope.worker_id = response.data.labor_cost.id
                $scope.daily_cost = response.data.labor_cost.univalence == 0 ? '' : response.data.labor_cost.univalence
                $scope.other_items = response.data.worker_craft_norm
                //简单0处理
                for (let [key, value] of $scope.other_items.entries()) {
                    if (value.quantity == 0) {
                        value.quantity = ''
                    }
                    value['flag'] = false
                    value['name'] = 'name' + value.id
                }
            }, function (error) {
                console.log(error)
            })
            $state.go('intelligent.edit_worker')
        }
        //保存工人资费
        $scope.get_worker_tips = function (valid) {
            console.log($scope.other_items)
            let arr = []
            for (let [key, value] of $scope.other_items.entries()) {
                arr.push({
                    id: value.id,
                    quantity: value.quantity
                })
            }
            console.log(arr)
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $state.go('intelligent.worker_price_list')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if (valid) {
                $http.post('/quote/labor-cost-edit', {
                    id: $scope.worker_id,
                    univalence: $scope.daily_cost,
                    else: arr
                }, config).then(function (response) {
                    console.log(response)
                    $scope.second_title = '资费/做工标准'
                    $scope.three_title = ''
                    $scope.four_title = ''
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                }, function (error) {
                    console.log(error)
                })
            } else {
                $scope.submitted = true
            }
        }
        //返回工人列表页
        $scope.returnWorkerList = function () {
            $scope.second_title = '资费/做工标准'
            $scope.three_title = ''
            $scope.four_title = ''
            $state.go('intelligent.worker_price_list')
        }

        /*首页管理*/
        //    跳转首页管理
        $scope.go_home_manage = function () {
            $scope.second_title = '首页管理'
            $scope.cur_county = $scope.county[0]
            $scope.three_title = ''
            $scope.four_title = ''
            $http.get('/quote/homepage-list', {
                params: {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id
                }
            }).then(function (response) {
                console.log(response)
                $scope.all_manage = response.data.list
            }, function (error) {
                console.log(error)
            })
            $state.go('intelligent.home_manage')
        }
        //添加推荐
        //跳转添加页面
        $scope.add_manage = function () {
            //初始化页面
            $scope.cur_image = ''
            $scope.a_pic_error = ''
            $scope.submitted = false
            $scope.second_title = '首页管理'
            $scope.three_title = '添加推荐'
            $scope.four_title = ''
            $scope.is_add_manage = 1
            $http.get('/quote/homepage-district', {
                params: {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id
                }
            }).then(function (response) {
                console.log(response)
                $scope.all_house_city = response.data.list
                $scope.cur_house_city = $scope.all_house_city[0]
                $http.post('/quote/homepage-toponymy', {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id,
                    district: $scope.cur_house_city.district_code
                }, config).then(function (response) {
                    console.log(response)
                    $scope.all_toponymy = response.data.list
                    $scope.cur_toponymy_house = $scope.all_toponymy[0]
                    $http.post('/quote/homepage-street', {
                        province: $scope.cur_province.id,
                        city: $scope.cur_city.id,
                        district: $scope.cur_house_city.district_code,
                        toponymy: $scope.cur_toponymy_house.toponymy
                    }, config).then(function (response) {
                        console.log(response)
                        $scope.all_street = response.data.list
                        $scope.cur_street = $scope.all_street[0]
                        $http.post('/quote/homepage-case', {
                            province: $scope.cur_province.id,
                            city: $scope.cur_city.id,
                            district: $scope.cur_house_city.district_code,
                            toponymy: $scope.cur_toponymy_house.toponymy,
                            street: $scope.cur_street.street
                        }, config).then(function (response) {
                            console.log(response)
                            $scope.all_case = response.data.list
                            $scope.cur_case = $scope.all_case[0]
                            $state.go('intelligent.add_manage')
                        }, function (error) {
                            console.log(error)
                        })
                    }, function (error) {
                        console.log(error)
                    })
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
        }
        //当修改区县
        $scope.change_county = function (item) {
            $http.post('/quote/homepage-toponymy', {
                province: $scope.cur_province.id,
                city: $scope.cur_city.id,
                district: item.district_code
            }, config).then(function (response) {
                console.log(response)
                $scope.all_toponymy = response.data.list
                $scope.cur_toponymy_house = $scope.all_toponymy[0]
                $http.post('/quote/homepage-street', {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id,
                    district: $scope.cur_house_city.district_code,
                    toponymy: $scope.cur_toponymy_house.toponymy
                }, config).then(function (response) {
                    console.log(response)
                    $scope.all_street = response.data.list
                    $scope.cur_street = $scope.all_street[0]
                    $http.post('/quote/homepage-case', {
                        province: $scope.cur_province.id,
                        city: $scope.cur_city.id,
                        district: $scope.cur_house_city.district_code,
                        toponymy: $scope.cur_toponymy_house.toponymy,
                        street: $scope.cur_street.street
                    }, config).then(function (response) {
                        console.log(response)
                        $scope.all_case = response.data.list
                        $scope.cur_case = $scope.all_case[0]
                    }, function (error) {
                        console.log(error)
                    })
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
        }
        //当修改小区
        $scope.change_toponymy = function (item) {
            $http.post('/quote/homepage-street', {
                province: $scope.cur_province.id,
                city: $scope.cur_city.id,
                district: $scope.cur_house_city.district_code,
                toponymy: item.toponymy
            }, config).then(function (response) {
                console.log(response)
                $scope.all_street = response.data.list
                $scope.cur_street = $scope.all_street[0]
                $http.post('/quote/homepage-case', {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id,
                    district: $scope.cur_house_city.district_code,
                    toponymy: $scope.cur_toponymy_house.toponymy,
                    street: $scope.cur_street.street
                }, config).then(function (response) {
                    console.log(response)
                    $scope.all_case = response.data.list
                    $scope.cur_case = $scope.all_case[0]
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
        }
        //当修改街道
        $scope.change_street = function (item) {
            $http.post('/quote/homepage-case', {
                province: $scope.cur_province.id,
                city: $scope.cur_city.id,
                district: $scope.cur_house_city.district_code,
                toponymy: $scope.cur_toponymy_house.toponymy,
                street: item.street
            }, config).then(function (response) {
                console.log(response)
                $scope.all_case = response.data.list
                $scope.cur_case = $scope.all_case[0]
            }, function (error) {
                console.log(error)
            })
        }
        //保存添加/编辑推荐
        $scope.save_manage = function (valid) {
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $state.go('intelligent.home_manage')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if (valid && $scope.cur_image != '') {
                if (!!$scope.is_add_manage) {
                    $http.post('/quote/homepage-add', {
                        province: $scope.cur_province.id,
                        city: $scope.cur_city.id,
                        district: $scope.cur_house_city.district_code,
                        toponymy: $scope.cur_toponymy_house.toponymy,
                        street: $scope.cur_street.street,
                        image: $scope.cur_image,
                        house_type_name: $scope.cur_case.particulars,
                        recommend_name: $scope.recommend_name
                    }, config).then(function (response) {
                        console.log(response)
                        $scope.second_title = '资费/做工标准'
                        $scope.three_title = ''
                        $scope.four_title = ''
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                        $http.get('/quote/homepage-list', {
                            params: {
                                province: $scope.cur_province.id,
                                city: $scope.cur_city.id
                            }
                        }).then(function (response) {
                            console.log(response)
                            $scope.all_manage = response.data.list
                        }, function (error) {
                            console.log(error)
                        })
                    }, function (error) {
                        console.log(error)
                    })
                } else {
                    $http.post('/quote/homepage-edit', {
                        id: $scope.cur_manage_id,
                        province: $scope.cur_province.id,
                        city: $scope.cur_city.id,
                        district: $scope.cur_house_city.district_code,
                        toponymy: $scope.cur_toponymy_house.toponymy,
                        street: $scope.cur_street.street,
                        image: $scope.cur_image,
                        house_type_name: $scope.cur_case.particulars,
                        recommend_name: $scope.recommend_name
                    }, config).then(function (response) {
                        console.log(response)
                        $scope.second_title = '资费/做工标准'
                        $scope.three_title = ''
                        $scope.four_title = ''
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                        $http.get('/quote/homepage-list', {
                            params: {
                                province: $scope.cur_province.id,
                                city: $scope.cur_city.id
                            }
                        }).then(function (response) {
                            console.log(response)
                            $scope.all_manage = response.data.list
                        }, function (error) {
                            console.log(error)
                        })
                    }, function (error) {
                        console.log(error)
                    })
                }
            } else {
                $scope.submitted = true
            }
            if ($scope.cur_image == '') {
                $scope.a_pic_error = '请上传图片'
            }
        }
        //返回首页管理页
        $scope.returnHomeManage = function () {
            $state.go('intelligent.home_manage')
        }
        //修改状态
        $scope.change_status = function (item) {
            $http.get('/quote/homepage-status', {
                params: {
                    id: item.id,
                    status: +!+item.status
                }
            }).then(function (response) {
                console.log(response)
                $http.get('/quote/homepage-list', {
                    params: {
                        province: $scope.cur_province.id,
                        city: $scope.cur_city.id
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.all_manage = response.data.list
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
        }
        //编辑推荐
        $scope.go_edit_manage = function (item) {
            $scope.recommend_name = item.recommend_name
            $scope.cur_image = item.image
            $scope.cur_manage_id = item.id
            $scope.is_add_manage = 0
            $scope.second_title = '首页管理'
            $scope.three_title = '编辑推荐'
            $scope.four_title = ''
            $http.get('/quote/homepage-district', {
                params: {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id
                }
            }).then(function (response) {
                console.log(response)
                $scope.all_house_city = response.data.list
                for (let [key, value] of $scope.all_house_city.entries()) {
                    if (value.district_code == item.district_code) {
                        $scope.cur_house_city = value
                    }
                }
                $http.post('/quote/homepage-toponymy', {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id,
                    district: $scope.cur_house_city.district_code
                }, config).then(function (response) {
                    console.log(response)
                    $scope.all_toponymy = response.data.list
                    for (let [key, value] of $scope.all_toponymy.entries()) {
                        if (value.toponymy == item.toponymy) {
                            $scope.cur_toponymy_house = value
                        }
                    }
                    $http.post('/quote/homepage-street', {
                        province: $scope.cur_province.id,
                        city: $scope.cur_city.id,
                        district: $scope.cur_house_city.district_code,
                        toponymy: $scope.cur_toponymy_house.toponymy
                    }, config).then(function (response) {
                        console.log(response)
                        $scope.all_street = response.data.list
                        for (let [key, value] of $scope.all_street.entries()) {
                            if (value.street == item.street) {
                                $scope.cur_street = value
                            }
                        }
                        $http.post('/quote/homepage-case', {
                            province: $scope.cur_province.id,
                            city: $scope.cur_city.id,
                            district: $scope.cur_house_city.district_code,
                            toponymy: $scope.cur_toponymy_house.toponymy,
                            street: $scope.cur_street.street
                        }, config).then(function (response) {
                            console.log(response)
                            $scope.all_case = response.data.list
                            for (let [key, value] of $scope.all_case.entries()) {
                                if (value.particulars == item.house_type_name) {
                                    $scope.cur_case = value
                                }
                            }
                            $state.go('intelligent.add_manage')
                        }, function (error) {
                            console.log(error)
                        })
                    }, function (error) {
                        console.log(error)
                    })
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
        }
        //拖拽排序
        $scope.dropComplete = function (index, obj) {
            let idx = $scope.all_manage.indexOf(obj)
            $scope.all_manage[idx] = $scope.all_manage[index]
            $scope.all_manage[index] = obj
        }
        // 排序保存
        $scope.cur_sort = function () {
            let arr = []
            for (let [key, value] of $scope.all_manage.entries()) {
                arr.push({id: value.id, sort: key + 1})
            }
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            $http.post('/quote/homepage-sort', {
                sort: arr
            }, config).then(function (response) {
                console.log(response)
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
            }, function (error) {
                console.log(error)
            })
        }
        //删除推荐
        $scope.delete_manage = function (item) {
            console.log(+item.status == 0)
            if (+item.status == 0) {
                $http.post('/quote/homepage-delete', {
                    id: item.id
                }, config).then(function (response) {
                    console.log(response)
                    $http.get('/quote/homepage-list', {
                        params: {
                            province: $scope.cur_province.id,
                            city: $scope.cur_city.id
                        }
                    }).then(function (response) {
                        console.log(response)
                        $scope.all_manage = response.data.list
                    }, function (error) {
                        console.log(error)
                    })
                }, function (error) {
                    console.log(error)
                })
            }
        }

        /*工程标准*/
        //    工程标准主页
        $scope.go_engineering = function () {
            $http.get('/quote/project-norm-list').then(function (response) {
                console.log(response)
                $scope.all_project = response.data.list
                $scope.second_title = '工程标准'
                $scope.three_title = ''
                $scope.four_title = ''
                $state.go('intelligent.engineering_standards')
            }, function (error) {
                console.log(error)
            })
        }
        //工程标准编辑
        $scope.get_process = function (item) {
            console.log(item)
            $scope.process_list = []
            $scope.second_title = '工程标准'
            $scope.three_title = item.project + '工艺'
            $scope.cur_item_project = item.project
            $scope.four_title = ''
            $http.get('/quote/project-norm-edit-list', {
                params: {
                    city: $scope.cur_city.id,
                    project: item.project
                }
            }).then(function (response) {
                console.log(response)
                let arr = response.data.list
                //简单0处理
                for (let [key, value] of arr.entries()) {
                    if (value.material == 0) {
                        value.material = ''
                    }
                    value['flag'] = false
                    value['name'] = 'name' + value.id
                }
                //不同板块处理
                if (item.project == '强电' || item.project == '弱电' || item.project == '水路') {
                    for (let [key, value] of arr.entries()) {
                        value.cur_unit = 'm/点位'
                    }
                    $scope.process_list.push(arr)
                } else if (item.project == '防水') {
                    for (let [key, value] of arr.entries()) {
                        value.cur_unit = 'kg/m2'
                    }
                    $scope.process_list.push(arr)
                } else if (item.project == '乳胶漆') {
                    for (let [key, value] of arr.entries()) {
                        if (value.project_details.indexOf('腻子') != -1) {
                            value.cur_unit = 'kg/m2'
                        } else if (value.project_details.indexOf('底漆') != -1 || value.project_details.indexOf('面漆') != -1) {
                            value.cur_unit = 'L/m2'
                        } else if (value.project_details.indexOf('阴角线') != -1) {
                            value.cur_unit = 'm/m'
                        } else if (value.project_details.indexOf('石膏粉') != -1) {
                            value.cur_unit = 'm/m'
                        }
                    }
                    $scope.process_list.push(arr)
                } else if (item.project == '泥工') {
                    for (let [key, value] of arr.entries()) {
                        value.cur_unit = 'kg/m2'
                    }
                    $scope.process_list.push(arr)
                } else if (item.project == '杂工') {
                    let arr1 = [], arr2 = [], arr3 = []
                    for (let [key, value] of arr.entries()) {
                        if (value.project_details.indexOf('水泥') != -1) {
                            if (value.project_details.indexOf('补烂') != -1) {
                                value.cur_unit = 'kg/m'
                                arr1.push(value)
                            } else {
                                value.cur_unit = 'kg/m2'
                                arr1.push(value)
                            }
                        } else if (value.project_details.indexOf('河沙') != -1) {
                            if (value.project_details.indexOf('补烂') != -1) {
                                value.cur_unit = 'kg/m'
                                arr2.push(value)
                            } else {
                                value.cur_unit = 'kg/m2'
                                arr2.push(value)
                            }
                        } else {
                            if (value.project_details.indexOf('清运') != -1) {
                                value.cur_unit = '元'
                                arr3.push(value)
                            } else if (value.project_details.indexOf('面积') != -1) {
                                value.cur_unit = 'm2/车'
                                arr3.push(value)
                            } else if (value.project_details.indexOf('费用') != -1) {
                                value.cur_unit = '元/车'
                                arr3.push(value)
                            }
                        }
                    }
                    $scope.process_list = [arr1, arr2, arr3]
                } else if (item.project == '木作') {
                    let arr4 = [], arr5 = [], arr6 = []
                    $http.get('/quote/project-norm-woodwork-list').then(function (response) {
                        console.log(response)
                        console.log(2.5 == 2.50)
                        $scope.cur_norm = response.data.specification.find_specification
                        $scope.all_norm = response.data.specification.specification
                        let arr1 = angular.copy(response.data.series),
                            arr2 = angular.copy(response.data.series),
                            arr3 = angular.copy(response.data.series),
                            arr7 = angular.copy(response.data.style),
                            arr8 = angular.copy(response.data.style),
                            arr9 = angular.copy(response.data.style)
                        //系列
                        for (let [key, value] of response.data.coefficient.entries()) {
                            for (let [key1, value1] of arr1.entries()) {
                                value1['name'] = 'series01' + value1.id
                                if (value.project == value1.series) {
                                    if (+value.series_or_style == 0 && +value.coefficient == 1) {
                                        value1['series_or_style'] = 0
                                        value1['cur_id'] = value.id
                                        value1['coefficient'] = 1
                                        value1['value'] = value.value
                                    }
                                } else {
                                    if (value1.value == undefined) {
                                        value1['series_or_style'] = 0
                                        value1['coefficient'] = 1
                                        value1['value'] = ''
                                    }
                                }
                            }
                            for (let [key1, value1] of arr2.entries()) {
                                value1['name'] = 'series02' + value1.id
                                if (value.project == value1.series) {
                                    if (+value.series_or_style == 0 && +value.coefficient == 2) {
                                        value1['series_or_style'] = 0
                                        value1['cur_id'] = value.id
                                        value1['coefficient'] = 2
                                        value1['value'] = value.value
                                    }
                                } else {
                                    if (value1.value == undefined) {
                                        value1['series_or_style'] = 0
                                        value1['coefficient'] = 2
                                        value1['value'] = ''
                                    }
                                }
                            }
                            for (let [key1, value1] of arr3.entries()) {
                                value1['name'] = 'series03' + value1.id
                                if (value.project == value1.series) {
                                    if (+value.series_or_style == 0 && +value.coefficient == 3) {
                                        value1['series_or_style'] = 0
                                        value1['cur_id'] = value.id
                                        value1['coefficient'] = 3
                                        value1['value'] = value.value
                                    }
                                } else {
                                    if (value1.value == undefined) {
                                        value1['series_or_style'] = 0
                                        value1['coefficient'] = 3
                                        value1['value'] = ''
                                    }
                                }
                            }
                        }
                        //风格
                        for (let [key, value] of response.data.coefficient.entries()) {
                            for (let [key1, value1] of arr7.entries()) {
                                value1['name'] = 'style11' + value1.id
                                if (value.project == value1.style) {
                                    if (+value.series_or_style == 1 && +value.coefficient == 1) {
                                        value1['series_or_style'] = 1
                                        value1['cur_id'] = value.id
                                        value1['coefficient'] = 1
                                        value1['value'] = value.value
                                    }
                                } else {
                                    if (value1.value == undefined) {
                                        value1['series_or_style'] = 1
                                        value1['coefficient'] = 1
                                        value1['value'] = ''
                                    }
                                }
                            }
                            for (let [key1, value1] of arr8.entries()) {
                                value1['name'] = 'style12' + value1.id
                                if (value.project == value1.style) {
                                    if (+value.series_or_style == 1 && +value.coefficient == 2) {
                                        value1['series_or_style'] = 1
                                        value1['cur_id'] = value.id
                                        value1['coefficient'] = 2
                                        value1['value'] = value.value
                                    }
                                } else {
                                    if (value1.value == undefined) {
                                        value1['series_or_style'] = 1
                                        value1['coefficient'] = 2
                                        value1['value'] = ''
                                    }
                                }
                            }
                        }
                        $scope.all_series = [arr1, arr2, arr3]
                        $scope.all_style = [arr7, arr8]
                        console.log($scope.all_series)
                        for (let [key, value] of $scope.all_norm.entries()) {
                            for (let [key1, value1] of $scope.cur_norm.entries()) {
                                if (value.title == value1.title) {
                                    value1['options'] = value.value
                                }
                            }
                        }
                        for (let [key, value] of $scope.cur_norm.entries()) {
                            value.value = parseFloat(value.value) + ''
                            value.cur_unit = 'm'
                            if (value.title == '石膏板') {
                                arr4.push(value)
                            } else if (value.title == '龙骨') {
                                arr5.push(value)
                            } else if (value.title == '丝杆') {
                                arr6.push(value)
                            }
                        }
                        for (let [key, value] of arr.entries()) {
                            if (value.project_details.indexOf('长度') != -1) {
                                value.cur_unit = 'm'
                            } else if (value.project_details.indexOf('面积') != -1) {
                                value.cur_unit = 'm2'
                            } else {
                                value.cur_unit = '张'
                            }
                            if (value.project_details.indexOf('石膏板') != -1) {
                                arr4.push(value)
                            } else if (value.project_details.indexOf('龙骨') != -1) {
                                arr5.push(value)
                            } else if (value.project_details.indexOf('丝杆') != -1) {
                                arr6.push(value)
                            }
                        }
                        $scope.process_list = [arr4, arr5, arr6]
                        console.log($scope.process_list)
                        console.log(arr)
                    }, function (error) {
                        console.log(error)
                    })
                }
                $state.go('intelligent.engineering_process')
            }, function (error) {
                console.log(error)
            })
        }
        //工程标准保存
        $scope.get_engineering_process = function (valid) {
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $state.go('intelligent.engineering_standards')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            let arr = [], arr1 = [], arr2 = []
            if ($scope.cur_item_project != '木作') {
                for (let [key, value] of $scope.process_list.entries()) {
                    for (let [key1, value1] of value.entries()) {
                        arr.push({id: value1.id, material: value1.material})
                    }
                }
            } else {
                for (let [key, value] of $scope.all_series.entries()) {
                    for (let [key1, value1] of value.entries()) {
                        if (value1.cur_id != undefined) {
                            arr.push({
                                id: value1.cur_id,
                                value: value1.value
                            })
                        } else {
                            arr.push({
                                project: value1.series,
                                value: value1.value,
                                coefficient: value1.coefficient,
                                series_or_style: value1.series_or_style
                            })
                        }
                    }
                }
                for (let [key, value] of $scope.all_style.entries()) {
                    for (let [key1, value1] of value.entries()) {
                        if (value1.cur_id != undefined) {
                            arr.push({
                                id: value1.cur_id,
                                value: value1.value
                            })
                        } else {
                            arr.push({
                                project: value1.style,
                                value: value1.value,
                                coefficient: value1.coefficient,
                                series_or_style: value1.series_or_style
                            })
                        }
                    }
                }
                for (let [key, value] of $scope.process_list.entries()) {
                    for (let [key1, value1] of value.entries()) {
                        if (value1.options != undefined) {
                            arr1.push({
                                id: value1.id,
                                value: value1.value
                            })
                        } else {
                            arr2.push({
                                id: value1.id,
                                value: value1.material
                            })
                        }
                    }
                }
            }
            console.log(arr)
            console.log(arr1)
            console.log(arr2)
            if (valid) {
                if ($scope.cur_item_project != '木作') {
                    $http.post('/quote/project-norm-edit', {
                        material: arr
                    }, config).then(function (response) {
                        console.log(response)
                        $scope.second_title = '工程标准'
                        $scope.three_title = ''
                        $scope.four_title = ''
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    }, function (error) {
                        console.log(error)
                    })
                } else {
                    $http.post('/quote/project-norm-woodwork-edit', {
                        value: arr2,
                        specification: arr1,
                        coefficient: arr
                    }, config).then(function (res) {
                        console.log(res)
                        $scope.second_title = '工程标准'
                        $scope.three_title = ''
                        $scope.four_title = ''
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    }, function (error) {
                        console.log(error)
                    })
                }
            } else {
                $scope.submitted = true
            }
        }
        //工程标准返回
        $scope.return_engineering = function () {
            $scope.second_title = '工程标准'
            $scope.three_title = ''
            $scope.four_title = ''
            $state.go('intelligent.engineering_standards')
        }

        /*系数管理*/
        //跳转系数管理
        $scope.go_coefficient_manage = function () {
            $scope.all_coefficient = []
            $scope.second_title = '系数管理'
            $http.get('/quote/coefficient-list').then(function (res) {
                console.log(res)
                $scope.cur_all_coefficient = res.data.coefficient
                $scope.cur_all_category = res.data.list
                for (let [key, value] of $scope.cur_all_coefficient.entries()) {
                    let arr = angular.copy($scope.cur_all_category)
                    let cur_project = arr.find(function (item) {
                        return item.title == value.classify
                    })
                    if ($scope.all_coefficient.length > 0) {
                        for (let [key1, value1] of $scope.all_coefficient.entries()) {
                            let index = arr.findIndex(function (item) {
                                return item.title == value1.title
                            })
                            console.log(arr.find(function (item) {
                                return item.title == value1.title
                            }))
                            arr.splice(index, 1)
                        }
                        $scope.all_coefficient.push({
                            id: value.id,
                            title: value.classify,
                            coefficient: value.coefficient,
                            category: arr.sort(function (a, b) {
                                return +a.id - b.id
                            }),
                            cur_category: cur_project,
                            flag: false
                        })
                    } else {
                        $scope.all_coefficient.push({
                            id: value.id,
                            title: value.classify,
                            coefficient: value.coefficient,
                            category: arr.sort(function (a, b) {
                                return +a.id - b.id
                            }),
                            cur_category: cur_project,
                            flag: false
                        })
                    }
                }
                $state.go('intelligent.coefficient_manage')
            }, function (error) {
                console.log(error)
            })
        }
        //添加各项系数
        $scope.add_coefficient = function () {
            let arr = angular.copy($scope.cur_all_category)
            for (let [key1, value1] of $scope.all_coefficient.entries()) {
                let index = arr.findIndex(function (item) {
                    return item.title == value1.title
                })
                console.log(arr.find(function (item) {
                    return item.title == value1.title
                }))
                arr.splice(index, 1)
            }
            if (arr.length != 0) {
                $scope.all_coefficient.push({
                    title: arr[0].title,
                    coefficient: '',
                    category: arr.sort(function (a, b) {
                        return +a.id - b.id
                    }),
                    cur_category: arr[0]
                })
            }
        }
        //移除系数项
        $scope.remove_coefficient = function (item) {
            let cur_category = item.cur_category
            let index = $scope.all_coefficient.findIndex(function (cur_item) {
                return cur_item.title == item.title
            })
            $scope.all_coefficient.splice(index, 1)
            for (let [key, value] of $scope.all_coefficient.entries()) {
                let cur_index = value.category.findIndex(function (cur_item) {
                    return cur_item.id == cur_category.id
                })
                if (cur_index == -1) {
                    value.category.push(cur_category)
                    value.category.sort(function (a, b) {
                        return +a.id - b.id;
                    })
                }
                console.log(value)
            }
        }
        //保存系数项
        $scope.save_coefficient = function (valid) {
            let cur = $scope
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    cur.second_title = ''
                    $state.go('intelligent.intelligent_index')
                }
            }
            let arr = []
            for (let [key, value] of $scope.all_coefficient.entries()) {
                arr.push({
                    classify: value.title,
                    coefficient: value.coefficient
                })
            }
            if (valid) {
                $http.post('/quote/coefficient-add', {
                    value: arr
                }, config).then(function (res) {
                    console.log(res)
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                }, function (error) {
                    console.log(error)
                })
            } else {
                $scope.submitted = true
            }
        }

        /*添加材料项*/
        //跳转添加材料项列表
        $scope.go_add_material = function () {
            $scope.second_title = '添加材料项'
            tablePages1()
            $state.go('intelligent.add_material')
        }
        //跳转添加材料详情
        $scope.go_material_detail = function () {
            $scope.submitted = false
            $scope.is_add_material = true
            $scope.cur_choose_index = 0
            $scope.basic_attr = ''//商品基本属性
            $scope.other_attr = ''//商品额外属性
            //获取一级、二级和三级分类
            $http.get('/quote/assort-goods').then(function (response) {//默认一级
                console.log(response)
                $scope.level_one = response.data.data.categories
                $scope.cur_level_one1 = $scope.level_one[0]
                $http.get('/quote/assort-goods', {//默认二级
                    params: {
                        pid: $scope.cur_level_one1.id
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.level_two = response.data.data.categories
                    $scope.cur_level_two1 = $scope.level_two[0]
                    $http.get('/quote/assort-goods', {//默认三级
                        params: {
                            pid: $scope.cur_level_two1.id
                        }
                    }).then(function (response) {
                        $scope.level_three = response.data.data.categories
                        $scope.cur_level_three1 = $scope.level_three[0]
                        $scope.three_title = '材料项详情'
                        $state.go('intelligent.material_detail')
                        console.log($scope.level_three)
                        console.log(response)
                    }, function (error) {
                        console.log(error)
                    })
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
            //初始化系列和风格以及户型
            for (let [key, value] of $scope.cur_all_series.entries()) {
                value.flag = false
                value.num = ''
            }
            for (let [key, value] of $scope.cur_all_style.entries()) {
                value.flag = true
                value.num = ''
            }
            for (let [key, value] of $scope.all_area_range.entries()) {
                value.flag = true
                value.area = ''
            }
        }
        //获取户型相关
        $http.get('/quote/house-type-list').then(function (res) {
            console.log(res)
            $scope.all_area_range = res.data.list
        }, function (error) {
            console.log(error)
        })
        //监听一级改变
        $scope.$watch('cur_level_one1', function (newVal, oldVal) {
            if (oldVal != undefined) {
                $http.get('/quote/assort-goods', {//默认二级
                    params: {
                        pid: newVal.id
                    }
                }).then(function (response) {
                    $scope.level_two = response.data.data.categories
                    $scope.cur_level_two1 = $scope.level_two[0]
                    $http.get('/quote/assort-goods', {//默认三级
                        params: {
                            pid: $scope.cur_level_two.id
                        }
                    }).then(function (response) {
                        $scope.level_three = response.data.data.categories
                        $scope.cur_level_three1 = $scope.level_three[0]
                        $state.go('intelligent.material_detail')
                        console.log($scope.level_three)
                        console.log(response)
                    }, function (error) {
                        console.log(error)
                    })
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }
        })
        //监听二级改变
        $scope.$watch('cur_level_two1', function (newVal, oldVal) {
            if (oldVal != undefined) {
                $http.get('/quote/assort-goods', {//默认三级
                    params: {
                        pid: newVal.id
                    }
                }).then(function (response) {
                    $scope.level_three = response.data.data.categories
                    $scope.cur_level_three1 = $scope.level_three[0]
                    $state.go('intelligent.material_detail')
                    console.log($scope.level_three)
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }
        })
        //抓取材料
        $scope.basic_attr = ''//商品基本属性
        $scope.other_attr = ''//商品额外属性
        $scope.get_material = function () {
            $http.post('/quote/decoration-add-classify', {
                classify: $scope.cur_level_three1.title
            }, config).then(function (res) {
                console.log(res)
                $scope.basic_attr = res.data.goods
                $scope.other_attr = res.data.goods_attr
            }, function (error) {
                console.log(error)
            })
        }
        //保存材料项详情
        $scope.save_material = function (valid) {
            console.log($scope.cur_choose_index)
            $scope.animationsEnabled = true;
            let cur = $scope
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                cur.three_title = ''
                $scope.common_house = function () {
                    $uibModalInstance.close()
                        $state.go('intelligent.add_material')
                }
            }
            $scope.toggleAnimation = function () {
                $scope.animationsEnabled = !$scope.animationsEnabled;
            };
            all_modal.$inject = ['$scope', '$uibModalInstance']
            let next_all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '抓取材料信息错误，请重新抓取'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                }
            }

            let arr = [], arr1 = [], arr2 = [], data = ''
            //保存系列相关
            for (let [key, value] of $scope.cur_all_series.entries()) {
                if(value.cur_id!=undefined){
                    arr.push({
                        id:value.cur_id,
                        series: value.id,
                        quantity: value.num != undefined ? value.num : ''
                    })
                }else{
                    arr.push({
                        series: value.id,
                        quantity: value.num != undefined ? value.num : ''
                    })
                }
            }
            //保存风格相关
            for (let [key, value] of $scope.cur_all_style.entries()) {
                if(value.cur_id!=undefined){
                    arr1.push({
                        id:value.cur_id,
                        style: value.id,
                        quantity: value.num != undefined ? value.num : ''
                    })
                }else{
                    arr1.push({
                        style: value.id,
                        quantity: value.num != undefined ? value.num : ''
                    })
                }
            }
            //保存户型相关
            for (let [key, value] of $scope.all_area_range.entries()) {
                if(value.cur_id!=undefined){
                    arr2.push({
                        id:value.cur_id,
                        min_area: value.min_area,
                        max_area: value.max_area,
                        quantity: value.area != undefined ? value.area : ''
                    })
                }else{
                    arr2.push({
                        min_area: value.min_area,
                        max_area: value.max_area,
                        quantity: value.area != undefined ? value.area : ''
                    })
                }
            }
            console.log($scope.cur_level_three)
            if ($scope.is_add_material) {
                data = {
                    province: $scope.cur_province.id,
                    city: $scope.cur_city.id,
                    code: $scope.basic_attr.sku,
                    one_name: $scope.cur_level_one1.title,
                    two_name: $scope.cur_level_two1.title,
                    three_name: $scope.cur_level_three1.title,
                    message: $scope.cur_choose_index == 0 ? '系列相关' : ($scope.cur_choose_index == 1 ? '风格相关' : '户型相关'),
                    add: $scope.cur_choose_index == 0 ? arr : ($scope.cur_choose_index == 1 ? arr1 : arr2)
                }
            } else {
                data = {
                    id: $scope.material_category.id,
                    code: $scope.basic_attr.sku,
                    message: $scope.cur_choose_index == 0 ? '系列相关' : ($scope.cur_choose_index == 1 ? '风格相关' : '户型相关'),
                    add: $scope.cur_choose_index == 0 ? arr : ($scope.cur_choose_index == 1 ? arr1 : arr2)
                }
            }
            next_all_modal.$inject = ['$scope', '$uibModalInstance']
            if (valid) {
                if (!!$scope.basic_attr) {
                    if ($scope.is_add_material) {
                        $http.post('/quote/decoration-add', data, config).then(function (res) {
                            console.log(res)
                            $http.post('/quote/decoration-list', {}, config).then(function (res) {
                                console.log(res)
                                $scope.material_list = res.data.list.details
                            }, function (error) {
                                console.log(error)
                            })
                            $uibModal.open({
                                templateUrl: 'pages/intelligent/cur_model.html',
                                controller: all_modal
                            })
                        }, function (error) {
                            console.log(error)
                        })
                    } else {
                        $http.post('/quote/decoration-edit', data, config).then(function (res) {
                            console.log(res)
                            $http.post('/quote/decoration-list', {}, config).then(function (res) {
                                console.log(res)
                                $scope.material_list = res.data.list.details
                            }, function (error) {
                                console.log(error)
                            })
                           var model_uib =  $uibModal.open({
                                animation:$scope.animationsEnabled,
                                templateUrl: 'pages/intelligent/cur_model.html',
                                controller: all_modal,
                                backdrop:'static',
                               
                           })
                            model_uib.result.then(function (result) {
                                console.log(result)
                            },function (reason) {
                                console.log(reason)
                            })
                        }, function (error) {
                            console.log(error)
                        })
                    }
                } else {
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: next_all_modal
                    })
                }
                $scope.submitted = false
            } else {
                $scope.submitted = true
            }
        }
//编辑材料项
        $scope.edit_material = function (item) {
            $scope.is_add_material = false
            $scope.submitted = false
            $http.post('/quote/decoration-edit-list', {
                id: item.id
            }, config).then(function (res) {
                console.log(res)
                $scope.material_category = {
                    first_level: res.data.decoration_add.one_materials,
                    second_level: res.data.decoration_add.two_materials,
                    third_level: res.data.decoration_add.three_materials,
                    id: res.data.decoration_add.id
                }
                $scope.basic_attr = res.data.goods
                $scope.other_attr = res.data.goods_attr
                if (res.data.decoration_add.correlation_message == '系列相关') {
                    $scope.cur_choose_index = 0
                    for (let [key, value] of $scope.cur_all_series.entries()) {
                        let cur_index = res.data.decoration_message.findIndex(function (item) {
                            return value.id == item.series_id
                        })
                        value.flag = false
                        if (cur_index != -1) {
                            value.num = res.data.decoration_message[cur_index].quantity
                            value.cur_id = res.data.decoration_message[cur_index].id
                        } else {
                            value.num = ''
                        }
                    }
                } else if (res.data.decoration_add.correlation_message == '风格相关') {
                    $scope.cur_choose_index = 1
                    for (let [key, value] of $scope.cur_all_style.entries()) {
                        let cur_index = res.data.decoration_message.findIndex(function (item) {
                            return value.id == item.style_id
                        })
                        value.flag = false
                        if (cur_index != -1) {
                            value.num = res.data.decoration_message[cur_index].quantity
                            value.cur_id = res.data.decoration_message[cur_index].id
                        } else {
                            value.num = ''
                        }
                    }
                } else {
                    $scope.cur_choose_index = 2
                    for (let [key, value] of $scope.all_area_range.entries()) {
                        let cur_index = res.data.decoration_message.findIndex(function (item) {
                            return value.min_area == item.min_area
                        })
                        value.flag = false
                        if (cur_index != -1) {
                            value.area = res.data.decoration_message[cur_index].quantity
                            value.cur_id = res.data.decoration_message[cur_index].id
                        } else {
                            value.num = ''
                        }
                    }
                }
                $scope.three_title = '材料项详情'
                $state.go('intelligent.material_detail')
            }, function (error) {
                console.log(error)
            })
        }
//返回添加材料项列表
        $scope.material_return = function () {
            $scope.three_title = ''
            $state.go('intelligent.add_material')
        }
//删除材料项模态框
        $scope.delete_material = function (item) {
            console.log($scope.cur_imgSrc)
            let data = $scope
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '是否确认删除'
                $scope.is_cancel = true
                $scope.common_house = function () {
                    $http.post('/quote/decoration-del', {
                        id: item.id
                    }, config).then(function (res) {
                        console.log(res)
                        $http.post('/quote/decoration-list', {}, config).then(function (res) {
                            console.log(res)
                            data.material_list = res.data.list.details
                        }, function (error) {
                            console.log(error)
                        })
                        $uibModalInstance.close()
                        $scope.is_cancel = false
                    }, function (error) {
                        console.log(error)
                    })
                }
                $scope.cancel_delete = function () {
                    $uibModalInstance.close()
                    $scope.is_cancel = false
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            $uibModal.open({
                templateUrl: 'pages/intelligent/cur_model.html',
                controller: all_modal
            })
        }
        /*户型面积*/
//跳转户型面积
//添加面积范围
        $scope.all_area_range = []
        $scope.usable_area_range = [{
            min_area: 1,
            max_area: 180
        }]
        $scope.area_tips = ''
//添加户型面积范围
        $scope.add_area_range = function (valid) {
            console.log($scope.usable_area_range)
            if ($scope.all_area_range.length != 0) {
                let cur_data = $scope.all_area_range[$scope.all_area_range.length - 1]
                console.log($scope.all_area_range)
                console.log(cur_data)
                let is_valid = $scope.usable_area_range.findIndex(function (item) {
                    return +cur_data.min_area >= +item.min_area && +cur_data.max_area <= +item.max_area
                })
                console.log(is_valid)
                if (valid) {
                    if (+cur_data.min_area < +cur_data.max_area) {
                        if (is_valid != -1) {
                            $scope.all_area_range.push({
                                min_area: '',
                                max_area: ''
                            })
                            let arr = []
                            for (let [key, value] of $scope.usable_area_range.entries()) {
                                if (key == is_valid) {
                                    arr.push({
                                        min_area: value.min_area,
                                        max_area: cur_data.min_area-1
                                    }, {
                                        min_area: +cur_data.max_area+1,
                                        max_area: value.max_area
                                    })
                                } else {
                                    arr.push(value)
                                }
                            }
                            $scope.usable_area_range = arr
                            $scope.area_tips = ''
                        } else {
                            $scope.area_tips = '请不要填写已添加范围内的数值'
                        }
                    } else {
                        $scope.area_tips = '请填写的最大面积大于最小面积'
                    }
                } else {
                    $scope.area_tips = '请填写0＜X≤180的整数字'
                }
            } else {
                $scope.all_area_range.push({
                    min_area: '',
                    max_area: ''
                })
            }
        }
//移除户型面积范围
        $scope.remove_area_range = function (item) {
            let cur_item = item
            let cur_index2 = $scope.all_area_range.findIndex(function (item1) {
                return item1.min_area == cur_item.min_area
            })
            $scope.all_area_range.splice(cur_index2,1)
            let cur_index = $scope.usable_area_range.findIndex(function (item1) {
                return +cur_item.max_area +1 == +item1.min_area
            })
            let cur_index1 = $scope.usable_area_range.findIndex(function (item1) {
                return +cur_item.min_area - 1 == +item1.max_area
            })
            if(cur_index != -1){
                $scope.usable_area_range[cur_index] = {
                    min_area:cur_item.min_area,
                    max_area:$scope.usable_area_range[cur_index].max_area
                }
            }else{
                if(cur_index1 != -1){
                    $scope.usable_area_range[cur_index1] = {
                        min_area:$scope.usable_area_range[cur_index1].min_area,
                        max_area:cur_item.max_area
                    }
                }else{
                    console.log(111)
                    $scope.usable_area_range.push({
                        min_area:cur_item.min_area,
                        max_area:cur_item.max_area
                    })
                }
            }
            console.log($scope.usable_area_range)
        }
//保存户型面积
        $scope.save_area = function (valid) {
            let cur_data = $scope.all_area_range[$scope.all_area_range.length - 1]
            console.log(cur_data)
            console.log($scope.usable_area_range)
            let is_valid = $scope.usable_area_range.findIndex(function (item) {
                return +cur_data.min_area >= +item.min_area && +cur_data.max_area <= +item.max_area
            })
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    $state.go('intelligent.general_manage')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            console.log(is_valid)
            let arr = []
            for (let [key, value] of $scope.all_area_range.entries()) {
                arr.push({
                    add_id:$scope.cur_edit_item.id,
                    min_area: value.min_area,
                    max_area: value.max_area
                })
            }
            if (valid) {
                if (+cur_data.min_area < +cur_data.max_area) {
                    if (is_valid != -1) {
                        $scope.area_tips = ''
                        $http.post('/quote/commonality-else-edit', {
                            apartment_area: arr.sort(function (a, b) {
                                return +a.min_area - b.min_area
                            })
                        }, config).then(function (res) {
                            console.log(res)
                            $scope.three_title = ''
                            $uibModal.open({
                                templateUrl: 'pages/intelligent/cur_model.html',
                                controller: all_modal
                            })
                        }, function (error) {
                            console.log(error)
                        })
                    } else {
                        $scope.area_tips = '请不要填写已添加范围内的数值'
                    }
                } else {
                    $scope.area_tips = '请填写的最大面积大于最小面积'
                }
            } else {
                $scope.area_tips = '请填写0＜X≤180的整数字'
            }
        }

        /*通用管理*/
//跳转通用管理页
        $scope.go_general_manage = function () {
            $http.get('/quote/commonality-list').then(function (res) {
                console.log(res)
                $scope.all_general_manage = res.data.post
                for(let [key,value] of $scope.all_general_manage.entries()){
                    if(value.title=='强电'||value.title=='弱电'||value.title=='水路'){
                        value.title = value.title + '点位'
                    }
                }
                $scope.second_title = '通用管理'
                $state.go('intelligent.general_manage')
            }, function (error) {
                console.log(error)
            })
        }
        //编辑通用管理
        $scope.go_general_detail = function (item) {
            $scope.cur_edit_item = item
            if(item.title.indexOf('点位')!=-1){
                $http.post('/quote/commonality-title',{
                    id:item.id
                },config).then(function (res) {
                    console.log(res)
                    $scope.one_title = res.data.list.one_title
                    $scope.cur_general_count = res.data.count
                    for(let [key,value] of $scope.one_title.entries()){
                        value['two_title'] = []
                    }
                    for(let [key,value] of res.data.list.two_title.entries()){
                        let cur_index = $scope.one_title.findIndex(function (item) {
                            return item.id == value.pid
                        })
                        $scope.one_title[cur_index].two_title.push(value)
                    }
                    console.log($scope.one_title)
                    $scope.del_two_id = []
                    $scope.cur_one_title = angular.copy($scope.one_title,['destination'])
                    console.log($scope.cur_one_title)
                    $state.go('intelligent.general_detail')
                },function (error) {
                    console.log(error)
                })
            }else{
                $scope.regx = $scope.cur_edit_item.title == '杂工'?/^\d{0,}(.5)?$/:/^\d{0,}$/
                    $http.post('/quote/commonality-else-list',{
                        id:item.id
                    },config).then(function (res) {
                        console.log(res)
                        $scope.main_item = res.data.list
                        $scope.area_item = res.data.area
                        $scope.all_area_range = res.data.apartment_area
                        $scope.else_area_range = res.data.else_area
                        $scope.cur_area_range = []
                        $scope.three_title = '通用管理详情'
                        console.log($scope.else_area_range)
                        if(item.title == '户型面积'){
                            $scope.three_title = '户型面积'
                            let arr = []
                            if($scope.all_area_range.length!=0){
                                $scope.all_area_range.reduce(function (prev, cur, index) {
                                    if (+prev.max_area + 1 == +cur.min_area) {
                                        console.log(prev)
                                        // console.l    og(&&index!=$scope.all_area_range.length-1)
                                        if(index != $scope.all_area_range.length - 1){
                                            return {min_area: prev.min_area, max_area: cur.max_area}
                                        }else{
                                            arr.push(prev)
                                        }
                                    }else{
                                        arr.push(prev)
                                    }
                                })
                            }
                            console.log(arr)
                            let arr1 = $scope.usable_area_range
                            for (let [key, value] of arr.entries()) {
                                let cur_index = arr1.findIndex(function (item) {
                                    return +value.min_area >= +item.min_area && +value.max_area <= +item.max_area
                                })
                                if (cur_index != -1) {
                                    if (+value.min_area == +arr1[cur_index].min_area && +value.max_area == +arr1[cur_index].max_area) {
                                        arr1.splice(cur_index, 1).sort(function (a, b) {
                                            return +a.min_area - b.min_area
                                        })
                                    } else if (+value.min_area == +arr1[cur_index].min_area && +value.max_area != +arr1[cur_index].max_area) {
                                        arr1.push({
                                            min_area: +value.max_area+1,
                                            max_area: arr1[cur_index].max_area
                                        })
                                        arr1.splice(cur_index, 1).sort(function (a, b) {
                                            return +a.min_area - b.min_area
                                        })
                                    } else if (+value.min_area != +arr1[cur_index].min_area && +value.max_area == +arr1[cur_index].max_area) {
                                        arr1.push({
                                            min_area: arr1[cur_index].min_area,
                                            max_area: +value.min_area-1
                                        })
                                        arr1.splice(cur_index, 1).sort(function (a, b) {
                                            return +a.min_area - b.min_area
                                        })
                                    } else {
                                        arr1.push({
                                            min_area: arr1[cur_index].min_area,
                                            max_area: +value.min_area-1
                                        }, {
                                            min_area: +value.max_area+1,
                                            max_area: arr1[cur_index].max_area
                                        })
                                        arr1.splice(cur_index, 1).sort(function (a, b) {
                                            return +a.min_area - b.min_area
                                        })
                                    }
                                }
                            }
                            console.log(arr1)
                            $scope.usable_area_range = arr1
                            console.log($scope.usable_area_range)
                            $state.go('intelligent.house_area')
                        }else if(item.title == '面积比例'){
                            for(let [key,value] of $scope.main_item.entries()){
                                value.project += '百分比'
                            }
                            $state.go('intelligent.else_general_manage')
                        }else if(item.title == '木作'){
                            $state.go('intelligent.else_general_manage')
                        }else if(item.title == '杂工'||item.title == '防水'){
                            if(item.title == '杂工'){
                                $scope.cur_area_range = [{
                                    title:'其他杂工天数',
                                    all_area_range:[]
                                }]
                            }else{
                                $scope.cur_area_range = [{
                                    title:'其他防水面积',
                                    all_area_range:[]
                                }]
                            }
                            console.log($scope.cur_area_range[0].all_area_range)
                            for(let [key,value] of $scope.else_area_range.entries()){
                                let cur_index = res.data.area.findIndex(function (item) {
                                    return item.min_area == value.min_area && item.max_area == value.max_area
                                })
                                console.log(res.data.area[3])
                                if(cur_index == -1){
                                    $scope.cur_area_range[0].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:''
                                    })
                                }else{
                                    $scope.cur_area_range[0].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:res.data.area[cur_index].project_value,
                                        id:res.data.area[cur_index].id
                                    })
                                }
                            }
                            console.log($scope.cur_area_range)
                            $state.go('intelligent.else_general_manage')
                        }else if(item.title=='油漆'){
                            $scope.cur_area_range = [{
                                title:'其他乳胶漆面积',
                                all_area_range:[]
                            },{
                                title:'其他腻子面积',
                                all_area_range:[]
                            },{
                                title:'其他阴角线长度',
                                all_area_range:[]
                            }]
                            //整理乳胶漆面积
                            for(let [key,value] of $scope.else_area_range.entries()){
                                let cur_index = res.data.area.findIndex(function (item) {
                                    return item.min_area == value.min_area && item.max_area == value.max_area&&item.project_name.indexOf('乳胶漆')!=-1
                                })
                                if(cur_index != -1){
                                    $scope.cur_area_range[0].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:res.data.area[cur_index].project_value,
                                        id:res.data.area[cur_index].id
                                    })
                                }else{
                                    $scope.cur_area_range[0].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:''
                                    })
                                }
                            }
                            //整理腻子面积
                            for(let [key,value] of $scope.else_area_range.entries()){
                                let cur_index = res.data.area.findIndex(function (item) {
                                    return item.min_area == value.min_area && item.max_area == value.max_area&&item.project_name.indexOf('腻子')!=-1
                                })
                                if(cur_index != -1){
                                    $scope.cur_area_range[1].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:res.data.area[cur_index].project_value,
                                        id:res.data.area[cur_index].id
                                    })
                                }else{
                                    $scope.cur_area_range[1].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:''
                                    })
                                }
                            }
                            //整理阴角线面积
                            for(let [key,value] of $scope.else_area_range.entries()){
                                let cur_index = res.data.area.findIndex(function (item) {
                                    return item.min_area == value.min_area && item.max_area == value.max_area&&item.project_name.indexOf('阴角线')!=-1
                                })
                                if(cur_index != -1){
                                    $scope.cur_area_range[2].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:res.data.area[cur_index].project_value,
                                        id:res.data.area[cur_index].id
                                    })
                                }else{
                                    $scope.cur_area_range[2].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:''
                                    })
                                }
                            }
                            $state.go('intelligent.else_general_manage')
                        }else if(item.title == '泥作'){
                            $scope.cur_area_range = [{
                                title:'其他地面积',
                                all_area_range:[]
                            },{
                                title:'其他墙面积',
                                all_area_range:[]
                            }]
                            //整理地面积
                            for(let [key,value] of $scope.else_area_range.entries()){
                                let cur_index = res.data.area.findIndex(function (item) {
                                    return item.min_area == value.min_area && item.max_area == value.max_area&&item.project_name.indexOf('地面积')!=-1
                                })
                                if(cur_index != -1){
                                    $scope.cur_area_range[0].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:res.data.area[cur_index].project_value,
                                        id:res.data.area[cur_index].id
                                    })
                                }else{
                                    $scope.cur_area_range[0].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:''
                                    })
                                }
                            }
                            //整理墙面积
                            for(let [key,value] of $scope.else_area_range.entries()){
                                let cur_index = res.data.area.findIndex(function (item) {
                                    return item.min_area == value.min_area && item.max_area == value.max_area&&item.project_name.indexOf('墙面积')!=-1
                                })
                                if(cur_index != -1){
                                    $scope.cur_area_range[1].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:res.data.area[cur_index].project_value,
                                        id:res.data.area[cur_index].id
                                    })
                                }else{
                                    $scope.cur_area_range[1].all_area_range.push({
                                        min_area:value.min_area,
                                        max_area:value.max_area,
                                        num:''
                                    })
                                }
                            }
                            $state.go('intelligent.else_general_manage')
                        }
                        console.log(res)
                    },function (error) {
                        console.log(error)
                    })
                }
        }
        //添加一级标题
        $scope.add_first_title = function () {
            $scope.one_title.push({
                title:''
            })
            $scope.cur_one_title = angular.copy($scope.one_title,['destination'])
            console.log($scope.one_title)
        }
        //添加二级标题
        $scope.add_second_title = function (item) {
            item.two_title.push({
                title:'',
                count:'',
                pid:item.id
            })
            $scope.cur_one_title = angular.copy($scope.one_title,['destination'])
        }
        //移除一级标题
        $scope.remove_first_title = function (index,item) {
            if(item.id == undefined){
                $scope.one_title.splice(index,1)
            }else{
                for(let [key,value] of item.two_title.entries()){
                    $scope.cur_general_count.count = +$scope.cur_general_count.count - value.count
                }
                $http.post('/quote/commonality-title-add',{
                    del_id:item.id
                },config).then(function (res) {
                    console.log(res)
                    $http.post('/quote/commonality-title',{
                        id:$scope.cur_general_count.id
                    },config).then(function (res) {
                        console.log(res)
                        $scope.one_title = res.data.list.one_title
                        // $scope.cur_general_count = res.data.count
                        for(let [key,value] of $scope.one_title.entries()){
                            value['two_title'] = []
                        }
                        for(let [key,value] of res.data.list.two_title.entries()){
                            let cur_index = $scope.one_title.findIndex(function (item) {
                                return item.id == value.pid
                            })
                            $scope.one_title[cur_index].two_title.push(value)
                        }
                        for(let [key,value] of $scope.one_title.entries()){
                            if(value.two_title.length == 0){
                                value.two_title.push({
                                    title:'',
                                    count:'',
                                    pid:value.id
                                })
                            }
                        }
                        console.log($scope.one_title)
                    },function (error) {
                        console.log(error)
                    })
                },function (error) {
                    console.log(error)
                })
            }
        }
        //移除二级标题
        $scope.remove_second_title = function (index,item) {
            if(item.id!=undefined){
                $scope.del_two_id.push(+item.two_title[index].id)
            }
            $scope.cur_general_count.count = +$scope.cur_general_count.count - item.two_title[index].count
            item.two_title.splice(index,1)
        }
        //改变单项点位计算总点位
        $scope.get_all_count = function (parent_index,index,count=0) {
            console.log($scope.cur_one_title[parent_index].two_title[index])
            if(!isNaN(+count)){
                $scope.cur_general_count.count = +$scope.cur_general_count.count + (+count - $scope.cur_one_title[parent_index]
                    .two_title[index].count)
                $scope.cur_one_title[parent_index].two_title[index].count = count
            }
        }
        //返回通用管理列表
        $scope.return_general_manage = function () {
            $state.go('intelligent.general_manage')
        }
        //保存水电通用管理
        $scope.save_general_manage = function (valid,index,item) {
            let arr = []
            if(index == 1){
                if(item.title!=''){
                    $http.post('/quote/commonality-title-add',{
                        one_title:{id:$scope.cur_general_count.id,title:item.title}
                    },config).then(function (res) {
                        console.log(res)
                        $http.post('/quote/commonality-title',{
                            id:$scope.cur_general_count.id
                        },config).then(function (res) {
                            console.log(res)
                            $scope.one_title = res.data.list.one_title
                            for(let [key,value] of $scope.one_title.entries()){
                                value['two_title'] = []
                            }
                            for(let [key,value] of res.data.list.two_title.entries()){
                                let cur_index = $scope.one_title.findIndex(function (item) {
                                    return item.id == value.pid
                                })
                                $scope.one_title[cur_index].two_title.push(value)
                            }
                            for(let [key,value] of $scope.one_title.entries()){
                                if(value.two_title.length == 0){
                                    value.two_title.push({
                                        title:'',
                                        count:'',
                                        pid:value.id
                                    })
                                }
                            }
                            $scope.cur_one_title = angular.copy($scope.one_title,['destination'])
                            console.log($scope.one_title)
                        },function (error) {
                            console.log(error)
                        })
                    },function (error) {
                        console.log(error)
                    })
                }else{

                }
            }else{
                for(let [key,value] of $scope.one_title.entries()){
                    for(let [key1,value1] of value.two_title.entries()){
                        if(value1.id == undefined){
                            arr.push({
                                id:value.id,
                                title:value1.title,
                                count:value1.count
                            })
                        }else{
                            arr.push({
                                edit_id:value1.id,
                                title:value1.title,
                                count:value1.count
                            })
                        }
                    }
                }
                if(valid){
                    let all_data = ''
                    let cur = $scope
                    let all_modal = function ($scope, $uibModalInstance) {
                        $scope.cur_title = '保存成功'
                        $scope.common_house = function () {
                            $uibModalInstance.close()
                            cur.three_title = ''
                            $state.go('intelligent.general_manage')
                        }
                    }
                    all_modal.$inject = ['$scope', '$uibModalInstance']
                    if($scope.del_two_id.length == 0){
                        all_data = {
                            two_title:arr,
                            count:{id:$scope.cur_general_count.id,count:$scope.cur_general_count.count,title:$scope.cur_general_count.title}
                        }
                    }else{
                        all_data = {
                            two_title:arr,
                            del_id:$scope.del_two_id,
                            count:{id:$scope.cur_general_count.id,count:$scope.cur_general_count.count,title:$scope.cur_general_count.title}
                        }
                    }
                    $http.post('/quote/commonality-title-two-add',all_data,config).then(function (res) {
                        console.log(res)
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    },function (error) {
                        console.log(error)
                    })
                }
            }
        }
        //保存其他通用管理
        $scope.save_else_general = function (valid) {
            console.log($scope.cur_area_range)
            console.log(valid)
            $scope.true_data = true
            let cur = $scope
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    cur.three_title = ''
                    $state.go('intelligent.general_manage')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            if($scope.cur_edit_item.title == '面积比例'){
                let data = $scope.main_item.reduce(function (prev,cur,index) {
                    console.log(prev)
                    return +prev+(+cur.project_value)
                },0)
                if(data > 100){
                    $scope.true_data = false
                }else{
                    $scope.true_data = true
                }
            }
            console.log($scope.main_item)
            console.log($scope.cur_area_range)
            let arr = [],arr1=[]
            for(let [key,value] of $scope.main_item.entries()){
                arr.push({
                   id:value.id,
                    coefficient:value.project_value
                })
            }
            for(let [key,value] of $scope.cur_area_range.entries()){
                for(let [key1,value1] of value.all_area_range.entries()){
                    if(value1.id == undefined){
                        arr1.push({
                            min_area:value1.min_area,
                            max_area:value1.max_area,
                            project_value:value1.num,
                            project_name:value.title,
                            points_id:$scope.cur_edit_item.id
                        })
                    }else{
                        arr1.push({
                            value:value1.num,
                            id:value1.id
                        })
                    }
                }
            }
            if(valid&&$scope.true_data){
                $http.post('/quote/commonality-else-edit',{
                    else:[{
                        value:arr,
                        apartment_area:arr1
                    }]
                },config).then(function (res) {
                   console.log(res)
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                },function (error) {
                    console.log(error)
                })
            }else{
                $scope.submitted = true
            }
        }
        /*智能报价商品管理*/
        //跳转智能报价商品管理页
        $scope.go_goods_manage = function () {
            $scope.check_item = []
            $scope.submitted = false
            $http.get('/quote/goods-management-list').then(function (res) {
                console.log(res)
                $scope.check_item = res.data.list
                $scope.second_title = '智能报价商品管理'
            },function (error) {
                console.log(error)
            })
            $http.get('/quote/assort-goods').then(function (response) {//默认一级
                console.log(response)
                $scope.level_one = response.data.data.categories
                $scope.cur_level_one = $scope.level_one[0]
                $http.get('/quote/assort-goods', {//默认二级
                    params: {
                        pid: $scope.cur_level_one.id
                    }
                }).then(function (response) {
                    $scope.level_two = response.data.data.categories
                    $scope.cur_level_two = $scope.level_two[0]
                    $http.get('/quote/assort-goods', {//默认三级
                        params: {
                            pid: $scope.cur_level_two.id
                        }
                    }).then(function (response) {
                        $scope.level_three = response.data.data.categories
                        for (let [key, value] of $scope.level_three.entries()) {
                            if ($scope.check_item.length == 0) {
                                value['complete'] = false
                            } else {
                                let cur_index = $scope.check_item.findIndex(function (item) {
                                    return item.id == value.id
                                })
                                    if (cur_index != -1) {
                                        value['complete'] = true
                                    }
                            }
                        }
                        console.log($scope.level_three)
                        console.log(response)
                    }, function (error) {
                        console.log(error)
                    })
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
            $state.go('intelligent.goods_manage')
        }
        //保存智能报价商品管理
        $scope.save_goods_manage = function (valid) {
            console.log(valid)
            console.log($scope.check_item)
            let cur = $scope
            let all_modal = function ($scope, $uibModalInstance) {
                $scope.cur_title = '保存成功'
                $scope.common_house = function () {
                    $uibModalInstance.close()
                    cur.second_title = ''
                    $state.go('intelligent.intelligent_index')
                }
            }
            all_modal.$inject = ['$scope', '$uibModalInstance']
            let arr = []
            for(let [key,value] of $scope.check_item.entries()){
                arr.push({
                    id:value.id,
                    pid:value.pid,
                    title:value.title,
                    path:value.path,
                    quantity:value.quantity
                })
            }
            if(valid){
                $http.post('/quote/goods-management-add',{
                    add_item:arr
                },config).then(function (res) {
                    console.log(res)
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                },function (error) {
                    console.log(error)
                })
            }else{
                $scope.submitted = true
            }
        }
    })