<?php
// Database connection
include '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['studentFname'] ?? '';
    $lname = $_POST['studentLname'] ?? '';
    $mname = $_POST['studentMname'] ?? '';
    $lrn = $_POST['studentLRN'] ?? '';
    $glevel = $_POST['studentGLevel'] ?? '';
    $course = $_POST['studentCourse'] ?? '';
    $photo_path = '';

    // Handle file upload
    if (isset($_FILES['studentPhoto']) && $_FILES['studentPhoto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['studentPhoto']['tmp_name'];
        $fileName = $_FILES['studentPhoto']['name'];
        $fileSize = $_FILES['studentPhoto']['size'];
        $fileType = $_FILES['studentPhoto']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validate file type and size
        $allowedfileExtensions = array('jpg', 'jpeg', 'png');
        if (in_array($fileExtension, $allowedfileExtensions) && $fileSize < 5 * 1024 * 1024) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '../../uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $photo_path = 'uploads/' . $newFileName;
            }
        }
    }

    // Insert user data into the database
    $stmt = $conn->prepare("INSERT INTO students (Fname, Lname, Mname, LRN, GLevel, Course, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fname, $lname, $mname, $lrn, $glevel, $course, $photo_path);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Student added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding student: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>