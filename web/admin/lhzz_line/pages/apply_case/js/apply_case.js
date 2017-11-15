angular.module('apply_case',[])
        .controller('apply_case_ctrl',function ($scope,$http,$state) {
            //post请求配置
            const config = {
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                transformRequest: function (data) {
                    return $.param(data)
                }
            };
            //添加小区部分
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
                $http.get('/effect/effect-list',{
                    params:$scope.params
                }).then(function (res) {
                    console.log(res);
                    $scope.cur_today_apply = res.data.data.today_apply
                    $scope.cur_today_earnest = res.data.data.today_earnest
                    $scope.cur_all_apply = res.data.data.all_apply
                    $scope.cur_all_earnest = res.data.data.all_earnest
                    $scope.apply_list = res.data.data['0'].list
                    // $scope.house_detail = res.data.model.details
                    $scope.Config.totalItems = res.data.data['0'].total_page
                },function (err) {
                    console.log(err);
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
                if($scope.params.time_type == 'custom'){
                    if($scope.params.start_time!=''||$scope.params.start_time!=''){
                        tablePages()
                    }
                }else{
                    tablePages()
                }
                $scope.params.keyword = ''
            }
            tablePages()
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
                $http.post('/effect/edit-remark',{
                    id:$scope.cur_item.id,
                    remark:$scope.cur_item.remark
                },config).then(function (response) {
                    console.log(response)
                    tablePages()
                },function (error) {
                    console.log(error)
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
                $http.post('/effect/effect-view',{
                        id:item.id
                },config).then(function (response) {
                    console.log(response)
                    $scope.particulars_view = response.data.data.particulars_view
                    $scope.material = Object.entries(response.data.data.material)
                    for(let [key,value] of $scope.material.entries()){
                        value[2] = {index:key,cur_index:0}
                    }
                    console.log($scope.material)
                    $state.go('apply_case.case_detail')
                },function (error) {
                    console.log(error)
                })
            }
            //保存备注
            $scope.save_remark = function () {
                $http.post('/effect/effect-view',{
                    id:$scope.cur_item.id,
                    remark:$scope.particulars_view.remark
                },config).then(function (response) {
                    console.log(response)
                    tablePages()
                },function (error) {
                    console.log(error)
                })
            }
            //返回前页
            $scope.go_index = function () {
                $scope.second_title =  ''
                $state.go('apply_case.index')
            }
        })