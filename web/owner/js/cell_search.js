/**
 * Created by xl on 2017/6/30 0030.
 */
angular.module("cell_search",[])
       .controller("cell_search_ctrl",function ($scope,$http,testFactory) {
           $scope.data = ''
           $scope.input = {
               a:"111"
           }
           $scope.getData = function () {
               let arr = []
               $http.get("/owner/search").then(function (response) {
                   for(let item of response.data.data.effect){
                       if(item.toponymy.indexOf($scope.data)!=-1 && $scope.data!=''){
                           arr.push({"toponymy":item.toponymy,"site_particulars":item.site_particulars})
                       }
                   }
                   $scope.search_data = arr;

                   console.log(arr)
               },function (response) {
                   
               })
           }
       })
