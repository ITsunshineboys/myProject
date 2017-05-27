CREATE TABLE `decoration_particulars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `sittingRoom_diningRoom_area` int(10) DEFAULT NULL COMMENT '客餐厅及过道地面面积',
  `masterBedroom_area` int(10) DEFAULT NULL COMMENT '主卧室地面面积',
  `secondaryBedroom_area` int(10) DEFAULT NULL COMMENT '次卧室地面面积',
  `hostToilet_area` int(10) DEFAULT NULL COMMENT '主卫生间地面面积',
  `kitchen_area` int(10) DEFAULT NULL COMMENT '厨房地面面积',
  `hallway_area` int(10) DEFAULT NULL COMMENT '玄关地面面积',
  `sittingRoom_diningRoom_perimeter` int(10) DEFAULT NULL COMMENT '客餐厅及过道周长',
  `masterBedroom_perimeter` int(10) DEFAULT NULL COMMENT '主卧室周长',
  `secondaryBedroom_perimeter` int(10) DEFAULT NULL COMMENT '次卧室周长',
  `toilet_perimeter` int(10) DEFAULT NULL COMMENT '卫生间周长',
  `kitchen_perimeter` int(10) DEFAULT NULL COMMENT '厨房周长',
  `hallway_perimeter` int(10) DEFAULT NULL COMMENT '玄关地面周长',
  `drawingRoom_balcony_area` int(10) DEFAULT NULL COMMENT '客厅阳台面积',
  `masterBedroom_balcony_area` int(10) DEFAULT NULL COMMENT '主卧阳台面积',
  `secondaryBedroom_balcony_area` int(10) DEFAULT NULL COMMENT '次卧阳台面积',
  `toilet_balcony_area` int(10) DEFAULT NULL COMMENT '卫生间阳台面积',
  `kitchen_balcony_area` int(10) DEFAULT NULL COMMENT '厨房阳台面积',
  `bedroom_aisle_area` int(10) DEFAULT NULL COMMENT '卧室过道地面面积',
  `drawingRoom_balcony_perimeter` int(10) DEFAULT NULL COMMENT '客厅阳台周长',
  `masterBedroom_balcony_perimeter` int(10) DEFAULT NULL COMMENT '主卧阳台周长',
  `secondaryBedroom_balcony_perimeter` int(10) DEFAULT NULL COMMENT '次卧阳台周长',
  `toilet_balcony_perimeter` int(10) DEFAULT NULL COMMENT '卫生间阳台周长',
  `kitchen_balcony_perimeter` int(10) DEFAULT NULL COMMENT '厨房阳台周长',
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
  `drawingRoom_sill_length` int(10) DEFAULT NULL COMMENT '客厅窗台板长度',
  `masterBedroom_sill_length` int(10) DEFAULT NULL COMMENT '主卧窗台板长度',
  `secondaryBedroom_sill_length` int(10) DEFAULT NULL COMMENT '次卧窗台板长度',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `circuitry_reconstruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL COMMENT '装修列表',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品',
  `points_id` int(11) DEFAULT NULL COMMENT '点位',
  `material` varchar(50) DEFAULT NULL COMMENT '材料',
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
