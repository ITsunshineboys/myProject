app.controller("index_ctrl", function ($rootScope, $scope, _ajax, $state) {
    $rootScope.baseUrl = baseUrl;
    $scope.loginOut = function () {
        _ajax.post('/site/admin-logout', {}, function (res) {
            if (res.code === 200) {
                // sessionStorage.removeItem('finance_menu');
                // sessionStorage.removeItem('mall_menu');
                // sessionStorage.removeItem('mall_dd_menu');
                // sessionStorage.removeItem('finance_dd_menu');
                // sessionStorage.removeItem('account_menu');
                // sessionStorage.removeItem('account_dd_menu');
                // sessionStorage.removeItem('other_menu');
                sessionStorage.clear()
                window.location.href = 'login.html'
            }
        })
    };
    //富文本框配置
    $rootScope.config = {
        // 定制图标
        toolbars: [
            ['Undo', 'Redo', 'formatmatch', 'removeformat', 'Bold', 'italic', 'underline', 'strikethrough', 'fontborder',
                'horizontal', 'fontfamily', 'fontsize', 'justifyleft', 'justifyright',
                'justifycenter', 'justifyjustify', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist',
                'rowspacingtop', 'rowspacingbottom', 'imagecenter', 'simpleupload', 'time', 'date', 'preview']
        ],

        //首行缩进距离,默认是2em
        indentValue: '2em',
        //初始化编辑器宽度,默认1000
        initialFrameWidth: 800,
        //初始化编辑器高度,默认320
        initialFrameHeight: 320,
        //编辑器初始化结束后,编辑区域是否是只读的，默认是false
        readonly: false,
        //启用自动保存
        enableAutoSave: false,
        //自动保存间隔时间， 单位ms
        saveInterval: 1000,
        //是否开启初始化时即全屏，默认关闭
        fullscreen: false,
        //图片操作的浮层开关，默认打开
        imagePopup: true,
        //提交到后台的数据是否包含整个html字符串
        allHtmlEnabled: false,
        //是否启用元素路径，默认是显示
        elementPathEnabled: false,
        //是否开启字数统计
        wordCount: false
    }

    //侧边栏导航
    $scope.mall_flag = false;//商城管理
    $scope.finance_flag = false;//财务中心
    $scope.account_flag = false;//账户管理
    $scope.other_flag = 'home';
    $scope.mall_active = 0;
    $scope.finance_active = 0;
    $scope.account_active = 0;

    //商城管理
    $scope.mall_obj = [
        {id: 0, name: '商城数据', link: 'merchant_index'},
        {id: 1, name: 'APP搜索位-banner管理', link: 'banner_recommend'},
        {id: 2, name: '搜索页面', link: 'search'},
        {id: 3, name: '商家管理', link: 'store_mag'},
        {id: 4, name: '商城数据', link: 'mall_data'},
        {id: 5, name: '品牌管理', link: 'brand_index'},
        {id: 6, name: '分类管理', link: 'class.online'},
        {id: 7, name: '系列/风格/属性管理', link: 'style_index'},
        {id: 8, name: '新品牌/新分类审核', link: 'new_brand_class.new_brand'},
        {id: 9, name: '商家入驻审核', link: 'settle_verify.wait'},
        {id: 10, name: '线下体验店', link: 'offline_shop.shop'}
    ];
    //财务中心
    $scope.finance_obj = [
        {id: 0, name: '商城财务', link: 'mall_finance'},
        {id: 1, name: '业主财务', link: 'owner_finance'},
    ];
    //账户管理
    $scope.account_obj = [
        {id: 0, name: '用户列表', link: 'account_user_list.normal'},
        {id: 1, name: '用户审核', link: 'account_user_verify.wait'}
    ]

    //商城管理------一级
    $scope.mall_click = function () {
        $scope.mall_flag = true;
        $scope.finance_flag = false;
        $scope.account_flag = false;
        $scope.other_flag = '';
        $scope.mall_active = 0;
        sessionStorage.removeItem('finance_menu');
        sessionStorage.removeItem('mall_dd_menu');
        sessionStorage.removeItem('finance_dd_menu');
        sessionStorage.removeItem('account_menu');
        sessionStorage.removeItem('account_dd_menu');
        sessionStorage.removeItem('other_menu');
        sessionStorage.setItem('mall_menu', $scope.mall_flag);
        $state.go('merchant_index')
    }
    $rootScope.mall_click = $scope.mall_click;
    if (sessionStorage.getItem('mall_menu') != null) {
        $scope.mall_flag = true;
        $scope.finance_flag = false;
        $scope.account_flag = false;
        $scope.other_flag = '';
        $scope.mall_active = 0;
    }
    //商城管理------二级
    $scope.mall_repeat = function (item) {
        $scope.mall_active = item.id;
        sessionStorage.setItem('mall_dd_menu', $scope.mall_active);
        $state.go(item.link)
    }
    if (sessionStorage.getItem('mall_dd_menu') != null) {
        $scope.mall_flag = true;
        $scope.finance_flag = false;
        $scope.account_flag = false;
        $scope.other_flag = '';
        $scope.mall_active = sessionStorage.getItem('mall_dd_menu');
    }


    //财务中心------一级
    $scope.finance_click = function () {
        $scope.finance_flag = true;
        $scope.mall_flag = false;
        $scope.account_flag = false;
        $scope.other_flag = '';
        $scope.finance_active = 0;
        sessionStorage.removeItem('mall_menu');
        sessionStorage.removeItem('mall_dd_menu');
        sessionStorage.removeItem('finance_dd_menu');
        sessionStorage.removeItem('account_menu');
        sessionStorage.removeItem('account_dd_menu');
        sessionStorage.removeItem('other_menu');
        sessionStorage.setItem('finance_menu', $scope.finance_flag);
        $state.go('mall_finance');
    }
    $rootScope.finance_click = $scope.finance_click;
    if (sessionStorage.getItem('finance_menu') != null) {
        $scope.finance_flag = true;
        $scope.account_flag = false;
        $scope.mall_flag = false;
        $scope.other_flag = '';
        $scope.finance_active = 0;
    }
    //财务中心-二级
    $scope.finance_repeat = function (item) {
        $scope.finance_active = item.id;
        sessionStorage.setItem('finance_dd_menu', $scope.finance_active);
        $state.go(item.link)
        console.log(item.link);
    }
    if (sessionStorage.getItem('finance_dd_menu') != null) {
        $scope.finance_flag = true;
        $scope.account_flag = false;
        $scope.mall_flag = false;
        $scope.other_flag = '';
        $scope.finance_active = sessionStorage.getItem('finance_dd_menu');
        ;
    }

    //账户管理------一级
    $scope.account_click = function () {
        $scope.account_flag = true;
        $scope.finance_flag = false;
        $scope.mall_flag = false;
        $scope.other_flag = '';
        $scope.account_active = 0;

        sessionStorage.removeItem('mall_menu');
        sessionStorage.removeItem('finance_menu');
        sessionStorage.removeItem('mall_dd_menu');
        sessionStorage.removeItem('finance_dd_menu');
        sessionStorage.removeItem('other_menu');
        sessionStorage.setItem('account_menu', $scope.account_flag);
        $state.go('account_user_list.normal')
    }

    $rootScope.account_click = $scope.account_click;
    if (sessionStorage.getItem('account_menu') != null) {
        $scope.account_menu = true;
        $scope.mall_flag = false;
        $scope.finance_flag = false;
        $scope.other_flag = '';
        $scope.account_active = 0;
    }

    //账户管理------二级
    $scope.account_repeat = function (item) {
        $scope.account_active = item.id;
        sessionStorage.setItem('account_dd_menu', $scope.account_active);
        $state.go(item.link)
    }
    if (sessionStorage.getItem('account_dd_menu') != null) {
        $scope.account_flag = true;
        $scope.mall_flag = false;
        $scope.finance_flag = false;
        $scope.other_flag = '';
        $scope.account_active = sessionStorage.getItem('account_dd_menu');
    }


    //其他一级菜单
    $scope.other_display = function (value) {
        sessionStorage.removeItem('mall_menu');
        sessionStorage.removeItem('mall_dd_menu');
        sessionStorage.removeItem('finance_menu');
        sessionStorage.removeItem('finance_dd_menu');
        sessionStorage.removeItem('account_menu');
        sessionStorage.removeItem('account_dd_menu');
        console.log(value);
        $scope.other_flag = '';
        $scope.other_flag = value;
        sessionStorage.setItem('other_menu', $scope.other_flag);
        $scope.mall_flag = false;
        $scope.finance_flag = false;
        $scope.account_flag = false;
    }
    if (sessionStorage.getItem('other_menu') != null) {
        $scope.mall_flag = false;
        $scope.finance_flag = false;
        $scope.account_flag = false;
        $scope.other_flag = sessionStorage.getItem('other_menu');
    }
});