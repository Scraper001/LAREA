-- Enhanced Attendance System Database Schema
-- This script creates the enhanced tables for the attendance system

-- First, create a unified users table if it doesn't exist
-- This will replace the need for separate students_tbl
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `Fname` varchar(100) NOT NULL,
  `Lname` varchar(100) NOT NULL,
  `Mname` varchar(100) DEFAULT NULL,
  `GLevel` varchar(12) NOT NULL,
  `Course` varchar(100) DEFAULT 'N/A',
  `LRN` bigint(12) NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `user_type` enum('student','teacher','admin') DEFAULT 'student',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_users_lrn` (`LRN`),
  KEY `idx_users_type` (`user_type`),
  KEY `idx_users_grade` (`GLevel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create enhanced attendance table
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `student_id` int(12) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') DEFAULT 'absent',
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `hours_worked` decimal(4,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(12) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_date` (`student_id`, `date`),
  KEY `idx_attendance_date` (`date`),
  KEY `idx_attendance_status` (`status`),
  FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create attendance settings table
CREATE TABLE IF NOT EXISTS `attendance_settings` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `school_start_time` time DEFAULT '08:00:00',
  `school_end_time` time DEFAULT '17:00:00',
  `late_threshold_minutes` int(3) DEFAULT 15,
  `half_day_hours` decimal(4,2) DEFAULT 4.00,
  `full_day_hours` decimal(4,2) DEFAULT 8.00,
  `break_duration_minutes` int(3) DEFAULT 60,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default attendance settings
INSERT INTO `attendance_settings` (`school_start_time`, `school_end_time`, `late_threshold_minutes`, `half_day_hours`, `full_day_hours`, `break_duration_minutes`, `active`) 
VALUES ('08:00:00', '17:00:00', 15, 4.00, 8.00, 60, 1)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Migrate existing students data to users table if students_tbl exists
INSERT IGNORE INTO `users` (`Fname`, `Lname`, `Mname`, `GLevel`, `Course`, `LRN`, `photo_path`, `user_type`)
SELECT `Fname`, `Lname`, `Mname`, `GLevel`, `Course`, `LRN`, `photo_path`, 'student'
FROM `students_tbl`
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'students_tbl');

-- Migrate existing attendance data if attendance_tbl exists
INSERT IGNORE INTO `attendance` (`student_id`, `date`, `status`)
SELECT u.id, a.date, CASE WHEN a.attendance = 1 THEN 'present' ELSE 'absent' END
FROM `attendance_tbl` a
JOIN `users` u ON u.LRN = a.LRN
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'attendance_tbl');