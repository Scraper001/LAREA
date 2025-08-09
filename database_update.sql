-- Database update for Grade Management System
-- Add these tables to your existing LAREA_DB database

-- Grades table for storing student grades
CREATE TABLE `grades_tbl` (
  `grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `LRN` bigint(12) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `assessment_type` varchar(50) NOT NULL DEFAULT 'Quiz',
  `assessment_name` varchar(200) NOT NULL,
  `grade_value` decimal(5,2) NOT NULL,
  `max_points` decimal(5,2) NOT NULL DEFAULT 100.00,
  `grading_period` varchar(20) NOT NULL DEFAULT '1st Quarter',
  `date_recorded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remarks` text DEFAULT NULL,
  `teacher_notes` text DEFAULT NULL,
  PRIMARY KEY (`grade_id`),
  KEY `idx_grades_student` (`student_id`),
  KEY `idx_grades_lrn` (`LRN`),
  KEY `idx_grades_subject` (`subject`),
  KEY `idx_grades_period` (`grading_period`),
  FOREIGN KEY (`student_id`) REFERENCES `students_tbl`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`LRN`) REFERENCES `students_tbl`(`LRN`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Anecdotal records table for behavioral and academic observations
CREATE TABLE `anecdotal_records_tbl` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `LRN` bigint(12) NOT NULL,
  `record_type` varchar(50) NOT NULL DEFAULT 'Behavioral',
  `observation_title` varchar(200) NOT NULL,
  `observation_details` text NOT NULL,
  `severity_level` varchar(20) NOT NULL DEFAULT 'Low',
  `date_recorded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `follow_up_required` tinyint(1) NOT NULL DEFAULT 0,
  `follow_up_notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`record_id`),
  KEY `idx_anecdotal_student` (`student_id`),
  KEY `idx_anecdotal_lrn` (`LRN`),
  KEY `idx_anecdotal_type` (`record_type`),
  KEY `idx_anecdotal_date` (`date_recorded`),
  FOREIGN KEY (`student_id`) REFERENCES `students_tbl`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`LRN`) REFERENCES `students_tbl`(`LRN`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data for testing (optional)
-- You can remove this section if you don't want sample data

-- Sample grades for existing student (LRN: 123123123123)
INSERT INTO `grades_tbl` (`student_id`, `LRN`, `subject`, `assessment_type`, `assessment_name`, `grade_value`, `max_points`, `grading_period`, `remarks`) VALUES
(3, 123123123123, 'Mathematics', 'Quiz', 'Algebra Quiz 1', 85.00, 100.00, '1st Quarter', 'Good understanding of basic algebra'),
(3, 123123123123, 'Mathematics', 'Exam', 'Midterm Exam', 78.50, 100.00, '1st Quarter', 'Needs improvement in word problems'),
(3, 123123123123, 'Science', 'Quiz', 'Physics Quiz', 92.00, 100.00, '1st Quarter', 'Excellent performance'),
(3, 123123123123, 'English', 'Assignment', 'Essay Writing', 88.00, 100.00, '1st Quarter', 'Creative and well-structured');

-- Sample anecdotal records
INSERT INTO `anecdotal_records_tbl` (`student_id`, `LRN`, `record_type`, `observation_title`, `observation_details`, `severity_level`, `follow_up_required`) VALUES
(3, 123123123123, 'Behavioral', 'Positive Leadership', 'Student showed excellent leadership skills during group work in Mathematics class. Helped peers understand difficult concepts.', 'Low', 0),
(3, 123123123123, 'Academic', 'Improvement Needed', 'Student struggles with reading comprehension in English class. Recommended additional reading practice.', 'Medium', 1);