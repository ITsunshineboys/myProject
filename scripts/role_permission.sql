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
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES (1,1,'recommend-admin-index','mall'),(2,1,'recommend-disable-batch','mall'),(3,1,'recommend-delete-batch','mall'),(4,1,'recommend-delete','mall'),(5,1,'recommend-status-toggle','mall'),(6,1,'recommend-history','mall'),(7,1,'recommend-second-admin','mall'),(8,1,'recommend-by-sku','mall'),(9,1,'recommend-add','mall'),(10,1,'recommend-edit','mall'),(11,1,'recommend-sort','mall'),(12,6,'recommend-add-supplier','mall'),(13,6,'recommend-edit-supplier','mall'),(14,6,'recommend-delete-supplier','mall'),(15,6,'recommend-delete-batch-supplier','mall'),(16,6,'recommend-admin-index-supplier','mall'),(17,1,'category-review','mall'),(18,1,'categories-admin','mall'),(19,6,'categories-admin','mall'),(20,1,'category-status-toggle','mall'),(21,1,'category-disable-batch','mall'),(22,1,'category-enable-batch','mall'),(23,1,'category-list-admin','mall'),(24,6,'category-list-admin','mall'),(25,1,'categories-manage-admin','mall'),(26,6,'categories-manage-admin','mall'),(27,1,'category-add','mall'),(28,6,'category-add','mall'),(29,1,'category-edit','mall'),(30,1,'category-offline-reason-reset','mall'),(31,1,'category-reason-reset','mall'),(32,1,'category-review-list','mall'),(33,6,'category-attrs','mall'),(34,1,'brand-add','mall'),(35,6,'brand-add','mall'),(36,1,'brand-review','mall'),(37,1,'brand-edit','mall'),(38,1,'brand-offline-reason-reset','mall'),(39,1,'brand-reason-reset','mall'),(40,1,'brand-status-toggle','mall'),(41,1,'brand-disable-batch','mall'),(42,1,'brand-enable-batch','mall'),(43,1,'brand-review-list','mall'),(44,1,'brand-list-admin','mall'),(45,6,'brand-list-admin','mall'),(46,6,'brand-application-add','mall'),(47,1,'brand-application-list-admin','mall'),(48,6,'brand-application-list-admin','mall'),(49,1,'brand-application-review-list','mall'),(50,1,'brand-application-review-note-reset','mall'),(51,1,'brand-application-review','mall'),(52,6,'logistics-template-add','mall'),(53,6,'logistics-template-edit','mall'),(54,1,'logistics-template-view','mall'),(55,6,'logistics-template-view','mall'),(56,6,'logistics-templates-supplier','mall'),(57,6,'logistics-template-status-toggle','mall'),(58,1,'goods-attr-add','mall'),(59,1,'goods-attr-list-admin','mall'),(60,6,'goods-add','mall'),(61,1,'goods-edit','mall'),(62,6,'goods-edit','mall'),(63,1,'goods-edit-lhzz','mall'),(64,6,'goods-attrs-admin','mall'),(65,1,'goods-status-toggle','mall'),(66,6,'goods-status-toggle','mall'),(67,1,'goods-disable-batch','mall'),(68,6,'goods-disable-batch','mall'),(69,6,'goods-delete-batch','mall'),(70,1,'goods-enable-batch','mall'),(71,1,'goods-offline-reason-reset','mall'),(72,1,'goods-reason-reset','mall'),(73,1,'goods-list-admin','mall'),(74,6,'goods-list-admin','mall'),(75,6,'goods-inventory-reset','mall'),(76,1,'supplier-add','mall'),(77,6,'supplier-icon-reset','mall'),(78,1,'supplier-view-admin','mall'),(79,6,'supplier-view-admin','mall'),(80,1,'shop-data','mall'),(81,6,'shop-data','mall'),(82,6,'supplier-index-admin','mall'),(83,1,'check-role-get-identity','mall'),(84,1,'supplier-status-toggle','mall'),(85,1,'supplier-list','mall'),(86,1,'index-admin','mall'),(87,1,'user-identity','mall'),(88,1,'user-add','mall'),(89,1,'reset-mobile','mall'),(90,1,'user-status-toggle','mall'),(91,1,'user-disable-batch','mall'),(92,1,'user-disable-remark-reset','mall'),(93,1,'user-enable-batch','mall'),(94,1,'user-view-lhzz','mall'),(95,1,'reset-user-status-logs','mall'),(96,1,'user-list','mall'),(97,1,'index-admin-lhzz','mall'),(98,1,'categories-have-style-series','mall'),(99,1,'categories-style-series-reset','mall');
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-10 15:20:52
