/**
 * Created by xl on 2017/6/29 0029.
 */
angular.module('app',[])
    .controller("Intelligent_quotation",function ($scope,$http) {
        $scope.nowSeries ='���';
        $scope.nowStyle = '�ִ���Լ';
        $http({
            method:"get",
            url:url+"owner/series-and-style"
        }).then(function successCallback (resp) {
            $scope.message = resp.data.data.show.stairs_details;
            $scope.style = resp.data.data.show.series;
            $scope.me=resp.data.data.show.style;
            console.log($scope.me);
        },function errorCallback () {

        });
        //�л�ϵ��
        $scope.toggleSeries = function (item) {
            $scope.nowSeries = item;
        };

        //�л����
        $scope.toggleStyle = function (item) {
            $scope.nowStyle = item;
        }
    });


//������ȡ������תҳ��
$(".search_a").blur(function () {

});
