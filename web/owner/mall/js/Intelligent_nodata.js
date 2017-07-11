angular.module('intelligent_nodata', [])
    .controller('nodata_ctrl', function ($scope, $http) {
        //小区地址
        $scope.message = ''
        $scope.nowStyle = '现代简约'
        $scope.nowSeries = '齐家'

        $scope.$watch('message', function (newVal, oldVal) {
            if (newVal && newVal != oldVal) {
                if (newVal.length > 45) {
                    $scope.message = newVal.substr(0, 45)
                }
            }
        })
        //解析字符串为JSON
        function getJSON(str) {
            var obj = {};
            var arr = str.split("&");
            for (var i = 0; i < arr.length; i++) {
                for (var j = 0; j < arr[i].length; j++) {
                    obj[arr[i].split('=')[0]] = arr[i].split('=')[1];
                }
            }
            return obj;
        }

        //请求后台数据
        $http.post('/owner/series-and-style').then(function (response) {
            let arr = []
            let arr2 = []
            let arr3 = []
            let arr1 = []
            //系列数据
            for (let item of response.data.data.show.series) {
                arr.push("series=" + item.series + "&intro=" + item.intro + "&theme=" + item.theme)
            }
            //风格数据
            for (let item of response.data.data.show.style) {
                arr1.push("style=" + item.style + "&intro=" + item.intro + "&theme=" + item.theme)
            }
            var m = Array.from(new Set(arr))
            for (var i = 0; i < m.length; i++) {
                arr2.push(getJSON(m[i]))
            }
            var n = Array.from(new Set(arr1))
            for (var i = 0; i < n.length; i++) {
                arr3.push(getJSON(n[i]))
            }
            $scope.series = arr2;
            $scope.style = arr3;
        }, function (response) {

        })
        //切换系列
        $scope.toggleSeries = function (item) {
            $scope.nowSeries = item;
        }
        //切换风格
        $scope.toggleStyle = function (item) {
            $scope.nowStyle = item;
        }
    })
/**
 * Created by xl on 2017/7/4 0004.
 */
//第一个点击事件加减
$(".reduce_a").on("click", function () {
    var text = $(".val_input").val();
    //alert(text);
    if (text > 0) {
        text--;
        $(".val_input").val(text);
        //alert(text);
    } else {
        text = 0;
    }
});
$(".add_a").on("click", function () {
    var text = $(".val_input").val();
    if (text < 6) {
        text++;
        $(".val_input").val(text);
    } else if (text = 6) {
        text = 6;
    }
});

//第二个点击事件加减
$(".reduce_b").on("click", function () {
    var text = $(".val_input_b").val();
    //alert(text);
    if (text > 0) {
        text--;
        $(".val_input_b").val(text);
        //alert(text);
    } else {
        text = 0;
    }
});
$(".add_b").on("click", function () {
    var text = $(".val_input_b").val();
    if (text < 3) {
        text++;
        $(".val_input_b").val(text);
    } else if (text = 3) {
        text = 3;
    }
});

//第三个点击事件加减
$(".reduce_c").on("click", function () {
    var text = $(".val_input_c").val();
    //alert(text);
    if (text > 0) {
        text--;
        $(".val_input_c").val(text);
        //alert(text);
    } else {
        text = 0;
    }
});
$(".add_c").on("click", function () {
    var text = $(".val_input_c").val();
    if (text < 4) {
        text++;
        $(".val_input_c").val(text);
    } else if (text = 3) {
        text = 3;
    }
});

//第四个点击事件加减
$(".reduce_d").on("click", function () {
    var text = $(".val_input_d").val();
    //alert(text);
    if (text > 0) {
        text--;
        $(".val_input_d").val(text);
        //alert(text);
    } else {
        text = 0;
    }
});
$(".add_d").on("click", function () {
    var text = $(".val_input_d").val();
    if (text < 2) {
        text++;
        $(".val_input_d").val(text);
    } else if (text = 2) {
        text = 2;
    }
});
