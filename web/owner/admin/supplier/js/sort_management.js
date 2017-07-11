//后台商品分类列表
var category_list_admin="mall/category-list-admin";
//后台管理的一二级分类的列表接口
var categories_admin="mall/categories-admin";
//文件上传
var upload1="site/upload";
//分类添加接口
var category_add="mall/category-add";
app.controller("sort_management",function($http,$scope){
    $scope.url=url;
    //控制页头的显示及样式
    $scope.header_display=2;
    $scope.sort_pid=0;
    $scope.title="";
    $scope.icon="";
    $scope.header_show=function(obj){
        $scope.header_display=obj;
    };
    //右边内容宽度自适应
    $scope.zishiy=function (){
        var browser_width1=$(document).width()-$(".nav_box").width();
        $(".my_container").css("width",browser_width1);
        $(".header_box").css("width",browser_width1);
        //浏览器大小变化的监听
        $(window).resize(function() {
            var browser_width1=$(document).width()-$(".nav_box").width();
            $(".my_container").css("width",browser_width1);
            $(".header_box").css("width",browser_width1);
        });
    };
    $scope.zishiy();
    //后台管理的一二级分类的列表数据的获取
    //获取一级分类的列表数据
    $scope.gain_fist=function(){
        $http({
            method:"GET",
            url:url+categories_admin
        })
            .success(function(data,status){
                $scope.first_level=data.data.categories;
                $scope.second= $scope.first_level[0].id;
                $scope.gain_second()
            });
    };
    $scope.gain_fist();

    //获取二级列表数据函数
    $scope.gain_second=function(){
        $http({
            method:"GET",
            url:url+categories_admin+"?pid="+ $scope.second
        })
            .success(function(data,status){
                $scope.second_level=data.data.categories;
            });
    };
    $(document).on("change","#first_level",function(){
        $scope.second=$(this).val();
        $scope.gain_second()
    });
    $(document).on("change","#second_level",function(){
        $scope.sort_pid=$(this).val();
    });
    //获取分类列表数据事件
    $scope.order1="";
    $scope.data_list=function(){
        $scope.page = 1;
        $scope.page_size = 12;
        //清空上一次分页的数据
        $('#pageTool').text("");
        $http({
            method:"GET",
            url:url+category_list_admin+"?page="+$scope.page+"&size="+ $scope.page_size
        })
            .success(function(data,status){
                $scope.sort_list=data.data.category_list_admin.details;
                $scope.sort_total=data.data.category_list_admin.total;
                $scope.page_count=$scope.sort_total/$scope.page_size;

                console.log("$scope.page_count"+$scope.page_count);
                $('#pageTool').Paging({
                    pagesize: $scope.page, count: $scope.page_count,toolbar:true,ellipseTpl:". . .",  callback: function (page, size, count) {
                        //console.log(arguments);
                        //点击页码后重新访问接口获取当前页码的数据
                        $scope.page=page;
                        $http({
                            method: "GET",
                            url:url+category_list_admin+"?page="+$scope.page+"&size="+ $scope.page_size
                        })
                            .success(function (data, status) {
                                $scope.sort_list=data.data.category_list_admin.details;
                            });
                    }
                });
            });
    };
    //列表数据初始化
    $scope.data_list();
    //列表的升降序事件
    $scope.order_btn=function(){
        if(!$("#order_btn").hasClass("clicked")) {
            $("#order_btn").addClass("clicked");
            $(".to_top").removeClass("order_on");
            $(".to_bottom").addClass("order_on");
            $scope.order1="create_time";
        } else {
            $("#order_btn").removeClass("clicked");
            $(".to_bottom").removeClass("order_on");
            $(".to_top").addClass("order_on");
            $scope.order1="-create_time";
        }

    };
//上传图标图片
    $scope.add_image1="";
    $scope.getImageWidthAndHeight=function(id, callback) {
        var _URL = window.URL || window.webkitURL;
        $("#" + id).change(function (e) {
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onload = function () {
                    callback && callback({"width": this.width, "height": this.height, "filesize": file.size});
                };
                img.src = _URL.createObjectURL(file);
            }
        });
    };
    $scope.getImageWidthAndHeight('myfile1', function (obj) {
        console.log('width:'+obj.width+'-----height:'+obj.height);
        $scope.img_width1=obj.width;
        $scope.img_height1=obj.height;
    });
    $(document).on("change","#myfile1",function(){
        var formData = new FormData($( "#uploadForm1" )[0]);
        $.ajax({
            url: url+upload1 ,
            type: 'POST',
            data: formData,
            dataType: "json",
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                $scope.up_code=data.code;
                console.log(" $scope.up_code"+ $scope.up_code)
                if($scope.up_code==200){
                    $scope.icon=data.data.file_path;
                    $(".sort_img").attr({src:$scope.url+$scope.icon})
                    console.log("$scope.img_width11111111="+$scope.img_width1)
                    console.log("$scope.img_height1111111111="+$scope.img_height1)
                    if($scope.img_width1>=$scope.img_height1){
                        $(".sort_img").css({"width":"100%","height":"auto"})
                    }
                    else{
                        $(".sort_img").css({"width":"auto","height":"100%"})
                    }
                }
                else{
                   $("#uploadForm1 .img_warn").text("* 上传图片的格式不正确或尺寸不匹配，请重新上传")
                }

            },
            error: function (returndata) {
                console.log(returndata);
            }
        });
    });

    //

    //添加的确认事件
    var ue = UE.getEditor('editor');
    $scope.add=function () {
        var arr = [];
        var pattern = new RegExp("[`~!@#$%^&*()-+_=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|《》{}【】‘；：”“'。，、？]");
        arr.push(UE.getEditor('editor').getContent());
        $scope.description=arr.join("\n");
        if($scope.title=="" || $scope.title=="undefined"){
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .add_save").addClass('show').removeClass("hide");
            $(".popup .add_save  .warm_word1").text('分类名称不能为空');

        }
        else if($scope.title!=""&&pattern.test($scope.title)){
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .add_save").addClass('show').removeClass("hide");
            $(".popup .add_save  .warm_word1").text('分类名称格式不正确，请重新填写');
        }
        else if($scope.title!=""&&$scope.icon==""){
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .add_save").addClass('show').removeClass("hide");
            $(".popup .add_save  .warm_word1").text('请上传图片');
        }
        else{
            $.ajax({
                url: url+ category_add,
                type: 'POST',
                data:{"title": $scope.title,"pid":$scope.sort_pid,"icon":$scope.icon,"description":$scope.description},
                dataType: "json",
                contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                success: function (data) {
                    $scope.first=data.code;
                    if($scope.first==200){
                        $(".popup").addClass('show').removeClass("hide");
                        $(".popup .add_save").addClass('show').removeClass("hide");
                        $(".popup .add_save  .warm_word1").text('添加成功');
                        console.log("添加分类成功=="+data.code);
                        $scope.data_list();
                    }
                    else if($scope.first==1006){
                        $(".popup").addClass('show').removeClass("hide");
                        $(".popup .add_save").addClass('show').removeClass("hide");
                        $(".popup .add_save  .warm_word1").text('该分类名称已存在，请重新填写');
                        console.log("商品分类名称不能重复=="+data.code);
                    }
                    else{
                        $(".popup").addClass('show').removeClass("hide");
                        $(".popup .add_save").addClass('show').removeClass("hide");
                        $(".popup .add_save  .warm_word1").text('未添加成功，请重新添加');

                        console.log("添加失败=="+data.code);
                    }
                }
            });
        }

    };
    $scope.add_close1= function () {
        $(".popup").addClass('hide').removeClass("show");
        $(".popup .add_save").addClass('hide').removeClass("show");
        if($scope.first==200){
            $scope.header_display=2;
        }
    };
    //添加的返回事件
    $scope.add_return= function () {
        $scope.header_display=2;
    };
    //查看的返回事件
    $scope.check_return= function () {
        $scope.header_display=2;
    };
    //分类详情的图标的大小及居中控制
    $scope.img_size=function(){
        $(".sort_img1").css({"width":"100%","height":"auto"});
        $scope.img_width= $(".sort_img1").width();
        $scope.img_height= $(".sort_img1").height();
        if($scope.img_width>=$scope.img_height){
            $(".sort_img1").css({"width":"100%","height":"auto"})
        }
        else{
            $(".sort_img1").css({"width":"auto","height":"100%"})
        }
    }
    $scope.img_size();

    $scope.$on('ngRepeatFinished', function (data) { //接收广播，一旦repeat结束就会执行
        //查看事件的处理
        $scope.check_btn=function(header_show, check_name, check_icon, check_review_time, check_review_status, check_reason, check_description, check_titles){
            $scope.check_name=check_name;
            $scope.check_icon=check_icon;
            $scope.check_review_time=check_review_time;
            $scope.check_parent_title=check_titles;
            $scope.check_review_status=check_review_status;
            $scope.check_reason=check_reason;
            $scope.check_description=check_description;
            $scope.header_display=header_show;
            alert("进入查看函数")
            $(".sort_description").html($scope.check_description);
        };
        //筛选事件的实现
        $scope.screen=function(now_id,now_classs){
            var this_id="#"+now_id;
            var this_class="."+now_classs;
            if(!$(this_class).hasClass("clicked")){
                $(this_class).addClass("clicked");
                $(this_id).addClass("show").removeClass("hide")
            } else {
                $(this_class).removeClass("clicked");
                $(this_id).addClass("hide").removeClass("show")
            }
        };
        //控制哪些列显示哪些不显示
        $("input[name=cell]").on("click",function() {
            console.log("$(this).val()"+$(this).val())
            if ($(this).prop("checked") == true) {
                var cell_name="."+$(this).val();
                $(cell_name).show();
            }
            else if($(this).prop("checked") != true){
                var cell_name1="."+$(this).val();
                $(cell_name1).hide();

            }

        });

    });
});
app.controller('itemReaptCtrl', ['$scope', function ($scope) {
    $scope.$watch($scope.$last, function () {
        if($scope.$last){   //$scope.$last是来判断是否是最后一个ng-repeat对象， 如果是则$scope.$last的值为true ,反之则为false
            setTimeout(function(){$scope.$emit('ngRepeatFinished')},1); // 由于是向父控制器中发布广播，所有用$emit
        }
    })
}]);
