;
let offline_edit = angular.module("offlineeditModule",[]);
offline_edit.controller("offlineedit",function ($rootScope,$scope,$http,$stateParams,$state,Upload,$location,$anchorScroll,$window,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: 'merchant_index'
    }, {
        name: '品牌管理',
        link: 'brand_index',
        params:{down_flag:true}
    }, {
        name: '品牌详情'
    }];
	$scope.myng=$scope;
    $scope.now_edit_list=[];
	$scope.now_edit_list=$stateParams.down_shelves_list;//当前那条数据
	 console.log($scope.now_edit_list);
	 $scope.online_time_flag=$stateParams.online_time_flag;//排序的类型
	/*===============进入页面显示的数据==================*/
	$scope.brand_name_model=$scope.now_edit_list.name;//品牌名称
	$scope.upload_img_src=$scope.now_edit_list.certificate;//商品注册证
	$scope.upload_logo_src=$scope.now_edit_list.logo;//LOGO
	$scope.edit_reason=$scope.now_edit_list.offline_reason;//下架原因
	$scope.off_people=$scope.now_edit_list.applicant;//操作人员
	$scope.off_time=$scope.now_edit_list.offline_time;//下架时间
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
      url:baseUrl+'/site/upload',
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
      url:baseUrl+'/site/upload',
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


  //系列分类
  $scope.item_check = [];
  $scope.three=[];
    //获取一级
    _ajax.get('/mall/categories',{},function (res) {
        $scope.details = res.data.categories;
        $scope.oneColor = $scope.details[0];
    })
    //获取二级
    _ajax.get('/mall/categories',{pid:1},function (res) {
        $scope.second = res.data.categories;
        $scope.twoColor= $scope.second[0];
    })
  //获取三级
    _ajax.get('/mall/categories',{pid:2},function (res) {
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
  //点击一级 获取相对应的二级
    $scope.getMore = function (n) {
        $scope.oneColor = n;
        _ajax.get('/mall/categories',{pid:n.id},function (res) {
            $scope.second = res.data.categories;
            $scope.twoColor = $scope.second[0];
            _ajax.get('/mall/categories',{pid:+ $scope.second[0].id},function (res) {
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
          console.log($scope.add_three);
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

  /*===================判断====================*/
  $scope.edit_ok_v='';//模态框
  $scope.ids_arr=[];//三级分类
  //确认按钮
  $scope.save_btn=function (valid,error) {
        if(valid && $scope.item_check.length>=1){
          $scope.ids_arr=[];
          for(let [key,value] of $scope.item_check.entries()){
            if(value.complete){
              delete value.complete
            }
            $scope.ids_arr.push($scope.item_check[key].id)
          }
          console.log($scope.ids_arr);
          _ajax.post('/mall/brand-edit',{
              id:$scope.now_edit_list.id,
              name:$scope.brand_name_model,
              certificate:$scope.upload_img_src,
              logo:$scope.upload_logo_src,
              category_ids:$scope.ids_arr.join(',')
          },function (res) {
              if(res.code==200){
                  $('#edit_ok_modal').modal('show');
              }else{
                  $scope.edit_title_red=true;
                  $anchorScroll.yOffset = 150;
                  $location.hash('brand_title');
                  $anchorScroll();
                  $window.document.getElementById('brand_title').focus();
              }
          });
        }else{
          $scope.submitted = true;
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
                    console.log(typeof value.$name)
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
  };
/*编辑成功*/
$scope.save_modal_btn=function () {
  setTimeout(function () {
    $state.go('brand_index',{down_flag:true});//跳转主页
  },300)
};
});