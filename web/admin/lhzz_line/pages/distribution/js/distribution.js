angular.module('distribution',[])
   .controller('distribution_ctrl',function ($scope,$http,$state,$uibModal) {
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
       //跳转分销首页
       $scope.go_index = function () {
           $scope.second_title = ''
           $scope.three_title = ''
           $scope.four_title = ''
           $state.go('distribution.index')
       }
       //跳转二级页面
       $scope.go_second = function () {
           $scope.three_title = ''
           $scope.four_title = ''
           if ($scope.second_title == '详情') {
               $state.go('distribution.detail')
           }
       }
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
               $scope.fatherset = response.data.data.fatherset
               $scope.myself = response.data.data.myself
               $scope.profit = response.data.data.profit
               $scope.subset = response.data.data.subset
               $state.go('distribution.detail')
           },function (error) {
               console.log(error)
           })
       }
       //查看相关联交易订单列表
       $scope.associate = function () {
           $http.get('/distribution/correlate-order',{
               params:{
                   mobile:$scope.cur_item.mobile
               }
           }).then(function (response) {
               console.log(response)
               $scope.three_title = '相关联交易订单'
               $scope.total_amount = response.data.data.total_amount
               $scope.total_orders = response.data.data.total_orders
               $scope.all_order_detail = response.data.data.details
               $state.go('distribution.associate_list')
           },function (error) {
               console.log(error)
           })
       }
       //保存收益
       $scope.save_profit = function (valid) {
           let all_modal = function ($scope, $uibModalInstance) {
               $scope.cur_title = '保存成功'
               $scope.common_house = function () {
                   $uibModalInstance.close()
                   $state.go('distribution.index')
               }
           }
           all_modal.$inject = ['$scope', '$uibModalInstance']
           if(valid){
               $http.post('/distribution/add-profit',{
                   mobile:$scope.myself.mobile,
                   profit:$scope.profit
               },config).then(function (res) {
                   console.log(res)
                   $uibModal.open({
                       templateUrl: 'pages/intelligent/cur_model.html',
                       controller: all_modal
                   })  
               },function (error) {
                   console.log(error)
               })
           }else{
               $scope.submitted = true
           }
       }
       //修改关联列表备注
       //弹出备注模态框
       $scope.replace_remarks = function (item) {
           $scope.cur_associate_item = angular.copy(item)
       }
       //修改备注请求
       $scope.edit_remarks = function () {
           $http.post('/distribution/add-remarks',{
               order_no:$scope.cur_associate_item.order_no,
               remarks:$scope.cur_associate_item.remarks
           },config).then(function (response) {
               console.log(response)
               $http.get('/distribution/correlate-order',{
                   params:{
                       mobile:$scope.cur_item.mobile
                   }
               }).then(function (response) {
                   console.log(response)
                   $scope.all_order_detail = response.data.data.details
                   $state.go('distribution.associate_list')
               },function (error) {
                   console.log(error)
               })
           },function (error) {
               console.log(error)
           })
       }
   })