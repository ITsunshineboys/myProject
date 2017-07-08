//点击减号递减

$(".min").on('click',function () {
   var numVal=$(".text_box").val();
   //alert(numVal);
   if(numVal>0){
      $(".text_box").val(numVal-1);
   }else{
      $(".text_box").val(0);
   }
});

//点击加号递加
$(".add").on('click',function () {
   var numVal=$(".text_box").val();
   //alert(numVal);
      $(".text_box").val(parseInt(numVal)+1);

});

//点击添加购物车  提示框2秒消失
$(".join_cart").on("click" ,function (){
   setTimeout(function(){
      $("#myModalP").modal("hide")
   },2000);
});

$(".pic_tabs li").on("click",function () {
   //$()
});

console.log("url上带的值=="+decodeURI(GetQueryString('txt')));

app.controller("details",function($http,$scope){
   $http({
        method: 'post',
        url: url+"categories?pid=6"
        //url:"http://test.cdlhzz.cn:888/mall/categories?pid=6"
   }).then(function successCallback(data) {
        $scope.message = data.data.data.categories;
        //alert(message)
   }, function errorCallback(data) {
        alert(2222);

});
});

