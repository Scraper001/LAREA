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
    case 'add_relationship':
        addRelationship($conn);
        break;
    case 'delete_relationship':
        deleteRelationship($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function addRelationship($conn) {
    // Validate required fields
    $required_fields = ['parent_user_id', 'student_id', 'relationship_type'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            return;
        }
    }
    
    $parent_user_id = (int)$_POST['parent_user_id'];
    $student_id = (int)$_POST['student_id'];
    $relationship_type = mysqli_real_escape_string($conn, trim($_POST['relationship_type']));
    $is_primary_contact = isset($_POST['is_primary_contact']) ? 1 : 0;
    
    // Verify parent is actually a parent (user level 3)
    $parent_check = "SELECT userLevel_col FROM tbl_user WHERE id = ?";
    $stmt = mysqli_prepare($conn, $parent_check);
    mysqli_stmt_bind_param($stmt, "i", $parent_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $parent = mysqli_fetch_assoc($result);
    
    if (!$parent || $parent['userLevel_col'] != 3) {
        echo json_encode(['success' => false, 'message' => 'Selected user is not a parent']);
        return;
    }
    
    // Verify student exists
    $student_check = "SELECT COUNT(*) as count FROM students_tbl WHERE id = ?";
    $stmt = mysqli_prepare($conn, $student_check);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student_count = mysqli_fetch_assoc($result)['count'];
    
    if ($student_count == 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }
    
    // Check if relationship already exists
    $relationship_check = "SELECT COUNT(*) as count FROM parent_child_relationships WHERE parent_user_id = ? AND student_id = ?";
    $stmt = mysqli_prepare($conn, $relationship_check);
    mysqli_stmt_bind_param($stmt, "ii", $parent_user_id, $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $relationship_count = mysqli_fetch_assoc($result)['count'];
    
    if ($relationship_count > 0) {
        echo json_encode(['success' => false, 'message' => 'Relationship already exists between this parent and student']);
        return;
    }
    
    // If this is set as primary contact, remove primary status from other relationships for this student
    if ($is_primary_contact) {
        $update_primary = "UPDATE parent_child_relationships SET is_primary_contact = 0 WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $update_primary);
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        mysqli_stmt_execute($stmt);
    }
    
    // Insert the new relationship
    $insert_sql = "INSERT INTO parent_child_relationships (parent_user_id, student_id, relationship_type, is_primary_contact) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "iisi", $parent_user_id, $student_id, $relationship_type, $is_primary_contact);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Parent-child relationship added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add relationship']);
    }
}

function deleteRelationship($conn) {
    $relationship_id = (int)$_POST['relationship_id'];
    
    $sql = "DELETE FROM parent_child_relationships WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $relationship_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Relationship deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete relationship']);
    }
}

mysqli_close($conn);
?>