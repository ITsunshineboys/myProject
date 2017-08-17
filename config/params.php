<?php

$districts = require(__DIR__ . '/districts.php'); // district data
//$pinyin = require(__DIR__ . '/pinyin.php');
$auth = require(__DIR__ . '/auth.php');

return [
    'adminEmail' => 'admin@example.com',
    'offsetAiteCubeNo' => 10001,
    'offsetAiteCubeNoInternal' => 1,
    'offsetGeneral' => 10000,
    'ownerRoleId' => 7,
    'supplierRoleId' => 6,
    'lhzzRoleId' => 1,
    'maxRolesNumber' => 2,
    'errorCodes' => [
        403 => '没有权限',
        500 => '接口服务器异常',
        1000 => '请求的参数不正确',
        1001 => '用户名/密码不正确',
        1002 => '验证码不正确',
        1003 => '请先停用再删除',
        1004 => '上传文件大小不能超过5M',
        1005 => '编辑商品分类不能跨级',
        1006 => '商品分类名称不能重复',
        1007 => '商品品牌名称不能重复',
        1008 => '物流模板名称不能重复',
        1009 => '商品属性名称不能重复',
        1010 => '该用户尚未注册',
        1011 => '该用户已添加过角色',
        1012 => '商品所在分类已移除，请商家重新添加新商品。',
        1013 => '商品所在品牌已移除，请商家重新选择新品牌。',
        1014 => '商品所在店铺已关闭，请开店后上传商品。',
        1015 => '该用户已被禁封',
        1016 => '您已输错密码次数达到5次',
        1017 => '昵称只能修改一次',
        1018 => '昵称已存在',
        1019 => '该手机号已被注册',
        1020 => '验证码超时',
        1021 => '请上传jpg/jpeg/png类型的文件',
        1022 => '商品所在分类风格或系列已移除，请商家重新添加新商品。',
        1023 => '您的账户在另一地点登录',
		1024 => '今日修改次数已超过5次',
        1050 => '请求方式错误',
        1051 => '失败',
        1052 => '未登录',
        1053 => '两次输入密码不一致',
        1054 => '提现金额超出可提现限额，请重新填写',
        1055 => '支付密码错误',
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
    ],
    'download' => [
        'directory' => 'down',
        'enableBuffering' => 'yes',
        'rate' => 102400, // 速度限制 Byte/s
    ],
    'uploadPublic' => [
        'maxSize' => 5 * 1024 * 1024 , // 5M
        'extensions' => ['png', 'jpg', 'jpeg'],
    ],
    'sm' => [
        'appKey' => '23532528',
        'appSecret' => 'f589df8c27f634a94df0b908da7339f7',
        'interval' => 60, // 1m
        'validationCode' => [
            'expire' => 600, // 10m
            'rule' => 'fourDigits',
        ],
        'maxSendNumPerDay' => 5,
        'signName' => '领航装饰设计',
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
        '0' => '待审核',
        '1' => '审核不通过',
        '2' => '审核通过',
        '3' => '未认证',
    ],
    'districts' => $districts,
//    'pinyin'=>$pinyin,
    'auth' => $auth,
    'goods' => [
        'maxImagesCnt' => 5,
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
        'default_nickname' => 'default nickname', // 待定
        'deault_icon_path' => 'uploads/user_icon_default.png', // 默认头像
    ],
    //聊天服务配置
    'chatOptions' => [
            'client_id' => 'YXA6DxGD4HQZEee78SNJqGuchQ',
            'client_secret' => 'YXA6isIrLRTIwuEyLOIGXGHGTPMX3WE',
            'org_name' => '1170170729178073',
            'app_name' => '111'
    ],
    'value_all' => -1, // 下拉框“全部”的值
    'style_series' => [
        'offline_reason' => '分类风格或系列移除',
    ],
    'security' => [
        'salt' => '#599!@271%4ca7*-8$63',
    ],
];