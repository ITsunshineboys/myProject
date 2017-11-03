/**
 * Created by xl on 2017/8/7 0007.
 */
var shop_style= angular.module("freight_template",[])
    .controller("freight_template_ctrl",function ($scope,$http,$state,$stateParams ,$q) {
        $scope.hidden_way = true;
        $scope.airHidden = true;
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
        //城市选择  获取一级
        $http({
            method: "get",
            url: "city.json"
        }).then(function (response) {
            $scope.FirstCity = response.data[0]['86'];
            let arr = [];
            for (let [key,value] of Object.entries($scope.FirstCity)) {
                arr.push({'id': key, 'name': value,'flag':false})
            }
            $scope.province = arr;
        });

        //点击下拉按钮展开获取所有的二级市区
        $scope.getMoreCity = function (item) {
            arr1=[];
            $scope.cur_province = item;
            //firstArr = $scope.cur_province
            //获取二级城市
            $http.get('city.json').then(function (response) {
                for (let [key, value] of Object.entries(response.data[0][item.id])) {
                    arr1.push({'id': key, 'name': value,'flag':false})
                }
                $scope.city = arr1;
                console.log($scope.city);
                for(let [key,value] of $scope.city.entries()){
                    if(newArr.find(function(item){  //判断数组是否重复
                            return item.id == value.id
                        })
                        == undefined ){
                         console.log(0)
                    }else{
                        value.flag = true;
                    }
                }

            });
            console.log($scope.city);
        };

        $scope.getSecondId = function (item) {
            item.flag = !item.flag;

        };

        //当点击确定保存选择的二级 获取选择二级的状态
        $scope.getCity = function () {
            for(let [key,value] of $scope.city.entries()){
                let x = newArr.findIndex(function(item){  //判断数组是否重复
                    return item.id == value.id
                });
                if(x == -1 && value.flag == true){
                    newArr.push(value);
                }else if (x!== -1 && value.flag == false){
                    newArr.splice(x,1)
                }
            }
            console.log(newArr);
            //判断
            if($scope.city.find(function (item){
                    return item.flag == true
                }) !==undefined){
                $scope.cur_province.flag=true
            }else{
                $scope.cur_province.flag= false
            }
        };
        //点击确认获取已勾选的二级城市

        $scope.addCity = function () {
            let oldArrId = [];
            let oldArrName = [];
            console.log(oldArr);
            oldArr = newArr;
            for (let [key,value] of oldArr.entries()) {
                oldArrId.push(value.id);
                oldArrName.push(value.name)
            }
            $scope.chooseCity = oldArrName.join(',');
            $scope.chooseCityid = oldArrId.join(',');
            console.log(oldArrId);
            console.log($scope.chooseCityid);
        };
        //点击切换配送方式
        $scope.changeView = function (item) {
            $scope.test = item;
        };
        $scope.changeViewMore = function (item) {
            $scope.test = item;
        };
        // 确认编辑保存物流模板
        $scope.getReally = function () {
            if($scope.textContent !== undefined  ) {
                console.log(111122222)
                $scope.airHidden = true;
                $http({
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    transformRequest: function (data) {
                        return $.param(data)
                    },
                    method: 'POST',
                    url: 'http://test.cdlhzz.cn:888/mall/logistics-template-add',
                    data:{
                        name:$scope.textContent,
                        delivery_method:+$scope.test,
                        district_codes:$scope.chooseCityid,
                        delivery_cost_default:$scope.test    == 0 ? +$scope.cost*100:{},
                        delivery_cost_delta:$scope.test      == 0 ? +$scope.dete*100:{},
                        delivery_number_default:$scope.test  == 0 ? +$scope.ber:{},
                        delivery_number_delta:$scope.test    == 0 ? +$scope.num:{}
                    }
                }).then(function successCallback(response) {
                    console.log(response);
                    console.log(response.data.code);
                    $scope.repeat =response.data.code;
                    console.log(typeof (parseInt($scope.repeat)))
                    if (newArr.length > 0 && $scope.textContent !== undefined ){
                        $state.go('commodity_manage');
                        console.log(3333333)
                    }else{
                        alert("亲!确定填完！")
                    }

                });
            }else{
                console.log(2222);
                $scope.airHidden = false;
            }

        };
        //返回上一页
        $scope.getBack = function () {
            $state.go('commodity_manage')
        }

    });