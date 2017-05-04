create table role (
    id int PRIMARY key auto_increment,
    name varchar(25) not null default '',
    admin_module varchar(25) not null default '',
    detail_table varchar(25) not null default ''
) default charset = utf8;

insert into role(id, name, admin_module, detail_table) values
(1, '公司后台管理员', 'lhzz', ''),
(2, '工人', 'worker', 'worker'),
(3, '设计师', 'designer', 'designer'),
(4, '项目经理', 'manager', 'project_manager'),
(5, '装修公司', 'decoration_company','decoration_company'),
(6, '供应商', 'supplier', 'supplier'),
(7, '业主', 'owner', 'user')
;