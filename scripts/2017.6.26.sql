CREATE TABLE `decoration_add` (
  `id` int(11) NOT NULL,
  `series_id` int(11) DEFAULT NULL COMMENT '系列',
  `style_id` int(11) DEFAULT NULL COMMENT '风格',
  `project` varchar(50) DEFAULT NULL COMMENT '项目名称',
  `min_area` int(10) unsigned zerofill DEFAULT '0000000000' COMMENT '最小面积',
  `max_area` int(10) unsigned zerofill DEFAULT '0000000000' COMMENT '最大面积',
  `material` varchar(20) DEFAULT NULL COMMENT '材料',
  `price` bigint(20) DEFAULT NULL COMMENT '价格',
  `sku` int(10) DEFAULT NULL COMMENT '商品编码',
  `supplier_price` bigint(20) DEFAULT NULL COMMENT '平台价格',
  `district_code` int(10) DEFAULT NULL COMMENT '城市编码',
  `district` varchar(20) DEFAULT NULL COMMENT '城市',
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