let baseUrl = (function () {
    let stages = [
        "http://test.cdlhzz.cn", // �����ӿ�����
        "http://v1.cdlhzz.cn" // չʾ�ӿ�����
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



