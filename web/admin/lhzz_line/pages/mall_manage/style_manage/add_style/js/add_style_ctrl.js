let add_style = angular.module("addstyleModule",['ngFileUpload']);
add_style.controller("add_style",function ($scope,$http,$state,$stateParams,Upload) {
  $scope.myng=$scope;
	//POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  //风格列表所有数据
  $scope.style_arr=[];
  $http.get('http://test.cdlhzz.cn:888/mall/style-list',{
    params:{
      size:99999
    }
  }).then(function (res) {
    console.log(res);
    $scope.style_arr=res.data.data.series_list.details;
  },function (err) {
    console.log(err)
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

	//风格增加确认按钮
  $scope.tran_arr=[];
	$scope.add_style_ok=function (valid) {
    let brand_obj = JSON.stringify({"style":""+$scope.style_name});//序列化！！！
	  if(valid&&JSON.stringify($scope.style_arr).indexOf(brand_obj.slice(1,brand_obj.length-1))==-1&&$scope.upload_img_arr!=''){
      for(let[key,value] of $scope.style_label_arr.entries()){
        if(value.num!=''){
          $scope.tran_arr.push(value.num);//标签组
        }
      }
      $scope.sur_id='suremodal';//确认模态框
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
    $http.post('http://test.cdlhzz.cn:888/mall/style-add',{
      style:$scope.style_name,// 名称
      theme:$scope.tran_arr.join(','),//标签组
      intro:$scope.style_intro,//简介
      images:$scope.upload_img_arr.join(',')//图片
    },config).then(function (res) {
      console.log(res);
    },function (err) {
      console.log(err);
    });
		setTimeout(function () {
			$state.go("style_index",{showstyle:true});
    },300);
  }
});