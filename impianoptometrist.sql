-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307/
-- Generation Time: Dec 29, 2025 at 03:10 AM
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
-- Database: `impianoptometrist`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Email` varchar(30) NOT NULL,
  `password` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Email`, `password`) VALUES
('admin123@impian.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `customer_appointments`
--

CREATE TABLE `customer_appointments` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `doctor` varchar(100) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_appointments`
--

INSERT INTO `customer_appointments` (`appointment_id`, `user_id`, `appointment_date`, `appointment_time`, `doctor`, `reason`, `status`, `created_at`) VALUES
(13, 1, '2025-11-12', '10:00:00', 'Dr. Liana', 'sae', 'Completed', '2025-11-10 17:52:28'),
(14, 1, '2025-11-14', '10:00:00', 'Dr. Liana', 'check up', 'Pending', '2025-11-12 03:56:32'),
(20, 2, '2025-11-20', '09:00:00', 'Dr. Liana', 'check up', 'Upcoming', '2025-11-18 06:00:27'),
(21, 2, '2025-11-20', '10:00:00', 'Dr. Liana', '', 'Cancelled', '2025-11-18 06:43:12'),
(22, 2, '2025-11-19', '11:00:00', 'Dr. Liana', 'Eye prescription check up', 'Upcoming', '2025-11-19 03:24:13'),
(23, 2, '2025-12-03', '10:00:00', 'Dr. Liana', 'lens cleanup', 'Upcoming', '2025-12-03 15:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `ITEM_ID` varchar(15) NOT NULL,
  `ITEM_BRAND` varchar(255) DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `item_image` varchar(100) DEFAULT NULL,
  `ITEM_MATERIAL` varchar(50) DEFAULT NULL,
  `ITEM_PRICE` decimal(10,2) DEFAULT NULL,
  `ITEM_QTY` int(11) DEFAULT NULL,
  `sales_count` int(11) NOT NULL DEFAULT 0,
  `CATEGORY_ID` varchar(15) DEFAULT NULL,
  `STAFF_NRIC` varchar(15) DEFAULT NULL,
  `ITEM_STATUS` varchar(20) DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`ITEM_ID`, `ITEM_BRAND`, `item_name`, `item_image`, `ITEM_MATERIAL`, `ITEM_PRICE`, `ITEM_QTY`, `sales_count`, `CATEGORY_ID`, `STAFF_NRIC`, `ITEM_STATUS`) VALUES
('CL001', 'Softmed', 'Breathables XW', 'CL001.jpg', 'HEMA', 13.00, 5, 23, 'CAT005', '940715102384', 'Available'),
('CL002', 'Softlens', 'bausch-lomb', 'CL002.jpg', 'HEMA', 17.00, 11, 14, 'CAT005', '940715102384', 'Available'),
('CL003', 'Alcon', 'Total30', 'CL003.png', 'HEMA', 90.00, 3, 20, 'CAT005', '940715102384', 'Available'),
('CO001', 'Polaroid', 'PLD 0009', 'CO001.jpg', 'METAL', 190.00, 6, 13, 'CAT003', '940715102384', 'Available'),
('CO002', 'Cyber', 'CB18007', 'CO002.jpg', 'METAL', 248.00, 96, 14, 'CAT003', '940715102384', 'Available'),
('CO003', 'Gunnar', 'Parker', 'CO003.jpg', 'PLASTIC', 230.00, 6, 31, 'CAT003', '940715102384', 'Available'),
('F001', 'ADIDAS', 'OR0612', 'F001.jpg', 'METAL', 317.00, 6, 17, 'CAT001', '940715102384', 'Available'),
('F002', 'ADIDAS', 'OR6921', 'F002.jpg', 'PLASTIC', 301.00, 95, 18, 'CAT001', '940715102384', 'Unavailable'),
('F003', 'AIRLITE', 'AR612S', 'F003.jpg', 'PLASTIC', 126.80, 8, 21, 'CAT001', '940715102384', 'Available'),
('F004', 'AIRLITE', 'AR6025', 'F004.jpg', 'STAINLESS STEEL', 367.93, 6, 30, 'CAT001', '940715102384', 'Available'),
('F005', 'ALAIN DELON', 'AD82SA', 'F005.jpg', 'PLASTIC', 325.00, 96, 30, 'CAT001', '940715102384', 'Available'),
('F007', 'ALAIN DELON', 'AD72X5', 'F006.jpg', 'TITANIUM', 330.00, 6, 31, 'CAT001', '940715102384', 'Available'),
('F008', 'ALAIN DELON', 'AD7S26', 'F008.jpg', 'ACETATE', 499.00, 96, 29, 'CAT001', '940715102384', 'Available'),
('F009', 'ALFIO RALDO', 'AF126S', 'F009.jpg', 'PLASTIC', 230.00, 6, 37, 'CAT001', '940715102384', 'Available'),
('F010', 'ALFIO RALDO', 'AF61X2', 'F010.png', 'TITANIUM', 400.00, 96, 31, 'CAT001', '940715102384', 'Available'),
('F020', 'CHARMANT', 'Brille CH10876', 'F020.jpg', 'TITANIUM', 550.00, 6, 29, 'CAT001', '980109105278', 'Available'),
('F051', 'Seiko', 'Titanium T 674', 'F051.jpg', 'TITANIUM', 228.48, 6, 30, 'CAT001', '980109105278', 'Available'),
('F052', 'Seiko', 'T-7020 CO30', 'F052.jpg', 'TITANIUM', 216.00, 6, 29, 'CAT001', '980109105278', 'Available'),
('F053', 'Seiko', 'SJ9012 Vintage', 'F053.jpg', 'TITANIUM', 343.68, 30, 29, 'CAT001', '980109105278', 'Available'),
('F100', 'RENOMA PARIS', '25-1267 COL-2', 'F100.jpg', 'TITANIUM', 499.00, 52, 29, 'CAT001', '980109105278', 'Available'),
('F801', 'Ray-Ban', 'RX6495', 'F801.png', 'METAL', 276.00, 6, 29, 'CAT001', '940715102384', 'Available'),
('F802', 'Ralph', 'RA7158U', 'F802.png', 'PLASTIC', 230.00, 6, 29, 'CAT001', '940715102384', 'Available'),
('F803', 'Masuraga', 'MA7126', 'F803.png', 'METAL', 336.00, 6, 31, 'CAT001', '940715102384', 'Available'),
('F804', 'Adidas', 'OR5076', 'F804.png', 'PLASTIC', 450.00, 6, 30, 'CAT001', '940715102384', 'Available'),
('F805', 'Prada', 'VPR 17W', 'F805.png', 'ACETATE', 420.00, 11, 30, 'CAT001', '940715102384', 'Available'),
('F806', 'MASURAGA', 'MA1262', 'F806.png', NULL, 10.00, 6, 5, 'CAT001', NULL, 'Available'),
('L001', 'HOYA', NULL, NULL, '1.55 STELLIFY', 174.00, 6, 29, 'CAT002', '940715102384', 'Available'),
('L002', 'HOYA', NULL, NULL, '1.56 EYEMEDIC 2ETC', 107.89, -7, 29, 'CAT002', '940715102384', 'Available'),
('ST001', 'CityLens', NULL, NULL, 'ELASTIC', 9.99, -14, 29, 'CAT004', '980109105278', 'Available'),
('ST002', 'CityLens', NULL, NULL, 'Neoprene', 4.99, -14, 29, 'CAT004', '980109105278', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE `item_category` (
  `CATEGORY_ID` varchar(15) NOT NULL,
  `CATEGORY_NAME` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_category`
--

INSERT INTO `item_category` (`CATEGORY_ID`, `CATEGORY_NAME`) VALUES
('CAT001', 'Frame'),
('CAT002', 'Lense'),
('CAT003', 'Clip On'),
('CAT004', 'Strap'),
('CAT005', 'Contact Lense');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `shipping_option` varchar(50) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `order_status` varchar(50) DEFAULT 'Processing',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `shipping_option`, `shipping_address`, `order_status`, `order_date`) VALUES
(16, 2, 560.00, 'pickup', NULL, 'Completed', '2025-11-07 19:50:47'),
(17, 1, 13.00, 'pickup', NULL, 'Completed', '2025-11-08 09:57:16'),
(18, 2, 640.00, 'pickup', NULL, 'Completed', '2025-11-09 04:10:34'),
(19, 2, 400.00, 'pickup', NULL, 'Completed', '2025-11-11 04:36:18'),
(20, 2, 302.50, 'delivery', 'Raja Fadzli Azri Shah Bin Raja Muazam Shah\nBlok A Kelana Centre Point 501 Jln Ss 7/19 Ss7 Petaling Jaya\nPetaling Jaya, Selangor 40150', 'Completed', '2025-11-11 04:50:04'),
(21, 2, 336.00, 'pickup', NULL, 'Completed', '2025-11-11 05:24:51'),
(22, 2, 315.00, 'pickup', NULL, 'Cancelled', '2025-11-11 08:13:11'),
(23, 2, 619.00, 'pickup', NULL, 'Processing', '2025-11-11 08:17:39'),
(24, 1, 1380.00, 'pickup', NULL, 'Processing', '2025-11-12 03:52:49'),
(29, 2, 332.00, 'delivery', 'Raja Fadzli Azri Shah Raja Muazam Shah\nBlok A Kelana Centre Point 501 Jln Ss 7/19 Ss7 Petaling Jaya\nPetaling Jaya, Selangor 40150', 'Processing', '2025-11-16 08:24:05'),
(63, 2, 248.00, 'pickup', NULL, 'Completed', '2025-11-18 05:46:58'),
(64, 2, 353.68, 'delivery', 'Raja Fadzli Azri Shah Raja Muazam Shah\nBlok A Kelana Centre Point 501 Jln Ss 7/19 Ss7 Petaling Jaya\nPetaling Jaya, Selangor 40150', 'Processing', '2025-11-18 05:51:01'),
(65, 2, 483.60, 'pickup', NULL, 'Processing', '2025-11-19 03:21:59'),
(66, 2, 126.80, 'pickup', NULL, 'Completed', '2025-11-19 03:22:31'),
(67, 2, 126.80, 'pickup', NULL, 'Processing', '2025-11-19 03:45:25'),
(68, 2, 263.60, 'delivery', 'Raja Fadzli Azri Shah Raja Muazam Shah\nBlok A Kelana Centre Point 501 Jln Ss 7/19 Ss7 Petaling Jaya\nPetaling Jaya, Selangor 40150', 'Processing', '2025-12-03 15:32:13'),
(69, 2, 253.60, 'pickup', NULL, 'Processing', '2025-12-03 15:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `quantity`, `price_per_item`) VALUES
(23, 16, 'F007', 1, 330.00),
(24, 16, 'F009', 1, 230.00),
(25, 17, 'CL001', 1, 13.00),
(26, 18, 'CO001', 1, 190.00),
(27, 18, 'F804', 1, 450.00),
(28, 19, 'F010', 1, 400.00),
(29, 20, 'F002', 1, 292.50),
(30, 21, 'F803', 1, 336.00),
(31, 22, 'F001', 1, 315.00),
(32, 23, 'F003', 5, 123.80),
(33, 24, 'F009', 6, 230.00),
(41, 29, 'F005', 1, 322.00),
(76, 63, 'CO002', 1, 248.00),
(77, 64, 'F053', 1, 343.68),
(78, 65, 'F009', 1, 230.00),
(79, 65, 'F003', 2, 126.80),
(80, 66, 'F003', 1, 126.80),
(81, 67, 'F003', 1, 126.80),
(82, 68, 'F003', 2, 126.80),
(83, 69, 'F003', 2, 126.80);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `SALES_ID` varchar(15) NOT NULL,
  `CUST_NICKNAME` varchar(255) NOT NULL,
  `CUST_PHONE` varchar(12) DEFAULT NULL,
  `ITEM_ID` varchar(15) NOT NULL,
  `QTY` decimal(2,0) DEFAULT NULL,
  `PRICE` decimal(6,2) DEFAULT NULL,
  `STAFF_NRIC` varchar(15) NOT NULL,
  `SALES_DATE` date DEFAULT NULL,
  `SALES_TIME` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_cart`
--

CREATE TABLE `saved_cart` (
  `user_id` int(11) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `STAFF_NRIC` varchar(15) NOT NULL,
  `STAFF_FNAME` varchar(255) NOT NULL,
  `STAFF_LNAME` varchar(255) NOT NULL,
  `STAFF_PHONE` varchar(12) NOT NULL,
  `STAFF_HIRE_DATE` date DEFAULT NULL,
  `STAFF_DATE_OF_BIRTH` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `STAFF_STATUS` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`STAFF_NRIC`, `STAFF_FNAME`, `STAFF_LNAME`, `STAFF_PHONE`, `STAFF_HIRE_DATE`, `STAFF_DATE_OF_BIRTH`, `email`, `password`, `STAFF_STATUS`) VALUES
('940715102384', 'Nur Liana', 'Amir', '01167234981', '2019-08-12', '1994-07-15', 'liana@gmail.com', '$2y$10$UL5bpPRhFNEQvtuPRPL0AOkK1GRSt0.n7fPhc.Oc914aCtuVnlpSy', 'Active'),
('971013101253', 'amin faisal', 'bin Tahmir', '01016535213', '2025-11-19', '1997-10-13', 'aminfaisal123@gmail.com', '$2y$10$lE3wzlMsP8zEozdOdNbw0eusv6xCLZtTMCzFuiIG02BVB3Q/vOJMa', 'Resigned'),
('980109105278', 'Muhamamd Izzat', 'Amir', '01283561042', '2022-03-27', '1998-01-09', 'izzat@gmail.com', '$2y$10$DxPdoQ9ETCoN2p9aKQI.QeEBv/.QagD1Sr7PUOUdM.9LvFbf7CXi2', 'Resigned');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `eye_power_left` varchar(10) DEFAULT NULL,
  `eye_power_right` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone_number`, `profile_image`, `gender`, `password`, `eye_power_left`, `eye_power_right`) VALUES
(1, 'Muhammad Amin', 'Faisal Halim', 'aminfaisal123@yahoo.com', '0106416234', NULL, 'other', '$2y$10$osRztHeP8yebEG8F8Hw0IuZ.UfflA8fqwwsSvDhQm8rWGZJeT313O', NULL, NULL),
(2, 'Raja Fadzli Azri Shah', 'Raja Muazam Shah', 'azrishah1212@gmail.com', '01084069127', 'profile_2.png', 'male', '$2y$10$ksO6rNWwxFEqIEePkBg9FuPZMua/bQIQXyxxxA5.OggoFpJGrkp8y', '+3.00 D', '+3.00 D'),
(8, 'Aisyah', 'Fahmi', 'aisyah@gmail.com', '0107523512', 'profile_8.png', 'other', '$2y$10$jpETiNE0woKOfq7GJKN.b.7K.XQv5CQ2mpaFSnCLM5lXRqWeI5YF2', NULL, NULL),
(9, 'Rizq', 'Syah', 'rizqsyah@gmail.com', '01075657534', NULL, 'female', '$2y$10$e82RibC/1Co2LsBCK9OQbuCDG.sK3U.rQaQiqvzhWc0hXZawUHSkq', NULL, NULL),
(10, 'aish', 'fahmi', 'fahmi@gmail.com', '01051235123', NULL, 'female', '$2y$10$BrwwbsBLmp97X83jc4XU4.NgVUfGSVuK9GujCxpeuxNC3G71JPvZW', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Email`),
  ADD UNIQUE KEY `password` (`password`);

--
-- Indexes for table `customer_appointments`
--
ALTER TABLE `customer_appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`ITEM_ID`),
  ADD KEY `FK_ITEM_ITEM_CATEGORY` (`CATEGORY_ID`),
  ADD KEY `FK_ITEM_STAFF` (`STAFF_NRIC`);

--
-- Indexes for table `item_category`
--
ALTER TABLE `item_category`
  ADD PRIMARY KEY (`CATEGORY_ID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`SALES_ID`,`ITEM_ID`,`STAFF_NRIC`),
  ADD KEY `FK_SALES_STAFF` (`STAFF_NRIC`),
  ADD KEY `FK_SALES_ITEM` (`ITEM_ID`);

--
-- Indexes for table `saved_cart`
--
ALTER TABLE `saved_cart`
  ADD PRIMARY KEY (`user_id`,`item_id`),
  ADD KEY `fk_saved_cart_item` (`item_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`STAFF_NRIC`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer_appointments`
--
ALTER TABLE `customer_appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_appointments`
--
ALTER TABLE `customer_appointments`
  ADD CONSTRAINT `customer_appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `FK_ITEM_ITEM_CATEGORY` FOREIGN KEY (`CATEGORY_ID`) REFERENCES `item_category` (`CATEGORY_ID`),
  ADD CONSTRAINT `FK_ITEM_STAFF` FOREIGN KEY (`STAFF_NRIC`) REFERENCES `staff` (`STAFF_NRIC`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `FK_SALES_ITEM` FOREIGN KEY (`ITEM_ID`) REFERENCES `item` (`ITEM_ID`),
  ADD CONSTRAINT `FK_SALES_STAFF` FOREIGN KEY (`STAFF_NRIC`) REFERENCES `staff` (`STAFF_NRIC`);

--
-- Constraints for table `saved_cart`
--
ALTER TABLE `saved_cart`
  ADD CONSTRAINT `fk_saved_cart_item` FOREIGN KEY (`item_id`) REFERENCES `item` (`ITEM_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_saved_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
