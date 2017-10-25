let shop_style_let= angular.module("shop_style",['ngFileUpload']);
shop_style_let.controller("shop_style_ctrl",function ($scope,$http,$stateParams,$state,Upload,$location,$anchorScroll,$window) {
      /*POST请求头*/
      $scope.myng=$scope;
      const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
          return $.param(data)
        }
      };
      $scope.logistics=[];//物流模块列表
      $scope.goods_all_attrs=[];//所有属性数据
      $scope.shop_logistics=[];//物流模板默认第一项
      $scope.category_id=$stateParams.category_id;//三级分类的id
      $scope.first_category_title=$stateParams.first_category_title;//一级分类名称
      $scope.second_category_title=$stateParams.second_category_title;//二级分类名称
      $scope.third_category_title=$stateParams.third_category_title;//三级分类名称

      /*-----------------品牌、系列、风格 获取-----------------*/
      $scope.brands_arr=[];
      $scope.series_arr=[];
      $scope.styles_arr=[];
      $http.get('http://test.cdlhzz.cn:888/mall/category-brands-styles-series',{
        params:{
          category_id:+$scope.category_id
        }
      }).then(function (res) {
        console.log(res);
        /*品牌、系列、风格 下拉框开始*/
            //初始化下拉框的第一项  开始
        $scope.brands_arr=res.data.data.category_brands_styles_series.brands;
        if($scope.brands_arr.length>0){
          $scope.brand_model=res.data.data.category_brands_styles_series.brands[0].id;
        }
        $scope.series_arr=res.data.data.category_brands_styles_series.series;
        if($scope.series_arr.length>0){
          $scope.series_model=res.data.data.category_brands_styles_series.series[0].id;
        }
        $scope.styles_arr=res.data.data.category_brands_styles_series.styles;
        if($scope.styles_arr.length>0){
          $scope.style_model=res.data.data.category_brands_styles_series.styles[0].id;
        }
            //初始化下拉框的第一项  结束
      },function (err) {
        console.log(err);
      });
     /*品牌、系列、风格 下拉框结束*/

      /*---------------属性获取-----------------*/

      $scope.goods_input_attrs=[];//普通文本框
      $scope.goods_select_attrs=[];//下拉框

      $scope.goods_select_value=[];//下拉框的值
      $scope.pass_attrs_name=[];//名称
      $scope.pass_attrs_value=[];//值

        $http.get('http://test.cdlhzz.cn:888/mall/category-attrs',{
            params:{
              category_id:+$scope.category_id
            }
        }).then(function (res) {
          console.log(res);
          $scope.goods_all_attrs=res.data.data.category_attrs;
          //console.log('属性');
          //console.log($scope.goods_all_attrs);
          //循环所有获取到的属性值，判断是普通文本框还是下拉框
          for( let [key,value] of $scope.goods_all_attrs.entries()){
            if(value.addition_type==1){
              $scope.goods_select_attrs.push(value);
            }else{
              $scope.goods_input_attrs.push(value);
            }
          }
          //循环添加名称和值
          for(let [key,value] of $scope.goods_input_attrs.entries()){
            $scope.attr_name=value.name;
            $scope.attr_value=value.value;
          }
          //循环下拉框的value
          for(let [key,value] of $scope.goods_select_attrs.entries()){
            $scope.goods_select_name=value.name;//名称
            $scope.goods_select_value=value.value;//下拉框
            $scope.goods_select_model=$scope.goods_select_value[0];
          }

        },function (err) {
          console.log(err)
        });
      /*----------------自己添加的属性--------------------*/
      $scope.own_attrs_arr=[];//自定义数组
     // $scope.attrs_name=[];//名称
      //$scope.attrs_value=[];//内容
      //添加属性
      $scope.i=1;
      $scope.add_own_attrs=function () {
        $scope.own_attrs_arr.push({name:'',value:'',name_model:'attrs'+$scope.i,value_model:'value'+$scope.i});
        $scope.i++;
      };
      //删除属性
      $scope.del_own_attrs=function (index) {
        console.log(index);
        $scope.own_attrs_arr.splice(index,1);
      };

      /*----------------上传封面图-----------------------*/
      $scope.upload_cover_src='';
      $scope.data = {
        file:null
      };
      $scope.upload_cover = function (file) {
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
            $scope.cover_flag="上传图片格式不正确，请重新上传"
          }else{
            $scope.cover_flag='';
            $scope.upload_cover_src=response.data.data.file_path;
          }
        },function (error) {
          console.log(error)
        })
      };

      /*------------------------上传多张图片--------------------------*/
      //上传图片
      $scope.upload_img_arr=[]; //图片数组
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
          if(!response.data.data){
            $scope.img_flag="上传图片格式不正确，请重新上传"
          }else{
            $scope.img_flag='';
            $scope.upload_img_arr.push(response.data.data.file_path)
          }
        },function (error) {
          console.log(error)
        })
      };
      //删除图片
      $scope.del_img=function (item) {
        $http.post('http://test.cdlhzz.cn:888/site/upload-delete',{
          file_path:item
        },config).then(function (res) {
          console.log(res);
          $scope.upload_img_arr.splice($scope.upload_img_arr.indexOf(item),1);
        },function (err) {
          console.log(err);
        })
      };
      //售后、保障
      $scope.after_sale_services=[];//售后、保障传值数组
      $scope.invoice_check=true;

      //价格、库存

      //市场价
      $scope.price_flag=false;
      $scope.my_market_price=function () {
        (+$scope.market_price>=+$scope.platform_price)&&(+$scope.market_price>=+$scope.supplier_price)?$scope.price_flag=false:$scope.price_flag=true;
      };
      //平台价
      $scope.my_platform_price=function () {
        (+$scope.platform_price<=+$scope.market_price)&&(+$scope.platform_price>=+$scope.supplier_price)?$scope.price_flag=false:$scope.price_flag=true;
      };
      //供货商价
      $scope.my_supplier_price=function () {
        (+$scope.supplier_price<=+$scope.platform_price)&&(+$scope.supplier_price<=+$scope.market_price)?$scope.price_flag=false:$scope.price_flag=true;
      };

      //物流模块类别
    $scope.logistics_flag=false;
      $http.post('http://test.cdlhzz.cn:888/mall/logistics-templates-supplier',{},config).then(function (res) {
        console.log('物流模板');
          console.log(res);
          if(res.data.data.logistics_templates_supplier.length==0){
                $scope.logistics_flag=false;
          }else{
              $scope.logistics_flag=true;
              $scope.logistics=res.data.data.logistics_templates_supplier;
              $scope.shop_logistics=res.data.data.logistics_templates_supplier[0].id;
              //物流模块详情
              $scope.$watch('shop_logistics',function (newVal,oldVal) {
                  $http.get('http://test.cdlhzz.cn:888/mall/logistics-template-view',{
                      params:{
                          id:+newVal
                      }
                  }).then(function (res) {
                      console.log('物流详情');
                      console.log(res);
                      $scope.logistics_method=res.data.data.logistics_template.delivery_method;//快递方式
                      $scope.district_names=res.data.data.logistics_template.district_names;//地区
                      $scope.delivery_cost_default=res.data.data.logistics_template.delivery_cost_default;//默认运费
                      $scope.delivery_number_default=res.data.data.logistics_template.delivery_number_default;//默认运费的数量
                      $scope.delivery_cost_delta=res.data.data.logistics_template.delivery_cost_delta;//增加件费用
                      $scope.delivery_number_delta=res.data.data.logistics_template.delivery_number_delta;//增加件的数量
                  },function (err) {
                      console.log(err);
                  });
              });
          }

      },function (err) {
        console.log(err)
      });



  /*-----------------添加按钮-----------------------*/
  $scope.add_goods_confirm=function (valid,error) {
    $scope.after_sale_services=[];
    //提供发票
      if($scope.invoice_check){
        $scope.after_sale_services.push(0);
      }
      //上门安装
      if($scope.door_instal_model){
        $scope.after_sale_services.push(1);
      }else if($scope.door_instal_model===false){
        del_correspond(1);
      }
      //上门维修
    if($scope.door_service_check){
      $scope.after_sale_services.push(2);
    }else if($scope.door_service_check===false){
      del_correspond(2);
    }
    //上门退货
    if($scope.door_return_check){
      $scope.after_sale_services.push(3);
    }else if($scope.door_return_check===false){
      del_correspond(3);
    }
    //上门换货
    if($scope.door_replacement_check){
      $scope.after_sale_services.push(4);
    }else if($scope.door_replacement_check===false){
      del_correspond(4);
    }
    //退货
    if($scope.return_check){
      $scope.after_sale_services.push(5);
    }else if($scope.return_check===false){
      del_correspond(5);
    }
    //换货
    if($scope.replacement_check){
      $scope.after_sale_services.push(6);
    }else if($scope.replacement_check===false){
      del_correspond(6);
    }

    //不勾选的状态删除对应项
    function del_correspond(num){
      let del_index=$scope.after_sale_services.findIndex(function(value,index,arr) {
        return value==num ;
      });
      if(del_index!= -1){
        $scope.after_sale_services.splice(del_index,1);
      }
    }

    /*判断必填项，全部ok，调用添加接口*/
    if(valid && $scope.upload_cover_src && !$scope.price_flag){
      $scope.success_variable='#on_shelves_add_success';
      /*循环自己添加的属性*/
      for(let[key,value] of $scope.own_attrs_arr.entries()){
        $scope.pass_attrs_name.push(value.name);//属性名
        $scope.pass_attrs_value.push(value.value);//属性值
      }
      /*判断是默认属性是 下拉框还是普通文本框*/
      if($scope.goods_input_attrs[0]!=undefined){
        $scope.pass_attrs_name.push($scope.attr_name);
        $scope.pass_attrs_value.push($scope.attr_value);
      }
      if($scope.goods_select_attrs[0]!=undefined){
        $scope.pass_attrs_name.push($scope.goods_select_name);
        $scope.pass_attrs_value.push($scope.goods_select_model);
      }
      /*判断风格和系列是否存在，如果不存在，值传0*/
      $scope.series_model==undefined?$scope.series_model=0:$scope.series_model=parseInt($scope.series_model);
      $scope.style_model==undefined?$scope.style_model=0:$scope.style_model=parseInt($scope.style_model);
      /*如果没有属性，则传空数组*/
      if($scope.pass_attrs_name[0]==undefined){
        $scope.pass_attrs_name=[];
      }
      if($scope.pass_attrs_value[0]==undefined){
        $scope.pass_attrs_value=[];
      }
       $http.post('http://test.cdlhzz.cn:888/mall/goods-add',{
        category_id:+$scope.category_id,      //三级分类id
        title:$scope.goods_name,              //名称
        subtitle:$scope.des_name,             //特色
        brand_id:+$scope.brand_model,      //品牌
        style_id:$scope.style_model,      //风格
        series_id:$scope.series_model,    //系列
        'names[]':$scope.pass_attrs_name,   // 属性名称
        'values[]':$scope.pass_attrs_value, //属性值
        cover_image:$scope.upload_cover_src,//封面图
        'images[]':$scope.upload_img_arr,   //图片
        supplier_price:+$scope.supplier_price*100,//供货价
        platform_price:+$scope.platform_price*100,//平台价
        market_price:+$scope.market_price*100,//市场价
        left_number:+$scope.left_number,//库存
        logistics_template_id:+$scope.shop_logistics,//物流模板
        after_sale_services:$scope.after_sale_services.join(','),//售后、保障
        description:$scope.detail_description//描述
      },config).then(function (res) {
        console.log('添加成功');
        console.log(res);
      },function (err) {
        console.log(err);
      })
    }else{
      $scope.submitted=true;
    }
    //判断封面图是否上传
    if(!$scope.upload_cover_src){
      $scope.cover_flag='请上传图片'
    }
    //名称输入框为空， 文本框变红，并跳转到对于的位置
    if(!valid){
      $scope.submitted = true;
      // if(value.$invalid=true){
      for (let [key, value] of error.entries()) {
        if (value.$invalid) {
          $anchorScroll.yOffset = 150;
          $location.hash(value.$name);
          $anchorScroll();
          $window.document.getElementById(value.$name).focus();
          break
        }
      }
    }
  };
  //添加成功模态框确认按钮
  $scope.on_shelves_add_success=function () {
    setTimeout(function () {
      $state.go('commodity_manage');
    },300)
  }
});

