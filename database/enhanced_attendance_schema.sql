-- Enhanced Attendance System Database Schema
-- This script updates the existing database with enhanced features

-- Create attendance settings table for system configuration
CREATE TABLE IF NOT EXISTS `attendance_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_start_time` time NOT NULL DEFAULT '08:00:00',
  `school_end_time` time NOT NULL DEFAULT '17:00:00',
  `break_start_time` time DEFAULT '12:00:00',
  `break_end_time` time DEFAULT '13:00:00',
  `late_threshold_minutes` int(11) DEFAULT 15,
  `overtime_threshold_minutes` int(11) DEFAULT 30,
  `half_day_hours` decimal(4,2) DEFAULT 4.00,
  `full_day_hours` decimal(4,2) DEFAULT 8.00,
  `academic_year` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default settings
INSERT INTO `attendance_settings` 
(`school_start_time`, `school_end_time`, `break_start_time`, `break_end_time`, `late_threshold_minutes`, `overtime_threshold_minutes`, `academic_year`) 
VALUES 
('08:00:00', '17:00:00', '12:00:00', '13:00:00', 15, 30, '2024-2025')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Create enhanced attendance table
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused','half_day') NOT NULL DEFAULT 'absent',
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `break_start` time DEFAULT NULL,
  `break_end` time DEFAULT NULL,
  `total_hours` decimal(4,2) DEFAULT NULL,
  `overtime_hours` decimal(4,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_date_unique` (`student_id`, `date`),
  KEY `idx_attendance_date` (`date`),
  KEY `idx_attendance_status` (`status`),
  KEY `idx_attendance_student` (`student_id`),
  KEY `idx_attendance_recorded_by` (`recorded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create holiday management table
CREATE TABLE IF NOT EXISTS `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `type` enum('national','school','other') DEFAULT 'school',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `holiday_date_unique` (`date`),
  KEY `idx_holiday_date` (`date`),
  KEY `idx_holiday_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create attendance reports table for saved reports
CREATE TABLE IF NOT EXISTS `attendance_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_name` varchar(255) NOT NULL,
  `report_type` enum('daily','weekly','monthly','custom') NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `grade_filter` varchar(50) DEFAULT NULL,
  `student_filter` varchar(255) DEFAULT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_report_type` (`report_type`),
  KEY `idx_report_date` (`date_from`, `date_to`),
  KEY `idx_report_generated_by` (`generated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Migrate existing attendance data from attendance_tbl to new attendance table
INSERT INTO `attendance` (`student_id`, `date`, `status`, `created_at`)
SELECT 
    (SELECT id FROM students_tbl WHERE LRN = attendance_tbl.LRN LIMIT 1) as student_id,
    `date`,
    CASE 
        WHEN `attendance` = 1 THEN 'present' 
        ELSE 'absent' 
    END as status,
    NOW() as created_at
FROM `attendance_tbl` 
WHERE NOT EXISTS (
    SELECT 1 FROM `attendance` a 
    WHERE a.student_id = (SELECT id FROM students_tbl WHERE LRN = attendance_tbl.LRN LIMIT 1) 
    AND a.date = attendance_tbl.date
);

-- Add indexes to existing tables for better performance
ALTER TABLE `students_tbl` 
ADD INDEX IF NOT EXISTS `idx_students_grade` (`GLevel`),
ADD INDEX IF NOT EXISTS `idx_students_name` (`Lname`, `Fname`);

ALTER TABLE `behavior_tbl` 
ADD INDEX IF NOT EXISTS `idx_behavior_lrn` (`LRN`),
ADD INDEX IF NOT EXISTS `idx_behavior_date` (`date_entry`),
ADD INDEX IF NOT EXISTS `idx_behavior_type` (`behavior_type`);

-- Create view for easy student lookup with attendance
CREATE OR REPLACE VIEW `student_attendance_view` AS
SELECT 
    s.id as student_id,
    s.Fname,
    s.Lname,
    s.Mname,
    s.GLevel,
    s.Course,
    s.LRN,
    s.photo_path,
    a.date as attendance_date,
    a.status,
    a.time_in,
    a.time_out,
    a.total_hours,
    a.overtime_hours,
    a.remarks
FROM students_tbl s
LEFT JOIN attendance a ON s.id = a.student_id;