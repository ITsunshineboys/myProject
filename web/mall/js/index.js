var carousel="carousel";
//轮播设置开始
function carousel(){
    var mySwiper = new Swiper('#swiper-container',{
        autoplay: 2000,//可选选项，自动滑动
        pagination: '.pagination',
        loop:true,
        grabCursor: true,
        paginationClickable: true
    });
//左右箭头事件
//$('.arrow-left').on('click', function(e){
//    e.preventDefault();
//    mySwiper.swipePrev()
//});
//$('.arrow-right').on('click', function(e){
//    e.preventDefault();
//    mySwiper.swipeNext()
//});
//轮播设置结束
    app.controller("index",function($scope,$http){
        $http.get(url+carousel)
            .success(function(data){
                $scope.carousel1=data.data.carousel;
            })
        $scope.$on('ngRepeatFinished', function (data) { //接收广播，一旦repeat结束就会执行
            carousel();
        })
    })
}


app.controller('itemReaptCtrl', ['$scope', function ($scope) {
    $scope.$watch($scope.$last, function () {
        if($scope.$last){   //$scope.$last是来判断是否是最后一个ng-repeat对象， 如果是则$scope.$last的值为true ,反之则为false
            setTimeout(function(){$scope.$emit('ngRepeatFinished')},1); // 由于是向父控制器中发布广播，所有用$emit
        }
    })
}]);

