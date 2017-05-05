CREATE TABLE `goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int unsigned not null default 0,
  `category_id` int unsigned not null default 0,
  `sku` bigint unsigned not null default 0,
  `title` varchar(100) not null DEFAULT '',
  `image1` varchar(255) not null DEFAULT comment '封面图',
  `image2` varchar(255) not null DEFAULT '',
  `image3` varchar(255) not null DEFAULT '',
  `image4` varchar(255) not null DEFAULT '',
  `image5` varchar(255) not null DEFAULT '',
  `supplier_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `platform_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `market_price` bigint not null DEFAULT 0 comment 'unit: fen',
  `description` varchar(255) not null DEFAULT '',
  `create_time` int not null DEFAULT 0,
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