angular.module('distribution',[])
   .controller('distribution_ctrl',function ($scope,$http,$state) {
       //post请求配置
       const config = {
           headers: {'Content-Type': 'application/x-www-form-urlencoded'},
           transformRequest: function (data) {
               return $.param(data)
           }
       };
       //默认登录状态(后期会删除)
       $http.post('/site/login', {
           'username': 13551201821,
           'password': 'demo123'
       }, config).then(function (response) {
           console.log(response)
       }, function (error) {
           console.log(error)
       })
       $scope.ctrlScope = $scope
       $scope.time_type = [
           {name:'全部时间',str:'all'},
           {name:'今天',str:'today'},
           {name:'本周',str:'week'},
           {name:'本月',str:'month'},
           {name:'本年',str:'year'},
           {name:'自定义',str:'custom'}
       ]
       $scope.cur_time_type = $scope.time_type[0]
       //初始化分销列表
       $http.get('/distribution/getdistributionlist',{
           params:{
               page:1,
               time_type:$scope.cur_time_type.str
           }
       }).then(function (response) {
           console.log(response)
           $scope.distribution_list = response.data.data.list
           $scope.nowday_add = response.data.data.nowday_add
           $scope.total_add = response.data.data.total_add
       },function (error) {
           console.log(error)
       })
       //获取样板间列表
       //修改全部时间
       $scope.$watch('cur_time_type',function (newVal,oldVal) {
           // $scope.keyword = ''
           if(newVal.str!='custom'){
               $http.get('/distribution/getdistributionlist',{
                   params:{
                       page:1,
                       time_type:$scope.cur_time_type.str
                   }
               }).then(function (response) {
                   console.log(response)
                   $scope.distribution_list = response.data.data.list
                   $scope.nowday_add = response.data.data.nowday_add
                   $scope.total_add = response.data.data.total_add
               },function (error) {
                   console.log(error)
               })
           }else{
               $scope.start_time = ''
               $scope.end_time = ''
           }
       })
       //开始时间修改
       $scope.$watch('start_time',function (newVal,oldVal) {
           console.log(newVal)
           let obj = ''
           if($scope.end_time == ''){
               obj = {
                   page:1,
                   time_type:$scope.cur_time_type.str,
                   start_time:newVal
               }
           }else{
               if(new Date(newVal).getTime()<new Date($scope.end_time).getTime()) {
                   obj = {
                       page:1,
                       time_type: $scope.cur_time_type.str,
                       start_time: newVal,
                       end_time: $scope.end_time
                   }
               }
           }
           console.log(obj)
           if(obj!=''){
               $http.get('/distribution/getdistributionlist',{
                   params:obj
               }).then(function (response) {
                   console.log(response)
                   $scope.distribution_list = response.data.data.list
                   $scope.nowday_add = response.data.data.nowday_add
                   $scope.total_add = response.data.data.total_add
               },function (error) {
                   console.log(error)
               })
           }
       })
       //结束时间修改
       $scope.$watch('end_time',function (newVal,oldVal) {
           console.log(newVal)
           let obj = ''
           if($scope.start_time == ''){
               obj = {
                   page:1,
                   time_type:$scope.cur_time_type.str,
                   end_time:newVal
               }
           }else {
               if (new Date(newVal).getTime() > new Date($scope.start_time).getTime()) {
                   obj = {
                       page:1,
                       time_type: $scope.cur_time_type.str,
                       start_time: $scope.start_time,
                       end_time: newVal
                   }
               }
           }
           console.log(obj)
           if(obj!=''){
               $http.get('/distribution/getdistributionlist',{
                   params:obj
               }).then(function (response) {
                   console.log(response)
                   $scope.distribution_list = response.data.data.list
                   $scope.nowday_add = response.data.data.nowday_add
                   $scope.total_add = response.data.data.total_add
               },function (error) {
                   console.log(error)
               })
           }
       })
       //搜索手机号
       $scope.update_keyword = function () {
           // $scope.cur_time_type = $scope.time_type[0]
           $http.get('/distribution/getdistributionlist',{
               params:{
                   page:1,
                   keyword:$scope.keyword
               }
           }).then(function (response) {
               console.log(response)
               $scope.distribution_list = response.data.data.list
               $scope.nowday_add = response.data.data.nowday_add
               $scope.total_add = response.data.data.total_add
           },function (error) {
               console.log(error)
           })
       }
       //查看分销详情
       $scope.get_detail = function (item) {
           $scope.cur_item = item
           $http.post('/distribution/getdistributiondetail',{
               mobile:item.mobile
           },config).then(function (response) {
               console.log(response)
               $scope.second_title = '详情'
               $state.go('distribution.detail')
           },function (error) {
               console.log(error)
           })
       }
       //查看相关联交易订单列表
       $scope.associate = function () {
           $http.get('/distribution/searchmore',{
               params:{
                   mobile:$scope.cur_item.mobile
               }
           }).then(function (response) {
               console.log(response)
           },function (error) {
               console.log(error)
           })
       }
   })