
var banner_recommend = angular.module("banner_recommend_module",[]);
banner_recommend.controller("banner_recommend_ctrl",function ($scope,$http) {

  //选择城市开始
  $scope.second_title='';//二级列表项初始化
  $scope.three_title='';//三级列表项初始化
  $scope.ctrlScope = $scope;
  $scope.search_txt = '';
  //初始化省市区县;
  $http.get('districts2.json').then(function (response) {
    // console.log((response.data[0]['86']))
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
    $http.get('districts2.json').then(function (response) {
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

  let config = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: function (data) {
      return $.param(data)
    }
  };

  //后台推荐首页(admin)
  let recommend_url="http://test.cdlhzz.cn:888/mall/recommend-admin-index";
  let recommend_params={
    district_code:510100,
    type:0
  };
  $http.get(recommend_url,recommend_params).then(function (res) {
    console.log("后台推荐首页");
    console.log(res);
  },function (err) {
    console.log(err);
  });

  /**
   *--------------------------------------
   *   商家添加
   * -------------------------------------
   */

//商铺添加--获取按钮
  $scope.shop_rep=[]; //推荐--添加--商铺添加
  $scope.link_rep=[]; //推荐--添加--链接添加
  $scope.shop_check=0;//推荐--添加--商铺添加(是否启用) 默认为0 停用
  $scope.link_check=0;//推荐--添加--链接添加(是否启用) 默认为0 停用

  $scope.recommend_shop_add_get=function () {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:$scope.shop_model
      }
    }).then(function (res) {
      console.log(res);
      $scope.recommend_shop_url=res.data.data.detail.url; //商品链接
      $scope.recommend_shop_title=res.data.data.detail.title; //商品标题
      $scope.recommend_shop_subtitle=res.data.data.detail.subtitle; //商品副标题
      $scope.recommend_shop_platform_price=res.data.data.detail.platform_price; //平台价格
    },function (err) {
      console.log(err);
    });
  };

  //商铺添加--确认按钮
  $scope.recommend_shop_add_btn=function () {
    let url= 'http://test.cdlhzz.cn:888/mall/recommend-add';
    let params= {
      district_code:510100,
      url:$scope.recommend_shop_url,
      title:$scope.recommend_shop_title,
      image:"123.jgp",
      from_type:"1",
      type:0,
      sku:$scope.shop_model,
      description:"asdasdas",
      platform_price:$scope.recommend_shop_platform_price,
      status:$scope.shop_check
    };
    $http.post(url,params,config).then(function (response) {
      let myDate=new Date(); //假设的秒数
      let now_time=myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes();
      $scope.shop_rep.push({
        'title':$scope.recommend_shop_title,
        'check':$scope.shop_check==0?'已启用':"停用",
        'shop_types':'商家',
        'sku':$scope.shop_model,
        'now_time':now_time,
        'subtitle':$scope.recommend_shop_subtitle,
        'platform_price':$scope.recommend_shop_platform_price
      });
      console.log(response)
    },function (error) {
      console.log(error)
    })
  };

  /**
   *--------------------------
   *     商家编辑
   * -------------------------
   */


  //商家编辑获取按钮
  $scope.shop_edit_check=0;
  $scope.recommend_shop_edit_get=function () {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:$scope.shop_edit_model
      }
    }).then(function (res) {
      console.log("商家编辑获取按钮");
      console.log(res);
      $scope.recommend_shop_edit_url=res.data.data.detail.url; //商品链接
      $scope.recommend_shop_edit_title=res.data.data.detail.title; //商品标题
      $scope.recommend_shop_edit_subtitle=res.data.data.detail.subtitle; //商品副标题
      $scope.recommend_shop_edit_platform_price=res.data.data.detail.platform_price; //平台价格
    },function (err) {
      console.log(err);
    });
  };

  //商家点击编辑--按钮
  $scope.shop_edit_index=function (index) {
    $scope.edit_index=index;
  };
  //编辑确认按钮
  $scope.recommend_shop_edit=function () {
    let url= 'http://test.cdlhzz.cn:888/mall/recommend-edit';
    let params= {
      id:1,
      url:$scope.recommend_shop_edit_url,
      title:$scope.recommend_shop_edit_title,
      image:"123.jgp",
      from_type:"1",
      status:$scope.shop_edit_check,
      type:0,
      sku:$scope.shop_edit_model,
      platform_price:$scope.recommend_shop_edit_platform_price
    };
    $http.post(url,params,config).then(function (response) {
      let myDate=new Date(); //假设的秒数
      let now_time=myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes();
      $scope.shop_rep.splice($scope.edit_index,1,{
        'title':$scope.recommend_shop_edit_title,
        'check':$scope.shop_edit_check==0?'已启用':"停用",
        'shop_types':'商家',
        'sku':$scope.shop_edit_model,
        'now_time':now_time
      });
      console.log("shop_rep数组为");
      console.log($scope.shop_rep);
    },function (error) {
      console.log(error)
    });
  };


  /**
   *--------------------------
   * 商家详情
   * -------------------------
   */

  $scope.shop_details=function (item) {
    $http({
      method:"GET",
      url:"http://test.cdlhzz.cn:888/mall/goods-by-sku",
      params:{
        sku:item.sku
      }
    }).then(function (res) {
      $scope.shop_details_subtitle=res.data.data.detail.subtitle;
      $scope.shop_details_platform_price=res.data.data.detail.platform_price;
    },function (err) {
      console.log(err);
    });
    $scope.shop_details_title=item.title;
    $scope.shop_details_subtitle='';
    $scope.shop_details_types=item.shop_types;
    $scope.shop_details_sku=item.sku;
    $scope.shop_details_time=item.now_time;
  }

  /**
   *--------------------------
   *商家删除
   * -------------------------
   */
  //商家单个删除
  //点击删除按钮
  $scope.shop_del=function(index){
    $scope.del_ok_index=index;
  };
  //确认删除
  $scope.shop_del_ok=function () {
    $scope.shop_rep.splice($scope.del_ok_index,1);
  };


  /**
   *-----------------------------------
   *  链接添加
   * ----------------------------------
   */

  //链接添加--确认按钮
  $scope.recommend_link=function () {
    let url= 'http://test.cdlhzz.cn:888/mall/recommend-add';
    let params= {
      district_code:510100,
      url:$scope.recommend_link_url,
      title:$scope.recommend_link_title,
      image:"123.jgp",
      from_type:"2",
      status:$scope.link_check,
      type:2,
      description:"描述",
      platform_price:$scope.recommend_link_platform_price
    };
    $http.post(url,params,config).then(function (response) {
      let myDate=new Date(); //假设的秒数
      let now_time=myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes();
      $scope.link_rep.push({
        'link_link':$scope.recommend_link_url,//链接
        'link_title':$scope.recommend_link_title,//标题
        'link_subtitle':$scope.recommend_link_subtitle,//副标题
        'check':$scope.link_check==0?'已启用':"停用",
        'ling_show_price':$scope.recommend_link_show_price,//显示价格
        'link_types':'链接',
        'now_time':now_time
      });
      console.log(response)
    },function (error) {
      console.log(error)
    })
  };
});



//点击切换


  function tabChangeFn(my) {
    var myTab=document.getElementsByClassName("myTab")[0];
    var tabChange=document.getElementsByClassName("tabChange");
    for(var i=0;i<myTab.children.length;i++){
      myTab.children[i].style.color="#666F80";
      myTab.children[i].style.borderBottom="1px solid white";
    }
    my.style.color="#3F94F6";
    my.style.borderBottom="1px solid #3F94F6";
  }
  function a(a) {
    a.style.color="red"
  }


