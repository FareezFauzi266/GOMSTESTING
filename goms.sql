CREATE DATABASE  IF NOT EXISTS `goms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `goms`;
-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: goms
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `financialledger`
--

DROP TABLE IF EXISTS `financialledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `financialledger` (
  `ledgerID` int NOT NULL AUTO_INCREMENT,
  `paymentID` int DEFAULT NULL,
  `ledgerName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ledgerID`),
  KEY `paymentID` (`paymentID`),
  CONSTRAINT `financialledger_ibfk_1` FOREIGN KEY (`paymentID`) REFERENCES `logpayment` (`paymentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financialledger`
--

LOCK TABLES `financialledger` WRITE;
/*!40000 ALTER TABLE `financialledger` DISABLE KEYS */;
/*!40000 ALTER TABLE `financialledger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventoryitem`
--

DROP TABLE IF EXISTS `inventoryitem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventoryitem` (
  `itemCode` int NOT NULL AUTO_INCREMENT,
  `itemName` varchar(100) DEFAULT NULL,
  `itemQuantity` int DEFAULT NULL,
  `itemPrice` decimal(10,2) DEFAULT NULL,
  `itemCategory` varchar(50) DEFAULT NULL,
  `itemSupplierID` int DEFAULT NULL,
  PRIMARY KEY (`itemCode`),
  KEY `itemSupplierID` (`itemSupplierID`),
  CONSTRAINT `inventoryitem_ibfk_1` FOREIGN KEY (`itemSupplierID`) REFERENCES `supplier` (`supplierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventoryitem`
--

LOCK TABLES `inventoryitem` WRITE;
/*!40000 ALTER TABLE `inventoryitem` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventoryitem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice` (
  `invoiceCode` int NOT NULL AUTO_INCREMENT,
  `invoiceDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `totalAmount` decimal(10,2) DEFAULT NULL,
  `itemCode` int DEFAULT NULL,
  `supplierID` int DEFAULT NULL,
  PRIMARY KEY (`invoiceCode`),
  KEY `itemCode` (`itemCode`),
  KEY `supplierID` (`supplierID`),
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`itemCode`) REFERENCES `inventoryitem` (`itemCode`),
  CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`supplierID`) REFERENCES `supplier` (`supplierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice`
--

LOCK TABLES `invoice` WRITE;
/*!40000 ALTER TABLE `invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logpayment`
--

DROP TABLE IF EXISTS `logpayment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logpayment` (
  `paymentID` int NOT NULL AUTO_INCREMENT,
  `userID` int DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `paymentAmount` decimal(10,2) DEFAULT NULL,
  `paymentMethod` varchar(50) DEFAULT NULL,
  `transactionType` varchar(50) DEFAULT NULL,
  `discount` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`paymentID`),
  KEY `userID` (`userID`),
  CONSTRAINT `logpayment_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logpayment`
--

LOCK TABLES `logpayment` WRITE;
/*!40000 ALTER TABLE `logpayment` DISABLE KEYS */;
/*!40000 ALTER TABLE `logpayment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenanceitem`
--

DROP TABLE IF EXISTS `maintenanceitem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenanceitem` (
  `maintainedItemID` int NOT NULL AUTO_INCREMENT,
  `scheduleID` int DEFAULT NULL,
  `itemCode` int DEFAULT NULL,
  `frequencyDays` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`maintainedItemID`),
  KEY `scheduleID` (`scheduleID`),
  KEY `itemCode` (`itemCode`),
  CONSTRAINT `maintenanceitem_ibfk_1` FOREIGN KEY (`scheduleID`) REFERENCES `maintenanceschedule` (`scheduleID`),
  CONSTRAINT `maintenanceitem_ibfk_2` FOREIGN KEY (`itemCode`) REFERENCES `inventoryitem` (`itemCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenanceitem`
--

LOCK TABLES `maintenanceitem` WRITE;
/*!40000 ALTER TABLE `maintenanceitem` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenanceitem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenancerecord`
--

DROP TABLE IF EXISTS `maintenancerecord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenancerecord` (
  `recordID` int NOT NULL AUTO_INCREMENT,
  `maintainedItemID` int DEFAULT NULL,
  `userID` int DEFAULT NULL,
  `maintenanceDate` date DEFAULT NULL,
  `itemCondition` enum('Good','Need Maintenance') DEFAULT NULL,
  `remarks` text,
  `attachmentPath` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`recordID`),
  KEY `maintainedItemID` (`maintainedItemID`),
  KEY `userID` (`userID`),
  CONSTRAINT `maintenancerecord_ibfk_1` FOREIGN KEY (`maintainedItemID`) REFERENCES `maintenanceitem` (`maintainedItemID`),
  CONSTRAINT `maintenancerecord_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenancerecord`
--

LOCK TABLES `maintenancerecord` WRITE;
/*!40000 ALTER TABLE `maintenancerecord` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenancerecord` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenanceschedule`
--

DROP TABLE IF EXISTS `maintenanceschedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenanceschedule` (
  `scheduleID` int NOT NULL AUTO_INCREMENT,
  `scheduleName` varchar(100) DEFAULT NULL,
  `scheduleDescription` text,
  `createdBy` int DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`scheduleID`),
  KEY `createdBy` (`createdBy`),
  CONSTRAINT `maintenanceschedule_ibfk_1` FOREIGN KEY (`createdBy`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenanceschedule`
--

LOCK TABLES `maintenanceschedule` WRITE;
/*!40000 ALTER TABLE `maintenanceschedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenanceschedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier` (
  `supplierID` int NOT NULL AUTO_INCREMENT,
  `supplierName` varchar(100) DEFAULT NULL,
  `supplierContactNumber` varchar(20) DEFAULT NULL,
  `supplierEmail` varchar(100) DEFAULT NULL,
  `supplierAddress` text,
  `supplierPICName` varchar(100) DEFAULT NULL,
  `supplierPICNumber` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`supplierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier`
--

LOCK TABLES `supplier` WRITE;
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `userName` varchar(100) NOT NULL,
  `userRole` enum('Manager','Staff') NOT NULL,
  `userEmail` varchar(100) DEFAULT NULL,
  `userPassword` varchar(255) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-17 23:35:45
