CREATE TABLE `project_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目ID',
  `worker_id` int(11) unsigned not null DEFAULT 0 COMMENT '工人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

