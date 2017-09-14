/**
 * Created by xl on 2017/8/10 0010.
 */
var operation_record= angular.module("operation_record",[])
    .controller("operation_record_ctrl",function ($scope,$http,$state,$stateParams) {
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
        $scope.id = $stateParams.id;
        $scope.review_status_desc = $stateParams.review_status_desc;
        $scope.legal_person = $stateParams.legal_person;
        $scope.identity_no = $stateParams.identity_no;
        $scope.identity_card_front_image = $stateParams.identity_card_front_image;
        $scope.identity_card_back_image = $stateParams.identity_card_back_image;
        $scope.review_status_desc = $stateParams.review_status_desc;
        $scope.review_time = $stateParams.review_time;
        $scope.status_remark = $stateParams.status_remark;
        $scope.status_operator = $stateParams.status_operator;
        $scope.a = $stateParams.a;
        //console.log($scope.review_status_desc);
        //console.log($scope.a);
        //console.log($scope.status_remark);
        $scope.flag = true;
        $scope.strat = false;
        //����
        $scope.changePic = function () {
            $scope.flag = false;
            $scope.strat = true;
            $http({
                method: 'get',
                url: 'http://test.cdlhzz.cn:888/mall/reset-user-status-logs',
                params: {
                    "sort[]": "id:3"
                }

            }).then(function successCallback(response) {
                $scope.past_record = response.data.data.reset_user_status_logs.details;
                console.log(response);
            })
            //����
            $scope.changePicse = function () {
                $scope.strat = false;
                $scope.flag = true;
                $http({
                    method: 'get',
                    url: 'http://test.cdlhzz.cn:888/mall/reset-user-status-logs',
                    params: {
                        "sort[]": "id:4"
                    }
                }).then(function successCallback(response) {
                    $scope.past_record = response.data.data.reset_user_status_logs.details;
                    console.log(response);
                });
            }
        };
        $http({
            method: 'get',
            url: 'http://test.cdlhzz.cn:888/mall/reset-user-status-logs'
        }).then(function successCallback(response) {
            $scope.past_record = response.data.data.reset_user_status_logs.details;

            /*-----------------------------��ҳ-----------------------*/
            $scope.history_list_colse = [];
            $scope.history_all_page = Math.ceil(response.data.data.reset_user_status_logs.total / 12);//��ȡ��ҳ��
            console.log($scope.history_all_page);
            let all_num = $scope.history_all_page;//ѭ����ҳ��
            for (let i = 0; i < all_num; i++) {
                $scope.history_list_colse.push(i + 1);
                console.log($scope.history_list_colse)
            }
            //������֣���ת������ҳ
            $scope.choosePageColse = function (page) {
                $scope.page = page;
                console.log($scope.page);
                $http.get('http://test.cdlhzz.cn:888/mall/reset-user-status-logs', {
                    params: {
                        'page': $scope.page
                    }
                }).then(function (response) {
                    // console.log(response);
                    $scope.past_record = response.data.data.reset_user_status_logs.details;

                }, function (err) {
                    console.log(err);
                });
            };
            //��ʾ��ǰ�ǵڼ�ҳ����ʽ
            $scope.isActivePage = function (page) {
                return $scope.page == page;
            };
            //����ҳ�棬Ĭ������Ϊ��һҳ
            if ($scope.page === undefined) {
                $scope.page = 1;
            }
            //��һҳ
            $scope.Previous = function () {
                if ($scope.page > 1) {                //��ҳ������1ʱ��ִ��
                    $scope.page--;
                    $scope.choosePageColse($scope.page);
                }
            };
            //��һҳ
            $scope.Next = function () {
                if ($scope.page < $scope.history_all_page) { //�ж��Ƿ�Ϊ���һҳ��������ǣ�ҳ��+1,
                    $scope.page++;
                    $scope.choosePageColse($scope.page);
                }
            }
        });
        //������ر���״̬
        $scope.getBack = function () {
            $state.go("account_comment", {
                'id': $scope.id,
                'icon': $scope.icon,
                'nickname': $scope.nickname,
                'old_nickname': $scope.old_nickname,
                'district_name': $scope.district_name,
                'birthday': $scope.birthday,
                'signature': $scope.signature,
                'mobile': $scope.mobile,
                'aite_cube_no': $scope.aite_cube_no,
                'create_time': $scope.create_time,
                'names': $scope.names,
                'review_status_desc': $scope.review_status_desc,
                'status_remark': $scope.status_remark,
                'status_operator': $scope.status_operator,
                'a': $scope.a
            })
        };
        $scope.getRemark = function (item) {
            $scope.remark =item;
        }
    });