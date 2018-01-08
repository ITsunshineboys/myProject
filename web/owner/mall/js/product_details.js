app.controller('product_details_ctrl', function ($timeout,_ajax, $scope, $state, $stateParams) {
    //初始化
    $scope.status = $stateParams.status
    $scope.materials = JSON.parse(sessionStorage.getItem('copies'))
    //获取详情数据
    _ajax.get('/mall/goods-view', {
        id: $stateParams.id
    }, function (res) {
        console.log('商品详情');
        console.log(res);
        let arr = [], arr1 = []
        for (let [key, value] of res.data.goods_view.after_sale_services.entries()) {
            if (value == '提供发票' || value == '上门安装') {
                arr.push(value)
            } else {
                arr1.push(value)
            }
        }
        $scope.goods_detail = {//商品详情
            id:res.data.goods_view.id,//商品id
            path:res.data.goods_view.category_path,//分类path
            title:$stateParams.title,//三级分类名称
            purchase_price_decoration_company:res.data.goods_view.purchase_price_decoration_company,//供货商价
            images: res.data.goods_view.images,//轮播图
            goods_name: res.data.goods_view.title,//商品名
            subtitle: res.data.goods_view.subtitle,//商品特色
            platform_price: res.data.goods_view.platform_price,//平台价
            sale_services: res.data.goods_view.after_sale_services,//服务条款
            protection: arr,//保障
            aftermarket: arr1,//售后
            cover_image: res.data.goods_view.cover_image,//商品图
            left_number: res.data.goods_view.left_number,//库存
            description: res.data.goods_view.description,//描述
            sku: res.data.goods_view.sku,//产品编码
            brand_name: res.data.goods_view.brand_name,//品牌名
            series_name: res.data.goods_view.series_name,//系列名
            style_name: res.data.goods_view.style_name,//风格名
            attrs: res.data.goods_view.attrs,//属性
            quantity:1//数量
        }
        $scope.shop_detail = {//店铺详情
            icon: res.data.goods_view.supplier.icon,//店铺头像
            shop_name: res.data.goods_view.supplier.shop_name,//店铺名
            goods_number: res.data.goods_view.supplier.goods_number,//商品数
            comprehensive_score: res.data.goods_view.supplier.comprehensive_score,//综合详情
        }
    })
    //改变数量
    $scope.changeQuantity = function (flag) {
        if (flag == 1) {
            if($scope.goods_detail.quantity >= $scope.goods_detail.left_number){
                $scope.goods_detail.quantity = $scope.goods_detail.left_number
            }else{
                $scope.goods_detail.quantity ++
            }
        } else if(flag == 0) {
            if($scope.goods_detail.quantity <= 1){
                $scope.goods_detail.quantity = 1
            }else{
                $scope.goods_detail.quantity --
            }
        }else{
            if($scope.goods_detail.quantity!==''){
                if($scope.goods_detail.quantity == 0){
                    $scope.goods_detail.quantity = 1
                }else if(+$scope.goods_detail.quantity > +$scope.goods_detail.left_number){
                    $scope.goods_detail.quantity = $scope.goods_detail.left_number
                }
            }
        }
    }
    //更换或者添加商品
    $scope.getGoods = function () {
        if($scope.status == 1){
            for(let [key,value] of $scope.materials.entries()){
                for(let [key1,value1] of value.second_level.entries()){
                    let index = value1.goods.findIndex(function (item) {
                        return item.id == $stateParams.replace_id
                    })
                    if(index!=-1){
                        value.cost += -value1.goods[index].cost + $scope.goods_detail.platform_price * $scope.goods_detail.quantity
                        value1.cost += -value1.goods[index].cost + $scope.goods_detail.platform_price * $scope.goods_detail.quantity
                        value.procurement += -value1.goods[index].procurement + $scope.goods_detail.purchase_price_decoration_company * $scope.goods_detail.quantity
                        value1.procurement += -value1.goods[index].procurement + $scope.goods_detail.purchase_price_decoration_company * $scope.goods_detail.quantity
                        value1.goods.splice(index,1,{
                            left_number:$scope.goods_detail.left_number,
                            sku:$scope.goods_detail.sku,
                            id:$scope.goods_detail.id,
                            category_id:$scope.goods_detail.path.split(',')[2],
                            platform_price:$scope.goods_detail.platform_price,
                            purchase_price_decoration_company:$scope.goods_detail.purchase_price_decoration_company,
                            name:$scope.goods_detail.brand_name,
                            title:$stateParams.title,
                            series_name:$scope.goods_detail.series_name,
                            style_name:$scope.goods_detail.style_name,
                            subtitle:$scope.goods_detail.subtitle,
                            path:$scope.goods_detail.path,
                            cover_image:$scope.goods_detail.cover_image,
                            quantity:$scope.goods_detail.quantity,
                            goods_name:$scope.goods_detail.goods_name,
                            shop_name:$scope.shop_detail.shop_name,
                            cost:$scope.goods_detail.platform_price * $scope.goods_detail.quantity,
                            procurement:$scope.goods_detail.quantity * $scope.goods_detail.purchase_price_decoration_company
                        })
                        sessionStorage.setItem('copies',JSON.stringify($scope.materials))
                        if($stateParams.index == 1){
                            $timeout(function () {
                                $state.go('main_materials',{index:$stateParams.index})
                            },300)
                        }else{

                        }
                    }
                }
            }
        }else{

        }
    }
    //返回前一页
    $scope.goPrev = function () {
        history.go(-1)
    }
})
    .filter("toHtml", ["$sce", function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        }
    }]);