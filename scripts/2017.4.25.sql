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
