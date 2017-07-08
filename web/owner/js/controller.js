angular.module("all_controller", [])
    .controller("cell_search_ctrl", function ($scope, $http) {//小区搜索控制器
        $scope.data = ''
        $scope.getData = function () {
            let arr = []
            $http({
               url:"/owner/search",
                method:"post",
                data:{str:"花"}
            }).then(function (response) {
                console.log(response)
                for (let item of response.data.data.effect) {
                    if (item.toponymy.indexOf($scope.data) != -1 && $scope.data != '') {
                        arr.push({"toponymy": item.toponymy, "site_particulars": item.site_particulars})
                    }
                }
                $scope.search_data = arr;
            }, function (response) {

            })
        }
    })
    .controller("intelligent_index_ctrl", function ($scope,$http) {//主页控制器
        // $scope.get_quotation = function () {
            $http({
                url:"/owner/search",
                method:"get",
                data:{'id':'111'}
            }).then(function (response) {
                console.log(response)
            },function (response) {

            })
        // }
    })
    .controller("intelligent_nodata_ctrl", function ($scope,$stateParams, $http) { //无数据控制器
        $scope.message = ''
        $scope.nowStyle = '现代简约'
        $scope.nowStairs = '实木结构'
        $scope.nowSeries = '齐家'
        $scope.toponymy = $stateParams.toponymy
        $scope.choose_stairs = true;
        console.log($stateParams)

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
            console.log($scope.series)
        }, function (response) {

        })
        //切换楼梯
        $scope.toggleStairs = function (item) {
            $scope.nowStairs = item;
        }
        //切换系列
        $scope.toggleSeries = function (item) {
            $scope.nowSeries = item;
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
    .controller("intelligent_quotation_ctrl",function ($scope,$http) {//有资料选择器
        $scope.nowSeries ='齐家';
        $scope.nowStyle = '现代简约';
        $http({
            method:"get",
            url:"/owner/series-and-style"
        }).then(function successCallback (resp) {
            $scope.message = resp.data.data.show.stairs_details;
            $scope.style = resp.data.data.show.series;
            $scope.me=resp.data.data.show.style;
            console.log($scope.me);
        },function errorCallback () {

        });
        //切换系列
        $scope.toggleSeries = function (item) {
            $scope.nowSeries = item;
        };

        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.nowStyle = item;
        }
    })
