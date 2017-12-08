/**
 * Created by xl on 2017/6/29 0029.
 */
app.controller("modelRoomCtrl", ["$scope", "$timeout", "$location", "$anchorScroll", "$state", "$stateParams", "_ajax", function ($scope, $timeout, $location, $anchorScroll, $state, $stateParams, _ajax) {
    sessionStorage.removeItem('check_goods');
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
    let mySwiper;   // 轮播变量
    let huxingParams = {
        roomCode: $stateParams.roomCode,
        toponymy: $stateParams.toponymy,
        street: $stateParams.street
    };
    sessionStorage.setItem("huxingParams", JSON.stringify(huxingParams));

    let params = {
        toponymy: $scope.params.toponymy,   // 小区名称
        particulars: "",                    // 厅室名称
        area: "",                           // 面积
        stairway: "",                       // 有无楼梯
        stair_id: "",                       // 楼梯信息 ID
        series: "",                         // 系列ID
        style: ""                           // 风格ID
    };
    $scope.isLoading = true;    // 加载动画显示
    $scope.materials = [];
    // 样板间信息
    _ajax.get("/owner/case-list", $scope.params, function (res) {
        console.log(res, "样板间");
        let data = res.data;
        $scope.huxing = data;
        if (sessionStorage.getItem("activeObj") === null) {
            for (let i of data) {
                if (i.type === '1') {
                    $scope.activeObj = angular.copy(i);
                    break;
                }
            }
        } else {
            $scope.activeObj = JSON.parse(sessionStorage.getItem("activeObj"));
        }
        let activeTemp = angular.copy($scope.activeObj);
        params.particulars = activeTemp.particulars;
        params.area = activeTemp.area;
        params.stairway = activeTemp.stairway;
        if (activeTemp.stairway === "1") {
            params.stair_id = activeTemp.stair_id;
        } else {
            params.stair_id = ""
        }
        params.series = activeTemp.case_picture[0].series_id;
        params.style = activeTemp.case_picture[0].style_id;

        // 材料分类
        _ajax.get("/owner/classify", {}, function (res) {
            console.log(res, "材料分类");
            let data = res.data;
            let tempArray = data.pid.stair;
            let primary = [];
            let others = [];
            for (let i of tempArray) {
                i.cost = 0;   // 商品总价
                i.second_level = [];    // 二级分类
                i.count = 0; // 商品项
                if (i.title === "辅材" || i.title === "主要材料") {
                    primary.push(i);
                } else {
                    others.push(i);
                }
            }
            $scope.classData = data.pid;    // 材料分类;
            let tempCls = primary.concat(others);
            sessionStorage.setItem("materials_bak", JSON.stringify(tempCls));
            materials();
        });
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

    // 户型选择
    let huxingFlag = false;
    $scope.huxingFun = function (obj) {
        // 若数据未加载完成，不允许点击其他户型
        if (huxingFlag) {
            return
        }
        huxingFlag = true;
        let openId = sessionStorage.getItem("openId"),
            materials_bak = sessionStorage.getItem("materials_bak"),
            huxingParams = sessionStorage.getItem("huxingParams"),
            roomScroll = sessionStorage.getItem("roomScroll");
        if (openId !== null || materials_bak !== null || huxingParams !== null || roomScroll !== null) {
            sessionStorage.clear();
            sessionStorage.setItem("openId", openId);
            sessionStorage.setItem("materials_bak", materials_bak);
            sessionStorage.setItem("huxingParams", huxingParams);
        }
        sessionStorage.setItem("activeObj", JSON.stringify(obj));
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
            materials();
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
            huxingFlag = false;
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
            if (obj.count === 0) {
                continue
            }
            for (let second of obj.second_level) {
                for (let o of second.goods_detail) {
                    let tempObj = {
                        goods_id: o.goods_id,
                        count: o.quantity,
                        price: o.cost,
                        first_cate_id: obj.id
                    };
                    payParams.material.push(tempObj);
                }
            }
        }
        sessionStorage.setItem("payParams", JSON.stringify(payParams));
        $state.go("deposit");
    };

    // 修改材料
    $scope.editMaterial = function (id, index) {
        sessionStorage.setItem("roomScroll", id);
        let huxing = {
            area: $scope.activeObj.area,
            series: $scope.activeObj.case_picture[0].series_id,
            style: $scope.activeObj.case_picture[0].series_id
        };
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

    $scope.$on('ngRepeatFinished', function () {
        $timeout(function () {
            let $grid = $('#basis_decoration').find('.grid');
            let cur_height = [0, 0];
            $grid.each(function () {
                let min = parseFloat(cur_height[0]) > parseFloat(cur_height[1]) ? cur_height[1] : cur_height[0];
                let minIndex = cur_height[0] > cur_height[1] ? 1 : 0;
                $(this).css({
                    'top': min,
                    'left': minIndex * ($(window).width() * 0.471),
                });
                cur_height[minIndex] += $(this).outerHeight() + 20;
                $('#basis_decoration').outerHeight(parseFloat(cur_height[0]) > parseFloat(cur_height[1]) ? cur_height[0] : cur_height[1])
            });
        })
    });

    // 材料
    function materials() {
        $scope.isLoading = true;
        $scope.price = 0;               // 原价
        $scope.preferential = 0;        // 优惠价
        let workerMoney = 0;            // 工人费用
        $scope.materials = JSON.parse(sessionStorage.getItem("materials_bak"));
        _ajax.get("/owner/case-particulars", params, function (res) {
            console.log(res, "材料");
            let data = res.data;
            if (data === null) {
                $scope.activeObj.type = 0;
                $scope.isLoading = false;
                return;
            }

            let params = {  // 系数参数集合
                list: []
            };
            let freightParams = {   // 运费参数集合
                goods: []
            };
            $scope.activeObj.type = 1;
            console.log(mySwiper);
            if (mySwiper !== undefined) {
                try {
                    mySwiper.destroy(true, true);
                } catch (e) {
                    console.log(e);
                }
            }
            if (sessionStorage.getItem("materials") === null) {
                $scope.roomPicture = data.images.effect_images;
                sessionStorage.setItem("roomPicture", JSON.stringify($scope.roomPicture));
                let materials = data.goods;     // 材料信息
                let worker = data.worker_data;  // 工人信息
                sessionStorage.setItem("worker", JSON.stringify(worker));
                for (let obj of worker) {
                    workerMoney += parseFloat(obj.worker_price);
                }
                $scope.price = workerMoney;
                $scope.preferential = workerMoney;

                // 遍历材料
                for (let obj of materials) {
                    // 遍历一级分类
                    for (let o of $scope.materials) {
                        // 判断两者一级标题是否相等
                        if (obj.goods_first === o.title) {
                            // 判断 materials 是否有二级分类
                            if (o.second_level.length === 0) {
                                // 遍历二级分类
                                for (let cls of $scope.classData.level) {
                                    // 判断商品二级分类是否相等
                                    if (obj.goods_second === cls.title) {
                                        let tempSecondInfo = {
                                            id: cls.id,
                                            title: obj.goods_second,
                                            cost: parseFloat(obj.cost),
                                            goods_detail: []
                                        };
                                        tempSecondInfo.goods_detail.push(obj);
                                        o.second_level.push(tempSecondInfo);
                                        break;
                                    }
                                }
                            } else {
                                let flag = true;
                                // 遍历 materials 的二级分类
                                for (let cls of o.second_level) {
                                    if (obj.goods_second === cls.title) {
                                        cls.cost += parseFloat(obj.cost);
                                        cls.goods_detail.push(obj);
                                        flag = false;
                                        break;
                                    }
                                }
                                // 如果 materials 的二级分类没有相同，则遍历分类数据
                                if (flag) {
                                    for (let cls of $scope.classData.level) {
                                        // 判断商品二级分类是否相等
                                        if (obj.goods_second === cls.title) {
                                            let tempSecondInfo = {
                                                id: cls.id,
                                                title: obj.goods_second,
                                                cost: parseFloat(obj.cost),
                                                goods_detail: []
                                            };
                                            tempSecondInfo.goods_detail.push(obj);
                                            o.second_level.push(tempSecondInfo);
                                            break;
                                        }
                                    }
                                }
                            }
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
                        goods_id: obj.id,
                        num: obj.quantity
                    };
                    params.list.push(tempObj);
                    freightParams.goods.push(tempFreight);
                }

                for (let obj of $scope.materials) {
                    for (let o of obj.second_level) {
                        obj.cost += o.cost;
                        obj.count += o.goods_detail.length;
                    }
                }
            } else {
                $scope.roomPicture = JSON.parse(sessionStorage.getItem("roomPicture"));
                $scope.materials = JSON.parse(sessionStorage.getItem("materials"));
                let worker = JSON.parse(sessionStorage.getItem("worker"));
                for (let obj of worker) {
                    workerMoney += parseFloat(obj.worker_price);
                }
                $scope.price = workerMoney;
                $scope.preferential = workerMoney;

                for (let obj of $scope.materials) {    // 遍历材料
                    if (obj.count === 0) {
                        continue;
                    }
                    for (let second of obj.second_level) {
                        for (o of second.goods_detail) {
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
                }
            }
            // 系数
            _ajax.post("/owner/coefficient", params, function (res) {
                let data = res.data;
                $scope.price += parseFloat(data.total_prices);
                $scope.preferential += parseFloat(data.special_offer);
                huxingFlag = false;
            });

            // 运费
            _ajax.post("/order/calculation-freight", freightParams, function (res) {
                let data = res.data;
                $scope.price += parseFloat(data);
                $scope.preferential += parseFloat(data);
            });

            $timeout(function () {
                mySwiper = new Swiper("#swiperList", {
                    autoplay: 3000,
                    loop: true,
                    pagination: ".swiper-pagination"
                });

                if (sessionStorage.getItem("roomScroll") !== null) {
                    let roomScroll = sessionStorage.getItem("roomScroll");
                    $location.hash(roomScroll);
                    $anchorScroll();
                }
                $scope.isLoading = false;
            });
        })
    }
}]);