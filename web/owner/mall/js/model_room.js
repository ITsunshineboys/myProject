/**
 * Created by xl on 2017/6/29 0029.
 */
app.controller("modelRoomCtrl", ["$scope", "$timeout", "$state", "$stateParams", "_ajax", function ($scope, $timeout, $state, $stateParams, _ajax) {
    $scope.activeStatus = "";   // 户型选中状态值
    $scope.activeObj = {        // 选中的户型参数
        id: "",
        high: 0,     // 层高
        window: 0   // 飘窗
    };
    $scope.stairsList = [];     // 楼梯列表
    $scope.seriesList = [];     // 系列列表
    $scope.seriesDesc = {       // 系列描述
        intro: "",
        theme: ""
    };
    $scope.styleList = [];      // 风格列表
    $scope.demand = {           // 特殊要求
        text: ""
    };
    $scope.params = {
        code: $stateParams.roomCode,            // 区编码
        toponymy: $stateParams.toponymy,    // 小区名称
        street: $stateParams.street         // 街道地址
    };
    let huxingParams = {
        roomCode: $stateParams.roomCode,
        toponymy: $stateParams.toponymy,
        street: $stateParams.street
    };
    sessionStorage.setItem("huxingParams", JSON.stringify(huxingParams));

    let params = {
        toponymy: $scope.params.toponymy,    // 小区名称
        particulars: "",                    // 厅室名称
        area: "",                           // 面积
        stairway: "",                       // 有无楼梯
        stair_id: "",                       // 楼梯信息 ID
        series: "",                         // 系列ID
        style: "",                          // 风格ID
    };

    // 样板间信息
    _ajax.get("/owner/case-list", $scope.params, function (res) {
        console.log(res, "样板间");
        let data = res.data;
        $scope.huxing = data;
        for (let i of data) {
            if (i.type === '1') {
                $scope.activeObj = angular.copy(i);
                params.particulars = i.particulars;
                params.area = i.area;
                params.stairway = i.stairway;
                if (i.stairway === "1") {
                    params.stair_id = i.stair_id;
                } else {
                    params.stair_id = ""
                }
                params.series = i.case_picture[0].series_id;
                params.style = i.case_picture[0].style_id;
                break;
            }
        }
        materials();
    });

    // 楼梯、系列和风格数据
    _ajax.get("/owner/series-and-style", {}, function (res) {
        console.log(res, "楼梯、风格和系列");
        let data = res.data;
        $scope.stairsList = data.show.stairs_details;
        $scope.seriesList = data.show.series;
        $scope.styleList = data.show.style;
        $scope.seriesDesc = {
            intro: data.show.series[0].intro,
            theme: data.show.series[0].theme
        };
    });

    // 材料分类
    $scope.materials = [];
    _ajax.get("/owner/classify", {}, function (res) {
        console.log(res, "材料分类");
        let data = res.data;
        let tempArray = data.pid.stair;
        let primary = [];
        let others = [];
        for (let i of tempArray) {
            i.goods = [];
            i.totalMoney = 0;
            i.second_level = [];
            if (i.title === "辅材" || i.title === "主要材料") {
                primary.push(i);
            } else {
                others.push(i);
            }
        }
        $scope.classData = data.pid;    // 材料分类;
        $scope.materials = primary.concat(others);
        sessionStorage.setItem("materials_bak", JSON.stringify($scope.materials));
    });

    // 户型选择
    let huxingFlag = true;
    $scope.huxingFun = function (obj) {
        $scope.activeObj = angular.copy(obj);
        params.particulars = obj.particulars;
        params.area = obj.area;
        if (obj.type === '1') {
            if (obj.stairway === "1") {
                params.stairway = obj.stairway;
                params.stair_id = obj.stair_id;
            } else {
                params.stairway = obj.stairway;
                params.stair_id = "";
            }
            params.series = obj.case_picture[0].series_id;
            params.style = obj.case_picture[0].style_id;
            if (huxingFlag) {
                huxingFlag = false;
                materials();
            }
        } else {
            if (obj.stairway === "1") {
                params.stairway = obj.stairway;
                $scope.activeObj.stair_id = angular.copy($scope.stairsList[0].id);
                params.stair_id = $scope.stairsList[0].id;
            } else {
                params.stairway = obj.stairway;
                params.stair_id = "";
            }
            let tempArray = [{
                series_id: $scope.seriesList[0].id,
                style_id: $scope.styleList[0].id
            }];
            $scope.activeObj.case_picture = angular.copy(tempArray);
            params.series = $scope.seriesList[0].id;
            params.style = $scope.styleList[0].id;
        }
    };

    // 楼梯选择
    $scope.stairsFun = function (obj) {
        $scope.activeObj.stair_id = angular.copy(obj.id);
        params.stair_id = obj.id;
        materials();
    };

    // 系列选择
    $scope.seriesFun = function (obj) {
        $scope.activeObj.case_picture[0].series_id = angular.copy(obj.id);
        $scope.seriesDesc = {
            intro: obj.intro,
            theme: obj.theme
        };
        params.series = obj.id;
        materials();
    };

    // 风格选择
    $scope.styleFun = function (obj) {
        $scope.activeObj.case_picture[0].style_id = angular.copy(obj.id);
        params.style = obj.id;
        materials();
    };

    // 申请样板间
    $scope.payDeposit = function () {
        let activeObj = $scope.activeObj;
        let payParams = {
            province_code: activeObj.province_code,                     // 省编码
            city_code: activeObj.city_code,                             // 市级编码
            district_code: activeObj.district_code,                     // 区级编码
            bedroom: activeObj.bedroom,                                 // 卧室
            toilet: activeObj.toilet,                                   // 卫生间
            kitchen: activeObj.kitchen,                                 // 厨房
            high: activeObj.high,                                       // 高度
            window: activeObj.window,                                   // 窗户
            sittingRoom_diningRoom: activeObj.sittingRoom_diningRoom,   // 客厅过道
            area: activeObj.area,                                       // 面积
            phone: "",                                                  // 电话号码
            name: "",                                                   // 姓名
            requirement: $scope.demand.text,                            // 特殊要求
            toponymy: activeObj.toponymy,                               // 小区名称
            series: activeObj.case_picture[0].series_id,                // 系列
            style: activeObj.case_picture[0].style_id,                  // 风格
            street: activeObj.street,                                   // 街道
            particulars: activeObj.particulars,                         // 楼层详情
            stairway: activeObj.stairway,                               // 楼梯信息
            stair_id: activeObj.stair_id,                               // 楼梯材料id
            original_price: $scope.price,                               // 原价
            sale_price: $scope.preferential,                            // 打折价
            material: [],                                               // 商品信息
        };
        for (let obj of $scope.materials) {
            for (let o of obj.goods) {
                let tempObj = {
                    goods_id: o.goods_id,
                    count: o.quantity,
                    price: o.cost,
                    first_cate_id: obj.id
                };
                payParams.material.push(tempObj);
            }
        }
        sessionStorage.setItem("payParams", JSON.stringify(payParams));
        $state.go("deposit");
    };

    // 修改材料
    $scope.editMaterial = function (index) {
        let huxing = {
            area: $scope.activeObj.area,
            series: $scope.activeObj.case_picture[0].series_id,
            style: $scope.activeObj.case_picture[0].series_id
        };
        console.log($scope.materials);
        for (let material of $scope.materials) {
            for (let goods of material.goods) {
                for (let second of $scope.classData.level) {
                    // 遍历二级分类 判断二级标题是否相等
                    if (second.title === goods.goods_second) {
                        let temp = {
                            id: second.id,
                            title: second.title,
                            three_level: []
                        };
                        // 二级分类数组为0，则直接添加
                        if (material.second_level.length === 0) {
                            material.second_level.push(temp);
                            let tempLevel = {
                                id: goods.category_id,
                                title: goods.goods_three,
                                goods_detail: []
                            };
                            if (goods.goods_first === "辅材") {
                                tempLevel = {
                                    id: goods.effect_id,
                                    title: goods.goods_three,
                                    goods_detail: []
                                }
                            }
                            material.second_level[0].three_level.push(tempLevel);
                            material.second_level[0].three_level[0].goods_detail.push(goods);
                        } else {
                            let flag = true;
                            // 遍历二级分类数组
                            for (let secondLevel of material.second_level) {
                                // 若已有相同二级分类，则不做添加
                                if (secondLevel.id === second.id) {
                                    flag = false;
                                    let bool = true;
                                    for (let three of secondLevel.three_level) {
                                        // 判断三级分类是否相同,若相同则添加商品
                                        if (three.title === goods.goods_three) {
                                            bool = false;
                                            three.goods_detail.push(goods);
                                            break;
                                        }
                                    }
                                    if (bool) {
                                        let tempLevel = {
                                            id: goods.category_id,
                                            title: goods.goods_three,
                                            goods_detail: []
                                        };
                                        if (goods.goods_first === "辅材") {
                                            tempLevel = {
                                                id: goods.effect_id,
                                                title: goods.goods_three,
                                                goods_detail: []
                                            }
                                        }
                                        secondLevel.three_level.push(tempLevel);
                                        secondLevel.three_level[secondLevel.three_level.length - 1].goods_detail.push(goods);
                                    }
                                    break;
                                }
                            }
                            if (flag) {
                                material.second_level.push(temp);
                                let tempLevel = {
                                    id: goods.category_id,
                                    title: goods.goods_three,
                                    goods_detail: []
                                };
                                if (goods.goods_first === "辅材") {
                                    tempLevel = {
                                        id: goods.effect_id,
                                        title: goods.goods_three,
                                        goods_detail: []
                                    }
                                }
                                material.second_level[material.second_level.length - 1].three_level.push(tempLevel);
                                material.second_level[material.second_level.length - 1].three_level[0].goods_detail.push(goods);
                            }
                        }
                        break
                    }
                }
            }
        }

        sessionStorage.setItem("huxing", JSON.stringify(huxing));
        sessionStorage.setItem("materials", JSON.stringify($scope.materials));
        switch (index) {
            case 0:
                $state.go('nodata.basics_decoration');
                break;
            case 1:
                $state.go('nodata.main_material');
                break;
            default:
                $state.go('nodata.other_material', {index: index})
        }
    };

    // 材料
    function materials() {
        $scope.price = 0;               // 原价
        $scope.preferential = 0;        // 优惠价
        let workerMoney = 0;            // 工人费用
        $scope.materials = JSON.parse(sessionStorage.getItem("materials_bak"));
        _ajax.get("/owner/case-particulars", params, function (res) {
            console.log(res, "材料");
            let data = res.data;
            if (data === null) {
                $scope.activeObj.type = 0;
                return;
            }

            let params = {  // 系数参数集合
                list: []
            };
            let freightParams = {   // 运费参数集合
                goods: []
            };
            $scope.activeObj.type = 1;

            if (sessionStorage.getItem("materials") === null) {
                $scope.roomPicture = data.images.effect_images;
                sessionStorage.setItem("roomPicture", JSON.stringify($scope.roomPicture));

                $timeout(function () {
                    let mySwiper = new Swiper("#swiperList", {
                        autoplay: 3000,
                        loop: true,
                        pagination: ".swiper-pagination"
                    });
                });

                let materials = data.goods;     // 材料信息
                let worker = data.worker_data;  // 工人信息
                sessionStorage.setItem("worker", JSON.stringify(worker));
                for (let obj of worker) {
                    workerMoney += parseFloat(obj.worker_price);
                }
                $scope.price = workerMoney;
                $scope.preferential = workerMoney;

                for (let obj of materials) {    // 遍历材料
                    for (let o of $scope.materials) {
                        // 遍历一级分类  判断分类标题是否相等
                        if (obj.goods_first === o.title) {
                            o.goods.push(obj);
                            break;
                        }
                    }
                    let tempObj = {
                        one_title: obj.goods_first,
                        two_title: obj.goods_second,
                        three_title: obj.goods_three,
                        price: obj.cost,
                        procurement: obj.procurement
                    };
                    let tempFreight = {
                        goods_id: obj.goods_id,
                        num: obj.quantity
                    };
                    params.list.push(tempObj);
                    freightParams.goods.push(tempFreight);
                }

                for (let obj of $scope.materials) {
                    for (let o of obj.goods) {
                        obj.totalMoney += parseFloat(o.cost);
                    }
                }
            } else {
                $scope.roomPicture = JSON.parse(sessionStorage.getItem("roomPicture"));

                $timeout(function () {
                    let mySwiper = new Swiper("#swiperList", {
                        autoplay: 3000,
                        loop: true,
                        pagination: ".swiper-pagination"
                    });
                });

                $scope.materials = JSON.parse(sessionStorage.getItem("materials"));
                for (let material of $scope.materials) {
                    material.second_level = [];
                }
                let worker = JSON.parse(sessionStorage.getItem("worker"));
                for (let obj of worker) {
                    workerMoney += parseFloat(obj.worker_price);
                }
                $scope.price = workerMoney;
                $scope.preferential = workerMoney;

                for (let obj of $scope.materials) {    // 遍历材料
                    for (let o of obj.goods) {
                        let tempObj = {
                            one_title: o.goods_first,
                            two_title: o.goods_second,
                            three_title: o.goods_three,
                            price: o.cost,
                            procurement: o.procurement
                        };
                        let tempFreight = {
                            goods_id: o.id,
                            num: o.quantity
                        };
                        params.list.push(tempObj);
                        freightParams.goods.push(tempFreight);
                    }
                }

                for (let obj of $scope.materials) {
                    obj.totalMoney = 0;
                    for (let o of obj.goods) {
                        obj.totalMoney += parseFloat(o.cost);
                    }
                }
            }
            // 系数
            _ajax.post("/owner/coefficient", params, function (res) {
                let data = res.data;
                $scope.price += parseFloat(data.total_prices);
                $scope.preferential += parseFloat(data.special_offer);
                huxingFlag = true;
            });

            // 运费
            _ajax.post("/order/calculation-freight", freightParams, function (res) {
                let data = res.data;
                $scope.price += parseFloat(data);
                $scope.preferential += parseFloat(data);
            });
        })
    }
}]);