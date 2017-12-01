-- 上线后，数据表的更新放在这个文件 --

ALTER TABLE engineering_standard_craft modify column id int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE decoration_particulars add effect_id int(11) DEFAULT NULL COMMENT '装修列表' after id;


ALTER TABLE effect_earnest ADD uid INT (11) DEFAULT '0' COMMENT '用户id' AFTER id;
ALTER TABLE effect_earnest ADD type TINYINT (1) DEFAULT '0' COMMENT '类型 0:申请方案 1:保存方案' AFTER status;
ALTER TABLE effect_earnest ADD item TINYINT (1) DEFAULT '0' COMMENT '0:H5 1:App' AFTER type;

--12.1 add table
CREATE TABLE `order_goods_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(50) NOT NULL DEFAULT '',
  `unit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: 无, 1: L, 2: M, 3: M^2, 4: Kg, 5: MM',
  `addition_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: 普通添加, 1: 下拉框添加',
  `goods_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `order_goods_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `certificate` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `order_goods_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `order_logistics_district` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_template_id` int(11) NOT NULL DEFAULT '0',
  `district_code` int(6) unsigned NOT NULL DEFAULT '0',
  `district_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `order_logistics_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `delivery_method` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：快递物流，1：送货上门',
  `delivery_cost_default` bigint(20) NOT NULL DEFAULT '0' COMMENT '默认运费, 单位：分',
  `delivery_number_default` int(11) NOT NULL DEFAULT '0' COMMENT '默认运费对应商品数量',
  `delivery_cost_delta` bigint(20) NOT NULL DEFAULT '0' COMMENT '增加件运费, 单位：分',
  `delivery_number_delta` int(11) NOT NULL DEFAULT '0' COMMENT '增加件运费对应商品数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `order_series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) DEFAULT '' COMMENT '订单号',
  `sku` varchar(50) DEFAULT '' COMMENT '商品编号',
  `series` varchar(50) DEFAULT '' COMMENT '系列',
  `intro` varchar(255) DEFAULT '' COMMENT '系列介绍',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `order_style` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `style` varchar(50) DEFAULT '' COMMENT '风格',
  `intro` varchar(255) DEFAULT '' COMMENT '风格介绍',
  `theme` varchar(50) DEFAULT '' COMMENT '风格主题',
  `images` varchar(255) DEFAULT NULL COMMENT '图片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `order_goodslist` ADD `category_id` int(11) DEFAULT '0' COMMENT '分类id';
ALTER TABLE `order_goodslist` ADD  `after_sale_services` set('0', '1', '2', '3', '4', '5', '6') not null DEFAULT '0' comment '0：提供发票, 1：上门安装, 2：上门维修, 3：上门退货, 4:上门换货, 5：退货, 6:换货';


CREATE TABLE `order_goods_description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) CHARACTER SET utf32 NOT NULL DEFAULT '' COMMENT '订单商品描述',
  `order_no` varchar(50) NOT NULL,
  `sku` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `order_goodslist` ADD `platform_price` bigint(20) NOT NULL DEFAULT '0' COMMENT 'unit: fen';
ALTER TABLE `order_goodslist` ADD  `purchase_price_decoration_company` bigint(20) NOT NULL DEFAULT '0' COMMENT 'unit: fen';
ALTER TABLE `order_goodslist` ADD  `purchase_price_manager` bigint(20) NOT NULL DEFAULT '0' COMMENT 'unit: fen';
ALTER TABLE `order_goodslist` ADD  `purchase_price_designer` bigint(20) NOT NULL DEFAULT '0' COMMENT 'unit: fen';

ALTER TABLE `order_goodslist` ADD  `subtitle` varchar(100) NOT NULL DEFAULT '';



