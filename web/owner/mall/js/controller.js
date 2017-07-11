angular.module("all_controller", [])
    .controller("cell_search_ctrl", function ($scope, $http) {//小区搜索控制器
        $scope.data = ''
        $scope.getData = function () {
            let arr = []
            let url = "/owner/search"
            let data = {
                str:$scope.data
            }
            let config = {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest:function (data) {
                    return $.param(data)
                }
            }
            $http.post(url,data,config).then(function (response) {
                console.log(response)
                // console.log(response)
                // for (let item of response.data.data.effect) {
                //     if (item.toponymy.indexOf($scope.data) != -1 && $scope.data != '') {
                //         arr.push({"toponymy": item.toponymy, "site_particulars": item.site_particulars})
                //     }
                // }
                // $scope.search_data = arr;
            }, function (response) {

            })
        }
    })
    .controller("intelligent_index_ctrl", function ($scope,$http) {//主页控制器
       /* $scope.get_quotation = function () {
            $http({
                url:"/owner/search?id=1",
                method:"get"
            }).then(function (response) {
                $scope.data = response.data.data.effect
                $scope.imgSrc =  response.data.data.effect_picture
                $scope.all_data = {toponymy:$scope.data.toponymy,site_particulars:$scope.data.site_particulars,
                high:$scope.data.high,particulars:$scope.data.particulars,window:$scope.data.window}
                console.log(response)
            },function (response) {

            })
        }*/
    })
    .controller("intelligent_nodata_ctrl", function ($scope,$stateParams, $http) { //无数据控制器
        $scope.message = ''
        $scope.nowStyle = '现代简约'
        $scope.nowStairs = '实木结构'
        $scope.nowSeries = '齐家'
        $scope.toponymy =''|| $stateParams.toponymy
        $scope.choose_stairs = true;
        //生成材料变量
        $scope.house_bedroom = 0
        $scope.house_hall = 0
        $scope.house_kitchen = 0
        $scope.house_toilet = 0
        //无资料户型加减方法
        $scope.add = function (item,category) {
            if($scope[category]<item){
                $scope[category]++
            }else{
                $scope[category] = item
            }
        }
        $scope.subtract = function (item,category) {
            if($scope[category]>item){
                $scope[category]--
            }else{
                $scope[category] = item
            }
        }
        //生成材料方法
        $scope.getData = function () {
            let url = "/owner/weak-current"
            let data = {
                bedroom:$scope.house_bedroom,
                area:1,      //面积
                hall:$scope.house_hall,       //餐厅
                toilet:$scope.house_toilet,   // 卫生间
                kitchen:$scope.house_kitchen,  //厨房
                stairs_details_id:1, //楼梯
                series:1,   //系列
                style:1,  //风格
                window:1,//飘窗
                province:510000,   //省编码
                city:510100      // 市编码
            }
            let config = {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest:function (data) {
                    return $.param(data)
                }
            }
            $http.post(url,data,config).then(function (response) {
                console.log(response)
            },function (error) {
                console.log(error)
            })
        }
        $scope.$watch('message', function (newVal, oldVal) {
            if (newVal && newVal != oldVal) {
                if (newVal.length > 45) {
                    $scope.message = newVal.substr(0, 45)
                }
            }
        })

        //请求后台数据
        $http.get('/owner/series-and-style').then(function (response) {
            $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
            $scope.series = response.data.data.show.series;//系列数据
            $scope.style = response.data.data.show.style;//风格数据
            $scope.style_picture = response.data.data.show.style_picture;//轮播图片数据
            console.log($scope.style_picture)
        }, function (response) {

        })
        //切换楼梯
        $scope.toggleStairs = function (item) {
            $scope.nowStairs = item;
        }
        //切换系列
        $scope.toggleSeries = function (item,index) {
            $scope.nowSeries = item;
            $scope.swiperImg = $scope.style_picture.slice(index,index*3)
        }
        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.nowStyle = item;
        }
    })
    .controller("move_furniture_ctrl", function ($scope, $http) {//移动家具控制器
        $http({
            method: 'get',
            url: "/mall/categories"
        }).then(function successCallback(response) {
            $scope.message = response.data.data.categories;
        }, function errorCallback(response) {

        });
    })
    .controller("location_city_ctrl",function ($scope) {//城市选择控制器
        // $scope.goPrev = function () {
        //     window.history.back()
        // }
    })
    .controller("intelligent_quotation_ctrl",function ($scope,$http,$stateParams) {//有资料选择器
        $scope.nowSeries ='齐家';
        $scope.nowStyle = '现代简约';
        $scope.nowStairs = '实木结构'
        $scope.choose_stairs = true;
        $scope.index = 0
        $scope.house_index = 0
        $scope.isSelect = true
        $scope.id = $stateParams.id
        console.log($stateParams.id)
        let url = "/owner/search"
        let data = {
            id:$stateParams.id
        }
        let config = {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            transformRequest:function (data) {
                return $.param(data)
            }
        }
        $http.post(url,data,config).then(function (response) {//小区房型基本信息
             console.log(response)
            let arr = []
            $scope.data = response.data.data.effect
            $scope.imgSrc =  response.data.data.effect_picture
            $scope.all_data = {"toponymy":$scope.data[0].toponymy,site_particulars:$scope.data[0].site_particulars,
                high:$scope.data[0].high,area:$scope.data[0].area,particulars:$scope.data[0].particulars,window:$scope.data[0].window}
            console.log($scope.data[0].toponymy)
         },function (response) {

         })
        $http.get('/owner/series-and-style').then(function (response) {
            $scope.stairs_details = response.data.data.show.stairs_details;//楼梯数据
            $scope.series = response.data.data.show.series;//系列数据
            $scope.style = response.data.data.show.style;//风格数据
            $scope.style_picture = response.data.data.show.style_picture;//轮播图片数据
            console.log($scope.series)
        }, function (response) {

        })
        //切换户型
        $scope.toggleHouse = function (item) {
            $scope.house_index = item
        }
        //切换系列
        $scope.toggleSeries = function (item) {
            $scope.nowSeries = item;
        };

        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.nowStyle = item;
        }
        //切换楼梯结构
        $scope.toggleStairs = function (index) {
            $scope.index = index
        }
    })
