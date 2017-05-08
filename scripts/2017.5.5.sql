CREATE TABLE `goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int unsigned not null default 0,
  `brand_id` int unsigned not null default 0,
  `category_id` int unsigned not null default 0,
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
  `favourable_comment_rate` tinyint unsigned not null default 0 comment '好评率',
  `description` varchar(255) not null DEFAULT '',
  `create_time` int not null DEFAULT 0,
  `status` tinyint(1) not null DEFAULT 0 comment '0: 上架 1: 下架',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_recommend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` bigint unsigned not null default 0,
  `title` varchar(100) not null DEFAULT '',
  `image` varchar(255) not null DEFAULT '',
  `description` varchar(255) not null DEFAULT '',
  `type` tinyint(1) not null default 0 comment '1: first 2: second',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `goods_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) not null DEFAULT '',
  `pid` int(11) unsigned not null default 0,
  `level` tinyint(1) unsigned not null default 0,
  `path` varchar(50) not null DEFAULT '',
  `icon` varchar(255) not null DEFAULT '',
  `deleted` tinyint(1) not null default 0,
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