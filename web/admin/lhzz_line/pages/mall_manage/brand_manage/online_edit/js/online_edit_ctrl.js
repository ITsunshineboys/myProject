;
let online_edit = angular.module("onlineeditModule",['ngFileUpload']);
online_edit.controller("onlineedit",function ($scope,$http,$stateParams,$state,Upload,$location,$anchorScroll,$window) {
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  $scope.myng=$scope;
  $scope.now_edit_list=[];
  $scope.now_edit_list=$stateParams.item;//当前那条数据
  $scope.index=$stateParams.index;//当前那条数据的索引
  console.log($scope.now_edit_list);
  $scope.online_time_flag=$stateParams.online_time_flag;//排序的类型
  /*===============进入页面显示的数据==================*/
  $scope.brand_on_name_model=$scope.now_edit_list.name;//品牌名称
  $scope.upload_img_src=$scope.now_edit_list.certificate;//商品注册证
  $scope.upload_logo_src=$scope.now_edit_list.logo;//LOGO
  $scope.online_people=$scope.now_edit_list.applicant;//操作人员
  $scope.online_time=$scope.now_edit_list.online_time;//下架时间
  //下架数据列表  size的设置是为了获取所有数据
  $scope.cycle_arr=[];
  $http.get('http://test.cdlhzz.cn:888/mall/brand-list-admin', {
    params: {
      status:1,
      size:9999,
      'sort[]':$scope.online_time_flag
    }}).then(function (res) {
      console.log(res);
    $scope.cycle_arr=res.data.data.brand_list_admin.details;
  },function (err) {
    console.log(err);
  });

  /*===========================上传图片开始===============================*/
  //上传商标注册证
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
  /*===========================上传图片结束==============================*/

  /*===========================系列分类开始==============================*/
  $scope.item_check = [];
  //获取一级
  $http({
    method: 'get',
    url: 'http://test.cdlhzz.cn:888/mall/categories'
  }).then(function successCallback(response) {
    $scope.details = response.data.data.categories;
    $scope.oneColor = $scope.details[0];
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
  $scope.three=[];
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
      if($scope.add_three==0){
          $scope.item_check.push(item);
      }
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
          console.log(value)
          if(item.id==value.id){
              value.complete=false;
          }
      }
      $scope.item_check.splice($scope.item_check.indexOf(item),1);
    //分类提示文字
    if($scope.item_check.length<1){
      $scope.sort_check='请至少选择一个分类';
    }else{
      $scope.sort_check='';
    }
  };
  //默认进页面获取三级分类所具有的系类
    for(let[key,value] of $scope.now_edit_list.categories.entries()){
        $scope.item_check.push(value);
    }
  /*===========================系列分类结束==============================*/

  /*===================判断====================*/
  $scope.edit_ok_v='';//模态框
  $scope.ids_arr=[];//三级分类
  //确认按钮
  $scope.save_btn=function (valid,error) {
    console.log(valid);
    let brand_obj = JSON.stringify({"name":""+$scope.brand_on_name_model});//序列化！！！
    for(let [key,value] of $scope.cycle_arr.entries()){
      if(valid && $scope.item_check.length>=1 && ((value.name==$scope.brand_on_name_model && key==$scope.index) || (JSON.stringify($scope.cycle_arr).indexOf(brand_obj.slice(1,brand_obj.length-1))==-1))){
        //名称和索引相同，代表用户进入没有修改名称，可进行下一步操作
        //名称和索引都不相同，也可以修改
        $scope.edit_ok_v='#edit_ok_modal';//弹出模态框
        $scope.ids_arr=[];
        for(let [key,value] of $scope.item_check.entries()){
          if(value.complete){
            delete value.complete
          }
          $scope.ids_arr.push($scope.item_check[key].id)
        }
        console.log($scope.ids_arr);
        $http.post('http://test.cdlhzz.cn:888/mall/brand-edit',{
          id:$scope.now_edit_list.id,
          name:$scope.brand_on_name_model,
          certificate:$scope.upload_img_src,
          logo:$scope.upload_logo_src,
          category_ids:$scope.ids_arr.join(',')
        },config).then(function (res) {
          console.log(res);
          console.log('修改成功');
        },function (err) {
          console.log(err);
        });
        $scope.edit_title_red=false;
        return
      }else{
        $scope.submitted=true
      }
      if(value.name==$scope.brand_on_name_model && key!=$scope.index){  //名称有重复，提示文字
        $scope.edit_title_red=true;
      }

      if(!valid){					//名称输入框为空， 文本框变红，并跳转到对于的位置
        $scope.submitted = true;
        if(value.$invalid=true){
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
      }
      //判断分类
      if($scope.item_check.length<1){
        $scope.sort_check='请至少选择一个分类';
      }else{
        $scope.sort_check='';
      }
    }
  };
  /*实时判断输入的内容，是否有重复的名称*/
  $scope.$watch('brand_on_name_model',function (newVal,oldVal) {
    for(let [key,value] of $scope.cycle_arr.entries()) {
      if (newVal == value.name  && key!=$scope.index) {
        return $scope.edit_title_red=true;
      }else{
        $scope.edit_title_red=false;
      }
    }
  });
  /*下架*/
  $scope.down_shelver_ok=function () {
    console.log($scope.now_edit_list.id)
    $http.post('http://test.cdlhzz.cn:888/mall/brand-status-toggle',{
      id:$scope.now_edit_list.id,
      offline_reason:$scope.edit_down_shelves_reason
    },config).then(function (res) {
      setTimeout(function () {
        $state.go('brand_index');//跳转主页
      },300)
    },function (err) {
      console.log(err);
    })
  };
  /*编辑成功*/
  $scope.save_modal_btn=function () {
    setTimeout(function () {
      $state.go('brand_index');//跳转主页
    },300)
  };
});