let banner_recommend  = angular.module("banner_recommend_module",['ngFileUpload','ngDraggable']);
banner_recommend.controller("banner_recommend_ctrl",function ($rootScope,$scope,$http,Upload,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: 'APP推荐位-banner管理',
    }];

  $scope.myng=$scope;//原形继承转换，解决ng-model 无效问题
  $scope.shop_rep=[];
  $scope.upload_txt="上传";
  $scope.upload_dis=false;
  //选择城市开始
  //初始化省市区县;
  $http.get('city.json').then(function (response) {
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
    $http.get('city.json').then(function (response) {
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

  //商家上传图片
  $scope.upload_img_src='';
  $scope.data = {
    file:null
  };
  $scope.upload = function (file) {
    if(!$scope.data.file){
      return
    }
    $scope.upload_txt="上传中...";
    $scope.upload_dis=true;
    console.log($scope.data);
    Upload.upload({
      url:'/site/upload',
      data:{'UploadForm[file]':file}
    }).then(function (response) {
      console.log(response);
      if(!response.data.data){
        $scope.img_flag="上传图片格式不正确，请重新上传";
        $scope.upload_txt="上传";
        $scope.upload_dis=false;
      }else{
        $scope.img_flag='';
        $scope.upload_img_src=response.data.data.file_path;
        $scope.upload_txt="上传";
        $scope.upload_dis=false;
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
    $scope.upload_txt="上传中...";
    $scope.upload_dis=true;
    Upload.upload({
      url:'/site/upload',
      data:{'UploadForm[file]':file}
    }).then(function (response) {
      console.log(response);
      if(!response.data.data){
        $scope.img_link_flag="上传图片格式不正确，请重新上传";
        $scope.upload_txt="上传";
        $scope.upload_dis=false;
      }else{
        $scope.img_link_flag='';
        $scope.upload_link_img_src=response.data.data.file_path;
        $scope.upload_txt="上传";
        $scope.upload_dis=false;
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

  //后台推荐首页(admin)
  let banner_list_fn=function () {
      _ajax.get('/mall/recommend-admin-index',{
          'district_code':510100,
          'type':0
      },function (res) {
          console.log(res);
          $scope.shop_rep = res.data.recommend_admin_index.details;
      })
  }
  banner_list_fn();

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
        console.log(value.id)
      }
    }
  };
  //批量删除确认按钮
  $scope.delete_batch_btn=function () {
    _ajax.post('/mall/recommend-delete-batch',{
        'ids':$scope.delete_batch_num.join(',')
    },function (res) {
        banner_list_fn();
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
    }
  };
  //批量停用确认按钮
  $scope.disable_batch_btn=function () {
    _ajax.post('/mall/recommend-disable-batch',{'ids':$scope.disable_batch_num.join(',')},function (res) {
        banner_list_fn();
    })
  };
  /**
   *--------------------------------------
   *   商家添加
   * -------------------------------------
   */

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
  $scope.link_rep=[]; //推荐--添加--链接添加
  $scope.shop_check=0;//推荐--添加--商家添加(是否启用) 默认为0 停用
  $scope.link_check=0;//推荐--添加--链接添加(是否启用) 默认为0 停用
  $scope.shop_edit_check=0;//推荐--添加--商家编辑(是否启用) 默认为0 停用
  $scope.recommend_shop_add_get=function () {
    $http.get('/mall/goods-by-sku',{
      params:{
        sku:$scope.shop_model
      }
    }).then(function (res) {
      console.log(res)
      if(res.data.code==200){
        if(res.data.data.detail.length===0){
          //Banner - sku错误提示
          $scope.banner_num_error=true;
          $scope.recommend_shop_url='';
          $scope.recommend_shop_title='';
          $scope.shop_sku_error='商品编号错误，请重新添加';
        }else{
          $scope.banner_num_error=false;
          $scope.recommend_shop_url=res.data.data.detail.url; //商品链接
          $scope.recommend_shop_title=res.data.data.detail.title; //商品标题
        }
      }else if(res.data.code==1000){
        $scope.banner_num_error=true;
        $scope.recommend_shop_url='';
        $scope.recommend_shop_title='';
        $scope.shop_sku_error='商品编号错误，请重新添加';
      }else if(res.data.code==403){
        window.location.href='login.html';
      }
    })
  };
  $scope.shop_sku_error='商品编号不能为空'
  //商家添加--确认按钮
  $scope.recommend_shop_add_btn=function (valid) {
    if(valid && $scope.upload_img_src && !$scope.img_flag){
      _ajax.post('/mall/recommend-add',{
          district_code:510100,
          url:$scope.recommend_shop_url,
          title:$scope.recommend_shop_title,
          image:$scope.upload_img_src,
          from_type:"1",
          status:$scope.shop_check,
          type:0,
          sku:$scope.shop_model
      },function (res) {
        console.log(res);
        if (res.code==200) {
          $('#recommend_shop_modal_add').modal('hide')
          banner_list_fn();
        }else{
          $scope.banner_num_error=true;
          $scope.recommend_shop_url='';
          $scope.recommend_shop_title='';
          $scope.shop_sku_error='商品编号错误，请重新添加';
        }
      })
    }else{
      $scope.submitted = true;
    }
    if(!$scope.upload_img_src){
      $scope.img_flag='请上传图片';
    }
  };

  /**
   *-----------------------------------
   *  链接添加
   * ----------------------------------
   */

  //链接添加--确认按钮
  $scope.recommend_link_add_btn=function (valid) {
    if($scope.recommend_link_url.indexOf('http://')){
      $scope.recommend_link_url='http://'+$scope.recommend_link_url
    }
    if(valid&&$scope.upload_link_img_src&&!$scope.img_link_flag){
      $scope.variable_link_add_modal='modal';
      _ajax.post('/mall/recommend-add',{
          district_code:510100,
          url:$scope.recommend_link_url,
          title:$scope.recommend_link_title,
          image:$scope.upload_link_img_src,
          from_type:"2",
          status:$scope.link_check,
          type:0
      },function (res) {
          banner_list_fn();
      })
    }else{
      $scope.link_submitted = true;
    }
    if(!$scope.upload_link_img_src){
      $scope.img_link_flag='请上传图片';
      $scope.variable_link_add_modal='';
    }
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
    if(item.goods_status==2 ||item.goods_status==undefined){
      $scope.recommend_shop_modal_edit='#recommend_shop_modal_edit';
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
    }else{
      $scope.recommend_shop_modal_edit='#please_up_shops';
    }
  };

  //商家编辑 - 获取按钮
  $scope.recommend_shop_edit_get=function () {
    $http.get('/mall/goods-by-sku',{
      params:{
        sku:+$scope.shop_edit_sku
      }
    }).then(function (res) {
      if(res.data.code==200){
        if(res.data.data.detail.length===0){
          // sku错误提示
          $scope.banner_num_error=true;
          $scope.recommend_shop_edit_url='';
          $scope.recommend_shop_edit_title='';
          $scope.shop_sku_error='商品编号错误，请重新添加';
        }else{
          $scope.banner_num_error=false;
          $scope.recommend_shop_edit_url=res.data.data.detail.url; //商品链接
          $scope.recommend_shop_edit_title=res.data.data.detail.title; //商品标题
        }
      }else if(res.data.code==1000){
        $scope.banner_num_error=true;
        $scope.recommend_shop_edit_url='';
        $scope.recommend_shop_edit_title='';
        $scope.shop_sku_error='商品编号错误，请重新添加';
      }else if(res.data.code==403){
        window.location.href='login.html';
      }
    })
  };
  //编辑确认按钮
  $scope.recommend_shop_edit=function (valid) {
    console.log($scope.edit_item)
    if(valid && !$scope.img_flag && !$scope.img_link_flag){
      if($scope.edit_item.from_type=='商家'){
        if($scope.upload_img_src==''){
          $scope.upload_img_src=$scope.recommend_shop_edit_img;
        }
        _ajax.post('/mall/recommend-edit',{
            id:+$scope.edit_item.id,
            url:$scope.recommend_shop_edit_url,
            title:$scope.recommend_shop_edit_title,
            image:$scope.upload_img_src,
            from_type:"1",
            status:+$scope.shop_edit_check,
            type:0,
            sku:+$scope.shop_edit_sku
        },function (res) {
          if (res.code==200) {
            $('#recommend_shop_modal_edit').modal('hide')
            banner_list_fn();
          }else{
            $scope.banner_num_error=true;
            $scope.recommend_shop_edit_url='';
            $scope.recommend_shop_edit_title='';
            $scope.shop_sku_error='商品编号错误，请重新添加';
          }
        })
      }
      if($scope.edit_item.from_type=='链接'){
        if($scope.upload_link_img_src==''){
          $scope.upload_link_img_src=$scope.link_edit_img;
        }
        if($scope.link_edit_url.indexOf('http://')){
          $scope.link_edit_url='http://'+$scope.link_edit_url
        }
        _ajax.post('/mall/recommend-edit',{
            id:+$scope.edit_item.id,
            url:$scope.link_edit_url,
            title:$scope.link_edit_title,
            image:$scope.upload_link_img_src,
            from_type:"2",
            status:+$scope.shop_edit_check,
            type:0
        },function (res) {
            banner_list_fn();
        })
      }
    }else{
      $scope.submitted = true;
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
      $scope.shop_details_viewed_left=$scope.shop_datails.left_number;//库存
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
  };

  /**
   *--------------------------
   *商家删除
   * -------------------------
   */
  //商家单个删除
  //点击删除按钮
  $scope.shop_del=function(item){
    $scope.del_ok_index=item;
    console.log(item);
  };
  //确认删除
  $scope.shop_del_ok=function () {
      _ajax.post('/mall/recommend-delete',{'id':$scope.del_ok_index.id},function (res) {
          banner_list_fn();
      })
  };




  //单个停用
  $scope.stop_use=function (item) {
    $scope.stop_use_item=item;
    console.log(item)
  };
  //单个停用确认按钮
  $scope.sole_stop_btn=function () {
    _ajax.post('/mall/recommend-status-toggle',{id:$scope.stop_use_item.id},function (res) {
        banner_list_fn();
    })
  };
  //单个启用
  $scope.start_use=function (item) {
    console.log(item);
    if(item.goods_status===2||item.goods_status==undefined){
        $scope.stop_use_item=item;
        $scope.solo_begin_modal='#solo_begin_modal'
    }else{
        $scope.solo_begin_modal='#please_up_shops';
    }

  };
  //单个启用确认按钮
  $scope.sole_begin_btn=function () {
    _ajax.post('/mall/recommend-status-toggle',{id:$scope.stop_use_item.id},function (res) {
        banner_list_fn();
    })
  };
  $scope.show_all = function (m) {
      m === true ? $scope[m] = false : $scope[m] = true;
  };

  // 分类菜单
  $scope.show_1=true;
  $scope.show_2=true;
  $scope.show_3=true;
  $scope.show_4=true;
  $scope.show_5=true;
  $scope.show_6=true;
  $scope.show_7=true;
  $scope.show_8=true
  $scope.show_9=true;
  $scope.show_10=false;
  $scope.show_11=false;
  $scope.show_12=false;
  $scope.show_13=false;
  $scope.show_14=true;

  //初始化---添加文本框值
  $scope.clear_add_content=function () {
    $scope.submitted = false;
    $scope.img_flag='';
    $scope.upload_img_src='';//图片
    //链接
    $scope.link_submitted=false;
    $scope.img_link_flag='';
    $scope.upload_link_img_src='';//图片
    $scope.num_blur=false;
    $scope.shop_title_blur=false;
    $scope.link_title_blur=false;
    $scope.link_link_blur=false;
    $scope.img_flag='';//清空 “格式不正确”
    $scope.upload_img_src='';//清空上一次上传图片
    //商家
    $scope.shop_model='';
    $scope.recommend_shop_url='';
    $scope.recommend_shop_title='';
    $scope.recommend_shop_subtitle='';
    $scope.recommend_shop_platform_price='';
    $scope.shop_sku_error='商品编号不能为空';
    $scope.banner_num_error=false;
    //链接
    $scope.recommend_link_url="http://";
    $scope.recommend_link_title='';
    $scope.recommend_link_subtitle='';
    $scope.recommend_link_show_price='';
  };
  //拖拽排序
  $scope.dropComplete = function(index,obj){
    let idx = $scope.shop_rep.indexOf(obj);
    $scope.shop_rep[idx] = $scope.shop_rep[index];
    $scope.shop_rep[index] = obj
  };
  $scope.sort_order=[];
  $scope.save_btn_ok=function () {
    $scope.sort_order=[];
    for(let[key,value] of $scope.shop_rep.entries()){
      $scope.sort_order.push(value.id);
    }
  };
  $scope.banner_save_confirm=function () {
    _ajax.post('/mall/recommend-sort',{ids:$scope.sort_order.join(',')},function (res) {
        console.log(res);
    })
  }
  /*图片放大显示*/
  $scope.showImgs = (src) => {
    console.log(src)
    $scope.showImg = src;
  }
});