CREATE TABLE `goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int unsigned not null default 0,
  `brand_id` int unsigned not null default 0,
  `category_id` int unsigned not null default 0,
  `series_id` int(11) unsigned not null default 0,
  `style_id` int(11) unsigned not null default 0,
  `sku` bigint unsigned not null default 0,
  `title` varchar(100) not null DEFAULT '',
  `subtitle` varchar(100) not null DEFAULT '',
  `image1` varchar(255) not null DEFAULT '' comment '封面图',
  `image2` varchar(255) not null DEFAULT '',
  `image3` varchar(255) not null DEFAULT '',
  `image4` varchar(255) not null DEFAULT '',
  `image5` varchar(255) not null DEFAULT '',
  `supplier_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `platform_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `market_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `purchase_price` bigint not null DEFAULT 0 comment 'unit: fen',
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) unsigned not null default 0,
  `supplier_name` varchar(25) not null DEFAULT '' COMMENT '供应商',
  `title` varchar(50) not null DEFAULT '',
  `pid` int(11) unsigned not null default 0,
  `level` tinyint(1) unsigned not null default 0,
  `path` varchar(50) not null DEFAULT '',
  `icon` varchar(255) not null DEFAULT '',
  `description` text not null,
  `approve_time` int unsigned not null DEFAULT 0,
  `reject_time` int unsigned not null DEFAULT 0,
  `reason` varchar(100) not null DEFAULT '' comment '原因',
  `create_time` int unsigned not null DEFAULT 0,
  `review_status` tinyint(1) not null DEFAULT 0 comment '0: 待审核 1: 审核不通过 2:审核通过',
  `deleted` tinyint(1) not null default 0,
  `online_time` int unsigned not null DEFAULT 0,
  `offline_time` int unsigned not null DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) not null DEFAULT '',
  `logo` varchar(255) not null DEFAULT '',
  `certificate` varchar(255) not null DEFAULT '',
  `supplier_id` int(11) unsigned not null default 0,
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
  `create_time` int not null DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;