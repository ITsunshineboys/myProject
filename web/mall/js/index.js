var carousel="carousel";
//�ֲ����ÿ�ʼ
var mySwiper = new Swiper('#swiper-container',{
    autoplay: 2000,//��ѡѡ��Զ�����
    pagination: '.pagination',
    loop:true,
    grabCursor: true,
    paginationClickable: true
});
//���Ҽ�ͷ�¼�
//$('.arrow-left').on('click', function(e){
//    e.preventDefault();
//    mySwiper.swipePrev()
//});
//$('.arrow-right').on('click', function(e){
//    e.preventDefault();
//    mySwiper.swipeNext()
//});
//�ֲ����ý���
app.controller("index",function($scope,$http){
    $http.get(url+carousel)
        .success(function(data){
            $scope.carousel=data.data;
        })
})
