CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned not null DEFAULT 0 COMMENT '业主“id”',
  `decoration_company_id` int(11) unsigned not null DEFAULT 0 COMMENT '装修公司“id”',
  `project_manager_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目经理“id”',
  `designer_id` int(11) unsigned not null DEFAULT 0 COMMENT '设计师“id”',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
