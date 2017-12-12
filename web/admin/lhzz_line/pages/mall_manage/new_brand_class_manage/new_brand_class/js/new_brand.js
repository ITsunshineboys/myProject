angular.module("new_brand_module",[])
  .controller("new_brand_ctrl",function ($rootScope,$scope,$http,_ajax) {
    $scope.Config = {
      showJump: true,
      itemsPerPage: 12,
      currentPage: 1,
      onChange: function () {
        tablePages();
      }
    }
    $scope.params = {
      page: 1,                        // 当前页数
      pid: 0,                      // 父分类id，0：全部
      sort_time:2                 //默认降序
    };
    let tablePages=function () {
      $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
      _ajax.get('/supplieraccount/supplier-brand-list',$scope.params,function (res) {
        console.log(res);
        $scope.Config.totalItems = res.data.total;
        $scope.brand_list=res.data.details;
      })
    };
    $scope.params.sort_time=2;//申请时间排序状态
    /*分类选择一级下拉框*/
    _ajax.get('/mall/categories-manage-admin',{},function (res) {
      $scope.firstclass = res.data.categories;
      $scope.firstselect = res.data.categories[0].id;
    });
    /*分类选择二级下拉框*/
    $scope.secondclass=[];//二级分类数组
    $scope.subClass = function (pid) {
      console.log(pid)
      pid!=0?($scope.two_select_flag=true,$scope.three_select_flag=false):($scope.two_select_flag=false,$scope.three_select_flag=false);
      $scope.down_two=pid;
      $scope.params.pid=$scope.down_two;
      tablePages();
      _ajax.get('/mall/categories-manage-admin',{pid: pid},function (res) {
        $scope.secondclass = res.data.categories;
        $scope.secselect = res.data.categories[0].id;
      })
    };
    /*分类选择三级下拉框*/
    $scope.three_class=[];//二级分类数组
    $scope.three_Class = function (pid) {
      console.log(pid)
      pid!=0?$scope.three_select_flag=true:$scope.three_select_flag=false;
      if(pid==0){
        pid=$scope.down_two
      }
      $scope.down_three=pid;
      $scope.params.pid=pid;
      _ajax.get('/mall/categories-manage-admin',{pid: pid},function (res) {
        $scope.three_class = res.data.categories;
        $scope.three_select = res.data.categories[0].id;
      })
      tablePages();
    };
    //监听三级
    $scope.last_Class=function (pid) {
      console.log(pid)
      if(pid==0){
        pid=$scope.down_three
      }
      $scope.last_value=pid;
      $scope.params.pid=pid;
      tablePages();
    }

    //审核备注按钮
    $scope.remark=function (value) {
      $scope.modal_reason=value;
      $('#check_modal').modal('show');
    };
    //排序
    $scope.sortClick=function () {
      $scope.params.sort_time===2?$scope.params.sort_time=1:$scope.params.sort_time=2;
      tablePages();
    }
  });