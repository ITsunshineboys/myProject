create table roles (
    id int PRIMARY key auto_increment,
    name varchar(24) not null
) default charset = utf8;

insert into roles(id, name) values
(1, 'system manager'),
(2, 'worker'),
(3, 'designer'),
(4, 'project manager'),
(5, 'decoration company'),
(6, 'supplier'),
(7, 'owner')
;