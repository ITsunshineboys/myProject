let edit_attribute = angular.module("edit_attribute_module", []);
edit_attribute.controller("edit_attribute_ctrl", function ($rootScope,$scope, $http, $stateParams, $state, _ajax) {
    $rootScope.crumbs = [{
        name: '商城管理',
        icon: 'icon-shangchengguanli',
        link: $rootScope.mall_click
    }, {
        name: '系列/风格/属性管理',
        link: 'style_index',
        params: {showattr: true}
    }, {
        name: '属性管理详情'
    }];

    $scope.iswarning = false;
    $scope.iswarningcontent = false;
    $scope.propid = $stateParams.propid;
    $scope.titles = $stateParams.titles;
    $scope.propattrs = $stateParams.propattrs;


    $scope.btnclick = true;

    let namesarr = []; //属性名称数组
    let valuesarr = [];//属性内容数组
    let typesarr = [];//type数组
    let unitsarr = [];//单位数组
    let unit_value_arr = [];//单位真值数组

    /*单位默认*/
    $scope.unitarrs = [{unit: '无'}, {unit: 'L'}, {unit: 'MM'}, {unit: 'M'}, {unit: 'kg'}, {unit: 'M²'}]
    for (let [key, value] of $scope.propattrs.entries()) {
        for (let [key1, value1] of $scope.unitarrs.entries()) {
            if (value.unit == value1.unit) {
                value['cur_unit'] = value1
            }
        }
    }


    /*普通添加方法*/
    $scope.generalAdd = function () {
        let obj = {
            name: '',
            value: '',
            cur_unit: $scope.unitarrs[0],
            addition_type: '0'
        }
        $scope.propattrs.push(obj);
    }

    /*下拉添加方法*/
    $scope.selectAdd = function () {
        let obj = {
            name: '',
            value: '',
            cur_unit: $scope.unitarrs[0],
            addition_type: '1'
        }
        $scope.propattrs.push(obj);
    }


    /*删除当前项*/
    $scope.deleteTr = function (obj) {
        $scope.propattrs.splice(obj, 1);
    }

    /*失去光标判断属性名是否重复*/
    $scope.checkname = function (obj, index) {
        $scope.iswarning = false;
        if (obj == '') {
            $scope.temp = index;
            $scope.iswarning = true;
        } else {
            $scope.iswarning = false;
        }
    }

    /*下拉框添加失去焦点判断*/
    $scope.checkContent = function (obj, index) {
        $scope.iswarningcontent = false;
        if (obj == '') {
            // console.log(1213123)
            $scope.tempcontent = index;
            $scope.iswarningcontent = true;
        } else {
            $scope.iswarningcontent = false;
        }
    }

    /*保存属性*/
    $scope.saveProp = function () {
        namesarr = [];
        valuesarr = [];
        typesarr = [];
        unitsarr = [];
        unit_value_arr = [];

        for (let i = 0; i < $scope.propattrs.length; i++) {
            if($scope.propattrs[i].name==''){
                $scope.backInfo = '属性名称不能为空';
                $scope.btnclick = false;
                $("#success_modal").modal("show");
                return;
            }

            namesarr.push($scope.propattrs[i].name);
            valuesarr.push($scope.propattrs[i].value);
            typesarr.push($scope.propattrs[i].addition_type);
            unitsarr.push($scope.propattrs[i].cur_unit.unit);
        }


        for (let i = 0; i < unitsarr.length; i++) {
            switch (unitsarr[i]) {
                case "无":
                    unit_value_arr.push(0);
                    break;
                case "L":
                    unit_value_arr.push(1);
                    break;
                case "M":
                    unit_value_arr.push(2);
                    break;
                case "M²":
                    unit_value_arr.push(3);
                    break;
                case "kg":
                    unit_value_arr.push(4);
                    break;
                case "MM":
                    unit_value_arr.push(5);
                    break;
            }
        }

        let data = {
            category_id: +$scope.propid,
            "names[]": namesarr,
            "values[]": valuesarr,
            "units[]": unit_value_arr,
            "addition_types[]": typesarr
        };

        _ajax.post('/mall/goods-attr-add',data,function (res) {
            if(res.code == 200){
                $scope.backInfo = '保存成功';
                $scope.btnclick = true;
            } else{
                $scope.backInfo = res.msg;
                $scope.btnclick = false;
            }
            $("#success_modal").modal("show");
        })
    }

    /*确认保存成功*/
    $scope.modalBack = () => {
        setTimeout(() => {
            $state.go('style_index', {showattr: true})
        }, 200)
    }


});