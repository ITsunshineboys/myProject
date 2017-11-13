create table role (
    id int PRIMARY key auto_increment,
    name varchar(25) not null default '',
    admin_module varchar(25) not null default '',
    detail_table varchar(25) not null default '',
    detail_model varchar(25) not null default ''
) default charset = utf8;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(26) DEFAULT '' comment '环信用户名',
  `hx_pwd_date` int unsigned not null DEFAULT 0 comment '设置环信密码日期',
  `password` varchar(100) DEFAULT '',
  `authKey` varchar(30) DEFAULT '',
  `authKeyAdmin` varchar(30) DEFAULT '',
  `oldAuthKey` varchar(30) DEFAULT '',
  `oldAuthKeyAdmin` varchar(30) DEFAULT '',
  `accessToken` varchar(255) DEFAULT '',
  `mobile` bigint DEFAULT 0,
  `nickname` varchar(20) DEFAULT '',
  `identity_no` varchar(18) DEFAULT '',
  `aite_cube_no` bigint DEFAULT 0,
  `icon` varchar(100) DEFAULT '',
  `create_time` int DEFAULT 0,
  `login_time` int DEFAULT 0,
  `login_role_id` int DEFAULT 0,
  `last_role_id_app` int DEFAULT 7,
  `legal_person` varchar(50) not null DEFAULT '' comment '法人',
  `identity_card_front_image` varchar(255) not null DEFAULT '' comment '身份证正面图片',
  `identity_card_back_image` varchar(255) not null DEFAULT '' comment '身份证反面图片',
  `deadtime` int unsigned not null default 0 comment '封号时间',
  `signature` varchar(20) not null DEFAULT '' comment '个性签名',
  `gender` tinyint(1) unsigned not null DEFAULT 0 comment '0: 男, 1: 女, 2: 保密',
  `birthday` int(8) unsigned not null DEFAULT 0,
  `district_code` int(6) unsigned not null default 0 comment '区域码',
  `district_name` varchar(50) NOT NULL DEFAULT '' comment '区域名',
  `availableamount` bigint(20) NOT NULL DEFAULT '0' COMMENT '可用余额',
  `balance` bigint(20) NOT NULL DEFAULT '0' COMMENT '余额, unit: fen',
  `pay_password` varchar(100) NOT NULL DEFAULT '' COMMENT '支付密码',
  `registration_id` varchar(100) NOT NULL DEFAULT '0' COMMENT '极光注册id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table user_role (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `role_id` int(11) NOT NULL DEFAULT 0,
  `review_apply_time` int unsigned not null DEFAULT 0 comment '审核申请时间',
  `review_time` int unsigned not null DEFAULT 0 comment '审核时间',
  `review_status` tinyint(1) not null DEFAULT 3 comment '0: 待审核, 1: 审核不通过, 2: 审核通过, 3: 未认证',
  `review_remark` varchar(50) not null DEFAULT '' comment'审核备注',
  `reviewer_uid` int(11) NOT NULL DEFAULT 0 comment '审核人用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `decoration_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '昵称',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `name` varchar(50) not null DEFAULT '' COMMENT '装修公司名称',
  `licence` varchar(50) not null DEFAULT '' COMMENT '营业执照号',
  `licence_image` varchar(255) not null DEFAULT '营业执照图片',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  `pay_password` varchar(100) NOT NULL,
  `availableamount` bigint(20) NOT NULL COMMENT '可用余额',
  `balance` bigint(20) NOT NULL DEFAULT '0' COMMENT '余额, unit: fen',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `designer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '设计师名称',
  `decoration_company_id` int(11) unsigned not null DEFAULT 0 COMMENT '装修公司“id”',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  `pay_password` varchar(100) NOT NULL COMMENT '支付密码',
  `availableamount` bigint(20) NOT NULL COMMENT '可用余额',
  `balance` bigint(20) NOT NULL DEFAULT '0' COMMENT '余额, unit: fen',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `project_manager` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '项目经理名称',
  `decoration_company_id` int(11) unsigned not null DEFAULT 0 COMMENT '装修公司“id”',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  `pay_password` varchar(100) NOT NULL COMMENT '支付密码',
  `availableamount` bigint(20) NOT NULL COMMENT '可用余额',
  `balance` bigint(20) NOT NULL DEFAULT '0' COMMENT '余额, unit: fen',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `project_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目ID',
  `worker_id` int(11) unsigned not null DEFAULT 0 COMMENT '工人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `supplier` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '供应商',
  `shop_name` varchar(25) not null DEFAULT '' COMMENT '店铺名称',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像',
  `name` varchar(50) not null DEFAULT '' COMMENT '公司名称',
  `licence` varchar(50) not null DEFAULT '' COMMENT '营业执照号',
  `licence_image` varchar(255) not null DEFAULT '营业执照图片',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  `status` tinyint(1) unsigned not null default 2 comment '0: 已关闭，1：正常营业，2：等待审核，3：审核未通过，4：审核通过',
  `follower_number` int(11) unsigned not null default 0 comment '关注人数',
  `comprehensive_score` float unsigned not null default 10 comment '综合评分',
  `store_service_score` float unsigned not null default 10 comment '店家服务评分',
  `logistics_speed_score` float unsigned not null default 10 comment '物流速度评分',
  `delivery_service_score` float unsigned not null default 10 comment '配送员服务评分',
  `type_org` tinyint(1) unsigned not null default 0 comment '0:个体工商户, 1:企业',
  `type_shop` tinyint(1) unsigned not null default 0 comment '0:旗舰店, 1:自营店, 2:专营店, 3:专卖店',
  `category_id` int(11) unsigned not null default 0,
  `shop_no` int(11) unsigned not null default 0 comment '店铺号',
  `create_time` int unsigned not null DEFAULT 0,
  `quality_guarantee_deposit` bigint not null DEFAULT 0 comment '质保金, unit: fen',
  `support_offline_shop` tinyint(1) not null DEFAULT 0 comment '0: 不支持, 1: 支持',
  `balance` bigint(20) NOT NULL DEFAULT 0 COMMENT '账户总余额',
  `availableamount` bigint(20) NOT NULL DEFAULT 0 COMMENT '可用金额',
  `pay_password` varchar(100) NOT NULL DEFAULT '' COMMENT '支付密码',
  `district_code` int(6) unsigned NOT NULL DEFAULT 0 COMMENT '区域码',
  `district_name` varchar(50) NOT NULL DEFAULT '' COMMENT '区域名称',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
  `sales_volumn_month` int unsigned not null default 0 comment '本月销量',
  `sales_amount_month` bigint unsigned not null default 0 comment '本月销售额, 单位: 分',
  `month` int(6) unsigned not null default 0 comment '当前月份，比如：201708',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned not null DEFAULT 0 COMMENT '业主“id”',
  `decoration_company_id` int(11) unsigned not null DEFAULT 0 COMMENT '装修公司“id”',
  `project_manager_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目经理“id”',
  `designer_id` int(11) unsigned not null DEFAULT 0 COMMENT '设计师“id”',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `work_type` (
  `id` int(11) NOT NULL,
  `worker_name` varchar(10) DEFAULT NULL COMMENT '工种类型',
  `rank_name` varchar(10) DEFAULT NULL,
  `min_value` int(11) DEFAULT NULL COMMENT '最小值',
  `max_value` int(11) DEFAULT NULL COMMENT '最大值',
  `establish_time` int(11) DEFAULT NULL COMMENT '创建日期',
  `state` tinyint(4) DEFAULT '0' COMMENT '0：关闭，1：开启',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `lhzz` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 5.5 start--
CREATE TABLE `goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int unsigned not null default 0,
  `brand_id` int unsigned not null default 0,
  `category_id` int unsigned not null default 0,
  `area_id` int(11) NOT NULL,
  `series_id` int(11) unsigned not null default 0,
  `style_id` int(11) unsigned not null default 0,
  `sku` bigint unsigned not null default 0 COMMENT '商品编号',
  `title` varchar(100) not null DEFAULT '',
  `subtitle` varchar(100) not null DEFAULT '',
  `cover_image` varchar(255) not null DEFAULT '' comment '封面图',
  `supplier_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `platform_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `market_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `purchase_price_decoration_company` bigint not null DEFAULT 0 comment 'unit: fen',
  `purchase_price_manager` bigint not null DEFAULT 0 comment 'unit: fen',
  `purchase_price_designer` bigint not null DEFAULT 0 comment 'unit: fen',
  `sold_number` int unsigned not null default 0 comment '销量',
  `left_number` int unsigned not null default 0 comment '库存',
  `comment_number` int unsigned not null default 0 comment '评价数',
  `viewed_number` int unsigned not null default 0 comment '浏览量',
  `favourable_comment_rate` tinyint unsigned not null default 0 comment '好评率',
  `description` varchar(255) not null DEFAULT '',
  `create_time` int not null DEFAULT 0,
  `offline_time` int not null DEFAULT 0,
  `online_time` int not null DEFAULT 0,
  `delete_time` int not null DEFAULT 0,
  `publish_time` int not null DEFAULT 0,
  `status` tinyint(1) not null DEFAULT 0 comment '0：已下架, 1：等待上架, 2：已上架, 3：已删除',
  `after_sale_services` set('0', '1', '2', '3', '4', '5', '6') not null DEFAULT '0' comment '0：提供发票, 1：上门安装, 2：上门维修, 3：上门退货, 4:上门换货, 5：退货, 6:换货',
  `logistics_template_id` int(11) unsigned not null default 0,
  `offline_reason` varchar(100) not null DEFAULT '' comment '下架原因',
  `offline_person` varchar(20) not null DEFAULT '' comment '下架人',
  `offline_uid` int(11) unsigned not null DEFAULT 0,
  `reason` varchar(100) not null DEFAULT '' comment '审核原因',
  `online_person` varchar(20) not null DEFAULT '' comment '上架人',
  `online_uid` int(11) unsigned not null DEFAULT 0,
  `profit_rate` int(11) unsigned not null DEFAULT 0 comment '利润率',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_recommend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` bigint unsigned not null default 0,
  `platform_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `url` varchar(255) not null DEFAULT '',
  `supplier_id` int unsigned not null DEFAULT 0,
  `supplier_name` varchar(50) not null DEFAULT '',
  `title` varchar(100) not null DEFAULT '',
  `image` varchar(255) not null DEFAULT '',
  `description` varchar(255) not null DEFAULT '',
  `type` tinyint(1) not null default 0 comment '0: banner 2: second',
  `from_type` tinyint(1) not null default 0 comment '1: mall 2: link',
  `create_time` int not null DEFAULT 0,
  `delete_time` int not null DEFAULT 0,
  `status` tinyint(1) not null DEFAULT 0 comment '0: 已停用 1: 已启用',
  `sorting_number` int unsigned not null default 0,
  `district_code` int(6) unsigned not null default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) unsigned not null default 0,
  `supplier_name` varchar(25) not null DEFAULT '' COMMENT '供应商',
  `user_id` int(11) unsigned not null DEFAULT 0,
  `user_name` varchar(20) not null DEFAULT '',
  `title` varchar(50) not null DEFAULT '',
  `pid` int(11) unsigned not null default 0,
  `parent_title` varchar(50) not null DEFAULT '',
  `level` tinyint(1) unsigned not null default 0,
  `path` varchar(50) not null DEFAULT '',
  `icon` varchar(255) not null DEFAULT '',
  `description` text not null,
  `approve_time` int unsigned not null DEFAULT 0,
  `reject_time` int unsigned not null DEFAULT 0,
  `reason` varchar(100) not null DEFAULT '' comment '原因',
  `offline_reason` varchar(100) not null DEFAULT '' comment '下架原因',
  `offline_person` varchar(20) not null DEFAULT '' comment '下架人',
  `online_person` varchar(20) not null DEFAULT '' comment '上架人',
  `create_time` int unsigned not null DEFAULT 0,
  `review_status` tinyint(1) not null DEFAULT 0 comment '0: 待审核 1: 审核不通过 2:审核通过',
  `deleted` tinyint(1) not null default 0,
  `online_time` int unsigned not null DEFAULT 0,
  `offline_time` int unsigned not null DEFAULT 0,
  `attr_op_uid` int(11) unsigned not null DEFAULT 0,
  `attr_op_username` varchar(20) not null DEFAULT '',
  `attr_op_time` int not null DEFAULT 0,
  `attr_number` int(11) not null DEFAULT 0,
  `has_style` tinyint(1) not null DEFAULT 0 comment '0: 无风格，1: 有风格',
  `has_series` tinyint(1) not null DEFAULT 0 comment '0: 无系列，1: 有系列',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) not null DEFAULT '',
  `logo` varchar(255) not null DEFAULT '',
  `certificate` varchar(255) not null DEFAULT '',
  `supplier_id` int(11) unsigned not null default 0,
  `supplier_name` varchar(25) not null DEFAULT '',
  `user_id` int(11) unsigned not null DEFAULT 0,
  `user_name` varchar(20) not null DEFAULT '',
  `approve_time` int unsigned not null DEFAULT 0,
  `reject_time` int unsigned not null DEFAULT 0,
  `reason` varchar(100) not null DEFAULT '' comment '审核原因',
  `offline_reason` varchar(100) not null DEFAULT '' comment '下架原因',
  `offline_person` varchar(20) not null DEFAULT '' comment '下架人',
  `online_person` varchar(20) not null DEFAULT '' comment '上架人',
  `create_time` int unsigned not null DEFAULT 0,
  `review_status` tinyint(1) not null DEFAULT 0 comment '0: 待审核 1: 审核不通过 2:审核通过',
  `status` tinyint(1) not null default 0,
  `online_time` int unsigned not null DEFAULT 0,
  `offline_time` int unsigned not null DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_recommend_view_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recommend_id` int(11) unsigned not null default 0,
  `ip` int(11) unsigned not null default 0,
  `log_ip_number` tinyint(1) unsigned not null default 0,
  `create_time` int not null DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_recommend_sale_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recommend_id` int unsigned not null default 0,
  `number` int unsigned not null default 1 comment '销售数量',
  `create_time` int not null DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_area` (
  `id` int(11) NOT NULL,
  `express_area` varchar(255) DEFAULT NULL COMMENT '快递区域',
  `delivery_door_area` varchar(255) DEFAULT NULL COMMENT '送货上门区域',
  `piece` int(10) DEFAULT NULL COMMENT '件数',
  `price` bigint(20) NOT NULL COMMENT '价格',
  `mounting_cost` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table brand_category (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `category_id_level1` int(11) NOT NULL DEFAULT 0,
  `category_id_level2` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table logistics_template (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) not null default 0,
  `name` varchar(50) not null DEFAULT '',
  `delivery_method` tinyint(1) NOT NULL DEFAULT 0 comment '0：快递物流，1：送货上门',
  `delivery_cost_default` bigint NOT NULL DEFAULT 0 comment '默认运费, 单位：分',
  `delivery_number_default` int(11) NOT NULL DEFAULT 0 comment '默认运费对应商品数量',
  `delivery_cost_delta` bigint NOT NULL DEFAULT 0 comment '增加件运费, 单位：分',
  `delivery_number_delta` int(11) NOT NULL DEFAULT 0 comment '增加件运费对应商品数量',
  `status` tinyint(1) not null default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table logistics_district (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) not null default 0,
  `district_code` int(6) unsigned not null default 0,
  `district_name` varchar(100) not null DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table goods_attr (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) not null default '',
  `value` varchar(50) not null default '',
  `unit` tinyint(1) not null DEFAULT 0 comment '0: 无, 1: L, 2: M, 3: M^2, 4: Kg, 5: MM',
  `addition_type` tinyint(1) not null default 0 comment '0: 普通添加, 1: 下拉框添加',
  `goods_id` int(11) not null default 0,
  `category_id` int(11) not null default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table goods_image (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned not null default 0,
  `image` varchar(255) not null DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table goods_comment (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `role_id` int(11) unsigned not null default 0,
  `name` varchar(25) not null default '',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `content` varchar(255) not null default '',
  `score` tinyint(2) not null default 0,
  `create_time` int not null DEFAULT 0,
  `goods_id` int(11) unsigned not null default 0,
  `store_service_score` float NOT NULL DEFAULT '0',
  `shipping_score` float NOT NULL DEFAULT '0',
  `logistics_speed_score` float NOT NULL DEFAULT '0',
  `is_anonymous` tinyint(1) NOT NULL COMMENT '1:匿名  2： 实名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table comment_image (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) unsigned not null default 0,
  `image` varchar(255) not null DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table comment_reply (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) unsigned not null default 0,
  `content` varchar(255) not null default '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table brand_application (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) unsigned not null default 0,
  `category_id` int(11) unsigned not null default 0,
  `brand_id` int(11) unsigned not null default 0,
  `authorization_start` int unsigned not null default 0,
  `authorization_end` int unsigned not null default 0,
  `category_title` varchar(255) not null DEFAULT '',
  `brand_name` varchar(50) not null DEFAULT '',
  `brand_logo` varchar(255) not null DEFAULT '',
  `supplier_name` varchar(25) not null DEFAULT '' COMMENT '供应商',
  `mobile` bigint unsigned not null DEFAULT 0,
  `review_status` tinyint(1) not null DEFAULT 0 comment '0: 待审核 1: 审核不通过 2:审核通过',
  `review_note` varchar(100) not null DEFAULT '' comment '审核备注',
  `review_time` int unsigned not null DEFAULT 0,
  `create_time` int not null DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table brand_application_image (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_application_id` int(11) unsigned not null default 0,
  `image` varchar(255) not null DEFAULT '',
  `authorization_name` varchar(50) not null DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_stat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int unsigned not null default 0,
  `sold_number` int unsigned not null default 0 comment '销售数量',
  `amount_sold` bigint(20) unsigned not null default 0 comment '销售额',
  `create_date` int(8) not null DEFAULT 0,
  `ip_number` int(11) unsigned not null default 0 comment '游客数',
  `viewed_number` int(11) unsigned not null default 0 comment '访问量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `amount_order` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '订单金额',
  `supplier_id` int(10) unsigned NOT NULL DEFAULT '0',
  `address_id` int(11) NOT NULL COMMENT '收货地址id',
  `invoice_id` int(11) NOT NULL COMMENT '发票信息id',
  `pay_status` tinyint(1) NOT NULL COMMENT 'pay_status：0：未付款 1：已付款',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `pay_name` varchar(120) NOT NULL COMMENT '支付方式',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `paytime` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `order_refer` tinyint(1) NOT NULL COMMENT 'order_refer:1：线下店2：非线下店；',
  `return_insurance` bigint(20) NOT NULL COMMENT '退货保险费',
  `remarks` varchar(255) DEFAULT NULL COMMENT '备注',
  `role_id` tinyint(1) NOT NULL DEFAULT '7',
  `buyer_message` varchar(255) NOT NULL COMMENT '买家留言',
  `consignee` varchar(45) NOT NULL COMMENT '收货人姓名',
  `district_code` varchar(10) NOT NULL COMMENT '收货人地区编号',
  `region` varchar(90) NOT NULL COMMENT '收货人详细地址',
  `consignee_mobile` bigint(20) NOT NULL COMMENT '收货人手机号',
  `invoice_type` tinyint(1) NOT NULL COMMENT '1:普通发票  2： 电子发票',
  `invoice_header_type` tinyint(1) NOT NULL COMMENT '1:个人发票  2.公司发票',
  `invoice_header` varchar(50) NOT NULL COMMENT '发票抬头',
  `invoicer_card` varchar(18) DEFAULT NULL COMMENT '发票纳税人识别号',
  `invoice_content` varchar(30) DEFAULT NULL COMMENT '发票内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='\r\n';
-- 5.5 end--

-- 5.6 start--
CREATE TABLE `user_residence` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `province` varchar(20) DEFAULT NULL COMMENT '省份',
  `city` varchar(20) DEFAULT NULL COMMENT '市',
  `district` varchar(20) DEFAULT NULL COMMENT '区',
  `street` varchar(100) DEFAULT NULL COMMENT '街道',
  `toponymy` varchar(50) DEFAULT NULL COMMENT '小区名称',
  `area` smallint(20) DEFAULT NULL COMMENT '面积',
  `high` smallint(20) DEFAULT NULL COMMENT '层高',
  `room` smallint(5) DEFAULT NULL COMMENT '室',
  `hall` smallint(5) DEFAULT NULL COMMENT '厅',
  `toilet` smallint(5) DEFAULT NULL COMMENT '卫',
  `kitchen` smallint(5) DEFAULT NULL COMMENT '厨',
  `window` smallint(5) DEFAULT NULL COMMENT '飘窗',
  `balcony` smallint(5) DEFAULT NULL COMMENT '阳台',
  `user_id` int(11) DEFAULT NULL COMMENT '业主id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `style` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style` varchar(50) DEFAULT '' COMMENT '风格',
  `intro` varchar(255) DEFAULT '' COMMENT '风格介绍',
  `theme` varchar(50) DEFAULT '' COMMENT '风格主题',
  `modelling_length_coefficient` float(10,1) DEFAULT '0.0' COMMENT '造型长度系数',
  `modelling_day_coefficient` float(10,1) DEFAULT '0.0' COMMENT '造型天数系数',
  `flat_area_coefficient` float(10,1) DEFAULT '0.0' COMMENT '平顶面积系数',
  `flat_day_coefficient` float(10,1) DEFAULT '0.0' COMMENT '平顶天数系数',
  `category_id` int(11) DEFAULT NULL COMMENT '分类id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: 已停用 1: 已启用',
  `creation_time` int(11) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL COMMENT '图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `series` varchar(50) DEFAULT '' COMMENT '系列',
  `intro` varchar(255) DEFAULT '' COMMENT '系列介绍',
  `theme` varchar(50) DEFAULT '' COMMENT '系列主题',
  `modelling_length_coefficient` float(10,1) DEFAULT NULL COMMENT '造型长度系数',
  `modelling_day_coefficient` float(10,1) DEFAULT NULL COMMENT '造型天数系数',
  `flat_area_coefficient` float(10,1) DEFAULT NULL COMMENT '平顶面积系数',
  `flat_day_coefficient` float(10,1) DEFAULT NULL COMMENT '平顶天数系数',
  `category_id` int(11) DEFAULT '0' COMMENT '分类id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: 已停用 1: 已启用',
  `creation_time` int(11) DEFAULT NULL,
  `series_grade` int(5) DEFAULT NULL COMMENT '系列等级',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `effect_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL,
  `effect_images` varchar(255) DEFAULT NULL COMMENT '效果图 1-6张',
  `images_user` varchar(20) DEFAULT NULL COMMENT '图纸名称',
  `series_id` int(11) DEFAULT NULL COMMENT '系列id',
  `style_id` int(11) DEFAULT NULL COMMENT '风格id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `effect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bedroom` int(5) DEFAULT NULL COMMENT '卧室',
  `sittingRoom_diningRoom` int(5) DEFAULT NULL COMMENT '客餐厅和过道',
  `toilet` int(5) DEFAULT NULL COMMENT '卫生间',
  `kitchen` int(5) DEFAULT NULL COMMENT '厨房',
  `window` int(5) DEFAULT NULL COMMENT '飘窗',
  `area` int(5) DEFAULT NULL COMMENT '面积',
  `high` float(5,2) DEFAULT NULL COMMENT '层高',
  `province` varchar(10) DEFAULT NULL COMMENT '省份',
  `province_code` int(11) DEFAULT NULL COMMENT '省编码',
  `city` varchar(10) DEFAULT NULL COMMENT '市',
  `city_code` int(11) DEFAULT NULL COMMENT '市编码',
  `district` varchar(10) DEFAULT NULL COMMENT '区',
  `district_code` int(11) DEFAULT NULL COMMENT '区编码',
  `toponymy` varchar(10) DEFAULT NULL COMMENT '小区名称',
  `street` varchar(50) DEFAULT NULL COMMENT '街道',
  `particulars` varchar(50) DEFAULT NULL COMMENT '厅室详情',
  `stairway` tinyint(5) DEFAULT '0' COMMENT '楼梯信息 0：无 1：有',
  `stair_id` tinyint(5) DEFAULT '0' COMMENT '楼梯材料',
  `add_time` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `house_image` varchar(50) DEFAULT NULL COMMENT '户型图一张',
  `type` int(2) DEFAULT '0' COMMENT '0-普通，1-案列，2-样板间',
  `sort_id` int(10) unsigned DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `labor_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province` varchar(20) DEFAULT NULL,
  `province_code` varchar(20) DEFAULT NULL COMMENT '省份编码',
  `city` varchar(20) DEFAULT NULL,
  `city_code` varchar(20) DEFAULT NULL COMMENT '市编码',
  `univalence` bigint(10) NOT NULL COMMENT '工人单价',
  `worker_kind` varchar(20) DEFAULT NULL COMMENT '工人种类',
  `rank` varchar(20) DEFAULT NULL COMMENT '工人级别',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `decoration_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL COMMENT '效果图',
  `decoration_company_id` int(11) DEFAULT NULL COMMENT '装修公司',
  `project_manager_id` int(11) DEFAULT NULL COMMENT '项目经理',
  `majordomo` varchar(50) DEFAULT NULL COMMENT '总监',
  `hardcover_manager` varchar(50) DEFAULT NULL COMMENT '精装经理',
  `monitor` varchar(50) DEFAULT NULL COMMENT '监察',
  `user_id` int(11) DEFAULT NULL COMMENT '业主',
  `project_name` varchar(255) DEFAULT NULL COMMENT '工程名称',
  `begin_time` datetime DEFAULT NULL COMMENT '开工时间',
  `end_time` datetime DEFAULT NULL COMMENT '竣工时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL COMMENT '名称',
  `pid` int(10) DEFAULT '0' COMMENT '弱电点位',
  `count` int(11) DEFAULT NULL,
  `level` tinyint(1) DEFAULT NULL COMMENT '等级',
  `differentiate` tinyint(1) DEFAULT '0' COMMENT '0-固定，1-新添加',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `points_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place_id` int(11) DEFAULT NULL,
  `details` varchar(50) DEFAULT NULL COMMENT '简介详情',
  `points_quantity` int(10) unsigned DEFAULT NULL COMMENT '点位数量',
  `points_quantity_total` int(11) DEFAULT NULL COMMENT '点位总和',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `points_total` (
  `id` int(11) NOT NULL,
  `place_id` int(11) DEFAULT NULL,
  `place` varchar(20) DEFAULT NULL,
  `points_total` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- 5.6 end--

-- 5.26 start--
CREATE TABLE `decoration_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL COMMENT '效果图',
  `decoration_company_id` int(11) DEFAULT NULL COMMENT '装修公司',
  `project_manager_id` int(11) DEFAULT NULL COMMENT '项目经理',
  `majordomo` varchar(50) DEFAULT NULL COMMENT '总监',
  `hardcover_manager` varchar(50) DEFAULT NULL COMMENT '精装经理',
  `monitor` varchar(50) DEFAULT NULL COMMENT '监察',
  `user_id` int(11) DEFAULT NULL COMMENT '业主',
  `project_name` varchar(255) DEFAULT NULL COMMENT '工程名称',
  `begin_time` datetime DEFAULT NULL COMMENT '开工时间',
  `end_time` datetime DEFAULT NULL COMMENT '竣工时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `decoration_particulars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hall_area` int(10) DEFAULT NULL COMMENT '客餐厅及过道地面面积',
  `bedroom_area` int(10) DEFAULT NULL COMMENT '卧室地面面积',
  `toilet_area` int(10) DEFAULT NULL COMMENT '卫生间地面面积',
  `kitchen_area` int(10) DEFAULT NULL COMMENT '厨房地面面积',
  `hallway_area` int(10) DEFAULT NULL COMMENT '玄关地面面积',
  `hall_perimeter` int(10) DEFAULT NULL COMMENT '客餐厅及过道周长',
  `bedroom_perimeter` int(10) DEFAULT NULL COMMENT '卧室周长',
  `toilet_perimeter` int(10) DEFAULT NULL COMMENT '卫生间周长',
  `kitchen_perimeter` int(10) DEFAULT NULL COMMENT '厨房周长',
  `hallway_perimeter` int(10) DEFAULT NULL COMMENT '玄关地面周长',
  `shoe_cabinet_length` int(10) DEFAULT NULL COMMENT '鞋柜长度',
  `masterBedroom_garderobe_length` int(10) DEFAULT NULL COMMENT '主卧衣柜长度',
  `secondaryBedroom_garderobe_length` int(10) DEFAULT NULL COMMENT '次卧衣柜长度',
  `toilet_pipe` int(10) DEFAULT NULL COMMENT '卫生间包管',
  `kitchen_pipe` int(10) DEFAULT NULL COMMENT '厨房包管',
  `cabinet_length` int(10) DEFAULT NULL COMMENT '橱柜长度',
  `drawingRoom_curtain_length` int(10) DEFAULT NULL COMMENT '客厅窗帘长度',
  `masterBedroom_curtain_length` int(10) DEFAULT NULL COMMENT '主卧窗帘长度',
  `secondaryBedroom_curtain_length` int(10) DEFAULT NULL COMMENT '次卧窗帘长度',
  `wallCabinet_length` int(10) DEFAULT NULL COMMENT '吊柜长度',
  `modelling_length` int(10) DEFAULT NULL COMMENT '造型长度',
  `flat_area` int(10) DEFAULT NULL COMMENT '平顶面积',
  `balcony_area` int(10) DEFAULT NULL COMMENT '阳台面积',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `circuitry_reconstruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `project` char(15) DEFAULT NULL COMMENT '强电or弱电',
  `material` varchar(20) DEFAULT NULL COMMENT '材料',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `waterway_reconstruction` (
  `id` int(11) NOT NULL,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `goods_id` int(11) DEFAULT NULL COMMENT '品牌',
  `points_id` int(11) DEFAULT NULL COMMENT '点位',
  `material` varchar(50) DEFAULT NULL COMMENT '材料',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `waterproof_reconstruction` (
  `id` int(11) NOT NULL,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL COMMENT '商品',
  `material` varchar(50) DEFAULT NULL COMMENT '材料',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `carpentry_reconstruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品',
  `project` varchar(50) DEFAULT NULL COMMENT '项目',
  `material` varchar(50) DEFAULT NULL COMMENT '材料',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `paint_reconstruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `quantity` int(50) DEFAULT NULL COMMENT '数量',
  `material` varchar(50) DEFAULT NULL COMMENT '材料',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `plastering_reconstruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `project` varchar(50) DEFAULT NULL COMMENT '项目',
  `quantity` varchar(50) DEFAULT NULL COMMENT '数量',
  `material` varchar(50) DEFAULT NULL COMMENT '材料',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `dismantle_new` (
  `id` int(11) NOT NULL,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `project` varchar(50) DEFAULT NULL COMMENT '项目',
  `describe` varchar(100) DEFAULT NULL COMMENT '工艺描述',
  `quantity` int(10) DEFAULT NULL COMMENT '数量',
  `material` varchar(50) DEFAULT NULL COMMENT '材料',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `finished_product` (
  `id` int(11) NOT NULL,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `project` varchar(50) DEFAULT NULL COMMENT '项目',
  `material` varchar(50) DEFAULT NULL COMMENT '材料',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `transportation` (
  `id` int(11) NOT NULL,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `project` varchar(50) DEFAULT NULL COMMENT '项目',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `cleaning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `project` varchar(50) DEFAULT NULL COMMENT '项目',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- 5.26 end--

-- 6.26 start --
CREATE TABLE `decoration_add` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province_code` int(10) DEFAULT NULL,
  `city_code` int(10) DEFAULT NULL,
  `one_materials` varchar(11) DEFAULT '0' COMMENT '一级材料',
  `two_materials` varchar(10) DEFAULT NULL,
  `three_materials` varchar(10) DEFAULT NULL,
  `correlation_message` varchar(11) DEFAULT '0' COMMENT '相关信息',
  `sku` int(10) DEFAULT NULL COMMENT '项目名称',
  `add_time` int(10) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `carpentry_add` (
  `id` int(11) NOT NULL,
  `series_id` int(11) DEFAULT NULL,
  `style_id` int(11) DEFAULT NULL,
  `project` varchar(50) DEFAULT NULL COMMENT '项目名称',
  `province` varchar(20) DEFAULT NULL COMMENT '省',
  `city` varchar(20) DEFAULT NULL COMMENT '市',
  `standard` int(10) DEFAULT NULL COMMENT '标准长度或面积',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `engineering_standard_craft` (
  `id` int(11) NOT NULL,
  `district_code` int(10) DEFAULT NULL COMMENT '城市编码',
  `district_name` varchar(20) DEFAULT NULL COMMENT '城市',
  `project` varchar(20) DEFAULT NULL COMMENT '项目名称',
  `material` float(10,2) DEFAULT NULL COMMENT '用料',
  `project_details` varchar(20) DEFAULT NULL COMMENT '项目详情',
  `units` varchar(10) DEFAULT NULL COMMENT '单价',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stairs_details` (
  `id` int(11) NOT NULL,
  `attribute` varchar(20) DEFAULT NULL COMMENT '属性',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `engineering_universal_criterion` (
  `id` int(11) NOT NULL,
  `project` varchar(20) DEFAULT NULL COMMENT '项目名称',
  `project_particulars` varchar(20) DEFAULT NULL COMMENT '项目详情',
  `units` varchar(10) DEFAULT NULL COMMENT '单位',
  `project_value` float(11,2) unsigned DEFAULT NULL COMMENT '值',
  `storey` float(5,2) unsigned zerofill DEFAULT NULL COMMENT '层高',
  `receive_storey` float(5,2) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `material_property_classify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classify` varchar(20) DEFAULT NULL COMMENT '分类',
  `category` varchar(20) DEFAULT NULL COMMENT '类型',
  `material` varchar(20) DEFAULT NULL COMMENT '材料',
  `quantity` int(10) DEFAULT '1' COMMENT '数量',
  `status` tinyint(1) unsigned zerofill DEFAULT '0' COMMENT '0-无计算公式，1-有计算公式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- 6.26 end--

CREATE TABLE `goods_recommend_supplier` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` bigint unsigned not null default 0,
  `platform_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `url` varchar(255) not null DEFAULT '',
  `supplier_id` int unsigned not null DEFAULT 0,
  `supplier_name` varchar(50) not null DEFAULT '',
  `title` varchar(100) not null DEFAULT '',
  `image` varchar(255) not null DEFAULT '',
  `description` varchar(255) not null DEFAULT '',
  `type` tinyint(1) not null default 0 comment '0: banner 2: second',
  `from_type` tinyint(1) not null default 0 comment '1: mall 2: link',
  `create_time` int not null DEFAULT 0,
  `delete_time` int not null DEFAULT 0,
  `status` tinyint(1) not null DEFAULT 0 comment '0: 已停用 1: 已启用',
  `sorting_number` int unsigned not null default 0,
  `district_code` int(6) unsigned not null default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 7.21 start--
CREATE TABLE `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_type` tinyint(1) NOT NULL COMMENT '1:普通发票  2： 电子发票',
  `invoice_header_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:个人发票  2.公司发票',
  `invoice_header` varchar(50) NOT NULL COMMENT '发票抬头',
  `invoicer_card` varchar(18) NOT NULL COMMENT '发票纳税人识别号',
  `invoice_content` varchar(30) NOT NULL COMMENT '发票内容',
  `invoicetoken` varchar(32) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: 未默认  1：  默认',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_address`;
CREATE TABLE `user_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT 0,
  `consignee` varchar(60) NOT NULL,
  `zipcode` varchar(60) NOT NULL,
  `mobile` varchar(60) NOT NULL,
  `district` int(6) NOT NULL,
  `region` varchar(150) NOT NULL,
  `addresstoken` varchar(32) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：未默认  1 ： 默认',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- 7.21 end

-- 7.27 start
create table district (
  `id` int(11) unsigned NOT NULL DEFAULT 0,
  `pid` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 7.27 end
-- 7.30 start
CREATE TABLE `order_goodslist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_number` int(8) NOT NULL,
  `goods_attr_id` varchar(50) NOT NULL,
  `create_time` int(11) NOT NULL,
  `goods_name` varchar(100) NOT NULL,
  `goods_price` bigint(20) NOT NULL COMMENT '商品购买价格',
  `sku` bigint(20) NOT NULL,
  `market_price` bigint(20) NOT NULL,
  `supplier_price` bigint(20) NOT NULL,
  `shipping_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:快递物流   1：送货上门',
  `order_status` tinyint(1) NOT NULL COMMENT '0:未完成 1:已完成 2:已取消',
  `shipping_status` tinyint(1) NOT NULL COMMENT '0:未配送 1：配送中 2.配送完成',
  `customer_service` tinyint(1) NOT NULL,
  `is_unusual` tinyint(1) NOT NULL COMMENT '0:无异常  1：请申退款 2： 退款失败',
  `freight` bigint(20) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `cover_image` varchar(100) NOT NULL COMMENT '订单商品封面图',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

create table user_mobile (
  `id` int(11) unsigned NOT NULL auto_increment,
  `uid` int(11) unsigned NOT NULL DEFAULT 0,
  `mobile` bigint DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 comment '绑定时间',
  `op_uid` int(11) unsigned not null DEFAULT 0 comment '操作人员用户id',
  `op_username` varchar(20) not null DEFAULT '' comment '操作人员名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 7.30 end

-- 8.1 start
create table user_status (
  `id` int(11) unsigned NOT NULL auto_increment,
  `uid` int(11) unsigned NOT NULL DEFAULT 0,
  `mobile` bigint DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 comment '操作时间',
  `op_uid` int(11) unsigned not null DEFAULT 0 comment '操作人员用户id',
  `op_username` varchar(20) not null DEFAULT '' comment '操作人员名称',
  `remark` varchar(100) not null DEFAULT '' comment '备注',
  `status` tinyint(1) unsigned not null DEFAULT 0 comment '0: 关闭, 1: 开启',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `supplier_bankinformation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL COMMENT '商家id',
  `bankname` varchar(50) NOT NULL COMMENT '开户银行',
  `bankcard` bigint(30) NOT NULL COMMENT '银行卡号',
  `username` varchar(50) NOT NULL COMMENT '开户名',
  `position` varchar(150) NOT NULL COMMENT '开户行所在地',
  `bankbranch` varchar(150) NOT NULL COMMENT '开户行支行名',
  `create_time` int(11) NOT NULL COMMENT '开户时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `supplier_freezelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `freeze_money` bigint(20) NOT NULL COMMENT '结冻资金',
  `supplier_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `freeze_reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 8.1 end
-- 8.3 start
CREATE TABLE `supplier_cashregister` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `cash_money` bigint(20) NOT NULL DEFAULT '0' COMMENT '申请提现金额',
  `real_money` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际到账金额',
  `apply_time` int(11) NOT NULL COMMENT '申请提现时间',
  `handle_time` int(11) DEFAULT NULL COMMENT '商家处理时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '提现状态  1:未提现 2:提现中 3:已提现 4:提现失败',
  `supplier_reason` varchar(150) NOT NULL COMMENT '商家提现操作原因',
  `transaction_no` varchar(50) NOT NULL COMMENT '交易单号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 8.3 end
-- 8.5 start
CREATE TABLE `assort_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL COMMENT '商品名称',
  `category_id` int(10) DEFAULT NULL COMMENT '分类id',
  `pid` int(10) DEFAULT NULL COMMENT '分类的父类id',
  `path` varchar(20) DEFAULT NULL COMMENT '关系',
  `state` tinyint(4) DEFAULT NULL COMMENT '0-案例商品管理，1-商品管理',
  `quantity` int(11) DEFAULT NULL COMMENT '数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- 8.5 end

-- 8.7  start
CREATE TABLE `alipayreturntest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(3000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- 8.7  end
-- 8.9  start
CREATE TABLE `distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL COMMENT '绑定手机号父id',
  `mobile` bigint(20) NOT NULL COMMENT '手机号',
  `profit` bigint(20) NOT NULL COMMENT '收益',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `applydis_time` int(11) DEFAULT NULL COMMENT '绑定父id时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- 8.9  end
-- 8.11 start
CREATE TABLE `supplier_accessdetail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_type` tinyint(1) NOT NULL COMMENT '1:货款 2.提现失败  3.充值  4.扣款  ',
  `access_money` bigint(20) NOT NULL COMMENT '收支金额',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单号',
  `transaction_no` varchar(50) NOT NULL COMMENT '交易单号',
  `supplier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- 8.11 end
-- 8.12 start
CREATE TABLE `express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `waybillnumber` varchar(60) NOT NULL COMMENT '快递单号',
  `waybillname` varchar(20) NOT NULL COMMENT '快递公司',
  `sku` int(11) NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `create_time` int(11) NOT NULL,
  `receive_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- 8.12 end

-- 8.13 start

DROP TABLE IF EXISTS supplier_bankinformation;
CREATE TABLE `user_bankinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `role_id` int(50) NOT NULL COMMENT '角色id',
  `log_id` int(11) NOT NULL COMMENT '银行卡记录id',
  `selected` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未默认  1：默认',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户银行卡信息';


-- 8.13 end

-- 8.15 start

CREATE TABLE `user_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` INT(11) NOT NULL COMMENT '用户id',
  `role_id` INT(11) NOT NULL COMMENT '角色id',
  `chat_username`  VARCHAR(100) NOT NULL COMMENT '环信用户名',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `login_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态  0:封禁 1:正常 ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户对应环信表';

CREATE TABLE `order_platform_handle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `handle` tinyint(1) NOT NULL COMMENT '1:关闭订单退款 2：关闭订单线下退款 3：退货 4.换货 5：上门维修 6：上门退货 7：上门换货',
  `reasons` varchar(300) NOT NULL,
  `creat_time` int(11) NOT NULL,
  `refund_result` tinyint(1) NOT NULL COMMENT '0：操作未进行 1：操作进行中 2：操作进行完成 3：操作进行失败',
  `refund_type` tinyint(1) NOT NULL COMMENT '1:退至顾客钱包 2.线下自行退款 3.退至支付宝',
  `refund_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- 8.15 end

-- 8.18 start

CREATE TABLE `works_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `effect_id` int(10) DEFAULT NULL COMMENT '效果图ID',
  `goods_first` varchar(20) DEFAULT NULL COMMENT '一级商品',
  `goods_second` varchar(20) DEFAULT NULL COMMENT '二级商品',
  `goods_three` varchar(20) DEFAULT NULL COMMENT '三级商品',
  `three_category_id` int(11) DEFAULT NULL,
  `goods_code` int(10) DEFAULT NULL COMMENT '商品编码',
  `goods_quantity` int(10) DEFAULT NULL COMMENT '商品数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `works_worker_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL,
  `worker_kind` varchar(10) DEFAULT NULL COMMENT '工作种类',
  `worker_price` bigint(20) DEFAULT NULL COMMENT '工人费用 unit： fen',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `works_backman_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL,
  `backman_option` varchar(20) DEFAULT NULL COMMENT '杂工选项',
  `backman_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 8.18 end

-- 8.22 start

CREATE TABLE `user_follow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '3: 设计师 5: 装修公司 6: 店铺',
  `follow_id` int(11) NOT NULL DEFAULT '0',
  `follow_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注时间',
  `unfollow_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '取关时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0: 不关注 1: 关注 ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter TABLE designer ADD COLUMN `follower_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关注人数';
alter TABLE decoration_company ADD COLUMN `follower_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关注人数';

-- 8.25 start
CREATE TABLE `order_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1:  商家待处理  2.处理完成',
  `apply_reason` varchar(100) NOT NULL,
  `handle` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未处理  1：同意  2：驳回',
  `handle_reason` varchar(100) NOT NULL,
  `create_time` int(11) NOT NULL,
  `handle_time` int(11) NOT NULL,
  `refund_time` int(11) NOT NULL,
  `order_type` varchar(30) NOT NULL  COMMENT '退款时状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- 8.25 end

-- 8.29 start
CREATE TABLE `brainpower_inital_supervise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province` varchar(20) DEFAULT NULL COMMENT '省',
  `province_code` int(20) DEFAULT NULL COMMENT '省编码',
  `city` varchar(20) DEFAULT NULL COMMENT '市',
  `city_code` int(20) DEFAULT NULL COMMENT '市编码',
  `district` varchar(20) DEFAULT NULL COMMENT '区',
  `district_code` int(20) DEFAULT NULL COMMENT '区编码',
  `street` varchar(100) DEFAULT NULL COMMENT '街道地址',
  `toponymy` varchar(20) DEFAULT NULL COMMENT '小区名称',
  `image` varchar(255) DEFAULT NULL COMMENT '图片名称',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `house_type_name` varchar(50) DEFAULT NULL COMMENT '户型名称',
  `recommend_name` varchar(50) DEFAULT NULL COMMENT '推荐名称',
  `status` tinyint(2) DEFAULT '0' COMMENT '0-下架 1-上架',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `worker`;

CREATE TABLE `worker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `project_manager_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '项目经理id',
  `province_code` int(20) DEFAULT NULL COMMENT '省份编码',
  `city_code` int(20) DEFAULT NULL COMMENT '市编码',
  `native_place` varchar(100) NOT NULL DEFAULT '' COMMENT '籍贯',
  `worker_type_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '工种id(只能选pid为0的)',
  `nickname` varchar(25) NOT NULL DEFAULT '' COMMENT '工人名字',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `follower_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关注人数',
  `comprehensive_score` float unsigned NOT NULL DEFAULT '10' COMMENT '综合评分',
  `feedback` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '好评率',
  `order_total` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总接单数',
  `order_done` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成订单数',
  `level` int(10) NOT NULL DEFAULT '0' COMMENT '数值填写对比worker_type等级',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `signature` varchar(100) NOT NULL DEFAULT '' COMMENT '个性签名',
  `availableamount` bigint(20) NOT NULL DEFAULT '0' COMMENT '可用余额',
  `balance` bigint(20) NOT NULL DEFAULT '0' COMMENT '余额, unit: fen',
  `pay_password` varchar(100) NOT NULL DEFAULT '' COMMENT '支付密码',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
  `examine_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '认证状态 0:未认证 1:待审核 2:审核不通过 3:已认证',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '接单状态: 1,接单 0,不接单',
  `skill_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '特长的id',
  `work_year` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '工龄：单位(年)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `worker_order`;

CREATE TABLE `worker_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `worker_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '工人id',
  `worker_type_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '工人类型id',
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '工单号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '工期(天数)',
  `days` varchar(1000) NOT NULL DEFAULT '' COMMENT '工作的具体日期(很多天,逗号分隔)',
  `map_location` varchar(100) NOT NULL DEFAULT '' COMMENT '地图定位',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '施工详细地址',
  `con_people` varchar(25) NOT NULL DEFAULT '' COMMENT '联系人',
  `con_tel` char(11) NOT NULL COMMENT '联系电话',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单总金额',
  `front_money` bigint(20) NOT NULL DEFAULT '0' COMMENT '订金',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:未开始 1:接单中 2:已接单 3:申请开工(已接单) 4:施工中 5:已完工 6:已取消',
  `describe` varchar(350) NOT NULL DEFAULT '' COMMENT '订单描述',
  `demand` varchar(300) NOT NULL DEFAULT '' COMMENT '个性需求',
  `reason` varchar(350) NOT NULL DEFAULT '' COMMENT '修改原因',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:快捷下单 1：非快捷下单',
  `is_old` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否旧数据，0：不是，  1：是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `worker_type`;
DROP TABLE IF EXISTS `work_type`;

CREATE TABLE `worker_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(1) NOT NULL DEFAULT '0' COMMENT '所属上级工种id',
  `worker_name` varchar(20) NOT NULL DEFAULT '' COMMENT '工种名字',
  `image` varchar(255) DEFAULT NULL COMMENT '工程图片',
  `establish_time` int(11) DEFAULT NULL COMMENT '创建日期',
  `status` tinyint(4) DEFAULT '0' COMMENT '0：下架，1：上架',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `labor_cost_detail`;

CREATE TABLE `labor_cost_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `worker_tpye_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '工种id',
  `province_code` char(6) NOT NULL COMMENT '省份编码',
  `city_code` char(6) NOT NULL COMMENT '市编码',
  `place` VARCHAR(25) NOT NULL DEFAULT '' COMMENT '具体地点',
  `craft` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '工艺',
  `price` bigint(20) NOT NULL DEFAULT '0' COMMENT '价格 (分)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `worker_order_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `worker_order_no` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '工人订单号',
  `order_img` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '工单图片地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `work_result_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_day_result_id` int(11) NOT NULL DEFAULT '0' COMMENT '工作日期id',
  `result_img_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '工作成果图片名称',
  `result_img` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '工作单成果图片地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 8.29 end

-- 8.30 start

CREATE TABLE `worker_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(25) NOT NULL DEFAULT '' COMMENT '条目名字',
  `unit` tinyint(1) not null DEFAULT 0 comment '0: 无, 1: L, 2: M, 3: M^2, 4: Kg, 5: MM',
  `pid` INT(11) NOT NULL DEFAULT 0 COMMENT '父id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `worker_craft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` INT(11) NOT NULL DEFAULT '0' COMMENT '工种条目id',
  `craft` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '工艺',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `worker_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `worker_order_id` int(11) NOT NULL DEFAULT '0' COMMENT '工单id',
  `worker_item_id` int(11) NOT NULL DEFAULT '0' COMMENT '工人条目id',
  `worker_craft_id` int(11) NOT NULL DEFAULT '0' COMMENT '工艺id',
  `area` bigint(20) NOT NULL DEFAULT '0' COMMENT '面积,单位: dm^2',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0: 否，1：是',
  `length` bigint(20) NOT NULL DEFAULT '0' COMMENT '长度（水电）',
  `electricity` tinyint(1) NOT NULL DEFAULT '0' COMMENT '（水电：0:弱电,1:强电）',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '(水电：个数)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `worker_type_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `worker_type_id` INT(11) NOT NULL DEFAULT '0' COMMENT '工种id(只能选pid为0的)',
  `worker_item_id` INT(11) NOT NULL DEFAULT '0' COMMENT '工人条目id(只能选pid为0的)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 9.6 start
CREATE TABLE `order_after_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(20) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1. 退货  2.换货  3.上门维修  4. 上门换货   5.上门退货  ',
  `description` varchar(600) NOT NULL COMMENT '问题描述',
  `create_time` int(11) NOT NULL,
  `supplier_handle` tinyint(1) NOT NULL,
  `supplier_handle_reason` varchar(50) NOT NULL,
  `supplier_handle_time` int(11) NOT NULL,
  `complete_time` int(11) NOT NULL,
  `buyer_express_id` int(11) NOT NULL,
  `buyer_express_confirm` tinyint(1) NOT NULL COMMENT '0:未确认  1:已确认',
  `supplier_express_confirm` tinyint(1) NOT NULL COMMENT '0 :未确认  1：已确认',
  `supplier_express_id` int(11) NOT NULL,
  `worker_name` varchar(30) NOT NULL,
  `worker_mobile` varchar(20) NOT NULL,
  `supplier_send_man` tinyint(1) NOT NULL COMMENT '0:派人  1：已派出人员',
  `supplier_send_time` int(11) NOT NULL COMMENT '商家派出工作人员时间',
  `buyer_confirm` tinyint(1) NOT NULL COMMENT '0 :未确认  1：已确认',
  `buyer_confirm_time` int(11) NOT NULL COMMENT '顾客确认时间',
  `supplier_confirm` tinyint(1) NOT NULL COMMENT '0: 未确认  1：已确认',
  `supplier_confirm_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `order_after_sale_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `after_sale_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- 9.6 end
CREATE TABLE `craft_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `worker_craft_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '具体工艺id',
  `province_code` varchar(20) DEFAULT NULL COMMENT '省份编码',
  `city_code` varchar(20) DEFAULT NULL COMMENT '市编码',
  `price` bigint(20) NOT NULL COMMENT '工人价格',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `worker_grade_supervise` (
  `id` int(11) NOT NULL,
  `work_type` varchar(10) DEFAULT NULL COMMENT '工作种类',
  `grade` varchar(10) DEFAULT NULL COMMENT '级别',
  `min_value` int(4) unsigned zerofill DEFAULT '0001' COMMENT '最小值',
  `max_value` int(4) unsigned zerofill DEFAULT '0100' COMMENT '最大值',
  `province_code` int(10) DEFAULT '510000' COMMENT '省编码',
  `city_code` int(10) DEFAULT '510100' COMMENT '市编码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `worker_craft_norm` (
  `id` int(11) NOT NULL,
  `labor_cost_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL COMMENT '每天完成的数量',
  `worker_kind_details` varchar(20) DEFAULT NULL COMMENT '工种详情',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 9.13 start

CREATE TABLE `user_accessdetail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `role_id` int(50) NOT NULL DEFAULT 0 COMMENT '角色id',
  `access_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1.充值 2.扣款 3.已提现 4.提现中  5.驳回 6.货款 7.使用',
  `access_money` bigint(20) NOT NULL DEFAULT 0 COMMENT '收支金额',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `transaction_no` varchar(50) NOT NULL DEFAULT '' COMMENT '交易单号',
  `sku` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_cashregister` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `role_id` int(50) NOT NULL DEFAULT '0' COMMENT '角色id',
  `bank_log_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户银行卡操作记录id',
  `cash_money` bigint(20) NOT NULL DEFAULT '0' COMMENT '申请提现金额',
  `real_money` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际到账金额',
  `apply_time` int(11) NOT NULL COMMENT '申请提现时间',
  `handle_time` int(11) DEFAULT NULL COMMENT '处理提现时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '提现状态  1:提现中 2:已提现 3:提现失败',
  `supplier_reason` varchar(150) NOT NULL COMMENT '提现操作原因',
  `transaction_no` varchar(50) NOT NULL COMMENT '交易单号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `engineering_standard_carpentry_coefficient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project` varchar(10) DEFAULT NULL COMMENT '项目名称',
  `value` float(10,2) DEFAULT NULL COMMENT '值',
  `coefficient` int(5) DEFAULT NULL COMMENT '系数',
  `series_or_style` tinyint(4) DEFAULT NULL COMMENT '0-系数，1-风格',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `engineering_standard_carpentry_craft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '名称',
  `value` float(11,2) DEFAULT NULL COMMENT '值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `coefficient_management` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classify` varchar(5) DEFAULT NULL COMMENT '分类',
  `coefficient` float(10,2) DEFAULT '1.00' COMMENT '系数值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `user_freezelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `freeze_money` bigint(20) NOT NULL DEFAULT '0' COMMENT '冻结余额',
  `create_time` int(11) NOT NULL,
  `freeze_reason` varchar(255) NOT NULL COMMENT '冻结原因',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未解冻 1:已解冻',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 9.14 start

CREATE TABLE `worker_skill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skill` varchar(50) NOT NULL DEFAULT '' COMMENT '工人特长',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `worker_works_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `works_id` int(11) NOT NULL DEFAULT 0 COMMENT '工人作品id',
  `star` tinyint(2) NOT NULL DEFAULT 15 COMMENT '作品评分',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `role_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户角色id',
  `review` VARCHAR(350) NOT NULL DEFAULT '' COMMENT '评论内容',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '工人作品评论表';


CREATE TABLE `worker_order_day_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` VARCHAR(50) NOT NULL DEFAULT 0 COMMENT '订单号',
  `work_desc` VARCHAR(350) NOT NULL DEFAULT '' COMMENT '工作描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 9.15 start

CREATE TABLE `worker_works` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `worker_id` int(11) NOT NULL DEFAULT '0' COMMENT '工人id',
  `order_no` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '工人订单号',
  `title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '标题',
  `desc` VARCHAR(350) NOT NULL DEFAULT '' COMMENT '作品描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='工人作品表';



CREATE TABLE `worker_works_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `works_id` int(11) NOT NULL DEFAULT 0 COMMENT '工人作品id',
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '状态: 0:无效, 1:前, 2:中, 3:后',
  `desc` VARCHAR(350) NOT NULL DEFAULT '' COMMENT '描述',
  `img_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '图片id逗号分隔，有前导0的为worker_order_img，没有为worker_result_img',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='工人作品详情';
-- 9.15 end

-- 9.20 start
CREATE TABLE `apartment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apartment` varchar(100) NOT NULL DEFAULT '' COMMENT '户型',
  `status` tinyint(1) NOT NULL COMMENT '状态 0:可用 1:不可用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='户型表';

CREATE TABLE `point_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `parent_id` int(11) NOT NULL COMMENT '父级id',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '个数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='点位表';

CREATE TABLE `project_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points_id` int(11) DEFAULT NULL,
  `project` varchar(100) NOT NULL DEFAULT '' COMMENT '项目名称',
  `parent_project` varchar(100) NOT NULL COMMENT '父级项目名称',
  `apartment_area_id` int(11) NOT NULL DEFAULT '0' COMMENT '户型id',
  `project_value` int(11) NOT NULL COMMENT '项目值',
  `unit` varchar(20) NOT NULL COMMENT '单位',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目详细表';

-- 9.25 start

CREATE TABLE `bankinfo_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bankname` varchar(50) NOT NULL COMMENT '开户银行',
  `bankcard` bigint(30) NOT NULL COMMENT '银行卡号',
  `bank_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1.信用卡  2.借记卡',
  `username` varchar(50) NOT NULL COMMENT '开户名',
  `position` varchar(150) NOT NULL COMMENT '开户行所在地',
  `bankbranch` varchar(150) NOT NULL COMMENT '开户行支行名',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户银行卡记录';

CREATE TABLE `decoration_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_add_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL COMMENT '数量',
  `style_id` int(11) DEFAULT NULL,
  `series_id` int(11) DEFAULT NULL,
  `min_area` int(11) DEFAULT NULL,
  `max_area` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `apartment_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points_id` int(11) DEFAULT NULL,
  `min_area` int(11) DEFAULT NULL,
  `max_area` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `deleted_goods_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(25) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '头像地址',
  `content` varchar(255) NOT NULL DEFAULT '',
  `score` float NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0',
  `store_service_score` float NOT NULL DEFAULT '0',
  `shipping_score` float NOT NULL DEFAULT '0',
  `logistics_speed_score` float NOT NULL DEFAULT '0',
  `is_anonymous` tinyint(1) NOT NULL COMMENT '1:匿名  2： 实名',
  `handle_uid` int(11) NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(30) NOT NULL,
  `comment_time` int(11) NOT NULL COMMENT '用户评论时间',
  `comment_id` int(11) NOT NULL COMMENT '评论ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- 10.23 start
CREATE TABLE `chat_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `send_role_id` int(11) NOT NULL,
  `send_uid` int(11) NOT NULL,
  `to_role_id` int(11) NOT NULL,
  `to_uid` int(11) NOT NULL,
  `content` varchar(255) NOT NULL COMMENT '内容',
  `send_time` int(11) NOT NULL,
  `type` varchar(20) DEFAULT NULL,

  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未读 1:已读',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- 10.27 start
CREATE TABLE `effect_earnest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) NOT NULL COMMENT '样板id',
  `phone` char(11) NOT NULL COMMENT '电话号码',
  `name` varchar(255) NOT NULL COMMENT '名字',
  `earnest` bigint(20) NOT NULL DEFAULT '0' COMMENT '定金',
  `transaction_no` varchar(50) NOT NULL COMMENT '交易单号',
  `remark` text NOT NULL COMMENT '备注',
  `requirement` varchar(255) DEFAULT NULL,
  `original_price` bigint(20) NOT NULL DEFAULT '0' COMMENT '原价',
  `sale_price` bigint(20) NOT NULL DEFAULT '0' COMMENT '打折价',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未付款 1:已付款',
  `create_time` int(11) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_news_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(255) NOT NULL,
  `send_time` int(11) NOT NULL,
  `order_no` varchar(30) NOT NULL,
  `sku` bigint(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未读 1:已读',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 11.2 start
CREATE TABLE `worker_rank` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `worker_type_id` int(11) DEFAULT NULL,
  `rank_name` char(20) DEFAULT NULL COMMENT '等级名称',
  `min_value` int(11) DEFAULT NULL,
  `max_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `shipping_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `goods_id` bigint(20) NOT NULL,
  `goods_num` bigint(20) NOT NULL DEFAULT '1' COMMENT '购物车商品数量',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `effect_material` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) NOT NULL DEFAULT '0' COMMENT '样板id',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品id',
  `price` bigint(20) NOT NULL COMMENT '商品价格',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '商品个数',
  `first_cate_id` int(11) NOT NULL DEFAULT '0' COMMENT '一级分类id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

insert into role(id, name, admin_module, detail_table, detail_model) values
(1, '公司后台管理员', 'lhzz', 'lhzz', 'Lhzz'),
(2, '工人', 'worker', 'worker', 'Worker'),
(3, '设计师', 'designer', 'designer', 'Designer'),
(4, '项目经理', 'manager', 'project_manager', 'manager'),
(5, '装修公司', 'decoration_company','decoration_company','DecorationCompany'),
(6, '供应商', 'supplier', 'supplier', 'Supplier'),
(7, '业主', 'owner', 'user', 'User')
;

INSERT INTO user VALUES (1,'',0,'$2y$13$oPfoskC5c0E7x87z2kQ0cebTBosxLs6Gs6BdBA8cT.bf.Q7Ir4Pf6','pjf2vsrvkk9psa10reans9q7vl','pjf2vsrvkk9psa10reans9q7vl','s5g3gcdt4lavao2i4fgoq3amhr','sqpjfv6pd5htioi0f9fg9ogt5h','',13551201821,'恒少sss','230622199507135858',10001,'uploads/2017/09/26/1506411137.jpg',1493458425,1509949890,1,6,'111','uploads/2017/08/04/1501833935.jpg','uploads/2017/08/04/1501830700.jpg',0,'13551201821爸爸爸爸本 我升级',0,20170828,450127,'广西壮族自治区-南宁市-横县',9593702,9794702,'$2y$13$q4wxMCZytBiGfj9sXsrnq.CSoWZETPqssWIzbxAhRvrOMTxfOVc66','100d855909432a20899');

insert into user_role(id, user_id, role_id, review_status) values(1, 1, 1, 2);
insert into lhzz(id, uid, nickname) values(1, 1, 'hj');
