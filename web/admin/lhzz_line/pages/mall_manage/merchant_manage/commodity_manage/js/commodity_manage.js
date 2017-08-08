;
let commodity_manage = angular.module("commodity_manage_module",[]);
commodity_manage.controller("commodity_manage_ctrl",function ($scope,$http) {
    /*页面Menu切换 开始*/
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.wait_flag=false;
    $scope.del_flag=false;
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
      if(m===true){
        $scope.on_menu_flag=false;
      }else {
        $scope.on_menu_flag=true;
      }
    };

    $scope.show_1=true;
    $scope.show_a= function (m) {
      if(m===true){
        $scope.show_1=true;
      }else {
        $scope.show_1=false;
      }
    };
    $scope.show_2=true;
    $scope.show_b= function (m) {
      if(m===true){
        $scope.show_2=true;
      }else {
        $scope.show_2=false;
      }
    };
    $scope.show_3=true;
    $scope.show_c= function (m) {
      if(m===true){
        $scope.show_3=true;
      }else {
        $scope.show_3=false;
      }
    };
    $scope.show_4=true;
    $scope.show_d= function (m) {
      if(m===true){
        $scope.show_4=true;
      }else {
        $scope.show_4=false;
      }
    };
    $scope.show_5=true;
    $scope.show_e= function (m) {
      if(m===true){
        $scope.show_5=true;
      }else {
        $scope.show_5=false;
      }
    };
    $scope.show_6=false;
    $scope.show_f= function (m) {
      if(m===true){
        $scope.show_6=true;
      }else {
        $scope.show_6=false;
      }
    };
    $scope.show_7=false;
    $scope.show_g= function (m) {
      if(m===true){
        $scope.show_7=true;
      }else {
        $scope.show_7=false;
      }
    };
    $scope.show_8=false;
    $scope.show_h= function (m) {
      if(m===true){
        $scope.show_8=true;
      }else {
        $scope.show_8=false;
      }
    };
    $scope.show_9=true;
    $scope.show_i= function (m) {
      if(m===true){
        $scope.show_9=true;
      }else {
        $scope.show_9=false;
      }
    };
    $scope.show_10=true;
    $scope.show_j= function (m) {
      if(m===true){
        $scope.show_10=true;
      }else {
        $scope.show_10=false;
      }
    };
    $scope.show_11=true;
    $scope.show_k= function (m) {
      if(m===true){
        $scope.show_11=true;
      }else {
        $scope.show_11=false;
      }
    };
    $scope.show_12=true;
    $scope.show_l= function (m) {
      if(m===true){
        $scope.show_12=true;
      }else {
        $scope.show_12=false;
      }
    };
    $scope.show_13=true;
    $scope.show_m= function (m) {
      if(m===true){
        $scope.show_13=true;
      }else {
        $scope.show_13=false;
      }
    };
    $scope.show_14=true;
    $scope.show_n= function (m) {
      if(m===true){
        $scope.show_14=true;
      }else {
        $scope.show_14=false;
      }
    };
    $scope.show_15=true;
    $scope.show_n= function (m) {
      if(m===true){
        $scope.show_15=true;
      }else {
        $scope.show_15=false;
      }
    };
    /*已上架表格Menu切换 结束*/

    /*已下架表格Menu切换 开始*/
    $scope.down_menu_flag=false;
    $scope.down_menu=function (m) {
      if(m===true){
        $scope.down_menu_flag=false;
      }else {
        $scope.down_menu_flag=true;
      }
    };

    $scope.down_1=true;
    $scope.down_a=function (m) {
      if(m===true){
        $scope.down_1=true;
      }else {
        $scope.down_1=false;
      }
    }
    $scope.down_2=true;
    $scope.down_b=function (m) {
      if(m===true){
        $scope.down_2=true;
      }else {
        $scope.down_2=false;
      }
    }
    $scope.down_3=true;
    $scope.down_c=function (m) {
      if(m===true){
        $scope.down_3=true;
      }else {
        $scope.down_3=false;
      }
    }
    $scope.down_4=true;
    $scope.down_d=function (m) {
      if(m===true){
        $scope.down_4=true;
      }else {
        $scope.down_4=false;
      }
    }
    $scope.down_5=true;
    $scope.down_e=function (m) {
      if(m===true){
        $scope.down_5=true;
      }else {
        $scope.down_5=false;
      }
    }
    $scope.down_6=false;
    $scope.down_f=function (m) {
      if(m===true){
        $scope.down_6=true;
      }else {
        $scope.down_6=false;
      }
    }
    $scope.down_7=false;
    $scope.down_g=function (m) {
      if(m===true){
        $scope.down_7=true;
      }else {
        $scope.down_7=false;
      }
    }
    $scope.down_8=false;
    $scope.down_h=function (m) {
      if(m===true){
        $scope.down_8=true;
      }else {
        $scope.down_8=false;
      }
    }
    $scope.down_9=true;
    $scope.down_i=function (m) {
      if(m===true){
        $scope.down_9=true;
      }else {
        $scope.down_9=false;
      }
    }
    $scope.down_10=false;
    $scope.down_j=function (m) {
      if(m===true){
        $scope.down_10=true;
      }else {
        $scope.down_10=false;
      }
    }
    $scope.down_11=true;
    $scope.down_k=function (m) {
      if(m===true){
        $scope.down_11=true;
      }else {
        $scope.down_11=false;
      }
    }
    $scope.down_12=true;
    $scope.down_l=function (m) {
      if(m===true){
        $scope.down_12=true;
      }else {
        $scope.down_12=false;
      }
    }
    $scope.down_13=true;
    $scope.down_m=function (m) {
      if(m===true){
        $scope.down_13=true;
      }else {
        $scope.down_13=false;
      }
    }
    $scope.down_14=true;
    $scope.down_n=function (m) {
      if(m===true){
        $scope.down_14=true;
      }else {
        $scope.down_14=false;
      }
    }
    $scope.down_15=true;
    $scope.down_o=function (m) {
      if(m===true){
        $scope.down_15=true;
      }else {
        $scope.down_15=false;
      }
    }
    $scope.down_16=true;
    $scope.down_p=function (m) {
      if(m===true){
        $scope.down_16=true;
      }else {
        $scope.down_16=false;
      }
    }
    $scope.down_17=true;
    $scope.down_q=function (m) {
      if(m===true){
        $scope.down_17=true;
      }else {
        $scope.down_17=false;
      }
    }
    /*已下架表格Menu切换 结束*/

    /*等待上架表格Menu切换 开始*/
    $scope.wait_menu_flag=false;
    $scope.wait_menu=function (m) {
      if(m===true){
        $scope.wait_menu_flag=false;
      }else {
        $scope.wait_menu_flag=true;
      }
    }

    $scope.wait_1=true;
    $scope.wait_a=function (m) {
      if(m===true){
        $scope.wait_1=true;
      }else {
        $scope.wait_1=false;
      }
    }
    $scope.wait_2=true;
    $scope.wait_b=function (m) {
      if(m===true){
        $scope.wait_2=true;
      }else {
        $scope.wait_2=false;
      }
    }
    $scope.wait_3=true;
    $scope.wait_c=function (m) {
      if(m===true){
        $scope.wait_3=true;
      }else {
        $scope.wait_3=false;
      }
    }
    $scope.wait_4=true;
    $scope.wait_d=function (m) {
      if(m===true){
        $scope.wait_4=true;
      }else {
        $scope.wait_4=false;
      }
    }
    $scope.wait_5=true;
    $scope.wait_e=function (m) {
      if(m===true){
        $scope.wait_5=true;
      }else {
        $scope.wait_5=false;
      }
    }
    $scope.wait_6=false;
    $scope.wait_f=function (m) {
      if(m===true){
        $scope.wait_6=true;
      }else {
        $scope.wait_6=false;
      }
    }
    $scope.wait_7=false;
    $scope.wait_g=function (m) {
      if(m===true){
        $scope.wait_7=true;
      }else {
        $scope.wait_7=false;
      }
    }
    $scope.wait_8=false;
    $scope.wait_h=function (m) {
      if(m===true){
        $scope.wait_8=true;
      }else {
        $scope.wait_8=false;
      }
    }
    $scope.wait_9=true;
    $scope.wait_i=function (m) {
      if(m===true){
        $scope.wait_9=true;
      }else {
        $scope.wait_9=false;
      }
    }
    $scope.wait_10=false;
    $scope.wait_j=function (m) {
      if(m===true){
        $scope.wait_10=true;
      }else {
        $scope.wait_10=false;
      }
    }
    $scope.wait_11=true;
    $scope.wait_k=function (m) {
      if(m===true){
        $scope.wait_11=true;
      }else {
        $scope.wait_11=false;
      }
    }
    $scope.wait_12=true;
    $scope.wait_l=function (m) {
      if(m===true){
        $scope.wait_12=true;
      }else {
        $scope.wait_12=false;
      }
    }
    $scope.wait_13=true;
    $scope.wait_m=function (m) {
      if(m===true){
        $scope.wait_13=true;
      }else {
        $scope.wait_13=false;
      }
    }
    $scope.wait_14=true;
    $scope.wait_n=function (m) {
      if(m===true){
        $scope.wait_14=true;
      }else {
        $scope.wait_14=false;
      }
    }
    $scope.wait_15=true;
    $scope.wait_o=function (m) {
      if(m===true){
        $scope.wait_15=true;
      }else {
        $scope.wait_15=false;
      }
    }
    /*等待上架表格Menu切换 结束*/
  });
