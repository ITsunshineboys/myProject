angular.module('income_pay_module',[])
.controller('income_pay_ctrl',function ($rootScope,$scope,$http,$state,_ajax) {
    $scope.myng=$scope;
    $rootScope.crumbs = [{
        name: '钱包',
        icon: 'icon-qianbao',
        link: 'supplier_wallet'
    }, {
        name: '收支明细',
    }];
    /*分页配置*/
    $scope.wjConfig = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    };
    $scope.params = {
        page: 1,                        // 当前页数
        time_type: 'all',               // 时间类型
        keyword: '',                    // 关键字查询
        start_time: '',                 // 自定义开始时间
        end_time: '',                   // 自定义结束时间
        type: '7',                      // 类型选择
        sort_time:'2'                   //默认降序
    };
    let tablePages=function () {
        $scope.params.page=$scope.wjConfig.currentPage;//点击页数，传对应的参数
        _ajax.get('/withdrawals/find-supplier-access-detail-list',$scope.params,function (res) {
            $scope.income_pay_list=res.data.list;
            $scope.wjConfig.totalItems = res.data.count;
        });
    };

  $scope.income_pay_list=[];
  //状态
  $scope.status_arr=[
    {id:7,value:'全部'},
    {id:6,value:'货款'},
    {id:5,value:'驳回'},
    {id:4,value:'提现中'},
    {id:3,value:'已提现'},
    {id:1,value:'充值'},
    {id:2,value:'扣款'}
    ];
  $scope.params.type=$scope.status_arr[0].id;//类型选择 默认全部
  //时间类型
  _ajax.get('/site/time-types',{},function (res) {
      $scope.time = res.data.time_types;
  })
  //监听时间和类型
    $scope.time_status_change=function () {
        $scope.wjConfig.currentPage = 1; //页数跳转到第一页
        $scope.params.keyword='';//初始化搜索框
        $scope.income_search='';//初始化搜索框
        $scope.params.start_time='';//初始化开始时间
        $scope.params.end_time='';//初始化结束时间
        tablePages();
    };
    //类型选择
    $scope.status_change=function () {
        $scope.wjConfig.currentPage = 1; //页数跳转到第一页
        $scope.params.keyword='';//初始化搜索框
        $scope.income_search='';//初始化搜索框
        tablePages();
    }
  //监听开始和结束时间
  $scope.time_change=function () {
      $scope.wjConfig.currentPage = 1; //页数跳转到第一页
      tablePages();
  };
  //监听搜索
  $scope.search_click=function () {
      $scope.wjConfig.currentPage = 1; //页数跳转到第一页
      $scope.params.time_type = $scope.time[0].value;   // 时间类型
      $scope.params.start_time = '';     // 自定义开始时间
      $scope.params.end_time = '';       // 自定义结束时间
      $scope.params.type=$scope.status_arr[0].id;//类型初始化
      $scope.params.keyword=$scope.income_search;
      tablePages();
  }
  //详情，如果是货款、扣款、充值状态就请求接口并弹出模态框，否则就跳转页面
    $scope.show_click=function (transaction_no,access_type,income) {
        if(access_type=='货款'||access_type=='扣款'||access_type=='充值'){
            _ajax.post('/withdrawals/supplier-access-detail',{transaction_no:transaction_no},function (res) {
                console.log(res);
                $('#detail_modal').modal('show');
                $scope.detail_list=res.data;
                for(let value of $scope.detail_list){
                    if(value.name=='扣款金额' || value.name=='货款金额'){
                        $scope.money_flag=true;
                    }
                }
            });
        }else{
            $scope.detail_modal_flag="";
            setTimeout(function () {
                $state.go('wallet_detail',{transaction_no:transaction_no,income:'income'});
            },300);
        }
    };

    //时间排序
    $scope.params.sort_time=2;//默认降序
    $scope.time_src='lib/images/arrow_down.png';//默认降序
    $scope.time_sort=function () {
        //图标和排序
        if($scope.time_src=='lib/images/arrow_down.png'){
            $scope.params.sort_time=1;
            $scope.time_src='lib/images/arrow_up.png';
        }else{
            $scope.time_src='lib/images/arrow_down.png';
            $scope.params.sort_time=2;
        }
        $scope.wjConfig.currentPage = 1; //页数跳转到第一页
        tablePages();
    };
});