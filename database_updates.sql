-- Database updates for Parent Account System and Admin User Management
-- Use this file to update existing LAREA_DB

-- 1. Add parent-child relationship table
CREATE TABLE IF NOT EXISTS `parent_child_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_user_id` int(12) NOT NULL,
  `student_id` int(12) NOT NULL,
  `relationship_type` varchar(50) NOT NULL DEFAULT 'Parent',
  `is_primary_contact` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_parent_student` (`parent_user_id`, `student_id`),
  KEY `idx_parent_user` (`parent_user_id`),
  KEY `idx_student` (`student_id`),
  CONSTRAINT `fk_parent_user` FOREIGN KEY (`parent_user_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_relationship` FOREIGN KEY (`student_id`) REFERENCES `students_tbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Add user profile information table for extended user details
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(12) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_profile` (`user_id`),
  KEY `idx_user_status` (`status`),
  CONSTRAINT `fk_user_profile` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Insert sample admin user (userLevel_col = 2 for admin)
INSERT IGNORE INTO `tbl_user` (`userID_col`, `password_col`, `userLevel_col`) VALUES 
(999, '$2y$10$cPkmBsWgbGQbIT8fCjMHEus7eFvcA0dgro.eIMqbsYXY2Y295K.ye', 2);

-- 4. Insert admin profile
INSERT IGNORE INTO `user_profiles` (`user_id`, `first_name`, `last_name`, `email`) VALUES 
((SELECT id FROM tbl_user WHERE userID_col = 999), 'System', 'Administrator', 'admin@larea.edu');

-- 5. Insert sample parent user (userLevel_col = 3 for parent)
INSERT IGNORE INTO `tbl_user` (`userID_col`, `password_col`, `userLevel_col`) VALUES 
(777, '$2y$10$cPkmBsWgbGQbIT8fCjMHEus7eFvcA0dgro.eIMqbsYXY2Y295K.ye', 3);

-- 6. Insert parent profile
INSERT IGNORE INTO `user_profiles` (`user_id`, `first_name`, `last_name`, `email`) VALUES 
((SELECT id FROM tbl_user WHERE userID_col = 777), 'John', 'Doe', 'john.doe@parent.com');

-- 7. Create sample parent-child relationship (assuming student with id=3 exists)
INSERT IGNORE INTO `parent_child_relationships` (`parent_user_id`, `student_id`, `relationship_type`, `is_primary_contact`) VALUES 
((SELECT id FROM tbl_user WHERE userID_col = 777), 3, 'Father', 1);

-- Note: Default password for both admin (999) and parent (777) is 'password123'
-- User levels: 1=Teacher/Staff, 2=Admin, 3=Parent