
let commodity_manage = angular.module("commodity_manage",[])
  .controller("commodity_manage_ctrl",function ($scope,$http,$state,$stateParams) {
      $scope.n = $stateParams.n;
  /*页面Menu切换 开始*/
    $scope.on_flag=true;
    $scope.down_flag=false;
    $scope.wait_flag=false;
    $scope.logistics_flag=false;
    //已上架
    $scope.on_shelves=function () {
      $scope.on_flag=true;
      $scope.down_flag=false;
      $scope.wait_flag=false;
      $scope.logistics_flag=false;
    };
  //已下架
    $scope.down_shelves=function () {
      $scope.down_flag=true;
      $scope.on_flag=false;
      $scope.wait_flag=false;
      $scope.logistics_flag=false;
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
    };
    $scope.wait_15=true;
    $scope.wait_o=function (m) {
      if(m===true){
        $scope.wait_15=true;
      }else {
        $scope.wait_15=false;
      }
    };
     /*等待上架表格Menu切换 结束*/
     //等待上架
      /*--------------------等待上架 开始-------------------------*/
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
      const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
          return $.param(data)
        }
      };
      $scope.myng=$scope;
      $scope.down_list_arr=[];
      $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
            params:{
              status:1
            }
          }
      ).then(function (res) {
            console.log('等待上架');
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
                    status:1,
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
      //查看获取审核备注
      $scope.getRest = function (item) {
        $scope.reset = item
      }

      /*----------------搜索---------------*/
      $scope.off_search_btn=function () {
        console.log($scope.off_search_content)
        $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
          params:{
            status:1,
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
       //监听搜索框的值为空时，返回最初的值
      $scope.$watch("off_search_content",function (newVal,oldVal) {
        if(newVal == ""){
          $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
                params:{
                  status:1
                }
              }
          ).then(function (res) {
                console.log('等待上架');
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
                        status:1,
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
              })
        }
      });
      /*=======降序=====*/
      $scope.on_time_sort=function () {
        $scope.sort_status='publish_time:3';
        $scope.on_time_flag=false;
        $scope.down_time_flag=true;

        $scope.page=1;
        $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
          params:{
            status:1,
            page:$scope.page,
            'sort[]':$scope.sort_status
          }
        }).then(function (res) {
          console.log(res);
            $scope.down_list_arr=res.data.data.goods_list_admin.details;
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
      /*============升序==================*/
      $scope.on_time_flag=true;
      $scope.down_time_flag=false;
      $scope.down_time_sort=function (status) {
        $scope.sort_status='publish_time:4';
        $scope.on_time_flag=true;
        $scope.down_time_flag=false;
        $scope.page=1;
        $http.get('http://test.cdlhzz.cn:888/mall/goods-list-admin',{
          params:{
            status:1,
            page:$scope.page,
            'sort[]':$scope.sort_status
          }
        }).then(function (res) {
          console.log(res);
            $scope.down_list_arr=res.data.data.goods_list_admin.details;
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

      /*--------------------已下架 结束-------------------------*/



     //物流模板开始
      $http({
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        //transformRequest: function (data) {
        //  return $.param(data)
        //},
        method: 'POST',
        url: 'http://test.cdlhzz.cn:888/mall/logistics-templates-supplier'
      }).then(function successCallback(response) {
        console.log(response);
        $scope.contentMore = response.data.data.logistics_templates_supplier;
        console.log($scope.contentMore);

      });

      //删除获取ID
       $scope.getId = function (item) {
         console.log(item);
         $scope.id = item;
         //删除物流模板
         $scope.deleteTemplate = function () {
           console.log($scope.id);
           $http({
             headers: {'Content-Type': 'application/x-www-form-urlencoded'},
             transformRequest: function (data) {
               return $.param(data)
             },
             method: 'POST',
             url: 'http://test.cdlhzz.cn:888/mall/logistics-template-status-toggle',
             data:{
               id:+$scope.id
             }
           }).then(function successCallback(response) {
             $http({
               headers: {'Content-Type': 'application/x-www-form-urlencoded'},
               //transformRequest: function (data) {
               //  return $.param(data)
               //},
               method: 'POST',
               url: 'http://test.cdlhzz.cn:888/mall/logistics-templates-supplier'
             }).then(function successCallback(response) {
               $scope.contentMore = response.data.data.logistics_templates_supplier;
               console.log($scope.contentMore);
             });
             console.log(response);
           });
         }
       };

      //查看物流模板详情
      $scope.getDetails = function (item) {
        $scope.id = item.id;
        $scope.name = item.name;
        console.log($scope.id);
        $state.go('template_details',{'id':$scope.id,'name':$scope.name})
      }
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