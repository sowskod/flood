-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Oct 04, 2024 at 01:59 AM
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
-- Database: `ricmar`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(11) NOT NULL,
  `brgy_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `brgy_name`) VALUES
(2, 'Maasim'),
(3, 'Caingin'),
(4, 'Coral na Bato'),
(5, 'Cruz na daan'),
(6, 'Diliman'),
(7, 'Capihan'),
(8, 'Libis'),
(9, 'Lico'),
(10, 'Mabalas-balas'),
(11, 'Maguinao'),
(12, 'Paco'),
(13, 'Pansumaloc'),
(14, 'Pantubig'),
(15, 'Pulong Bayabas'),
(16, 'Pulo'),
(18, 'Pulo'),
(19, 'Banca Banca');

-- --------------------------------------------------------

--
-- Table structure for table `flood_data`
--

CREATE TABLE `flood_data` (
  `id` int(11) NOT NULL,
  `brgy_id` int(11) NOT NULL,
  `flood_date` date NOT NULL,
  `population` int(11) NOT NULL,
  `flood_level` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flood_data`
--

INSERT INTO `flood_data` (`id`, `brgy_id`, `flood_date`, `population`, `flood_level`) VALUES
(14, 7, '2024-09-25', 0, 'Low'),
(15, 7, '2024-09-25', 0, 'Low'),
(17, 7, '2024-09-23', 0, 'Normal'),
(21, 3, '2024-09-23', 0, 'Low'),
(22, 3, '2024-09-23', 0, 'Low'),
(23, 3, '2024-09-23', 0, 'Normal'),
(25, 3, '2024-09-23', 0, 'High'),
(29, 19, '2024-09-25', 0, 'Normal'),
(30, 19, '2024-09-16', 0, 'Medium'),
(31, 19, '2024-10-04', 0, 'High'),
(32, 19, '2024-10-04', 0, 'High'),
(33, 19, '2024-10-04', 0, 'High'),
(34, 19, '2024-10-04', 0, 'High'),
(35, 19, '2024-10-04', 0, 'High'),
(36, 19, '2024-10-04', 0, 'High'),
(37, 19, '2024-10-04', 0, 'High'),
(38, 19, '2024-10-04', 0, 'High'),
(39, 19, '2024-10-04', 0, 'High'),
(42, 6, '2024-09-26', 0, 'High'),
(43, 6, '2024-09-27', 0, 'Low'),
(44, 6, '2024-09-28', 0, 'Medium');

-- --------------------------------------------------------

--
-- Table structure for table `flood_dataa`
--

CREATE TABLE `flood_dataa` (
  `id` int(11) NOT NULL,
  `brgy_id` int(11) DEFAULT NULL,
  `rainfall` float DEFAULT NULL,
  `wind_speed` float DEFAULT NULL,
  `flood_occurred` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flood_dataa`
--

INSERT INTO `flood_dataa` (`id`, `brgy_id`, `rainfall`, `wind_speed`, `flood_occurred`) VALUES
(1, 2, 125, 198, NULL),
(2, 3, 125, 198, NULL),
(3, 4, 125, 198, NULL),
(4, 5, 125, 198, NULL),
(5, 6, 125, 198, NULL),
(6, 7, 125, 198, NULL),
(7, 8, 125, 198, NULL),
(8, 9, 125, 198, NULL),
(9, 10, 125, 198, NULL),
(10, 11, 125, 198, NULL),
(11, 12, 125, 198, NULL),
(12, 13, 125, 198, NULL),
(13, 14, 125, 198, NULL),
(14, 15, 125, 198, NULL),
(15, 16, 125, 198, NULL),
(16, 18, 125, 198, NULL),
(17, 19, 125, 198, NULL),
(18, 2, 245, 254, NULL),
(19, 3, 245, 254, NULL),
(20, 4, 245, 254, NULL),
(21, 5, 245, 254, NULL),
(22, 6, 245, 254, NULL),
(23, 7, 245, 254, NULL),
(24, 8, 245, 254, NULL),
(25, 9, 245, 254, NULL),
(26, 10, 245, 254, NULL),
(27, 11, 245, 254, NULL),
(28, 12, 245, 254, NULL),
(29, 13, 245, 254, NULL),
(30, 14, 245, 254, NULL),
(31, 15, 245, 254, NULL),
(32, 16, 245, 254, NULL),
(33, 18, 245, 254, NULL),
(34, 19, 245, 254, NULL),
(35, 2, 576, 765, NULL),
(36, 3, 576, 765, NULL),
(37, 4, 576, 765, NULL),
(38, 5, 576, 765, NULL),
(39, 6, 576, 765, NULL),
(40, 7, 576, 765, NULL),
(41, 8, 576, 765, NULL),
(42, 9, 576, 765, NULL),
(43, 10, 576, 765, NULL),
(44, 11, 576, 765, NULL),
(45, 12, 576, 765, NULL),
(46, 13, 576, 765, NULL),
(47, 14, 576, 765, NULL),
(48, 15, 576, 765, NULL),
(49, 16, 576, 765, NULL),
(50, 18, 576, 765, NULL),
(51, 19, 576, 765, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flood_data`
--
ALTER TABLE `flood_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brgy_id` (`brgy_id`);

--
-- Indexes for table `flood_dataa`
--
ALTER TABLE `flood_dataa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brgy_id` (`brgy_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `flood_data`
--
ALTER TABLE `flood_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `flood_dataa`
--
ALTER TABLE `flood_dataa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flood_data`
--
ALTER TABLE `flood_data`
  ADD CONSTRAINT `flood_data_ibfk_1` FOREIGN KEY (`brgy_id`) REFERENCES `barangays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `flood_dataa`
--
ALTER TABLE `flood_dataa`
  ADD CONSTRAINT `flood_dataa_ibfk_1` FOREIGN KEY (`brgy_id`) REFERENCES `barangays` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
