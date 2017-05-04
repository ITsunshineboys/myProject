var carousel="carousel";
//轮播设置开始
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
            $scope.carousel=data.data;
        })
})
