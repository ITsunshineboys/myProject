CREATE TABLE `supplier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) DEFAULT NULL COMMENT '供应商',
  `mobile` bigint(20) DEFAULT NULL COMMENT '手机号',
  `identity_no` int(255) DEFAULT NULL COMMENT '个人身份标识',
  `aite_cubo_no` int(255) DEFAULT NULL COMMENT '魔方号',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `icon` varchar(255) DEFAULT NULL COMMENT '头像',
  `create_time` int(20) DEFAULT NULL COMMENT '注册时间',
  `login_time` int(20) DEFAULT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
