-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 22, 2025 at 11:06 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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

-- --------------------------------------------------------

--
-- Table structure for table `financialledger`
--

CREATE TABLE `financialledger` (
  `ledgerID` int(11) NOT NULL,
  `paymentID` int(11) DEFAULT NULL,
  `ledgerName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventoryitem`
--

CREATE TABLE `inventoryitem` (
  `itemCode` int(11) NOT NULL,
  `itemName` varchar(100) DEFAULT NULL,
  `itemQuantity` int(11) DEFAULT NULL,
  `itemPrice` decimal(10,2) DEFAULT NULL,
  `itemCategory` varchar(50) DEFAULT NULL,
  `itemSupplierID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventoryitem`
--

INSERT INTO `inventoryitem` (`itemCode`, `itemName`, `itemQuantity`, `itemPrice`, `itemCategory`, `itemSupplierID`) VALUES
(7001, 'Gym Membership - Basic', 36, 150.00, 'membership', 6),
(7002, 'Gym T-Shirt', 200, 15.00, 'merchandise', 4),
(7003, 'Protein Powder', 75, 80.00, 'supplements', 5),
(7004, 'Energy Bar Pack', 143, 10.00, 'supplements', 5),
(7005, 'Water Bottle', 167, 12.00, 'merchandise', 4),
(7006, 'Resistance Bands', 90, 20.00, 'equipment', 2),
(7007, 'Treadmill', 5, 2000.00, 'equipment', 7);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoiceCode` int(11) NOT NULL,
  `invoiceDate` datetime DEFAULT current_timestamp(),
  `totalAmount` decimal(10,2) DEFAULT NULL,
  `itemCode` int(11) DEFAULT NULL,
  `supplierID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logpayment`
--

CREATE TABLE `logpayment` (
  `paymentID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `paymentAmount` decimal(10,2) DEFAULT NULL,
  `paymentMethod` enum('Cash','Credit/Debit Card','Bank Transfer','E-Wallet') NOT NULL,
  `transactionType` varchar(50) DEFAULT NULL,
  `discount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logpayment`
--

INSERT INTO `logpayment` (`paymentID`, `userID`, `createdAt`, `paymentAmount`, `paymentMethod`, `transactionType`, `discount`) VALUES
(9001, 1, '2025-07-18 15:14:18', 126.72, 'E-Wallet', 'merchandise', 12),
(9002, 2, '2025-07-19 01:41:54', 46.80, 'Bank Transfer', 'merchandise, supplements', 10),
(9003, 2, '2025-07-19 01:42:44', 1800.00, 'Cash', 'membership', 0),
(9004, 2, '2025-07-19 19:16:55', 240.00, 'E-Wallet', 'membership', 20),
(9005, 2, '2025-07-19 19:42:10', 30.00, 'Credit/Debit Card', 'supplements', 0);

-- --------------------------------------------------------

--
-- Table structure for table `maintenanceitem`
--

CREATE TABLE `maintenanceitem` (
  `maintainedItemID` varchar(10) NOT NULL,
  `scheduleID` varchar(10) NOT NULL,
  `itemCode` int(11) NOT NULL,
  `daysOfWeek` varchar(7) NOT NULL DEFAULT '-------'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenanceitem`
--

INSERT INTO `maintenanceitem` (`maintainedItemID`, `scheduleID`, `itemCode`, `daysOfWeek`) VALUES
('MID001', 'MTB001', 7006, 'M-W-F-U'),
('MID002', 'MTB001', 7001, 'MT-HF--'),
('MID003', 'MTB002', 7005, 'M-W-F-U'),
('MID004', 'MTB002', 7004, 'MTWHFSU'),
('MID005', 'MTB003', 7007, 'M--H--U'),
('MID006', 'MTB004', 7007, 'MTW----'),
('MID007', 'MTB005', 7007, 'MTWHFSU');

-- --------------------------------------------------------

--
-- Table structure for table `maintenancerecord`
--

CREATE TABLE `maintenancerecord` (
  `recordID` varchar(10) NOT NULL,
  `maintainedItemID` varchar(10) NOT NULL,
  `userID` int(11) NOT NULL,
  `maintenanceDate` date NOT NULL,
  `itemCondition` enum('OK','Needs Repair','Replace Soon') NOT NULL,
  `remarks` text DEFAULT NULL,
  `attachmentPath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenancerecord`
--

INSERT INTO `maintenancerecord` (`recordID`, `maintainedItemID`, `userID`, `maintenanceDate`, `itemCondition`, `remarks`, `attachmentPath`) VALUES
('R001', 'MID001', 2, '2025-07-14', 'Needs Repair', 'hehe', NULL),
('R002', 'MID001', 2, '2025-07-16', 'OK', 'hehe', NULL),
('R003', 'MID001', 2, '2025-07-18', 'OK', 'hhhh', NULL),
('R004', 'MID002', 2, '2025-07-14', 'OK', 'hehe123', NULL),
('R005', 'MID002', 2, '2025-07-15', 'OK', 'heehe123321', '/uploads/maintenance/ATT_687ac266d3ee5_Screenshot 2025-07-19 at 3.53.55 AM.png'),
('R006', 'MID002', 2, '2025-07-17', 'OK', 'huhu', '/uploads/maintenance/ATT_R006.png'),
('R007', 'MID003', 2, '2025-07-14', 'Needs Repair', 'hehe', '/uploads/maintenance/ATT_R007.png'),
('R008', 'MID004', 2, '2025-07-14', 'OK', 'eheh', NULL),
('R009', 'MID005', 2, '2025-07-14', 'OK', 'hehe', NULL),
('R010', 'MID005', 2, '2025-07-21', 'Needs Repair', 'lalalalal', NULL),
('R011', 'MID001', 2, '2025-07-21', 'OK', '', '/uploads/maintenance/ATT_R011.heic'),
('R012', 'MID006', 2, '2025-07-21', 'Needs Repair', 'fsfssf', '/uploads/maintenance/ATT_R012.jpg'),
('R013', 'MID003', 2, '2025-07-21', 'Replace Soon', 'lalalala', NULL),
('R014', 'MID006', 2, '2025-07-22', 'Needs Repair', 'Needs repair', NULL),
('R015', 'MID006', 2, '2025-07-23', 'OK', 'Test gambo', NULL),
('R016', 'MID006', 2, '2025-07-28', 'OK', 'DEBUG IMAGE', NULL),
('R017', 'MID006', 2, '2025-07-29', 'OK', 'DEBUG IMG NOT SHOWING 2', NULL),
('R018', 'MID006', 2, '2025-07-29', 'OK', 'DEBUG IMG NOT SHOWING 2', NULL),
('R019', 'MID006', 2, '2025-07-29', 'OK', 'DEBUG IMG NOT SHOWING 2', NULL),
('R020', 'MID006', 2, '2025-07-29', 'OK', 'DEBUG IMG NOT SHOWING 2', NULL),
('R021', 'MID006', 2, '2025-07-29', 'OK', 'DEBUG IMG NOT SHOWING 2', NULL),
('R022', 'MID006', 2, '2025-07-29', 'OK', 'DEBUG IMG NOT SHOWING 2', NULL),
('R023', 'MID006', 2, '2025-07-29', 'OK', 'DEBUG IMG NOT SHOWING 2', NULL),
('R024', 'MID006', 2, '2025-07-29', 'OK', 'DEBUG IMG NOT SHOWING 2', NULL),
('R025', 'MID006', 2, '2025-07-30', 'OK', 'TEST IMG DEBUG 3', '/uploads/maintenance/ATT_R025.jpg'),
('R026', 'MID007', 2, '2025-07-22', 'Needs Repair', 'DEBUG 1', '/uploads/maintenance/ATT_R026.jpg'),
('R027', 'MID007', 2, '2025-07-23', 'OK', 'DEBUG 2', '/uploads/maintenance/ATT_R027.png'),
('R028', 'MID007', 2, '2025-07-24', 'OK', 'DEBUG 3', '/uploads/maintenance/ATT_R028.jpeg'),
('R029', 'MID007', 2, '2025-07-25', 'OK', 'DEBUG 4', '/uploads/maintenance/ATT_R029.png');

-- --------------------------------------------------------

--
-- Table structure for table `maintenanceschedule`
--

CREATE TABLE `maintenanceschedule` (
  `scheduleID` varchar(10) NOT NULL,
  `scheduleName` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdAt` date DEFAULT curdate(),
  `scheduleDesc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenanceschedule`
--

INSERT INTO `maintenanceschedule` (`scheduleID`, `scheduleName`, `createdBy`, `createdAt`, `scheduleDesc`) VALUES
('MTB001', 'Electrical Items', 2, '2025-07-19', 'Maintenance for Electrical Items'),
('MTB002', 'Free weight machines', 2, '2025-07-19', 'Maintenance schedule for free weights item. eg: dumbelss and plates'),
('MTB003', 'Electrical Items', 3, '2025-07-20', 'sssss'),
('MTB004', 'TOLOL', 2, '2025-07-21', 'hehe'),
('MTB005', 'DEBUG', 2, '2025-07-22', 'DEBUG PURPOSE');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplierID` int(11) NOT NULL,
  `supplierName` varchar(100) DEFAULT NULL,
  `supplierContactNumber` varchar(20) DEFAULT NULL,
  `supplierEmail` varchar(100) DEFAULT NULL,
  `supplierAddress` text DEFAULT NULL,
  `supplierPICName` varchar(100) DEFAULT NULL,
  `supplierPICNumber` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplierID`, `supplierName`, `supplierContactNumber`, `supplierEmail`, `supplierAddress`, `supplierPICName`, `supplierPICNumber`) VALUES
(1, 'FitPro Supplies', '012-3456789', 'contact@fitpro.com', '123 Fitness St, Kuala Lumpur, Malaysia', 'John Tan', '012-9876543'),
(2, 'Healthy Essentials', '013-4567890', 'sales@healthyessentials.my', '456 Wellness Ave, Selangor, Malaysia', 'Lisa Wong', '013-8765432'),
(3, 'NutriPlus Distributors', '014-5678901', 'info@nutriplus.com.my', '789 Nutrition Blvd, Penang, Malaysia', 'Ahmad Faiz', '014-7654321'),
(4, 'GymGear Supplies', '015-6789012', 'support@gymgear.my', '321 Power Rd, Johor Bahru, Malaysia', 'Siti Rahmah', '015-6543210'),
(5, 'Elite Sports Equip', '016-7890123', 'contact@elitesports.com', '654 Champion Ln, Melaka, Malaysia', 'Michael Lee', '016-5432109'),
(6, 'Spartan Gym and Fitness', '017-805 5323', 'contact@spartangym.com', 'Damansara Damai, 47810 Petaling Jaya, Selangor', 'Sufhian', '017-805 5323'),
(7, 'Hammer Pro', '0198961029', 'sales@hammerpro.com', NULL, 'Abu', '0122341231');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `userName` varchar(100) NOT NULL,
  `userRole` enum('Manager','Staff') NOT NULL,
  `userEmail` varchar(100) DEFAULT NULL,
  `userPassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `userName`, `userRole`, `userEmail`, `userPassword`) VALUES
(1, 'gomsManager', 'Manager', 'anyemail@gmail.com', 'goms123'),
(2, 'fareezM', 'Manager', 'fareez@gmail.com', 'fareez'),
(3, 'Sheikh', 'Staff', 'sheikh@gmail.com', 'sheikh');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `financialledger`
--
ALTER TABLE `financialledger`
  ADD PRIMARY KEY (`ledgerID`),
  ADD KEY `paymentID` (`paymentID`);

--
-- Indexes for table `inventoryitem`
--
ALTER TABLE `inventoryitem`
  ADD PRIMARY KEY (`itemCode`),
  ADD KEY `itemSupplierID` (`itemSupplierID`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoiceCode`),
  ADD KEY `itemCode` (`itemCode`),
  ADD KEY `supplierID` (`supplierID`);

--
-- Indexes for table `logpayment`
--
ALTER TABLE `logpayment`
  ADD PRIMARY KEY (`paymentID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `maintenanceitem`
--
ALTER TABLE `maintenanceitem`
  ADD PRIMARY KEY (`maintainedItemID`),
  ADD KEY `scheduleID` (`scheduleID`),
  ADD KEY `itemCode` (`itemCode`);

--
-- Indexes for table `maintenancerecord`
--
ALTER TABLE `maintenancerecord`
  ADD PRIMARY KEY (`recordID`),
  ADD KEY `maintainedItemID` (`maintainedItemID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `maintenanceschedule`
--
ALTER TABLE `maintenanceschedule`
  ADD PRIMARY KEY (`scheduleID`),
  ADD KEY `createdBy` (`createdBy`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplierID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `financialledger`
--
ALTER TABLE `financialledger`
  MODIFY `ledgerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventoryitem`
--
ALTER TABLE `inventoryitem`
  MODIFY `itemCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7008;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoiceCode` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logpayment`
--
ALTER TABLE `logpayment`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9006;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
