//获取后台品牌管理列表接口
var brand_list_admin="mall/brand-list-admin";
//文件上传
var upload1="site/upload";
//获取一二三级分类数据的接口
var categories_manage_admin="mall/categories-manage-admin";
//添加品牌接口
var brand_add="mall/brand-add";
app.controller("brand_management",function($scope,$http){
    $scope.url=url;
    $scope.icon1="";
    $scope.icon2="";
    $scope.title="";
    //控制页头的显示及样式
    $scope.header_display=2;
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
    //获取分类列表数据事件
    $scope.order1="";
    $scope.data_list=function(){
        $scope.page = 1;
        $scope.page_size = 12;
        //清空上一次分页的数据
        $('#pageTool').text("");
        $http({
            method:"GET",
            url:url+brand_list_admin+"?page="+$scope.page+"&size="+ $scope.page_size
        })
            .success(function(data,status){
                $scope.brand_list=data.data.brand_list_admin.details;
                //$scope.brand_list=[
                //    {
                //        "id": "2",
                //        "name": "b46",
                //        "logo": "aa5.jpg",
                //        "create_time": "2017-05-26 04:39",
                //        "online_time": "2017-06-01 05:47",
                //        "offline_time": "2017-06-01 05:47",
                //        "review_status": "审核通过",
                //        "reason": "",
                //        "offline_reason": "",
                //        "status": "已下架",
                //        "review_time": "1970-01-01 01:00",
                //        "applicant": "supplier 1",
                //        "category_titles": [
                //            {
                //                "root_category_title":"main materia",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            },
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"分开发烦你",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"分开发烦你2222",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //
                //        ]
                //    },
                //    {
                //        "id": "3",
                //        "name": "b49",
                //        "logo": "aa2.jpg",
                //        "create_time": "2017-05-26 05:58",
                //        "online_time": "2017-05-26 08:46",
                //        "offline_time": "2017-05-26 05:58",
                //        "review_status": "审核通过",
                //        "reason": "aaa",
                //        "offline_reason": "aaa",
                //        "status": "已下架",
                //        "review_time": "2017-05-26 08:46",
                //        "applicant": "supplier 1",
                //        "category_titles": [
                //            {
                //                "root_category_title":"main materia",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            },
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"分开发烦你",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"分开发烦你2222",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //        ]
                //    },
                //    {
                //        "id": "6",
                //        "name": "b50",
                //        "logo": "aa5.jpg",
                //        "create_time": "2017-05-26 06:06",
                //        "online_time": "2017-05-27 04:12",
                //        "offline_time": "2017-05-27 04:13",
                //        "review_status": "审核通过",
                //        "reason": "",
                //        "offline_reason": "batch offline2",
                //        "status": "已下架",
                //        "review_time": "1970-01-01 01:00",
                //        "applicant": "supplier 1",
                //        "category_titles": [
                //            {
                //                "root_category_title":"main materia",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            },
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"分开发烦你",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"分开发烦你2222",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //        ]
                //    },
                //    {
                //        "id": "16",
                //        "name": "b52",
                //        "logo": "aa5.jpg",
                //        "create_time": "2017-05-26 06:13",
                //        "online_time": "2017-05-27 04:12",
                //        "offline_time": "2017-05-27 04:13",
                //        "review_status": "待审核",
                //        "reason": "",
                //        "offline_reason": "batch offline2",
                //        "status": "已下架",
                //        "review_time": "1970-01-01 01:00",
                //        "applicant": "supplier 1",
                //        "category_titles": [
                //            {
                //                "root_category_title":"main materia",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            },
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"分开发烦你",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"分开发烦你2222",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //            ,
                //            {
                //                "root_category_title":"",
                //                "parent_category_title":"title",
                //                "level3_category_titles":"title,title,title"
                //            }
                //        ]
                //    }
                //]
                $scope.brand_total=data.data.brand_list_admin.total;
                $scope.page_count=$scope.brand_total/$scope.page_size;

                console.log("$scope.page_count"+$scope.page_count);
                $('#pageTool').Paging({
                    pagesize: $scope.page, count: $scope.page_count,toolbar:true,ellipseTpl:". . .",  callback: function (page, size, count) {
                        //console.log(arguments);
                        //点击页码后重新访问接口获取当前页码的数据
                        $scope.page=page;
                        $http({
                            method: "GET",
                            url:url+brand_list_admin+"?page="+$scope.page+"&size="+ $scope.page_size
                        })
                            .success(function (data, status) {
                                $scope.brand_list=data.data.brand_list_admin.details;
                            });

                        //alert('当前第 ' + page + '页,每页 ' + size + '条,总页数：' + count + '页')
                    }
                });
            });
    };
    $scope.data_list();
    //查看的返回事件
    $scope.check_return= function () {
        $scope.header_display=2;
    };
    //品牌详情的图标的大小及居中控制
    $scope.img_size=function(){
        $(".sort_img").css({"width":"100%","height":"auto"});
        $scope.img_width= $(".sort_img").width();
        $scope.img_height= $(".sort_img").height();
        if($scope.img_width>=$scope.img_height){
            $(".sort_img").css({"width":"100%","height":"auto"})
        }
        else{
            $(".sort_img").css({"width":"auto","height":"100%"})
        }
    };
    $scope.img_size();
    //上传图标图片
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
        $scope.img_width1=obj.width;
        $scope.img_height1=obj.height;
    });
    $scope.getImageWidthAndHeight('myfile2', function (obj) {
        $scope.img_width2=obj.width;
        $scope.img_height2=obj.height;
    });
    $scope.up_load=function(obj){
        var now_id="#myfile"+obj;
        var now_form="#uploadForm"+obj;
        $(document).on("change",now_id,function(){
            var formData = new FormData($(now_form)[0]);
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
                        if(obj==1){
                            $scope.icon1=data.data.file_path;
                            $(".sort_img1").attr({src:$scope.url+$scope.icon1})
                            console.log("$scope.img_width11111111="+$scope.img_width1)
                            console.log("$scope.img_height1111111111="+$scope.img_height1)
                            if($scope.img_width1>=$scope.img_height1){
                                $(".sort_img1").css({"width":"100%","height":"auto"})
                            }
                            else{
                                $(".sort_img1").css({"width":"auto","height":"100%"})
                            }
                            $("#uploadForm1 .img_warn1").text("")
                        }
                        else if(obj==2){
                            $scope.icon2=data.data.file_path;
                            $(".sort_img2").attr({src:$scope.url+$scope.icon2})
                            console.log("$scope.img_width11111111="+$scope.img_width2)
                            console.log("$scope.img_height1111111111="+$scope.img_height2)
                            if($scope.img_width2>=$scope.img_height2){
                                $(".sort_img2").css({"width":"100%","height":"auto"})
                            }
                            else{
                                $(".sort_img2").css({"width":"auto","height":"100%"})
                            }
                            $("#uploadForm2 .img_warn2").text("")
                        }


                    }
                    else{
                        if(obj==1){
                            $("#uploadForm1 .img_warn1").text("* 上传图片的格式不正确或尺寸不匹配，请重新上传")

                        }
                        else if(obj==2){
                            $("#uploadForm2 .img_warn2").text("* 上传图片的格式不正确或尺寸不匹配，请重新上传")
                        }
                    }

                },
                error: function (returndata) {
                    console.log(returndata);
                }
            });
        });
    };
    $scope.up_load(1);
    $scope.up_load(2);
    //获取一级分类
    $scope.rank= function (obj,rank) {
        //rank为分类的级别
        $http({
            method:"GET",
            url:url+categories_manage_admin+"?pid="+obj
        })
            .success(function(data,status){
                if(rank==1){
                    $scope.rank_list1=data.data.categories;
                    //二三级分类的初始化
                    $scope.rank($(".rank_first").eq(0).attr("name"),2);
                    $scope.rank($(".rank_second").eq(0).attr("name"),3);
                }
                else if(rank==2){
                    $scope.rank_list2=data.data.categories;
                    //三级分类的初始化
                    $scope.rank($(".rank_second").eq(0).attr("name"),3);
                }
                else if(rank==3){
                    $scope.rank_list3=data.data.categories;
                }
            })
    };
    $scope.rank(0,1);
    $scope.$on('ngRepeatFinished', function (data) { //接收广播，一旦repeat结束就会执行
        $scope.check_btn=function(header_show, check_name, check_icon,
                                  check_logo, check_category_titles,
                                  check_review_time, check_review_status,
                                  check_reason ){
            $scope.check_name=check_name;
            $scope.check_icon=check_icon;
            $scope.check_logo=check_logo;
            $scope.check_category_titles=check_category_titles;
            $scope.check_review_time=check_review_time;
            $scope.check_review_status=check_review_status;
            $scope.check_reason=check_reason;
            $scope.header_display=header_show;
            //alert("进入查看函数")
        };
        var ishave=2;
        $(".rank_first").on("click",function() {
            $('.choose_all').attr("disabled",false);
            $scope.rank($(this).attr("name"),2);
            ishave=2;
        });
        $(".rank_second").on("click",function() {
            ishave=2;
            $('.choose_all').attr("disabled",false);
            $(".choose_all").prop("checked",false);
            $scope.rank($(this).attr("name"),3);
        });
        //对比右边选中的三级分类是否包括当前三级的分类
        $scope.three_all=function(){
            for(var i=0;i<$(".choose").length;i++){
                var selet_id=$(".choose").eq(i).val();
                for(var a=0;a<$(".rank_now").length;a++){
                    var now_id= $(".rank_now").eq(a).attr("name");
                    $(".rank_now").eq(a).attr("name");
                    if(now_id==selet_id){
                        $(".choose").eq(i).prop("checked",true);
                    }
                }
            }
        };
        $scope.three_all();
        $("input[name=choose]").on("click",function(){
            $scope.add_id2=$(this).val();
            $scope.add_name2=$(this).attr("title1");
            ishave=2;
            if($(this).is(':checked')){
                for(var a=0;a<$(".rank_now").length;a++){
                    var now_id= $(".rank_now").eq(a).attr("name");
                    $(".rank_now").eq(a).attr("name");
                    if(now_id==$scope.add_id2){
                        ishave=0;
                    }
                }
                if(ishave==2){
                    $(".selected").append('<div class="rank_now '+$scope.add_id2+'" name="'+$scope.add_id2+'">'+$scope.add_name2+'<div class="delete_btn">×</div></div>')
                    //已选中的分类的删除事件
                    $(".delete_btn").on("click",function(){
                        var delete_id="."+$(this).parent().attr("name");
                        $(this).parent().remove();
                        $(".rank_three").find(delete_id).prop("checked",false);
                    });
                }
            }
            else if(!$(this).is(':checked')){
                for(var b=0;b<$(".rank_now").length;b++){
                    var now_id1= $(".rank_now").eq(b).attr("name");
                    $(".rank_now").eq(b).attr("name");
                    if(now_id1==$scope.add_id2){
                        ishave=0;
                        var remove_id="."+now_id1;
                        $(".selected").find(remove_id).remove();
                    }
                }
            }
        });
        //添加的确认事件
        $scope.add=function () {
            $scope.category_id=[];
            for(var i=0;i< $(".rank_now").length;i++){
                $scope.category_id.push($(".rank_now").eq(i).attr("name"))
            }
            $scope.category_ids=$scope.category_id.join(",");
            console.log("$scope.category_ids"+$scope.category_ids);
            console.log("$scope.title"+$scope.title);
            console.log("$scope.icon1"+$scope.icon1);
            console.log("$scope.icon2"+$scope.icon2);
            console.log("$scope.sort_pid"+$scope.sort_pid);
            if($scope.title=="" || $scope.title=="undefined"){
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .add_save").addClass('show').removeClass("hide");
                $(".popup .add_save  .warm_word1").text('品牌名称不能为空');
            }
            else if($scope.title!=""&&$scope.icon1==""){
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .add_save").addClass('show').removeClass("hide");
                $(".popup .add_save  .warm_word1").text('请上传商标注册证');
            }
            else if($scope.title!=""&&$scope.icon1!=""&&$scope.icon2==""){
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .add_save").addClass('show').removeClass("hide");
                $(".popup .add_save  .warm_word1").text('请上传品牌LOGO');
            }
            else if($scope.title!=""&&$scope.icon1!=""&&$scope.icon2!=""&&$scope.category_id.length==0){
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .add_save").addClass('show').removeClass("hide");
                $(".popup .add_save  .warm_word1").text('请选择品牌所在分类');
            }
            else{
                $.ajax({
                    url: url+ brand_add,
                    type: 'POST',
                    data:{"name": $scope.title,"certificate":$scope.icon1,"logo":$scope.icon2,"category_ids":$scope.category_ids},
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
                            $(".popup .add_save  .warm_word1").text('该品牌名称已存在，请重新填写');
                            console.log("品牌名称不能重复=="+data.code);
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
    });
});
app.controller('itemReaptCtrl', ['$scope', function ($scope) {
    $scope.$watch($scope.$last, function () {
        if($scope.$last){   //$scope.$last是来判断是否是最后一个ng-repeat对象， 如果是则$scope.$last的值为true ,反之则为false
            setTimeout(function(){$scope.$emit('ngRepeatFinished')},1); // 由于是向父控制器中发布广播，所有用$emit
        }
    })
}]);
