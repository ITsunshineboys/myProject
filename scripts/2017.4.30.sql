CREATE TABLE `supplier` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '供应商',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
