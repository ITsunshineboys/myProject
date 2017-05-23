//实例化编辑器
//建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
var ue = UE.getEditor('editor');
var url='http://192.168.0.147:8080/YmhBackStage/';
        //添加新闻

var index_big_url,news_small;
        //上传文件
    $("#btn1").on("click", function (e) {
        e.preventDefault();
        var formData = new FormData($( "#uploadForm" )[0]);
        $.ajax({
            url: url+"file/upload.do?type=0" ,
            type: 'POST',
            data: formData,
            dataType: "json",
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                index_big_url=data.result[0].relativePath;
                alert("首页上传成功");
                console.log("index_big_url===1111======"+ index_big_url);
            },
            error: function (returndata) {
                alert(returndata);
            }
        });
    });
    $("#btn2").on("click", function (e) {
        e.preventDefault();
        var formData = new FormData($( "#uploadForm1" )[0]);
        $.ajax({
            url: url+"file/upload.do?type=0" ,
            type: 'POST',
            data: formData,
            dataType: "json",
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                news_small=data.result[0].relativePath;
                alert("新闻列表上传成功");
                console.log("news_small===1111======"+ news_small);
            },
            error: function (returndata) {
                alert(returndata);
            }
        });
    });

function getContent() {
    var arr = [];
    var detail="<p>丰东股份就看到<p/>";
    arr.push(UE.getEditor('editor').getContent());
    //function HTMLEncode(html)
    //{
    //    var temp = document.createElement ("div");
    //    (temp.textContent != null) ? (temp.textContent = html) : (temp.innerText = html);
    //    var output = temp.innerHTML;
    //    temp = null;
    //    return output;
    //}
    //
    //function HTMLDecode(text)
    //{
    //    var temp = document.createElement("div");
    //    temp.innerHTML = text;
    //    var output = temp.innerText || temp.textContent;
    //    temp = null;
    //    return output;
    //}
    //
    ////var html = "<br>dffdf<p>qqqqq</p>";
    //var encodeHTML = HTMLEncode(detail);
    //alert("方法一：" +encodeHTML);
    //var decodeHTML = HTMLDecode(encodeHTML);
    //alert("方法一：" +decodeHTML);




    alert("detail========"+detail)
    $(".html").text(UE.getEditor('editor').getContent());
    var title=$(".title").val();
    var type=$(".type").val();
    var jianjie=$(".jianjie").val();
    var state=$(".state").val();
    var yesTop=$(".yesTop").val();
    console.log("index_big_url===2222======"+ index_big_url);
    console.log("news_small===2222======"+ news_small);
    console.log("title=====22222222===="+title)
    console.log("type=====2222222===="+type)
    console.log("jianjie=====2222222===="+jianjie)
    console.log("state=====222222222===="+state)
    console.log("yesTop=====222222222===="+yesTop)
    console.log("html======"+UE.getEditor('editor').getContent())
    //JSON.stringify(detail)
    //alert(detail)

    $.ajax({
        url: url+"news/add.do?detail="+detail+"&type="+type+"&content="+jianjie
        +"&img_small=uploadfiles/images/news/new_indexbig01.png&img_mid="+news_small+"&img_big="+index_big_url
        +"&state="+state+"&yesTop="+yesTop+"&handler=161113182121724004056&title="+title,
        type: 'post',
        dataType: "json",
        //data:{detail:detail},
        processData : false,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        success: function (data) {
            if(data.err==1){
                alert("总提交成功");
            }
            else{
                alert("总提交失败");
            }

        },
        error:function(){
            //alert("ajax提交失败的处理函数！")
        }
    });
}
