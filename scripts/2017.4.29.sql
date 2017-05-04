CREATE TABLE `project_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `worker_id` int(11) DEFAULT NULL COMMENT '工人',
  `project_manager_id` int(11) DEFAULT NULL COMMENT '项目经理',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

