
window.onload = function () {
    $.ajax( {
        method:"get",
        url:'http://test.cdlhzz.cn:888/site/all-roles',
        success:function (data) {
            document.write(data)
        }

    })
}