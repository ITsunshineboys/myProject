CREATE TABLE `user_conrtol` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(255) unsigned DEFAULT NULL COMMENT '业主“id”',
  `decoration_company_id` int(255) unsigned DEFAULT NULL COMMENT '装修公司“id”',
  `project_manager_id` int(255) unsigned DEFAULT NULL COMMENT '项目经理“id”',
  `stylist_id` int(255) unsigned DEFAULT NULL COMMENT '设计师“id”',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
