app.controller('nodata_ctrl',function ($http,_ajax,$state,$scope,$anchorScroll,$location,$q) {
    $scope.vm = $scope
    //基本信息
    $scope.params = {
        bedroom: 1,//卧室
        area: 60,      //面积
        hall: 1,       //餐厅
        toilet: 1,   // 卫生间
        kitchen: 1,  //厨房
        series: '',   //系列
        style: '',  //风格
        window: 0,//飘窗
        high: '', //层高
        province: 510000,   //省编码
        city: 510100,      // 市编码
        stairway_id: 0,//有无楼梯
        stairs: 0//楼梯结构
    }
    //层高信息
    $scope.high = [2.8,3,3.3,4.5]
    $scope.params.high = $scope.high[0]
    if(sessionStorage.getItem('params')!=null){
        $scope.params = JSON.parse(sessionStorage.getItem('params'))
    }
    //风格、系列以及楼梯信息
    _ajax.get('/owner/series-and-style',{},function (res) {
        console.log(res);
        $scope.series = res.data.show.series//系列
        $scope.style = res.data.show.style//风格
        $scope.stairs = res.data.show.stairs_details//楼梯
        if(sessionStorage.getItem('params')==null) {
            $scope.params.series = $scope.series[0].id
            $scope.params.style = $scope.style[0].id
        }
        $scope.cur_series = $scope.series[0]
        $scope.cur_style = $scope.style[0]
    })
    //小区信息
    $scope.toponymy = {
        name:'',
        address:''
    }
    if(sessionStorage.getItem('toponymy')!=null){
        $scope.toponymy = JSON.parse(sessionStorage.getItem('toponymy'))
    }
    /*存基本信息sessionStorage*/
    $scope.$watch('params',function(newVal,oldVal){
        sessionStorage.setItem('params',JSON.stringify(newVal))
    },true)
    $scope.$watch('toponymy',function (newVal,oldVal) {
        sessionStorage.setItem('toponymy',JSON.stringify(newVal))
    },true)
    //改变室厅厨卫
    $scope.changeQuantity = function (str,flag,limit) {
        if(flag == 1){
            if($scope.params[str.split('.')[1]] >= limit){
                $scope.params[str.split('.')[1]] = limit
            }else{
                $scope.params[str.split('.')[1]] ++
            }
        }else{
            if($scope.params[str.split('.')[1]] <= limit){
                $scope.params[str.split('.')[1]] = limit
            }else{
                $scope.params[str.split('.')[1]] --
            }
        }
    }
    //生成材料
    $scope.getMaterials = function (valid,error) {
        $q.all([
            (function () {
                return _ajax.get('/owner/electricity',$scope.params,function (res) {
                    console.log('强弱电');
                    console.log(res);
                })
            })(),
            (function () {
                return  _ajax.get('/owner/waterway', $scope.params, function (res) {
                    console.log('水路');
                    console.log(res);
                    // $scope.materials = res.data
                })
            })()
        ]).then(function () {
            console.log($scope.materials);
        })
        // if(valid){
        //
        // }else{
            // $scope.submitted = true
            // for (let [key, value] of error.entries()) {
            //     if (value.$invalid) {
            //         $anchorScroll.yOffset = 300
            //         $location.hash(value.$name)
            //         $anchorScroll()
            //         break
            //     }
            // }
        // }
    }
})