<?php
include "../../connection/conn.php";
$conn = conn();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get student ID
    $student_id = mysqli_real_escape_string($conn, trim($_POST['studentId']));

    // Validate student ID
    if (empty($student_id) || !is_numeric($student_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid student ID.']);
        exit;
    }

    // First, get the student's photo path before deletion
    $select_query = "SELECT photo_path FROM students_tbl WHERE id = '$student_id'";
    $select_result = mysqli_query($conn, $select_query);

    if (mysqli_num_rows($select_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found.']);
        exit;
    }

    $student_data = mysqli_fetch_assoc($select_result);
    $photo_path = $student_data['photo_path'];

    // Delete student from database

    $sql_select_user = "SELECT * FROM students_tbl WHERE id = '$student_id'";
    $result = $conn->query($sql_select_user);
    $row = $result->fetch_assoc();


    $conn->query("DELETE FROM attendance_tbl WHERE LRN = '" . $row['LRN'] . "'");


    $delete_query = "DELETE FROM students_tbl WHERE id = '$student_id'";
    $delete_result = mysqli_query($conn, $delete_query);


    if ($delete_result) {
        // If deletion successful and photo exists, remove the photo file
        if ($photo_path && file_exists('../../' . $photo_path)) {
            unlink('../../' . $photo_path);
        }

        echo json_encode(['success' => true, 'message' => 'Student deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete student.']);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>