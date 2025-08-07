-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2025 at 12:28 PM
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
-- Database: `larea_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_tbl`
--

CREATE TABLE `attendance_tbl` (
  `id` int(12) NOT NULL,
  `studentName` varchar(100) NOT NULL,
  `LRN` bigint(12) NOT NULL,
  `attendance` int(12) NOT NULL,
  `date` date NOT NULL,
  `attendance_count` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_tbl`
--

INSERT INTO `attendance_tbl` (`id`, `studentName`, `LRN`, `attendance`, `date`, `attendance_count`) VALUES
(2, 'Rohannnn Rasdasd asdas', 123123123123, 1, '2025-07-26', 1),
(3, 'Charles Babage h', 123123123123, 1, '2025-07-30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `behavior_tbl`
--

CREATE TABLE `behavior_tbl` (
  `behavior_ID_PK` int(11) NOT NULL,
  `LRN` varchar(100) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `middle_name` varchar(250) DEFAULT NULL,
  `last_name` varchar(250) NOT NULL,
  `behavior_type` varchar(150) NOT NULL,
  `student_image` varchar(200) DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `date_entry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students_tbl`
--

CREATE TABLE `students_tbl` (
  `id` int(12) NOT NULL,
  `Fname` varchar(100) NOT NULL,
  `Lname` varchar(100) NOT NULL,
  `Mname` varchar(100) NOT NULL,
  `GLevel` varchar(12) NOT NULL,
  `Course` varchar(100) NOT NULL,
  `LRN` bigint(12) NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_tbl`
--

INSERT INTO `students_tbl` (`id`, `Fname`, `Lname`, `Mname`, `GLevel`, `Course`, `LRN`, `photo_path`) VALUES
(3, 'Charles', 'Babage', 'h', 'Grade 7', 'N/A', 123123123123, 'uploads/student_photos/123123123123_1753529988.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(12) NOT NULL,
  `userID_col` int(12) NOT NULL,
  `password_col` varchar(100) NOT NULL,
  `userLevel_col` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `userID_col`, `password_col`, `userLevel_col`) VALUES
(1, 123, '$2y$10$cPkmBsWgbGQbIT8fCjMHEus7eFvcA0dgro.eIMqbsYXY2Y295K.ye', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `behavior_tbl`
--
ALTER TABLE `behavior_tbl`
  ADD PRIMARY KEY (`behavior_ID_PK`);

--
-- Indexes for table `students_tbl`
--
ALTER TABLE `students_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_students_lrn` (`LRN`),
  ADD KEY `idx_students_photo` (`photo_path`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `behavior_tbl`
--
ALTER TABLE `behavior_tbl`
  MODIFY `behavior_ID_PK` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students_tbl`
--
ALTER TABLE `students_tbl`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
