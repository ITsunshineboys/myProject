//�ֲ��ӿ�
var carousel1="carousel";
//��Ʒ����
var classify="categories";
//�����Ƽ�
//var recommend="recommend-first";
//�Ƽ��б�ӿ�
var recommend_list="recommend-second";
//�ֲ����ÿ�ʼ
function carousel() {
    var mySwiper = new Swiper('#swiper-container', {
        autoplay: 2000,//��ѡѡ��Զ�����
        pagination: '.pagination',
        loop: true,
        grabCursor: true,
        paginationClickable: true
    });
}
//�ֲ����ý���
app.controller("index",function($scope,$http){
        //�ֲ�����
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
        ////�����Ƽ�    ��ʱ��ע��
        //$http.get(url+recommend)
        //    .success(function(data){
        //        $scope.recommend=data.data;
        //    })
        //�Ƽ��б�����
        $http.get(url+recommend_list)
            .success(function(data){
                $scope.recommend_list=data.data;
            });
        $scope.$on('ngRepeatFinished', function (data) { //���չ㲥��һ��repeat�����ͻ�ִ��
            var mySwiper = new Swiper('#swiper-container',{
                autoplay: 2000,//��ѡѡ��Զ�����
                pagination: '.pagination',
                loop:true,
                grabCursor: true,
                paginationClickable: true
            });
        })
});



app.controller('itemReaptCtrl', ['$scope', function ($scope) {
    $scope.$watch($scope.$last, function () {
        if($scope.$last){   //$scope.$last�����ж��Ƿ������һ��ng-repeat���� �������$scope.$last��ֵΪtrue ,��֮��Ϊfalse
            setTimeout(function(){$scope.$emit('ngRepeatFinished')},1); // �������򸸿������з����㲥��������$emit
        }
    })
}]);

