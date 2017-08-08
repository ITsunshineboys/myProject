create table role (
    id int PRIMARY key auto_increment,
    name varchar(25) not null default '',
    admin_module varchar(25) not null default '',
    detail_table varchar(25) not null default ''
) default charset = utf8;

insert into role(id, name, admin_module, detail_table) values
(1, '公司后台管理员', 'lhzz', 'lhzz'),
(2, '工人', 'worker', 'worker'),
(3, '设计师', 'designer', 'designer'),
(4, '项目经理', 'manager', 'project_manager'),
(5, '装修公司', 'decoration_company','decoration_company'),
(6, '供应商', 'supplier', 'supplier'),
(7, '业主', 'owner', 'user')
;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) DEFAULT '',
  `password` varchar(100) DEFAULT '',
  `authKey` varchar(30) DEFAULT '',
  `authKeyAdmin` varchar(30) DEFAULT '',
  `accessToken` varchar(255) DEFAULT '',
  `mobile` bigint DEFAULT 0,
  `nickname` varchar(20) DEFAULT '',
  `identity_no` varchar(18) DEFAULT '',
  `aite_cube_no` bigint DEFAULT 0,
  `icon` varchar(100) DEFAULT '',
  `create_time` int DEFAULT 0,
  `login_time` int DEFAULT 0,
  `login_role_id` int DEFAULT 0,
  `legal_person` varchar(50) not null DEFAULT '' comment '法人',
  `identity_card_front_image` varchar(255) not null DEFAULT '' comment '身份证正面图片',
  `identity_card_back_image` varchar(255) not null DEFAULT '' comment '身份证反面图片',
  `deadtime` int unsigned not null default 0 comment '封号时间',
  `signature` varchar(20) not null DEFAULT '' comment '个性签名',
  `gender` tinyint(1) unsigned not null DEFAULT 0 comment '0: 男, 1: 女, 2: 保密',
  `birthday` int(8) unsigned not null DEFAULT 0,
  `district_code` int(6) unsigned not null default 0 comment '区域码',
  `district_name` varchar(50) NOT NULL DEFAULT '' comment '区域名',
  `balance` bigint not null DEFAULT 0 comment '余额, unit: fen',
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
  `status` tinyint(1) not null default 0,
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
  `pay_password` varchar(100) NOT NULL COMMENT '支付密码' ,
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

CREATE TABLE `worker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '工人姓名',
  `project_manager_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目经理“id”',
  `work_type_id` int(11) unsigned not null DEFAULT 0 COMMENT '工种类型“id”',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table work_type (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lhzz` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--5.5 start--
CREATE TABLE `goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int unsigned not null default 0,
  `brand_id` int unsigned not null default 0,
  `category_id` int unsigned not null default 0,
  `area_id` int(11) NOT NULL,
  `series_id` int(11) unsigned not null default 0,
  `style_id` int(11) unsigned not null default 0,
  `sku` bigint unsigned not null default 0,
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
  `status` tinyint(1) not null DEFAULT 0 comment '0：已下架, 1：等待上架, 2：已上架, 3：已删除',
  `after_sale_services` set('0', '1', '2', '3', '4') not null DEFAULT '0' comment '0：提供发票, 1：上门安装, 2：上门维修, 3：上门退货, 4:上门换货, 5：退货, 6:换货',
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
  `amount_sold` int unsigned not null default 0 comment '销售额',
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
  `address_id` int(11) NOT NULL COMMENT '收货地址号',
  `invoice_id` int(11) NOT NULL COMMENT '发票信息',
  `pay_status` tinyint(1) NOT NULL COMMENT 'pay_status：0：未付款 1：已付款 2：已退款',
  `user_id` int(11) NOT NULL,
  `pay_name` varchar(120) NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `paytime` int(11) NOT NULL DEFAULT '0',
  `order_refer` tinyint(1) NOT NULL COMMENT 'order_refer:1：线下店2：非线下店；',
  `return_insurance` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='\r\n';
--5.5 end--

--5.6 start--
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

CREATE TABLE `style_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(11) DEFAULT NULL COMMENT '风格表id',
  `picture` varchar(255) DEFAULT '' COMMENT '图片',
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
  `house_pictrue` varchar(255) DEFAULT '' COMMENT '户型图',
  `vr_pictrue` varchar(255) DEFAULT NULL COMMENT 'VR图',
  `images_user` varchar(20) DEFAULT NULL COMMENT '图纸名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `effect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `series_id` int(11) DEFAULT NULL COMMENT '系列id',
  `style_id` int(11) DEFAULT NULL COMMENT '风格id',
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
  `street` varchar(10) DEFAULT NULL COMMENT '街道',
  `particulars` varchar(50) DEFAULT NULL COMMENT '厅室详情',
  `site_particulars` varchar(100) DEFAULT NULL COMMENT '地址详情',
  `stairway` tinyint(5) DEFAULT '0' COMMENT '楼梯信息 0：无 1：有',
  `add_time` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `stairdetail` varchar(100)  DEFAULT NULL COMMENT '楼梯材料',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `labor_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province_code` varchar(20) DEFAULT NULL COMMENT '省份编码',
  `city_code` varchar(20) DEFAULT NULL COMMENT '市编码',
  `univalence` bigint(10) NOT NULL COMMENT '工人单价',
  `worker_kind` varchar(20) DEFAULT NULL COMMENT '工人种类',
  `quantity` int(10) NOT NULL DEFAULT '0' COMMENT '每天完成的数量',
  `unit` varchar(10) DEFAULT NULL COMMENT '单位',
  `rank` varchar(20) DEFAULT NULL COMMENT '工人级别',
  `worker_kind_details` varchar(20) DEFAULT NULL COMMENT '工种详情',
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

CREATE TABLE `main_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `effect_id` int(11) DEFAULT NULL COMMENT '效果图',
  `province` varchar(50) DEFAULT NULL COMMENT '省',
  `province_code` int(11) DEFAULT NULL COMMENT '省编码',
  `city` varchar(50) DEFAULT NULL COMMENT '市',
  `city_code` int(11) DEFAULT NULL COMMENT '市编码',
  `series_id` int(11) DEFAULT NULL COMMENT '系列',
  `style_id` int(11) DEFAULT NULL COMMENT '风格',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fixation_furniture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL,
  `goods_category_id` int(11) DEFAULT NULL,
  `province` varchar(20) DEFAULT NULL COMMENT '省',
  `city` varchar(20) DEFAULT NULL COMMENT '市',
  `series_id` int(11) DEFAULT NULL,
  `style_id` int(11) DEFAULT NULL,
  `province_code` int(11) DEFAULT NULL COMMENT '省编码',
  `city_code` int(11) DEFAULT NULL COMMENT '市编码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `move_furniture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL COMMENT '省',
  `province_code` int(11) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `city_code` int(11) DEFAULT NULL,
  `series_id` int(11) DEFAULT NULL,
  `style_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `appliances_assort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `goods_category_id` int(11) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `province_code` int(11) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `city_code` int(11) DEFAULT NULL,
  `series_id` int(11) DEFAULT NULL,
  `style_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `soft_outfit_assort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_category_id` int(11) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `province_code` int(11) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `city_code` int(11) DEFAULT NULL,
  `series_id` int(11) DEFAULT NULL,
  `style_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `intelligence_assort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_category_id` int(11) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `province_code` int(11) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `city_code` int(11) DEFAULT NULL,
  `series_id` int(11) DEFAULT NULL,
  `style_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `life_assort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_category_id` int(11) DEFAULT NULL,
  `province_code` int(11) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `city_code` int(11) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `series_id` int(11) DEFAULT NULL,
  `style_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place` varchar(20) DEFAULT NULL COMMENT '位置',
  `weak_current_points` int(5) NOT NULL DEFAULT '0' COMMENT '弱电点位',
  `effect_id` int(11) DEFAULT NULL,
  `waterway_points` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
--5.6 end--

--5.26 start--
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
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `hall_area` int(10) DEFAULT NULL COMMENT '客餐厅及过道地面面积',
  `bedroom_area` int(10) DEFAULT NULL COMMENT '卧室地面面积',
  `toilet_area` int(10) DEFAULT NULL COMMENT '卫生间地面面积',
  `kitchen_area` int(10) DEFAULT NULL COMMENT '厨房地面面积',
  `hallway_area` int(10) DEFAULT NULL COMMENT '玄关地面面积',
  `hall_perimeter` int(10) DEFAULT NULL COMMENT '客餐厅及过道周长',
  `bedroom_perimeter` int(10) DEFAULT NULL COMMENT '卧室周长',
  `toilet_perimeter` int(10) DEFAULT NULL COMMENT '卫生间周长',
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
--5.26 end--

--6.26 start --
CREATE TABLE `decoration_add` (
  `id` int(11) NOT NULL,
  `series_id` int(11) DEFAULT '0' COMMENT '系列',
  `style_id` int(11) DEFAULT '0' COMMENT '风格',
  `project` varchar(50) DEFAULT NULL COMMENT '项目名称',
  `min_area` int(10) DEFAULT '0' COMMENT '最小面积',
  `max_area` int(10) DEFAULT '0' COMMENT '最大面积',
  `material` varchar(20) DEFAULT NULL COMMENT '材料',
  `quantity` int(20) DEFAULT '0' COMMENT '数量',
  `sku` int(10) DEFAULT NULL COMMENT '商品编码',
  `supplier_price` bigint(20) DEFAULT NULL COMMENT '平台价格',
  `district_code` int(10) DEFAULT NULL COMMENT '城市编码',
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
  `id` int(11) NOT NULL,
  `classify` varchar(20) DEFAULT NULL COMMENT '分类',
  `category` varchar(20) DEFAULT NULL COMMENT '类型',
  `material` varchar(20) DEFAULT NULL COMMENT '材料',
  `quantity` int(10) DEFAULT '1' COMMENT '数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--6.26 end--

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


--7.21 start--
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_type` varchar(5) NOT NULL,
  `invoice_header_type` int(1) NOT NULL DEFAULT '0',
  `invoice_header` varchar(50) NOT NULL,
  `invoice_content` varchar(30) NOT NULL,
  `creat_time` datetime NOT NULL,
  `invoicetoken` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
--7.21 end

--7.27 start
create table district (
  `id` int(11) unsigned NOT NULL DEFAULT 0,
  `pid` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--7.27 end
--7.30 start
DROP TABLE IF EXISTS `order_goodslist`;
CREATE TABLE `order_goodslist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_number` int(8) NOT NULL,
  `goods_attr_id` varchar(50) NOT NULL,
  `create_time` int(11) NOT NULL,
  `goods_name` varchar(100) NOT NULL,
  `goods_price` bigint(20) NOT NULL,
  `sku` int(11) NOT NULL,
  `market_price` bigint(20) NOT NULL,
  `supplier_price` bigint(20) NOT NULL,
  `shipping_type` tinyint(1) NOT NULL,
  `order_status` tinyint(1) NOT NULL,
  `shipping_status` tinyint(1) NOT NULL,
  `customer_service` tinyint(1) NOT NULL,
  `is_unusual` tinyint(1) NOT NULL,
  `freight` bigint(20) NOT NULL,
  `comment` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

create table user_mobile (
  `id` int(11) unsigned NOT NULL auto_increment,
  `uid` int(11) unsigned NOT NULL DEFAULT 0,
  `mobile` bigint DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 comment '绑定时间',
  `op_uid` int(11) unsigned not null DEFAULT 0 comment '操作人员用户id',
  `op_username` varchar(20) not null DEFAULT '' comment '操作人员名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--7.30 end

--8.1 start
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
  `supplier_id` int(11) NOT NULL,
  `bankname` varchar(50) NOT NULL,
  `bankcard` int(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `position` varchar(150) NOT NULL,
  `bankbranch` varchar(150) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `supplier_freezelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `freeze_money` bigint(20) NOT NULL COMMENT '结冻资金',
  `supplier_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `freeze_reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--8.1 end
--8.3 start
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--8.3 end
--8.5 start
CREATE TABLE `effect_earnst` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL COMMENT '样板id',
  `phone` char(11) DEFAULT NULL COMMENT '电话号码',
  `name` varchar(255) DEFAULT NULL COMMENT '名字',
  `earnest` decimal(9,2) DEFAULT '0.00' COMMENT '定金',
  `remark` text COMMENT '备注',
  `create_time` int(11) DEFAULT NULL COMMENT '申请时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


CREATE TABLE `assort_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `one_path` varchar(20) DEFAULT NULL COMMENT '一级分类',
  `two_path` varchar(20) DEFAULT NULL COMMENT '二级分类',
  `three_parh` varchar(20) DEFAULT NULL COMMENT '三级分类',
  `add_path` varchar(20) DEFAULT NULL COMMENT '商品管理',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--8.5 end

--8.7  start
CREATE TABLE `alipayreturntest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(3000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
--8.7  end


