;
let shop_decoration=angular.module('shop_decoration_module',['ngFileUpload','ngDraggable'])
.controller('shop_decoration_ctrl',function ($scope,$http,$q,Upload) {
  $scope.myng=$scope;
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  $scope.banner_list=[];//Banner
  $scope.recommend_list=[];//推荐
  $scope.delete_arr=[];//删除个数数组
  $scope.b_tab_class='bottom_border';//默认给Banner加class
  $scope.b_show_table_flag=true;
  $scope.r_show_table_flag=false;
  $scope.banner_tab=function () {
     //$scope.show_hide_menu();
    $scope.b_tab_class='bottom_border';
    $scope.r_tab_class='';
    $scope.b_show_table_flag=true;
    $scope.r_show_table_flag=false;
  };
  $scope.recommend_tab=function () {
     //$scope.show_hide_menu();
    $scope.b_tab_class='';
    $scope.r_tab_class='bottom_border';
    $scope.b_show_table_flag=false;
    $scope.r_show_table_flag=true;
  };

  /*判断类型为Banner还是推荐，请求列表接口*/
  $scope.judgment=function () {
    if($scope.b_show_table_flag){
      $http.get(baseUrl+'/mall/recommend-admin-index-supplier',{
        params:{
          district_code:510100,
          type:0
        }
      }).then(function (res) {
        $scope.banner_list=res.data.data.recommend_admin_index_supplier.details;
      },function (err) {
        console.log(err);
      });
    }else{
      $http.get(baseUrl+'/mall/recommend-admin-index-supplier',{
        params:{
          district_code:510100,
          type:2
        }
      }).then(function (res) {
        $scope.recommend_list=res.data.data.recommend_admin_index_supplier.details;
      },function (err) {
        console.log(err);
      });
    }
  };

  $scope.selectAll_modal=false;
  $scope.selectAll= function (m) {
    if($scope.b_show_table_flag) {
      for (let [key, value] of $scope.banner_list.entries()) {
        if (m === true) {
          value.state = false;
          $scope.selectAll_modal = false;
        } else {
          value.state = true;
          $scope.selectAll_modal = true;
        }
      }
    }else{
      for (let [key, value] of $scope.recommend_list.entries()) {
        if (m === true) {
          value.state = false;
          $scope.selectAll_modal = false;
        } else {
          value.state = true;
          $scope.selectAll_modal = true;
        }
      }
    }
  };


  /*--------------------------Banner----------------------------------*/
  $http.get(baseUrl+'/mall/recommend-admin-index-supplier',{
    params:{
      district_code:510100,
      type:0
    }
  }).then(function (res) {
    console.log(res);
    $scope.banner_list=res.data.data.recommend_admin_index_supplier.details;
  },function (err) {
    console.log(err);
  });
  //Banner-sku获取
  $scope.banner_get_sku=function (num) {
    $scope.add_pass_sku=$scope.banner_add_sku;//把传过去的值重新定义，为确认按钮做判断
    //添加获取sku
    if(num===0){
      $http.get(baseUrl+'/mall/goods-by-sku',{
        params:{
          sku:+$scope.banner_add_sku
        }
      }).then(function (res) {
        if(res.data.code==1000){
          $scope.banner_num_error=true;
          $scope.banner_add_title='';
          $scope.banner_add_url='';
        }else if(res.data.code==200){
          if(res.data.data.detail.length===0){
            //Banner - sku错误提示
            $scope.banner_num_error=true;
            $scope.banner_add_title='';
            $scope.banner_add_url='';
          }else{
            $scope.banner_num_error=false;
            $scope.banner_add_title=res.data.data.detail.title;
            $scope.banner_add_url=res.data.data.detail.url;
          }
        }
      },function (err) {
        console.log(err);
      })
    }else{
      $scope.edit_pass_sku=$scope.banner_edit_sku;
      if($scope.banner_edit_change_error){
        $scope.banner_edit_change_error=false;
      }
      //编辑获取sku
      $http.get(baseUrl+'/mall/goods-by-sku',{
        params:{
          sku:+$scope.banner_edit_sku
        }
      }).then(function (res) {
        if(res.data.code==1000){
          $scope.banner_edit_num_error=true;
          $scope.banner_edit_title='';
          $scope.banner_edit_url='';
        }else if(res.data.code==200){
          if(res.data.data.detail.length===0){
            //Banner - sku错误提示
            $scope.banner_edit_num_error=true;
            $scope.banner_edit_title='';
            $scope.banner_edit_url='';
          }else{
            $scope.banner_edit_num_error=false;
            $scope.banner_edit_title=res.data.data.detail.title;
            $scope.banner_edit_url=res.data.data.detail.url;
          }
        }
      },function (err) {
        console.log(err);
      })
    }
  };

  //Banner - 上传图片
  $scope.banner_add_upload = function (file) {
    if(!$scope.data.file){
      return
    }
    console.log($scope.data);
    Upload.upload({
      url:baseUrl+'/site/upload',
      data:{'UploadForm[file]':file}
    }).then(function (response) {
      console.log(response);
      if(!response.data.data){
        $scope.banner_add_img_flag="上传图片格式不正确，请重新上传"
      }else{
        $scope.banner_add_img_flag='';
        $scope.banner_add_img_src=response.data.data.file_path;
      }
    },function (error) {
      console.log(error)
    })
  };

  //Banner - 添加确认按钮
  $scope.$watch('banner_add_sku',function (newVal,oldVal) { //监听sku输入框，如果为空时，编码错误提示消除
    console.log(newVal);
    if(newVal==undefined || newVal==''){
      $scope.banner_num_error=false;
    }
  });
  $scope.banner_add_btn = function (valid) {
    //如果有提示编码不能为空，消除提示编码错误
    if($scope.submitted){
      $scope.banner_num_error=false;
    }
    //输出正确编号，点击获取后，改变之前输入的编号，导致编号和内容不符
    if($scope.banner_add_sku!=$scope.add_pass_sku && $scope.add_pass_sku!=undefined){
      $scope.banner_num_error=true;
      $scope.banner_add_title='';
      $scope.banner_add_url='';
    }
    //只输入编号，不点击获取，直接点确定
    if($scope.banner_add_title==''&&$scope.banner_add_sku!=''&&$scope.banner_add_sku!=undefined){
      $scope.banner_num_error=true;
    }

    if(valid && $scope.banner_add_img_src && !$scope.banner_num_error){
      $scope.variable_add_modal='modal';
      if($scope.b_show_table_flag){
          $scope.pass_type=0;
      }else{
        $scope.pass_type=2;
      }
      $http.post(baseUrl+'/mall/recommend-add-supplier',{
          url:$scope.banner_add_url,
          title:$scope.banner_add_title,
          image:$scope.banner_add_img_src,
          from_type:'1',
          type:+$scope.pass_type,
          sku:+$scope.banner_add_sku
      },config).then(function (res) {
        $scope.judgment();
      },function (err) {
        console.log(err);
      })
    }else{
      $scope.submitted=true;
    }
    if(!$scope.banner_add_img_src){
      $scope.banner_add_img_flag='请上传图片';
      $scope.variable_add_modal='';
    }
  };
  //Banner - 添加取消按钮
  $scope.banner_clear_add=function () {
    $scope.banner_add_sku='';
    $scope.banner_add_title='';
    $scope.banner_add_url='';
    $scope.banner_add_img_src='';
    $scope.banner_add_img_flag='';
    $scope.submitted=false;
    $scope.banner_num_error=false;
    $scope.banner_add_num_blur=false;
    $scope.banner_edit_num_error=false;
    $scope.banner_edit_change_error=false;
    console.log($scope.banner_edit_num_error)
  };

  //Banner -  编辑上传图片
  $scope.banner_edit_upload = function (file) {
    if(!$scope.data.file){
      return
    }
    console.log($scope.data);
    Upload.upload({
      url:baseUrl+'/site/upload',
      data:{'UploadForm[file]':file}
    }).then(function (response) {
      console.log(response);
      if(!response.data.data){
        $scope.banner_edit_img_flag="上传图片格式不正确，请重新上传"
      }else{
        $scope.banner_edit_img_flag='';
        $scope.banner_edit_img_src=response.data.data.file_path;
      }
    },function (error) {
      console.log(error)
    })
  };
  //Banner - 点击编辑按钮
  $scope.banner_edit=function (id,sku,title,url,image) {
    $scope.variable_edit_modal='';
    $scope.banner_edit_num_error=false;
    $scope.banner_edit_id=id;
    $scope.banner_edit_sku=sku;
    $scope.banner_edit_click_sku=$scope.banner_edit_sku;
    $scope.banner_edit_title=title;
    $scope.banner_edit_url=url;
    $scope.banner_edit_img_src=image;
  };
  //Banner - 编辑确认按钮
  $scope.banner_edit_change_sku=function (sku) {
    if(sku==undefined || sku==''){
      $scope.banner_edit_num_error=false;
    }
    if(sku!=$scope.banner_edit_click_sku){
      $scope.banner_edit_change_error=true;
      console.log(111111111111111)
    }else{
      $scope.banner_edit_change_error=false;
    }
  };
  $scope.banner_edit_btn=function (valid) {
    //如果有提示编码不能为空，消除提示编码错误
    if($scope.banner_edit_change_error){
      console.log(222222222222);
      $scope.banner_edit_num_error=true;
      $scope.banner_edit_title='';
      $scope.banner_edit_url='';
    }else{
      $scope.banner_edit_num_error=false;
    }
    //输出正确编号，点击获取后，改变之前输入的编号，导致编号和内容不符
    if($scope.banner_edit_sku!=$scope.edit_pass_sku && $scope.banner_edit_sku!='' && $scope.banner_edit_sku!=undefined && $scope.edit_pass_sku!=undefined){
      $scope.banner_edit_num_error=true;
      $scope.banner_edit_title='';
      $scope.banner_edit_url='';
    }
    if($scope.banner_edit_sku==$scope.edit_pass_sku ){
      $scope.banner_edit_num_error=false;
    }
    if($scope.submitted){
      $scope.banner_edit_num_error=false;
    }

    if(valid && $scope.banner_edit_img_src && !$scope.banner_edit_num_error && !$scope.banner_edit_change_error){
      console.log(valid)
      console.log($scope.banner_edit_num_error)
      $scope.variable_edit_modal='modal';
      if($scope.b_show_table_flag){
        $scope.edit_pass_type=0
      }else{
        $scope.edit_pass_type=2
      }
      $http.post(baseUrl+'/mall/recommend-edit-supplier',{
        id:+$scope.banner_edit_id,
        url:$scope.banner_edit_url,
        title:$scope.banner_edit_title,
        image:$scope.banner_edit_img_src,
        from_type:'1',
        type:+$scope.edit_pass_type,
        sku:+$scope.banner_edit_sku
      },config).then(function (res) {
        $scope.judgment();
      },function (err) {
        console.log(err);
      })
    }
  };

  //Banner - 详情
  $scope.banner_detail=function (item) {
    console.log(item);
    $scope.banner_detail_title=item.title;
    $scope.banner_detail_left_number=item.left_number;
    $scope.banner_detail_supplier_price=item.supplier_price;
    $scope.banner_detail_platform_price=item.platform_price;
    $scope.banner_detail_market_price=item.market_price;
    $scope.banner_detail_purchase_price_decoration_company=item.purchase_price_decoration_company;
    $scope.banner_detail_purchase_price_manager=item.purchase_price_manager;
    $scope.banner_detail_purchase_price_designer=item.purchase_price_designer;
    $scope.banner_detail_image=item.image
  };

  //Banner - 单个删除
  $scope.banner_del=function (id) {
    $scope.delete_arr=[];
    $scope.delete_arr.push(id);

  };

  //批量删除
  $scope.delete_all=function () {
    $scope.delete_arr=[];
    if($scope.b_show_table_flag){
      for (let [key, value] of $scope.banner_list.entries()) {
        if(JSON.stringify($scope.banner_list).indexOf('"state":true')===-1){  //提示请勾选再删除
          $scope.delete_modal=true;
        }
        if(value.state){
          $scope.delete_modal=false;
          $scope.delete_arr.push(value.id);
          console.log($scope.delete_arr);
        }
      }
    }else{
      for (let [key, value] of $scope.recommend_list.entries()) {
        if(JSON.stringify($scope.recommend_list).indexOf('"state":true')===-1){  //提示请勾选再删除
          $scope.delete_modal=true;
        }
        if(value.state){
          $scope.delete_modal=false;
          $scope.delete_arr.push(value.id);
          console.log($scope.delete_arr);
        }
      }
    }

  };
  $scope.banner_del_confirm=function () {
    $http.post(baseUrl+'/mall/recommend-delete-batch-supplier',{
      ids:$scope.delete_arr.join(',')
    },config).then(function (res) {
      console.log(res);
      $scope.judgment();
    },function (err) {
      console.log(err);
    })
  };
  //拖拽排序 - Banner
  $scope.dropComplete = function(index,obj){
    let idx = $scope.banner_list.indexOf(obj);
    $scope.banner_list[idx] = $scope.banner_list[index];
    $scope.banner_list[index] = obj
  };
  //拖拽排序 - 推荐
  $scope.r_dropComplete=function (index,obj) {
    let idx = $scope.recommend_list.indexOf(obj);
    $scope.recommend_list[idx] = $scope.recommend_list[index];
    $scope.recommend_list[index] = obj
  };
  //
  $scope.sort_order=[];
  $scope.banner_save_btn=function () {
    $scope.sort_order=[];
    if($scope.b_show_table_flag){
      for(let[key,value] of $scope.banner_list.entries()){
        $scope.sort_order.push(value.id);
      }
    }else{
      for(let[key,value] of $scope.recommend_list.entries()){
        $scope.sort_order.push(value.id);
      }
    }
  };
  $scope.save_confirm=function () {
    console.log($scope.sort_order);
    $http.post(baseUrl+'/mall/recommend-sort-supplier',{
      ids:$scope.sort_order.join(',')
    },config).then(function (res) {
      console.log(res);
    },function (err) {
      console.log(err);
    })
  };


  /*--------------------------推荐-----------------------------*/
  $http.get(baseUrl+'/mall/recommend-admin-index-supplier',{
    params:{
      district_code:510100,
      type:2
    }
  }).then(function (res) {
    console.log(res);
    $scope.recommend_list=res.data.data.recommend_admin_index_supplier.details;
  },function (err) {
    console.log(err);
  });
  /*-----------------------Menu---------------------------*/
  // 分类菜单
  $scope.show_hide_menu=function () {
    $scope.show_1=true;$scope.rec_1=true;$scope.show_2=true;$scope.rec_2=true;
    $scope.show_3=true;$scope.rec_3=true;$scope.show_4=true;$scope.rec_4=true;
    $scope.show_5=true;$scope.rec_5=true;$scope.show_6=true;$scope.rec_6=true;
    $scope.show_7=true;$scope.rec_7=true;$scope.show_8=false;$scope.rec_8=false;
    $scope.show_9=false;$scope.rec_9=false;$scope.show_10=false;$scope.rec_10=false;
    $scope.show_11=false;$scope.rec_11=false;$scope.show_12=false;$scope.rec_12=false;
    $scope.show_13=false;$scope.rec_13=false;$scope.show_14=false;$scope.rec_14=false;
    $scope.show_15=true;$scope.rec_15=true;
  };
  $scope.show_hide_menu();
  $scope.show_a= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_1=true: $scope.show_1=false;
    }else{
      m===true?$scope.rec_1=true: $scope.rec_1=false;
    }
  };
  $scope.show_b= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_2=true: $scope.show_2=false;
    }else{
      m===true?$scope.rec_2=true: $scope.rec_2=false;
    }
  };
  $scope.show_c= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_3=true: $scope.show_3=false;
    }else{
      m===true?$scope.rec_3=true: $scope.rec_3=false;
    }
  };
  $scope.show_d= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_4=true: $scope.show_4=false;
    }else{
      m===true?$scope.rec_4=true: $scope.rec_4=false;
    }
  };
  $scope.show_e= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_5=true: $scope.show_5=false;
    }else{
      m===true?$scope.rec_5=true: $scope.rec_5=false;
    }
  };
  $scope.show_f= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_6=true: $scope.show_6=false;
    }else{
      m===true?$scope.rec_6=true: $scope.rec_6=false;
    }
  };
  $scope.show_g= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_7=true: $scope.show_7=false;
    }else{
      m===true?$scope.rec_7=true: $scope.rec_7=false;
    }
  };
  $scope.show_h= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_8=true: $scope.show_8=false;
    }else{
      m===true?$scope.rec_8=true: $scope.rec_8=false;
    }
  };
  $scope.show_i= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_9=true: $scope.show_9=false;
    }else{
      m===true?$scope.rec_9=true: $scope.rec_9=false;
    }
  };
  $scope.show_j= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_10=true: $scope.show_10=false;
    }else{
      m===true?$scope.rec_10=true: $scope.rec_10=false;
    }
  };
  $scope.show_k= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_11=true: $scope.show_11=false;
    }else{
      m===true?$scope.rec_11=true: $scope.rec_11=false;
    }
  };
  $scope.show_l= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_12=true: $scope.show_12=false;
    }else{
      m===true?$scope.rec_12=true: $scope.rec_12=false;
    }
  };
  $scope.show_m= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_13=true: $scope.show_13=false;
    }else{
      m===true?$scope.rec_13=true: $scope.rec_13=false;
    }
  };
  $scope.show_n= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_14=true: $scope.show_14=false;
    }else{
      m===true?$scope.rec_14=true: $scope.rec_14=false;
    }
  };
  $scope.show_o= function (m) {
    if($scope.b_show_table_flag){
      m===true?$scope.show_15=true: $scope.show_15=false;
    }else{
      m===true?$scope.rec_15=true: $scope.rec_15=false;
    }
  };
});
