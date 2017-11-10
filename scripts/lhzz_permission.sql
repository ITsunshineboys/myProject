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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lhzz_permission`
--

LOCK TABLES `lhzz_permission` WRITE;
/*!40000 ALTER TABLE `lhzz_permission` DISABLE KEYS */;
INSERT INTO `lhzz_permission` VALUES (1,'recommend-admin-index','mall',''),(2,'recommend-disable-batch','mall',''),(3,'recommend-delete-batch','mall',''),(4,'recommend-delete','mall',''),(5,'recommend-status-toggle','mall',''),(6,'recommend-history','mall',''),(7,'recommend-second-admin','mall',''),(8,'recommend-by-sku','mall',''),(9,'recommend-add','mall',''),(10,'recommend-edit','mall',''),(11,'recommend-sort','mall',''),(12,'category-review','mall',''),(13,'categories-admin','mall',''),(14,'category-status-toggle','mall',''),(15,'category-disable-batch','mall',''),(16,'category-enable-batch','mall',''),(17,'category-list-admin','mall',''),(18,'categories-manage-admin','mall',''),(19,'category-add','mall',''),(20,'category-edit','mall',''),(21,'category-offline-reason-reset','mall',''),(22,'category-reason-reset','mall',''),(23,'category-review-list','mall',''),(24,'brand-add','mall',''),(25,'brand-review','mall',''),(26,'brand-edit','mall',''),(27,'brand-offline-reason-reset','mall',''),(28,'brand-reason-reset','mall',''),(29,'brand-status-toggle','mall',''),(30,'brand-disable-batch','mall',''),(31,'brand-enable-batch','mall',''),(32,'brand-review-list','mall',''),(33,'brand-list-admin','mall',''),(34,'brand-application-list-admin','mall',''),(35,'brand-application-review-list','mall',''),(36,'brand-application-review-note-reset','mall',''),(37,'brand-application-review','mall',''),(38,'logistics-template-view','mall',''),(39,'goods-attr-add','mall',''),(40,'goods-attr-list-admin','mall',''),(41,'goods-edit','mall',''),(42,'goods-edit-lhzz','mall',''),(43,'goods-status-toggle','mall',''),(44,'goods-disable-batch','mall',''),(45,'goods-enable-batch','mall',''),(46,'goods-offline-reason-reset','mall',''),(47,'goods-reason-reset','mall',''),(48,'goods-list-admin','mall',''),(49,'supplier-add','mall',''),(50,'supplier-view-admin','mall',''),(51,'shop-data','mall',''),(52,'check-role-get-identity','mall',''),(53,'supplier-status-toggle','mall',''),(54,'supplier-list','mall',''),(55,'index-admin','mall',''),(56,'user-identity','mall',''),(57,'user-add','mall',''),(58,'reset-mobile','mall',''),(59,'user-status-toggle','mall',''),(60,'user-disable-batch','mall',''),(61,'user-disable-remark-reset','mall',''),(62,'user-enable-batch','mall',''),(63,'user-view-lhzz','mall',''),(64,'reset-user-status-logs','mall',''),(65,'user-list','mall',''),(66,'index-admin-lhzz','mall',''),(67,'categories-have-style-series','mall',''),(68,'categories-style-series-reset','mall','');
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

-- Dump completed on 2017-11-10 16:45:21
