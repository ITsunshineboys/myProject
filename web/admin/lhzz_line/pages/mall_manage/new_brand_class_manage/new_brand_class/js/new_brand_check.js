angular.module("new_brand_check_module",[])
  .controller("new_brand_check_ctrl",function ($rootScope,$scope,$http,_ajax,$state,$stateParams) {
    $rootScope.crumbs = [{
      name: '商城管理',
      icon: 'icon-shangchengguanli',
      link: $rootScope.mall_click
    }, {
      name: '新品牌/新分类审核',
      link: 'new_brand_class.new_brand'
    },{
      name: '详情'
    }];
    $scope.brand_id=$stateParams.brand_id;//品牌传过来的id
    $scope.pass_review_status=$stateParams.review_status;//品牌传过来的状态
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
    _ajax.get('/supplieraccount/supplier-brand-view',{
      brand_id:$scope.brand_id
    },function (res) {
      console.log(res);
      $scope.name=res.data.name;//名称
      $scope.certificate=res.data.certificate;//
      $scope.logo=res.data.logo;//logo
      $scope.category_titles=res.data.category_titles;//所在分类
      $scope.apply_people=res.data.apply_people;//申请人
      $scope.create_time=res.data.create_time;//创建时间
      $scope.review_status=res.data.review_status;//审核状态
      $scope.review_time=res.data.review_time;//审核时间
      $scope.reason=res.data.reason;//审核备注
    });
    //模态框确认按钮
    $scope.check_ok=function () {
      _ajax.post('/mall/brand-review',{
        id:$scope.brand_id,
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

  });