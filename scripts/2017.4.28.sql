CREATE TABLE `project_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` char(50) DEFAULT NULL COMMENT '项目经理名称',
  `identity_no` int(255) NOT NULL COMMENT '个人身份标识',
  `mobile` bigint(20) unsigned NOT NULL COMMENT '手机号码',
  `aite_cube_no` int(255) unsigned NOT NULL COMMENT '魔方号',
  `password` varchar(50) DEFAULT NULL COMMENT '登录密码',
  `decoration_company_id` int(255) DEFAULT NULL COMMENT '装修公司“id”',
  `icon` varchar(255) DEFAULT NULL COMMENT '头像地址',
  `create_time` int(20) DEFAULT NULL COMMENT '注册时间',
  `login_time` int(20) DEFAULT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

