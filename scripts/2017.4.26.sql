CREATE TABLE `decoration_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '装修公司名称',
  `identity_no` varchar(19) NOT NULL default '' COMMENT '个人身份标识',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
