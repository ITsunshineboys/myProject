-- 上线后，数据表的更新放在这个文件 --

ALTER TABLE engineering_standard_craft modify column id int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE decoration_particulars add effect_id int(11) DEFAULT NULL COMMENT '装修列表' after id;
