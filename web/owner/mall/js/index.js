//轮播接口
var carousel1="carousel";
//商品分类
var classify="categories";
//特殊推荐
//var recommend="recommend-first";
//推荐列表接口
var recommend_list="recommend-second";
//轮播设置开始
function carousel() {
    var mySwiper = new Swiper('#swiper-container', {
        autoplay: 2000,//可选选项，自动滑动
        pagination: '.pagination',
        loop: true,
        grabCursor: true,
        paginationClickable: true
    });
}
//轮播设置结束
app.controller("index",function($scope,$http){
        //轮播数据
    console.log(url+carousel1)
        $http.get(url+carousel1)
            .success(function(data){
                console.log(data)
                $scope.carousel1=data.data.carousel;
            });
        $http.get(url+classify)
            .success(function(data){
                $scope.class_list=data.data.categories
            });
        ////特殊推荐    暂时先注释
        //$http.get(url+recommend)
        //    .success(function(data){
        //        $scope.recommend=data.data;
        //    })
        //推荐列表数据
        $http.get(url+recommend_list)
            .success(function(data){
                $scope.recommend_list=data.data;
            });
        $scope.$on('ngRepeatFinished', function (data) { //接收广播，一旦repeat结束就会执行
            var mySwiper = new Swiper('#swiper-container',{
                autoplay: 2000,//可选选项，自动滑动
                pagination: '.pagination',
                loop:true,
                grabCursor: true,
                paginationClickable: true
            });
        })
});



app.controller('itemReaptCtrl', ['$scope', function ($scope) {
    $scope.$watch($scope.$last, function () {
        if($scope.$last){   //$scope.$last是来判断是否是最后一个ng-repeat对象， 如果是则$scope.$last的值为true ,反之则为false
            setTimeout(function(){$scope.$emit('ngRepeatFinished')},1); // 由于是向父控制器中发布广播，所有用$emit
        }
    })
}]);

