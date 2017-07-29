angular.module("all_controller", [])
    //��ҳ������
    .controller("mall_index_ctrl", function ($scope,$http,$state,$stateParams) {  //��ҳ������

        $http({   //�ֲ��ӿڵ���
            method: 'get',
            url: "swiper.json"
        }).then(function successCallback(response) {
            $scope.swiper_img = response.data.data.carousel;
            //console.log( $scope.message);
        }, function errorCallback(response) {

        });
        $http({   //��Ʒ�����б�
            method: 'get',
            url: "http://test.cdlhzz.cn:888/mall/categories"
        }).then(function successCallback (response) {
            $scope.message=response.data.data.categories;
            console.log( $scope.message);
        }, function errorCallback (response) {

        });
        $http({   //������Ʒ�б�
            method: 'get',
            url: "swiper.json"
        }).then(function successCallback (response) {
            $scope.commodity=response.data.data.carousel;
            console.log( $scope.commodity);
        }, function errorCallback(response) {

        });
    })
    //�������������
    .controller("minute_class_ctrl", function ($scope,$http ,$state,$stateParams) {
         $scope.pid = $stateParams.pid;
         $scope.title =  $stateParams.title;
         console.log($scope.pid);
         console.log($scope.title);
        //������ݻ�ȡ
        $http({
            method:'get',
            url:'http://test.cdlhzz.cn:888/mall/categories'
        }).then( function successCallback (response) {
            $scope.star= response.data.data.categories;
            console.log(response)
        });
        //��ҳ�б��������б�ֵid��ȡ����(һ��id��ȥ����)
        $http({
            method:'get',
            url:'http://test.cdlhzz.cn:888/mall/categories?pid='+$stateParams.pid
        }).then( function successCallback (response) {
            $scope.details = response.data.data.categories;
            //console.log(response.data.data.categories[0].id);
            console.log(response)
        });

        //��ҳ�б��������б�ֵid��ȡ����(һ��id��ȥ����)
        $http({
            method:'get',
            url:'http://test.cdlhzz.cn:888/mall/categories-level3?pid='+$stateParams.pid
        }).then( function successCallback (response) {
            $scope.commentThree= response.data.categories_level3;
            console.log(response)
        });

        //����������б�˵���ȡ�ұ�����
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
    //С������
    .controller("search_ctrl", function ($scope,$http ,$state,$stateParams) {

    })
    //��Ʒ����
    .controller("commodity_search_ctrl", function ($scope,$http ,$state,$stateParams) {
        $scope.data = '';
        //$scope.title =  $stateParams.title;
        //�ж�
        $scope.getSearch = function () {
            let arr=[];
            $http({
                method:'get',
                url:"http://test.cdlhzz.cn:888/mall/search?keyword="+$scope.data
            }).then( function successCallback (response) {
                $scope.commoditySearch= response.data.data.search.goods;
                for (let [key,item] of response.data.data.search.goods.entries()) { //�ж���������ݺ����ݿ�����ƥ��
                    if (item.title.indexOf($scope.data) != -1 && $scope.data != '') {
                        arr.push({"title": item.title,"id":item.id})
                    }
                }
                $scope.search_data = arr;
                console.log(response)
            });
        };
        //��ת��ĳ����Ʒ����
        $scope.getBackData = function (item) {
            $state.go("details",{id:item})

        }
    })

    //ĳ����Ʒ����ϸ�б�
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
