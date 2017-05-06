CREATE TABLE `user_residence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province` char(20) DEFAULT NULL COMMENT '省份',
  `city` char(20) DEFAULT NULL COMMENT '市',
  `district` char(20) DEFAULT NULL COMMENT '区',
  `street` varchar(100) DEFAULT NULL COMMENT '街道',
  `toponymy` char(50) DEFAULT NULL COMMENT '小区名称',
  `area` smallint(20) DEFAULT NULL COMMENT '面积',
  `high` smallint(20) DEFAULT NULL COMMENT '层高',
  `room` smallint(5) DEFAULT NULL COMMENT '室',
  `hall` smallint(5) DEFAULT NULL COMMENT '厅',
  `toilet` smallint(5) DEFAULT NULL COMMENT '卫',
  `kitchen` smallint(5) DEFAULT NULL COMMENT '厨',
  `window` smallint(5) DEFAULT NULL COMMENT '飘窗',
  `balcony` smallint(5) DEFAULT NULL COMMENT '阳台',
  `user_id` int(50) DEFAULT NULL COMMENT '业主id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `series` char(50) DEFAULT NULL COMMENT '系列',
  `intro` varchar(255) DEFAULT NULL COMMENT '系列介绍',
  `theme` char(50) DEFAULT NULL COMMENT '系列主题',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `style` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style` char(50) DEFAULT NULL COMMENT '风格',
  `intro` varchar(255) DEFAULT NULL COMMENT '风格介绍',
  `theme` char(50) DEFAULT NULL COMMENT '风格主题',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `style_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(11) DEFAULT NULL COMMENT '风格表id',
  `picture` varchar(255) DEFAULT NULL COMMENT '图片',
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

CREATE TABLE `effect_ picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `effect_id` int(11) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL COMMENT '图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

