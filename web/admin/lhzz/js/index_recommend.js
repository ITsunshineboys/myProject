var recommend_list="mall/recommend-admin-index";
//文件上传
var upload1="site/upload";
//单个改变是否停用
var stop_status="mall/recommend-status-toggle";
//批量改变是否停用接口
var  stops_status="mall/recommend-disable-batch";
//批量删除
var  recommend_deletes="mall/recommend-delete-batch";
//删除推荐接口
var recommend_delete="mall/recommend-delete";
//推荐排序
var recommend_sort="mall/recommend-sort";
app.controller("index_recommend",function($scope,$http){
    //加载页面表格数据函数
    $scope.table_data1=function(){
        $http({
            method: "GET",
            url: url + recommend_list
        })
            .success(function (data, status) {
                //$scope.page = 1;
                $scope.mydata = data.data;
                //$scope.mydata = data.data.recommend_admin_index.details[0];

            }).
            error(function (data, status) {
                //$scope.data = data || "Request failed";
                //$scope.status = status;
            });
    };
    //页面初始加载数据
    $scope.table_data1();
    $scope.$on('ngRepeatFinished', function (data) { //接收广播，一旦repeat结束就会执行
        //排序操作处理
        $scope.order_alter=function(){
            var fixHelper = function(e, ui) {
                //console.log(ui)
                ui.children().each(function() {
                    $(this).width($(this).width());     //在拖动时，拖动行的cell（单元格）宽度会发生改变。在这里做了处理就没问题了
                });
                return ui;
            };
            //jQuery(function(){
            $(" tbody").sortable({                //这里是talbe tbody，绑定 了sortable
                helper: fixHelper,                  //调用fixHelper
                axis:"y",
                start:function(e, ui){
                    //ui.helper.css({"background":"red"})     //拖动时的行，要用ui.helper
//					alert(ui.helper.attr('class'))

                    return ui;
                },
                stop:function(e, ui){
                    //拖拽完成事件

                    //改变序列号



                    //}
                    //alert(ui.item.attr('name'))
                    //alert(ui.item.find(".order").text())

                    //ui.item.removeClass("ui-state-highlight"); //释放鼠标时，要用ui.item才是释放的行
                    return ui;
                }
            }).disableSelection();
            //})
        };
        $scope.order_alter();
        //数据已连接====单个删除事件的控制
        $scope.dele=function(id){
            var del_id1="."+id;
            $scope.del_id=id;
            //$scope.del_name=$("tbody").find(del_id1).find(".tb_name").text()
            //$scope.manage_qx=$("tbody").find(del_id1).find(".manage_qx").text()
            $scope.del_stop=$("tbody").find(del_id1).find(".action").find(".stop").text()
            console.log("i===="+del_id1);
            console.log("$scope.del_id===="+$scope.del_id);
            //console.log("$scope.tb_name===="+$scope.del_name);
            console.log("$scope.del_stop===="+$scope.del_stop);
            if($scope.del_stop=="停用"){
                //弹窗的显示
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .delete1").addClass('show').removeClass("hide");
                $(".popup .delete1 .not_stop").addClass('show').removeClass("hide");
                $(".popup .delete1 .not_stop .warm_word1").text('请先停用后删除');
                //没有停用的删除的关闭按钮
                $scope.del_close1=function(){
                    //弹窗的隐藏
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .delete1").addClass('hide').removeClass("show");
                    $(".popup .delete1 .not_stop").addClass('hide').removeClass("show");
                }
            }
            else if($scope.del_stop=="启用"){
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .delete1").addClass('show').removeClass("hide");
                $(".popup .delete1 .is_stop").addClass('show').removeClass("hide");
                $scope.del_sure=function(){
                    $.ajax({
                        url: url+recommend_delete,
                        type: 'POST',
                        data:{"id":$scope.del_id},
                        dataType: "json",
                        contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                        success: function (data) {
                            $scope.loginout=data;
                            if($scope.loginout.code==200){
                                $scope.table_data1();
                            }

                        }
                    });
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .delete1").addClass('hide').removeClass("show");
                    $(".popup .delete1 .is_stop").addClass('hide').removeClass("show");
                    $("tbody").find(del_id1).remove();
                };
                $scope.del_cancel=function(){
                    alert("删除的取消事件")
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .delete1").addClass('hide').removeClass("show");
                    $(".popup .delete1 .is_stop").addClass('hide').removeClass("show");
                }
            }
        };
        //数据已连接=====单个停用事件的控制
        $scope.stop_one=function(obj){
            $scope.stop_id=obj;
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .stop1").addClass('show').removeClass("hide");
            $(".popup .stop1 .is_stop").addClass('show').removeClass("hide");
            $scope.stop_cancel=function(){
                //弹窗的隐藏
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .stop1").addClass('hide').removeClass("show");
                $(".popup .stop1 .is_stop").addClass('hide').removeClass("show");
            };
            $scope.stop_sure=function() {
                //单个确认停用的数据交互
                $.ajax({
                    url: url+stop_status,
                    type: 'POST',
                    data:{"id":$scope.stop_id},
                    dataType: "json",
                    contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                    success: function (data) {
                        $scope.loginout=data;
                        if($scope.loginout.code==200){
                            $scope.table_data1();
                        }

                    }
                });
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .stop1").addClass('hide').removeClass("show");
                $(".popup .stop1 .is_stop").addClass('hide').removeClass("show");
            }
        };
        //全选事件的控制
        $scope.all=function(){
            if($(".choose_all1").is(':checked')){
                alert("选中");
                $(".choose").prop("checked", true);
                //$('.choose').attr('checked', 'checked');
            }
            else if(!$(".choose_all1").is(':checked')){
                $(".choose").prop("checked",false);
                //$(".choose").prop("checked") == false


                //$(".choose").attr("checked",false)//未选中
                alert("没有选中")
            }

            //console.log(obj.length)
            //alert(obj[0].id)
        };
        //数据已连接====批量删除事件的处理

        $scope.delete=function(){
            $scope.is_stop=true;
            var text=[];
            var pitch_text=[];
            $("input[name=item]").each(function() {
                if ($(this).prop("checked") == true) {
                    console.log($(this).val());
                    var cal="."+$(this).val()
                    $scope.now_stop=$(cal).find(".action").find(".stop").text()
                    console.log($(cal).find(".action").find(".stop").text());
                    if($scope.now_stop=="停用"){
                        $scope.is_stop=false;
                        text.push($(this).val());
                    }
                    pitch_text.push($(this).val());

                }
            });
            if(text==""){
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .delete1").addClass('show').removeClass("hide");
                $(".popup .delete1 .not_stop").addClass('show').removeClass("hide");
                $(".popup .delete1 .not_stop .warm_word1").text('请先选择后删除');
                //没有停用的没有选择删除对象的关闭按钮
                $scope.del_close1=function(){
                    //弹窗的隐藏
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .delete1").addClass('hide').removeClass("show");
                    $(".popup .delete1 .not_stop").addClass('hide').removeClass("show");
                }
            }
            else{
                $scope.del_ids=text.join(",");
                if( $scope.is_stop==false){
                    //alert("请先停用再删除")
                    $(".popup").addClass('show').removeClass("hide");
                    $(".popup .delete1").addClass('show').removeClass("hide");
                    $(".popup .delete1 .not_stop").addClass('show').removeClass("hide");
                    $(".popup .delete1 .not_stop .warm_word1").text('请先停用后删除');
                    //没有停用的没有选择删除对象的关闭按钮
                    $scope.del_close1=function(){
                        //弹窗的隐藏
                        $(".popup").addClass('hide').removeClass("show");
                        $(".popup .delete1").addClass('hide').removeClass("show");
                        $(".popup .delete1 .not_stop").addClass('hide').removeClass("show");
                    }
                }
                else if( $scope.is_stop==true){
                    console.log("text==="+text.join(","))
                    $(".popup").addClass('show').removeClass("hide");
                    $(".popup .delete1").addClass('show').removeClass("hide");
                    $(".popup .delete1 .is_stop").addClass('show').removeClass("hide");
                    $scope.del_sure=function(){
                        $(".popup").addClass('hide').removeClass("show");
                        $(".popup .delete1").addClass('hide').removeClass("show");
                        $(".popup .delete1 .is_stop").addClass('hide').removeClass("show");
                        console.log("text.length====="+text.length)
                        $.ajax({
                            url: url+recommend_deletes,
                            type: 'POST',
                            data:{"id":$scope.del_ids},
                            dataType: "json",
                            contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                            success: function (data) {
                                $scope.loginout=data;
                                if($scope.loginout.code==200){
                                    $scope.table_data1();
                                }

                            }
                        });
                        for(var a=0;a<pitch_text.length;a++){
                            $scope.del_class="."+pitch_text[a]
                            $("tbody").find( $scope.del_class).remove()
                        }
                    };
                    $scope.del_cancel=function(){
                        $(".popup").addClass('hide').removeClass("show");
                        $(".popup .delete1").addClass('hide').removeClass("show");
                        $(".popup .delete1 .is_stop").addClass('hide').removeClass("show");
                    }
                }
            }

        };
        //数据已连接====批量停用事件的处理
        $scope.stop=function(){
            //$scope.is_stop=true;
            var text=[];
            var stops=[]
            $("input[name=item]").each(function() {
                if ($(this).prop("checked") == true) {
                    console.log($(this).val());
                    var cal="."+$(this).val()
                    $scope.now_stop=$(cal).find(".action").find(".stop").text()
                    console.log($(cal).find(".action").find(".stop").text());
                    if($scope.now_stop=="停用"){
                        stops.push($(this).val());
                    }
                    text.push($(this).val());

                }
            });
            if(text==""){
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .delete1").addClass('show').removeClass("hide");
                $(".popup .delete1 .not_stop").addClass('show').removeClass("hide");
                $(".popup .delete1 .not_stop .warm_word1").text('请先选择后停用');
                //没有停用的没有选择删除对象的关闭按钮
                $scope.del_close1=function(){
                    //弹窗的隐藏
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .delete1").addClass('hide').removeClass("show");
                    $(".popup .delete1 .not_stop").addClass('hide').removeClass("show");
                }
            }
            else if(text!=""){
                $(".popup").addClass('show').removeClass("hide");
                $(".popup .stop1").addClass('show').removeClass("hide");
                $(".popup .stop1 .is_stop").addClass('show').removeClass("hide");
                $scope.stop_cancel=function(){
                    //弹窗的隐藏
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .stop1").addClass('hide').removeClass("show");
                    $(".popup .stop1 .is_stop").addClass('hide').removeClass("show");
                };
                $scope.stop_sure=function(){
                    $scope.stop_ids=stops.join(",");
                    console.log("text==="+$scope.stop_ids);
                    $.ajax({
                        url: url+stops_status,
                        type: 'POST',
                        data:{"id":$scope.stop_ids},
                        dataType: "json",
                        contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                        success: function (data) {
                            $scope.loginout=data;
                            if($scope.loginout.code==200){
                                $scope.table_data1();
                            }
                        }
                    });
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .stop1").addClass('hide').removeClass("show");
                    $(".popup .stop1 .is_stop").addClass('hide').removeClass("show");
                    console.log("text.length====="+text.length)
                    console.log("stops.length====="+stops.length)

                };
            }
        };
        //添加事件的处理
        $scope.add=function(){
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .add1").addClass('show').removeClass("hide");
            $scope.add_sure=function(){
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .add1").addClass('hide').removeClass("show");
            };
            $scope.add_cancel=function(){
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .add1").addClass('hide').removeClass("show");
            };
        };
        //上传文件
        $scope.doUpload=function (load) {
            var formData = new FormData($( ".uploadForm" )[0]);
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
                    if(load==1){
                        $scope.load1=data.data;
                        alert( "1111成功");
                    }
                    else if(load==2){
                        $scope.load2=data.data;
                        alert( "222222成功");
                    }
                    else if(load==3){
                        $scope.load3=data.data;
                        alert( "3333成功");
                    }
                    else if(load==4){
                        $scope.load4=data.data;
                        alert( "444444成功");
                    }


                },
                error: function (returndata) {
                    alert(returndata);
                }
            });
        };
        //编辑事件处理
        $scope.edit=function(obj){
            $scope.edit_id=obj;
            alert($scope.edit_id)
            if($scope.edit_id=="链接"){
                $(".popup .edit1 .is_edit .Product_box").addClass('show').removeClass("hide");
                $(".popup .edit1 .is_edit .link_box").addClass('hide').removeClass("show");
            }
            else{
                $(".popup .edit1 .is_edit .link_box").addClass('show').removeClass("hide");
                $(".popup .edit1 .is_edit .Product_box").addClass('hide').removeClass("show");
            }
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .edit1").addClass('show').removeClass("hide");
            $(".popup .edit1 .is_edit").addClass('show').removeClass("hide");
            //取消编辑
            $scope.edit_cancel=function(){
                //弹窗的隐藏
                alert("取消编辑")
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .edit1").addClass('hide').removeClass("show");
                $(".popup .edit1 .is_edit").addClass('hide').removeClass("show");
            };
            //确认编辑
            $scope.edit_sure=function() {
                alert("确认编辑")
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .edit1").addClass('hide').removeClass("show");
                $(".popup .edit1 .is_edit").addClass('hide').removeClass("show");
            }
        };
        //顺序更换保存
        $scope.save1=function(){
            var order2=$("tbody tr ");
            var save_id=[]
            for(var c=0;c<order2.length;c++){
                console.log(order2.eq(c).attr("name"))
                save_id.push(order2.eq(c).attr("name"))
            }
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .order_save").addClass('show').removeClass("hide");
            //$(".popup .order_save .is_edit").addClass('show').removeClass("hide");
            //取消保存
            $scope.save_cancel=function(){
                //弹窗的隐藏
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .order_save").addClass('hide').removeClass("show");
            };
            //确认保存
            $scope.save_sure=function() {
                $scope.save1=save_id.join(",");
                $.ajax({
                    url: url+recommend_sort,
                    type: 'POST',
                    data:{"id":$scope.save1},
                    dataType: "json",
                    contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                    success: function (data) {
                        $scope.loginout=data;
                        if($scope.loginout.code==200){
                            $scope.table_data1();
                        }
                    }
                });

                console.log($scope.save1)
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .order_save").addClass('hide').removeClass("show");
            }
        };
        //筛选事件的实现
        $(".screen").click(function(){
            if(!$(this).hasClass("clicked")) {
                $(this).addClass("clicked");
                $("#cells_box").addClass("show").removeClass("hide")
            } else {
                $(this).removeClass("clicked");
                $("#cells_box").addClass("hide").removeClass("show")
            }
        });
        $("input[name=cell]").on("click",function() {
                if ($(this).prop("checked") == true) {
                    var cell_name="."+$(this).val();
                    $(cell_name).show();

                }
                else if($(this).prop("checked") != true){
                    var cell_name1="."+$(this).val();
                    //alert("没有选中")
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
//

//导航栏
//$(".nav_box dt").css({"background-color":"#F8F8F8"});
$(".nav_box dt img").attr("src","images/select1.png");
$(function(){
    $(".nav_box dd").hide();
    //初始该显示的dd
    $(".nav_box .mall>dd").show();
    $(".nav_box dt").click(function(){
        $(".nav_box dt").css({"background-color":"#F5F7FA","color":"#ABABAB"});
        $(this).css({"background-color": "#E6E9F0","color":"#5677FC"});
        $(this).parent().find('dd').removeClass("menu_chioce");
        $(this).parent().parent().find('dd').find("a").removeClass("dd_on");
        $(".nav_box dt img").attr("src","../../images/select1.png");
        $(this).parent().find('img').attr("src","../../images/select2.png");
        $(".menu_chioce").slideUp();
        $(this).parent().find('dd').slideToggle();
        $(this).parent().find('dd').addClass("menu_chioce");
        $(this).parent().find('dd').click(function(){
            $(this).parent().find('dd').find("a").removeClass("dd_on");
            $(this).find("a").addClass("dd_on");
        })
    });
})
//teb页面
function resetTabs(obj) {
    $(obj).parent().parent().next("div").find(".tab").hide();
    $(obj).parent().parent().find("a").removeClass("current");
}
function loadTab() {
    $(".content > div").hide();
    $(".tabs").each(function () {
        $(this).find("li:first a").addClass("current");
    });
    $(".content").each(function () {
        $(this).find("div:first").fadeIn();
    });
    $(".tabs a").on("click", function (e) {
        e.preventDefault();
        if ($(this).attr("class") == "current") {
            return;
        } else {
            resetTabs(this);
            $(this).addClass("current");
            $($(this).attr("name")).fadeIn();
        }
    });
}
$(function(){

    loadTab();

});
//弹窗的tab页面
function loadTab1() {
    $(".content1 > div").hide();
    $(".adds1").each(function () {
        $(this).find("li:first a").addClass("current1");
    });
    $(".content1").each(function () {
        $(this).find("div:first").fadeIn();
    });
    $(".adds1 a").on("click", function (e) {
        e.preventDefault();
        if ($(this).attr("class") == "current1") {
            return;
        } else {
            if($(this).attr("name")==1){
                alert("dsdsds")
                $(this).addClass("current1");
                $($(this).attr("name")).fadeIn();
            }
            //resetTabs1(this);

        }
    });
}
$(function(){
    loadTab1();
});
//弹窗tab页面
function smallTab() {

    $(".content1 > .tab2").hide();
    $(".adds1").each(function () {
        $(this).next("div").find(".add_tab1").show();
        $(this).find("li").find('a[name="add_tab1"]').addClass("current");
    });
    $(".content1").each(function () {
        $(this).find("div:first").fadeIn();
    });
    $(".adds1 a").on("click", function (e) {

        e.preventDefault();
        $(this).parent().parent().next("div").find(".tab2").hide();
        $(this).parent().parent().find("a").removeClass("current1");
        if ($(this).attr("class") == "current1"&&$(this)==$(".categorys").find("li:first a")) {
            return;
        }
        else {
            if ($(this).attr("name") == "add_tab1") {
                $(this).parent().parent().find("a").removeClass("current1");
                $(this).parent().parent().next("div").find(".add_tab1").show();
                $(this).addClass("current1");
                $( $(this).attr("name")).fadeIn();
            }
            else if($(this).attr("name") == "add_tab2"){
                $(this).parent().parent().find("a").removeClass("current1");
                $(this).parent().parent().next("div").find(".add_tab2").show();
                $(this).addClass("current1");
                $( $(this).attr("name")).fadeIn();
            }
        }
    });
}
$(function(){
    smallTab();
});




