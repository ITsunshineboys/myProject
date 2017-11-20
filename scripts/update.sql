-- 上线后，数据表的更新放在这个文件 --

ALTER TABLE engineering_standard_craft modify column id int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE decoration_particulars add effect_id int(11) DEFAULT NULL COMMENT '装修列表' after id;


ALTER TABLE effect_earnest ADD uid INT (11) DEFAULT '0' COMMENT '用户id' AFTER id;
ALTER TABLE effect_earnest ADD type TINYINT (1) DEFAULT '0' COMMENT '类型 0:申请方案 1:保存方案' AFTER status;
ALTER TABLE effect_earnest ADD item TINYINT (1) DEFAULT '0' COMMENT '0:H5 1:App' AFTER type;