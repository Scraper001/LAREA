-- Database Update Script for Grade Management System
-- This script adds the missing tables and enhancements for the grading system

-- Create grades_tbl if it doesn't exist
CREATE TABLE IF NOT EXISTS `grades_tbl` (
  `grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `LRN` bigint(12) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `assessment_type` varchar(50) NOT NULL,
  `assessment_name` varchar(200) NOT NULL,
  `grade_value` decimal(5,2) NOT NULL,
  `max_points` decimal(5,2) NOT NULL,
  `percentage` decimal(5,2) GENERATED ALWAYS AS ((`grade_value` / `max_points`) * 100) STORED,
  `grade_category` varchar(20) GENERATED ALWAYS AS (
    CASE 
      WHEN ((`grade_value` / `max_points`) * 100) >= 90 THEN 'Excellent'
      WHEN ((`grade_value` / `max_points`) * 100) >= 85 THEN 'Very Good'
      WHEN ((`grade_value` / `max_points`) * 100) >= 80 THEN 'Good'
      WHEN ((`grade_value` / `max_points`) * 100) >= 75 THEN 'Satisfactory'
      WHEN ((`grade_value` / `max_points`) * 100) >= 70 THEN 'Needs Improvement'
      ELSE 'Failed'
    END
  ) STORED,
  `pass_fail_status` varchar(10) GENERATED ALWAYS AS (
    CASE 
      WHEN ((`grade_value` / `max_points`) * 100) >= 75 THEN 'Pass'
      ELSE 'Fail'
    END
  ) STORED,
  `grading_period` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `teacher_notes` text DEFAULT NULL,
  `date_recorded` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`grade_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_lrn` (`LRN`),
  KEY `idx_subject` (`subject`),
  KEY `idx_grading_period` (`grading_period`),
  KEY `idx_grade_category` (`grade_category`),
  KEY `idx_pass_fail` (`pass_fail_status`),
  FOREIGN KEY (`student_id`) REFERENCES `students_tbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create anecdotal_records_tbl if it doesn't exist
CREATE TABLE IF NOT EXISTS `anecdotal_records_tbl` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `LRN` bigint(12) NOT NULL,
  `record_type` varchar(50) NOT NULL,
  `observation_title` varchar(200) NOT NULL,
  `observation_details` text NOT NULL,
  `severity_level` enum('Low','Medium','High','Critical') DEFAULT 'Low',
  `follow_up_required` tinyint(1) DEFAULT 0,
  `follow_up_notes` text DEFAULT NULL,
  `date_recorded` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`record_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_lrn` (`LRN`),
  KEY `idx_record_type` (`record_type`),
  KEY `idx_severity` (`severity_level`),
  FOREIGN KEY (`student_id`) REFERENCES `students_tbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create grade_history_tbl for tracking grade improvements
CREATE TABLE IF NOT EXISTS `grade_history_tbl` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `grading_period` varchar(50) NOT NULL,
  `previous_grade` decimal(5,2) DEFAULT NULL,
  `new_grade` decimal(5,2) NOT NULL,
  `previous_percentage` decimal(5,2) DEFAULT NULL,
  `new_percentage` decimal(5,2) NOT NULL,
  `improvement` decimal(5,2) GENERATED ALWAYS AS (`new_percentage` - IFNULL(`previous_percentage`, 0)) STORED,
  `action_type` enum('CREATED','UPDATED','DELETED') NOT NULL,
  `date_changed` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`history_id`),
  KEY `idx_grade_id` (`grade_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_subject_period` (`subject`, `grading_period`),
  FOREIGN KEY (`student_id`) REFERENCES `students_tbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create grade_settings_tbl for configurable grading parameters
CREATE TABLE IF NOT EXISTS `grade_settings_tbl` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(50) NOT NULL UNIQUE,
  `setting_value` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default grade settings
INSERT INTO `grade_settings_tbl` (`setting_name`, `setting_value`, `description`) VALUES
('passing_grade', '75', 'Minimum percentage required to pass'),
('excellent_threshold', '90', 'Minimum percentage for Excellent grade'),
('very_good_threshold', '85', 'Minimum percentage for Very Good grade'),
('good_threshold', '80', 'Minimum percentage for Good grade'),
('satisfactory_threshold', '75', 'Minimum percentage for Satisfactory grade'),
('needs_improvement_threshold', '70', 'Minimum percentage for Needs Improvement grade')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Create view for grade statistics
CREATE OR REPLACE VIEW `grade_statistics_view` AS
SELECT 
    s.id as student_id,
    s.LRN,
    CONCAT(s.Fname, ' ', s.Lname) as student_name,
    s.GLevel,
    s.Course,
    g.subject,
    g.grading_period,
    COUNT(g.grade_id) as total_assessments,
    AVG(g.percentage) as average_percentage,
    MIN(g.percentage) as lowest_grade,
    MAX(g.percentage) as highest_grade,
    SUM(CASE WHEN g.pass_fail_status = 'Pass' THEN 1 ELSE 0 END) as passed_assessments,
    SUM(CASE WHEN g.pass_fail_status = 'Fail' THEN 1 ELSE 0 END) as failed_assessments,
    SUM(CASE WHEN g.grade_category = 'Excellent' THEN 1 ELSE 0 END) as excellent_grades,
    SUM(CASE WHEN g.grade_category = 'Very Good' THEN 1 ELSE 0 END) as very_good_grades,
    SUM(CASE WHEN g.grade_category = 'Good' THEN 1 ELSE 0 END) as good_grades,
    SUM(CASE WHEN g.grade_category = 'Satisfactory' THEN 1 ELSE 0 END) as satisfactory_grades,
    SUM(CASE WHEN g.grade_category = 'Needs Improvement' THEN 1 ELSE 0 END) as needs_improvement_grades,
    SUM(CASE WHEN g.grade_category = 'Failed' THEN 1 ELSE 0 END) as failed_grades
FROM students_tbl s
LEFT JOIN grades_tbl g ON s.id = g.student_id
GROUP BY s.id, s.LRN, s.Fname, s.Lname, s.GLevel, s.Course, g.subject, g.grading_period;

-- Create view for overall class performance
CREATE OR REPLACE VIEW `class_performance_view` AS
SELECT 
    subject,
    grading_period,
    COUNT(DISTINCT student_id) as total_students,
    AVG(percentage) as class_average,
    MIN(percentage) as lowest_grade,
    MAX(percentage) as highest_grade,
    COUNT(CASE WHEN pass_fail_status = 'Pass' THEN 1 END) as students_passed,
    COUNT(CASE WHEN pass_fail_status = 'Fail' THEN 1 END) as students_failed,
    (COUNT(CASE WHEN pass_fail_status = 'Pass' THEN 1 END) / COUNT(DISTINCT student_id)) * 100 as pass_rate
FROM grades_tbl
GROUP BY subject, grading_period;

-- Create triggers for grade history tracking
DELIMITER //

CREATE TRIGGER `grade_history_insert` AFTER INSERT ON `grades_tbl`
FOR EACH ROW 
BEGIN
    INSERT INTO grade_history_tbl (grade_id, student_id, subject, grading_period, new_grade, new_percentage, action_type)
    VALUES (NEW.grade_id, NEW.student_id, NEW.subject, NEW.grading_period, NEW.grade_value, NEW.percentage, 'CREATED');
END//

CREATE TRIGGER `grade_history_update` AFTER UPDATE ON `grades_tbl`
FOR EACH ROW 
BEGIN
    INSERT INTO grade_history_tbl (grade_id, student_id, subject, grading_period, previous_grade, new_grade, previous_percentage, new_percentage, action_type)
    VALUES (NEW.grade_id, NEW.student_id, NEW.subject, NEW.grading_period, OLD.grade_value, NEW.grade_value, OLD.percentage, NEW.percentage, 'UPDATED');
END//

DELIMITER ;