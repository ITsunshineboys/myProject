;
let commodity_manage = angular.module("commodity_manage",[])
  .controller("commodity_manage_ctrl",function ($scope,$http,$state,$stateParams) {
    $scope.myng=$scope;
    /*POST请求头*/
    const config = {
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      transformRequest: function (data) {
        return $.param(data)
      }
    };
    $scope.down_list_arr=[];
    $scope.up_list_arr=[];
    /*页面Menu切换 开始*/
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.wait_flag=false;
    $scope.logistics_flag=false;

    /*判断返回*/
      //已上架
    if($stateParams.on_flag==true){
      $scope.on_flag=true;
      $scope.down_flag=false;
      $scope.wait_flag=false;
      $scope.logistics_flag=false;
    }
      //已下架
    if($stateParams.down_flag==true){
      $scope.on_flag=false;
      $scope.down_flag=true;
      $scope.wait_flag=false;
      $scope.logistics_flag=false;
    }

    //已上架
    $scope.on_shelves=function () {
      $scope.on_flag=true;
      $scope.down_flag=false;
      $scope.wait_flag=false;
      $scope.logistics_flag=false;

      /*初始化已下架的搜索*/
      $scope.off_search_content='';//清空输入框值
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:0,
          keyword:$scope.off_search_content
        }
      }).then(function (res) {
        console.log(res);
        $scope.down_list_arr=res.data.data.goods_list_admin.details;
        /*--------------------分页------------------------*/
        $scope.down_history_list=[];
        $scope.down_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
        let all_num=$scope.down_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.down_history_list.push(i+1)
        }
        $scope.page=1;
      },function (err) {
        console.log(err);
      })

    };
  //已下架
    $scope.down_shelves=function () {
      $scope.down_flag=true;
      $scope.on_flag=false;
      $scope.wait_flag=false;
      $scope.logistics_flag=false;

      // 初始化已上架搜索
      $scope.search_content='';//搜索输入框的值
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:2,
          keyword:$scope.search_content
        }
      }).then(function (res) {
         console.log(res);
        $scope.up_list_arr=res.data.data.goods_list_admin.details;
        /*--------------------分页------------------------*/
        $scope.on_history_list=[];
        $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
        let all_num=$scope.on_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.on_history_list.push(i+1)
        }
        $scope.page=1;
      },function (err) {
        console.log(err);
      })
    };
    //等待下架
    $scope.wait_shelves=function () {
      $scope.wait_flag=true;
      $scope.on_flag=false;
      $scope.down_flag=false;
      $scope.logistics_flag=false;
    };
    //物流模块
    $scope.logistics=function () {
      $scope.logistics_flag=true;
      $scope.on_flag=false;
      $scope.down_flag=false;
      $scope.wait_flag=false;
    };
  /*页面Menu切换 结束*/


  /*已上架表格Menu切换 开始*/
  $scope.on_menu_flag=false;
    $scope.on_menu=function (m) {
      m===true?$scope.on_menu_flag=false:$scope.on_menu_flag=true
    };

    $scope.show_1=true;
    $scope.show_a= function (m) {
      m===true?$scope.show_1=true:$scope.show_1=false;
    };
    $scope.show_2=true;
    $scope.show_b= function (m) {
      m===true?$scope.show_2=true:$scope.show_2=false;
    };
    $scope.show_3=true;
    $scope.show_c= function (m) {
      m===true?$scope.show_3=true:$scope.show_3=false;
    };
    $scope.show_4=true;
    $scope.show_d= function (m) {
      m===true?$scope.show_4=true:$scope.show_4=false;
    };
    $scope.show_5=true;
    $scope.show_e= function (m) {
      m===true?$scope.show_5=true:$scope.show_5=false;
    };
    $scope.show_6=false;
    $scope.show_f= function (m) {
      m===true?$scope.show_6=true:$scope.show_6=false;
    };
    $scope.show_7=false;
    $scope.show_g= function (m) {
      m===true?$scope.show_7=true:$scope.show_7=false;
    };
    $scope.show_8=false;
    $scope.show_h= function (m) {
      m===true?$scope.show_8=true:$scope.show_8=false;
    };
    $scope.show_9=true;
    $scope.show_i= function (m) {
      m===true?$scope.show_9=true:$scope.show_9=false;
    };
    $scope.show_10=true;
    $scope.show_j= function (m) {
      m===true?$scope.show_10=true:$scope.show_10=false;
    };
    $scope.show_11=true;
    $scope.show_k= function (m) {
      m===true?$scope.show_11=true:$scope.show_11=false;
    };
    $scope.show_12=true;
    $scope.show_l= function (m) {
      m===true?$scope.show_12=true:$scope.show_12=false;
    };
    $scope.show_13=true;
    $scope.show_m= function (m) {
      m===true?$scope.show_13=true:$scope.show_13=false;
    };
    $scope.show_14=true;
    $scope.show_n= function (m) {
      m===true?$scope.show_14=true:$scope.show_14=false;
    };
    $scope.show_15=true;
    $scope.show_n= function (m) {
      m===true?$scope.show_15=true:$scope.show_15=false;
    };
  /*已上架表格Menu切换 结束*/

    /*已下架表格Menu切换 开始*/
    $scope.down_menu_flag=false;
    $scope.down_menu=function (m) {
      m===true?$scope.down_menu_flag=false:$scope.down_menu_flag=true;
    };

  $scope.down_1=true;
  $scope.down_a=function (m) {
    m===true?$scope.down_1=true:$scope.down_1=false;
  };
    $scope.down_2=true;
    $scope.down_b=function (m) {
      m===true?$scope.down_2=true:$scope.down_2=false;
    };
    $scope.down_3=true;
    $scope.down_c=function (m) {
      m===true?$scope.down_3=true:$scope.down_3=false;
    };
    $scope.down_4=true;
    $scope.down_d=function (m) {
      m===true?$scope.down_4=true:$scope.down_4=false;
    };
    $scope.down_5=true;
    $scope.down_e=function (m) {
      m===true?$scope.down_5=true:$scope.down_5=false;
    };
    $scope.down_6=false;
    $scope.down_f=function (m) {
      m===true?$scope.down_6=true:$scope.down_6=false;
    };
    $scope.down_7=false;
    $scope.down_g=function (m) {
      m===true?$scope.down_7=true:$scope.down_7=false;
    };
    $scope.down_8=false;
    $scope.down_h=function (m) {
      m===true?$scope.down_8=true:$scope.down_8=false;
    };
    $scope.down_9=true;
    $scope.down_i=function (m) {
      m===true?$scope.down_9=true:$scope.down_9=false;
    };
    $scope.down_10=false;
    $scope.down_j=function (m) {
      m===true?$scope.down_10=true:$scope.down_10=false;
    };
    $scope.down_11=true;
    $scope.down_k=function (m) {
      m===true?$scope.down_11=true:$scope.down_11=false;
    };
    $scope.down_12=true;
    $scope.down_l=function (m) {
      m===true?$scope.down_12=true:$scope.down_12=false;
    };
    $scope.down_13=true;
    $scope.down_m=function (m) {
      m===true?$scope.down_13=true:$scope.down_13=false;
    };
    $scope.down_14=true;
    $scope.down_n=function (m) {
      m===true?$scope.down_14=true:$scope.down_14=false;
    };
    $scope.down_15=true;
    $scope.down_o=function (m) {
      m===true?$scope.down_15=true:$scope.down_15=false;
    };
    $scope.down_16=true;
    $scope.down_p=function (m) {
      m===true?$scope.down_16=true:$scope.down_16=false;
    };
    $scope.down_17=true;
    $scope.down_q=function (m) {
      m===true?$scope.down_17=true:$scope.down_17=false;
    };
  /*已下架表格Menu切换 结束*/

  /*等待上架表格Menu切换 开始*/
    $scope.wait_menu_flag=false;
    $scope.wait_menu=function (m) {
      m===true?$scope.wait_menu_flag=false:$scope.wait_menu_flag=true;
    };

    $scope.wait_1=true;
    $scope.wait_a=function (m) {
      m===true?$scope.wait_1=true:$scope.wait_1=false;
    };
    $scope.wait_2=true;
    $scope.wait_b=function (m) {
      m===true?$scope.wait_2=true:$scope.wait_2=false;
    };
    $scope.wait_3=true;
    $scope.wait_c=function (m) {
      m===true?$scope.wait_3=true:$scope.wait_3=false;
    };
    $scope.wait_4=true;
    $scope.wait_d=function (m) {
      m===true?$scope.wait_4=true:$scope.wait_4=false;
    };
    $scope.wait_5=true;
    $scope.wait_e=function (m) {
      m===true?$scope.wait_5=true:$scope.wait_5=false;
    };
    $scope.wait_6=false;
    $scope.wait_f=function (m) {
      m===true?$scope.wait_6=true:$scope.wait_6=false;
    };
    $scope.wait_7=false;
    $scope.wait_g=function (m) {
      m===true?$scope.wait_7=true:$scope.wait_7=false;
    };
    $scope.wait_8=false;
    $scope.wait_h=function (m) {
      m===true?$scope.wait_8=true:$scope.wait_8=false;
    };
    $scope.wait_9=true;
    $scope.wait_i=function (m) {
      m===true?$scope.wait_9=true:$scope.wait_9=false;
    };
    $scope.wait_10=false;
    $scope.wait_j=function (m) {
      m===true?$scope.wait_10=true:$scope.wait_10=false;
    };
    $scope.wait_11=true;
    $scope.wait_k=function (m) {
      m===true?$scope.wait_11=true:$scope.wait_11=false;
    };
    $scope.wait_12=true;
    $scope.wait_l=function (m) {
      m===true?$scope.wait_12=true:$scope.wait_12=false;
    };
    $scope.wait_13=true;
    $scope.wait_m=function (m) {
      m===true?$scope.wait_13=true:$scope.wait_13=false;
    };
    $scope.wait_14=true;
    $scope.wait_n=function (m) {
      m===true?$scope.wait_14=true:$scope.wait_14=false;
    };
    $scope.wait_15=true;
    $scope.wait_o=function (m) {
      m===true?$scope.wait_15=true:$scope.wait_15=false;
    };
  /*等待上架表格Menu切换 结束*/

  /*-------------------公共功能 开始---------------------------*/

   //实时监听库存并修改
    $scope.change_left_number=function (id,left_num) {
      $http.post('http://test.cdlhzz.cn:888/mall/goods-inventory-reset',{
        id:+id,
        left_number:+left_num
      },config).then(function (res) {
        console.log(res);
      },function (err) {
        console.log(err);
      })
    };

    /*-------------------公共功能 结束---------------------------*/


    /*--------------------已上架 开始-------------------------*/

    $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
      params:{
        status:2
      }
    }).then(function (res) {
      console.log('已上架列表返回');
      console.log(res);
      $scope.up_list_arr=res.data.data.goods_list_admin.details;
      /*--------------------分页------------------------*/
      $scope.on_history_list=[];
      $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
      let all_num=$scope.on_history_all_page;//循环总页数
      for(let i=0;i<all_num;i++){
        $scope.on_history_list.push(i+1)
      }
      //$scope.sort_status='sold_number:4';
      $scope.page=1;
      //点击数字，跳转到多少页
      $scope.on_choosePage=function (page) {
        if($scope.on_history_list.indexOf(parseInt(page))!=-1){
          $scope.page=page;
          $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
            params:{
              status:2,
              page:$scope.page,
              'sort[]':$scope.sort_status
            }
          }).then(function (res) {
            console.log(res);
            $scope.up_list_arr=res.data.data.goods_list_admin.details;
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
      console.log(err)
    });


    /*------------------------排序---------------------------*/
    /*默认时间降序*/
    $scope.on_time_flag=false;
    $scope.down_time_flag=true;
    $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
      params:{
        status:2,
        'sort[]':'online_time:3'
      }
    }).then(function (res) {
      $scope.up_list_arr=res.data.data.goods_list_admin.details;
      /*--------------------分页------------------------*/
      $scope.on_history_list=[];
      $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
      let all_num=$scope.on_history_all_page;//循环总页数
      for(let i=0;i<all_num;i++){
        $scope.on_history_list.push(i+1)
      }
      $scope.page=1;
    },function (err) {
      console.log(err)
    });
    /*-------------------------销量排序-----------------------------*/
    $scope.default_sale_flag=true;
    $scope.on_sale_flag=false;
    $scope.down_sale_flag=false;
    //默认排序
    $scope.default_sale_sort=function () {
      $scope.on_sale_flag=false;
      $scope.default_sale_flag=false;
      $scope.down_sale_flag=true;
      $scope.sort_status='sold_number:4';
      $scope.page=1;
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:2,
          page:$scope.page,
          'sort[]':$scope.sort_status
        }
      }).then(function (res) {
        console.log(res);
        $scope.up_list_arr=res.data.data.goods_list_admin.details;
        /*--------------------分页------------------------*/
        $scope.on_history_list=[];
        $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
        let all_num=$scope.on_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.on_history_list.push(i+1)
        }
        $scope.page=1;
      },function (err) {
        console.log(err)
      })
    };
    //降序
    $scope.down_sale_sort=function () {
      $scope.on_sale_flag=true;
      $scope.down_sale_flag=false;
      $scope.sort_status='sold_number:3';
      $scope.page=1;
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:2,
          page:$scope.page,
          'sort[]':$scope.sort_status
        }
      }).then(function (res) {
        console.log(res);
        $scope.up_list_arr=res.data.data.goods_list_admin.details;
        /*--------------------分页------------------------*/
        $scope.on_history_list=[];
        $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
        let all_num=$scope.on_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.on_history_list.push(i+1)
        }
        $scope.page=1;
      },function (err) {
        console.log(err)
      })
    };
    //升序
    $scope.up_sale_sort=function () {
      $scope.on_sale_flag=false;
      $scope.down_sale_flag=true;
      $scope.sort_status='sold_number:4';
      $scope.page=1;
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:2,
          page:$scope.page,
          'sort[]':$scope.sort_status
        }
      }).then(function (res) {
        console.log(res);
        $scope.up_list_arr=res.data.data.goods_list_admin.details;
        /*--------------------分页------------------------*/
        $scope.on_history_list=[];
        $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
        let all_num=$scope.on_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.on_history_list.push(i+1)
        }
        $scope.page=1;
      },function (err) {
        console.log(err)
      })
    };
    /*上架时间排序*/
    /*降序*/
    $scope.on_time_sort=function (status) {
      if(status==0){
        $scope.my_sort_status=0;
        $scope.sort_status='offline_time:3';
      }else if(status==2){
        $scope.my_sort_status=2
        $scope.sort_status='online_time:3';
      }
      $scope.on_time_flag=false;
      $scope.down_time_flag=true;

      $scope.page=1;
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:+$scope.my_sort_status,
          page:$scope.page,
          'sort[]':$scope.sort_status
        }
      }).then(function (res) {
        console.log(res);
        if(status==2){
          $scope.up_list_arr=res.data.data.goods_list_admin.details;
          /*--------------------分页------------------------*/
          $scope.on_history_list=[];
          $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
          let all_num=$scope.on_history_all_page;//循环总页数
          for(let i=0;i<all_num;i++){
            $scope.on_history_list.push(i+1)
          }
        }else if(status==0){
          $scope.down_list_arr=res.data.data.goods_list_admin.details;
          /*--------------------分页------------------------*/
          $scope.down_history_list=[];
          $scope.down_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
          let all_num=$scope.down_history_all_page;//循环总页数
          for(let i=0;i<all_num;i++){
            $scope.down_history_list.push(i+1)
          }
        }

        $scope.page=1;
      },function (err) {
        console.log(err)
      })
    };
    /*升序*/
    $scope.down_time_sort=function (status) {
      if(status==0){
        $scope.my_sort_status=0;
        $scope.sort_status='offline_time:4';
      }else if(status==2){
        $scope.my_sort_status=2;
        $scope.sort_status='online_time:4';
      }
      $scope.on_time_flag=true;
      $scope.down_time_flag=false;
      $scope.page=1;
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:+$scope.my_sort_status,
          page:$scope.page,
          'sort[]':$scope.sort_status
        }
      }).then(function (res) {
        console.log(res);
        if(status==2){
          $scope.up_list_arr=res.data.data.goods_list_admin.details;
          /*--------------------分页------------------------*/
          $scope.on_history_list=[];
          $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
          let all_num=$scope.on_history_all_page;//循环总页数
          for(let i=0;i<all_num;i++){
            $scope.on_history_list.push(i+1)
          }
        }else if(status==0){
          $scope.down_list_arr=res.data.data.goods_list_admin.details;
          /*--------------------分页------------------------*/
          $scope.down_history_list=[];
          $scope.down_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
          let all_num=$scope.down_history_all_page;//循环总页数
          for(let i=0;i<all_num;i++){
            $scope.down_history_list.push(i+1)
          }
        }

        $scope.page=1;
      },function (err) {
        console.log(err)
      })
    };
    /*--------已上架全选按钮----------*/
    $scope.selectAll=false;
    $scope.all= function (m) {
      for(let[key,value] of $scope.up_list_arr.entries()){
        m===true?(value.state=false,$scope.selectAll=false):(value.state=true,$scope.selectAll=true)
      }
    };

  /*------------单个下架商品------------*/
  //点击下架
  $scope.offline_solo=function (id) {
    $scope.offline_id=id;
  };
  //确认下架按钮
  $scope.offline_solo_btn=function () {
    $http.post('http://test.cdlhzz.cn:888/mall/goods-status-toggle',{
      id:+$scope.offline_id
    },config).then(function (res) {
      console.log(res);
      /*重新请求数据，达到刷新的效果*/
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:2
        }
      }).then(function (res) {
        $scope.up_list_arr=res.data.data.goods_list_admin.details;
      },function (err) {
        console.log(err)
      });
    },function (err) {
      console.log(err);
    })
  };

  /*------------批量下架商品------------------*/
  $scope.batch_off_shelf=[];
  //点击下架按钮
  $scope.all_off_shelf=function () {
    $scope.batch_off_shelf=[];
    for(let[key,value] of $scope.up_list_arr.entries()){
      //如果没有勾选，提示勾选
      if(JSON.stringify($scope.up_list_arr).indexOf('"state":true')===-1){
          $scope.prompt='#please_check';
      }
      //正常下架
      if(value.state){
        $scope.batch_off_shelf.push(value.id);
        $scope.prompt='#down_shelves_modal'
      }
    }
  };
  //下架确认按钮
  $scope.all_off_shelf_confirm=function () {
    $http.post('http://test.cdlhzz.cn:888/mall/goods-disable-batch',{
      ids:$scope.batch_off_shelf.join(',')
    },config).then(function (res) {
      /*重新请求数据，达到刷新的效果*/
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:2
        }
      }).then(function (res) {
        $scope.up_list_arr=res.data.data.goods_list_admin.details;
      },function (err) {
        console.log(err)
      });
    },function (err) {
      console.log(err);
    })
  };



    /*----------------搜索---------------*/
    $scope.search_btn=function () {
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:2,
          keyword:$scope.search_content
        }
      }).then(function (res) {
        console.log(res);
        $scope.up_list_arr=res.data.data.goods_list_admin.details;
        /*--------------------分页------------------------*/
        $scope.on_history_list=[];
        $scope.on_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
        let all_num=$scope.on_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.on_history_list.push(i+1)
        }
        $scope.page=1;
      },function (err) {
        console.log(err);
      })
    };


    /*-----------添加分类--------------*/
    $scope.item_check = [];
    //获取一级
    $http({
      method: 'get',
      url: 'http://test.cdlhzz.cn:888/mall/categories'
    }).then(function successCallback(response) {
      $scope.details = response.data.data.categories;
      $scope.oneColor= $scope.details[0];
      // console.log(response);
      // console.log($scope.details)
    });
    //获取二级
    $http({
      method: 'get',
      url: 'http://test.cdlhzz.cn:888/mall/categories?pid=1'
    }).then(function successCallback(response) {
      $scope.second = response.data.data.categories;
      $scope.twoColor= $scope.second[0];
      // console.log($scope.second)
    });
    //获取三级
    $http({
      method: 'get',
      url: 'http://test.cdlhzz.cn:888/mall/categories?pid=2'
    }).then(function successCallback(response) {
      // console.log(response)
      $scope.three = response.data.data.categories;
      for(let [key,value] of $scope.three.entries()){
        if($scope.item_check.length == 0){
          value['complete'] = false
        }else{
          for(let [key1,value1] of $scope.item_check.entries()){
            if(value.id == value1.id){
              value.complete = true
            }
          }
        }
      }
    });
    //点击一级 获取相对应的二级
    $scope.getMore = function (n) {
      $scope.oneColor = n;
      $http({
        method: 'get',
        url: 'http://test.cdlhzz.cn:888/mall/categories?pid='+ n.id
      }).then(function successCallback(response) {
        $scope.second = response.data.data.categories;
        //console.log(response.data.data.categories[0].id);
        console.log(response);
        $scope.twoColor = $scope.second[0];
        $http({
          method: 'get',
          url: 'http://test.cdlhzz.cn:888/mall/categories?pid='+ $scope.second[0].id
        }).then(function successCallback(response) {
          $scope.three = response.data.data.categories;
          //console.log(response.data.data.categories[0].id);
          for(let [key,value] of $scope.three.entries()){
            if($scope.item_check.length == 0){
              value['complete'] = false
            }else{
              for(let [key1,value1] of $scope.item_check.entries()){
                if(value.id == value1.id){
                  value.complete = true
                }
              }
            }
          }
        });
      });
    };
    //点击二级 获取相对应的三级
    $scope.getMoreThree = function (n) {
      $scope.id=n;
      $scope.twoColor = n;
      $http({
        method: 'get',
        url: 'http://test.cdlhzz.cn:888/mall/categories?pid='+ n.id
      }).then(function successCallback(response) {
        $scope.three = response.data.data.categories;
        for(let [key,value] of $scope.three.entries()){
          if($scope.item_check.length == 0){
            value['complete'] = false
          }else{
            for(let [key1,value1] of $scope.item_check.entries()){
              if(value.id == value1.id){
                value.complete = true
              }
            }
          }
        }
      });
    };
    //添加拥有系列的三级
    $scope.check_item = function(item){
      $scope.item_check=[];
      $scope.threeColor = item;
      $scope.item_check.push(item);
    };

    /*------------分类确定----------------*/
    $scope.category_id='';
    $scope.add_confirm_red=false;//提示文字默认为false
    $scope.shop_style_go=function () {
      $scope.category_id='';
      if($scope.item_check.length!=0){
        $scope.add_confirm_modal='modal';
        $scope.category_id=$scope.item_check[0].id;
        setTimeout(function () {
          $state.go('shop_style',({
            category_id:$scope.category_id,
            first_category_title:$scope.oneColor.title,
            second_category_title:$scope.twoColor.title,
            third_category_title:$scope.threeColor.title
          })
          );
        },300);
        $scope.add_confirm_red=false;
      }else{
        $scope.add_confirm_red=true;
      }
    };

    /*--------------------已上架 结束-------------------------*/

    /*--------------------已下架 开始-------------------------*/

    $scope.down_list_arr=[];
    $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
      params:{
        status:0
      }
    }
    ).then(function (res) {
      console.log('已下架');
      console.log(res);
      $scope.down_list_arr=res.data.data.goods_list_admin.details;
      /*--------------------分页------------------------*/
      $scope.down_history_list=[];
      $scope.down_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
      let all_num=$scope.down_history_all_page;//循环总页数
      for(let i=0;i<all_num;i++){
        $scope.down_history_list.push(i+1)
      }
      $scope.page=1;
      //点击数字，跳转到多少页
      $scope.down_choosePage=function (page) {
        if($scope.down_history_list.indexOf(parseInt(page))!=-1){
          $scope.page=page;
          $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
            params:{
              status:0,
              page:$scope.page,
              'sort[]':$scope.sort_status
            }
          }).then(function (res) {
            //console.log(res);
            $scope.down_list_arr=res.data.data.goods_list_admin.details;
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
      $scope.down_Previous=function () {
        if($scope.page>1){                //当页数大于1时，执行
          $scope.page--;
          $scope.down_choosePage($scope.page);
        }
      };
      //下一页
      $scope.down_Next=function () {
        if($scope.page<$scope.down_history_all_page){ //判断是否为最后一页，如果不是，页数+1,
          $scope.page++;
          $scope.down_choosePage($scope.page);
        }
      }
    },function (err) {
      console.log(err)
    });
    /*--------已下架全选按钮----------*/
    // $scope.off_selectAll=false;
    // $scope.off_all= function (m) {
    //   for(let[key,value] of $scope.down_list_arr.entries()){
    //     m===true?(value.state=false,$scope.off_selectAll=false):(value.state=true,$scope.off_selectAll=true)
    //   }
    // };
    /*------------批量上架---------------*/
    // $scope.batch_on_shelf=[];
    // //点击下架按钮
    // $scope.all_on_shelf=function () {
    //   $scope.batch_on_shelf=[];
    //   for(let[key,value] of $scope.down_list_arr.entries()){
    //     //如果没有勾选，提示勾选
    //     if(JSON.stringify($scope.down_list_arr).indexOf('"state":true')===-1){
    //       $scope.on_prompt='#please_check';
    //     }
    //     //正常下架
    //     if(value.state){
    //       $scope.batch_on_shelf.push(value.id);
    //       $scope.on_prompt='#all_up_shelves_modal'
    //     }
    //   }
    // };
    //单个上架
    $scope.sole_on_shelf=function (id) {
      //$scope.batch_on_shelf=[];
      $scope.on_shelf_id=id;
      console.log($scope.on_shelf_id)
    };
    //上架确认按钮
    $scope.all_on_shelf_confirm=function () {
      $http.post('http://test.cdlhzz.cn:888/mall/goods-status-toggle',{
        id:+$scope.on_shelf_id
      },config).then(function (res) {
        console.log(res);
      },function (err) {
        console.log(err)
      })
    };

    /*----------------搜索---------------*/
    $scope.off_search_btn=function () {
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
        params:{
          status:0,
          keyword:$scope.off_search_content
        }
      }).then(function (res) {
        console.log(res);
        $scope.down_list_arr=res.data.data.goods_list_admin.details;
        /*--------------------分页------------------------*/
        $scope.down_history_list=[];
        $scope.down_history_all_page=Math.ceil(res.data.data.goods_list_admin.total/12);//获取总页数
        let all_num=$scope.down_history_all_page;//循环总页数
        for(let i=0;i<all_num;i++){
          $scope.down_history_list.push(i+1)
        }
        $scope.page=1;
      },function (err) {
        console.log(err);
      })
    };

    //批量删除
    // $scope.batch_del=[];
    // $scope.all_del_off=function () {
    //   $scope.batch_del=[];
    //   for(let[key,value] of $scope.down_list_arr.entries()){
    //     //如果没有勾选，提示勾选
    //     if(JSON.stringify($scope.down_list_arr).indexOf('"state":true')===-1){
    //       $scope.del_prompt='#please_check';
    //     }
    //     //正常下架
    //     if(value.state){
    //       $scope.batch_del.push(value.id);
    //       $scope.del_prompt='#off_del_modal'
    //     }
    //   }
    // };


    //单个删除
    $scope.solo_del_off=function (id) {
      $scope.batch_del=[];
      $scope.batch_del.push(id);
    };
    //删除确认按钮
    $scope.off_del_confirm=function () {
      $http.post('http://test.cdlhzz.cn:888/mall/goods-delete-batch',{
        ids:$scope.batch_del.join(',')
      },config).then(function (res) {
        console.log('删除');
        console.log(res);
      },function (err) {
        console.log(err)
      })
    };
    //下架原因
    $scope.reason_click=function (reason) {
      $scope.down_reason=reason;
    }


    /*--------------------已下架 结束-------------------------*/

  })
  .directive('stringToNumber2', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, ngModel) {
        ngModel.$parsers.push(function(value) {
          return '' + value;
        });
        ngModel.$formatters.push(function(value) {
          return parseInt(value);
        });
      }
    };
  });
