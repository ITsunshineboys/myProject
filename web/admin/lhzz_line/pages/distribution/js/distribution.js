angular.module('distribution',[])
   .controller('distribution_ctrl',function ($rootScope,$scope,_ajax,$http,$state,$uibModal) {
       $rootScope.crumbs = [
           {
               name:'分销',
               icon:'icon-fenxiao',
           }
       ]
       //分销列表部分
       /*分页配置*/
       $scope.Config = {
           showJump: true,
           itemsPerPage: 12,
           currentPage: 1,
           onChange: function () {
               tablePages();
           }
       }
       let tablePages=function () {
           $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
           _ajax.get('/distribution/getdistributionlist',$scope.params,function (res) {
               console.log(res);
               $scope.distribution_list = res.data.list
               $scope.nowday_add = res.data.nowday_add
               $scope.total_add = res.data.total_add
               $scope.Config.totalItems = res.data.count;
           })
       };
       $scope.params = {
           time_type:'all',
           start_time:'',
           end_time:'',
           keyword:''
       };
       $scope.getDistribution = function () {
           $scope.Config.currentPage = 1
           $scope.params.keyword = ''
           $scope.keyword = ''
           if($scope.params.time_type == 'custom'){
               if($scope.params.start_time!=''||$scope.params.end_time!=''){
                   tablePages()
               }
           }else{
               $scope.params.start_time = ''
               $scope.params.end_time = ''
               tablePages()
           }
       }
       //相关联交易订单部分
       /*分页配置*/
       $scope.Config1 = {
           showJump: true,
           itemsPerPage: 12,
           currentPage: 1,
           onChange: function () {
               tablePages1();
           }
       }
       let tablePages1=function () {
           $scope.params1.page=$scope.Config1.currentPage;//点击页数，传对应的参数
           _ajax.get('/distribution/correlate-order',$scope.params1,function (res) {
               console.log(res);
               $scope.total_amount = res.data.total_amount
               $scope.total_orders = res.data.total_orders
               $scope.all_order_detail = res.data.details
               $scope.Config1.totalItems = res.data.count;
           })
       };
       $scope.params1 = {
           mobile:''
       };
       //跳转分销首页
       $scope.go_index = function () {
           $rootScope.crumbs = [
               {
                   name:'分销',
                   icon:'icon-fenxiao',
               }
           ]
           tablePages()
           $state.go('distribution.index')
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
       //搜索手机号
       $scope.update_keyword = function () {
           $scope.params.time_type = 'all'
           $scope.params.start_time = ''
           $scope.params.end_time = ''
           $scope.params.keyword = $scope.keyword
           tablePages()
       }
       $scope.$watch('keyword',function (newVal,oldVal) {
           if(newVal == ''){
               $scope.params.keyword = newVal
               tablePages()
           }
       })
       //查看分销详情
       $scope.get_detail = function (item) {
           $scope.cur_item = item
           $rootScope.crumbs = [
               {
                   name:'分销',
                   icon:'icon-fenxiao',
                   link:function () {
                       $state.go('distribution.index'),
                           $rootScope.crumbs.splice(1,2)
                   }
               },
               {
                   name:'详情'
               }
           ]
           _ajax.post('/distribution/getdistributiondetail',{
               mobile:item.mobile
           },function (res) {
               console.log(res)
               $scope.fatherset = res.data.fatherset
               $scope.myself = res.data.myself
               $scope.profit = res.data.profit
               $scope.subset = res.data.subset
               $state.go('distribution.detail')
           })
       }
       //查看相关联交易订单列表
       $scope.associate = function () {
           $scope.params1.mobile = $scope.cur_item.mobile
           $rootScope.crumbs = [
               {
                   name:'分销',
                   icon:'icon-fenxiao',
                   link:function () {
                       $state.go('distribution.index'),
                           $rootScope.crumbs.splice(1,2)
                   }
               },
               {
                   name:'详情',
                   link:function () {
                       $state.go('distribution.detail'),
                           $rootScope.crumbs.splice(2,1)
                   }
               },
               {
                   name:'相关联交易订单'
               }
           ]
           tablePages1()
               $state.go('distribution.associate_list')
       }
       //保存收益
       $scope.save_profit = function (valid) {
           let all_modal = function ($scope, $uibModalInstance) {
               $scope.cur_title = '保存成功'
               $scope.common_house = function () {
                   $uibModalInstance.close()
                   $rootScope.crumbs = [
                       {
                           name:'分销',
                           icon:'icon-fenxiao',
                       }
                   ]
                   $state.go('distribution.index')
               }
           }
           all_modal.$inject = ['$scope', '$uibModalInstance']
           if(valid){
               _ajax.post('/distribution/add-profit',{
                   mobile:$scope.myself.mobile,
                   profit:$scope.profit
               },function (res) {
                   console.log(res)
                   tablePages()
                   $uibModal.open({
                       templateUrl: 'pages/intelligent/cur_model.html',
                       controller: all_modal
                   })
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
           _ajax.post('/distribution/add-remarks',{
               order_no:$scope.cur_associate_item.order_no,
               remarks:$scope.cur_associate_item.remarks
           },function (res) {
               console.log(res)
               _ajax.get('/distribution/correlate-order',{
                   mobile:$scope.cur_item.mobile
               },function (res) {
                   console.log(res)
                   $scope.all_order_detail = res.data.details
                   $state.go('distribution.associate_list')
               })
           })
       }
   })