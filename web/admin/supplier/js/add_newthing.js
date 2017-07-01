/**
 * Created by xl on 2017/6/27 0027.
 */
//点击添加
$(".add_btn").on("click",function (){
    var newNode=$("<li>\
    <img src='images/red.png' alt=''/>\
    <button onclick='deleteAll (this)' class='delete_all'>-</button>\
    <span>长</span>\
    <input type='text' placeholder='请输入属性值'/>\
    <span>m</span>\
    </li>");
    $(".attr_data").append(newNode);
});

//点击删除
 function deleteAll (obj){
     $(obj).parent().remove();
 }

//获取数据  获取大后台 商品属性值 数据
