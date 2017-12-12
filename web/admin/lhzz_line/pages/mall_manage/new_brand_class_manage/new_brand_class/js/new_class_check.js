angular.module("new_class_check_module",[])
  .controller("new_class_check_ctrl",function ($rootScope,$scope,$http,_ajax,$state,$stateParams) {
    $rootScope.crumbs = [{
      name: '商城管理',
      icon: 'icon-shangchengguanli',
      link: $rootScope.mall_click
    }, {
      name: '新品牌/新分类审核',
      link: 'new_brand_class.new_class'
    },{
      name: '详情'
    }];
    $scope.cate_id=$stateParams.cate_id;//品牌传过来的id
    $scope.pass_review_status=$stateParams.review_status;//品牌传过来的状态
    console.log($scope.cate_id);
    console.log($scope.pass_review_status);
    $scope.check_arr=[{value:2,name:'通过'},{value:1,name:'不通过'}];
    $scope.check_select=$scope.check_arr[0].value;//默认第一项是通过
    //多行文本输入框
    $scope.remark='';
    $scope.change_txt=function (value) {
      if(value==undefined){
        $scope.remark='';
      }else{
        $scope.remark=value;
      }
    };
    //状态变化
    $scope.selectChange=function (value) {
      $scope.check_select=value;
    }
    _ajax.get('/supplieraccount/supplier-cate-view',{
      cate_id:$scope.cate_id
    },function (res) {
      console.log(res);
      $scope.title=res.data.title;//名称
      $scope.titles=res.data.titles;//分类所属
      $scope.icon=res.data.icon;//图片
      $scope.description=res.data.description;//描述
      $scope.apply_people=res.data.apply_people;//申请人
      $scope.create_time=res.data.create_time;//申请时间
      $scope.review_time=res.data.review_time;//审核时间
      $scope.reason=res.data.reason;//备注
      $scope.review_status=res.data.review_status;//审核状态
    });
    //模态框确认按钮
    $scope.check_ok=function () {
      console.log($scope.remark);
      _ajax.post('/mall/category-review',{
        id:$scope.cate_id,
        review_status:$scope.check_select,
        reason:$scope.remark
      },function (res) {
        console.log(res);
        setTimeout(function () {
          history.go(-1);
        },300)
      });
    };
    //返回按钮
    $scope.back_btn=function () {
      history.go(-1);
    }

  })