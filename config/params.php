<?php

$districts = require(__DIR__ . '/districts.php'); // district data
//$pinyin = require(__DIR__ . '/pinyin.php');
$auth = require(__DIR__ . '/auth.php');
//$paramsOnline = require(__DIR__ . '/params_online.php');

return [
    'adminEmail' => 'admin@example.com',
    'offsetAiteCubeNo' => 10001,
    'offsetAiteCubeNoInternal' => 1,
    'offsetGeneral' => 10000,
    'ownerRoleId' => 7,
    'supplierRoleId' => 6,
    'decorationCompanyRoleId' => 5,
    'managerRoleId' => 4,
    'designerRoleId' => 3,
    'workerRoleId' => 2,
    'lhzzRoleId' => 1,
    'maxRolesNumber' => 2,
    'errorCodes' => [
        403 => '没有权限',
        500 => '接口服务器异常',
        1000 => '请求的参数不正确',
        1001 => '账号/密码不正确',
        1002 => '验证码错误，请重新输入',
        1003 => '请先停用再删除',
        1004 => '上传文件大小不能超过5M',
        1005 => '编辑商品分类不能跨级',
        1006 => '商品分类名称不能重复',
        1007 => '商品品牌名称不能重复',
        1008 => '物流模板名称不能重复',
        1009 => '商品属性名称不能重复',
        1010 => '该手机号还未注册，请联系客服400-3948-398',
        1011 => '该手机号已注册商家，请重新输入',
        1012 => '商品所在分类已移除，请商家重新添加新商品。',
        1013 => '商品所在品牌已移除，请商家重新选择新品牌。',
        1014 => '商品所在店铺已关闭，请开店后上传商品。',
        1015 => '该账号已被封号，请联系客服400-3948-398',
        1016 => '您已输错密码次数达到5次',
        1017 => '昵称只能修改一次',
        1018 => '昵称已存在',
        1019 => '该手机号已被注册',
        1020 => '验证码超时，请重新输入',
        1021 => '请上传jpg/jpeg/png/amr/mp3/m4a类型的文件',
        1022 => '商品所在分类风格或系列已移除，请商家重新添加新商品。',
        1023 => '您的账户在另一地点登录',
        1024 => '今日修改次数已达5次',
        1025 => '该手机号与当前绑定手机号相同',
        1026 => '您最多只能添加6个收货地址',
        1027 => '每日修改手机号码次数为3次，请明日再修改',
        1028 => '该店铺名称已存在，请重新填写',
        1029 => '该代码已存在，请重新填写',
        1030 => '该公司名称已存在，请重新填写',
        1031 => '您已经提交过申请了，请耐心等待',
        1032 => '此订单已退款或正在退款，请勿重复处理',
        1033 => '余额不足',
        1034 => '你没有该操作的权限',
        1035 => '该订单暂不支持此操作',
        1036 => '该订单异常，请勿操作',
        1037 => '此账号已被封号，请先解禁账号',
        1038 => '此身份证号已存在，请重新输入',
        1039 => '商品编码错误，请重新添加',
        1040 => '该手机号还未注册商家，请联系客服400-3948-398',
        1041 => '当前银行卡与实名认证用户不匹配，请更换银行卡',
        1042 => '请填写采购价后上架商品',
        1050 => '请求方式错误',
        1051 => '失败',
        1052 => '未登录',
        1053 => '两次输入密码不一致',
        1054 => '提现金额超出可提现限额，请重新填写',
        1055 => '支付密码错误',
        1056 => '工人信息有误',
        1057 => '工人详情有误',
        1058 => '点位信息有误',
        1059 => '工艺信息有误',
        1060 => '点位详情有误',
        1061 => '商品详情有误',
        1062 => '杂工信息有误',
        1063 => '材料添加项有误',
        1064 => '系数信息有误',
        1065 => '材料信息有误',
        1066 => '案列信息有误',
        1067 => '商品属性有误',
        1068 => '效果图为空',
        1069 => '该工种不能删除',
        1070 => '请输入正确的11位手机号',
        1071 => '该手机号以注册工人。请重新输入',
        1072 => '请输入正确的18身份证号',
        1073 => '该身份证号已注册，请重新输入',
    ],
    'events' => [
        'supplier' => [
            'login' => 'supplier_login',
            'logout' => 'supplier_logout',
        ],
        'mall' => [
            'category' => [
                'updateBatch' => 'mall_category_update_batch',
            ],
        ],
        '3rd' => [
            'failed' => [
                'createHuanxinUser' => 'failed_create_huanxin_user',
                'resetHuanxinUserPassword' => 'failed_reset_huanxin_user_password',
            ],
        ],
        'db' => [
            'failed' => 'failed_db',
        ],
        'user' => [
            'register' => 'user_register',
            'login' => 'user_login',
        ],
        'system' => [
            'error' => 'system_error',
        ],
        'async' => 'async',
    ],
    'download' => [
        'directory' => 'uploads',
        'enableBuffering' => 'yes',
        'rate' => 102400, // 速度限制 Byte/s
    ],
    'uploadPublic' => [
        'maxSize' => 5 * 1024 * 1024 , // 5M
        'extensions' => ['png', 'jpg', 'jpeg', 'amr', 'mp3', 'm4a'],
        'compress' => [
            'minSize' => 100 * 1024, // 100K
            'extensions' => ['jpg', 'jpeg'],
            'quality' => 0.7,
        ],
    ],
    'sm' => [
        'appKey' => '23532528',
        'appSecret' => 'f589df8c27f634a94df0b908da7339f7',
        'interval' => 60, // 1m
        'validationCode' => [
            'expire' => 60, // 10m
            'rule' => 'fourDigits',
        ],
        'maxSendNumPerDay' => 5,
        'signName' => '领航装饰设计', // '艾特生活',
        'register' => [
            'templateId' => 'SMS_26070129',
            'signName' => '用户注册验证码',
        ],
        'resetPassword' => [
            'templateId' => 'SMS_26070127',
            'signName' => '修改密码验证码',
        ],
        'forgetPassword' => [
            'templateId' => 'SMS_63905689',
            'signName' => '忘记密码验证码',
        ],
        'resetPayPassword' => [
            'templateId' => 'SMS_86645022',
            'signName' => '修改交易密码验证码',
        ],
        'resetMobile' => [
            'templateId' => 'SMS_92765021',
            'signName' => '修改手机号验证码',
        ],
        'loginDistribution' => [
            'templateId' => 'SMS_111200008',
            'signName' => '登录分销系统',
        ],
        'gotOrder' => [
            'templateId' => 'SMS_109425248',
            'signName' => '收到订单',
        ],
        'homeDelivery' => [
            'templateId' => 'SMS_109420262',
            'signName' => '送货上门',
        ],
        'courierDelivery' => [
            'templateId' => 'SMS_109345282',
            'signName' => '快递发货',
        ],
    ],
    'timeTypes' => [
        'all' => '全部时间',
        'today' => '今天',
        'week' => '本周',
        'month' => '本月',
        'year' => '本年',
        'custom' => '自定义',
    ],
    'category' => [
        'admin' => [
            'currentName' => '当前',
            'allName' => '全部分类',
            'all' => '全部',
        ],
        'offline_reason' => '分类下架',
    ],
    'reviewStatuses' => [
        '0' => '等待审核',
        '1' => '审核不通过',
        '2' => '审核通过',
        '3' => '未认证',
    ],
    'districts' => $districts,
//    'pinyin'=>$pinyin,
    'auth' => $auth,
    'goods' => [
        'maxImagesCnt' => 5,
        'viewCacheTime' => 60, // unit: second
    ],
    'brand' => [
        'offline_reason' => '品牌下架',
    ],
    'supplier' => [
        'offline_reason' => '闭店下架',
    ],
    'lhzz' => [
        'offline_reason' => '运营下架',
    ],
    'district_default' => 510100, // 成都
    'grade' => '白银',  //工人默认级别
    'carouselMaxNumber' => 3,
    'user' => [
        'daily_forgot_pwd_cnt_max' => 5,
        'daily_reset_pwd_cnt_max' => 5,
        'default_nickname' => 'default nickname', // 待定
        'deault_icon_path' => 'uploads/user_icon_default.png', // 默认头像
    ],
    //聊天服务配置
    'chatOptions' => [
        'client_id' => 'YXA6i1icgLV5Eee3tef56bpxaQ',
        'client_secret' => 'YXA69F9p-SBqT-pW7F_3Zi_-dcxSC78',
        'org_name' => '1185170730178177',
        'app_name' => 'aitecube',
        'user_password_default' => 'a123',
    ],
    'value_all' => -1, // 下拉框“全部”的值
    'style_series' => [
        'offline_reason' => '分类风格或系列移除',
    ],
    'security' => [
        'salt' => '#599!@271%4ca7*-8$63',
    ],
    'desc' => [
        'support' => [
            '不支持',
            '支持',
        ],
    ],
//    'online' => $paramsOnline,
];