angular.module('intelligent_index', ['ngFileUpload', 'ui.bootstrap'])
    .controller('intelligent_ctrl', function ($scope, $state, $stateParams, $uibModal, $http, $timeout, Upload, $location, $anchorScroll, $window) {
        //公共配置以及一些变量初始化
        //post请求配置
        const config = {
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (data) {
                return $.param(data)
            }
        };
        $scope.ctrlScope = $scope//解决ngModel无法双向绑定
        $scope.second_title = ''//二级列表项初始化
        $scope.three_title = ''//三级列表项初始化
        $scope.search_txt = ''//小区搜索文本
        // $scope.pic_error = ''
        $scope.cur_num = 0
        $scope.cur_page = 1
        $scope.address = ''//小区详细地址
        $scope.house_name = ''//小区名称
        $scope.all_num = ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十"]//中文排序
        let arr = []
        //获取案例需要添加的商品编号和数量
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
        //案例页工人
        $http.get('/quote/labor-list').then(function (response) {
            console.log(response)
            $scope.common_worker = angular.copy(response.data.labor_list)
            for (let [key, value] of $scope.common_worker.entries()) {
                if (value.worker_kind == '杂工' && key != $scope.common_worker.length - 1) {
                    value['price'] = ''
                    $scope.common_worker.splice(key, 1)
                    $scope.common_worker.push(value)
                }
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
                        $scope.cur_house_information.cur_imgSrc = response.data.data.file_path
                        $scope.a_pic_error = ''
                    } else {
                        $scope.cur_house_information.cur_imgSrc = ''
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
        //默认登录状态(后期会删除)
        $http.post('/site/login', {
            'username': 13551201821,
            'password': 'demo123'
        }, config).then(function (response) {
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
            $scope.cur_county = $scope.county[0]
            console.log($scope.cur_county.id)
            //初始化省市区获取全市小区信息
            $http.get('/quote/plot-list', {
                params: {
                    'post': $scope.cur_county.id,
                    'page':$scope.cur_page
                }
            }).then(function (response) {
                $scope.total_pages = []
                let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                for (let i = 1; i <= num; i++) {
                    $scope.total_pages.push(i)
                }
                $scope.house_detail = response.data.model.details
                console.log($scope.total_pages)
                console.log(response)
            }, function (error) {
                console.log(error)
            })
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
                $scope.cur_county = $scope.county[0]
                //改变省获取全市小区信息
                $http.get('/quote/plot-list', {
                    params: {
                        'post': $scope.cur_county.id,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                }, function (error) {
                    console.log(error)
                })
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
                $scope.cur_county = $scope.county[0]
                //改变市获取全市小区信息
                $http.get('/quote/plot-list', {
                    params: {
                        'post': $scope.cur_county.id,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }, function (error) {
                console.log(error)
            })
        }
        //分页根据页码跳转
        $scope.topage = ''
        $scope.choosePage = function (page) {
            $scope.cur_page = page
            if($scope.start_time!=''&&$scope.end_time!=''){
                $http.get('/quote/plot-time-grabble', {
                    params: {
                        'city': $scope.cur_city.id,
                        'min': $scope.start_time,
                        'max': $scope.end_time,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }else if($scope.search_txt!=''){
                $http.get('/quote/plot-grabble', {
                    params: {
                        'city': $scope.cur_city.id,
                        'toponymy': $scope.search_txt,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }else{
                $http.get('/quote/plot-list', {
                    params: {
                        'post': $scope.cur_county.id,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                }, function (error) {
                    console.log(error)
                })
            }
        }
        //上一页
        $scope.Previous = function () {
            if($scope.cur_page == 1){
                $scope.cur_page = 1
            }else{
                $scope.cur_page--
            }
            console.log($scope.cur_page)
            if($scope.start_time!=''&&$scope.end_time!=''){
                $http.get('/quote/plot-time-grabble', {
                    params: {
                        'city': $scope.cur_city.id,
                        'min': $scope.start_time,
                        'max': $scope.end_time,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }else if(($scope.start_time!=''||$scope.end_time!='')&&$scope.search_txt!=''){
                $http.get('/quote/plot-grabble', {
                    params: {
                        'city': $scope.cur_city.id,
                        'toponymy': $scope.search_txt,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }else{
                $http.get('/quote/plot-list', {
                    params: {
                        'post': $scope.cur_county.id,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                }, function (error) {
                    console.log(error)
                })
            }
        }
        //下一页
        $scope.Next = function () {
            if($scope.cur_page == $scope.total_pages.length){
                $scope.cur_page = $scope.total_pages.lengt
            }else{
                $scope.cur_page++
            }
            if($scope.start_time!=''&&$scope.end_time!=''){
                $http.get('/quote/plot-time-grabble', {
                    params: {
                        'city': $scope.cur_city.id,
                        'min': $scope.start_time,
                        'max': $scope.end_time,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }else if($scope.search_txt!=''){
                $http.get('/quote/plot-grabble', {
                    params: {
                        'city': $scope.cur_city.id,
                        'toponymy': $scope.search_txt,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }else{
                $http.get('/quote/plot-list', {
                    params: {
                        'post': $scope.cur_county.id,
                        'page':$scope.cur_page
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                }, function (error) {
                    console.log(error)
                })
            }
        }
        //所有搜索小区的方式
        //切换区域获取小区列表
        // $scope.$watch('cur_county', function (newVal, oldVal) {
        //     // $scope.start_time = ''
        //     // $scope.end_time  = ''
        //     // $scope.search_txt = ''
        //     console.log(newVal)
        //     if (!!newVal) {
        //         $http.get('/quote/plot-list', {
        //             params: {
        //                 'post': newVal.id
        //             }
        //         }).then(function (response) {
        //             $scope.house_detail = response.data.model.details
        //             console.log(response)
        //         }, function (error) {
        //             console.log(error)
        //         })
        //     }
        // })
        $scope.cur_county_to_house = function () {
            $scope.start_time = ''
                $scope.end_time  = ''
                $scope.search_txt = ''
                    $http.get('/quote/plot-list', {
                            params: {
                            'post': $scope.cur_county.id,
                            'page':$scope.cur_page
                        }
                    }).then(function (response) {
                        $scope.total_pages = []
                        let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                        for (let i = 1; i <= num; i++) {
                            $scope.total_pages.push(i)
                        }
                        $scope.house_detail = response.data.model.details
                        console.log(response)
                    }, function (error) {
                        console.log(error)
                    })
        }
        //日期筛选小区
        //改变结束时间
        $scope.$watch('end_time', function (newVal, oldVal) {
            // $scope.cur_county = $scope.county[0]
            $scope.search_txt = ''
            if (newVal != '' && $scope.start_time != '' && new Date(newVal).getTime() > new Date($scope.start_time).getTime()) {
                $http.get('/quote/plot-time-grabble', {
                    params: {
                        'city': $scope.cur_city.id,
                        'min': $scope.start_time,
                        'max': newVal
                    }
                }).then(function (response) {
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }
        })
        //改变开始时间
        $scope.$watch('start_time', function (newVal, oldVal) {
            // $scope.cur_county = $scope.county[0]
            $scope.search_txt = ''
            if (newVal != '' && $scope.start_time != '' && new Date($scope.end_time).getTime() > new Date(newVal).getTime()) {
                $http.get('/quote/plot-time-grabble', {
                    params: {
                        'city': $scope.cur_city.id,
                        'min': newVal,
                        'max': $scope.end_time
                    }
                }).then(function (response) {
                    $scope.total_pages = []
                    let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                    for (let i = 1; i <= num; i++) {
                        $scope.total_pages.push(i)
                    }
                    $scope.house_detail = response.data.model.details
                    console.log(response)
                }, function (error) {
                    console.log(error)
                })
            }
        })
        //输入小区名模糊筛选小区
        $scope.search_house = function () {
            $scope.start_time = ''
            $scope.end_time  = ''
            $scope.cur_county = $scope.county[0]
            $http.get('/quote/plot-grabble', {
                params: {
                    'city': $scope.cur_city.id,
                    'toponymy': $scope.search_txt
                }
            }).then(function (response) {
                $scope.total_pages = []
                let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                for (let i = 1; i <= num; i++) {
                    $scope.total_pages.push(i)
                }
                $scope.house_detail = response.data.model.details
                console.log(response)
            }, function (error) {
                console.log(error)
            })
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
                for(let [key,value] of $scope.cur_all_series.entries()){
                    if(value.id == item.series){
                        $scope.cur_series = value
                    }
                }
                $scope.cur_style = $scope.cur_all_style[0]//当前案例图纸风格
                for(let [key,value] of $scope.cur_all_style.entries()){
                    if(value.id == item.style){
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
        $scope.delete_house = function (item) {
            console.log(item)
            $scope.house_informations.splice($scope.house_informations.indexOf(item), 1)
            if(item.id != undefined){
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
            if(item.id != undefined){
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
        $scope.addHouseReturn =function () {
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
            if($scope.second_title == '案例/社区配套商品管理'){
                $state.go('intelligent.add_support_goods')
            }else if($scope.second_title == '小区列表页'){
                $state.go('intelligent.house_list')
            }
        }
        //跳转三级页面
        $scope.go_three = function () {
            $scope.four_title = ''
            if($scope.three_title == '添加小区信息' || $scope.three_title == '编辑小区信息'){
                $state.go('intelligent.add_house')
            }
        }

        //跳转案例社区配套商品管理
        $scope.check_item = []
        $scope.goSupportGoods = function () {
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
                                for (let [key1, value1] of $scope.check_item.entries()) {
                                    if (value.id == value1.id) {
                                        value['complete'] = true
                                    }
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
                                console.log(111)
                                for (let [key1, value1] of $scope.check_item.entries()) {
                                    if (value.id == value1.id) {
                                        value['complete'] = true
                                        console.log(value)
                                    }
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
                let all_modal = function ($scope, $uibModalInstance) {
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                        $state.go('intelligent.intelligent_index')
                    }
                }
                all_modal.$inject = ['$scope', '$uibModalInstance']
                $uibModal.open({
                    templateUrl: 'pages/intelligent/cur_model.html',
                    controller: all_modal
                })
                console.log(response)
            }, function (error) {
                console.log(error)
            })
        }
        //勾选添加/删除三级
        $scope.item_check = function (item) {
            if (item.complete) {
                $scope.check_item.push(item)
            } else {
                for (let [key, value] of $scope.check_item.entries()) {
                    if (value.id == item.id) {
                        $scope.check_item.splice(key, 1)
                    }
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
            if($scope.is_add){
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
                                            'good_quantity': value3.good_quantity
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
                                if(!!value1.cur_id){
                                    value.drawing_list.push({
                                        'id':value1.cur_id,
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
                            'high': value.high,//层高
                            'sort_id':arr.length+1,
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
                            'sort_id':arr.length+1,
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
            }else{//编辑小区保存
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
                                    if(!!value3.cur_id || value3.cur_id == 0){
                                        if (!!value3.good_id && !!value3.good_quantity) {
                                            goods.push({
                                                'id':value3.cur_id,
                                                'first_name': value1.title,
                                                'second_name': value2.title,
                                                'three_name': value3.title,
                                                'good_code': value3.good_id,
                                                'good_quantity': value3.good_quantity
                                            })
                                            value['all_goods'] = goods
                                        }else{
                                            value.delete_goods.push(value3.cur_id)
                                        }
                                    }else{
                                        if (!!value3.good_id && !!value3.good_quantity) {
                                            goods.push({
                                                'first_name': value1.title,
                                                'second_name': value2.title,
                                                'three_name': value3.title,
                                                'good_code': value3.good_id,
                                                'good_quantity': value3.good_quantity
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
                            if(!!value1.cur_id){
                                if (!!value1.price) {
                                    worker.push({
                                        'worker_kind':value1.worker_kind,
                                        'price':value1.worker_kind,
                                        'id':value1.cur_id
                                    })
                                }else{
                                    value.delete_workers.push(value1.cur_id)
                                }
                            }else{
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
                            if(!!value1.cur_id){
                                if (!!value1.num || (value1.num == 0&&value1.name == '有无建渣点')) {
                                    backman_list.push({
                                        'name':value1.name,
                                        'num':value1.num,
                                        'id':value1.cur_id
                                    })
                                    value['backman_list'] = backman_list
                                }else{
                                    value.delete_backman.push(value1.cur_id)
                                }
                            }else{
                                if (!!value1.num || (value1.num == 0&&value1.name == '有无建渣点')) {
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
                                console.log(value.drawing_list)
                                value.drawing_list.push({
                                    'all_drawing': value1.drawing_list.join(','),
                                    'series': value1.series.id,
                                    'style': value1.style.id,
                                    'drawing_name': value1.drawing_name
                                })
                                console.log(1111)
                            }
                        }
                    }
                }
                for (let [key, value] of $scope.house_informations.entries()) {
                    if(!!value.id){
                        if (!value.is_ordinary) {
                            // for (let [key1, value1] of $scope.drawing_informations.entries()) {
                            //     if (value1.index == key) {
                            arr.push({
                                'id':value.id,
                                'house_type_name': value.house_type_name,//户型名
                                'other_id':value.id,
                                'area': value.area,//房屋面积
                                'cur_room': value.cur_room,//室
                                'cur_hall': value.cur_hall,//厅
                                'cur_toilet': value.cur_toilet,//卫
                                'cur_kitchen': value.cur_kitchen,//厨
                                'cur_imgSrc': value.cur_imgSrc,//户型图
                                'sort_id':arr.length+1,
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
                                'id':value.id,
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
                                'sort_id':arr.length+1,
                                'delete_goods':value.delete_goods,
                                'delete_workers':value.delete_workers,
                                'delete_backman':value.delete_backman,
                                'drawing_id':value.drawing_id,
                                'all_goods': value.all_goods || [],
                                'drawing_list': value.drawing_list.join(','),
                                'worker_list': value.worker_list || [],
                                'backman_option': value.backman_list || [],
                                'is_ordinary': 1
                            })
                            console.log(111)
                        }
                    }else{
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
                                'sort_id':arr.length+1,
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
                                'sort_id':arr.length+1,
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
                if($scope.is_add){
                    $http.post('/quote/plot-add', {
                        'province_code': $scope.cur_province.id,
                        'city_code': $scope.cur_city.id,
                        'house_name': $scope.house_name,
                        'cur_county_id': $scope.cur_house_county.id,
                        'address': $scope.address,
                        'house_informations': arr
                    }, config).then(function (response) {
                        //请求小区数据
                        $http.get('/quote/plot-list', {
                            params: {
                                'post': $scope.cur_county.id,
                                'page':$scope.cur_page
                            }
                        }).then(function (response) {
                            $scope.house_detail = response.data.model.details
                            $scope.total_pages = []
                            let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                            for (let i = 1; i <= num; i++) {
                                $scope.total_pages.push(i)
                            }
                            console.log(response)
                        }, function (error) {
                            console.log(error)
                        })
                        console.log(response)
                        //弹出保存成功模态框
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    }, function (error) {
                        console.log(error)
                    })
                }else{
                    $http.post('/quote/plot-edit',{
                        'province_code': $scope.cur_province.id,
                        'city_code': $scope.cur_city.id,
                        'house_name': $scope.house_name,
                        'cur_county_id': $scope.cur_house_county.id,
                        'address': $scope.address,
                        'house_informations': arr,
                        'delete_house':$scope.delete_house_list,
                        'delete_drawing':$scope.delete_drawing_list
                    },config).then(function (response) {
                        console.log(response)
                        //请求小区数据
                        $http.get('/quote/plot-list', {
                            params: {
                                'post': $scope.cur_county.id,
                                'page':$scope.cur_page
                            }
                        }).then(function (response) {
                            $scope.house_detail = response.data.model.details
                            $scope.total_pages = []
                            let num = Math.ceil(+response.data.model.total / +response.data.model.size)
                            for (let i = 1; i <= num; i++) {
                                $scope.total_pages.push(i)
                            }
                            console.log(response)
                        }, function (error) {
                            console.log(error)
                        })
                        console.log(response)
                        //弹出保存成功模态框
                        $uibModal.open({
                            templateUrl: 'pages/intelligent/cur_model.html',
                            controller: all_modal
                        })
                    },function (error) {
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

        //编辑所有页面配置
        $scope.go_edit_house = function (item) {
            $scope.three_title='编辑小区信息'
            $scope.delete_house_list = []
            $scope.delete_drawing_list = []
            $scope.is_add = 0
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
                    if(!+value.type){
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
                                'index':$scope.house_informations.length,
                                'drawing_list':[],
                                'flattop_area': value1.flat_area,
                                'balcony_area': value1.balcony_area,
                                'is_ordinary': 0
                            })
                        }}} else if (!!+value.type) {
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
                                'all_materials':angular.copy($scope.all_materials_copy),
                                'worker_list':angular.copy($scope.common_worker),
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
                                    'id':value1.id,
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
                                for(let [key2,value2] of $scope.cur_all_series.entries()){
                                    if(value.series == value2.id){
                                        value.series = value2
                                    }
                                }
                                for(let [key2,value2] of $scope.cur_all_style.entries()){
                                    if(value.style == value2.id){
                                        value.style = value2
                                    }
                                }
                            }
                        }

                    }
                }
                //整合普通户型图纸信息
                for(let [key,value] of $scope.drawing_informations.entries()){
                    for(let [key1,value1] of $scope.cur_all_series.entries()){
                        if(value.series == value1.id){
                            value.series = value1
                        }
                    }
                    for(let [key1,value1] of $scope.cur_all_style.entries()){
                        if(value.style == value1.id){
                            value.style = value1
                        }
                    }
                }
                //获取案例户型材料及费用
                for(let [key,value] of $scope.house_informations.entries()){
                    if(value.is_ordinary){
                        for(let [key1,value1] of value.all_materials.entries()){
                            for(let [key2,value2] of value1.second_level.entries()){
                                for(let [key3,value3] of value2.three_level.entries()){
                                    for(let [key4,value4] of response.data.effect.works_data.entries()){
                                        if(value3.title == value4.goods_three){
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
                for(let [key,value] of $scope.house_informations.entries()){
                    if(value.is_ordinary){
                        for(let [key1,value1] of value.worker_list.entries()){
                            for(let [key2,value2] of response.data.effect.works_worker_data.entries()){
                                if(value1.worker_kind == value2.worker_kind){
                                    value1.price = value2.worker_price
                                    value1['cur_id'] = value2.id
                                }
                            }
                        }
                    }
                }
                //获取案例杂工选项
                for(let [key,value] of $scope.house_informations.entries()){
                    if(value.is_ordinary){
                        for(let [key1,value1] of value.backman_option.entries()){
                            for(let [key2,value2] of response.data.effect.works_backman_data.entries()){
                                if(value1.name == value2.backman_option){
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
    })
