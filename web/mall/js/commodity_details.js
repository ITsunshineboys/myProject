//��ȡ����json ����
  var myApp = angular.module("myApp",[]);
  myApp.controller("comment_controller",function($scope, $http){
    $http({
        method: 'get',
        url: 'commodity.json'
    }).then(function successCallback(response) {
        $scope.message = response.data.data.category_goods;

    }, function errorCallback(response) {
        // ����ʧ��ִ�д���
        alert(response);

    });
  });
//������۸��ǰ��۸�ߵ�����
myApp.controller("salesPriority",function ($scope,$http) {
  $scope.sales_priority=function () {
   this.$(".memo_pad li").on("click",function () {
      $http({
        method: 'get',
        url: 'commodity.json'
      }).then( function () {
        if (this.$(".memo_pad li")==0) { //�жϵ��li���±���0ʱ���Ͱ���������
          
        }
        if (this.$(".memo_pad li")==1) { //�жϵ��li���±���0ʱ���Ͱ��۸�����


        }
        if (this.$(".memo_pad li")==2) { //�жϵ��li���±���0ʱ���Ͱ�����������

        }
      },function () {

      })
    })
  }
});

