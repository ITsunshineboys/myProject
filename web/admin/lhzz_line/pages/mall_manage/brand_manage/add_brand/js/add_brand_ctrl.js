;
let add_brand = angular.module("addbrandModule",['ngFileUpload']);
add_brand.controller("addbrand",function ($scope,$http,$state,Upload,$location,$anchorScroll,$window) {
  $scope.myng=$scope;
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
//下架列表
  $scope.cycle_arr=[];
  $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin', {
    params:{
      status:0,
      size:999999
    }
  }).then(function (res) {
    console.log("已下架后台列表");
    $scope.cycle_arr=res.data.data.brand_list_admin.details;
  },function (err) {
    console.log(err);
  });

	//上传商标注册证
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

  //上传品牌LOGO
  $scope.upload_logo_src='';
  $scope.data = {
    file:null
  };
  $scope.upload_logo = function (file) {
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
        $scope.img_logo_flag="上传图片格式不正确，请重新上传"
      }else{
        $scope.img_logo_flag='';
        $scope.upload_logo_src=response.data.data.file_path;
      }
    },function (error) {
      console.log(error)
    })
  };


  //系列分类
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

      for(let[key,value] of $scope.item_check.entries()){
          if(item.id==value.id){
              $scope.item_check.splice(key,1);
              $scope.add_three=1;
              break;
          }else{
              $scope.add_three=0
          }
          console.log($scope.add_three);
      }
      if($scope.add_three!=1){
          $scope.item_check.push(item);
      }
    // if(item.complete){
    //   $scope.item_check.push(item);
    // }else{
    //   $scope.item_check.splice($scope.item_check.indexOf(item),1)
    // }
    //分类提示文字
    if($scope.item_check.length<1){
      $scope.sort_check='请至少选择一个分类';
    }else{
      $scope.sort_check='';
    }
  };
  //删除拥有系列的三级
  $scope.delete_item = function (item) {
      for(let[key,value] of $scope.three.entries()){
          // console.log(value)
          if(item.id==value.id){
              value.complete=false;
          }
      }
      $scope.item_check.splice($scope.item_check.indexOf(item),1);
    //item.complete = false;
    //$scope.item_check.splice($scope.item_check.indexOf(item),1);
    //分类提示文字
    if($scope.item_check.length<1){
      $scope.sort_check='请至少选择一个分类';
    }else{
      $scope.sort_check='';
    }
  };

  //确定按钮
  $scope.brand_name_flag=false;//默认品牌名称提示文字 不显示
  $scope.add_brand_ok=function (valid,error) {
    let brand_obj = JSON.stringify({"name":""+$scope.brand_name_model});//序列化！！！
      if(valid && JSON.stringify($scope.cycle_arr).indexOf(brand_obj.slice(1,brand_obj.length-1))==-1 && $scope.upload_img_src && $scope.upload_logo_src && $scope.item_check.length>=1){
        $scope.add_modal_v='modal';
      }
      if(!valid){
        $scope.submitted = true;
        //循环错误，定位到第一次错误，并聚焦
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
    if(!$scope.upload_img_src){
      $scope.img_flag='请上传图片';
      $scope.add_modal_v='';
    }
    if(!$scope.upload_logo_src){
      $scope.img_logo_flag='请上传图片';
      $scope.add_modal_v='';
    }
    if($scope.item_check.length<1){
      $scope.sort_check='请至少选择一个分类';
    }else{
      $scope.sort_check='';
    }
  };
  //监听品牌名称 是否重复
  $scope.$watch('brand_name_model',function (newVal,oldVal) {
    for(let [key,value] of $scope.cycle_arr.entries()){
      if($scope.brand_name_model === value.name){
        $scope.brand_name_flag=true;
        break;
      }else{
        $scope.brand_name_flag=false;
      }
    }
  });

  //模态框确认按钮
	$scope.ids_arr=[];
  $scope.saveonline=function () {
    setTimeout(function () {
      $state.go('brand_index',{down_flag:true});//跳转主页
      for(let [key,value] of $scope.item_check.entries()){
        if(value.complete){
          delete value.complete
        }
        $scope.ids_arr.push($scope.item_check[key].id)
      }
      console.log($scope.ids_arr);
      let url='http://test.cdlhzz.cn:888/mall/brand-add';
      $http.post(url,{
        name:$scope.brand_name_model,
        certificate:$scope.upload_img_src,
        logo:$scope.upload_logo_src,
        category_ids:$scope.ids_arr.join(',')
      },config).then(function (res) {
        console.log("添加成功")
        console.log(res);
      },function (err) {
        console.log(err);
      });
    },300)
  }
  console.log($scope.brand_name_flag)
});