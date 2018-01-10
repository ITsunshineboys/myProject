app.controller('product_list_ctrl',function (_ajax,$scope,$state,$stateParams,$timeout) {
    //获取头部名称
    $scope.header_title = $stateParams.title
    //商品列表部分
    /*分页配置*/
    $scope.Config = {
        showJump: true,
        itemsPerPage: 12,
        currentPage: 1,
        onChange: function () {
            tablePages();
        }
    };
    let tablePages = function () {
        console.log($scope.params);
        _ajax.get('/mall/category-goods', $scope.params, function (res) {
            console.log(res);
            $scope.filter_material = [];
            for (let [key, value] of res.data.category_goods.entries()) {
                $scope.filter_material.push({
                    id: value.id,
                    cover_image: value.cover_image,
                    goods_name: value.title,
                    favourable_comment_rate: value.favourable_comment_rate,
                    sold_number: value.sold_number,
                    platform_price: value.platform_price,
                    subtitle: value.subtitle,
                })
            }
            $scope.Config.totalItems = $scope.filter_material.length
        })
    };
    $scope.params = {
        category_id: $stateParams.category_id,
        platform_price_min: '',
        platform_price_max: '',
        'sort[]': 'sold_number:3',
        brand_id: '',
        style_id: '',
        series_id: ''
    };
    $scope.style_arr = []//风格选择
    $scope.series_arr = []//系列选择
    $scope.brand_arr = []//品牌选择
    //请求风格、系列以及品牌
    _ajax.get('/mall/category-brands-styles-series', {
        category_id: $scope.params.category_id,
    }, function (res) {
        console.log(res)
        $scope.goods_series = res.data.category_brands_styles_series.series
        $scope.goods_style = res.data.category_brands_styles_series.styles
        $scope.goods_brands = res.data.category_brands_styles_series.brands
    })
    tablePages()
    //商品排序
    $scope.goods_sort = function (str) {
        if(str == 'sold_number'){
            $scope.params["sort[]"] = 'sold_number:3'
        }else if(str == 'platform_price'){
            if($scope.params["sort[]"].indexOf('platform_price') == -1){
                $scope.params["sort[]"] = 'platform_price:4'
            }else{
                $scope.params["sort[]"] = str + ($scope.params["sort[]"] == 'platform_price:3'?':4':':3')
            }
        }else if(str == 'favourable_comment_rate'){
            if($scope.params["sort[]"].indexOf('favourable_comment_rate') == -1){
                $scope.params["sort[]"] = 'favourable_comment_rate:4'
            }else{
                $scope.params["sort[]"] = str + ($scope.params["sort[]"] == 'favourable_comment_rate:3'?':4':':3')
            }
        }
        tablePages()
    }
    //风格、系列以及品牌选择
    $scope.filterGoods = function (str,item) {
        if(str === 'style'){
            if($scope.style_arr.indexOf(item.id) == -1){
                $scope.style_arr.push(item.id)
            }else{
                $scope.style_arr.splice($scope.style_arr.indexOf(item.id),1)
            }
        }else if(str === 'series'){
            if($scope.series_arr.indexOf(item.id) == -1){
                $scope.series_arr.push(item.id)
            }else{
                $scope.series_arr.splice($scope.series_arr.indexOf(item.id),1)
            }
        }else if(str === 'brand'){
            if($scope.brand_arr.indexOf(item.id) == -1){
                $scope.brand_arr.push(item.id)
            }else{
                $scope.brand_arr.splice($scope.brand_arr.indexOf(item.id),1)
            }
        }
    }
    //完成筛选
    $scope.completeFilter = function () {
        $scope.params.style_id = $scope.style_arr.join(',')
        $scope.params.series_id = $scope.series_arr.join(',')
        $scope.params.brand_id = $scope.brand_arr.join(',')
        tablePages()
    }
    //价格区间
    $scope.choosePrice = function (str) {
        if ($scope.params[str] != '') {
            if (str === 'platform_price_min') {
                if($scope.params.platform_price_max!=''&&+$scope.params[str] > +$scope.params.platform_price_max){
                    let num = $scope.params.platform_price_max
                    $scope.params.platform_price_max = $scope.params[str]
                    $scope.params[str] = num
                }
            } else if (str === 'platform_price_max') {
                if($scope.params.platform_price_min!=''&&+$scope.params[str] < +$scope.params.platform_price_min){
                    let num = $scope.params.platform_price_min
                    $scope.params.platform_price_min = $scope.params[str]
                    $scope.params[str] = num
                }
            }
        }
    }
    //跳转详情页
    $scope.goDetails = function (item) {
        $timeout(function () {
            $state.go('product_details',{index:$stateParams.index,status:$stateParams.status,id:item.id,replace_id:$stateParams.id,title:$stateParams.title})
        },300)
    }
    //返回上一页
    $scope.goPrev = function () {
        history.go(-1)
    }
})