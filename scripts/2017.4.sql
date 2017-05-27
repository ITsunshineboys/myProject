create table role (
    id int PRIMARY key auto_increment,
    name varchar(25) not null default '',
    admin_module varchar(25) not null default '',
    detail_table varchar(25) not null default ''
) default charset = utf8;

insert into role(id, name, admin_module, detail_table) values
(1, '公司后台管理员', 'lhzz', 'lhzz'),
(2, '工人', 'worker', 'worker'),
(3, '设计师', 'designer', 'designer'),
(4, '项目经理', 'manager', 'project_manager'),
(5, '装修公司', 'decoration_company','decoration_company'),
(6, '供应商', 'supplier', 'supplier'),
(7, '业主', 'owner', 'user')
;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) DEFAULT '',
  `password` varchar(100) DEFAULT '',
  `authKey` varchar(25) DEFAULT '',
  `accessToken` varchar(255) DEFAULT '',
  `mobile` bigint DEFAULT 0,
  `nickname` varchar(20) DEFAULT '',
  `identity_no` varchar(18) DEFAULT '',
  `aite_cube_no` bigint DEFAULT 0,
  `icon` varchar(100) DEFAULT '',
  `create_time` int DEFAULT 0,
  `login_time` int DEFAULT 0,
  `login_role_id` int DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table user_role (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `role_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `decoration_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '昵称',
  `identity_no` varchar(19) NOT NULL default '' COMMENT '个人身份标识',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `name` varchar(50) not null DEFAULT '' COMMENT '装修公司名称',
  `licence` varchar(50) not null DEFAULT '' COMMENT '营业执照号',
  `licence_image` varchar(255) not null DEFAULT '营业执照图片',
  `legal_person` varchar(50) not null DEFAULT '' comment '法人',
  `identity_card_front_image` varchar(255) not null DEFAULT '' comment '身份证正面图片',
  `identity_card_back_image` varchar(255) not null DEFAULT '' comment '身份证反面图片',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `designer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '设计师名称',
  `identity_no` varchar(19) not null DEFAULT '' COMMENT '个人身份标识',
  `decoration_company_id` int(11) unsigned not null DEFAULT 0 COMMENT '装修公司“id”',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `name` varchar(50) not null DEFAULT '' COMMENT '名称',
  `identity_card_front_image` varchar(255) not null DEFAULT '' comment '身份证正面图片',
  `identity_card_back_image` varchar(255) not null DEFAULT '' comment '身份证反面图片',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `project_manager` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '项目经理名称',
  `identity_no` varchar(19) not null DEFAULT '' COMMENT '个人身份标识',
  `decoration_company_id` int(11) unsigned not null DEFAULT 0 COMMENT '装修公司“id”',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `name` varchar(50) not null DEFAULT '' COMMENT '名称',
  `identity_card_front_image` varchar(255) not null DEFAULT '' comment '身份证正面图片',
  `identity_card_back_image` varchar(255) not null DEFAULT '' comment '身份证反面图片',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `project_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目ID',
  `worker_id` int(11) unsigned not null DEFAULT 0 COMMENT '工人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `supplier` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '供应商',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像',
  `name` varchar(50) not null DEFAULT '' COMMENT '公司名称',
  `licence` varchar(50) not null DEFAULT '' COMMENT '营业执照号',
  `licence_image` varchar(255) not null DEFAULT '营业执照图片',
  `legal_person` varchar(50) not null DEFAULT '' comment '法人',
  `identity_card_front_image` varchar(255) not null DEFAULT '' comment '身份证正面图片',
  `identity_card_back_image` varchar(255) not null DEFAULT '' comment '身份证反面图片',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned not null DEFAULT 0 COMMENT '业主“id”',
  `decoration_company_id` int(11) unsigned not null DEFAULT 0 COMMENT '装修公司“id”',
  `project_manager_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目经理“id”',
  `designer_id` int(11) unsigned not null DEFAULT 0 COMMENT '设计师“id”',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `worker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(25) not null DEFAULT '' COMMENT '工人姓名',
  `identity_no` int(11) unsigned not null DEFAULT 0 COMMENT '个人身份标识',
  `project_manager_id` int(11) unsigned not null DEFAULT 0 COMMENT '项目经理“id”',
  `work_type_id` int(11) unsigned not null DEFAULT 0 COMMENT '工种类型“id”',
  `icon` varchar(255) not null DEFAULT '' COMMENT '头像地址',
  `name` varchar(50) not null DEFAULT '' COMMENT '名称',
  `identity_card_front_image` varchar(255) not null DEFAULT '' comment '身份证正面图片',
  `identity_card_back_image` varchar(255) not null DEFAULT '' comment '身份证反面图片',
  `approve_reason` varchar(100) not null DEFAULT '' comment '同意原因',
  `reject_reason` varchar(100) not null DEFAULT '' comment '拒绝原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table work_type (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lhzz` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned not null default 0,
  `nickname` varchar(25) not null DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;