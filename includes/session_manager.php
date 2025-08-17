<?php
// Session management and role-based access control

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user level
 */
function getUserLevel() {
    return isset($_SESSION['user_level']) ? $_SESSION['user_level'] : null;
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Check if user has specific role
 */
function hasRole($level) {
    return getUserLevel() == $level;
}

/**
 * Check if user is admin (level 2)
 */
function isAdmin() {
    return hasRole(2);
}

/**
 * Check if user is parent (level 3)
 */
function isParent() {
    return hasRole(3);
}

/**
 * Check if user is teacher/staff (level 1)
 */
function isTeacher() {
    return hasRole(1);
}

/**
 * Require login and redirect if not logged in
 */
function requireLogin($redirect_to = '../auth/login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_to");
        exit();
    }
}

/**
 * Require specific role and redirect if unauthorized
 */
function requireRole($level, $redirect_to = '../auth/login.php') {
    requireLogin($redirect_to);
    if (!hasRole($level)) {
        header("Location: ../users/unauthorized.php");
        exit();
    }
}

/**
 * Require admin access
 */
function requireAdmin($redirect_to = '../auth/login.php') {
    requireRole(2, $redirect_to);
}

/**
 * Require parent access
 */
function requireParent($redirect_to = '../auth/login.php') {
    requireRole(3, $redirect_to);
}

/**
 * Require teacher access
 */
function requireTeacher($redirect_to = '../auth/login.php') {
    requireRole(1, $redirect_to);
}

/**
 * Get user profile information
 */
function getUserProfile($user_id = null) {
    if (!$user_id) {
        $user_id = getCurrentUserId();
    }
    
    if (!$user_id) {
        return null;
    }
    
    include_once dirname(__DIR__) . '/connection/conn.php';
    $conn = conn();
    
    $sql = "SELECT up.*, tu.userID_col, tu.userLevel_col 
            FROM user_profiles up 
            JOIN tbl_user tu ON up.user_id = tu.id 
            WHERE up.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $profile = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $profile = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $profile;
}

/**
 * Get role name from level
 */
function getRoleName($level) {
    switch ($level) {
        case 1: return 'Teacher/Staff';
        case 2: return 'Administrator';
        case 3: return 'Parent';
        default: return 'Unknown';
    }
}
?>