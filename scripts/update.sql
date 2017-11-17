-- 上线后，数据表的更新放在这个文件 --

CREATE TABLE `engineering_standard_craft` (
  `id` int(11) NOT NULL,
  `district_code` int(10) DEFAULT NULL COMMENT '城市编码',
  `project` varchar(20) DEFAULT NULL COMMENT '项目名称',
  `material` float(10,2) DEFAULT NULL COMMENT '用料',
  `project_details` varchar(20) DEFAULT NULL COMMENT '项目详情',
  `units` varchar(10) DEFAULT NULL COMMENT '单价',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
