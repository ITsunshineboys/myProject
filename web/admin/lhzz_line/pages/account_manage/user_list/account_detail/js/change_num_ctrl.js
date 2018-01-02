/**
 * Created by xl on 2017/8/10 0010.
 */
app.controller('change_num_ctrl', ['$state', '$scope', '$stateParams', '$http', '$rootScope', '_ajax', function ($state, $scope, $stateParams, $http, $rootScope, _ajax) {
    $rootScope.crumbs = [{
        name: '账户管理',
        icon: 'icon-zhanghuguanli',
        link: $rootScope.account_click
    },{
        name: '账户详情',
        link: -1
    },{
        name:'更换手机号'
    }];

    const pattern = /^(13|14|15|17|18|19)[0-9]{9}$/;
    $scope.warning = {
        isNull: false,
        isRegistered: false,
        isPhone: false
    }


    // 检测手机号是否为空
    $scope.checkNull = () => {
        !$scope.new_num ? $scope.warning.isNull = true : $scope.warning.isNull = false;
    }

 // 提交
    $scope.sureChange = function() {
        $scope.warning.isNull = false;
        $scope.warning.isRegistered = false;
        $scope.warning.isPhone = false;
        if(!$scope.new_num ){
            $scope.warning.isNull = true;
            return;
        }else{
            $scope.warning.isNull = false;
            testReg();
        }
    }
    
    
    // 检测手机号正则是否通过
    function testReg() {
        if(!pattern.test($scope.new_num)){
            $scope.warning.isPhone = true;
            $scope.warning.isNull = true;
            return
        }else{
            $scope.warning.isRegistered = false;
            testRegisted();
        }
    }

    // 检测手机号是否注册过
    function testRegisted() {
        _ajax.get('/site/check-mobile-registered', {mobile:$scope.new_num}, function (res) {
            if(res.code == 1019){
                $scope.warning.isRegistered = true;
                $scope.warning.isNull = true;
                return;
            }else{
                submit();
            }
        })
    }

    // 提交
    function submit () {
        _ajax.post('/mall/reset-mobile', {mobile:$scope.new_num,user_id:$stateParams.user_id}, function (res) {
            _alert('提示','更换成功',function () {
                   $state.go('account_mag_detail',{new_num:$scope.new_num})
            })
        })
    }
}])