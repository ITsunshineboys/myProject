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
  `image` varchar(255) NOT NULL COMMENT '图片名称',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `house_type_name` varchar(50) DEFAULT NULL COMMENT '户型名称',
  `recommend_name` varchar(50) NOT NULL COMMENT '推荐名称',
  `status` tinyint(2) DEFAULT '0' COMMENT '0-下架 1-上架',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `brainpower_inital_supervise` ADD  `effect_id` int(11) NOT NULL;

--12.6

ALTER TABLE `engineering_standard_craft` ADD  `points_id`  int(11) NOT NULL DEFAULT '0';

update engineering_standard_craft set points_id=2 where id=1;
update engineering_standard_craft set points_id=2 where id=2;
update engineering_standard_craft set points_id=1 where id=3;
update engineering_standard_craft set points_id=1 where id=4;
update engineering_standard_craft set points_id=3 where id=5;
update engineering_standard_craft set points_id=3 where id=6;
update engineering_standard_craft set points_id=69 where id=7;
update engineering_standard_craft set points_id=4 where id=8;
update engineering_standard_craft set points_id=4 where id=9;
update engineering_standard_craft set points_id=4 where id=10;
update engineering_standard_craft set points_id=4 where id=11;
update engineering_standard_craft set points_id=4 where id=32;
update engineering_standard_craft set points_id=4 where id=33;
update engineering_standard_craft set points_id=4 where id=34;
update engineering_standard_craft set points_id=4 where id=35;
update engineering_standard_craft set points_id=4 where id=36;
update engineering_standard_craft set points_id=4 where id=37;

-- test 2017.12.6 yr
update points set title='主卧' where title='主卧室';
update points set title='次卧' where title='次卧室';

-- ac 2017.12.7 yr
update points set title='主卧' where id=65;
update points set title='次卧' where id=66;

-- all 2017.12.7 hyz

ALTER TABLE `order_platform_handle` modify column handle tinyint(1) NOT NULL comment '1:关闭订单退款 2：关闭订单线下退款 3：退货 4.换货 5：上门维修 6：上门退货 7：上门换货    8:关闭订单';

--op test 2017.12.9 yr
update engineering_standard_craft set points_id=6 where id=21;
update engineering_standard_craft set points_id=6 where id=22;
update engineering_standard_craft set points_id=6 where id=23;
update engineering_standard_craft set points_id=6 where id=24;
update engineering_standard_craft set points_id=6 where id=25;
update engineering_standard_craft set points_id=6 where id=26;
update engineering_standard_craft set points_id=6 where id=27;
update engineering_standard_craft set points_id=6 where id=28;
update engineering_standard_craft set points_id=6 where id=29;
update engineering_standard_craft set points_id=6 where id=30;
update engineering_standard_craft set points_id=6 where id=31;


--all 2017.12.9  hyz

CREATE TABLE `line_supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `district_code` int(6) NOT NULL,
  `address` varchar(100) NOT NULL COMMENT '详细地址',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：已关闭  2：已开启',
  `mobile` bigint(20) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='线下体验店商家表';


CREATE TABLE `line_supplier_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `line_supllier_id` int(11) NOT NULL COMMENT '线下体验店id',
  `goods_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:已关闭  2：已开启',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='线下体验店商品表';

--all 2017.12.11  hyz
ALTER TABLE `line_supplier_goods` CHANGE `line_supllier_id`  `line_supplier_id`  int(11) NOT NULL COMMENT '线下体验店id';


--all 2017.12.12  hyz

ALTER TABLE `user_accessdetail` ADD  `recharge_pay_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:支付宝  2.微信';

--all 2017.12.13 yr
ALTER TABLE `chat_record` ADD  `length` int(11) NOT NULL DEFAULT '0' COMMENT '语音长度';

--test 2017.12.15 yr

update points set title='强电路点位' where title='强电';
update points set title='弱电路点位' where title='弱电';
update points set title='水路点位' where title='水路';

--test 2017.12.20 yr
CREATE TABLE `effect_toponymy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `effect_id` varchar(20) NOT NULL,
  `add_time` int(11) NOT NULL,
  `province_code` int(11) DEFAULT NULL COMMENT '省编码',
  `city_code` int(11) DEFAULT NULL COMMENT '市编码',
  `district_code` int(11) DEFAULT NULL COMMENT '区编码',
  `toponymy` varchar(50) DEFAULT NULL COMMENT '小区名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--test 2017.12.22 yr
ALTER TABLE chat_record  MODIFY COLUMN `type` TINYINT(1);

--all 2017.12.26 hj
create table goods_style (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned not null default 0,
  `style_id` int(11) unsigned not null default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--all 2017.12.28 hyz
ALTER TABLE `user_accessdetail` modify column access_type tinyint(2) NOT NULL DEFAULT '0' COMMENT '1.充值 2.扣款 3.已提现 4.提现中  5.驳回 6.货款 7.使用  8.奖励金  9.工程款  10.工程退款   11.退款';

--all 2017.12.28  17:46 hyz
ALTER TABLE `user_accessdetail` ADD  `refund_bank_log_id` int(11) NOT NULL COMMENT '退款银行卡logID';
