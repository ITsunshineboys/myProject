;
let add_brand = angular.module("addbrandModule",['ngFileUpload']);
add_brand.controller("addbrand",function ($rootScope,$scope,$http,$state,Upload,$location,$anchorScroll,$window,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '品牌管理',
        link: 'brand_index',
        params:{down_flag:true}
    }, {
        name: '添加品牌'
    }];
  $scope.trademark_txt='上传';
  $scope.logo_txt='上传'
  $scope.upload_dis=false;
  $scope.myng=$scope;
	//上传商标注册证
  $scope.upload_img_src='';
  $scope.data = {
    file:null
  };
  $scope.upload = function (file) {
    if(!$scope.data.file){
      return
    }
    $scope.trademark_txt='上传...';
    $scope.upload_dis=true;
    console.log($scope.data);
    Upload.upload({
      url:baseUrl+'/site/upload',
      data:{'UploadForm[file]':file}
    }).then(function (response) {
      console.log(response);
      if(!response.data.data){
        $scope.img_flag="上传图片格式不正确，请重新上传"
      }else{
        $scope.img_flag='';
        $scope.upload_img_src=response.data.data.file_path;
        $scope.trademark_txt='上传';
        $scope.upload_dis=false;
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
    $scope.logo_txt='上传...'
    $scope.upload_dis=true;
    console.log($scope.data);
    Upload.upload({
      url:baseUrl+'/site/upload',
      data:{'UploadForm[file]':file}
    }).then(function (response) {
      console.log(response);
      if(!response.data.data){
        $scope.img_logo_flag="上传图片格式不正确，请重新上传"
      }else{
        $scope.img_logo_flag='';
        $scope.upload_logo_src=response.data.data.file_path;
        $scope.logo_txt='上传'
        $scope.upload_dis=false;
      }
    },function (error) {
      console.log(error)
    })
  };


  //系列分类
  $scope.item_check = [];
  //获取一级
  _ajax.get('/mall/categories',{},function (res) {
      $scope.details = res.data.categories;
      $scope.oneColor= $scope.details[0];
  })
  //获取二级
  _ajax.get('/mall/categories?pid=1',{},function (res) {
      $scope.second = res.data.categories;
      $scope.twoColor= $scope.second[0];
  })
  //获取三级
  _ajax.get('/mall/categories?pid=2',{},function (res) {
      $scope.three = res.data.categories;
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
  })
  //点击一级 获取相对应的二级
  $scope.getMore = function (n) {
    $scope.oneColor = n;
    _ajax.get('/mall/categories',{pid:n.id},function (res) {
        console.log(res);
        $scope.second = res.data.categories;
        $scope.twoColor = $scope.second[0];
        _ajax.get('/mall/categories',{pid:$scope.second[0].id},function (res) {
            $scope.three = res.data.categories;
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
        })
    })
  };
  //点击二级 获取相对应的三级
  $scope.getMoreThree = function (n) {
    $scope.id=n;
    $scope.twoColor = n;
    _ajax.get('/mall/categories',{pid:n.id},function (res) {
        $scope.three = res.data.categories;
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
      $scope.add_three=0;
      for(let[key,value] of $scope.item_check.entries()){
          if(item.id==value.id){
              $scope.item_check.splice(key,1);
              $scope.add_three=1;
              break;
          }else{
              $scope.add_three=0
          }
      }
      if($scope.add_three!=1){
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
          // console.log(value)
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

  //确定按钮
  $scope.ids_arr=[];
  $scope.brand_name_flag=false;//默认品牌名称提示文字 不显示
  $scope.add_brand_ok=function (valid,error) {
      if(valid && $scope.upload_img_src && $scope.upload_logo_src && $scope.item_check.length>=1){
          $scope.ids_arr=[];
          for(let [key,value] of $scope.item_check.entries()){
              $scope.ids_arr.push($scope.item_check[key].id)
          }
          console.log($scope.ids_arr);
          _ajax.post('/mall/brand-add',{
              name:$scope.brand_name_model,
              certificate:$scope.upload_img_src,
              logo:$scope.upload_logo_src,
              category_ids:$scope.ids_arr.join(',')
          },function (res) {
              console.log(res);
              if(res.code==200){
                $('#down_add_modal').modal('show');
              }else{
                  $scope.brand_name_flag=true;
                  $anchorScroll.yOffset = 150;
                  $location.hash('brand_title');
                  $anchorScroll();
                  $window.document.getElementById('brand_title').focus();
              }
          })
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

  //模态框确认按钮
  $scope.saveonline=function () {
    setTimeout(function () {
      $state.go('brand_index',{down_flag:true});//跳转主页
    },300)
  }
});