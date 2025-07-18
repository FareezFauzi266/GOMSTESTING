-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 18, 2025 at 07:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `goms`
--
CREATE DATABASE IF NOT EXISTS `goms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `goms`;

-- --------------------------------------------------------

--
-- Table structure for table `financialledger`
--

DROP TABLE IF EXISTS `financialledger`;
CREATE TABLE IF NOT EXISTS `financialledger` (
  `ledgerID` int(11) NOT NULL AUTO_INCREMENT,
  `paymentID` int(11) DEFAULT NULL,
  `ledgerName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ledgerID`),
  KEY `paymentID` (`paymentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventoryitem`
--

DROP TABLE IF EXISTS `inventoryitem`;
CREATE TABLE IF NOT EXISTS `inventoryitem` (
  `itemCode` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(100) DEFAULT NULL,
  `itemQuantity` int(11) DEFAULT NULL,
  `itemPrice` decimal(10,2) DEFAULT NULL,
  `itemCategory` varchar(50) DEFAULT NULL,
  `itemSupplierID` int(11) DEFAULT NULL,
  PRIMARY KEY (`itemCode`),
  KEY `itemSupplierID` (`itemSupplierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE IF NOT EXISTS `invoice` (
  `invoiceCode` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceDate` datetime DEFAULT current_timestamp(),
  `totalAmount` decimal(10,2) DEFAULT NULL,
  `itemCode` int(11) DEFAULT NULL,
  `supplierID` int(11) DEFAULT NULL,
  PRIMARY KEY (`invoiceCode`),
  KEY `itemCode` (`itemCode`),
  KEY `supplierID` (`supplierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logpayment`
--

DROP TABLE IF EXISTS `logpayment`;
CREATE TABLE IF NOT EXISTS `logpayment` (
  `paymentID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `paymentAmount` decimal(10,2) DEFAULT NULL,
  `paymentMethod` enum('Cash','Credit/Debit Card','Bank Transfer','E-Wallet') NOT NULL,
  `transactionType` varchar(50) DEFAULT NULL,
  `discount` int(11) DEFAULT 0,
  PRIMARY KEY (`paymentID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenanceitem`
--

DROP TABLE IF EXISTS `maintenanceitem`;
CREATE TABLE IF NOT EXISTS `maintenanceitem` (
  `maintainedItemID` int(11) NOT NULL AUTO_INCREMENT,
  `scheduleID` int(11) DEFAULT NULL,
  `itemCode` int(11) DEFAULT NULL,
  `frequencyDays` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`maintainedItemID`),
  KEY `scheduleID` (`scheduleID`),
  KEY `itemCode` (`itemCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenancerecord`
--

DROP TABLE IF EXISTS `maintenancerecord`;
CREATE TABLE IF NOT EXISTS `maintenancerecord` (
  `recordID` int(11) NOT NULL AUTO_INCREMENT,
  `maintainedItemID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `maintenanceDate` date DEFAULT NULL,
  `itemCondition` enum('Good','Need Maintenance') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `attachmentPath` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`recordID`),
  KEY `maintainedItemID` (`maintainedItemID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenanceschedule`
--

DROP TABLE IF EXISTS `maintenanceschedule`;
CREATE TABLE IF NOT EXISTS `maintenanceschedule` (
  `scheduleID` int(11) NOT NULL AUTO_INCREMENT,
  `scheduleName` varchar(100) DEFAULT NULL,
  `scheduleDescription` text DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`scheduleID`),
  KEY `createdBy` (`createdBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
CREATE TABLE IF NOT EXISTS `supplier` (
  `supplierID` int(11) NOT NULL AUTO_INCREMENT,
  `supplierName` varchar(100) DEFAULT NULL,
  `supplierContactNumber` varchar(20) DEFAULT NULL,
  `supplierEmail` varchar(100) DEFAULT NULL,
  `supplierAddress` text DEFAULT NULL,
  `supplierPICName` varchar(100) DEFAULT NULL,
  `supplierPICNumber` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`supplierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(100) NOT NULL,
  `userRole` enum('Manager','Staff') NOT NULL,
  `userEmail` varchar(100) DEFAULT NULL,
  `userPassword` varchar(255) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `financialledger`
--
ALTER TABLE `financialledger`
  ADD CONSTRAINT `financialledger_ibfk_1` FOREIGN KEY (`paymentID`) REFERENCES `logpayment` (`paymentID`);

--
-- Constraints for table `inventoryitem`
--
ALTER TABLE `inventoryitem`
  ADD CONSTRAINT `inventoryitem_ibfk_1` FOREIGN KEY (`itemSupplierID`) REFERENCES `supplier` (`supplierID`);

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`itemCode`) REFERENCES `inventoryitem` (`itemCode`),
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`supplierID`) REFERENCES `supplier` (`supplierID`);

--
-- Constraints for table `logpayment`
--
ALTER TABLE `logpayment`
  ADD CONSTRAINT `logpayment_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `maintenanceitem`
--
ALTER TABLE `maintenanceitem`
  ADD CONSTRAINT `maintenanceitem_ibfk_1` FOREIGN KEY (`scheduleID`) REFERENCES `maintenanceschedule` (`scheduleID`),
  ADD CONSTRAINT `maintenanceitem_ibfk_2` FOREIGN KEY (`itemCode`) REFERENCES `inventoryitem` (`itemCode`);

--
-- Constraints for table `maintenancerecord`
--
ALTER TABLE `maintenancerecord`
  ADD CONSTRAINT `maintenancerecord_ibfk_1` FOREIGN KEY (`maintainedItemID`) REFERENCES `maintenanceitem` (`maintainedItemID`),
  ADD CONSTRAINT `maintenancerecord_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `maintenanceschedule`
--
ALTER TABLE `maintenanceschedule`
  ADD CONSTRAINT `maintenanceschedule_ibfk_1` FOREIGN KEY (`createdBy`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;