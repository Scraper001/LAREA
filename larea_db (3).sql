-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 09, 2025 at 05:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `anecdotal_records_tbl`
--

CREATE TABLE `anecdotal_records_tbl` (
  `record_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `LRN` bigint(12) NOT NULL,
  `record_type` varchar(50) NOT NULL DEFAULT 'Behavioral',
  `observation_title` varchar(200) NOT NULL,
  `observation_details` text NOT NULL,
  `severity_level` varchar(20) NOT NULL DEFAULT 'Low',
  `date_recorded` datetime NOT NULL DEFAULT current_timestamp(),
  `follow_up_required` tinyint(1) NOT NULL DEFAULT 0,
  `follow_up_notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anecdotal_records_tbl`
--

INSERT INTO `anecdotal_records_tbl` (`record_id`, `student_id`, `LRN`, `record_type`, `observation_title`, `observation_details`, `severity_level`, `date_recorded`, `follow_up_required`, `follow_up_notes`, `status`) VALUES
(1, 3, 123123123123, 'Behavioral', 'Positive Leadership', 'Student showed excellent leadership skills during group work in Mathematics class. Helped peers understand difficult concepts.', 'Low', '2025-08-09 22:50:10', 0, NULL, 'Active'),
(2, 3, 123123123123, 'Academic', 'Math Improvement Needed', 'Student struggles with complex word problems in Mathematics. Recommended additional practice and tutoring.', 'Medium', '2025-08-09 22:50:10', 1, NULL, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'absent',
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `hours_worked` decimal(4,2) DEFAULT NULL,
  `lunch_out` time DEFAULT NULL,
  `lunch_in` time DEFAULT NULL,
  `total_break_minutes` int(11) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_settings`
--

CREATE TABLE `attendance_settings` (
  `id` int(11) NOT NULL,
  `school_start_time` time NOT NULL DEFAULT '08:00:00',
  `school_end_time` time NOT NULL DEFAULT '17:00:00',
  `late_threshold_minutes` int(11) NOT NULL DEFAULT 15,
  `grace_period_minutes` int(11) NOT NULL DEFAULT 5,
  `lunch_start_time` time DEFAULT '12:00:00',
  `lunch_end_time` time DEFAULT '13:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_settings`
--

INSERT INTO `attendance_settings` (`id`, `school_start_time`, `school_end_time`, `late_threshold_minutes`, `grace_period_minutes`, `lunch_start_time`, `lunch_end_time`, `created_at`, `updated_at`) VALUES
(1, '08:00:00', '17:00:00', 15, 5, '12:00:00', '13:00:00', '2025-08-07 13:58:34', '2025-08-07 13:58:34');

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
-- Table structure for table `grades_tbl`
--

CREATE TABLE `grades_tbl` (
  `grade_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `LRN` bigint(12) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `assessment_type` varchar(50) NOT NULL DEFAULT 'Quiz',
  `assessment_name` varchar(200) NOT NULL,
  `grade_value` decimal(5,2) NOT NULL,
  `max_points` decimal(5,2) NOT NULL DEFAULT 100.00,
  `percentage` decimal(5,2) NOT NULL,
  `grade_status` varchar(20) NOT NULL DEFAULT 'Passed',
  `grade_category` varchar(30) NOT NULL DEFAULT 'Satisfactory',
  `grading_period` varchar(20) NOT NULL DEFAULT '1st Quarter',
  `date_recorded` datetime NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL,
  `teacher_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades_tbl`
--

INSERT INTO `grades_tbl` (`grade_id`, `student_id`, `LRN`, `subject`, `assessment_type`, `assessment_name`, `grade_value`, `max_points`, `percentage`, `grade_status`, `grade_category`, `grading_period`, `date_recorded`, `remarks`, `teacher_notes`) VALUES
(1, 3, 123123123123, 'Mathematics', 'Quiz', 'Algebra Quiz 1', 85.00, 100.00, 85.00, 'Passed', 'Very Good', '1st Quarter', '2025-08-09 22:50:10', 'Good understanding of basic algebra', NULL),
(2, 3, 123123123123, 'Mathematics', 'Exam', 'Midterm Exam', 68.50, 100.00, 68.50, 'Failed', 'Failed', '1st Quarter', '2025-08-09 22:50:10', 'Needs improvement in word problems', NULL),
(3, 3, 123123123123, 'Science', 'Quiz', 'Physics Quiz', 92.00, 100.00, 92.00, 'Passed', 'Excellent', '1st Quarter', '2025-08-09 22:50:10', 'Excellent performance', NULL),
(4, 3, 123123123123, 'English', 'Assignment', 'Essay Writing', 78.00, 100.00, 78.00, 'Passed', 'Satisfactory', '1st Quarter', '2025-08-09 22:50:10', 'Creative and well-structured', NULL),
(5, 3, 123123123123, 'Science', 'Quiz', 'asd', 100.00, 100.00, 100.00, 'Passed', 'Excellent', '1st Quarter', '2025-08-09 23:02:49', 'asd', ''),
(6, 3, 123123123123, 'Science', 'Quiz', 'asd', 100.00, 100.00, 100.00, 'Passed', 'Excellent', '1st Quarter', '2025-08-09 23:02:49', 'asd', ''),
(7, 3, 123123123123, 'Science', 'Quiz', 'asd', 100.00, 100.00, 100.00, 'Passed', 'Excellent', '1st Quarter', '2025-08-09 23:03:41', 'asd', ''),
(8, 3, 123123123123, 'Science', 'Quiz', 'asd', 100.00, 100.00, 100.00, 'Passed', 'Excellent', '1st Quarter', '2025-08-09 23:03:41', 'asd', ''),
(9, 3, 123123123123, 'Mathematics', 'Laboratory', '23', 23.00, 100.00, 23.00, 'Failed', 'Failed', '3rd Quarter', '2025-08-09 23:24:22', 'asd', '');

-- --------------------------------------------------------

--
-- Table structure for table `grade_settings_tbl`
--

CREATE TABLE `grade_settings_tbl` (
  `setting_id` int(11) NOT NULL,
  `setting_name` varchar(50) NOT NULL,
  `setting_value` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_settings_tbl`
--

INSERT INTO `grade_settings_tbl` (`setting_id`, `setting_name`, `setting_value`, `description`) VALUES
(1, 'passing_grade', '60', 'Minimum percentage required to pass'),
(2, 'excellent_grade', '90', 'Minimum percentage for Excellent rating'),
(3, 'very_good_grade', '85', 'Minimum percentage for Very Good rating'),
(4, 'good_grade', '80', 'Minimum percentage for Good rating'),
(5, 'satisfactory_grade', '85', 'Minimum percentage for Satisfactory rating'),
(6, 'needs_improvement_grade', '65', 'Minimum percentage for Needs Improvement rating'),
(7, 'outstanding_grade', '95', 'Minimum percentage for Outstanding rating'),
(8, 'very_satisfactory_grade', '90', 'Minimum percentage for Very Satisfactory rating'),
(9, 'fairly_satisfactory_grade', '80', 'Minimum percentage for Fairly Satisfactory rating'),
(10, 'did_not_meet_expectations_grade', '75', 'Minimum percentage for Did Not Meet Expectations rating');

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
-- Indexes for table `anecdotal_records_tbl`
--
ALTER TABLE `anecdotal_records_tbl`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `idx_anecdotal_student` (`student_id`),
  ADD KEY `idx_anecdotal_lrn` (`LRN`),
  ADD KEY `idx_anecdotal_type` (`record_type`),
  ADD KEY `idx_anecdotal_date` (`date_recorded`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_date` (`student_id`,`date`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_student_date` (`student_id`,`date`);

--
-- Indexes for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `grades_tbl`
--
ALTER TABLE `grades_tbl`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `idx_grades_student` (`student_id`),
  ADD KEY `idx_grades_lrn` (`LRN`),
  ADD KEY `idx_grades_subject` (`subject`),
  ADD KEY `idx_grades_period` (`grading_period`),
  ADD KEY `idx_grades_status` (`grade_status`);

--
-- Indexes for table `grade_settings_tbl`
--
ALTER TABLE `grade_settings_tbl`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

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
-- AUTO_INCREMENT for table `anecdotal_records_tbl`
--
ALTER TABLE `anecdotal_records_tbl`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `grades_tbl`
--
ALTER TABLE `grades_tbl`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `grade_settings_tbl`
--
ALTER TABLE `grade_settings_tbl`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anecdotal_records_tbl`
--
ALTER TABLE `anecdotal_records_tbl`
  ADD CONSTRAINT `anecdotal_records_tbl_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students_tbl` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `anecdotal_records_tbl_ibfk_2` FOREIGN KEY (`LRN`) REFERENCES `students_tbl` (`LRN`) ON DELETE CASCADE;

--
-- Constraints for table `grades_tbl`
--
ALTER TABLE `grades_tbl`
  ADD CONSTRAINT `grades_tbl_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students_tbl` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_tbl_ibfk_2` FOREIGN KEY (`LRN`) REFERENCES `students_tbl` (`LRN`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
