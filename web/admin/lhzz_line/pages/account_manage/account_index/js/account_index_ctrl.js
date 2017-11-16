
let account_index=angular.module("account_index_module",[]);
account_index.controller("account_index_ctrl",function ($scope,$http,$state,$stateParams,_ajax) {
  $scope.normal_flag=true;
  $scope.close_flag=false;
  $scope.mm = $scope;
  $scope.myng = $scope;
  $scope.normal=function () {
      $scope.normal_flag=true;
      $scope.close_flag=false;
      $scope.params.status = 1;
      tablePages();
  };
  $scope.close=function () {
      //关闭状态 选择时间排顺序  选择自定义 显示开始结束框
      _ajax.get('/site/time-types',{},function (response) {
          $scope.timeClose = response.data.time_types;
          $scope.selectValueClose = response.data.time_types[0];
          $scope.params.status = 0;
          $scope.params.time_type = $scope.selectValueClose.value;
          console.log(response);
          tablePages();
      });
      $scope.close_flag=true;
      $scope.normal_flag=false;
      $scope.params.status = 0;
      tablePages();
  };
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
    //获取账户管理列表 正常状态
    let tablePages = function () {
        $scope.params.page=$scope.Config.currentPage;//点击页数，传对应的参数
        _ajax.get('/mall/user-list',$scope.params,function (response) {
            console.log(response);
            console.log($scope.id);
            if($scope.close_flag==true){
                $scope.account_colse = response.data.user_list.details;
            }
            if($scope.normal_flag==true){
                $scope.account = response.data.user_list.details;
            }
            $scope.account = response.data.user_list.details;
            $scope.Config.totalItems = response.data.user_list.total;
            for(let [key,value] of $scope.account.entries()){
                value['names'] = value.role_names.join(',')
            }
        })
    };
    $scope.params = {
        keyword:'',
        page: 1,                        // 当前页数
        status:1,
        time_type: 'all',
        start_time: '',                 // 自定义开始时间
        end_time: '' ,                  // 自定义结束时间
        "sort[]":"id:4",
    };

    // 点击筛选降序
  $scope.changePic = function () {
    $scope.flag = false;
    $scope.strat = true;
    $scope.params["sort[]"] = 'id:4';
    $scope.params.status = 1;
    tablePages();

  };
  // //点击筛选升序
  $scope.changePicse = function () {
    $scope.strat = false;
    $scope.flag = true;
    $scope.params["sort[]"] = 'id:3';
    $scope.params.status = 1;
    tablePages();
  };


  //点击搜索
  $scope.search_text = "";
  $scope.getSearch = function () {
      $scope.params.keyword= $scope.search_text;
      $scope.params.status = 1;
      tablePages();
  };
  // //监听搜索的内容为空时，恢复初始状态
  $scope.$watch("search_text",function (newVal,oldVal) {
    if (newVal == "") {
          $scope.params.keyword ='';
          $scope.params.status = 1;
          tablePages();
    }
  });

// 2017-11-10位置

  //单个关闭原因
  $scope.getId = function (item) {
    $scope.id = item;
    $scope.getReson = function () {
      _ajax.post('/mall/user-status-toggle',{
          user_id:+$scope.id,
          remark:$scope.text},function (response) {
          $scope.params.status= 1;
          tablePages();
      });
    }
  };

  //点击关闭
   $scope.close_num_arr = [];
 $scope.change_model = function () {
   for(let [key,value] of $scope.account.entries()) {
     if (JSON.stringify($scope.account).indexOf('"state":true') === -1) {  //提示请勾选再删除
       $scope.more_modal = 'prompt_modal';
     }
     if (value.state) {  //直接删除
       $scope.more_modal = 'closemore_modal';
       $scope.close_num_arr.push(value.id);
     }
   }
 }

    //批量关闭原因
  let arr = [];
  $scope.changeStar = function (item) {
      arr.push(item);
      $scope.nemArr = arr.join(',');
      console.log($scope.nemArr)
  };
  //点击确定保存原因
   $scope.closeReset = function () {
       _ajax.post('/mall/user-disable-batch',{
           user_ids: $scope.nemArr,
           remark:$scope.more},function (response) {
                $scope.params.status= 1;
                tablePages();
       });
   };


  //正常状态 选择时间排顺序  选择自定义 显示开始结束框
  _ajax.get('/site/time-types',{},function (response) {
      $scope.time = response.data.time_types;
      $scope.selectValue = response.data.time_types[0];
      $scope.params.time_type = $scope.selectValue.value;
      $scope.params.status = 1;
      tablePages();
  });

  //============监听下拉框值的变化===========
  $scope.$watch('selectValue',function(newVal,oldVal){
      if(!!newVal){
        $scope.params.status = 1;
        $scope.params.time_type = newVal.value;
        tablePages();
    }
  });
  //监听开始时间
  $scope.$watch('begin_time',function (newVal,oldVal) {
      console.log(newVal);
      // $scope.page=1;//默认第一页
    if(newVal!=undefined && newVal!='' && $scope.begin_time!=undefined && $scope.end_time != undefined){
        $scope.params.time_type = 'custom';
        $scope.params.start_time = newVal;
        $scope.params.end_time = $scope.end_time;
        $scope.params.status = 1;
        tablePages();
    }
  });
  //监听结束时间
  $scope.$watch('end_time',function (newVal,oldVal) {
    // $scope.page=1;//默认第一页
    if(newVal!=undefined && newVal!='' && $scope.begin_time!=undefined && $scope.end_time!=undefined){
        $scope.params.time_type = 'custom';
        $scope.params.start_time = $scope.begin_time;
        $scope.params.end_time =newVal;
        $scope.params.status = 1;
        tablePages();
    }
  });


  //切换关闭的内容  第二部分

  //获取关闭原因
  $scope.getRemark = function (item) {
    $scope.remark = item.status_remark;
  };

  //单个选择开启
  $scope.getOpen = function (item) {
    console.log(item.id);
    $scope.getColse = function () {
      _ajax.post('/mall/user-status-toggle',{
          user_id:item.id,
          remark:item.status_remark
      },function (response) {
          $scope.params.status = 0;
          tablePages();
      });
    };
  };

   //批量开启
  //点击关闭
  $scope.open_num_arr = [];
  $scope.change_open_model = function () {
    for(let [key,value] of $scope.account_colse.entries()) {
      if (JSON.stringify($scope.account_colse).indexOf('"state":true') === -1) {  //提示请勾选再删除
        $scope.open_modal = 'sed_modal';
      }
      if (value.state) {  //直接删除
        $scope.open_modal = 'open_modal';
        $scope.open_num_arr.push(value.id);
      }
    }
  };
  //批量关闭原因
  let open_arr = [];
  $scope.changeOpen = function (item) {
    open_arr.push(item);
    $scope.nemArr = open_arr.join(',');
    console.log($scope.nemArr)
  };
  //点击确定保存原因
  $scope.openReset = function () {
    _ajax.post('/mall/user-enable-batch',{
        user_ids: $scope.nemArr
    },function (response) {
        $scope.params.status = 0;
        tablePages();

    })

  };
    // 点击筛选降序
    $scope.changePicClose = function () {
        console.log(111);
        $scope.flag = false;
        $scope.strat = true;
        $scope.params["sort[]"] = 'close_time:4';
        $scope.params.status = 0;
        tablePages();

    };
    $scope.changePicseClose = function () {
        console.log(222);
        $scope.flag = true;
        $scope.strat = false;
        $scope.params["sort[]"] = 'close_time:3';
        $scope.params.status = 0;
        tablePages();

    };




    // if ($scope.close_flag == true) {
        $scope.$watch('selectValueClose',function(newVal,oldVal){
            if(!!newVal) {
                $scope.params.status = 0;
                $scope.params.time_type = newVal.value;
                tablePages();
            }

        });
        //监听开始时间
        $scope.mm = $scope;
        $scope.myng = $scope;
        $scope.$watch('begin_time_more',function (newVal,oldVal) {
            console.log(1111);
            $scope.page=1;//默认第一页
            if(newVal!=undefined && newVal !='' && $scope.begin_time_more!=undefined && $scope.end_time_more!=undefined) {
                $scope.params.status = 0;
                $scope.params.time_type = 'custom';
                $scope.params.end_time = $scope.end_time_more;
                $scope.params.start_time = newVal;
                tablePages();
            }
        });
        //监听结束时间
        $scope.$watch('end_time_more',function (newVal,oldVal) {
            $scope.page = 1;//默认第一页
            if (newVal != undefined && newVal != '' && $scope.begin_time_more != undefined && $scope.end_time_more != undefined) {
                $scope.params.status = 0;
                $scope.params.time_type = 'custom';
                $scope.params.end_time = newVal;
                $scope.params.start_time = $scope.begin_time_more;
                tablePages();

            }
        });


        //关闭状态下 点击搜索
        $scope.getSearchClose = function () {
            console.log($scope.name_num);
            $scope.params.status = 0;
            $scope.params.keyword = $scope.name_num;
            tablePages();
        };
        //监听搜索的内容为空时，恢复初始状态
        $scope.$watch("name_num",function (newVal,oldVal) {
            if (newVal == "") {
                $scope.params.status = 0;
                tablePages();
            }
        })
    // }
});