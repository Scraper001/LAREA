<?php
include "../../connection/conn.php";
$conn = conn();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get student ID
    $student_id = mysqli_real_escape_string($conn, trim($_POST['studentId']));

    // Get form data
    $fname = mysqli_real_escape_string($conn, trim($_POST['studentFname']));
    $lname = mysqli_real_escape_string($conn, trim($_POST['studentLname']));
    $mname = mysqli_real_escape_string($conn, trim($_POST['studentMname']));
    $glevel = mysqli_real_escape_string($conn, trim($_POST['studentGLevel']));
    $course = mysqli_real_escape_string($conn, trim($_POST['studentCourse'])) ?: 'N/A';
    $lrn = mysqli_real_escape_string($conn, trim($_POST['studentLRN']));

    // Validate required fields
    if (empty($student_id) || empty($fname) || empty($lname) || empty($mname) || empty($glevel) || empty($lrn)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Validate LRN
    if (!is_numeric($lrn) || strlen($lrn) != 12) {
        echo json_encode(['success' => false, 'message' => 'LRN must be exactly 12 digits.']);
        exit;
    }

    // Check if LRN already exists (excluding current student)
    $check_query = "SELECT id FROM students_tbl WHERE LRN = '$lrn' AND id != '$student_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'LRN already exists in the system.']);
        exit;
    }

    // Get current photo path
    $current_photo_query = "SELECT photo_path FROM students_tbl WHERE id = '$student_id'";
    $current_photo_result = mysqli_query($conn, $current_photo_query);
    $current_photo_row = mysqli_fetch_assoc($current_photo_result);
    $current_photo_path = $current_photo_row['photo_path'];

    // Handle image upload
    $photo_path = $current_photo_path; // Keep current photo by default

    if (isset($_FILES['studentPhoto']) && $_FILES['studentPhoto']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file = $_FILES['studentPhoto'];
        $file_type = $file['type'];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];

        // Validate file type
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, and PNG are allowed.']);
            exit;
        }

        // Validate file size
        if ($file_size > $max_size) {
            echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 5MB.']);
            exit;
        }

        // Create upload directory if it doesn't exist
        $upload_dir = '../../uploads/student_photos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $lrn . '_' . time() . '.' . $file_extension;
        $new_photo_path = $upload_dir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file_tmp, $new_photo_path)) {
            // Delete old photo if it exists and is different
            if ($current_photo_path && file_exists('../../' . $current_photo_path)) {
                unlink('../../' . $current_photo_path);
            }

            // Store relative path for database
            $photo_path = 'uploads/student_photos/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
            exit;
        }
    }

    // Update student data
    $update_query = "UPDATE students_tbl SET 
                     Fname = '$fname', 
                     Lname = '$lname', 
                     Mname = '$mname', 
                     GLevel = '$glevel', 
                     Course = '$course', 
                     LRN = '$lrn', 
                     photo_path = " . ($photo_path ? "'$photo_path'" : "NULL") . "
                     WHERE id = '$student_id'";

    $result = mysqli_query($conn, $update_query);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Student updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update student.']);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>