let edit_attribute = angular.module("edit_attribute_module", []);
edit_attribute.controller("edit_attribute_ctrl", function ($scope, $http, $stateParams,$state) {
    const config = {
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        transformRequest: function (data) {
            return $.param(data)
        }
    }
    $scope.iswarning = false;
    $scope.iswarningcontent = false;
    $scope.propid = $stateParams.propid;
    $scope.titles = $stateParams.titles;
    $scope.propattrs = $stateParams.propattrs;

    let namesarr = []; //属性名称数组
    let valuesarr = [];//属性内容数组
    let typesarr = [];//type数组
    let unitsarr = [];//单位数组
    let unit_value_arr = [];//单位真值数组

    /*单位默认*/
    $scope.unitarrs = [{unit: '无'}, {unit: 'L'}, {unit: 'MM'}, {unit: 'M'}, {unit:'kg'},{unit:'M²'}]
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
            cur_unit:$scope.unitarrs[0],
            addition_type: '0'
        }
        $scope.propattrs.push(obj);
    }

    /*下拉添加方法*/
    $scope.selectAdd = function () {
        let obj = {
            name: '',
            value: '',
            cur_unit:$scope.unitarrs[0],
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
            /*重名搁置*/
            // for(let i=0;i<$scope.propattrs.length-1;i++){
            // 	if(obj==$scope.propattrs[i].name){
            // 		console.log('重名了')
            // 	}
            // }
        }
        // console.log(obj)


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


    // ng-blur="judge=!!storeform.storename.$invalid" ng-class={'tored':storeform.storename.$invalid&&alljudgefalse||storeform.storename.$error.required&&judge}

    /*保存属性*/
    $scope.saveProp = function () {
        namesarr = [];
        valuesarr = [];
        typesarr = [];
        unitsarr = [];
        unit_value_arr = [];
        // names 获取当前的所有name
        for (let i = 0; i < $scope.propattrs.length; i++) {
            namesarr.push($scope.propattrs[i].name);
            valuesarr.push($scope.propattrs[i].value);
            typesarr.push($scope.propattrs[i].addition_type);
            unitsarr.push($scope.propattrs[i].cur_unit.unit);
        }

        for(let i=0;i<unitsarr.length;i++){
            switch (unitsarr[i]){
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

        let url = baseUrl+"/mall/goods-attr-add";
        let data = {
            category_id: +$scope.propid,
            "names[]": namesarr,
            "values[]": valuesarr,
            "units[]":unit_value_arr,
            "addition_types[]":typesarr
        };
            $http.post(url,data,config).then(function (res) {
                if(res.data.code==200){
                    console.log(res);
                    $("#success_modal").modal("show");
                }
            })
    }

    /*确认保存成功*/
    $scope.modalBack = () => {
        setTimeout(()=>{
            $state.go('style_index',{showattr:true})
        },200)
    }


});