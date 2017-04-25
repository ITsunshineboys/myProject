create table roles (
    id int PRIMARY key auto_increment,
    name varchar(24) not null
) default charset = utf8;

insert into roles(id, name) values
(1, '公司后台管理员'),
(2, '工人'),
(3, '设计师'),
(4, '项目经理'),
(5, '装修公司'),
(6, '供应商'),
(7, '业主')
;