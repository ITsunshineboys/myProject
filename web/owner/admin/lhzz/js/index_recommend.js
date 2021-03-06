//推荐列表数据接口
var recommend_list="mall/recommend-admin-index";
//文件上传
var upload1="site/upload";
//删除文件
var upload_delete="site/upload-delete";
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
//历史数据接口
var recommend_history="mall/recommend-history";
//获取事件类型列表
var time_types="site/time-types";
//根据sku获取推荐信息
var gain_sku="mall/recommend-by-sku";
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
//城市选择
$(function () {
    'use strict';
    $('#distpicker1').distpicker();
});
app.controller("index_recommend",function($scope,$http){
    $scope.url=url;
    //
    $scope.kind_type=0;
    //默认城市的code
    $scope.district_code=510100;
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
    //搜索区域选择数据的获取事件
    //区域的原始数据
    $(document).on("change","#province2",function(){
        $scope.district_code=$("#city2").val();
        $scope.table_data1($scope.kind_type);

        console.log("$scope.city_code="+$scope.district_code)
    });
    $(document).on("change","#city2",function(){
        $scope.district_code=$(this).val();
        $scope.table_data1($scope.kind_type);

        console.log('city_code：'+$(this).val());//获取value
        console.log('city_name：'+$(this).find("option:selected").text());//获取选中文本
    });
    $scope.time_type= function () {
        $http({
            method:"GET",
            url:url+time_types
        })
            .success(function(data,status){
            $scope.time_kind=data.data.time_types;
            })
    };
    $scope.time_type();
    //搜索时间类型的变化获取新的历史数据
    $(document).on("change","#time_kind",function(){
        $scope.time_type1=$(this).val();
        if($scope.time_type1=="custom"){
            $(".custom").addClass("show").removeClass("hide")
        }
        else{
            $scope.history($scope.kind_type,$scope.time_type1,"","");
            $(".custom").addClass("hide").removeClass("show")
        }
    });
    //自定义搜索时间范围的搜索事件
    $scope.search=function(){
        $scope.start_time=$("#start").text();
        $scope.end_time=$("#end").text();
        if($scope.start_time=="开始时间" || $scope.start_time==""){
            //弹窗的显示
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .delete1").addClass('show').removeClass("hide");
            $(".popup .delete1 .not_stop").addClass('show').removeClass("hide");
            $(".popup .delete1 .not_stop .warm_word1").text('请先输入开始时间');
            //没有填入开始时间的关闭按钮
            $scope.del_close1=function(){
                //弹窗的隐藏
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .delete1").addClass('hide').removeClass("show");
                $(".popup .delete1 .not_stop").addClass('hide').removeClass("show");
            }
        }
        else if(($scope.start_time!="开始时间" && $scope.start_time!="")&& ($scope.end_time=="结束时间" || $scope.end_time=="") ){
            //弹窗的显示
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .delete1").addClass('show').removeClass("hide");
            $(".popup .delete1 .not_stop").addClass('show').removeClass("hide");
            $(".popup .delete1 .not_stop .warm_word1").text('请先输入结束时间');
            //没有填入开始时间的关闭按钮
            $scope.del_close1=function(){
                //弹窗的隐藏
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .delete1").addClass('hide').removeClass("show");
                $(".popup .delete1 .not_stop").addClass('hide').removeClass("show");
            }
        }else if($scope.start_time!="开始时间"&& $scope.end_time!="结束时间"){
            $scope.history($scope.kind_type,$scope.time_type1,$scope.start_time, $scope.end_time);
        }
        console.log("$scope.start_time"+$scope.start_time);
        console.log("$scope.end_time"+$scope.end_time);
    };
    //点击历史数据按钮事件
    $scope.history=function(kind,time_kind1,start_time,end_time){
        $scope.page = 1;
        $scope.page_size = 12;
        $scope.kind_type=kind;
        //清空上一次分页的数据
        $('#pageTool').text("");
        $http({
            method: "GET",
            url: url + recommend_history+"?type="+$scope.kind_type+"&district_code="+$scope.district_code+
            "&time_type="+time_kind1+"&start_time="+start_time+
            "&end_time="+end_time+"&page="+ $scope.page+"&size="+ $scope.page_size
        })
            .success(function (data, status) {
                $scope.history_data = data.data.recommend_history.details;
                $scope.history_total = data.data.recommend_history.total;
                console.log(" $scope.history_total="+ $scope.history_total);
                $scope.page_count=$scope.history_total/$scope.page_size;
                $('#pageTool').Paging({
                    pagesize: $scope.page, count: $scope.page_count,toolbar:true,ellipseTpl:". . .",  callback: function (page, size, count) {
                        console.log(arguments);
                        //点击页码后重新访问接口获取当前页码的数据
                        $scope.page=page;
                        $http({
                            method: "GET",
                            url: url + recommend_history+"?type="+$scope.kind_type+"&district_code="+$scope.district_code+"&time_type="+time_kind1+"&start_time="+start_time+"&end_time="+end_time+"&page="+ $scope.page+"&size="+ $scope.page_size
                        })
                            .success(function (data, status) {
                                $scope.history_data = data.data.recommend_history.details;
                             });

                        console.log('当前第 ' + page + '页,每页 ' + size + '条,总页数：' + count + '页')
                    }
                });
            }).
            error(function (data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
            });
        $(".tb_big_box").hide();
        $(".history_box").addClass("show").removeClass("hide");
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
        $(".history_box").addClass("hide").removeClass("show");
        $(".history").addClass("hide").removeClass("show");
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
    $scope.table_data1=function(data_type){
        $http({
            method: "GET",
            url: url + recommend_list+"?type="+data_type+"&district_code="+$scope.district_code
        })
            .success(function (data, status) {
                //$scope.page = 1;
                //$scope.mydata = [{"id":"1","sku":"12310002","title":"t","from_type":"商铺","status":"启用","create_time":"2017-04-01","image":"uploads/2017/05/13/1494649348.jpg","url":"mall/goods?id=2","supplier_name":"链接","delete_time":"0","viewed_number":0,"sold_number":0},{"id":"3","sku":"12310002","title":"t","from_type":"商铺","status":"停用","create_time":"2017-05-15","image":"uploads/2017/05/13/1494649348.jpg","url":"http://www.baidu.com","supplier_name":"supplier 1","delete_time":"0","viewed_number":0,"sold_number":0},{"id":"4","sku":"12310001","title":"t","from_type":"商铺","status":"停用","create_time":"2017-05-15","image":"uploads/2017/05/13/1494649348.jpg","url":"http://www.baidu.com","supplier_name":"supplier 1","delete_time":"0","viewed_number":0,"sold_number":0}]
               if(data_type==0){
                   $scope.mydata1 = data.data.recommend_admin_index.details;
               }
                else if(data_type==2){
                   $scope.mydata2 = data.data.recommend_admin_index.details;
                   //$scope.mydata2 = [{"id":"1","sku":"12310002","title":"t","from_type":"链接","status":"启用","create_time":"2017-04-01","image":"uploads/2017/05/13/1494649348.jpg","url":"mall/goods?id=2","supplier_name":"链接","delete_time":"0","viewed_number":0,"sold_number":0},{"id":"3","sku":"12310002","title":"t","from_type":"商铺","status":"停用","create_time":"2017-05-15","image":"uploads/2017/05/13/1494649348.jpg","url":"http://www.baidu.com","supplier_name":"supplier 1","delete_time":"0","viewed_number":0,"sold_number":0},{"id":"4","sku":"12310001","title":"t","from_type":"商铺","status":"停用","create_time":"2017-05-15","image":"uploads/2017/05/13/1494649348.jpg","url":"http://www.baidu.com","supplier_name":"supplier 1","delete_time":"0","viewed_number":0,"sold_number":0}];
               }


            }).
            error(function (data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
            });
    };
    //页面初始加载数据
    //banner的数据初始化==传的值代表0代表banner，2代表推荐产品
    $scope.table_data1(0);
    //推荐商品数据的初始化
    $scope.table_data1(2);
    $scope.$on('ngRepeatFinished', function (data) { //接收广播，一旦repeat结束就会执行
        //表格显示列的初始化控制
        //banner的表格显示初始化
        $(".tb_stock").hide();
        $(".tb_supplier_price").hide();
        $(".tb_platform_price").hide();
        $(".tb_market_price").hide();
        //商城首页的推荐表格显示的初始化
        $(".tb_description1").hide();
        $(".tb_stock1").hide();
        $(".tb_supplier_price1").hide();
        $(".tb_platform_price1").hide();
        $(".tb_market_price1").hide();
        $(".tb_show_price1").hide();
        //历史数据的表格显示的初始化
        $(".tb_status2").hide();
        $(".tb_delete_time2").hide();
        $(".tb_description2").hide();
        $(".tb_stock2").hide();
        $(".tb_supplier_price2").hide();
        $(".tb_platform_price2").hide();
        $(".tb_market_price2").hide();
        $(".tb_show_price2").hide();
        //列的文字显示的处理
        $(".tb_overflow").each(function(){
            var maxwidth=6;
            if($(this).text().length>maxwidth){
                $(this).text($(this).text().substring(0,maxwidth));
                $(this).html($(this).html()+'…');
            }
        });
        //文字超长列的鼠标移入效果
        $(".tb_overflow").hover(function(e){
            $scope.word=$(this).attr("name")
            //console.log($(this).find(".all_name").text())
            $("#warn").text($scope.word) .css({"top":(e.pageY -10) + "px","left":(e.pageX +20) + "px","display":"block"})
        });
        $(".tb_overflow").mousemove(function(e){
            $("#warn").css({"top":(e.pageY -10) + "px","left":(e.pageX +20) + "px","display":"block","transition":"all .3s linear"});
        });
        $(".tb_overflow").mouseout(function(e){
            $("#warn").css({"top":(100) + "px","left":(100) + "px","display":"none"})
            ;
        });
        //排序操作处理
        $scope.order_alter=function(){
            var fixHelper = function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());     //在拖动时，拖动行的cell（单元格）宽度会发生改变。在这里做了处理就没问题了
                });
                return ui;
            };
            jQuery(function(){
            $(".order_tbody").sortable({                //这里是talbe tbody，绑定 了sortable
                helper: fixHelper,                  //调用fixHelper
                axis:"y",
                start:function(e, ui){
                    //ui.helper.css({"background":"red"})     //拖动时的行，要用ui.helper
                    return ui;
                },
                stop:function(e, ui){
                    //拖拽完成事件
                   //释放鼠标时，要用ui.item才是释放的行
                    return ui;
                }
            }).disableSelection();
            })
        };
        $scope.order_alter();
        //数据已连接====单个删除事件的控制
        $scope.dele=function(id){
            var del_id1="."+id;
            $scope.del_id=id;
            $scope.del_stop=$("tbody").find(del_id1).find(".status").text();
            if($scope.del_stop=="启用"){
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
            else if($scope.del_stop=="停用"){
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
                                $scope.table_data1($scope.kind_type);
                            }

                        }
                    });
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .delete1").addClass('hide').removeClass("show");
                    $(".popup .delete1 .is_stop").addClass('hide').removeClass("show");
                    $("tbody").find(del_id1).remove();
                };
                $scope.del_cancel=function(){
                    console.log("删除的取消事件")
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .delete1").addClass('hide').removeClass("show");
                    $(".popup .delete1 .is_stop").addClass('hide').removeClass("show");
                }
            }
        };
        //数据已连接=====单个停用事件的控制
        $scope.stop_one=function(obj,status){
            $scope.stop_id=obj;
            $scope.stop_status=status;
            $scope.stop_count=1;
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
                        $scope.stop_data=data;
                        if($scope.stop_data.code==200){
                            console.log("停用成功");
                            $scope.table_data1($scope.kind_type);
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
            console.log("choose=="+choose);
            console.log("now_choose=="+now_choose);
            if($(".choose_all1").is(':checked')){
               console.log("选中");
                $(now_choose).prop("checked", true);
                //$('.choose').attr('checked', 'checked');
            }
            else if(!$(".choose_all1").is(':checked')){
                $(now_choose).prop("checked",false);
                //$(".choose").prop("checked") == false


                //$(".choose").attr("checked",false)//未选中
                console.log("没有选中")
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
                    var cal="."+$(this).val();
                    $scope.now_stop=$(cal).find(".status").text();
                    console.log($(cal).find(".status").text());
                    if($scope.now_stop=="启用"){
                        $scope.is_stop=false;
                        text.push($(this).val());
                    }
                    pitch_text.push($(this).val());

                }
            });
            if(pitch_text==""){
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
                $scope.del_ids=pitch_text.join(",");
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
                    console.log("text==="+text.join(","));
                    $(".popup").addClass('show').removeClass("hide");
                    $(".popup .delete1").addClass('show').removeClass("hide");
                    $(".popup .delete1 .is_stop").addClass('show').removeClass("hide");
                    $scope.del_sure=function(){
                        $(".popup").addClass('hide').removeClass("show");
                        $(".popup .delete1").addClass('hide').removeClass("show");
                        $(".popup .delete1 .is_stop").addClass('hide').removeClass("show");
                        console.log("text.length====="+text.length);
                        $.ajax({
                            url: url+recommend_deletes,
                            type: 'POST',
                            data:{"ids":$scope.del_ids},
                            dataType: "json",
                            contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                            success: function (data) {
                                $scope.loginout=data;
                                if($scope.loginout.code==200){
                                    $scope.table_data1($scope.kind_type);
                                }
                            }
                        });
                        for(var a=0;a<pitch_text.length;a++){
                            $scope.del_class="."+pitch_text[a];
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
            var text=[];
            var stops=[];
            $("input[name=item]").each(function() {
                if ($(this).prop("checked") == true) {
                    var cal="."+$(this).val();
                    $scope.now_stop=$(cal).find(".status").text();
                    if($scope.now_stop=="启用"){
                        $scope.stop_status="启用";
                        stops.push($(this).val());
                    }
                    text.push($(this).val());
                }
            });
            $scope.stop_count=text.length;
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
                        data:{"ids":$scope.stop_ids},
                        dataType: "json",
                        contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                        success: function (data) {
                            $scope.loginout=data;
                            if($scope.loginout.code==200){
                                $scope.table_data1($scope.kind_type);
                            }
                        }
                    });
                    $(".popup").addClass('hide').removeClass("show");
                    $(".popup .stop1").addClass('hide').removeClass("show");
                    $(".popup .stop1 .is_stop").addClass('hide').removeClass("show");
                    console.log("text.length====="+text.length);
                    console.log("stops.length====="+stops.length)

                };
            }
        };
        //上传文件
        $scope.doUpload=function (load) {
            var file_id="#myfile"+load;
            var form_id1="#uploadForm"+load;
            console.log("form_id1=="+form_id1);
            $(document).on("change",file_id,function(){
                $scope.doUpload(load)
            });
            var formData = new FormData($( form_id1 )[0]);
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
                        $scope.up_data=data;
                        if(load==1){
                            $scope.add_image1=data.data.file_path;
                            console.log( "1111成功");
                        }
                        else if(load==2){
                            $scope.add_image2=data.data.file_path;
                            console.log( "222222成功"+$scope.load2);
                        }
                        else if(load==3){
                            $scope.edit_image2=data.data.file_path;
                            console.log( "3333成功");
                            console.log( "3333成功 $scope.edit_image2="+ $scope.edit_image2);
                        }
                        else if(load==4){
                            $scope.edit_image2=data.data.file_path;
                            console.log( "444444成功 $scope.edit_image2="+ $scope.edit_image2);
                        }
                    },
                    error: function (returndata) {
                        console.log(returndata);
                    }
            });
        };
        //删除文件事件处理
        $scope.del_photo=function(obj){
            $.ajax({
                url: url+ upload_delete,
                type: 'POST',
                data:{"file_path":obj},
                dataType: "json",
                contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                success: function (data) {
                    //$scope.loginout=data;
                    console.log("删除文件成功");

                }
            });
        };
        //根据sku获取推荐信息
        $scope.gain_recommend=function(sku,sku_type){
            $scope.sku1=sku;
            $http({
                method: "GET",
                url: url + gain_sku+"?sku="+$scope.sku1
            })
                .success(function (data, status) {
                    if(sku_type=="添加"){
                        $scope.add_url=data.data.detail.url;
                        $scope.add_name=data.data.detail.title;
                        $scope.add_url1=data.data.detail;
                        $scope.add_description=data.data.subtitle;
                        $scope.add_price=data.data.platform_price;
                        console.log("获取的 $scope.add_url"+ $scope.add_url)
                        console.log("获取的 $scope.add_name"+ $scope.add_name)
                        console.log("获取的 $scope.add_price"+ $scope.add_price)
                        console.log("获取的 $scope.add_description"+ $scope.add_description)
                    }
                    else  if(sku_type=="编辑"){
                        $scope.edit_url=data.data.detail.url;
                        $scope.edit_name=data.data.detail.title;
                        $scope.edit_url1=data.data.detail;
                        $scope.edit_description=data.data.subtitle;
                        $scope.edit_price=data.data.platform_price;
                        console.log("获取的 $scope.edit_url"+ $scope.edit_url)
                        console.log("获取的 $scope.edit_name"+ $scope.edit_name)
                        console.log("获取的 $scope.edit_price"+ $scope.edit_price)
                        console.log("获取的 $scope.edit_description"+ $scope.edit_description)
                    }
                })

        };
        //添加事件的处理
        $scope.add=function(){
            $scope.add_sku="";
            $scope.add_url="";
            $scope.add_name="";
            $scope.add_statu=0;
            $scope.add_description="";
            $scope.add_price="";
            $scope.add_image="";
            $scope.add_image1="";
            $scope.add_image2="";

            $(".popup").addClass('show').removeClass("hide");
            $(".popup .add1").addClass('show').removeClass("hide");
            $('input[name=sex1]').click(function(){
                $scope.add_statu=$(this).val();

                console.log("非非点击确认外===链接的val==="+$(this).val())
            });
            //链接的是否停用的值得获取
            $('input[name=sex2]').click(function(){
                $scope.add_statu=$(this).val();
                console.log("非非点击确认外===链接的val==="+$(this).val())
            });
            $scope.add_sure=function(obj){
                $scope.add_form_type=obj;
                if($scope.add_form_type==1){
                    $scope.add_image=$scope.add_image1;
                }
                else if($scope.add_form_type==2){
                    $scope.add_image=$scope.add_image2;
                }
                console.log("是banner还是推荐==="+$scope.kind_type);
                console.log("编辑$scope.add_url==="+$scope.add_url);
                console.log("编辑$scope.add_name==="+$scope.add_name);
                console.log("编辑$scope.add_image==="+$scope.add_image);
                console.log("编辑$scope.add_form_type==="+$scope.add_form_type);
                console.log("编辑$scope.add_statu==="+$scope.add_statu);
                //console.log("编辑$scope.edit_type==="+$scope.edit_type);
                console.log("编辑:$scope.add_sku==="+$scope.add_sku);
                console.log("编辑$scope.add_description==="+$scope.add_description);
                console.log("编辑$scope.add_price==="+$scope.add_price);
                $.ajax({
                    url: url+ recommend_add,
                    type: 'POST',
                    data:{"url":$scope.add_url,"title":$scope.add_name,"image":
                        $scope.add_image,"from_type":$scope.add_form_type,"status":$scope.add_statu,
                        "type":$scope.kind_type,"sku":$scope.add_sku,"description":$scope.add_description,
                        "platform_price":$scope.add_price,"district_code":$scope.district_code},
                    dataType: "json",
                    contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                    success: function (data) {
                        $scope.add_data3=data;
                        console.log("添加成功");
                        $scope.table_data1($scope.kind_type);
                    },
                    error: function (returndata) {
                        console.log("错误=="+returndata);
                        $scope.add_data4=returndata;
                    }
                });

                $(".popup").addClass('hide').removeClass("show");
                $(".popup .add1").addClass('hide').removeClass("show");
            };
            $scope.add_cancel=function(){
                $scope.del_photo($scope.add_image2);
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .add1").addClass('hide').removeClass("show");
            };
        };
        //查看事件处理
        $scope.check_btn=function(viewed_number,sold_number,check_create_time,check_from_type,check_id,check_name1,check_description, check_sku1,check_supplier,check_url2, check_status2,check_image2, check_supplier_price, check_platform_price, check_market_price,check_show_price, check_left_number){
            $scope.check_viewed_number=viewed_number;
            $scope.check_sold_number=sold_number;
            $scope.check_from_type=check_from_type;
            $scope.check_create_time=check_create_time;
            $scope.check_id=check_id;
            $scope.check_name=check_name1;
            $scope.check_description=check_description;
            $scope.check_sku=check_sku1;
            $scope.check_supplier=check_supplier;
            $scope.check_url=check_url2;
            $scope.check_status=check_status2;
            $scope.check_image2=check_image2;
            $scope.check_supplier_price=check_supplier_price;
            $scope.check_platform_price=check_platform_price;
            $scope.check_market_price=check_market_price;
            $scope.check_show_price=check_show_price;
            //库存
            $scope.check_left_number=check_left_number;
            $(".popup").addClass('show').removeClass("hide");
            $(".popup .details_pop").addClass('show').removeClass("hide");
            $scope.details_close1=function(){
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .details_pop").addClass('hide').removeClass("show");
            }

        };
        //编辑事件处理
        $scope.edit=function(edit_id,edit_name1,edit_description,edit_sku1,edit_supplier,edit_url2,edit_status2,edit_image2,edit_price,edit_from_type){
            $scope.edit_id=edit_id;
            $scope.edit_name=edit_name1;
            $scope.edit_description=edit_description;
            $scope.edit_sku=edit_sku1;
            $scope.edit_supplier=edit_supplier;
            $scope.edit_url=edit_url2;
            $scope.edit_status=edit_status2;
            $scope.edit_image2=edit_image2;
            $scope.edit_price=edit_price;
            $scope.edit_from_type=edit_from_type;
            console.log("$scope.edit_supplier=="+$scope.edit_supplier);
            //是否停用的初始化
            if($scope.edit_status=="停用"){
                //alert("停用")
                $scope.edit_statu=0;
                $("#yes3").attr('checked', 'checked');
                $("#yes4").attr('checked', 'checked');
            }
            else if($scope.edit_status=="启用"){
                //alert("启用")
                $scope.edit_statu=1;
                $("#no3").attr('checked', 'checked');
                $("#no4").attr('checked', 'checked');
            }
            if($scope.edit_from_type=="链接"){
                $scope.from_type=2;
                //$scope.add_image2=$scope.load4.file_path;
                console.log("链接的val==="+$('input:radio[name="sex4"]:checked').val())
            }
            else{
                $scope.from_type=1;
                //$scope.add_image2=$scope.load3.file_path;
                console.log("非非链接的val==="+$('input[name=sex3][checked]').val()   )
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
                $scope.del_photo($scope.edit_image2);
                $(".popup").addClass('hide').removeClass("show");
                $(".popup .edit1").addClass('hide').removeClass("show");
                $(".popup .edit1 .is_edit").addClass('hide').removeClass("show");
            };
            //确认编辑
            //商品的是否停用的值得获取

            $('input[name=sex3]').click(function(){
                $scope.edit_statu=$(this).val();

                console.log("非非点击确认外===链接的val==="+$(this).val())
            });
            //链接的是否停用的值得获取
            $('input[name=sex4]').click(function(){
                $scope.edit_statu=$(this).val();
                console.log("非非点击确认外===链接的val==="+$(this).val())
            });
            $scope.edit_sure=function() {
                console.log("是banner还是推荐==="+$scope.kind_type);
                console.log("编辑$scope.edit_id==="+$scope.edit_id);
                console.log("编辑$scope.edit_url==="+$scope.edit_url);
                console.log("编辑$scope.edit_name==="+$scope.edit_name);
                console.log("编辑$scope.edit_image2==="+$scope.edit_image2);
                console.log("编辑$scope.from_type==="+$scope.from_type);
                console.log("编辑$scope.edit_statu==="+$scope.edit_statu);
                console.log("编辑$scope.edit_sku==="+$scope.edit_sku);
                console.log("编辑$scope.edit_description==="+$scope.edit_description);
                console.log("编辑$scope.edit_price==="+$scope.edit_price);

                $.ajax({
                    url: url+recommend_edit,
                    type: 'POST',
                    data:{"id":$scope.edit_id,"url":$scope.edit_url,"title":$scope.edit_name,"image":
                        $scope.edit_image2,"from_type":$scope.from_type,"status":$scope.edit_statu,
                    "type":$scope.kind_type,"sku":$scope.edit_sku,"description":$scope.edit_description,
                    "platform_price":$scope.edit_price},
                    dataType: "json",
                    contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                    success: function (data) {
                        $scope.edit_data=data;
                        console.log("编辑成功");
                        $scope.table_data1($scope.kind_type);

                    }
                });
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
                console.log(order2.eq(c).attr("name"));
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
                $scope.saves=save_id.join(",");
                console.log("$scope.saves=="+$scope.saves);
                console.log("进入排序保存函数");
                $.ajax({
                    url: url+recommend_sort,
                    type: 'POST',
                    data:{"ids":$scope.saves},
                    dataType: "json",
                    contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                    success: function (data) {
                        $scope.save_data=data;
                        console.log(" $scope.save_data="+ $scope.save_data)
                        if($scope.save_data.code==200){
                            $scope.table_data1($scope.kind_type);
                        }
                    }
                });
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
//

//导航栏
//$(".nav_box dt").css({"background-color":"#F8F8F8"});
$(".nav_box dt img").attr("src","images/select1.png");
$(function(){
    $(".nav_box dd").hide();
    //初始该显示的dd
    $(".nav_box .dl_on>dd").show();
    $(".nav_box dt").click(function(){
        $(".nav_box dt").css({"background-color":"#F5F7FA","color":"#ABABAB"});
        $(this).css({"background-color": " #E6E9F0;","color":"#5677FC"});
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




