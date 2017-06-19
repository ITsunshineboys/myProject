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
  `modelling_length_coefficient` float(10,1) DEFAULT NULL COMMENT '造型长度系数',
  `modelling_day_coefficient` float(10,1) DEFAULT NULL COMMENT '造型天数系数',
  `flat_area_coefficient` float(10,1) DEFAULT NULL COMMENT '平顶面积系数',
  `flat_day_coefficient` float(10,1) DEFAULT NULL COMMENT '平顶天数系数',
  `category_id` int(11) DEFAULT NULL default 0 COMMENT '分类id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

CREATE TABLE `series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `series` varchar(50) DEFAULT '' COMMENT '系列',
  `intro` varchar(255) DEFAULT '' COMMENT '系列介绍',
  `theme` varchar(50) DEFAULT '' COMMENT '系列主题',
  `modelling_length_coefficient` float(10,1) DEFAULT NULL COMMENT '造型长度系数',
  `modelling_day_coefficient` float(10,1) DEFAULT NULL COMMENT '造型天数系数',
  `flat_area_coefficient` float(10,1) DEFAULT NULL COMMENT '平顶面积系数',
  `flat_day_coefficient` float(10,1) DEFAULT NULL COMMENT '平顶天数系数',
  `category_id` int(11) DEFAULT NULL default 0 COMMENT '分类id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

CREATE TABLE `effect_ picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL,
  `picture` varchar(255) DEFAULT '' COMMENT '图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `effect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `series_id` int(11) DEFAULT NULL COMMENT '系列id',
  `style_id` int(11) DEFAULT NULL COMMENT '风格id',
  `room` int(5) DEFAULT NULL COMMENT '室',
  `hall` int(5) DEFAULT NULL COMMENT '厅',
  `toilet` int(5) DEFAULT NULL COMMENT '卫生间',
  `kitchen` int(5) DEFAULT NULL COMMENT '厨房',
  `window` int(5) DEFAULT NULL COMMENT '飘窗',
  `area` int(5) DEFAULT NULL COMMENT '面积',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `labor_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province` varchar(20) DEFAULT NULL COMMENT '省份',
  `city` varchar(20) DEFAULT NULL COMMENT '市',
  `district` varchar(20) DEFAULT NULL COMMENT '区县',
  `univalence` bigint(10) NOT NULL COMMENT '工人单价',
  `worker_kind` varchar(20) DEFAULT NULL COMMENT '工人种类',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `main_ materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fixation_furniture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `move_furniture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `appliances_assort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `soft_outfit_assort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `intelligence_assort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `life_assort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `basis_decoration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL COMMENT 'l类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `basis_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `basis_decoration_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `decoration_list_id` int(11) DEFAULT NULL,
  `place` varchar(20) DEFAULT NULL COMMENT '位置',
  `weak_current_points` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `points_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place_id` int(11) DEFAULT NULL,
  `details` varchar(50) DEFAULT NULL COMMENT '简介详情',
  `points_quantity` int(10) unsigned DEFAULT NULL COMMENT '点位数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


