-- MySQL dump 10.13  Distrib 5.7.17, for linux-glibc2.5 (x86_64)
--
-- Host: localhost    Database: yii2basic
-- ------------------------------------------------------
-- Server version	5.7.17

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(2) NOT NULL DEFAULT '0' COMMENT '角色id',
  `action` varchar(56) NOT NULL DEFAULT '' COMMENT '访问action',
  `controller` varchar(56) NOT NULL DEFAULT '' COMMENT '访问controller',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission` 
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES (1,1,'time-types','site'),(2,2,'time-types','site'),(3,3,'time-types','site'),(4,4,'time-types','site'),(5,5,'time-types','site'),(6,6,'time-types','site'),(7,7,'time-types','site'),(8,1,'recommend-admin-index','mall'),(9,1,'recommend-disable-batch','mall'),(10,1,'recommend-delete-batch','mall'),(11,1,'recommend-delete','mall'),(12,1,'recommend-status-toggle','mall'),(13,1,'recommend-history','mall'),(14,1,'recommend-second-admin','mall'),(15,1,'recommend-by-sku','mall'),(16,1,'recommend-add','mall'),(17,1,'recommend-edit','mall'),(18,1,'recommend-sort','mall'),(19,6,'recommend-add-supplier','mall'),(20,6,'recommend-edit-supplier','mall'),(21,6,'recommend-delete-supplier','mall'),(22,6,'recommend-delete-batch-supplier','mall'),(23,6,'recommend-admin-index-supplier','mall'),(24,1,'category-review','mall'),(25,1,'categories-admin','mall'),(26,6,'categories-admin','mall'),(27,1,'category-status-toggle','mall'),(28,1,'category-disable-batch','mall'),(29,1,'category-enable-batch','mall'),(30,1,'category-list-admin','mall'),(31,6,'category-list-admin','mall'),(32,1,'categories-manage-admin','mall'),(33,6,'categories-manage-admin','mall'),(34,1,'category-add','mall'),(35,6,'category-add','mall'),(36,1,'category-edit','mall'),(37,1,'category-offline-reason-reset','mall'),(38,1,'category-reason-reset','mall'),(39,1,'category-review-list','mall'),(40,6,'category-attrs','mall'),(41,1,'brand-add','mall'),(42,6,'brand-add','mall'),(43,1,'brand-review','mall'),(44,1,'brand-edit','mall'),(45,1,'brand-offline-reason-reset','mall'),(46,1,'brand-reason-reset','mall'),(47,1,'brand-status-toggle','mall'),(48,1,'brand-disable-batch','mall'),(49,1,'brand-enable-batch','mall'),(50,1,'brand-review-list','mall'),(51,1,'brand-list-admin','mall'),(52,6,'brand-list-admin','mall'),(53,6,'brand-application-add','mall'),(54,1,'brand-application-list-admin','mall'),(55,6,'brand-application-list-admin','mall'),(56,1,'brand-application-review-list','mall'),(57,1,'brand-application-review-note-reset','mall'),(58,1,'brand-application-review','mall'),(59,6,'logistics-template-add','mall'),(60,6,'logistics-template-edit','mall'),(61,1,'logistics-template-view','mall'),(62,6,'logistics-template-view','mall'),(63,6,'logistics-templates-supplier','mall'),(64,6,'logistics-template-status-toggle','mall'),(65,1,'goods-attr-add','mall'),(66,1,'goods-attr-list-admin','mall'),(67,6,'goods-add','mall'),(68,1,'goods-edit','mall'),(69,6,'goods-edit','mall'),(70,1,'goods-edit-lhzz','mall'),(71,6,'goods-attrs-admin','mall'),(72,1,'goods-status-toggle','mall'),(73,6,'goods-status-toggle','mall'),(74,1,'goods-disable-batch','mall'),(75,6,'goods-disable-batch','mall'),(76,6,'goods-delete-batch','mall'),(77,1,'goods-enable-batch','mall'),(78,1,'goods-offline-reason-reset','mall'),(79,1,'goods-reason-reset','mall'),(80,1,'goods-list-admin','mall'),(81,6,'goods-list-admin','mall'),(82,6,'goods-inventory-reset','mall'),(83,1,'supplier-add','mall'),(84,6,'supplier-icon-reset','mall'),(85,1,'supplier-view-admin','mall'),(86,6,'supplier-view-admin','mall'),(87,1,'shop-data','mall'),(88,6,'shop-data','mall'),(89,6,'supplier-index-admin','mall'),(90,1,'check-role-get-identity','mall'),(91,1,'supplier-status-toggle','mall'),(92,1,'supplier-list','mall'),(93,1,'index-admin','mall'),(94,1,'user-identity','mall'),(95,1,'user-add','mall'),(96,1,'reset-mobile','mall'),(97,1,'user-status-toggle','mall'),(98,1,'user-disable-batch','mall'),(99,1,'user-disable-remark-reset','mall'),(100,1,'user-enable-batch','mall'),(101,1,'user-view-lhzz','mall'),(102,1,'reset-user-status-logs','mall'),(103,1,'user-list','mall'),(104,1,'index-admin-lhzz','mall'),(105,1,'categories-have-style-series','mall'),(106,1,'categories-style-series-reset','mall'),(107,1,'getsupplierorderdetails','order'),(108,6,'getsupplierorderdetails','order'),(109,6,'expressupdate','order'),(110,6,'supplierdelivery','order'),(111,1,'getexpress','order'),(112,6,'getexpress','order'),(113,1,'getplatformdetail','order'),(114,6,'getplatformdetail','order'),(115,1,'getorderdetailsall','order'),(116,6,'getorderdetailsall','order'),(117,1,'platformhandlesubmit','order'),(118,1,'find-order-list','order'),(119,6,'find-order-list','order'),(120,6,'find-supplier-order-list','order'),(121,6,'find-unusual-list','order'),(122,1,'find-unusual-list-lhzz','order'),(123,1,'get-comment','order'),(124,6,'get-comment','order'),(125,6,'comment-reply','order'),(126,6,'supplier-after-sale-handle','order'),(127,6,'refund-handle','order'),(128,1,'supplier-delete-comment','order'),(129,6,'supplier-delete-comment','order'),(130,1,'delete-comment-list','order'),(131,6,'delete-comment-list','order'),(132,1,'delete-comment-details','order'),(133,6,'delete-comment-details','order'),(134,1,'goods-view','order'),(135,6,'goods-view','order'),(136,1,'getdistributionlist','distribution'),(137,1,'getdistributiondetail','distribution'),(138,1,'searchmore','distribution'),(139,1,'add-profit','distribution'),(140,1,'add-remarks','distribution'),(141,1,'correlate-order','distribution'),(142,6,'find-refund-detail','order'),(143,6,'after-sale-supplier-send-man','order'),(144,6,'after-sale-supplier-confirm','order'),(145,1,'after-sale-delivery','order'),(146,6,'after-sale-delivery','order'),(147,1,'find-shipping-cart-list','order'),(148,6,'find-shipping-cart-list','order'),(149,1,'effect-list','effect'),(150,1,'effect-view','effect'),(151,1,'account-list','supplieraccount'),(152,1,'account-view','supplieraccount'),(153,1,'apply-freeze','supplieraccount'),(154,1,'freeze-money','supplieraccount'),(155,1,'freeze-list','supplieraccount'),(156,1,'account-thaw','supplieraccount'),(157,1,'cashed-list','supplieraccount'),(158,1,'cashed-view','supplieraccount'),(159,1,'order-list-today','supplier-cash'),(160,1,'cash-index','supplier-cash'),(161,1,'cash-list-today','supplier-cash'),(162,1,'cash-action-detail','supplier-cash'),(163,1,'cash-deal','supplier-cash'),(164,6,'mall-view','supplier-cash'),(165,6,'get-cash-list','supplier-cash'),(166,1,'get-cash','supplier-cash'),(167,6,'get-cash','supplier-cash'),(168,6,'find-balance','withdrawals'),(169,6,'check-isset-pay-pwd','withdrawals'),(170,6,'send-pay-code','withdrawals'),(171,6,'set-pay-pwd','withdrawals'),(172,6,'find-supplier-balance','withdrawals'),(173,6,'supplier-withdrawals-apply','withdrawals'),(174,6,'find-supplier-freeze-list','withdrawals'),(175,6,'supplier-access-detail','withdrawals'),(176,6,'find-supplier-access-detail-list','withdrawals'),(177,1,'check-cash-money','withdrawals'),(178,6,'check-cash-money','withdrawals'),(179,1,'check-supplier-pay-pwd','withdrawals'),(180,6,'check-supplier-pay-pwd','withdrawals'),(181,6,'set-bank-card','withdrawals'),(182,6,'find-bank-card','withdrawals'),(183,1,'labor-cost-list','quote'),(184,1,'labor-cost-edit-list','quote'),(185,1,'labor-cost-edit','quote'),(186,1,'project-norm-list','quote'),(187,1,'project-norm-edit-list','quote'),(188,1,'project-norm-edit','quote'),(189,1,'project-norm-woodwork-list','quote'),(190,1,'project-norm-woodwork-edit','quote'),(191,1,'coefficient-list','quote'),(192,1,'coefficient-add','quote'),(193,1,'plot-list','quote'),(194,1,'labor-list','quote'),(195,1,'series-and-style','quote'),(196,1,'plot-add','quote'),(197,1,'plot-edit-view','quote'),(198,1,'plot-edit','quote'),(199,1,'plot-del','quote'),(200,1,'assort-goods','quote'),(201,1,'assort-goods-list','quote'),(202,1,'assort-goods-add','quote'),(203,1,'homepage-list','quote'),(204,1,'homepage-sort','quote'),(205,1,'homepage-district','quote'),(206,1,'homepage-toponymy','quote'),(207,1,'homepage-street','quote'),(208,1,'homepage-case','quote'),(209,1,'homepage-add','quote'),(210,1,'homepage-status','quote'),(211,1,'homepage-edit','quote'),(212,1,'homepage-delete','quote'),(213,1,'apartment-area-list','quote'),(214,1,'apartment-area','quote'),(215,1,'decoration-list','quote'),(216,1,'decoration-add-classify','quote'),(217,1,'house-type-list','quote'),(218,1,'decoration-message-list','quote'),(219,1,'decoration-add','quote'),(220,1,'decoration-del','quote'),(221,1,'decoration-edit-list','quote'),(222,1,'decoration-edit','quote'),(223,1,'commonality-list','quote'),(224,1,'commonality-title','quote'),(225,1,'commonality-title-add','quote'),(226,1,'commonality-title-two-add','quote'),(227,1,'commonality-else-list','quote'),(228,1,'commonality-else-edit','quote'),(229,1,'goods-management-list','quote'),(230,1,'goods-management-add','quote'),(231,1,'series-list','mall'),(232,1,'series-time-sort','mall'),(233,1,'series-add','mall'),(234,1,'series-edit','mall'),(235,1,'series-status','mall'),(236,1,'style-list','mall'),(237,1,'style-time-sort','mall'),(238,1,'style-add','mall'),(239,1,'style-edit','mall'),(240,1,'style-status','mall'),(241,2,'homepage-list','worker-management'),(242,2,'worker-type-list','worker-management'),(243,2,'worker-type-add','worker-management'),(244,2,'worker-type-edit','worker-management'),(245,2,'worker-list','worker-management'),(246,2,'worker-phone','worker-management'),(247,2,'worker-add','worker-management'),(248,2,'worker-order-list','worker-management'),(249,2,'workerOrderStatus','worker-management'),(250,1,'upload','site'),(251,2,'upload','site'),(252,3,'upload','site'),(253,4,'upload','site'),(254,5,'upload','site'),(255,6,'upload','site'),(256,7,'upload','site'),(257,1,'goods-by-sku','mall'),(258,6,'goods-by-sku','mall'),(259,1,'category','supplieraccount'),(260,1,'reset-mobile-logs','mall'),(261,1,'admin-logout','site'),(262,2,'admin-logout','site'),(263,3,'admin-logout','site'),(264,4,'admin-logout','site'),(265,5,'admin-logout','site'),(266,6,'admin-logout','site'),(267,7,'admin-logout','site'),(268,1,'upload-delete','site'),(269,2,'upload-delete','site'),(270,3,'upload-delete','site'),(271,4,'upload-delete','site'),(272,5,'upload-delete','site'),(273,6,'upload-delete','site'),(274,7,'upload-delete','site'),(275,1,'sku-fefer','quote')
,(276,1,'owner-account-list','supplieraccount')
,(277,1,'owner-account-detail','supplieraccount')
,(278,1,'owner-freeze-money','supplieraccount')
,(279,1,'owner-apply-freeze','supplieraccount')
,(280,1,'owner-freeze-list','supplieraccount')
,(281,1,'owner-freeze-taw','supplieraccount')
,(282,1,'owner-cash-index','supplier-cash')
,(283,1,'owner-cashed-list','supplier-cash')
,(284,1,'owner-cashed-detail','supplier-cash')
,(285,1,'owner-do-cash-deal','supplier-cash')
,(286,1,'supplier-brand-view','supplieraccount')
,(287,1,'supplier-brand-list','supplieraccount')
,(288,6,'supplier-brand-edit','supplieraccount')
,(289,1,'supplier-cate-list','supplieraccount')
,(290,1,'supplier-cate-view','supplieraccount')
,(291,6,'supplier-cate-edit','supplieraccount')
,(292,1,'supplier-access-detail-list','supplieraccount')
,(293,6,'supplier-brand-view','supplieraccount')
,(294,6,'supplier-brand-list','supplieraccount')
,(296,6,'supplier-cate-list','supplieraccount')
,(295,6,'supplier-cate-view','supplieraccount')
,(297,1,'after-find-express','order')
,(298,6,'after-find-express','order')
,(299,1,'after-sale-detail-admin','order')
,(300,6,'after-sale-detail-admin','order')
,(301,1,'get-order-num','order')
,(302,6,'get-order-num','order')
,(303,1,'close-order','order')
,(304,1,'get-supplier-info-by-shop-no','supplier')
,(305,1,'line-supplier-list','supplier')
,(306,1,'add-line-supplier','supplier')
,(307,1,'switch-line-supplier-status','supplier')
,(308,1,'get-edit-supplier-info-by-shop-no','supplier')
,(309,1,'up-line-supplier','supplier')
,(310,1,'line-supplier-goods-list','supplier')
,(311,1,'find-supplier-line-goods','supplier')
,(312,1,'find-supplier-line-by-district-code','supplier')
,(313,1,'add-line-supplier-goods','supplier')
,(314,1,'up-line-supplier-goods','supplier')
,(315,1,'switch-line-supplier-goods-status','supplier')
,(316,1,'del-line-supplier','supplier')
,(317,1,'del-line-supplier-goods','supplier')
,(318,1,'supplier-be-audited-list','supplier')
,(319,1,'supplier-be-audited-detail','supplier')
,(320,1,'get-up-supplier-line-goods','supplier')
,(321,1,'owner-access-status','supplieraccount')
,(322,1,'owner-audit-list','supplieraccount')
,(323,1,'audit-view','supplieraccount')
,(324,1,'owner-do-audit','supplieraccount')
,(325,1,'goods-list-search','mall')
;
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lhzz_permission`
--

DROP TABLE IF EXISTS `lhzz_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lhzz_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(56) NOT NULL DEFAULT '' COMMENT '访问action',
  `controller` varchar(56) NOT NULL DEFAULT '' COMMENT '访问controller',
  `desc` varchar(56) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lhzz_permission`
--

LOCK TABLES `lhzz_permission` WRITE;
/*!40000 ALTER TABLE `lhzz_permission` DISABLE KEYS */;
INSERT INTO `lhzz_permission` VALUES (1,'time-types','site',''),(2,'recommend-admin-index','mall',''),(3,'recommend-disable-batch','mall',''),(4,'recommend-delete-batch','mall',''),(5,'recommend-delete','mall',''),(6,'recommend-status-toggle','mall',''),(7,'recommend-history','mall',''),(8,'recommend-second-admin','mall',''),(9,'recommend-by-sku','mall',''),(10,'recommend-add','mall',''),(11,'recommend-edit','mall',''),(12,'recommend-sort','mall',''),(13,'category-review','mall',''),(14,'categories-admin','mall',''),(15,'category-status-toggle','mall',''),(16,'category-disable-batch','mall',''),(17,'category-enable-batch','mall',''),(18,'category-list-admin','mall',''),(19,'categories-manage-admin','mall',''),(20,'category-add','mall',''),(21,'category-edit','mall',''),(22,'category-offline-reason-reset','mall',''),(23,'category-reason-reset','mall',''),(24,'category-review-list','mall',''),(25,'brand-add','mall',''),(26,'brand-review','mall',''),(27,'brand-edit','mall',''),(28,'brand-offline-reason-reset','mall',''),(29,'brand-reason-reset','mall',''),(30,'brand-status-toggle','mall',''),(31,'brand-disable-batch','mall',''),(32,'brand-enable-batch','mall',''),(33,'brand-review-list','mall',''),(34,'brand-list-admin','mall',''),(35,'brand-application-list-admin','mall',''),(36,'brand-application-review-list','mall',''),(37,'brand-application-review-note-reset','mall',''),(38,'brand-application-review','mall',''),(39,'logistics-template-view','mall',''),(40,'goods-attr-add','mall',''),(41,'goods-attr-list-admin','mall',''),(42,'goods-edit','mall',''),(43,'goods-edit-lhzz','mall',''),(44,'goods-status-toggle','mall',''),(45,'goods-disable-batch','mall',''),(46,'goods-enable-batch','mall',''),(47,'goods-offline-reason-reset','mall',''),(48,'goods-reason-reset','mall',''),(49,'goods-list-admin','mall',''),(50,'supplier-add','mall',''),(51,'supplier-view-admin','mall',''),(52,'shop-data','mall',''),(53,'check-role-get-identity','mall',''),(54,'supplier-status-toggle','mall',''),(55,'supplier-list','mall',''),(56,'index-admin','mall',''),(57,'user-identity','mall',''),(58,'user-add','mall',''),(59,'reset-mobile','mall',''),(60,'user-status-toggle','mall',''),(61,'user-disable-batch','mall',''),(62,'user-disable-remark-reset','mall',''),(63,'user-enable-batch','mall',''),(64,'user-view-lhzz','mall',''),(65,'reset-user-status-logs','mall',''),(66,'user-list','mall',''),(67,'index-admin-lhzz','mall',''),(68,'categories-have-style-series','mall',''),(69,'categories-style-series-reset','mall',''),(70,'getsupplierorderdetails','order',''),(71,'getexpress','order',''),(72,'getplatformdetail','order',''),(73,'getorderdetailsall','order',''),(74,'platformhandlesubmit','order',''),(75,'find-order-list','order',''),(76,'find-unusual-list-lhzz','order',''),(77,'get-comment','order',''),(78,'supplier-delete-comment','order',''),(79,'delete-comment-list','order',''),(80,'delete-comment-details','order',''),(81,'goods-view','order',''),(82,'getdistributionlist','distribution',''),(83,'getdistributiondetail','distribution',''),(84,'searchmore','distribution',''),(85,'add-profit','distribution',''),(86,'add-remarks','distribution',''),(87,'correlate-order','distribution',''),(88,'after-sale-delivery','order',''),(89,'find-shipping-cart-list','order',''),(90,'effect-list','effect',''),(91,'effect-view','effect',''),(92,'account-list','supplieraccount',''),(93,'account-view','supplieraccount',''),(94,'apply-freeze','supplieraccount',''),(95,'freeze-money','supplieraccount',''),(96,'freeze-list','supplieraccount',''),(97,'account-thaw','supplieraccount',''),(98,'cashed-list','supplieraccount',''),(99,'cashed-view','supplieraccount',''),(100,'order-list-today','supplier-cash',''),(101,'cash-index','supplier-cash',''),(102,'cash-list-today','supplier-cash',''),(103,'cash-action-detail','supplier-cash',''),(104,'cash-deal','supplier-cash',''),(105,'get-cash','supplier-cash',''),(106,'check-cash-money','withdrawals',''),(107,'check-supplier-pay-pwd','withdrawals',''),(108,'labor-cost-list','quote',''),(109,'labor-cost-edit-list','quote',''),(110,'labor-cost-edit','quote',''),(111,'project-norm-list','quote',''),(112,'project-norm-edit-list','quote',''),(113,'project-norm-edit','quote',''),(114,'project-norm-woodwork-list','quote',''),(115,'project-norm-woodwork-edit','quote',''),(116,'coefficient-list','quote',''),(117,'coefficient-add','quote',''),(118,'plot-list','quote',''),(119,'labor-list','quote',''),(120,'series-and-style','quote',''),(121,'plot-add','quote',''),(122,'plot-edit-view','quote',''),(123,'plot-edit','quote',''),(124,'plot-del','quote',''),(125,'assort-goods','quote',''),(126,'assort-goods-list','quote',''),(127,'assort-goods-add','quote',''),(128,'homepage-list','quote',''),(129,'homepage-sort','quote',''),(130,'homepage-district','quote',''),(131,'homepage-toponymy','quote',''),(132,'homepage-street','quote',''),(133,'homepage-case','quote',''),(134,'homepage-add','quote',''),(135,'homepage-status','quote',''),(136,'homepage-edit','quote',''),(137,'homepage-delete','quote',''),(138,'apartment-area-list','quote',''),(139,'apartment-area','quote',''),(140,'decoration-list','quote',''),(141,'decoration-add-classify','quote',''),(142,'house-type-list','quote',''),(143,'decoration-message-list','quote',''),(144,'decoration-add','quote',''),(145,'decoration-del','quote',''),(146,'decoration-edit-list','quote',''),(147,'decoration-edit','quote',''),(148,'commonality-list','quote',''),(149,'commonality-title','quote',''),(150,'commonality-title-add','quote',''),(151,'commonality-title-two-add','quote',''),(152,'commonality-else-list','quote',''),(153,'commonality-else-edit','quote',''),(154,'goods-management-list','quote',''),(155,'goods-management-add','quote',''),(156,'series-list','mall',''),(157,'series-time-sort','mall',''),(158,'series-add','mall',''),(159,'series-edit','mall',''),(160,'series-status','mall',''),(161,'style-list','mall',''),(162,'style-time-sort','mall',''),(163,'style-add','mall',''),(164,'style-edit','mall',''),(165,'style-status','mall',''),(166,'upload','site',''),(167,'goods-by-sku','mall',''),(168,'category','supplieraccount',''),(169,'reset-mobile-logs','mall',''),(170,'admin-logout','site',''),(171,'upload-delete','site',''),(172,'sku-fefer','quote','')
,(173,'owner-account-list','supplieraccount','')
,(174,'owner-account-detail','supplieraccount','')
,(175,'owner-freeze-money','supplieraccount','')
,(176,'owner-apply-freeze','supplieraccount','')
,(177,'owner-freeze-list','supplieraccount','')
,(178,'owner-freeze-taw','supplieraccount','')
,(179,'owner-cash-index','supplier-cash','')
,(180,'owner-cashed-list','supplier-cash','')
,(181,'owner-cashed-detail','supplier-cash','')
,(182,'owner-do-cash-deal','supplier-cash','')
,(183,'supplier-brand-view','supplieraccount','')
,(184,'supplier-brand-list','supplieraccount','')
,(186,'supplier-cate-list','supplieraccount','')
,(187,'supplier-cate-view','supplieraccount','')
,(189,'supplier-access-detail-list','supplieraccount','')
,(190,'after-find-express','order','')
,(191,'after-sale-detail-admin','order','')
,(192,'get-order-num','order','')
,(193,'close-order','order','')
,(194,'get-supplier-info-by-shop-no','supplier','')
,(195,'line-supplier-list','supplier','')
,(196,'add-line-supplier','supplier','')
,(197,'switch-line-supplier-status','supplier','')
,(198,'get-edit-supplier-info-by-shop-no','supplier','')
,(199,'up-line-supplier','supplier','')
,(200,'line-supplier-goods-list','supplier','')
,(201,'find-supplier-line-goods','supplier','')
,(202,'find-supplier-line-by-district-code','supplier','')
,(203,'add-line-supplier-goods','supplier','')
,(204,'up-line-supplier-goods','supplier','')
,(205,'switch-line-supplier-goods-status','supplier','')
,(206,'del-line-supplier','supplier','')
,(207,'del-line-supplier-goods','supplier','')
,(208,'supplier-be-audited-list','supplier','')
,(209,'supplier-be-audited-detail','supplier','')
,(210,'get-up-supplier-line-goods','supplier','')
,(211,'owner-access-status','supplieraccount','')
,(212,'owner-audit-list','supplieraccount','')
,(213,'audit-view','supplieraccount','')
,(214,'owner-do-audit','supplieraccount','')
,(215,'goods-list-admin','mall','')
;
/*!40000 ALTER TABLE `lhzz_permission` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-10 17:23:35
