var choose_style = angular.module("choose_styleModule",[]);
choose_style.controller("choose_style",function ($scope,$http,$state,_ajax,$rootScope) {
	$scope.back_index=function () {
    $state.go("style_index",{showstyle:true});
  }
  $rootScope.crumbs = [{
    name: '商城管理',
    icon: 'icon-shangchengguanli',
    link: $rootScope.mall_click
  }, {
    name: '系列/风格/属性管理',
    link: $scope.back_index
  }, {
    name: '选择风格'
  }];
	$scope.showstyle = true
	//风格管理
	$scope.item_check = [];
	//获取一级
  _ajax.get('/mall/categories', {}, function (res) {
    $scope.details = res.data.categories;
    $scope.oneColor = $scope.details[0];
  })
	//获取二级
  _ajax.get('/mall/categories', {pid: 1}, function (res) {
    if (res.data.categories.length>0) {
      $scope.second = res.data.categories;
      $scope.twoColor = $scope.second[0];
      //获取三级
      _ajax.get('/mall/categories', {pid: 2}, function (res) {
        $scope.three = res.data.categories;
        for (let [key, value] of  Object.entries($scope.three)) {
          if ($scope.item_check.length == 0) {
            value.complete = false
          } else {
            for (let [key1, value1] of $scope.item_check.entries()) {
              if (value.id == value1.id) {
                value.complete = true
              }
            }
          }
        }
      })
    } else {
      $scope.second = []
      $scope.three = []
    }
  })

	//点击一级 获取相对应的二级
  $scope.getMore = function (n) {
    $scope.oneColor = n;
    _ajax.get('/mall/categories', {pid: n.id}, function (res) {
      if (res.data.categories.length>0) {
        $scope.second = res.data.categories;
        $scope.twoColor = $scope.second[0];
        _ajax.get('/mall/categories', {pid: $scope.second[0].id}, function (res) {
          $scope.three = res.data.categories;
          for (let [key, value] of $scope.three.entries()) {
            if ($scope.item_check.length == 0) {
              value.complete = false
            } else {
              for (let [key1, value1] of $scope.item_check.entries()) {
                if (value.id == value1.id) {
                  value.complete = true
                }
              }
            }
          }
        })
      } else {
        $scope.second = []
        $scope.three = []
      }
    })
  };
	//点击二级 获取相对应的三级
    $scope.three = []
  $scope.getMoreThree = function (n) {
    $scope.id = n;
    $scope.twoColor = n;
    _ajax.get('/mall/categories',{pid:n.id},function (res) {
      if (res.data.categories.length>0) {
        $scope.three = res.data.categories;
        for (let [key, value] of $scope.three.entries()) {
          if ($scope.item_check.length == 0) {
            value.complete = false
          } else {
            for (let [key1, value1] of $scope.item_check.entries()) {
              if (value.id == value1.id) {
                value.complete = true
              }
            }
          }
        }
      } else {
        $scope.three = []
      }
    })
  };

	//添加拥有系列的三级
  $scope.check_item = function (item) {
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
      $scope.item_check.unshift(item);
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
  };
	//模态框确认按钮保存数据发送后台
	$scope.send_series = function(){
		let obj = {};
		for(let [key,value] of $scope.item_check.entries()){
			if(value.pid in obj){
				obj[value.pid+''].push(+value.id)
			}else{
				obj[value.pid+''] = [+value.id]
			}
		}
		//发送分类所拥有的系类分类、
    _ajax.post('/mall/categories-style-series-reset',{
      category_ids: obj,
      type: 'style'
    },function (res) {
      console.log(res)
    })
	};

	$scope.back_return =function () {
		setTimeout(function () {
            $state.go("style_index",{showstyle:true});
		},300)

	}
	//默认进页面获取三级分类所具有的系类
  _ajax.get('/mall/categories-have-style-series',{type:'style'},function (res) {
    console.log(res);
    $scope.item_check = res.data.have_style_series_categories;
    for (let [key, value] of Object.entries($scope.three)) {
      if ($scope.item_check.length == 0) {
        value.complete = false
      } else {
        for (let [key1, value1] of $scope.item_check.entries()) {
          if (value.id == value1.id) {
            value.complete = true
          }
        }
      }
    }
  })
});