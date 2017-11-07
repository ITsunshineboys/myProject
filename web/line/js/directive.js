let baseUrl = (function () {
    let stages = [
        "http://test.cdlhzz.cn", // 开发接口域名
        "http://v1.cdlhzz.cn" // 展示接口域名
    ];
    let stage = 0;
    try {
        let stageParam = location.search.split("&stage=")[1].split("&")[0];
        if (stages[stageParam])  {
            stage = stageParam;
        }
    } catch (e) {}
    return stages[stage];
})();



