;
let brand_index = angular.module("brand_index_module",[]);
brand_index.controller("brand_index_ctrl",function ($scope,$http,$stateParams) {
  $scope.myng=$scope;
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  /*品牌审核开始*/
  $scope.on_shelves_list=[];
  $scope.down_shelves_list=[];
  $scope.firstclass=[]; //一级分类
  $scope.brand_review_list=[];//列表循环数组
  $scope.application_num=[];//申请个数
  $scope.types=[];//品牌使用审核
  /*品牌审核结束*/

  /*--------------点击TAB 切换内容----------------*/
  //页面初始化
  if($stateParams.down_flag){
    $scope.on_flag=false;
    $scope.down_flag=true;
    $scope.check_flag=false;
    $scope.page=1;
  }else if($stateParams.check_flag){
    $scope.on_flag=false;
    $scope.down_flag=false;
    $scope.check_flag=true;
    $scope.page=1;
  }else{
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.check_flag=false;
    $scope.page=1;
  }


  //已上架
  $scope.on_shelves=function () {
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.check_flag=false;
    $scope.page=1;
    $scope.firstselect=0;
    $scope.online_up_flag=true;
    $scope.online_down_flag=false;
    $scope.online_time_flag='online_time:3';//上架时间，降序
    $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
      params:{
        status:1,
        'sort[]':$scope.online_time_flag
      }
    }).then(function (res) {
      // console.log(res);
      $scope.on_shelves_list=res.data.data.brand_list_admin.details;
    },function (err) {
      console.log(err);
    });
  };
  //已下架

  $scope.down_shelves=function () {
    $scope.down_flag=true;
    $scope.on_flag=false;
    $scope.check_flag=false;
    $scope.page=1;
    $scope.firstselect=0;
    $scope.online_up_flag=true;
    $scope.online_down_flag=false;
    $scope.online_time_flag='offline_time:3';//下架时间，降序排序
    $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
      params:{
        status:0,
        'sort[]':$scope.online_time_flag
      }
    }).then(function (res) {
      console.log("已下架后台列表");
      console.log(res);
      $scope.down_shelves_list=res.data.data.brand_list_admin.details;
    },function (err) {
      console.log(err);
    });

    /*监听*/
    $scope.$watch('firstselect',function (newVal,oldVal) {
      $scope.down_two=newVal;
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:0,
          pid:newVal,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        console.log('下架')
        console.log(res);
        $scope.down_shelves_list=res.data.data.brand_list_admin.details;
        /*--------------------分页------------------------*/
        $scope.history_list=[];
        $scope.history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);//获取总页数
        let all_num=$scope.history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.history_list.push(i+1)
        }
        $scope.page=1;
        //点击数字，跳转到多少页
        $scope.choosePage=function (page) {
          if($scope.history_list.indexOf(parseInt(page))!=-1){
            $scope.page=page;
            $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
              params:{
                status:0,
                pid:newVal,
                page:$scope.page,
                'sort[]':$scope.online_time_flag
              }
            }).then(function (res) {
              console.log(newVal);
              console.log($scope.page)
              $scope.down_shelves_list=res.data.data.brand_list_admin.details;
            },function (err) {
              console.log(err);
            });
          }
        };
        //显示当前是第几页的样式
        $scope.isActivePage=function (page) {
          return $scope.page==page;
        };
        //进入页面，默认设置为第一页
        if($scope.page===undefined){
          $scope.page=1;
        }
        //上一页
        $scope.Previous=function () {
          if($scope.page>1){                //当页数大于1时，执行
            $scope.page--;
            $scope.choosePage($scope.page);
          }
        };
        //下一页
        $scope.Next=function () {
          if($scope.page<$scope.history_all_page){ //判断是否为最后一页，如果不是，页数+1,
            $scope.page++;
            $scope.choosePage($scope.page);
          }
        }
      },function (err) {
        console.log(err);
      });
    });
    //监听二级
    $scope.$watch('secselect',function (newVal,oldVal) {
      if(newVal==0){
        newVal=$scope.down_two
      }
      $scope.down_three=newVal;
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:0,
          pid:newVal,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        $scope.down_shelves_list=res.data.data.brand_list_admin.details;
        /*重新计算页数 开始*/
        $scope.history_list=[];
        $scope.history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);//获取总页数
        let all_num=$scope.history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.history_list.push(i+1)
        }
        $scope.page=1;
        /*重新计算页数 结束*/
      },function (err) {
        console.log(err);
      });
    });
    //监听三级
    $scope.$watch('three_select',function (newVal,oldVal) {
      if(newVal==0){
        newVal=$scope.down_three
      }
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:0,
          pid:newVal,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        $scope.down_shelves_list=res.data.data.brand_list_admin.details;
        /*重新计算页数 开始*/
        $scope.history_list=[];
        $scope.history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);//获取总页数
        let all_num=$scope.history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.history_list.push(i+1)
        }
        $scope.page=1;
        /*重新计算页数 结束*/
      },function (err) {
        console.log(err);
      });
    });
  };



  //品牌审核
  $scope.wait_shelves=function () {
    $scope.check_flag=true;
    $scope.on_flag=false;
    $scope.down_flag=false;
    $scope.page=1;
    $http.get('http://test.cdlhzz.cn:888/mall/brand-application-review-list',{
      params:{
        review_status:-1
      }
    }).then(function (res) {
      console.log("品牌审核");
      console.log(res);
      $scope.brand_review_list=res.data.data.brand_application_review_list.details;
      console.log($scope.brand_review_list)
      /*判断多少个申请个数*/
      for(let [key,value] of $scope.brand_review_list.entries()){
        if(value.review_status==='待审核'){
          $scope.application_num.push(value);
        }
      }
    },function (err) {
      console.log(err);
    });
  };



  /*分类选择一级下拉框*/

  $scope.firstClass = (function () {
    $http({
      method: "get",
      url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
    }).then(function (response) {
      // console.log(response)
      $scope.firstclass = response.data.data.categories;
      $scope.firstselect = response.data.data.categories[0].id;
    })
  })();


  /*分类选择二级下拉框*/
  $scope.secondclass=[];//二级分类数组
  $scope.subClass = function (obj) {
    $http({
      method: "get",
      url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
      params: {pid: obj}
    }).then(function (response) {
      //console.log(response);
      $scope.secondclass = response.data.data.categories;
      $scope.secselect = response.data.data.categories[0].id;
    })
  };
  /*分类选择三级下拉框*/
  $scope.three_class=[];//二级分类数组
  $scope.three_Class = function (obj) {
    $http({
      method: "get",
      url: "http://test.cdlhzz.cn:888/mall/categories-manage-admin",
      params: {pid: obj}
    }).then(function (response) {
      $scope.three_class = response.data.data.categories;
      $scope.three_select = response.data.data.categories[0].id;
    })
  };

  /*==============================已上架===================================*/
  //已上架后台列表
  $scope.online_time_flag='online_time:3';
  $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
    params:{
      status:1,
      'sort[]':$scope.online_time_flag
    }
  }).then(function (res) {
    console.log("已上架")
     console.log(res);
    $scope.on_shelves_list=res.data.data.brand_list_admin.details;
  },function (err) {
    console.log(err);
  });
  /*三级联动搜索*/
  //监听一级
  $scope.$watch('firstselect',function (newVal,oldVal) {
    $scope.on_two=newVal;
    $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
      params:{
        status:1,
        'sort[]':$scope.online_time_flag,
        pid:newVal
      }
    }).then(function (res) {
      // console.log('已上架一级')
      // console.log(res);
      $scope.on_shelves_list=res.data.data.brand_list_admin.details;
      /*--------------------分页------------------------*/
      $scope.on_history_list=[];
      $scope.on_history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);//获取总页数
      let all_num=$scope.on_history_all_page;//循环总页数
      for(let i=0;i<all_num;i++){
        $scope.on_history_list.push(i+1)
      }
      $scope.page=1;
      //点击数字，跳转到多少页
      $scope.on_choosePage=function (page) {
        if($scope.on_history_list.indexOf(parseInt(page))!=-1){
          $scope.page=page;
          $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
            params:{
              status:1,
              'sort[]':$scope.online_time_flag,
              pid:newVal,
              page:$scope.page
            }
          }).then(function (res) {
            console.log(newVal);
            console.log(res);
            $scope.on_shelves_list=res.data.data.brand_list_admin.details;
          },function (err) {
            console.log(err);
          });
        }
      };
      //显示当前是第几页的样式
      $scope.isActivePage=function (page) {
        return $scope.page==page;
      };
      //进入页面，默认设置为第一页
      if($scope.page===undefined){
        $scope.page=1;
      }
      //上一页
      $scope.on_Previous=function () {
        if($scope.page>1){                //当页数大于1时，执行
          $scope.page--;
          $scope.on_choosePage($scope.page);
        }
      };
      //下一页
      $scope.on_Next=function () {
        if($scope.page<$scope.on_history_all_page){ //判断是否为最后一页，如果不是，页数+1,
          $scope.page++;
          $scope.on_choosePage($scope.page);
        }
      }
    },function (err) {
      console.log(err);
    });
  });
  //监听二级
  $scope.$watch('secselect',function (newVal,oldVal) {
    if(newVal==0){
      newVal=$scope.on_two
    }
    $scope.on_three=newVal;
    $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
      params:{
        status:1,
        'sort[]':$scope.online_time_flag,
        pid:newVal
      }
    }).then(function (res) {
      $scope.on_shelves_list=res.data.data.brand_list_admin.details;
      /*重新计算页数 开始*/
      $scope.on_history_list=[];
      $scope.on_history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);//获取总页数
      let all_num=$scope.on_history_all_page;//循环总页数
      for(let i=0;i<all_num;i++){
        $scope.on_history_list.push(i+1)
      }
      $scope.page=1;
      /*重新计算页数 结束*/
    },function (err) {
      console.log(err);
    });
  });
  //监听三级
  $scope.$watch('three_select',function (newVal,oldVal) {
    if(newVal==0){
      newVal=$scope.on_three
    }
    $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
      params:{
        status:1,
        'sort[]':$scope.online_time_flag,
        pid:newVal
      }
    }).then(function (res) {
      $scope.on_shelves_list=res.data.data.brand_list_admin.details;
      /*重新计算页数 开始*/
      $scope.on_history_list=[];
      $scope.on_history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);//获取总页数
      let all_num=$scope.on_history_all_page;//循环总页数
      for(let i=0;i<all_num;i++){
        $scope.on_history_list.push(i+1)
      }
      $scope.page=1;
      /*重新计算页数 结束*/
    },function (err) {
      console.log(err);
    });
  });

  //全选按钮
  $scope.on_select_all=false;
  $scope.on_all= function (m) {
    for(let i=0;i<$scope.on_shelves_list.length;i++){
      if(m===true){
        $scope.on_shelves_list[i].state=false;
        $scope.on_select_all=false;
      }else {
        $scope.on_shelves_list[i].state=true;
        $scope.on_select_all=true;
      }
    }
  };

  //时间排序
  $scope.online_up_flag=true;
  $scope.online_down_flag=false;
  $scope.online_time_change=function (num) {
    console.log(num);
    if($scope.online_up_flag==true){
      if(num==1){
        $scope.online_time_flag='online_time:4';
        $scope.online_status=1;
      }else{
        $scope.online_time_flag='offline_time:4';
        $scope.online_status=0;
      }
      $scope.online_up_flag=false;
      $scope.online_down_flag=true;
    }else if($scope.online_down_flag==true){
      if(num==1){
        $scope.online_time_flag='online_time:3';
        $scope.online_status=1;
      }else{
        $scope.online_time_flag='offline_time:3';
        $scope.online_status=0;
      }
      $scope.online_down_flag=false;
      $scope.online_up_flag=true;
    }
    $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
      params:{
        status:$scope.online_status,
        'sort[]':$scope.online_time_flag
      }
    }).then(function (res) {
      console.log(res);
      console.log($scope.online_status)
      if($scope.online_status==1){
        $scope.on_shelves_list=res.data.data.brand_list_admin.details;
      }else{
        $scope.down_shelves_list=res.data.data.brand_list_admin.details;
      }

    },function (err) {
      console.log(err);
    })
  };

  //批量下架
  $scope.batch_down_num=[];
  $scope.batch_down_shelves=function () {
    $scope.batch_down_num=[];
    $scope.sole_down_shelves_reason='';
    for(let [key,value] of $scope.on_shelves_list.entries()){
      if(JSON.stringify($scope.on_shelves_list).indexOf('"state":true')===-1){  //提示请勾选再删除
        $scope.on_modal_v='place_check_modal';
      }
      if(value.state){
        $scope.on_modal_v='on_shelves_down_reason_modal';
        $scope.batch_down_num.push(value.id);
      }
    }
  };
  //批量下架确认按钮
  $scope.down_shelver_ok=function () {
    let url='http://test.cdlhzz.cn:888/mall/brand-disable-batch';
    $http.post(url,{
      ids:$scope.batch_down_num.join(','),
      offline_reason:$scope.sole_down_shelves_reason
    },config).then(function (res) {
      console.log(res);
      $scope.on_select_all=false;//初始化check的勾选
      //重新请求已上架列表，达到刷新的作用
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:1,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        $scope.on_shelves_list=res.data.data.brand_list_admin.details;

        /*重新判断已上架的页数*/
        $scope.on_history_list=[];
        $scope.on_history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);
        let all_num=$scope.on_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.on_history_list.push(i+1)
        }
      },function (err) {
        console.log(err);
      });
      //重新请求已下架列表，达到刷新的作用
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:0,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        $scope.down_shelves_list=res.data.data.brand_list_admin.details;

        /*重新判断已下架的页数*/
        $scope.history_list=[];
        $scope.history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);
        let all_num=$scope.history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.history_list.push(i+1)
        }
      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log()
    })
  };

  /*单个下架*/
  $scope.down_shelver_btn=function (id) {
    $scope.batch_down_num=[];
    $scope.sole_down_shelves_reason='';
    $scope.down_shelver_ok=function () {
      let url='http://test.cdlhzz.cn:888/mall/brand-status-toggle';
      $http.post(url,{
        id:+id,
        offline_reason:$scope.sole_down_shelves_reason
      },config).then(function (res) {
        console.log(res);
        //重新请求已上架列表，达到刷新的作用
        $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
          params:{
          status:1,
          'sort[]':$scope.online_time_flag
        }
        }).then(function (res) {
          $scope.on_shelves_list=res.data.data.brand_list_admin.details;

          /*重新判断已上架的页数*/
          $scope.on_history_list=[];
          $scope.on_history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);
          let all_num=$scope.on_history_all_page;//循环总页数
          for(let i=0;i<all_num;i++){
            $scope.on_history_list.push(i+1)
          }
        },function (err) {
          console.log(err);
        });
        //重新请求已下架列表，达到刷新的作用
        $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
          params:{
            status:0,
            'sort[]':$scope.online_time_flag
          }
        }).then(function (res) {
          $scope.down_shelves_list=res.data.data.brand_list_admin.details;

          /*重新判断已下架的页数*/
          $scope.history_list=[];
          $scope.history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);
          let all_num=$scope.history_all_page;//循环总页数
          for(let i=0;i<all_num;i++){
            $scope.history_list.push(i+1)
          }
        },function (err) {
          console.log(err);
        });
      },function (err) {
        console.log()
      })
    }
  };

  /*================================已下架===================================*/

  /*三级联动搜索*/
  //监听一级

  //请求后台列表
  $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
    params:{
      status:0,
      'sort[]':'offline_time:3'
    }
  }).then(function (res) {
    console.log("已下架后台列表");
    console.log(res);
    $scope.down_shelves_list=res.data.data.brand_list_admin.details;
    /*--------------------分页------------------------*/
    $scope.history_list=[];
    $scope.history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);//获取总页数
    let all_num=$scope.history_all_page;//循环总页数
    for(let i=0;i<all_num;i++){
      $scope.history_list.push(i+1)
    }
    $scope.page=1;
  },function (err) {
    console.log(err);
  });
  // $scope.down_shelves_list=[];

  //全选按钮
  $scope.down_select_all=false;
  $scope.down_all= function (m) {
    for(let i=0;i<$scope.down_shelves_list.length;i++){
      if(m===true){
        $scope.down_shelves_list[i].state=false;
        $scope.down_select_all=false;
      }else {
        $scope.down_shelves_list[i].state=true;
        $scope.down_select_all=true;
      }
    }
  };
  //批量上架
  $scope.batch_num=[];
  $scope.batch_on_shelves=function () {
    $scope.batch_num=[];
    for(let [key,value] of $scope.down_shelves_list.entries()){
      if(JSON.stringify($scope.down_shelves_list).indexOf('"state":true')===-1){  //提示请勾选再删除
        $scope.down_modal_v='place_check_modal';
      }
      if(value.state){
        $scope.down_modal_v='on_shelves_modal';
        $scope.batch_num.push(value.id);
      }
    }
  };
  //批量上架确认按钮
  $scope.on_shelver_ok=function () {
    let url='http://test.cdlhzz.cn:888/mall/brand-enable-batch';
    $http.post(url,{
      ids:$scope.batch_num.join(',')
    },config).then(function (res) {
      console.log(res);
      $scope.down_select_all=false;//初始化check的勾选
      //重新请求已上架列表，达到刷新的作用
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:1,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        $scope.on_shelves_list=res.data.data.brand_list_admin.details;

        /*重新判断已上架的页数*/
        $scope.on_history_list=[];
        $scope.on_history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);
        let all_num=$scope.on_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.on_history_list.push(i+1)
        }

      },function (err) {
        console.log(err);
      });
      //重新请求已下架列表，达到刷新的作用
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:0,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        $scope.down_shelves_list=res.data.data.brand_list_admin.details;

        /*重新判断已下架的页数*/
        $scope.history_list=[];
        $scope.history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);
        let all_num=$scope.history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.history_list.push(i+1)
        }

      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log()
    })
  };
  //
  //单个上架
  $scope.on_shelver_btn=function (id) {
    $scope.sole_on_shelver_ok=function () {
      let url='http://test.cdlhzz.cn:888/mall/brand-enable-batch';
      $http.post(url,{
        ids:id
      },config).then(function (res) {
        console.log(res);
        //重新请求已上架列表，达到刷新的作用
        $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
          params:{
            status:1,
            'sort[]':$scope.online_time_flag
          }
        }).then(function (res) {
          $scope.on_shelves_list=res.data.data.brand_list_admin.details;
          /*重新判断已上架的页数*/
          $scope.on_history_list=[];
          $scope.on_history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);
          let all_num=$scope.on_history_all_page;//循环总页数
          for(let i=0;i<all_num;i++){
            $scope.on_history_list.push(i+1)
          }
        },function (err) {
          console.log(err);
        });
        //重新请求已下架列表，达到刷新的作用
        $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
          params:{
            status:0,
            'sort[]':$scope.online_time_flag
          }
        }).then(function (res) {
          $scope.down_shelves_list=res.data.data.brand_list_admin.details;
          /*重新判断已下架的页数*/
          $scope.history_list=[];
          $scope.history_all_page=Math.ceil(res.data.data.brand_list_admin.total/12);
          let all_num=$scope.history_all_page;//循环总页数
          for(let i=0;i<all_num;i++){
            $scope.history_list.push(i+1)
          }
        },function (err) {
          console.log(err);
        });
      },function (err) {
        console.log()
      })
    }
  };

  /*===========下架原因===========*/

  //点击输入下架原因
  $scope.down_reason_click=function (id,reason) {
    $scope.down_id=id;
    $scope.down_reason=reason;
  };
  //确定按钮
  $scope.down_reason_btn=function () {
    $scope.page=1;
    let url='http://test.cdlhzz.cn:888/mall/brand-offline-reason-reset';
    $http.post(url,{
      id:$scope.down_id,
      offline_reason:$scope.down_reason
    },config).then(function (res) {
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:0,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        $scope.down_shelves_list=res.data.data.brand_list_admin.details;
      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log(err);
    })
  };


  /*==============================品牌使用审核==================================*/

  //获取审核类型
  $http.get('http://test.cdlhzz.cn:888/site/review-statuses').then(function (res) {
    $scope.types=res.data.data.review_statuses;
    $scope.types.unshift({"name":"全部","value":-1});//接口没有全部，自己加！！
    $scope.selectValue=res.data.data.review_statuses[0];//全部
  },function (err) {
    console.log(err);
  });
  //监听类型
  $scope.$watch('selectValue',function (newVal,oldVal) {
    if(!!newVal){
      $http.get('http://test.cdlhzz.cn:888/mall/brand-application-review-list',{
        params:{
          review_status:+newVal.value,
          start_time:$scope.begin_time,
          end_time:$scope.end_time
        }
      }).then(function (res) {
        $scope.search_input_ok='';//清空搜索输入框
        $scope.brand_review_list=res.data.data.brand_application_review_list.details;

        /*--------------------分页------------------------*/
        $scope.check_history_list=[];
        $scope.check_history_all_page=Math.ceil(res.data.data.brand_application_review_list.total/12);//获取总页数
        let all_num=$scope.check_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.check_history_list.push(i+1)
        }
        $scope.page=1;
        //点击数字，跳转到多少页
        $scope.check_choosePage=function (page) {
          if($scope.check_history_list.indexOf(parseInt(page))!=-1){
            $scope.page=page;
            $http.get('http://test.cdlhzz.cn:888/mall/brand-application-review-list',{
              params:{
                review_status:+newVal.value,
                start_time:$scope.begin_time,
                end_time:$scope.end_time,
                page:$scope.page
              }
            }).then(function (res) {
              $scope.brand_review_list=res.data.data.brand_application_review_list.details;
            },function (err) {
              console.log(err);
            });
          }
        };
        //显示当前是第几页的样式
        $scope.isActivePage=function (page) {
          return $scope.page==page;
        };
        //进入页面，默认设置为第一页
        if($scope.page===undefined){
          $scope.page=1;
        }
        //上一页
        $scope.check_Previous=function () {
          if($scope.page>1){                //当页数大于1时，执行
            $scope.page--;
            $scope.check_choosePage($scope.page);
          }
        };
        //下一页
        $scope.check_Next=function () {
          if($scope.page<$scope.check_history_all_page){ //判断是否为最后一页，如果不是，页数+1,
            $scope.page++;
            $scope.check_choosePage($scope.page);
          }
        }
      },function (err) {
        console.log(err);
      })
    }
  });
  //监听开始时间
  $scope.$watch('begin_time',function (newVal,oldVal) {
    if(!!newVal){
      $http.get('http://test.cdlhzz.cn:888/mall/brand-application-review-list',{
        params:{
          review_status:+$scope.selectValue.value,
          start_time:newVal,
          end_time:$scope.end_time
        }
      }).then(function (res) {
        $scope.search_input_ok='';//清空搜索输入框
        $scope.brand_review_list=res.data.data.brand_application_review_list.details;
        /*重新计算页数 开始*/
        $scope.check_history_list=[];
        $scope.check_history_all_page=Math.ceil(res.data.data.brand_application_review_list.total/12);//获取总页数
        let all_num=$scope.check_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.check_history_list.push(i+1)
        }
        $scope.page=1;
        /*重新计算页数 结束*/
      },function (err) {
        console.log(err);
      })
    }
  });
  //监听结束时间
  $scope.$watch('end_time',function (newVal,oldVal) {
    if(!!newVal){
      $http.get('http://test.cdlhzz.cn:888/mall/brand-application-review-list',{
        params:{
          review_status:+$scope.selectValue.value,
          start_time:$scope.begin_time,
          end_time:newVal
        }
      }).then(function (res) {
        $scope.search_input_ok='';//清空搜索输入框
        $scope.brand_review_list=res.data.data.brand_application_review_list.details;
        /*重新计算页数 开始*/
        $scope.check_history_list=[];
        $scope.check_history_all_page=Math.ceil(res.data.data.brand_application_review_list.total/12);//获取总页数
        let all_num=$scope.check_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.check_history_list.push(i+1)
        }
        $scope.page=1;
        /*重新计算页数 结束*/
      },function (err) {
        console.log(err);
      })
    }
  });
  //审核备注
  $scope.review_click=function (id,reason) {
    $scope.review_id=id;
    $scope.check_review=reason;
  };
  //审核备注模态框，确认按钮
  $scope.review_btn=function () {
    $http.post('http://test.cdlhzz.cn:888/mall/brand-application-review-note-reset',{
      id:+$scope.review_id,
      review_note:$scope.check_review
    },config).then(function (res) {
      console.log('审核备注返回');
      console.log(res);
      //重新请求已下架列表，达到刷新的作用
      $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin',{
        params:{
          status:0,
          'sort[]':$scope.online_time_flag
        }
      }).then(function (res) {
        $scope.down_shelves_list=res.data.data.brand_list_admin.details;
      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log(err);
    })
  };
  //搜索
    $scope.search_btn=function () {
      $http.get('http://test.cdlhzz.cn:888/mall/brand-application-review-list',{
        params:{
          keyword:$scope.search_input_ok
        }
      }).then(function (res) {
        console.log(res);
        $scope.brand_review_list=res.data.data.brand_application_review_list.details;
        /*重新计算页数 开始*/
        $scope.check_history_list=[];
        $scope.check_history_all_page=Math.ceil(res.data.data.brand_application_review_list.total/12);//获取总页数
        let all_num=$scope.check_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.check_history_list.push(i+1)
        }
        $scope.page=1;
        /*重新计算页数 结束*/
      },function (err) {
        console.log(err);
      })
    }
});