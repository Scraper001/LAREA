<?php
include "../../connection/conn.php";
$conn = conn();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $fname = mysqli_real_escape_string($conn, trim($_POST['studentFname']));
    $lname = mysqli_real_escape_string($conn, trim($_POST['studentLname']));
    $mname = mysqli_real_escape_string($conn, trim($_POST['studentMname']));
    $glevel = mysqli_real_escape_string($conn, trim($_POST['studentGLevel']));
    $course = mysqli_real_escape_string($conn, trim($_POST['studentCourse'])) ?: 'N/A';
    $lrn = mysqli_real_escape_string($conn, trim($_POST['studentLRN']));

    // Validate required fields
    if (empty($fname) || empty($lname) || empty($mname) || empty($glevel) || empty($lrn)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Validate LRN
    if (!is_numeric($lrn) || strlen($lrn) != 12) {
        echo json_encode(['success' => false, 'message' => 'LRN must be exactly 12 digits.']);
        exit;
    }

    // Check if LRN already exists
    $check_query = "SELECT id FROM students_tbl WHERE LRN = '$lrn'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'LRN already exists in the system.']);
        exit;
    }

    // Handle image upload
    $photo_path = null;
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
        $photo_path = $upload_dir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $photo_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
            exit;
        }

        // Store relative path for database
        $photo_path = 'uploads/student_photos/' . $filename;
    }

    // Insert student data
    $insert_query = "INSERT INTO students_tbl (Fname, Lname, Mname, GLevel, Course, LRN, photo_path) 
                     VALUES ('$fname', '$lname', '$mname', '$glevel', '$course', '$lrn', " .
        ($photo_path ? "'$photo_path'" : "NULL") . ")";

    $result = mysqli_query($conn, $insert_query);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Student added successfully!']);
    } else {
        // If database insert fails, remove uploaded file
        if ($photo_path && file_exists('../../' . $photo_path)) {
            unlink('../../' . $photo_path);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to add student.']);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>