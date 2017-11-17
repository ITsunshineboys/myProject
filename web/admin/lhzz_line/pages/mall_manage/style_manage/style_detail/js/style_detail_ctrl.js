let style_detail = angular.module("styledetailModule",[]);
style_detail.controller("style_detail",function ($rootScope,$scope,$http,$state,$stateParams,Upload) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: 'merchant_index'
    }, {
        name: '系列/风格/属性管理',
        link: 'style_index',
        params:{showstyle:true}
    }, {
        name: '风格详情页'
    }];
	$scope.myng=$scope;
	$scope.page=$stateParams.page;//默认页数
  //POST请求的响应头
  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };
  $scope.change_txts=function () {
    if($scope.style_txt==undefined){
      $scope.style_txt='';
    }
  };
  //风格列表所有数据
  $scope.style_arr=[];
  $http.get(baseUrl+'/mall/style-list',{
    params:{
      size:99999
    }
  }).then(function (res) {
    $scope.style_arr=res.data.data.series_list.details;
  },function (err) {
    console.log(err)
  });

	$scope.style_item=$stateParams.style_item;//点击的那条数据的所有信息
	console.log($scope.style_item);
	/*进入页面默认显示数据*/
  $scope.style_name=$scope.style_item.style;//名称
	$scope.style_txt=$scope.style_item.intro;//简介
  $scope.label_list=$scope.style_item.theme.split(',');//分割标签列表
  $scope.style_label_arr=[];//标签组循环数组
  $scope.i=1;
  for(let[key,value] of $scope.label_list.entries()){
    $scope.style_label_arr.push({num:value,label_name:'label_name'+$scope.i});
    $scope.i++;
  }
  //增加标签
  $scope.label_add=function () {
    $scope.i++;
    $scope.style_label_arr.push({num:'',label_name:'label_name'+$scope.i});
    console.log($scope.style_label_arr)
  };
  //删除标签
  $scope.close_label=function (index) {
    $scope.style_label_arr.splice(index,1);
  };
  $scope.img_list=$scope.style_item.images.split(',');//分割图片列表
  //$scope.img_list=[];
  /*----------------------上传图片 开始-----------------------*/

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
        $scope.img_list.push(response.data.data.file_path)
      }
    },function (error) {
      console.log(error)
    })
  };

  //删除图片
  $scope.del_img=function (item) {
    //判断图片列表是否为空
    $http.post(baseUrl+'/site/upload-delete',{
      file_path:item
    },config).then(function (res) {
      console.log(res);
      $scope.img_list.splice($scope.img_list.indexOf(item),1);
      if($scope.img_list==''){
        $scope.img_flag='请上传图片';
      }
    },function (err) {
      console.log(err);
    });
  };
  /*----------------------上传图片 结束-----------------------*/

  //判断系列名称是否存在
  $scope.name_flag=false;
  $scope.$watch('style_name',function(newVal,oldVal){
    for(let [key,value] of $scope.style_arr.entries()){
      if(newVal===value.style && newVal!=$scope.style_item.style){
        $scope.name_flag=true;
        return;
      }else {
        $scope.name_flag=false;
      }
    }
  });

  //确认按钮
  $scope.tran_arr=[];
	$scope.style_det_ok=function (valid) {
		  if(valid && !$scope.name_flag && $scope.img_list!=''){
        $scope.sur_id="suremodal";
        for(let[key,value] of $scope.style_label_arr.entries()){
          if(value.num!=''){
            $scope.tran_arr.push(value.num);//标签组
          }
        }
      }else {
        $scope.submitted = true;
      }
    //判断图片列表是否为空
    if($scope.img_list==''){
      $scope.img_flag='请上传图片';
    }
  };
  //跳转页面
  $scope.style_go=function () {
      $http.post(baseUrl+'/mall/style-edit',{
        id:+$scope.style_item.id,
        style:$scope.style_name,
        theme:$scope.tran_arr.join(','),
        intro:$scope.style_txt,
        images:$scope.img_list.join(',')
      },config).then(function (res) {
        console.log(res);
      },function (err) {
        console.log(err);
      });
    setTimeout(function () {
      $state.go("style_index",{showstyle:true,page:$scope.page});
    },300);
  }
});