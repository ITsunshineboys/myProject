//��ȡ����json ����
  var myApp = angular.module("myApp",[]);
  myApp.controller("comment_controller",function($scope, $http,$filter){
    $http({
        method: 'get',
        //url: url+"categories?pid=
        url:"commodity.json"
    }).then(function successCallback(response) {
        //alert(response.data.data.category_goods);
        //$.each(response.data.data,function (i,n) {
        //    console.log(response.data.data.category_goods)
        //});
        ////console.log(response.data.data.category_goods)
        $scope.message = response.data.data.category_goods;
        var isopen=true;
        $scope.sort=function(str){
            $scope.message=$filter("orderBy")($scope.message,str,isopen);
            isopen=!isopen;
            //alert(11)
        };
        $scope.myFilter={
             limit:3
        };
        $scope.returnMore=function(a,b){
            return a>=b
        }


    }, function errorCallback(response) {
        // ����ʧ��ִ�д���
        alert(response);

    });
  });
//������۸��ǰ��۸�ߵ�����
//myApp.controller("prioritycontroller",function ($scope,$http) {
//  $scope.sales_priority=function () {
//   this.$(".memo_pad li").on("click",function () {
//      $http({
//        method: 'get',
//        url: 'commodity.json'
//      }).then( function () {
//         $scope.myFilter={
//             limit:1
//         };
//        //if (this.$(".memo_pad li")==0) { //�жϵ��li���±���0ʱ���Ͱ���������
//        //
//        //}
//        //if (this.$(".memo_pad li")==1) { //�жϵ��li���±���0ʱ���Ͱ��۸�����
//        //
//        //
//        //}
//        //if (this.$(".memo_pad li")==2) { //�жϵ��li���±���0ʱ���Ͱ�����������
//        //
//        //}
//      },function () {
//
//      })
//    })
//  }
//});

