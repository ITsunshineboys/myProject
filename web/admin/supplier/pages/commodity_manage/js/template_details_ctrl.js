/**
 * Created by xl on 2017/8/7 0007.
 */
var shop_style= angular.module("template_details",[])
    .controller("template_details_ctrl",function ($scope,$http,$state,$stateParams) {
        $scope.id = $stateParams.id;
        $scope.name = $stateParams.name;
        $scope.name_input =$scope.name;
        $scope.statusView ='';
        $scope.hidden_way = true;
        //查看物流模板开始
        $http({
            method: "get",
            url: baseUrl+"/mall/logistics-template-view",
            params: {
                id:+$scope.id
            }
        }).then(function (response) {
            $scope.teplateView = response.data.data.logistics_template;
            $scope.radioView = response.data.data.logistics_template.delivery_method;
            if($scope.radioView=="快递物流"){
                $scope.test = 0;
            }else{
                $scope.test = 1;
            }
            $scope.costView = response.data.data.logistics_template.delivery_cost_default;
            $scope.detaeView = response.data.data.logistics_template.delivery_cost_delta;
            $scope.numView = response.data.data.logistics_template.delivery_number_default;
            $scope.berView = response.data.data.logistics_template.delivery_number_delta;
            $scope.district_names = response.data.data.logistics_template.district_names;
            console.log($scope.district_names);
            $scope.cost = $scope.costView;
            $scope.num = $scope.numView;
            $scope.ber = $scope.berView;
            $scope.dete = $scope.detaeView;
            $scope.citys = $scope.district_names.join(',').slice(0,20);
            if ($scope.costView == 0 && $scope.detaeView == 0 && $scope.numView == 0 && $scope.berView == 0) {
                $scope.hidden_way = false;
            }else {
                $scope.hidden_way = true;
            }
        });
        //点击收费方式的切换
        $scope.changeStatus = function () {
            $scope.hidden_way = true;
        };
        $scope.changeStatusHidden = function () {
            $scope.hidden_way = false;
        };
        let newArr = [];
        let oldArr = [];
        let arr1 = [];
        //获取一级城市选择
        $http({
            method: "get",
            url: "city.json"
        }).then(function (response) {
            $scope.FirstCity = response.data[0]['86'];
            let arr = [];
            for (let [key,value] of Object.entries($scope.FirstCity)) {
                arr.push({'id': key, 'name': value ,'flag': false})
            }
            $scope.province = arr;
        });

        //点击下拉按钮展开获取所有的二级市区
        $scope.getMoreCity = function (item) {
            //$scope.district_names =newArr;
            console.log(newArr);
            arr1 = [];
            $scope.cur_province = item;
            //获取二级城市
            $http.get('city.json').then(function (response) {
                for (let [key, value] of Object.entries(response.data[0][item.id])) {
                   arr1.push({'id': key, 'name': value ,'flag' : false})
                }
                   $scope.city = arr1;
                   console.log($scope.city);
                //判断newArr和某个一级的全部二级的状态
                    for( let [key,value] of $scope.city.entries()) {
                        if(newArr.find(function (item){
                                return item.id == value.id
                            }) == undefined){
                            console.log(0)
                        }else{
                            value.flag = true;
                        }
                    }
                })
            };

        //点击勾选二级改变二级城市的状态
        $scope.changeId = function (item) {
            item.flag = !item.flag;
        };
         //点击二级确定按钮保存已选中的二级 获取选择的状态
        $scope.getCity = function () {
             for (let [key,value] of $scope.city.entries()) {
                 let x = newArr.findIndex(function (item) {
                     return item.id == value.id
                 });
                 if( x == -1 && value.flag == true){
                      newArr.push(value);
                 }else if (x == -1 && value.flag == false){
                      newArr.splice(x,1);
                 }
             }
            console.log(newArr);
            //判断
            if($scope.city.find(function (item){
                    return item.flag == true
                }) !== undefined) {
                $scope.cur_province.flag = true
            }else {
                $scope.cur_province.flag = false
            }
        };
        //点击一级确定获取选择城市   并渲染到页面传给后台
        $scope.addCity = function (item) {
            let oldArrId = [];
            let oldArrName = [];
            oldArr = newArr;
            for (let [key,value] of oldArr.entries()) {
                oldArrName.push(value.name);
                oldArrId.push(value.id)
            }
            $scope.citys = oldArrName.join(',');
            $scope.chooseCityId = oldArrId.join(',');
            console.log($scope.citys);
            console.log($scope.chooseCityId)
        };

        //点击切换配送方式
        $scope.changeView = function (item) {
            $scope.test = item;
        };
        $scope.changeViewMore = function (item) {
            $scope.test = item;
        };
         // 最后确认编辑保存物流模板
           $scope.getReally = function () {
               $http({
                   headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                   transformRequest: function (data) {
                       return $.param(data)
                   },
                   method: 'POST',
                   url: baseUrl+'/mall/logistics-template-edit',
                   data:{
                       id:+$scope.id,
                       name:$scope.name_input,
                       delivery_method:$scope.test,
                       district_codes: $scope.chooseCityId,
                       delivery_cost_default:$scope.test == 0 ? $scope.cost*100:{},
                       delivery_cost_delta:$scope.test == 0 ? $scope.dete*100:{},
                       delivery_number_default:$scope.test == 0 ? $scope.num:{},
                       delivery_number_delta:$scope.test == 0 ? $scope.ber:{}
                   }
               }).then(function successCallback(response) {
                   console.log(response);
                   if ( $scope.textContent !== undefined){
                       $state.go('commodity_manage')
                   }else{
                       alert("亲!填完在确定")
                   }
               });
           };
        //返回上一页
           $scope.getBack = function () {
                $state.go('commodity_manage',{logistics_flag:true})
           }
    });
