CREATE TABLE `decoration_company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` char(25) DEFAULT NULL COMMENT '装修公司名称',
  `identity_no` int(255) unsigned NOT NULL COMMENT '个人身份标识',
  `mobile` bigint(20) unsigned NOT NULL COMMENT '手机号',
  `aite_cube_no` varchar(255) DEFAULT NULL COMMENT '魔方号',
  `password` char(50) DEFAULT NULL COMMENT '密码',
  `icon` varchar(255) DEFAULT NULL COMMENT '头像地址',
  `create_time` int(20) DEFAULT NULL COMMENT '注册时间',
  `login_time` int(20) DEFAULT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
