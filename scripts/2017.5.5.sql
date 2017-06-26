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
  `unit` tinyint(1) not null DEFAULT 0 comment '0: 无, 1: L, 2: M, 3: M^2, 4: Kg',
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