angular.module("all_controller", [])
    //首页控制器
    .controller("mall_index_ctrl", function ($scope,$http,$state,$stateParams) {  //首页控制器

        $http({   //轮播接口调用
            method: 'get',
            url: "swiper.json"
        }).then(function successCallback(response) {
            $scope.swiper_img = response.data.data.carousel;
            //console.log( $scope.message);
        }, function errorCallback(response) {

        });
        $http({   //商品分类列表
            method: 'get',
            url: "http://test.cdlhzz.cn:888/mall/categories"
        }).then(function successCallback (response) {
            $scope.message=response.data.data.categories;
            console.log( $scope.message);
        }, function errorCallback (response) {

        });
        $http({   //分类商品列表
            method: 'get',
            url: "swiper.json"
        }).then(function successCallback (response) {
            $scope.commodity=response.data.data.carousel;
            console.log( $scope.commodity);
        }, function errorCallback(response) {

        });
    })
    //分类详情控制器
    .controller("minute_class_ctrl", function ($scope,$http ,$state,$stateParams) {
         $scope.pid = $stateParams.pid;
         $scope.title =  $stateParams.title;
         console.log($scope.pid);
         console.log($scope.title);
        //左侧数据获取
        $http({
            method:'get',
            url:'http://test.cdlhzz.cn:888/mall/categories'
        }).then( function successCallback (response) {
            $scope.star= response.data.data.categories;
            console.log(response)
        });
        //首页列表点击分类列表传值id获取数据(一级id查去二级)
        $http({
            method:'get',
            url:'http://test.cdlhzz.cn:888/mall/categories?pid='+$stateParams.pid
        }).then( function successCallback (response) {
            $scope.details = response.data.data.categories;
            //console.log(response.data.data.categories[0].id);
            console.log(response)
        });

        //首页列表点击分类列表传值id获取数据(一级id查去三级)
        $http({
            method:'get',
            url:'http://test.cdlhzz.cn:888/mall/categories-level3?pid='+$stateParams.pid
        }).then( function successCallback (response) {
            $scope.commentThree= response.data.categories_level3;
            console.log(response)
        });

        //点击左侧分类列表菜单获取右边数据
        //$scope.getTitle = function (item) {
        //    $http({
        //        method:'get',
        //        url:'http://test.cdlhzz.cn:888/mall/categories?pid='+$stateParams.pid
        //    }).then (function successCallback (response) {
        //        $scope.leftMain = response.data.data.categories;
        //        console.log(response)
        //    })
        //};

    })
    //小区搜索
    .controller("search_ctrl", function ($scope,$http ,$state,$stateParams) {

    })
    //商品搜索
    .controller("commodity_search_ctrl", function ($scope,$http ,$state,$stateParams) {
        $scope.data = '';
        //$scope.title =  $stateParams.title;
        //判断
        $scope.getSearch = function () {
            let arr=[];
            $http({
                method:'get',
                url:"http://test.cdlhzz.cn:888/mall/search?keyword="+$scope.data
            }).then( function successCallback (response) {
                $scope.commoditySearch= response.data.data.search.goods;
                for (let [key,item] of response.data.data.search.goods.entries()) { //判断输入框数据和数据库内容匹配
                    if (item.title.indexOf($scope.data) != -1 && $scope.data != '') {
                        arr.push({"title": item.title,"id":item.id})
                    }
                }
                $scope.search_data = arr;
                console.log(response)
            });
        };
        //跳转道某个商品详情
        $scope.getBackData = function (item) {
            $state.go("details",{id:item})

        }
    })

    //某个商品的详细列表
     .controller("details_ctrl", function ($scope,$http ,$state,$stateParams) {
        $scope.id=$stateParams.id;
        console.log($stateParams.id);
        $http({
            method:"get",
            url:'http://test.cdlhzz.cn:888/mall/category-goods?category_id='+$stateParams.id
        }).then(function successCallback (response) {
            $scope.detailsList = response.data.data.category_goods;
            console.log(response)
        })
        $scope.curGoPrev = function () {
            $state.go("minute_class")
        }

     })
