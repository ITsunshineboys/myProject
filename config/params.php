<?php

$districts = require(__DIR__ . '/districts.php');
$pinyin = require(__DIR__ . '/pinyin.php');
$auth = require(__DIR__ . '/auth.php');

return [
    'adminEmail' => 'admin@example.com',
    'offsetAiteCubeNo' => 10000,
    'ownerRoleId' => 7,
    'supplierRoleId' => 6,
    'lhzzRoleId' => 1,
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
    'upload' => [
        'directory' => 'uploads',
    ],
    'uploadPublic' => [
        'maxSize' => 5 * 1024 * 1024 , // 5M
        'extensions' => ['png', 'jpg', 'gif'],
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
    ],
    'districts' => $districts,
    'pinyin'=>$pinyin,
    'auth' => $auth,
    'goods' => [
        'maxImagesCnt' => 5,
    ],
];