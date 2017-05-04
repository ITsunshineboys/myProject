CREATE TABLE `worker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '工人姓名',
  `identity_no` int(11) unsigned not null DEFAULT 0 COMMENT '个人身份标识',
  `project_manager_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目经理“id”',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

