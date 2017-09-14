
var banner_recommend = angular.module("banner_recommend_module",[]);
banner_recommend.controller("banner_recommend_ctrl",function ($scope,$http) {

  //选择城市开始
  $scope.second_title='';//二级列表项初始化
  $scope.three_title='';//三级列表项初始化
  $scope.ctrlScope = $scope;
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

  //选项卡切换
  $scope.tab_banner_flag=true;
  $scope.tab_recommend_flag=false;
  $scope.tab_banner=function () {
    $scope.tab_banner_flag=true;
    $scope.tab_recommend_flag=false;
  };
  $scope.tab_recommend=function () {
    $scope.tab_banner_flag=false;
    $scope.tab_recommend_flag=true;
  };

<<<<<<< Updated upstream
  //商家上传图片
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
      if(!response.data.data){
        $scope.img_flag="上传图片格式不正确，请重新上传"
      }else{
        $scope.img_flag='';
        $scope.upload_img_src=response.data.data.file_path;
      }
    },function (error) {
      console.log(error)
    })
  };
  //链接上传图片
  $scope.upload_link_img_src='';
  $scope.data = {
    file:null
  };
  $scope.upload_link = function (file) {
    if(!$scope.data.file){
      return
    }
    console.log($scope.data);
    Upload.upload({
      url:'http://test.cdlhzz.cn:888/site/upload',
      data:{'UploadForm[file]':file}
    }).then(function (response) {
      console.log(response);
      if(!response.data.data){
        $scope.img_link_flag="上传图片格式不正确，请重新上传"
      }else{
        $scope.img_link_flag='';
        $scope.upload_link_img_src=response.data.data.file_path;
      }
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
=======
>>>>>>> Stashed changes
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };

  //后台推荐首页(admin)
  let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
<<<<<<< Updated upstream
  $http.get(recommend_url,{
    params:{
      'district_code':510100,
      'type':0
    }
  }).then(function (res) {
    $scope.shop_rep = res.data.data.recommend_admin_index.details;
=======
  let recommend_params={
    district_code:510100,
    type:0
  };
  $http.get(recommend_url,recommend_params).then(function (res) {
    console.log("后台推荐首页");
    console.log(res);
>>>>>>> Stashed changes
  },function (err) {
    console.log(err);
  });

<<<<<<< Updated upstream
  //批量删除按钮
  $scope.delete_batch_del=function () {
    $scope.delete_batch_num=[];
    for(let [key,value] of $scope.shop_rep.entries()){
      if(JSON.stringify($scope.shop_rep).indexOf('"state":true')===-1){  //提示请勾选再删除
        $scope.del_or_stop='my_modal_check';
      }
      if(value.state && value.status=="启用"){  //提示 请停用再删除
        $scope.del_or_stop='my_modal_del_stop';
        break;
      }
      if(value.state && value.status=="停用"){  //直接删除
        $scope.del_or_stop='my_modal_del';
        $scope.delete_batch_num.push(value.id);
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
          'type':0
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
      if(JSON.stringify($scope.shop_rep).indexOf('"state":true')===-1){  //提示 请勾选再操作
        $scope.stop_flag='my_modal_check';
      }
      if(value.state && value.status=="启用"){  //提示 请启用再停用
        $scope.stop_flag='my_modal_stop'
        $scope.disable_batch_num.push(value.id);
      }
      if(value.state && value.status=="停用"){
        $scope.stop_flag='my_modal_stop_begin';
        break;
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
          'type':0
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
=======
>>>>>>> Stashed changes
  /**
   *--------------------------------------
   *   商家添加
   * -------------------------------------
   */

<<<<<<< Updated upstream
  //清楚链接link_submit
  $scope.clear_link_submit=function () {
    $scope.link_submitted=false;
    $scope.img_link_flag='';
    $scope.upload_link_img_src='';//图片
  };
  //清楚商家submit
  $scope.clear_shop_submit=function () {
    $scope.submitted=false;
    $scope.img_flag='';
    $scope.upload_img_src='';//图片
  };

//商家添加--获取按钮
  $scope.shop_rep=[]; //推荐--添加--商家添加
=======
//商铺添加--获取按钮
  $scope.shop_rep=[]; //推荐--添加--商铺添加
>>>>>>> Stashed changes
  $scope.link_rep=[]; //推荐--添加--链接添加
  $scope.shop_check=0;//推荐--添加--商铺添加(是否启用) 默认为0 停用
  $scope.link_check=0;//推荐--添加--链接添加(是否启用) 默认为0 停用

  $scope.recommend_shop_add_get=function () {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:$scope.shop_model
      }
    }).then(function (res) {
      console.log(res);
      $scope.recommend_shop_url=res.data.data.detail.url; //商品链接
      $scope.recommend_shop_title=res.data.data.detail.title; //商品标题
      $scope.recommend_shop_subtitle=res.data.data.detail.subtitle; //商品副标题
      $scope.recommend_shop_platform_price=res.data.data.detail.platform_price; //平台价格
    },function (err) {
      console.log(err);
    });
  };

<<<<<<< Updated upstream
  //商家添加--确认按钮
  $scope.recommend_shop_add_btn=function (valid) {
    if(valid&&$scope.upload_img_src){
      $scope.variable_add_modal='modal';
      let url= 'http://test.cdlhzz.cn:888/mall/recommend-add';
      let params= {
        district_code:510100,
        url:$scope.recommend_shop_url,
        title:$scope.recommend_shop_title,
        image:$scope.upload_img_src,
        from_type:"1",
        status:$scope.shop_check,
        type:0,
        sku:$scope.shop_model
      };
      $http.post(url,params,config).then(function (response) {
        console.log("添加确定");
        console.log(response);
        $http.get(recommend_url,{
          params:{
            'district_code':510100,
            'type':0
          }
        }).then(function (res) {
          $scope.shop_rep = res.data.data.recommend_admin_index.details;
        },function (err) {
          console.log(err);
        });
      },function (error) {
        console.log(error)
      });
    }else{
      $scope.submitted = true;
    }
    if(!$scope.upload_img_src){
      $scope.img_flag='请上传图片';
      $scope.variable_add_modal='';
    }
  };

  /**
   *-----------------------------------
   *  链接添加
   * ----------------------------------
   */

  //链接添加--确认按钮
  $scope.recommend_link_add_btn=function (valid) {
    if(valid&&$scope.upload_link_img_src){
      $scope.variable_link_add_modal='modal';
      let url= 'http://test.cdlhzz.cn:888/mall/recommend-add';
      let params= {
        district_code:510100,
        url:$scope.recommend_link_url,
        title:$scope.recommend_link_title,
        image:$scope.upload_link_img_src,
        from_type:"2",
        status:$scope.link_check,
        type:0
      };
      $http.post(url,params,config).then(function (response) {
        $http.get(recommend_url,{
          params:{
            'district_code':510100,
            'type':0
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
    }else{
      $scope.link_submitted = true;
    }
    if(!$scope.upload_link_img_src){
      $scope.img_link_flag='请上传图片';
      $scope.variable_link_add_modal='';
    }
=======
  //商铺添加--确认按钮
  $scope.recommend_shop_add_btn=function () {
    let url= 'http://test.cdlhzz.cn:888/mall/recommend-add';
    let params= {
      district_code:510100,
      url:$scope.recommend_shop_url,
      title:$scope.recommend_shop_title,
      image:"123.jgp",
      from_type:"1",
      type:0,
      sku:$scope.shop_model,
      description:"asdasdas",
      platform_price:$scope.recommend_shop_platform_price,
      status:$scope.shop_check
    };
    $http.post(url,params,config).then(function (response) {
      let myDate=new Date(); //假设的秒数
      let now_time=myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes();
      $scope.shop_rep.push({
        'title':$scope.recommend_shop_title,
        'check':$scope.shop_check==0?'已启用':"停用",
        'shop_types':'商家',
        'sku':$scope.shop_model,
        'now_time':now_time,
        'subtitle':$scope.recommend_shop_subtitle,
        'platform_price':$scope.recommend_shop_platform_price
      });
      console.log(response)
    },function (error) {
      console.log(error)
    })
>>>>>>> Stashed changes
  };

  /**
   *--------------------------
   *     商家编辑
   * -------------------------
   */

<<<<<<< Updated upstream
  //商家点击编辑--按钮
  $scope.shop_edit_item=function (item) {
    $scope.edit_item=item;
    console.log("点击编辑获取");
    console.log($scope.edit_item);
    $scope.variable_modal="";//默认modal为空
    $scope.img_flag='';//清空 “格式不正确”
    //判断是否启用的选取状态
    if($scope.edit_item.status=='停用'){
      $scope.shop_edit_check=0;
    }else{
      $scope.shop_edit_check=1;
    }
    //-------商家编辑--------
    if($scope.edit_item.from_type=='商家'){
      $scope.upload_img_src='';
      $scope.shop_edit_sku=$scope.edit_item.sku;//编号
      $scope.recommend_shop_edit_title=$scope.edit_item.title;//标题
      $scope.recommend_shop_edit_url=$scope.edit_item.url; //商品链接
      $scope.recommend_shop_edit_img=$scope.edit_item.image;//图片
    }
    //-----链接编辑------
    if($scope.edit_item.from_type=='链接'){
      $scope.upload_link_img_src='';
      $scope.link_edit_url=$scope.edit_item.url; //商品链接
      $scope.link_edit_title=$scope.edit_item.title;//标题
      $scope.link_edit_subtitle=$scope.edit_item.description;//副标题
      $scope.link_edit_price=$scope.edit_item.show_price; //平台价格
      $scope.link_edit_img=$scope.edit_item.image;//图片
    }
  };
=======
>>>>>>> Stashed changes

  //商家编辑获取按钮
  $scope.shop_edit_check=0;
  $scope.recommend_shop_edit_get=function () {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:$scope.shop_edit_model
      }
    }).then(function (res) {
      console.log("商家编辑获取按钮");
      console.log(res);
      $scope.recommend_shop_edit_url=res.data.data.detail.url; //商品链接
      $scope.recommend_shop_edit_title=res.data.data.detail.title; //商品标题
      $scope.recommend_shop_edit_subtitle=res.data.data.detail.subtitle; //商品副标题
      $scope.recommend_shop_edit_platform_price=res.data.data.detail.platform_price; //平台价格
    },function (err) {
      console.log(err);
    });
  };

  //商家点击编辑--按钮
  $scope.shop_edit_index=function (index) {
    $scope.edit_index=index;
  };
  //编辑确认按钮
<<<<<<< Updated upstream
  $scope.recommend_shop_edit=function (valid) {
    if(valid){
      console.log(123)
      $scope.variable_modal="modal";
      console.log($scope.variable_modal)
      if($scope.edit_item.from_type=='商家'){
        if($scope.upload_img_src==''){
          $scope.upload_img_src=$scope.recommend_shop_edit_img;
        }
        let shop_url='http://test.cdlhzz.cn:888/mall/recommend-edit';
        $http.post(shop_url,{
          id:+$scope.edit_item.id,
          url:$scope.edit_item.url,
          title:$scope.recommend_shop_edit_title,
          image:$scope.upload_img_src,
          from_type:"1",
          status:+$scope.shop_edit_check,
          type:0,
          sku:+$scope.shop_edit_sku,
        },config).then(function (res) {
          console.log("编辑返回");
          console.log(res);
          let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
          $http.get(recommend_url,{
            params:{
              'district_code':510100,
              'type':0
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
        if($scope.upload_link_img_src==''){
          $scope.upload_link_img_src=$scope.link_edit_img;
        }
        let shop_url='http://test.cdlhzz.cn:888/mall/recommend-edit';
        $http.post(shop_url,{
          id:+$scope.edit_item.id,
          url:$scope.edit_item.url,
          title:$scope.link_edit_title,
          image:$scope.upload_link_img_src,
          from_type:"2",
          status:+$scope.shop_edit_check,
          type:0,
        },config).then(function (res) {
          console.log("编辑返回");
          console.log(res);
          let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
          $http.get(recommend_url,{
            params:{
              'district_code':510100,
              'type':0
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
    }else{
      $scope.submitted = true;
      $scope.variable_modal="";
      // $scope.img_link_flag='请上传图片';
    }
=======
  $scope.recommend_shop_edit=function () {
    let url= 'http://test.cdlhzz.cn:888/mall/recommend-edit';
    let params= {
      id:1,
      url:$scope.recommend_shop_edit_url,
      title:$scope.recommend_shop_edit_title,
      image:"123.jgp",
      from_type:"1",
      status:$scope.shop_edit_check,
      type:0,
      sku:$scope.shop_edit_model,
      platform_price:$scope.recommend_shop_edit_platform_price
    };
    $http.post(url,params,config).then(function (response) {
      let myDate=new Date(); //假设的秒数
      let now_time=myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes();
      $scope.shop_rep.splice($scope.edit_index,1,{
        'title':$scope.recommend_shop_edit_title,
        'check':$scope.shop_edit_check==0?'已启用':"停用",
        'shop_types':'商家',
        'sku':$scope.shop_edit_model,
        'now_time':now_time
      });
      console.log("shop_rep数组为");
      console.log($scope.shop_rep);
    },function (error) {
      console.log(error)
    });
>>>>>>> Stashed changes
  };


  /**
   *--------------------------
   * 商家详情
   * -------------------------
   */

  $scope.shop_details=function (item) {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:item.sku
      }
    }).then(function (res) {
      $scope.shop_details_subtitle=res.data.data.detail.subtitle;
      $scope.shop_details_platform_price=res.data.data.detail.platform_price;
    },function (err) {
      console.log(err);
    });
    $scope.shop_details_title=item.title;
    $scope.shop_details_subtitle='';
    $scope.shop_details_types=item.shop_types;
    $scope.shop_details_sku=item.sku;
    $scope.shop_details_time=item.now_time;
  }

  /**
   *--------------------------
   *商家删除
   * -------------------------
   */
  //商家单个删除
  //点击删除按钮
  $scope.shop_del=function(index){
    $scope.del_ok_index=index;
  };
  //确认删除
  $scope.shop_del_ok=function () {
    $scope.shop_rep.splice($scope.del_ok_index,1);
  };



<<<<<<< Updated upstream

  //单个停用
  $scope.stop_use=function (item) {
    $scope.stop_use_item=item;
    console.log(item)
  };
  //单个停用确认按钮
  $scope.sole_stop_btn=function () {
    let url='http://test.cdlhzz.cn:888/mall/recommend-status-toggle';
    $http.post(url,{
      id:$scope.stop_use_item.id
    },config).then(function (res) {
      let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':0
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
  };
  //单个启用确认按钮
  $scope.sole_begin_btn=function () {
    let url='http://test.cdlhzz.cn:888/mall/recommend-status-toggle';
    $http.post(url,{
      id:$scope.stop_use_item.id
    },config).then(function (res) {
      let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':0
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
      $http.get(recommend_url,{
        params:{
          'district_code':510100,
          'type':0
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
    $scope.submitted = false;
    $scope.img_flag='';//清空 “格式不正确”
    $scope.upload_img_src='';//清空上一次上传图片
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
=======
  //链接添加--确认按钮
  $scope.recommend_link=function () {
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
      platform_price:$scope.recommend_link_platform_price
    };
    $http.post(url,params,config).then(function (response) {
      let myDate=new Date(); //假设的秒数
      let now_time=myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes();
      $scope.link_rep.push({
        'link_link':$scope.recommend_link_url,//链接
        'link_title':$scope.recommend_link_title,//标题
        'link_subtitle':$scope.recommend_link_subtitle,//副标题
        'check':$scope.link_check==0?'已启用':"停用",
        'ling_show_price':$scope.recommend_link_show_price,//显示价格
        'link_types':'链接',
        'now_time':now_time
      });
      console.log(response)
    },function (error) {
      console.log(error)
    })
  };
>>>>>>> Stashed changes
});




