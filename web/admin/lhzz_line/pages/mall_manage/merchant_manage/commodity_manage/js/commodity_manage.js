;
let commodity_manage = angular.module("commodity_manage_module",[]);
commodity_manage.controller("commodity_manage_ctrl",function ($scope,$http,$stateParams) {

  /*页面Menu切换 开始*/
    //页面初始化
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.wait_flag=false;
    $scope.del_flag=false;
  //页面传值判断
  // $scope.on_flag=$stateParams.on_flag;
  // $scope.down_flag=$stateParams.down_flag;
  // $scope.wait_flag=$stateParams.wait_flag;
  // $scope.del_flag=$stateParams.del_flag;
  // if($scope.on_flag===true){
  //   $scope.down_flag=false;
  //   $scope.wait_flag=false;
  //   $scope.del_flag=false;
  // }
  // else if($scope.down_flag===true){
  //   $scope.on_flag=false;
  //   $scope.wait_flag=false;
  //   $scope.del_flag=false;
  // }else if($scope.wait_flag===true){
  //   $scope.on_flag=false;
  //   $scope.down_flag=false;
  //   $scope.del_flag=false;
  // }else if($scope.del_flag===true){
  //   $scope.on_flag=false;
  //   $scope.down_flag=false;
  //   $scope.wait_flag=false;
  // }
    //已上架
    $scope.on_shelves=function () {
      $scope.on_flag=true;
      $scope.down_flag=false;
      $scope.wait_flag=false;
      $scope.del_flag=false;
    };
    //已下架
    $scope.down_shelves=function () {
      $scope.down_flag=true;
      $scope.on_flag=false;
      $scope.wait_flag=false;
      $scope.del_flag=false;
    };
    //等待下架
    $scope.wait_shelves=function () {
      $scope.wait_flag=true;
      $scope.on_flag=false;
      $scope.down_flag=false;
      $scope.del_flag=false;
    };
    //物流模块
    $scope.logistics=function () {
      $scope.del_flag=true;
      $scope.on_flag=false;
      $scope.down_flag=false;
      $scope.wait_flag=false;
    };
    /*页面Menu切换 结束*/


/*已上架表格Menu切换 开始*/
    $scope.on_menu_flag=false;
    $scope.on_menu=function (m) {
      m===true?$scope.on_menu_flag=false:$scope.on_menu_flag=true;
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
    $scope.show_4=false;
    $scope.show_d= function (m) {
      m===true?$scope.show_4=true:$scope.show_4=false;
    };
    $scope.show_5=false;
    $scope.show_e= function (m) {
      m===true?$scope.show_5=true:$scope.show_5=false;
    };
    $scope.show_6=true;
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
    $scope.wait_4=false;
    $scope.wait_d=function (m) {
      m===true?$scope.wait_4=true:$scope.wait_4=false;
    };
    $scope.wait_5=false;
    $scope.wait_e=function (m) {
      m===true?$scope.wait_5=true:$scope.wait_5=false;
    };
    $scope.wait_6=true;
    $scope.wait_f=function (m) {
      m===true?$scope.wait_6=true:$scope.wait_6=false;
    };
    $scope.wait_7=true;
    $scope.wait_g=function (m) {
      m===true?$scope.wait_7=true:$scope.wait_7=false;
    };
    $scope.wait_8=false;
    $scope.wait_h=function (m) {
      m===true?$scope.wait_8=true:$scope.wait_8=false;
    };
    $scope.wait_9=false;
    $scope.wait_i=function (m) {
      m===true?$scope.wait_9=true:$scope.wait_9=false;
    };
    $scope.wait_10=true;
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
  $scope.wait_16=true;
  $scope.wait_p=function (m) {
    m===true?$scope.wait_16=true:$scope.wait_16=false;
  };
    /*等待上架表格Menu切换 结束*/

    /*已删除Menu切换 开始*/
    $scope.del_menu_flag=false;
    $scope.del_menu=function (m) {
      m===true?$scope.del_menu_flag=false:$scope.del_menu_flag=true;
    };
  $scope.del_1=true;$scope.del_2=true;$scope.del_3=true;$scope.del_6=true;$scope.del_7=true;
  $scope.del_9=true;$scope.del_10=true;$scope.del_11=true;$scope.del_12=true;$scope.del_13=true;$scope.del_14=true;
  $scope.del_4=false;$scope.del_5=false;$scope.del_8=false;
  $scope.del_a=function (m) {
    m===true?$scope.del_1=true:$scope.del_1=false;
  };
  $scope.del_b=function (m) {
    m===true?$scope.del_2=true:$scope.del_2=false;
  };
  $scope.del_c=function (m) {
    m===true?$scope.del_3=true:$scope.del_3=false;
  };
  $scope.del_d=function (m) {
    m===true?$scope.del_4=true:$scope.del_4=false;
  };
  $scope.del_e=function (m) {
    m===true?$scope.del_5=true:$scope.del_5=false;
  };
  $scope.del_f=function (m) {
    m===true?$scope.del_6=true:$scope.del_6=false;
  };
  $scope.del_g=function (m) {
    m===true?$scope.del_7=true:$scope.del_7=false;
  };
  $scope.del_h=function (m) {
    m===true?$scope.del_8=true:$scope.del_8=false;
  };
  $scope.del_i=function (m) {
    m===true?$scope.del_9=true:$scope.del_9=false;
  };
  $scope.del_j=function (m) {
    m===true?$scope.del_10=true:$scope.del_10=false;
  };
  $scope.delt_k=function (m) {
    m===true?$scope.del_11=true:$scope.del_11=false;
  };
  $scope.del_l=function (m) {
    m===true?$scope.del_12=true:$scope.del_12=false;
  };
  $scope.del_m=function (m) {
    m===true?$scope.del_13=true:$scope.del_13=false;
  };
  $scope.del_n=function (m) {
    m===true?$scope.del_14=true:$scope.del_14=false;
  };
    /*已删除Menu切换 结束*/
  });
