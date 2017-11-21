let add_style = angular.module("addstyleModule",['ngFileUpload']);
add_style.controller("add_style",function ($rootScope,$scope,$http,$state,$stateParams,Upload,_ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '系列/风格/属性管理',
        link: 'style_index',
        params:{showstyle:true}
    }, {
        name: '添加新风格'
    }];
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    }
  $scope.myng=$scope;
  $scope.style_intro='';
  $scope.change_txts=function () {
    if($scope.style_intro==undefined){
      $scope.style_intro='';
    }
  };


  //风格列表所有数据
  $scope.style_arr=[];
  _ajax.get('/mall/style-list',{size:99999},function (res) {
    console.log(res);
    $scope.style_arr=res.data.series_list.details;
  });
  //判断系列名称是否存在
  $scope.name_flag=false;
  $scope.$watch('style_name',function(newVal,oldVal){
    for(let [key,value] of $scope.style_arr.entries()){
      if(newVal===value.style){
        $scope.name_flag=true;
        return;
      }else {
        $scope.name_flag=false;
      }
    }
  });

  //标签列表
  $scope.i=1;
  $scope.style_label_arr=[{num:'',label_name:'label_name'+1}];

//增加标签
  $scope.label_add=function () {
    $scope.i++;
    $scope.style_label_arr.push({num:'',label_name:'label_name'+$scope.i});
  };

  //删除标签
  $scope.close_label=function (index) {
    $scope.style_label_arr.splice(index,1);
  };

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
      url:baseUrl+'/site/upload',
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
        $http.post(baseUrl+'/site/upload-delete',{file_path:item},config).then(function (res) {
			console.log(res);
			$scope.upload_img_arr.splice($scope.upload_img_arr.indexOf(item),1);
        },function (err) {
			console.log(err);
        })
  };

	//风格增加确认按钮
  $scope.tran_arr=[];
	$scope.add_style_ok=function (valid) {
    let brand_obj = JSON.stringify({"style":""+$scope.style_name});//序列化！！！
	  if(valid&&JSON.stringify($scope.style_arr).indexOf(brand_obj.slice(1,brand_obj.length-1))==-1&&$scope.upload_img_arr!=''){
      $scope.sur_id='suremodal';//确认模态框
	  for(let[key,value] of $scope.style_label_arr.entries()){
        if(value.num!=''){
          $scope.tran_arr.push(value.num);//标签组
        }
      }
      _ajax.post('/mall/style-add',{
          style:$scope.style_name,// 名称
          theme:$scope.tran_arr.join(','),//标签组
          intro:$scope.style_intro,//简介
          images:$scope.upload_img_arr.join(',')//图片
      },function (res) {
          console.log(res);
      })
    }else{
	    $scope.submitted=true;
    }
    //判断图片上传
    if($scope.upload_img_arr==''){
      $scope.img_flag='请上传图片';
    }
  };
  //添加成功后跳转
	$scope.style_go=function () {
        setTimeout(function () {
            $state.go("style_index",{showstyle:true});
        },300);
  }
});