/**
*智能报价基础数据
*/

-- ----------------------------
-- Records of labor_cost
-- ----------------------------
INSERT INTO `labor_cost` VALUES ('1', '四川省', '510000', '成都市', '510100', '222', '木工', '白银');
INSERT INTO `labor_cost` VALUES ('2', '四川省', '510000', '成都市', '510100', '300', '水电工', '白银');
INSERT INTO `labor_cost` VALUES ('3', '四川省', '510000', '成都市', '510100', '350', '防水工', '白银');
INSERT INTO `labor_cost` VALUES ('4', '四川省', '510000', '成都市', '510100', '500', '油漆工', '白银');
INSERT INTO `labor_cost` VALUES ('5', '四川省', '510000', '成都市', '510100', '300', '泥瓦工', '白银');
INSERT INTO `labor_cost` VALUES ('6', '四川省', '510000', '成都市', '510100', '220', '杂工', '白银');

-- ----------------------------
-- Records of worker_craft_norm
-- ----------------------------
INSERT INTO `worker_craft_norm` VALUES ('1', '1', '10', '造型长度');
INSERT INTO `worker_craft_norm` VALUES ('2', '1', '12', '平顶面积');
INSERT INTO `worker_craft_norm` VALUES ('3', '2', '5', '强电点位');
INSERT INTO `worker_craft_norm` VALUES ('4', '2', '5', '弱电点位');
INSERT INTO `worker_craft_norm` VALUES ('5', '2', '6', '水路点位');
INSERT INTO `worker_craft_norm` VALUES ('6', '3', '30', '做工面积');
INSERT INTO `worker_craft_norm` VALUES ('7', '4', '150', '乳胶漆底漆面积');
INSERT INTO `worker_craft_norm` VALUES ('8', '4', '150', '乳胶漆面漆面积');
INSERT INTO `worker_craft_norm` VALUES ('9', '4', '50', '腻子面积');
INSERT INTO `worker_craft_norm` VALUES ('10', '4', '30', '阴角线长度');
INSERT INTO `worker_craft_norm` VALUES ('11', '5', '30', '保护层长度');
INSERT INTO `worker_craft_norm` VALUES ('12', '5', '10', '贴地砖面积');
INSERT INTO `worker_craft_norm` VALUES ('13', '5', '8', '贴墙砖面积');
INSERT INTO `worker_craft_norm` VALUES ('14', '6', '8', '新建24墙面积');
INSERT INTO `worker_craft_norm` VALUES ('15', '6', '10', '新建12墙面积');
INSERT INTO `worker_craft_norm` VALUES ('16', '6', '30', '拆除24墙面积');
INSERT INTO `worker_craft_norm` VALUES ('17', '6', '40', '拆除12墙面积');
INSERT INTO `worker_craft_norm` VALUES ('18', '6', '15', '补烂长度');

-- ----------------------------
-- Records of engineering_standard_craft
-- ----------------------------
INSERT INTO `engineering_standard_craft` VALUES ('1', '510100', '弱电', '5.00', '网线', 'M');
INSERT INTO `engineering_standard_craft` VALUES ('2', '510100', '弱电', '10.00', '线管', 'M');
INSERT INTO `engineering_standard_craft` VALUES ('3', '510100', '强电', '10.00', '电线', 'M');
INSERT INTO `engineering_standard_craft` VALUES ('4', '510100', '强电', '10.00', '线管', 'M');
INSERT INTO `engineering_standard_craft` VALUES ('5', '510100', '水路', '2.00', 'PPR水管', 'M');
INSERT INTO `engineering_standard_craft` VALUES ('6', '510100', '水路', '2.00', 'PVC管', 'M');
INSERT INTO `engineering_standard_craft` VALUES ('7', '510100', '防水', '1.25', '防水涂剂', 'KG');
INSERT INTO `engineering_standard_craft` VALUES ('9', '510100', '木作', '1.50', '龙骨', '跟');
INSERT INTO `engineering_standard_craft` VALUES ('10', '510100', '木作', '2.00', '丝杆', '根');
INSERT INTO `engineering_standard_craft` VALUES ('11', '510100', '木作', '2.50', '造型长度石膏板', '张');
INSERT INTO `engineering_standard_craft` VALUES ('12', '510100', '乳胶漆', '0.33', '腻子', 'KG');
INSERT INTO `engineering_standard_craft` VALUES ('13', '510100', '乳胶漆', '0.08', '乳胶漆底漆', 'L');
INSERT INTO `engineering_standard_craft` VALUES ('14', '510100', '乳胶漆', '0.08', '乳胶漆面漆', 'L');
INSERT INTO `engineering_standard_craft` VALUES ('15', '510100', '乳胶漆', '1.20', '阴角线', 'M');
INSERT INTO `engineering_standard_craft` VALUES ('16', '510100', '乳胶漆', '3.00', '石膏粉', '元');
INSERT INTO `engineering_standard_craft` VALUES ('17', '510100', '泥工', '2.40', '贴砖', '高');
INSERT INTO `engineering_standard_craft` VALUES ('18', '510100', '泥工', '15.00', '水泥', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('19', '510100', '泥工', '3.00', '自流平', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('20', '510100', '泥工', '3.00', '河沙', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('21', '510100', '杂工', '20.00', '清运12墙', 'M2');
INSERT INTO `engineering_standard_craft` VALUES ('22', '510100', '杂工', '40.00', '清运24墙', 'M2');
INSERT INTO `engineering_standard_craft` VALUES ('23', '510100', '杂工', '20.00', '运渣车12墙面积', 'M2');
INSERT INTO `engineering_standard_craft` VALUES ('24', '510100', '杂工', '10.00', '运渣车24墙面积', 'M2');
INSERT INTO `engineering_standard_craft` VALUES ('25', '510100', '杂工', '300.00', '运渣车费用', '车');
INSERT INTO `engineering_standard_craft` VALUES ('26', '510100', '杂工', '10.00', '12墙水泥用量', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('27', '510100', '杂工', '15.00', '24墙水泥用量', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('28', '510100', '杂工', '2.00', '补烂水泥用量', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('29', '510100', '杂工', '3.00', '12墙河沙用量', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('30', '510100', '杂工', '3.00', '24墙河沙用量', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('31', '510100', '杂工', '2.00', '补烂河沙用量', 'kg');
INSERT INTO `engineering_standard_craft` VALUES ('32', '510100', '木作', '2.50', '平顶面积石膏板', '张');

-- ----------------------------
-- Records of series
-- ----------------------------
INSERT INTO `series` VALUES ('1', '齐家', '适合4-8年收入，对质量与价格实惠着重，同时关注健康的人群', '完善,健康,标准', '1.0', '1.0', '1.0', '1.0', '0', '1', '1499052178', '1');
INSERT INTO `series` VALUES ('2', '享家', '适合6-10年收入，对质量与生活品质有细致的追求', '品牌,小资,奢享', '1.2', '1.0', '1.0', '1.2', '0', '1', '1499052178', '2');
INSERT INTO `series` VALUES ('3', '享家+', '适合7-12年收入,对质量与生活品质有更加细致的追求', '品牌,小资,奢享', '1.2', '1.2', '1.0', '1.2', '0', '1', '1499052178', '3');
INSERT INTO `series` VALUES ('4', '智家', '高收入人群追求智能科技生活', '品牌,小资,智能化', '1.2', '1.2', '1.0', '1.2', '0', '1', '1499052178', '4');
INSERT INTO `series` VALUES ('5', '智家+', '高收入人群追求智能科技美好生活', '品牌,小资,智能化', '1.2', '1.2', '1.0', '1.2', '0', '1', '1499052178', '5');

-- ----------------------------
-- Records of style
-- ----------------------------
INSERT INTO `style` VALUES ('1', '现代简约', '现代简约风格是以简约为主的装修风格。', '简约实用,层次感强,舒适美观', '1.0', '1.0', '1.0', '1.0', null, '1', null, 'uploads/2017/08/28/1503909820.png,uploads/2017/08/28/1503911481.png,uploads/2017/08/28/1503914322.png');
INSERT INTO `style` VALUES ('2', '中国风', '中国风格是以简约为主的装修风格。', '格调高雅,古色古香,庄重贵气', '1.0', '1.3', '1.0', '1.1', null, '1', null, 'uploads/2017/08/28/1503908941.png,uploads/2017/08/28/1503914182.png,uploads/2017/08/28/1503914402.png');
INSERT INTO `style` VALUES ('3', '美式田园', '推崇回归自然、结合自然的风格，将自然、乡土风味整合成新的空间', '乡村风格,田园风格,地方风格', '1.0', '1.3', '1.0', '1.1', null, '1', null, 'uploads/2017/08/28/1503912490.png,uploads/2017/08/28/1503906826.png,uploads/2017/08/28/1503911254.png');
INSERT INTO `style` VALUES ('4', '欧式', '以古典柱式为中心的风格。欧式的居室有的不只是豪华大气，更多的是惬意的浪漫', '豪华大气,惬意,浪漫', '1.0', '1.3', '1.0', '1.1', null, '1', '1499066027', 'uploads/2017/08/28/1503908814.png,uploads/2017/08/28/1503910502.png,uploads/2017/08/28/1503908199.png');
INSERT INTO `style` VALUES ('5', '日式', '日式风格又称和风、和式风格，是来源于日本的装修和装饰风格，是东方风格中独树一帜的代表', '淡雅节制,深邃禅意', '1.0', '1.3', '1.0', '1.1', null, '1', null, 'uploads/2017/08/28/1503910234.png,uploads/2017/08/28/1503915188.png,uploads/2017/08/28/1503907109.png');

-- ----------------------------
-- Records of engineering_standard_carpentry_coefficient
-- ----------------------------
INSERT INTO `engineering_standard_carpentry_coefficient` VALUES ('1', '齐家', '5.00', '1', '0');
INSERT INTO `engineering_standard_carpentry_coefficient` VALUES ('2', '齐家', '1.50', '2', '0');
INSERT INTO `engineering_standard_carpentry_coefficient` VALUES ('3', '齐家', '1.50', '3', '0');
INSERT INTO `engineering_standard_carpentry_coefficient` VALUES ('4', '现代简约', '1.00', '1', '1');
INSERT INTO `engineering_standard_carpentry_coefficient` VALUES ('5', '现代简约', '1.10', '2', '1');

-- ----------------------------
-- Records of engineering_standard_carpentry_craft
-- ----------------------------
INSERT INTO `engineering_standard_carpentry_craft` VALUES ('1', '龙骨', '2.50');
INSERT INTO `engineering_standard_carpentry_craft` VALUES ('2', '丝杆', '2.50');
INSERT INTO `engineering_standard_carpentry_craft` VALUES ('3', '石膏板', '2.95');

-- ----------------------------
-- Records of coefficient_management
-- ----------------------------
INSERT INTO `coefficient_management` VALUES ('1', '辅材', '0.70');
INSERT INTO `coefficient_management` VALUES ('2', '主要材料', '0.70');
INSERT INTO `coefficient_management` VALUES ('3', '固定家具', '0.70');
INSERT INTO `coefficient_management` VALUES ('4', '移动家具', '0.70');
INSERT INTO `coefficient_management` VALUES ('5', '家电配套', '0.70');
INSERT INTO `coefficient_management` VALUES ('6', '软装配套', '0.70');
INSERT INTO `coefficient_management` VALUES ('7', '智能配套', '0.70');
INSERT INTO `coefficient_management` VALUES ('8', '生活配套', '0.70');

-- ----------------------------
-- Records of stairs_details
-- ----------------------------
INSERT INTO `stairs_details` VALUES ('1', '实木构造');
INSERT INTO `stairs_details` VALUES ('2', '钢木构造');
INSERT INTO `stairs_details` VALUES ('3', '纯木构造');
INSERT INTO `stairs_details` VALUES ('4', '纯刚构造');
INSERT INTO `stairs_details` VALUES ('5', '玻璃金属');

-- ----------------------------
-- Records of assort_goods
-- ----------------------------
INSERT INTO `assort_goods` VALUES ('68', '背景音乐系统', '136', '134', '133,134,136,', '0', null);
INSERT INTO `assort_goods` VALUES ('67', '智能配电箱', '135', '134', '133,134,135,', '0', null);
INSERT INTO `assort_goods` VALUES ('66', '灯具', '129', '127', '126,127,129,', '0', null);
INSERT INTO `assort_goods` VALUES ('65', '窗帘', '128', '127', '126,127,128,', '0', null);
INSERT INTO `assort_goods` VALUES ('64', '热水器', '118', '115', '114,115,118,', '0', null);
INSERT INTO `assort_goods` VALUES ('63', '油烟机', '116', '115', '114,115,116,', '0', null);
INSERT INTO `assort_goods` VALUES ('62', '沙发', '107', '106', '102,106,107,', '0', null);
INSERT INTO `assort_goods` VALUES ('61', '实木', '104', '103', '102,103,104,', '0', null);
INSERT INTO `assort_goods` VALUES ('60', '龙骨', '10', '9', '1,9,10,', '0', null);
INSERT INTO `assort_goods` VALUES ('59', '空心砖', '8', '7', '1,7,8,', '0', null);
INSERT INTO `assort_goods` VALUES ('58', '木地板', '45', '44', '43,44,45,', '0', null);
INSERT INTO `assort_goods` VALUES ('57', '自流平', '36', '47', '43,47,36,', '0', null);
INSERT INTO `assort_goods` VALUES ('56', '木门', '95', '94', '93,94,95,', '0', null);
INSERT INTO `assort_goods` VALUES ('55', '楼梯', '96', '94', '93,94,96,', '0', null);
INSERT INTO `assort_goods` VALUES ('69', '水槽', '146', '145', '144,145,146,', '0', null);
INSERT INTO `assort_goods` VALUES ('70', '纸巾盒', '158', '157', '144,157,158,', '0', null);
INSERT INTO `assort_goods` VALUES ('71', '欧松板', '11', '9', '1,9,11,', '0', null);

-- ----------------------------
-- Records of decoration_message
-- ----------------------------
INSERT INTO `decoration_message` VALUES ('1', '1', '20', null, '1', null, null);
INSERT INTO `decoration_message` VALUES ('2', '2', '12', '1', null, null, null);
INSERT INTO `decoration_message` VALUES ('3', '3', '12', null, null, '60', '69');
INSERT INTO `decoration_message` VALUES ('4', '4', '12', '1', null, null, null);

-- ----------------------------
-- Records of points
-- ----------------------------
INSERT INTO `points` VALUES ('7', '杂工', '0', null, '1', '0');
INSERT INTO `points` VALUES ('6', '泥作', '0', null, '1', '0');
INSERT INTO `points` VALUES ('5', '油漆', '0', null, '1', '0');
INSERT INTO `points` VALUES ('4', '木作', '0', null, '1', '0');
INSERT INTO `points` VALUES ('3', '水路', '0', '3', '1', '0');
INSERT INTO `points` VALUES ('2', '弱电', '0', '3', '1', '0');
INSERT INTO `points` VALUES ('1', '强电', '0', '51', '1', '0');
INSERT INTO `points` VALUES ('8', '入户', '1', '3', '2', '0');
INSERT INTO `points` VALUES ('9', '餐厅', '1', '2', '2', '0');
INSERT INTO `points` VALUES ('10', '客厅', '1', '12', '2', '0');
INSERT INTO `points` VALUES ('11', '客厅阳台', '1', '4', '2', '0');
INSERT INTO `points` VALUES ('12', '客厅卧室过道', '1', '1', '2', '0');
INSERT INTO `points` VALUES ('13', '厨房', '1', '6', '2', '0');
INSERT INTO `points` VALUES ('14', '生活阳台', '1', '3', '2', '0');
INSERT INTO `points` VALUES ('15', '卫生间', '1', '4', '2', '0');
INSERT INTO `points` VALUES ('16', '主卧室', '1', '7', '2', '0');
INSERT INTO `points` VALUES ('17', '次卧室', '1', '7', '2', '0');
INSERT INTO `points` VALUES ('18', '入户筒灯单开', '8', '1', '3', '0');
INSERT INTO `points` VALUES ('19', '入户过道双控单开', '8', '1', '3', '0');
INSERT INTO `points` VALUES ('20', '入户鞋柜插座', '8', '1', '3', '0');
INSERT INTO `points` VALUES ('21', '餐厅双开', '9', '1', '3', '0');
INSERT INTO `points` VALUES ('22', '餐厅插座', '9', '1', '3', '0');
INSERT INTO `points` VALUES ('23', '双控双开', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('24', '双控双开', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('25', '四开', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('26', '沙发背景墙插座', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('27', '沙发背景墙插座右', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('28', '光纤插座', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('29', '机顶盒插座', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('30', '网络插座', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('31', '备用插座', '10', '3', '3', '0');
INSERT INTO `points` VALUES ('32', '16A空调', '10', '1', '3', '0');
INSERT INTO `points` VALUES ('34', '写字台插座', '11', '1', '3', '0');
INSERT INTO `points` VALUES ('35', '备用插座', '11', '1', '3', '0');
INSERT INTO `points` VALUES ('36', '网络插座', '11', '1', '3', '0');
INSERT INTO `points` VALUES ('37', '双控单开', '12', '1', '3', '0');
INSERT INTO `points` VALUES ('38', '冰箱插座', '13', '1', '3', '0');
INSERT INTO `points` VALUES ('39', '电饭煲插座', '13', '1', '3', '0');
INSERT INTO `points` VALUES ('40', '油烟机插座', '13', '1', '3', '0');
INSERT INTO `points` VALUES ('41', '微波炉', '13', '1', '3', '0');
INSERT INTO `points` VALUES ('42', '备用插座', '13', '1', '3', '0');
INSERT INTO `points` VALUES ('43', '厨房单开', '13', '1', '3', '0');
INSERT INTO `points` VALUES ('44', '洗衣机插座', '14', '1', '3', '0');
INSERT INTO `points` VALUES ('45', '单开', '14', '1', '3', '0');
INSERT INTO `points` VALUES ('46', '热水器插座', '14', '1', '3', '0');
INSERT INTO `points` VALUES ('47', '浴霸开关', '15', '1', '3', '0');
INSERT INTO `points` VALUES ('48', '双开', '15', '1', '3', '0');
INSERT INTO `points` VALUES ('49', '吹风插座', '15', '1', '3', '0');
INSERT INTO `points` VALUES ('50', '马桶防水插座', '15', '1', '3', '0');
INSERT INTO `points` VALUES ('51', '卧室过道双控单开', '16', '1', '3', '0');
INSERT INTO `points` VALUES ('52', '床头双控单开', '16', '1', '3', '0');
INSERT INTO `points` VALUES ('53', '主灯双开', '16', '1', '3', '0');
INSERT INTO `points` VALUES ('54', '床头插座左', '16', '1', '3', '0');
INSERT INTO `points` VALUES ('55', '床头插座右', '16', '1', '3', '0');
INSERT INTO `points` VALUES ('56', '备用插座', '16', '1', '3', '0');
INSERT INTO `points` VALUES ('57', '空调', '16', '1', '3', '0');
INSERT INTO `points` VALUES ('58', '双控单开过道', '17', '1', '3', '0');
INSERT INTO `points` VALUES ('59', '双控单开主灯', '17', '1', '3', '0');
INSERT INTO `points` VALUES ('60', '主灯双开', '17', '1', '3', '0');
INSERT INTO `points` VALUES ('61', '床头插座左', '17', '1', '3', '0');
INSERT INTO `points` VALUES ('62', '床头插座右', '17', '1', '3', '0');
INSERT INTO `points` VALUES ('63', '空调', '17', '1', '3', '0');
INSERT INTO `points` VALUES ('64', '备用插座', '17', '1', '3', '0');
INSERT INTO `points` VALUES ('65', '主卧', '2', '1', '2', '0');
INSERT INTO `points` VALUES ('66', '次卧', '2', '1', '2', '0');
INSERT INTO `points` VALUES ('67', '客厅', '2', '1', '2', '0');
INSERT INTO `points` VALUES ('68', '面积比例', '0', null, '1', '0');
INSERT INTO `points` VALUES ('70', '户型面积', '0', null, '1', '0');
INSERT INTO `points` VALUES ('69', '防水', '0', null, '1', '0');
INSERT INTO `points` VALUES ('71', '书桌', '65', '1', '3', '0');
INSERT INTO `points` VALUES ('72', '电视', '67', '1', '3', '0');
INSERT INTO `points` VALUES ('73', '书桌', '66', '1', '3', '0');
INSERT INTO `points` VALUES ('74', '卫生间', '3', '2', '2', '0');
INSERT INTO `points` VALUES ('75', '厨房', '3', '1', '2', '0');
INSERT INTO `points` VALUES ('76', '马桶', '74', '1', '3', '0');
INSERT INTO `points` VALUES ('77', '洗漱池', '74', '1', '3', '0');
INSERT INTO `points` VALUES ('78', '洗菜池', '75', '1', '3', '0');