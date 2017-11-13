;
let brand_index = angular.module("brand_index_module",[]);
brand_index.controller("brand_index_ctrl",function ($scope,$http,$state,$stateParams,_ajax) {
  $scope.myng=$scope;
    /*品牌审核开始*/
    $scope.on_shelves_list=[];
    $scope.down_shelves_list=[];
    $scope.firstclass=[]; //一级分类
    $scope.brand_review_list=[];//列表循环数组
    $scope.application_num=[];//申请个数
    $scope.types=[];//品牌使用审核
    /*品牌审核结束*/

    /*分页配置*/
    $scope.wjConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            $scope.table.roles=[];//清空全选状态
            tablePages();
        }
    }
    let tablePages=function () {
        $scope.params.page=$scope.wjConfig.currentPage;//点击页数，传对应的参数
        _ajax.get('/mall/brand-list-admin',$scope.params,function (res) {
            console.log(res);
            if($scope.on_flag==true){  //--------------已上架
                $scope.on_shelves_list=res.data.brand_list_admin.details;
                $scope.wjConfig.totalItems = res.data.brand_list_admin.total;
            }else if($scope.down_flag==true){
                $scope.down_shelves_list=res.data.brand_list_admin.details;
                $scope.wjConfig.totalItems = res.data.brand_list_admin.total;
            }
        })
    };
    $scope.params = {
        page: 1,                        // 当前页数
        status: '1',                  // 0：已下架，1：已上架
        pid: '0',                      // 父分类id，0：全部
        'sort[]':'online_time:3'
    };

    //全选ID数组
    $scope.table = {
        roles: [],
    };
    $scope.checkAll = function () {
      if($scope.on_flag==true){
          !$scope.table.roles.length ? $scope.table.roles = $scope.on_shelves_list.map(function (item) {
              return item.id;
          }) : $scope.table.roles.length = 0;
      }else{
          !$scope.table.roles.length ? $scope.table.roles = $scope.down_shelves_list.map(function (item) {
              return item.id;
          }) : $scope.table.roles.length = 0;
      }
    };



  /*--------------点击TAB 切换内容----------------*/
  //页面初始化
  if($stateParams.down_flag){
    $scope.on_flag=false;
    $scope.down_flag=true;
    $scope.check_flag=false;

    $scope.table.roles=[];//清空全选状态
    $scope.wjConfig.currentPage=1;
    $scope.time_img='lib/images/sort_down.png';//时间排序图片
    $scope.params.status='0';
    $scope.params.pid='0';
    $scope.params['sort[]']='offline_time:3';//下架时间，降序排序
  }else if($stateParams.check_flag){
    $scope.on_flag=false;
    $scope.down_flag=false;
    $scope.check_flag=true;
  }else{
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.check_flag=false;

    $scope.table.roles=[];//清空全选状态
    $scope.wjConfig.currentPage=1;
    $scope.time_img='lib/images/sort_down.png';//时间排序图片
    $scope.params.page=1;
    $scope.params.status='1';
    $scope.params.pid='0'
    $scope.params['sort[]']='online_time:3';//上架时间，降序
  }


  //已上架
  $scope.on_shelves=function () {
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.check_flag=false;
    $scope.firstselect=0;

    $scope.table.roles=[];//清空全选状态
    $scope.wjConfig.currentPage=1;
    $scope.time_img='lib/images/sort_down.png';//时间排序图片
    $scope.params.status='1';
    $scope.params.pid='0'
    $scope.params['sort[]']='online_time:3';//上架时间，降序
    tablePages();
  };
  //已下架

  $scope.down_shelves=function () {
    $scope.down_flag=true;
    $scope.on_flag=false;
    $scope.check_flag=false;
    $scope.firstselect=0;

    $scope.table.roles=[];//清空全选状态
    $scope.wjConfig.currentPage=1;
    $scope.time_img='lib/images/sort_down.png';//时间排序图片
    $scope.params.page=1;
    $scope.params.status='0';
    $scope.params.pid='0'
    $scope.params['sort[]']='offline_time:3';//下架时间，降序排序
    tablePages();
  };

  //品牌审核
  $scope.wait_shelves=function () {
    $scope.check_flag=true;
    $scope.on_flag=false;
    $scope.down_flag=false;

    $scope.brand_Config.currentPage=1;
    $scope.brand_params.review_status=$scope.brand_types_arr[0].id;
    $scope.brand_params.start_time='';
    $scope.brand_params.end_time='';
    $scope.brand_img_src='lib/images/sort_down.png';
    $scope.brand_params['sort[]']='create_time:3';
    $scope.brand_params.keyword='';
    $scope.search_input_ok='';
    brand_Pages();
  };
    /*--------------点击TAB 切换内容 结束----------------*/


  /*分类选择一级下拉框*/
  _ajax.get('/mall/categories-manage-admin',{},function (res) {
      console.log(res);
      $scope.firstclass = res.data.categories;
      $scope.firstselect = res.data.categories[0].id;
  })
  //监听一级，返回数据
  $scope.$watch('firstselect',function (newVal,oldVal) {
      $scope.down_two=newVal;
      $scope.params.pid=$scope.down_two;
      $scope.table.roles=[];//清空全选状态
      tablePages();
  });
  /*分类选择二级下拉框*/
  $scope.secondclass=[];//二级分类数组
  $scope.subClass = function (pid) {
    _ajax.get('/mall/categories-manage-admin',{pid: pid},function (res) {
        $scope.secondclass = res.data.categories;
        $scope.secselect = res.data.categories[0].id;
    })
  };
    //监听二级
    $scope.$watch('secselect',function (newVal,oldVal) {
        if(newVal==0){
            newVal=$scope.down_two
        }
        $scope.down_three=newVal;
        $scope.params.pid=newVal;
        $scope.table.roles=[];//清空全选状态
        tablePages();
    });

  /*分类选择三级下拉框*/
  $scope.three_class=[];//二级分类数组
  $scope.three_Class = function (pid) {
    _ajax.get('/mall/categories-manage-admin',{pid: pid},function (res) {
        $scope.three_class = res.data.categories;
        $scope.three_select = res.data.categories[0].id;
    })
  };
    //监听三级
  $scope.$watch('three_select',function (newVal,oldVal) {
      if(newVal==0){
          newVal=$scope.down_three
      }
      $scope.last_value=newVal;
      $scope.params.pid=newVal;
      $scope.table.roles=[];//清空全选状态
      tablePages();
    });

  /*==============================已上架===================================*/
  //时间排序
    $scope.time_img='lib/images/sort_down.png';
    $scope.time_sort_click=function () {
        $scope.wjConfig.currentPage=1;
        if($scope.time_img=='lib/images/sort_down.png'){
            $scope.time_img='lib/images/sort_up.png';
            if($scope.on_flag==true){
                $scope.params['sort[]']="online_time:4";
            }else{
                $scope.params['sort[]']="offline_time:4";
            }
        }else{
            $scope.time_img='lib/images/sort_down.png';
            if($scope.on_flag==true){
                $scope.params['sort[]']="online_time:3";
            }else{
                $scope.params['sort[]']="offline_time:3";
            }
        }
        $scope.table.roles=[];
        tablePages();
    }

  //批量下架
  $scope.batch_down_shelves=function () {
    $scope.sole_down_shelves_reason='';
    if($scope.table.roles.length!=0){
        $scope.on_modal_v='on_shelves_down_reason_modal';
    }else{
        $scope.on_modal_v='place_check_modal';
    }
  };
  //批量下架确认按钮
  $scope.down_shelver_ok=function () {
    _ajax.post('/mall/brand-disable-batch',{
        ids:$scope.table.roles.join(','),
        offline_reason:$scope.sole_down_shelves_reason
    },function (res) {
        $scope.table.roles=[];//清空全选状态
        $scope.wjConfig.currentPage=1;//返回第一页
        tablePages();
    })
  };

  /*单个下架*/
  $scope.down_shelver_btn=function (id) {
    $scope.sole_down_shelves_reason='';
    $scope.down_shelver_ok=function () {
      _ajax.post('/mall/brand-status-toggle',{
          id:+id,
          offline_reason:$scope.sole_down_shelves_reason
      },function (res) {
          $scope.table.roles=[];//清空全选状态
          $scope.wjConfig.currentPage=1;//返回第一页
          tablePages();
      })
    }
  };

  /*================================已下架===================================*/

  //批量上架
  $scope.batch_on_shelves=function () {
    if($scope.table.roles.length!=0){
        $scope.down_modal_v='on_shelves_modal';
    }else{
        $scope.down_modal_v='place_check_modal';
    }
  };
  //批量上架确认按钮
  $scope.on_shelver_ok=function () {
    _ajax.post('/mall/brand-enable-batch',{ids:$scope.table.roles.join(',')},function (res) {
        $scope.table.roles=[];//清空全选状态
        $scope.wjConfig.currentPage=1;//返回第一页
        tablePages();
    })
  };
  //
  //单个上架
  $scope.on_shelver_btn=function (id) {
    $scope.sole_on_shelver_ok=function () {
      _ajax.post('/mall/brand-enable-batch',{ids:id},function (res) {
          $scope.table.roles=[];//清空全选状态
          $scope.wjConfig.currentPage=1;//返回第一页
          tablePages();
      })
    }
  };

  //点击输入下架原因
  $scope.down_reason_click=function (id,reason) {
    $scope.down_id=id;
    $scope.down_reason=reason;
  };
  //确定按钮
  $scope.down_reason_btn=function () {
    _ajax.post('/mall/brand-offline-reason-reset',{
        id:$scope.down_id,
        offline_reason:$scope.down_reason
    },function (res) {
        $scope.table.roles=[];//清空全选状态
        tablePages();
    })
  };

  /*==============================品牌使用审核==================================*/
    /*分页配置*/
    $scope.brand_Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            brand_Pages();
        }
    };
    let brand_Pages=function () {
        $scope.brand_params.page=$scope.brand_Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/mall/brand-application-review-list',$scope.brand_params,function (res) {
            $scope.brand_review_list=res.data.brand_application_review_list.details;
            $scope.brand_Config.totalItems = res.data.brand_application_review_list.total;
        })
    };
    $scope.brand_params = {
        page: 1,                        // 当前页数
        review_status:'-1',           //状态 -1：全部，0: 待审核，1: 审核不通过，2: 审核通过
        start_time:'',
        end_time:'',
        'sort[]':'create_time:3',
        keyword: '',
    };

    //判断有多少个申请
    _ajax.get('/mall/brand-application-review-list',{size:99999},function (res) {
        /*判断多少个申请个数*/
        $scope.application_num=[];
        for(let [key,value] of res.data.brand_application_review_list.details.entries()){
            if(value.review_status=='0'){
                $scope.application_num.push(value);
            }
        }
    });

  //获取审核类型
  $scope.brand_types_arr=[
      {id:'-1',value:'全部'},
      {id:'0',value:'等待审核'},
      {id:'1',value:'审核不通过'},
      {id:'2',value:'审核通过'}
  ];
  $scope.brand_params.review_status=$scope.brand_types_arr[0].id;
  //监听类型
  $scope.brand_types=function () {
      $scope.search_input_ok='';//清除搜索栏
      $scope.brand_params.keyword='';//清除搜索栏
      $scope.brand_Config.currentPage=1;
      brand_Pages();
  }
  //监听时间
  $scope.brand_time_change=function () {
      $scope.brand_Config.currentPage=1;
      brand_Pages();
  }
    //时间排序
    $scope.brand_img_src='lib/images/sort_down.png';
    $scope.brand_img_src_click=function () {
        if($scope.brand_img_src=='lib/images/sort_down.png'){
            $scope.brand_img_src='lib/images/sort_up.png';
            $scope.brand_params['sort[]']="create_time:4";
        }else{
            $scope.brand_img_src='lib/images/sort_down.png';
            $scope.brand_params['sort[]']="create_time:3"
        }
        $scope.brand_Config.currentPage=1;
        brand_Pages();
    };

    //搜索
    $scope.brand_search_btn=function () {
       $scope.brand_params.keyword= $scope.search_input_ok;
       $scope.brand_params.review_status=$scope.brand_types_arr[0].id;
       $scope.brand_params.start_time='';
       $scope.brand_params.end_time='';
       $scope.brand_img_src='lib/images/sort_down.png';
       $scope.brand_params['sort[]']='create_time:3';
       $scope.brand_Config.currentPage=1;
       brand_Pages();
    };

  //审核备注
  $scope.review_click=function (id,reason) {
    $scope.review_id=id;
    $scope.check_review=reason;
  };
  //审核备注模态框，确认按钮
  $scope.review_btn=function () {
    _ajax.post('/mall/brand-application-review-note-reset',{
        id:+$scope.review_id,
        review_note:$scope.check_review
    },function (res) {
        brand_Pages();
    })
  };
});