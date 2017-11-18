-- 上线后，数据表的更新放在这个文件 --

ALTER TABLE engineering_standard_craft modify column id int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE project_view CHANGE project_vule project_value int(11) NOT NULL COMMENT '项目值';


CREATE TABLE `decoration_particulars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL COMMENT '装修列表',
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