var index_recommend = angular.module("index_recommend_module",[]);
index_recommend.controller("index_recommend_ctrl",function ($scope,$http,Upload) {
  $scope.myng=$scope;//原形继承转换，解决ng-model 无效问题
  //选择城市开始
  // $scope.second_title='';//二级列表项初始化
  // $scope.three_title='';//三级列表项初始化
  // // $scope.ctrlScope = $scope;
  // $scope.search_txt = '';
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

  //选项卡切换
  $scope.tab_banner_flag=false;
  $scope.tab_recommend_flag=true;
  $scope.tab_banner=function () {
    $scope.tab_banner_flag=true;
    $scope.tab_recommend_flag=false;
  };
  $scope.tab_recommend=function () {
    $scope.tab_banner_flag=false;
    $scope.tab_recommend_flag=true;
  };

  //上传图片
  $scope.upload_img_src='';
  $scope.data = {
    file:null
  };
  $scope.upload = function (file) {
    if(!$scope.data.file){
      return
    }
    console.log($scope.data);
    Upload.upload({
      url:'http://test.cdlhzz.cn:888/site/upload',
      data:{'UploadForm[file]':file}
    }).then(function (response) {
      console.log(response);
      $scope.upload_img_src=response.data.data.file_path;
    },function (error) {
      console.log(error)
    })
  };



//全选按钮
  $scope.delete_batch_num=[];//初始化删除数目数组
  $scope.disable_batch_num=[];//初始化停用数目数组
  $scope.selectAll=false;
  $scope.all= function (m) {
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
    console.log(res);
    $scope.shop_rep = res.data.data.recommend_admin_index.details;
    console.log($scope.shop_rep);
  },function (err) {
    console.log(err);
  });

  //批量删除按钮
  $scope.delete_batch_del=function () {
    $scope.delete_batch_num=[];
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

//商家添加--获取按钮
  $scope.shop_rep=[]; //推荐--添加--商家添加
  $scope.link_rep=[]; //推荐--添加--链接添加
  $scope.shop_check=0;//推荐--添加--商家添加(是否启用) 默认为0 停用
  $scope.link_check=0;//推荐--添加--链接添加(是否启用) 默认为0 停用
  $scope.shop_edit_check=0;//推荐--添加--商家编辑(是否启用) 默认为0 停用
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


  let shop_add_num_value=document.getElementById("shop_add_num_input").value.trim();//商品编号输入框的值
  let shop_add_num_input=document.getElementById("shop_add_num_input");//商品编号输入框
  let shop_num_null=document.getElementById("shop_num_null");//提示不能为空
  let recommend_shop_modal_add=document.getElementById("recommend_shop_modal_add");

  //商家添加--确认按钮
  shop_add_num_input.onblur=function (event) {
    // if (event.relatedTarget==recommend_shop_modal_add)
    // {
      let shop_add_num_input2=document.getElementById("shop_add_num_input");
      if(shop_add_num_input2.value.length>0){
        shop_num_null.style.display="none";
      }
    // }
  };

  $scope.recommend_shop_add_btn=function () {
    let shop_add_num_value=document.getElementById("shop_add_num_input").value.trim();
    if(shop_add_num_value.length==0){
      shop_num_null.style.display="block";
    }else{
      let shop_ok_btn=document.getElementById("recommend_shop_add_btn");
      shop_ok_btn.setAttribute("data-dismiss","modal");
    }
    let url= 'http://test.cdlhzz.cn:888/mall/recommend-add';
      let params= {
        district_code:510100,
        url:$scope.recommend_shop_url,
        title:$scope.recommend_shop_title,
        image:$scope.upload_img_src,
        from_type:"1",
        type:2,
        sku:$scope.shop_model,
        description:$scope.recommend_shop_subtitle,
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
    //-------商家编辑--------
    if($scope.edit_item.from_type=='商家'){
      $scope.shop_edit_sku=$scope.edit_item.sku;//编号
      $scope.recommend_shop_edit_title=$scope.edit_item.title;//标题
      $scope.recommend_shop_edit_url=$scope.edit_item.url; //商品链接
      $scope.recommend_shop_edit_platform_price=$scope.edit_item.platform_price; //平台价格
      $scope.recommend_shop_edit_subtitle=$scope.edit_item.description;//副标题
      $scope.recommend_shop_edit_img=$scope.edit_item.image;//图片
    }
    //-----链接编辑------
    if($scope.edit_item.from_type=='链接'){
      $scope.link_edit_url=$scope.edit_item.url; //商品链接
      $scope.link_edit_title=$scope.edit_item.title;//标题
      $scope.link_edit_subtitle=$scope.edit_item.description;//副标题
      $scope.link_edit_price=$scope.edit_item.show_price; //平台价格
      $scope.link_edit_img=$scope.edit_item.image;//图片
    }
  };

  //商家编辑 - 获取按钮
  $scope.recommend_shop_edit_get=function () {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:+$scope.shop_edit_sku
      }
    }).then(function (res) {
      console.log("商家获取");
      console.log(res);
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
    if($scope.edit_item.from_type=='商家'){
      let shop_url='http://test.cdlhzz.cn:888/mall/recommend-edit';
      $http.post(shop_url,{
        id:$scope.edit_item.id,
        url:$scope.edit_item.url,
        title:$scope.recommend_shop_edit_title,
        image:$scope.recommend_shop_edit_img,
        from_type:"1",
        status:$scope.shop_edit_check,
        type:2,
        sku:$scope.shop_edit_sku,
        description:$scope.recommend_shop_edit_subtitle,
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
    if($scope.edit_item.from_type=='链接'){
      console.log("进入链接");
      console.log($scope.edit_item);
      let shop_url='http://test.cdlhzz.cn:888/mall/recommend-edit';
      $http.post(shop_url,{
        id:$scope.edit_item.id,
        url:$scope.edit_item.url,
        title:$scope.link_edit_title,
        image:$scope.link_edit_img,
        from_type:"2",
        status:$scope.shop_edit_check,
        type:2,
        description:$scope.link_edit_subtitle,
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
    console.log($scope.shop_datails);
    if($scope.shop_datails.from_type=='商家'){
      console.log($scope.shop_datails);
      $scope.shop_details_title=$scope.shop_datails.title; //标题
      $scope.shop_details_subtitle=$scope.shop_datails.description;//副标题
      $scope.shop_details_types=$scope.shop_datails.from_type; //类型
      $scope.shop_details_sku=$scope.shop_datails.sku;  //编号
      $scope.shop_details_time=$scope.shop_datails.create_time; //创建时间
      $scope.shop_details_status=$scope.shop_datails.status;  //是否启用
      $scope.shop_details_platform_price=$scope.shop_datails.platform_price;//平台价格
      $scope.shop_details_supplier_name=$scope.shop_datails.supplier_name;//来源商家
      $scope.shop_details_supplier_price=$scope.shop_datails.supplier_price; //供货价格
      $scope.shop_details_market_price=$scope.shop_datails.market_price;  //市场价格
      $scope.shop_details_img=$scope.shop_datails.image;//图片
      $scope.shop_details_viewed_number=$scope.shop_datails.viewed_number;//上架浏览
      $scope.shop_details_sold_number=$scope.shop_datails.sold_number;//上架销量
    }
    //链接
    if($scope.shop_datails.from_type=='链接'){
      $scope.link_details_title=$scope.shop_datails.title;  //标题
      $scope.link_details_subtitle=$scope.shop_datails.description;//副标题
      $scope.link_details_from_type=$scope.shop_datails.from_type; //类型
      $scope.link_details_show_price=$scope.shop_datails.show_price;//显示价格
      $scope.link_details_supplier_name=$scope.shop_datails.supplier_name;//来源商家
      $scope.link_details_create_time=$scope.shop_datails.create_time;//创建时间
      $scope.link_details_status=$scope.shop_datails.status;//是否启用
      $scope.link_details_viewed_number=$scope.shop_datails.viewed_number;//浏览
      $scope.link_details_img=$scope.shop_datails.image;//图片
    }
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
      image:$scope.upload_img_src,
      from_type:"2",
      status:$scope.link_check,
      type:2,
      description:$scope.recommend_link_subtitle,
      platform_price:$scope.recommend_link_show_price
    };
    $http.post(url,params,config).then(function (response) {
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':2
        }
      }).then(function (res) {
        console.log("链接添加");
        console.log(res);
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

  //推荐排序
    $scope.recommend_sort_num=[];
    $scope.recommend_sort=function (item) {
    // $scope.sort=item;
    // console.log('排序');
    console.log($scope.sort);
    let url='http://test.cdlhzz.cn:888/mall/recommend-sort';
    for(let i=0; i<$scope.shop_rep.length;i++){
      $scope.recommend_sort_num.push($scope.shop_rep[i].id)
    }
    $scope.sort_join=$scope.recommend_sort_num.join(',');
    // console.log($scope.sort_join);
    $http.post(url,{
      ids:$scope.sort_join
    },config).then(function (res) {
      // console.log("排序返回");
      // console.log(res);
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':2
        }
      }).then(function (res) {
        console.log(res);
        $scope.shop_rep = res.data.data.recommend_admin_index.details;
      },function (err) {
        console.log(err);
      });
    },function (err) {
      console.log(err);
    })
  };

  // 分类菜单
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
  $scope.show_6=true;
  $scope.show_f= function (m) {
    if(m===true){
      $scope.show_6=true;
    }else {
      $scope.show_6=false;
    }
  };
  $scope.show_7=true;
  $scope.show_g= function (m) {
    if(m===true){
      $scope.show_7=true;
    }else {
      $scope.show_7=false;
    }
  };
  $scope.show_8=true;
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
  $scope.show_10=false;
  $scope.show_j= function (m) {
    if(m===true){
      $scope.show_10=true;
    }else {
      $scope.show_10=false;
    }
  };
  $scope.show_11=false;
  $scope.show_k= function (m) {
    if(m===true){
      $scope.show_11=true;
    }else {
      $scope.show_11=false;
    }
  };
  $scope.show_12=false;
  $scope.show_l= function (m) {
    if(m===true){
      $scope.show_12=true;
    }else {
      $scope.show_12=false;
    }
  };
  $scope.show_13=false;
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
  change_m=false;
  $scope.change_menu=function (m) {
    if(m===true){
      $scope.change_m=false;
    }else {
      $scope.change_m=true;
    }
  };

  //初始化---添加文本框值
  $scope.clear_add_content=function () {
    //商家
    $scope.shop_model='';
    $scope.recommend_shop_url='';
    $scope.recommend_shop_title='';
    $scope.recommend_shop_subtitle='';
    $scope.recommend_shop_platform_price='';
    //链接
    $scope.recommend_link_url='';
    $scope.recommend_link_title='';
    $scope.recommend_link_subtitle='';
    $scope.recommend_link_show_price='';
  }
});




