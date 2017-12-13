angular.module("new_class_module",[])
  .controller("new_class_ctrl",function ($rootScope,$scope,$http,_ajax) {
    $rootScope.crumbs = [{
      name: '商城管理',
      icon: 'icon-shangchengguanli',
      link: $rootScope.mall_click
    }, {
      name: '新品牌/新分类审核'
    }];
    $scope.Config = {
      showJump: true,
      itemsPerPage: 12,
      currentPage: 1,
      onChange: function () {
        tablePages();
      }
    }
    $scope.params = {
      page: 1,                      // 当前页数
      sort_time:2,                 //默认降序
      start_time:'',               //开始时间
      end_time:'',                 //结束时间
      status:-1,                   //状态 -1：全部 0：不通过 1：通过
    };
    let tablePages=function () {
      $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
      _ajax.get('/supplieraccount/supplier-cate-list',$scope.params,function (res) {
        console.log(res);
        $scope.Config.totalItems = res.data.total;
        $scope.class_list=res.data.details;
      })
    };
    $scope.params.sort_time=2;//申请时间排序状态
    //状态选择数据
    $scope.status_select=[
      {id:-1,value:'全部'},
      {id:0,value:'待审核'},
      {id:2,value:'通过'},
      {id:1,value:'不通过'}
      ];
    $scope.select_value=$scope.status_select[0].id;//‘全部’为默认项
    //下拉框状态
    $scope.selectChange=function (value) {
      $scope.params.status=value;
      tablePages();
    };
    //时间排序
    $scope.sortClick=function () {
      $scope.params.sort_time===2?$scope.params.sort_time=1:$scope.params.sort_time=2;
      tablePages();
    };
    //开始和结束时间
    $scope.timeChange=function () {
      tablePages();
    }
    //审核备注按钮
    $scope.remark=function (value) {
      $scope.modal_reason=value;
      $('#check_modal').modal('show');
    };
  });