angular.module('apply_case',[])
        .controller('apply_case_ctrl',function ($rootScope,$scope,_ajax,$http,$state,$uibModal) {
            $rootScope.crumbs = [
                {
                    name:'申请样板间',
                    icon:'icon-yangbanjian'
                }
            ]
            //申请样板间部分
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
                _ajax.get('/effect/effect-list',$scope.params,function (res) {
                    console.log(res);
                    $scope.cur_today_apply = res.data.today_apply
                    $scope.cur_today_earnest = res.data.today_earnest
                    $scope.cur_all_apply = res.data.all_apply
                    $scope.cur_all_earnest = res.data.all_earnest
                    $scope.apply_list = res.data['0'].list
                    $scope.Config.totalItems = res.data['0'].total_page
                })
            };
            $scope.params = {
                time_type:'all',
                keyword:'',
                start_time:'',
                end_time:''
            };
            $scope.getApplyCase = function () {
                $scope.Config.currentPage = 1
                $scope.params.keyword = ''
                $scope.keyword = ''
                if($scope.params.time_type == 'custom'){
                    if($scope.params.start_time!=''||$scope.params.start_time!=''){
                        tablePages()
                    }
                }else{
                    tablePages()
                }
            }
            tablePages()
            $scope.$watch('keyword',function (newVal,oldVal) {
                if(newVal == ''){
                    $scope.params.keyword = newVal
                    tablePages()
                }
            })
            $scope.ctrlScope = $scope
            $scope.keyword = ''
            $scope.time_type = [
                {name:'全部时间',str:'all'},
                {name:'今天',str:'today'},
                {name:'本周',str:'week'},
                {name:'本月',str:'month'},
                {name:'本年',str:'year'},
                {name:'自定义',str:'custom'}
            ]
            //修改样板间备注
            //弹出备注模态框
            $scope.replace_remarks = function (item) {
                $scope.cur_item = angular.copy(item)
            }
            //修改备注请求
            $scope.edit_remarks = function () {
                console.log($scope.cur_item)
                _ajax.post('/effect/edit-remark',{
                    id:$scope.cur_item.id,
                    remark:$scope.cur_item.remark
                },function (res) {
                    console.log(response)
                    tablePages()
                })
            }
            //搜索手机号或姓名
            $scope.update_keyword = function () {
                // $scope.cur_time_type = $scope.time_type[0]
                $scope.params.time_type = 'all'
                $scope.params.start_time = ''
                $scope.params.end_time = ''
                $scope.params.keyword = $scope.keyword
                tablePages()
            }
            //查看详情
            //获取详情
            $scope.get_detail = function (item) {
                $scope.cur_item = item
                _ajax.post('/effect/effect-view',{
                    id:item.id
                },function (res) {
                    console.log(res)
                    $scope.particulars_view = res.data.particulars_view
                    $scope.material = Object.entries(res.data.material)
                    for(let [key,value] of $scope.material.entries()){
                        value[2] = {index:key,cur_index:0}
                    }
                    console.log($scope.material)
                    $rootScope.crumbs = [
                        {
                            name:'申请样板间',
                            icon:'icon-yangbanjian',
                            link:function () {
                                $state.go('apply_case.index')
                                $rootScope.crumbs.splice(1,1)
                            }
                        },{
                        name:'详情'
                        }
                    ]
                    $state.go('apply_case.case_detail')
                })
            }
            //保存备注
            $scope.save_remark = function () {
                let all_modal = function ($scope, $uibModalInstance) {
                    $scope.cur_title = '保存成功'
                    $scope.common_house = function () {
                        $uibModalInstance.close()
                        $rootScope.crumbs = [
                            {
                                name:'申请样板间',
                                icon:'icon-yangbanjian',
                            }
                        ]
                        $state.go('apply_case.index')
                    }
                }
                all_modal.$inject = ['$scope', '$uibModalInstance']
                _ajax.post('/effect/effect-view',{
                    id:$scope.cur_item.id,
                    remark:$scope.particulars_view.remark
                },function (res) {
                    console.log(res)
                    tablePages()
                    $uibModal.open({
                        templateUrl: 'pages/intelligent/cur_model.html',
                        controller: all_modal
                    })
                })
            }
            //返回前页
            $scope.go_index = function () {
                $rootScope.crumbs = [
                    {
                        name:'申请样板间',
                        icon:'icon-yangbanjian',
                    }
                ]
                tablePages()
                $state.go('apply_case.index')
            }
        })