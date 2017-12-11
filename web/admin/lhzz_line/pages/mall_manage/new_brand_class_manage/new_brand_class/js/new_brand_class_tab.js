app.controller("new_brand_class_tab_ctrl",function ($rootScope,$scope,$http,_ajax,$state,Upload,$location,$anchorScroll,$window,) {
    $rootScope.crumbs = [{
      name: '商城管理',
      icon: 'icon-shangchengguanli',
      link: $rootScope.mall_click
    }, {
      name: '新品牌/新分类审核'
    }];
  });