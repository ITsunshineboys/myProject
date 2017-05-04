CREATE TABLE `designer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '设计师名称',
  `identity_no` varchar(19) not null DEFAULT '' COMMENT '个人身份标识',
  `decoration_company_id` int(11) unsigned not null DEFAULT 0 COMMENT '装修公司“id”',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;