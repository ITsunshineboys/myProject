/**
 * Created by xl on 2017/8/10 0010.
 */
var bind_record= angular.module("bind_record",[])
    .controller("bind_record_ctrl",function ($rootScope,$scope,$http,$state,$stateParams,_ajax) {
        $rootScope.crumbs = [{
            name: '账户管理',
            icon: 'icon-zhanghuguanli',
            link: $rootScope.account_click
        },{
            name:'账户详情',
            link:'account_comment'
        },{
            name:'过往绑定记录'
        }];
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
        $scope.flag = true;
        $scope.strat = false;


        /*分页配置*/
        $scope.Config = {
            showJump: true,
            itemsPerPage: 12,
            currentPage: 1,
            onChange: function () {
                tablePages();
            }
        };
        //獲取賬號过往的绑定记录
        let tablePages = function () {
            $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
            _ajax.get('/mall/reset-mobile-logs',$scope.params,function (response) {
                console.log(response);
                $scope.past_record = response.data.reset_mobile_logs.details;
                $scope.Config.totalItems = response.data.reset_mobile_logs.total;
            })
        };
        $scope.params = {
            user_id:+$scope.id,
            page: 1,                        // 当前页数
            "sort[]":"id:4",
        };
        //降序
        $scope.changePic = function () {
            $scope.flag = false;
            $scope.strat = true;
            console.log(1112);
            $scope.params['sort[]'] = 'id:3';
            tablePages();

        };
        //升序
        $scope.changePicse = function () {
            $scope.strat = false;
            $scope.flag = true;
            $scope.params['sort[]'] = 'id:4';
            tablePages();
        };


        // });

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
