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
        code: $stateParams.code,            // 区编码
        toponymy: $stateParams.toponymy,    // 小区名称
        street: $stateParams.street         // 街道地址
    };

    let params = {
        toponymy: $stateParams.toponymy,    // 小区名称
        particulars: "",                    // 厅室名称
        area: "",                           // 面积
        stairway: "",                       // 有无楼梯
        stair_id: "",                       // 楼梯信息 ID
        series: "",                         // 系列ID
        style: "",                          // 风格ID
    };

    // 样板间信息
    _ajax.post("/owner/case-list", $scope.params, function (res) {
        console.log(res, "样板间");
        let data = res.data;
        $scope.huxing = data;
        $scope.activeObj = data[0];
        params.particulars = data[0].particulars;
        params.area = data[0].area;
        params.stairway = data[0].stairway;
        if (data[0].stairway === "1") {
            params.stair_id = data[0].stair_id;
        } else {
            params.stair_id = ""
        }
        params.series = data[0].case_picture[0].series_id;
        params.style = data[0].case_picture[0].style_id;
        materials();
    });

    // 楼梯、系列和风格数据
    _ajax.post("/owner/series-and-style", {}, function (res) {
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
            if (i.title === "辅材" || i.title === "主要材料") {
                primary.push(i);
            } else {
                others.push(i);
            }
        }
        $scope.materials = primary.concat(others);
    });

    // 户型选择
    $scope.huxingFun = function (obj) {
        $scope.activeObj = obj;
        params.particulars = obj.particulars;
        params.area = obj.area;
        if (obj.stairway === "1") {
            params.stairway = obj.stairway;
            params.stair_id = obj.stair_id;
        } else {
            params.stairway = obj.stairway;
            params.stair_id = "";
        }
        params.series = obj.case_picture[0].series_id;
        params.style = obj.case_picture[0].style_id;
        materials();
    };

    // 楼梯选择
    $scope.stairsFun = function (obj) {
        $scope.activeObj.stair_id = obj.id;
        params.stair_id = obj.id;
        materials();
    };

    // 系列选择
    $scope.seriesFun = function (obj) {
        $scope.activeObj.case_picture[0].series_id = obj.id;
        $scope.seriesDesc = {
            intro: obj.intro,
            theme: obj.theme
        };
        params.series = obj.id;
        materials();
    };

    // 风格选择
    $scope.styleFun = function (obj) {
        $scope.activeObj.case_picture[0].style_id = obj.id;
        params.style = obj.id;
        materials();
    };

    let mySwiper = new Swiper("#swiperList", {
        autoplay: 3000,
        loop: true,
        observer: true,
        pagination: ".swiper-pagination"
    });

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
            stair_id: activeObj.stair_id                                // 楼梯材料id
        };
        sessionStorage.setItem("payParams", JSON.stringify(payParams));
        $state.go("deposit");
    };

    // 材料
    function materials() {
        _ajax.post("/owner/case-particulars", params, function (res) {
            console.log(res, "材料");
            let data = res.data;
            if (data === null) {
                $scope.activeObj.type = 0;
                return;
            }
            $scope.activeObj.type = 1;
            $scope.roomPicture = data.images.effect_images;
            $scope.price = 0;               // 原价
            $scope.preferential = 0;        // 优惠价
            let materials = data.goods;     // 材料信息
            let worker = data.worker_data;  // 工人信息
            let workerMoney = 0;            // 工人费用
            for (let obj of worker) {
                workerMoney += parseFloat(obj.worker_price);
            }
            $scope.price = workerMoney;
            $scope.preferential = workerMoney;
            let params = {  // 系数参数集合
                list: []
            };
            let freightParams = {   // 运费参数集合
                goods: []
            };
            for (let obj of materials) {
                let tempObj = {
                    one_title: obj.goods_first,
                    two_title: obj.goods_second,
                    three_title: obj.goods_three,
                    price: obj.goods_original_price
                };
                let tempFreight = {
                    goods_id: obj.id,
                    num: obj.goods_quantity
                };
                params.list.push(tempObj);
                freightParams.goods.push(tempFreight);
                for (let o of $scope.materials) {
                    if (obj.goods_first === o.title) {
                        o.goods.push(obj);
                        break;
                    }
                }
            }

            for (let obj of $scope.materials) {
                for(let o of obj.goods) {
                    obj.totalMoney += parseFloat(o.goods_original_price);
                }
            }

            // 系数
            _ajax.post("/owner/coefficient", params, function (res) {
                let data = res.data;
                $scope.price += parseFloat(data.total_prices);
                $scope.preferential += parseFloat(data.special_offer);
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