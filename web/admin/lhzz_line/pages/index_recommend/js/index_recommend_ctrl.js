var index_recommend = angular.module("index_recommend_module",[]);
index_recommend.controller("index_recommend_ctrl",function ($scope,$http) {
$scope.myng=$scope;//原形继承转换，解决ng-model 无效问题

  //选择城市开始
  $scope.second_title='';//二级列表项初始化
  $scope.three_title='';//三级列表项初始化
  // $scope.ctrlScope = $scope;
  $scope.search_txt = '';
  //初始化省市区县;
  $http.get('districts2.json').then(function (response) {
    // console.log((response.data[0]['86']))
    let arr = [];
    let arr1 = [];
    for(let [key,value] of Object.entries(response.data[0]['86'])){
      arr.push({'id':key,'name':value})
    }
    $scope.province = arr;
    $scope.cur_province = $scope.province[22];
    for(let [key,value] of Object.entries(response.data[0][$scope.cur_province.id])){
      arr1.push({'id':key,'name':value})
    }
    $scope.city = arr1;
    $scope.cur_city = $scope.city[0]
  },function (error) {
    console.log(error)
  });
  //根据省动态获取市
  $scope.getCity = function (item) {
    console.log(item);
    $scope.cur_province = item;
    $http.get('districts2.json').then(function (response) {
      let arr1 = [];
      for(let [key,value] of Object.entries(response.data[0][item.id])){
        arr1.push({'id':key,'name':value})
      }
      $scope.city = arr1;
      $scope.cur_city = $scope.city[0]

    },function (error) {
      console.log(error)
    })
  };
  //选择城市结束

//全选按钮
  $scope.delete_batch_num=[];//初始化删除数目数组
  $scope.disable_batch_num=[];//初始化停用数目数组
  $scope.selectAll=false;
  $scope.all= function (m) {
    // for(let i=0;i<$scope.shop_rep.length;i++){
    //   if(m===true){
    //     $scope.shop_rep[i].state=true;
    //     $scope.selectAll=false;
    //   }else {
    //     $scope.shop_rep[i].state=false;
    //     $scope.selectAll=true;
    //   }
    // }
    console.log(m);
    for(let i=0;i<$scope.shop_rep.length;i++){
      if(m===true){
        $scope.shop_rep[i].state=false;
        $scope.selectAll=false;
      }else {
        $scope.shop_rep[i].state=true;
        $scope.selectAll=true;
      }
    }

  };
//POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };

  //后台推荐首页(admin)
  let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
  $http.get(recommend_url,{
    params:{
      'district_code':510100,
      'type':2
    }
  }).then(function (res) {
    // console.log(res);
    $scope.shop_rep = res.data.data.recommend_admin_index.details;
  },function (err) {
    console.log(err);
  });
  //批量删除按钮
  $scope.delete_batch_del=function () {
    $scope.delete_batch_num=[];
    console.log($scope.shop_rep);
    for(let [key,value] of $scope.shop_rep.entries()){
      if(value.state && value.status=="停用"){
        $scope.delete_batch_num.push(value.id)
      }
    }
  };
  //批量删除确认按钮
  $scope.delete_batch_btn=function () {
    let url='http://test.cdlhzz.cn:888/mall/recommend-delete-batch';
    $http.post(url,{
      'ids':$scope.delete_batch_num.join(',')
    },config).then(function (response) {
      console.log(response)
      let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':2
        }
      }).then(function (res) {
        console.log("后台推荐首页");
        console.log(res);
        $scope.shop_rep = res.data.data.recommend_admin_index.details;
      },function (err) {
        console.log(err);
      });
    },function (error) {
      console.log(error)
    })
  };

  //批量停用按钮
  $scope.disable_batch=function () {
    $scope.disable_batch_num=[];
    for(let [key,value] of $scope.shop_rep.entries()){
      if(value.state && value.status=="启用"){
        $scope.disable_batch_num.push(value.id)
      }
    }

  };
  //批量停用确认按钮
  $scope.disable_batch_btn=function () {
    let url='http://test.cdlhzz.cn:888/mall/recommend-disable-batch';
    $http.post(url,{
      'ids':$scope.disable_batch_num.join(',')
    },config).then(function (response) {
      console.log("批量禁用返回");
      console.log(response);
      let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':2
        }
      }).then(function (res) {
        $scope.shop_rep = res.data.data.recommend_admin_index.details;
      },function (err) {
        console.log(err);
      });
    },function (error) {
      console.log(error)
    })
  };
  /**
   *--------------------------------------
   *   商家添加
   * -------------------------------------
   */

//商铺添加--获取按钮
  $scope.shop_rep=[]; //推荐--添加--商铺添加
  $scope.link_rep=[]; //推荐--添加--链接添加
  $scope.shop_check=0;//推荐--添加--商铺添加(是否启用) 默认为0 停用
  $scope.link_check=0;//推荐--添加--链接添加(是否启用) 默认为0 停用
  $scope.shop_edit_check=0;//推荐--添加--商铺编辑(是否启用) 默认为0 停用
  // $scope.shop_edit_check=0;//推荐--添加--商铺编辑(是否启用) 默认为0 停用
  $scope.recommend_shop_add_get=function () {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:$scope.shop_model
      }
    }).then(function (res) {
      $scope.recommend_shop_url=res.data.data.detail.url; //商品链接
      $scope.recommend_shop_title=res.data.data.detail.title; //商品标题
      $scope.recommend_shop_subtitle=res.data.data.detail.subtitle; //商品副标题
      $scope.recommend_shop_platform_price=res.data.data.detail.platform_price; //平台价格
    },function (err) {
      console.log(err);
    });
  };

  //商铺添加--确认按钮
  $scope.recommend_shop_add_btn=function () {
      let url= 'http://test.cdlhzz.cn:888/mall/recommend-add';
      let params= {
        district_code:510100,
        url:$scope.recommend_shop_url,
        title:$scope.recommend_shop_title,
        image:"123.jgp",
        from_type:"1",
        type:2,
        sku:$scope.shop_model,
        description:"asdasdas",
        platform_price:$scope.recommend_shop_platform_price,
        status:$scope.shop_check
      };
      $http.post(url,params,config).then(function (response) {
        console.log("添加确定");
        console.log(response);
        $http.get(recommend_url,{
          params:{
            'district_code':510100,
            'type':2
          }
        }).then(function (res) {
          $scope.shop_rep = res.data.data.recommend_admin_index.details;
        },function (err) {
          console.log(err);
        });
      },function (error) {
        console.log(error)
      })
  };

  /**
   *--------------------------
   *     商家编辑
   * -------------------------
   */

  //商家点击编辑--按钮
  $scope.shop_edit_item=function (item) {
    $scope.edit_item=item;
    console.log("点击编辑获取");
    console.log($scope.edit_item);
    //判断是否启用的选取状态
    if($scope.edit_item.status=='停用'){
      $scope.shop_edit_check=0;
    }else{
      $scope.shop_edit_check=1;
    }
    //-------商铺编辑--------
    if($scope.edit_item.from_type=='商铺'){
      $scope.shop_edit_sku=$scope.edit_item.sku;//编号
      $scope.recommend_shop_edit_title=$scope.edit_item.title;//标题
      $scope.recommend_shop_edit_url=$scope.edit_item.url; //商品链接
      $scope.recommend_shop_edit_platform_price=$scope.edit_item.platform_price; //平台价格
      $http({
        method:"GET",
        url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
        params:{
          sku:+$scope.edit_item.sku
        }
      }).then(function (res) {
        $scope.recommend_shop_edit_subtitle=res.data.data.detail.subtitle;//副标题
      },function (err) {
        console.log(err);
      });
    }
    //-----链接编辑------
    if($scope.edit_item.from_type=='链接'){
      $scope.link_edit_url=$scope.edit_item.url; //商品链接
      $scope.link_edit_title=$scope.edit_item.title;//标题
      // $scope.recommend_shop_edit_model=item.sku;
      $scope.link_edit_price=$scope.edit_item.show_price; //平台价格
      $http({
        method:"GET",
        url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
        params:{
          sku:$scope.edit_item.sku
        }
      }).then(function (res) {
        console.log("链接返回");
        console.log(res);
        $scope.link_edit_subtitle=res.data.data.detail.subtitle;//副标题
      },function (err) {
        console.log(err);
      });
    }

  };

  //商家编辑 - 获取按钮
  $scope.recommend_shop_edit_get=function () {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:+$scope.edit_item.sku
      }
    }).then(function (res) {
      $scope.recommend_shop_edit_url=res.data.data.detail.url; //商品链接
      $scope.recommend_shop_edit_title=res.data.data.detail.title; //商品标题
      $scope.recommend_shop_edit_subtitle=res.data.data.detail.subtitle; //商品副标题
      $scope.recommend_shop_edit_platform_price=res.data.data.detail.platform_price; //平台价格
    },function (err) {
      console.log(err);
    });
  };

 //编辑确认按钮
  $scope.recommend_shop_edit=function () {
    if($scope.edit_item.from_type=='商铺'){
      let shop_url='http://test.cdlhzz.cn:888/mall/recommend-edit';
      $http.post(shop_url,{
        id:$scope.edit_item.id,
        url:$scope.edit_item.url,
        title:$scope.recommend_shop_edit_title,
        image:"123.jgp",
        from_type:"1",
        status:$scope.shop_edit_check,
        type:2,
        sku:$scope.shop_edit_sku,
        description:"aaaaaa",
        platform_price:$scope.edit_item.platform_price
      },config).then(function (res) {
        console.log("编辑返回");
        console.log(res);
        let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
        $http.get(recommend_url,{
          params:{
            'district_code':510100,
            'type':2
          }
        }).then(function (res) {
          // console.log(res);
          $scope.shop_rep = res.data.data.recommend_admin_index.details;
        },function (err) {
          console.log(err);
        });
      },function (err) {
        console.log(err);
      })
    }
    if($scope.edit_item.from_type=='链接'){
      console.log("进入链接");
      console.log($scope.edit_item);
      let shop_url='http://test.cdlhzz.cn:888/mall/recommend-edit';
      $http.post(shop_url,{
        id:$scope.edit_item.id,
        url:$scope.edit_item.url,
        title:$scope.link_edit_title,
        image:"123.jgp",
        from_type:"2",
        status:$scope.shop_edit_check,
        type:2,
        sku:$scope.shop_edit_sku,
        description:"aaaaaa",
        platform_price:$scope.edit_item.platform_price
      },config).then(function (res) {
        console.log("编辑返回");
        console.log(res);
        let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
        $http.get(recommend_url,{
          params:{
            'district_code':510100,
            'type':2
          }
        }).then(function (res) {
          $scope.shop_rep = res.data.data.recommend_admin_index.details;
        },function (err) {
          console.log(err);
        });
      },function (err) {
        console.log(err);
      })
    }
  };

/**
 *--------------------------
 * 商家详情
 * -------------------------
*/

  $scope.shop_details=function (item) {
    $scope.shop_datails=item;
    $scope.shop_details_title=$scope.shop_datails.title;
    $scope.shop_details_types=$scope.shop_datails.from_type;
    $scope.shop_details_sku=$scope.shop_datails.sku;
    $scope.shop_details_time=$scope.shop_datails.create_time;
    $scope.shop_details_status=$scope.shop_datails.status;
    $scope.shop_details_platform_price=$scope.shop_datails.platform_price;
    $scope.shop_details_supplier_name=$scope.shop_datails.supplier_name;
    $scope.shop_details_supplier_price=$scope.shop_datails.supplier_price;
    $scope.shop_details_market_price=$scope.shop_datails.market_price;
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:$scope.shop_datails.sku
      }
    }).then(function (res) {
      $scope.shop_details_subtitle=res.data.data.detail.subtitle;
    },function (err) {
      console.log(err);
    });
    //链接
$scope.link_details_title=$scope.shop_datails.title;
$scope.link_details_from_type=$scope.shop_datails.from_type;
$scope.link_details_show_price=$scope.shop_datails.show_price;
$scope.link_details_supplier_name=$scope.shop_datails.supplier_name;
$scope.link_details_create_time=$scope.shop_datails.create_time;
$scope.link_details_status=$scope.shop_datails.status;
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:$scope.shop_datails.sku
      }
    }).then(function (res) {
      $scope.link_details_subtitle=res.data.data.detail.subtitle;
    },function (err) {
      console.log(err);
    });
  } ;

  /**
   *--------------------------
   *商家删除
   * -------------------------
   */
  //商家单个删除
  //点击删除按钮
  $scope.shop_del=function(item){
    $scope.del_ok_index=item;
    console.log("点击删除按钮");
    console.log(item);
  };
  //确认删除
  $scope.shop_del_ok=function () {
    $http.post('http://test.cdlhzz.cn:888/mall/recommend-delete',{'id':$scope.del_ok_index.id},config)
      .then(function (response) {
        $http.get(recommend_url,{
          params:{
            'district_code':510100,
            'type':2
          }
        }).then(function (res) {
          $scope.shop_rep = res.data.data.recommend_admin_index.details;
          console.log(res);
        },function (err) {
          console.log(err);
        });
        console.log(response)
      },function(error){
        console.log(error)
      })
  };


  /**
   *-----------------------------------
   *  链接添加
   * ----------------------------------
   */

  //链接添加--确认按钮
  $scope.recommend_link_add_btn=function () {
    let url= 'http://test.cdlhzz.cn:888/mall/recommend-add';
    let params= {
      district_code:510100,
      url:$scope.recommend_link_url,
      title:$scope.recommend_link_title,
      image:"123.jgp",
      from_type:"2",
      status:$scope.link_check,
      type:2,
      description:"描述",
      platform_price:$scope.recommend_link_show_price
    };
    $http.post(url,params,config).then(function (response) {
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':2
        }
      }).then(function (res) {
        $scope.shop_rep = res.data.data.recommend_admin_index.details;
      },function (err) {
        console.log(err);
      });
    },function (error) {
      console.log(error)
    })
  };

  //单个停用
  $scope.stop_use=function (item) {
    $scope.stop_use_item=item;
    let url='http://test.cdlhzz.cn:888/mall/recommend-status-toggle';
    $http.post(url,{
      id:$scope.stop_use_item.id
    },config).then(function (res) {
      let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':2
        }
      }).then(function (res) {
        // console.log(res);
        $scope.shop_rep = res.data.data.recommend_admin_index.details;
      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log(err);
    })
  };
  //单个启用
  $scope.start_use=function (item) {
    $scope.stop_use_item=item;
    let url='http://test.cdlhzz.cn:888/mall/recommend-status-toggle';
    $http.post(url,{
      id:$scope.stop_use_item.id
    },config).then(function (res) {
      let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':2
        }
      }).then(function (res) {
        // console.log(res);
        $scope.shop_rep = res.data.data.recommend_admin_index.details;
      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log(err);
    })
  };
});






















  //定义了城市的二维数组，里面的顺序跟省份的顺序是相同的。通过selectedIndex获得省份的下标值来得到相应的城市数组
  var city=[
    ["成都市","绵阳市","内江市"],
    ["江苏1","江苏2","江苏3"]
  ];

  function getCity(){
    //获得省份下拉框的对象
    var sltProvince=document.form1.province;
    //获得城市下拉框的对象
    var sltCity=document.form1.city;
    //得到对应省份的城市数组
    var provinceCity=city[sltProvince.selectedIndex - 1];
    //清空城市下拉框，仅留提示选项
    sltCity.length=1;
    //将城市数组中的值填充到城市下拉框中
    for(var i=0;i<provinceCity.length;i++){
      sltCity[i+1]=new Option(provinceCity[i],provinceCity[i]);
    }
  }

//点击切换
  function tabChangeFn(my) {
    var myTab=document.getElementsByClassName("myTab")[0];
    var tabChange=document.getElementsByClassName("tabChange");
    for(var i=0;i<myTab.children.length;i++){
      myTab.children[i].style.color="#666F80";
      myTab.children[i].style.borderBottom="1px solid white";
    }
    my.style.color="#3F94F6";
    my.style.borderBottom="1px solid #3F94F6";
  }



