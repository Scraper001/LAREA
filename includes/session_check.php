<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}

// Function to check user role
function check_user_role($required_level) {
    if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != $required_level) {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Function to get user role name
function get_user_role_name($level) {
    switch ($level) {
        case 1:
            return 'Admin';
        case 2:
            return 'Teacher';
        case 3:
            return 'Student';
        default:
            return 'Unknown';
    }
}

// Function to check if user has access to a feature
function has_permission($feature) {
    $user_level = $_SESSION['user_level'] ?? 0;
    
    switch ($feature) {
        case 'user_management':
        case 'system_settings':
        case 'admin_dashboard':
            return $user_level == 1; // Admin only
        case 'teacher_dashboard':
        case 'grade_management':
        case 'attendance_taking':
            return $user_level <= 2; // Admin and Teacher
        case 'student_dashboard':
            return $user_level <= 3; // All roles
        default:
            return false;
    }
}
?>