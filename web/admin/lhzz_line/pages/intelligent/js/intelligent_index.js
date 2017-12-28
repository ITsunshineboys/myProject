app.controller('intelligent_index_ctrl',function (_ajax,$http,$scope,$rootScope) {
    //面包屑
    $rootScope.crumbs = [
        {
            name: '智能报价',
            icon: 'icon-baojia',
        }
    ]
    //城市信息
    $http.get('city.json').then(function (res) {
        console.log(res)
        let arr = res.data['0']['86']
        $scope.province_options = []//省
        $scope.city_options = {}//市
        //整合省
        for(let [key,value] of Object.entries(arr)){
            $scope.province_options.push({
                region_code:key,
                region_name:value
            })
        }
        //整合市
        for(let [key,value] of $scope.province_options.entries()){
            let arr = []
            for(let [key1,value1] of Object.entries(res.data['0'][value.region_code])){
                arr.push({
                    region_code:key1,
                    region_name:value1
                })
            }
            $scope.city_options[value.region_code] = arr
        }
        $scope.province = $scope.province_options[22].region_code
        $scope.city = $scope.city_options[$scope.province][0].region_code
        if(sessionStorage.getItem('area')!=null){
            let obj = JSON.parse(sessionStorage.getItem('area'))
            $scope.province =  obj.province
            $scope.city = obj.city
        }
        sessionStorage.setItem('area',JSON.stringify({
            province:$scope.province,
            city:$scope.city
        }))
    })
    $scope.$watch('province',function (newVal,oldVal) {
        if(oldVal!=undefined&&newVal!=undefined){
            $scope.city = $scope.city_options[newVal][0].region_code
            sessionStorage.setItem('area',JSON.stringify({
                province:newVal,
                city:$scope.city
            }))
        }
    },true)
    $scope.$watch('city',function (newVal,oldVal) {
        if(oldVal!=undefined&&newVal!=undefined){
            sessionStorage.setItem('area',JSON.stringify({
                province:$scope.province,
                city:newVal
            }))
        }
    },true)
})