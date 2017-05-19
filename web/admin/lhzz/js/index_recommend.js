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
//编辑接口
var recommend_edit="mall/recommend-edit";
//添加接口
var recommend_add="mall/recommend-add";
var data1,data2;
//日历
var start = {
    elem: '#start',
    format: 'YYYY-MM-DD ',
    //min: laydate.now(), //设定最小日期为当前日期
    max: laydate.now(), //最大日期
    istoday: true,
    choose: function(datas){
        end.min = datas; //开始日选好后，重置结束日的最小日期
        end.start = datas; //将结束日的初始值设定为开始日
    }
};
var end = {
    elem: '#end',
    format: 'YYYY-MM-DD ',
    //min: laydate.now(),
    max:laydate.now(),
    istoday: true,
    choose: function(datas){
        start.max = datas; //结束日选好后，重置开始日的最大日期
    }
};
laydate(start);
laydate(end);
app.controller("index_recommend",function($scope,$http){
    $scope.url=url;
    $scope.kind_type=0;
    $scope.zishiy=function (){
        var browser_width1=window.innerWidth-$(".nav_box").width();
        $(".my_container").css("width",browser_width1);
        $(".header_box").css("width",browser_width1);
        //浏览器大小变化的监听
        $(window).resize(function() {
            var browser_width1=window.innerWidth-$(".nav_box").width();
            $(".my_container").css("width",browser_width1);
            $(".header_box").css("width",browser_width1);
        });
    };
    $scope.zishiy();
    $scope.history=function(kind){
        $scope.kind_type=kind;
        $(".tb_big_box").hide();
        $(".history_box").show();
        $(".history").addClass("show").removeClass("hide");
        $("#nav_2").removeClass("color1_8d").addClass("color_2f");
        console.log("history== $scope.kind_type"+ $scope.kind_type)
    };
    //teb页面开始
    //tab页面的动态初始化
    $scope.int=function (obj,now_class){
        //if(now_class==null||now_class==0){
        //    now_class=11;
        //}
        var my_class=String("."+now_class);
        obj.next("div").find(my_class).show();
        obj.find(".kind_li").find("a").removeClass("current");
        obj.find(".kind_li").find('a[name="'+now_class+'"]').addClass("current");

    };
    //点击的样式变化函数
    $scope.Curve=function  (obj,name){
        $scope.kind_type=name;
        var mykind=String("."+name);

        console.log(" 点击tab页面后$scope.kind_type=="+ $scope.kind_type)
        if (obj.attr("name") == name) {
            obj.parent().parent().find("a").removeClass("current");
            obj.parent().parent().next("div").find(".tab").hide();
            obj.parent().parent().next("div").find(mykind).show();
            obj.addClass("current");
            $(obj.attr("name")).fadeIn();
        }
    };
    $scope.loadTab=function (type) {
        $scope.kind_type=type;
        $scope.zishiy();
        //开始===点击首页推荐的页面内容显示的控制
        $("#nav_2").removeClass("color_2f").addClass("color1_8d");
        $(".tb_big_box").show();
        $(".history_box").hide();
        //结束===点击首页推荐的页面内容显示的控制
        $(".content > div").hide();
        $(".tabs").each(function () {
            var obj=$(this);
            $scope.int(obj,type);
        });
        $(".content").each(function () {
            var mytype="."+type;
            $(this).find(mytype).fadeIn();
        });
        $(".tabs a").on("click", function (e) {

            e.preventDefault();
            $(this).parent().parent().next("div").find(".tab2").hide();
            $(this).parent().parent().find("a").removeClass("current");
            if ($(this).attr("class") == "current"&&$(this)==$(".categorys").find("li:first a")) {
                return;
            }
            else {
                var obj=$(this);
                var  myname=$(this).attr("name");
                console.log("myname==="+myname)
                $scope.Curve(obj, myname)
            }
        });
    };
    $(function(){
        $scope.loadTab(0);
    });
    //tab页面的结束

    //加载页面表格数据函数
    $scope.table_data1=function(){
        $http({
            method: "GET",
            url: url + recommend_list
        })
            .success(function (data, status) {
                //$scope.page = 1;
                //$scope.mydata = [{"id":"1","sku":"12310002","title":"t","from_type":"商铺","status":"启用","create_time":"2017-04-01","image":"uploads/2017/05/13/1494649348.jpg","url":"mall/goods?id=2","supplier_name":"链接","delete_time":"0","viewed_number":0,"sold_number":0},{"id":"3","sku":"12310002","title":"t","from_type":"商铺","status":"停用","create_time":"2017-05-15","image":"uploads/2017/05/13/1494649348.jpg","url":"http://www.baidu.com","supplier_name":"supplier 1","delete_time":"0","viewed_number":0,"sold_number":0},{"id":"4","sku":"12310001","title":"t","from_type":"商铺","status":"停用","create_time":"2017-05-15","image":"uploads/2017/05/13/1494649348.jpg","url":"http://www.baidu.com","supplier_name":"supplier 1","delete_time":"0","viewed_number":0,"sold_number":0}]
    $scope.mydata = data.data.recommend_admin_index.details;

            }).
            error(function (data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
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
        $scope.all=function(choose){
            var now_choose="."+choose;
            console.log("choose=="+choose)
            console.log("now_choose=="+now_choose)
            if($(".choose_all1").is(':checked')){
                alert("选中");
                $(now_choose).prop("checked", true);
                //$('.choose').attr('checked', 'checked');
            }
            else if(!$(".choose_all1").is(':checked')){
                $(now_choose).prop("checked",false);
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
        $scope.edit=function(edit_id,edit_name1,edit_sku1,edit_supplier,edit_url2,edit_status2,edit_image2){
            $scope.edit_id=edit_id;
            $scope.edit_name=edit_name1;
            $scope.edit_sku=edit_sku1;
            $scope.edit_supplier=edit_supplier;
            $scope.edit_url=edit_url2;
            $scope.edit_status=edit_status2;
            $scope.edit_image2=edit_image2;
            console.log("$scope.edit_supplier=="+$scope.edit_supplier)
            if($scope.edit_status=="停用"){
                //alert("停用")
                $("#yes3").attr('checked', 'checked');
                $("#yes4").attr('checked', 'checked');
            }
            else if($scope.edit_status=="启用"){
                //alert("启用")
                $("#no3").attr('checked', 'checked');
                $("#no4").attr('checked', 'checked');
            }
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .edit1").addClass('show').removeClass("hide");
            $(".popup .edit1 .is_edit").addClass('show').removeClass("hide");
            if($scope.edit_supplier=="链接"){
                $(".popup .edit1 .is_edit .link_box").addClass('show').removeClass("hide");
                $(".popup .edit1 .is_edit .Product_box").addClass('hide').removeClass("show");
            }
            else{
                $(".popup .edit1 .is_edit .Product_box").addClass('show').removeClass("hide");
                $(".popup .edit1 .is_edit .link_box").addClass('hide').removeClass("show");
            }
            //取消编辑
            $scope.edit_cancel=function(){
                //弹窗的隐藏
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .edit1").addClass('hide').removeClass("show");
                $(".popup .edit1 .is_edit").addClass('hide').removeClass("show");
            };
            //确认编辑
            //商品的是否停用的值得获取
            $('input[name=sex3]').click(function(){
                $scope.sex3_val=$(this).val()
                console.log("非非点击确认外===链接的val==="+$(this).val())
            });
            //链接的是否停用的值得获取
            $('input[name=sex4]').click(function(){
                $scope.sex4_val=$(this).val()
                console.log("非非点击确认外===链接的val==="+$(this).val())
            });
            $scope.edit_sure=function(obj) {
                console.log("类型==="+obj);
                if(obj=="链接"){
                    $scope.from_type=2;
                    console.log("链接的val==="+$('input:radio[name="sex4"]:checked').val())
                }
                else{
                    $scope.from_type=1;
                    $('input[name=sex3]').click(function(){
                        console.log("非非点击===链接的val==="+$(this).val())
                    });
                    console.log("非非链接的val==="+$('input[name=sex3][checked]').val()   )
                }

                $(".popup").addClass('hide').removeClass("show");
                $(".popup .edit1").addClass('hide').removeClass("show");
                $(".popup .edit1 .is_edit").addClass('hide').removeClass("show");

            }
        };
        //查看事件
        $scope.look=function(url){
            console.log("我的url=="+url);
            window.open($scope.url+url);
            //window.open("http://www.baidu.com");

        };
        //顺序更换保存
        $scope.save1=function(){
            var order2=$("tbody tr ");
            var save_id=[];
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
        }
        //控制哪些列显示哪些不显示
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
});
////teb页面
////tab页面的动态初始化
//function int(obj,now_class){
//    if(now_class==null||now_class==0){
//        now_class=11;
//    }
//    var my_class=String("."+now_class);
//    obj.next("div").find(my_class).show();
//    obj.find(".categorys_li").find('a[name="'+now_class+'"]').addClass("current");
//
//}
////点击的样式变化函数
//function  Curve(obj,name){
//    var mykind=String("."+name);
//    console.log("mykind=="+mykind)
//    if (obj.attr("name") == name) {
//        obj.parent().parent().find("a").removeClass("current");
//        obj.parent().parent().next("div").find("div").hide();
//        obj.parent().parent().next("div").find(mykind).show();
//        obj.addClass("current");
//        $(obj.attr("name")).fadeIn();
//    }
//}
//function loadTab(type) {
//
//    $(".content > div").hide();
//    $(".tabs").each(function () {
//        var obj=$(this);
//        var my_class=type;
//        int(obj,my_class);
//        //$(this).find("li:first a").addClass("current");
//    });
//    $(".content").each(function () {
//        $(this).find("div:first").fadeIn();
//    });
//    $(".tabs a").on("click", function (e) {
//
//        e.preventDefault();
//        $(this).parent().parent().next("div").find(".tab2").hide();
//        $(this).parent().parent().find("a").removeClass("current");
//        if ($(this).attr("class") == "current"&&$(this)==$(".categorys").find("li:first a")) {
//            return;
//        }
//        else {
//            var obj=$(this);
//            var  myname=$(this).attr("name");
//            console.log("myname==="+myname)
//            Curve(obj, myname)
//        }
//    });
//}
//$(function(){
//    loadTab(0);
//});
//function resetTabs(obj) {
//    $(obj).parent().parent().next("div").find(".tab").hide();
//    $(obj).parent().parent().find("a").removeClass("current");
//}
//function loadTab() {
//    $(".content > div").hide();
//    $(".tabs").each(function () {
//        $(this).find("li:first a").addClass("current");
//    });
//    $(".content").each(function () {
//        $(this).find("div:first").fadeIn();
//    });
//    $(".tabs a").on("click", function (e) {
//        e.preventDefault();
//        if ($(this).attr("class") == "current") {
//            return;
//        } else {
//            resetTabs(this);
//            $(this).addClass("current");
//            $($(this).attr("name")).fadeIn();
//        }
//    });
//}
//$(function(){
//
//    loadTab();
//
//});
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




