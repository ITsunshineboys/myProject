/**
 * Created by xl on 2017/6/16 0016.
 */
$(".message").on("click",function () {
    $(".drop_down").css("display","block")
});
//调用后台接口获取评论
var myapp=angular.module('myapp',[]);
myapp.controller("commentctrl",function ($scope,$http) {
   $http({
       method:"get",
       url:url+"mall/goods-comments?id=31"
   }).then(function successCallback (data) {
       $scope.message=data.data.data.goods-comments.details;
       //alert(111);

   }, function errorCallback (data) {
       //alert(url);
   });

});