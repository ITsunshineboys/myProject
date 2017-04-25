CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) DEFAULT '',
  `password` varchar(100),
  `authKey` varchar(25) DEFAULT '',
  `accessToken` varchar(255) DEFAULT '',
  `mobile` bigint,
  `nickname` varchar(20) DEFAULT '',
  `identity_no` varchar(18) DEFAULT '',
  `aite_cube_no` bigint,
  `icon` varchar(100) DEFAULT '',
  `create_time` int,
  `login_time` int,
  `login_role_id` int,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;