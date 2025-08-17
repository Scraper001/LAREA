<?php
header('Content-Type: application/json');

include "../../includes/session_manager.php";
requireAdmin(); // Ensure only admins can access this

include "../../connection/conn.php";
$conn = conn();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_user':
        addUser($conn);
        break;
    case 'edit_user':
        editUser($conn);
        break;
    case 'toggle_user_status':
        toggleUserStatus($conn);
        break;
    case 'delete_user':
        deleteUser($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function addUser($conn) {
    // Validate required fields
    $required_fields = ['firstName', 'lastName', 'userID', 'userLevel'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            return;
        }
    }
    
    $firstName = mysqli_real_escape_string($conn, trim($_POST['firstName']));
    $lastName = mysqli_real_escape_string($conn, trim($_POST['lastName']));
    $middleName = mysqli_real_escape_string($conn, trim($_POST['middleName'] ?? ''));
    $userID = (int)$_POST['userID'];
    $userLevel = (int)$_POST['userLevel'];
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    
    // Validate user level
    if (!in_array($userLevel, [1, 2, 3])) {
        echo json_encode(['success' => false, 'message' => 'Invalid user level']);
        return;
    }
    
    // Check if userID already exists
    $check_sql = "SELECT COUNT(*) as count FROM tbl_user WHERE userID_col = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result)['count'];
    
    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'User ID already exists']);
        return;
    }
    
    // Default password (should be changed by user)
    $default_password = 'password123';
    $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert into tbl_user
        $user_sql = "INSERT INTO tbl_user (userID_col, password_col, userLevel_col) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $user_sql);
        mysqli_stmt_bind_param($stmt, "isi", $userID, $hashed_password, $userLevel);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create user account');
        }
        
        $new_user_id = mysqli_insert_id($conn);
        
        // Insert into user_profiles
        $profile_sql = "INSERT INTO user_profiles (user_id, first_name, last_name, middle_name, email, phone, status) VALUES (?, ?, ?, ?, ?, ?, 'active')";
        $stmt = mysqli_prepare($conn, $profile_sql);
        mysqli_stmt_bind_param($stmt, "isssss", $new_user_id, $firstName, $lastName, $middleName, $email, $phone);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create user profile');
        }
        
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'User added successfully. Default password: password123']);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function editUser($conn) {
    $user_id = (int)$_POST['user_id'];
    $firstName = mysqli_real_escape_string($conn, trim($_POST['firstName']));
    $lastName = mysqli_real_escape_string($conn, trim($_POST['lastName']));
    $middleName = mysqli_real_escape_string($conn, trim($_POST['middleName'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    
    $sql = "UPDATE user_profiles SET first_name = ?, last_name = ?, middle_name = ?, email = ?, phone = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $firstName, $lastName, $middleName, $email, $phone, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user']);
    }
}

function toggleUserStatus($conn) {
    $user_id = (int)$_POST['user_id'];
    $new_status = $_POST['status'] === 'active' ? 'inactive' : 'active';
    
    $sql = "UPDATE user_profiles SET status = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => "User $new_status successfully", 'new_status' => $new_status]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
    }
}

function deleteUser($conn) {
    $user_id = (int)$_POST['user_id'];
    
    // Don't allow deletion of current admin
    if ($user_id == getCurrentUserId()) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
        return;
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Delete user (cascading will handle related records)
        $sql = "DELETE FROM tbl_user WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to delete user');
        }
        
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

mysqli_close($conn);
?>