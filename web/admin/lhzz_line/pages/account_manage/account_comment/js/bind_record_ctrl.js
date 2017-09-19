/**
 * Created by xl on 2017/8/10 0010.
 */
var bind_record= angular.module("bind_record",[])
    .controller("bind_record_ctrl",function ($scope,$http,$state,$stateParams) {
        $scope.id = $stateParams.id;
        $scope.icon = $stateParams.icon;
        $scope.nickname = $stateParams.nickname;
        $scope.old_nickname = $stateParams.old_nickname;
        $scope.district_name = $stateParams.district_name;
        $scope.birthday = $stateParams.birthday;
        $scope.signature = $stateParams.signature;
        $scope.mobile = $stateParams.mobile;
        $scope.aite_cube_no = $stateParams.aite_cube_no;
        $scope.create_time = $stateParams.create_time;
        $scope.names = $stateParams.names;
        $scope.review_status_desc = $stateParams.review_status_desc;
        $scope.review_time = $stateParams.review_time;
        $scope.status_remark = $stateParams.status_remark;
        $scope.status_operator = $stateParams.status_operator;
        $scope.a = $stateParams.a;
        console.log($scope.id);
        //console.log($scope.status_remark);
        //console.log($scope.status_operator);

        $scope.flag = true;
        $scope.strat = false;

        //降序
        $scope.changePic = function () {
            $scope.flag = false;
            $scope.strat = true;
            console.log(1112);
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/reset-mobile-logs',
                params:{
                    "sort[]":"id:3"
                }

            }).then(function successCallback(response) {
                $scope.past_record = response.data.data.reset_mobile_logs.details;
                console.log(response);
            });

        };
        //升序
        $scope.changePicse = function () {
            $scope.strat = false;
            $scope.flag = true;
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/reset-mobile-logs',
                params:{
                    "sort[]":"id:4"
                }
            }).then(function successCallback(response) {
                $scope.past_record = response.data.data.reset_mobile_logs.details;
                console.log(response);
            });
        };

        //过往绑定记录
        $http({
            method: 'get',
            url: 'http://test.cdlhzz.cn:888/mall/reset-mobile-logs?user_id'+$scope.id
        }).then(function successCallback(response) {
            $scope.past_record = response.data.data.reset_mobile_logs.details;
            console.log(response);

            /*-----------------------------分页-----------------------*/
            $scope.history_list_colse=[];
            $scope.history_all_page=Math.ceil(response.data.data.reset_mobile_logs.total/12);//获取总页数
            console.log($scope.history_all_page);
            let all_num=$scope.history_all_page;//循环总页数
            for(let i=0;i<all_num;i++){
                $scope.history_list_colse.push(i+1);
                console.log($scope.history_list_colse)
            }
            //点击数字，跳转到多少页
            $scope.choosePageColse = function (page) {
                $scope.page=page;
                // $scope.isActivePage=function (now_page) {
                console.log($scope.page);
                // };
                $http.get('http://test.cdlhzz.cn:888/mall/reset-mobile-logs',{
                    params:{
                        'page':$scope.page
                    }
                }).then(function (response) {
                    // console.log(response);
                    $scope.past_record = response.data.data.reset_mobile_logs.details;

                },function (err) {
                    console.log(err);
                });
            };
            //显示当前是第几页的样式
            $scope.isActivePage=function (page) {
                return $scope.page==page;
            };
            //进入页面，默认设置为第一页
            if($scope.page===undefined){
                $scope.page=1;
            }
            //上一页
            $scope.Previous=function () {
                if($scope.page>1){                //当页数大于1时，执行
                    $scope.page--;
                    $scope.choosePageColse($scope.page);
                }
            };
            //下一页
            $scope.Next=function () {
                if($scope.page < $scope.history_all_page){ //判断是否为最后一页，如果不是，页数+1,
                    $scope.page++;
                    $scope.choosePageColse($scope.page);
                }
            }
        });

        //点击返回保存状态
        $scope.getReturn = function () {
            $state.go("account_comment",{'id':$scope.id,'icon':$scope.icon,
                'nickname':$scope.nickname,'old_nickname':$scope.old_nickname,
                'district_name':$scope.district_name,'birthday':$scope.birthday,
                'signature':$scope.signature,'mobile':$scope.mobile,'aite_cube_no':$scope.aite_cube_no,
                'create_time':$scope.create_time,'names':$scope.names,'review_status_desc':$scope.review_status_desc,
                'status_remark':$scope.status_remark,'status_operator':$scope.status_operator,'a':$scope.a
            })
        };




    });
