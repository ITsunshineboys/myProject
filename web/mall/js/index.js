var carousel="carousel";
//�ֲ����ÿ�ʼ
function carousel(){
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
                $scope.carousel1=data.data.carousel;
            })
        $scope.$on('ngRepeatFinished', function (data) { //���չ㲥��һ��repeat�����ͻ�ִ��
            carousel();
        })
    })
}


app.controller('itemReaptCtrl', ['$scope', function ($scope) {
    $scope.$watch($scope.$last, function () {
        if($scope.$last){   //$scope.$last�����ж��Ƿ������һ��ng-repeat���� �������$scope.$last��ֵΪtrue ,��֮��Ϊfalse
            setTimeout(function(){$scope.$emit('ngRepeatFinished')},1); // �������򸸿������з����㲥��������$emit
        }
    })
}]);

