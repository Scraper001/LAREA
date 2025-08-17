-- Database enhancement script for behavior_tbl
-- This script enhances the existing behavior_tbl to support comprehensive behavior management

-- First, let's add the missing columns to behavior_tbl to make it more comprehensive
ALTER TABLE behavior_tbl 
ADD COLUMN IF NOT EXISTS student_id INT(11) NULL AFTER behavior_ID_PK,
ADD COLUMN IF NOT EXISTS severity_level VARCHAR(20) DEFAULT 'Low' AFTER behavior_type,
ADD COLUMN IF NOT EXISTS behavior_category VARCHAR(50) DEFAULT 'General' AFTER severity_level,
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Active' AFTER behavior_category,
ADD COLUMN IF NOT EXISTS follow_up_required TINYINT(1) DEFAULT 0 AFTER status,
ADD COLUMN IF NOT EXISTS follow_up_notes TEXT NULL AFTER follow_up_required,
ADD COLUMN IF NOT EXISTS recorded_by INT(11) NULL AFTER follow_up_notes,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER date_entry;

-- Add proper foreign key relationships
ALTER TABLE behavior_tbl 
ADD CONSTRAINT fk_behavior_student_id FOREIGN KEY (student_id) REFERENCES students_tbl(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_behavior_lrn FOREIGN KEY (LRN) REFERENCES students_tbl(LRN) ON DELETE CASCADE;

-- Add indexes for better performance
ALTER TABLE behavior_tbl 
ADD INDEX idx_behavior_student_id (student_id),
ADD INDEX idx_behavior_lrn (LRN),
ADD INDEX idx_behavior_type (behavior_type),
ADD INDEX idx_behavior_category (behavior_category),
ADD INDEX idx_behavior_severity (severity_level),
ADD INDEX idx_behavior_status (status),
ADD INDEX idx_behavior_date (date_entry);

-- Create behavior categories reference table for consistent categorization
CREATE TABLE IF NOT EXISTS behavior_categories_tbl (
    category_id INT(11) NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    category_type ENUM('Positive', 'Negative', 'Neutral') NOT NULL DEFAULT 'Neutral',
    description TEXT NULL,
    color_code VARCHAR(7) DEFAULT '#6B7280',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (category_id),
    UNIQUE KEY unique_category_name (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default behavior categories
INSERT INTO behavior_categories_tbl (category_name, category_type, description, color_code) VALUES
('Academic Excellence', 'Positive', 'Outstanding academic performance and achievements', '#10B981'),
('Leadership', 'Positive', 'Demonstrates leadership qualities and helps others', '#3B82F6'),
('Participation', 'Positive', 'Active participation in class and school activities', '#8B5CF6'),
('Respect', 'Positive', 'Shows respect to teachers, peers, and school property', '#06B6D4'),
('Punctuality', 'Positive', 'Consistently arrives on time and meets deadlines', '#84CC16'),
('Tardiness', 'Negative', 'Frequent late arrivals or missed deadlines', '#F59E0B'),
('Disruptive Behavior', 'Negative', 'Behavior that disrupts the learning environment', '#EF4444'),
('Academic Concerns', 'Negative', 'Issues with academic performance or effort', '#F97316'),
('Violation of Rules', 'Negative', 'Breaking school rules or policies', '#DC2626'),
('Attendance Issues', 'Negative', 'Excessive absences or truancy', '#7C2D12'),
('General Observation', 'Neutral', 'General behavioral observation or note', '#6B7280'),
('Follow-up Required', 'Neutral', 'Requires additional attention or follow-up', '#4B5563')
ON DUPLICATE KEY UPDATE category_name = VALUES(category_name);

-- Create behavior severity levels reference table
CREATE TABLE IF NOT EXISTS behavior_severity_tbl (
    severity_id INT(11) NOT NULL AUTO_INCREMENT,
    severity_name VARCHAR(20) NOT NULL,
    severity_level INT(2) NOT NULL,
    description TEXT NULL,
    color_code VARCHAR(7) DEFAULT '#6B7280',
    requires_parent_notification TINYINT(1) DEFAULT 0,
    requires_admin_notification TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    PRIMARY KEY (severity_id),
    UNIQUE KEY unique_severity_name (severity_name),
    UNIQUE KEY unique_severity_level (severity_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default severity levels
INSERT INTO behavior_severity_tbl (severity_name, severity_level, description, color_code, requires_parent_notification, requires_admin_notification) VALUES
('Low', 1, 'Minor behavioral observation, no immediate action required', '#10B981', 0, 0),
('Medium', 2, 'Moderate concern that may require teacher intervention', '#F59E0B', 0, 0),
('High', 3, 'Serious behavioral issue requiring immediate attention', '#EF4444', 1, 0),
('Critical', 4, 'Severe behavioral incident requiring administrative action', '#DC2626', 1, 1)
ON DUPLICATE KEY UPDATE severity_name = VALUES(severity_name);

-- Update existing records to populate student_id field
UPDATE behavior_tbl b 
SET student_id = (
    SELECT s.id 
    FROM students_tbl s 
    WHERE s.LRN = b.LRN 
    LIMIT 1
) 
WHERE student_id IS NULL AND LRN IS NOT NULL;

-- Update existing records to set default category
UPDATE behavior_tbl 
SET behavior_category = 'General Observation' 
WHERE behavior_category IS NULL OR behavior_category = '';

-- Update existing records to set proper severity based on behavior_type
UPDATE behavior_tbl 
SET severity_level = CASE 
    WHEN behavior_type = 'Commendable' THEN 'Low'
    WHEN behavior_type = 'Needs Improvement' THEN 'Medium'
    WHEN behavior_type = 'Violation' THEN 'High'
    ELSE 'Low'
END
WHERE severity_level = 'Low' OR severity_level IS NULL;