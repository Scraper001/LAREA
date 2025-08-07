<?php
// Include database connection
include '../../config/db_connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the student ID from the form data
    $studentId = $_POST['studentId'];
    
    // Get the updated student data from the form
    $fname = $_POST['studentFname'];
    $lname = $_POST['studentLname'];
    $mname = $_POST['studentMname'];
    $lrn = $_POST['studentLRN'];
    $glevel = $_POST['studentGLevel'];
    $course = $_POST['studentCourse'];
    
    // Prepare the SQL statement to update the student record
    $stmt = $conn->prepare("UPDATE students SET Fname=?, Lname=?, Mname=?, LRN=?, GLevel=?, Course=? WHERE id=?");
    $stmt->bind_param("ssssssi", $fname, $lname, $mname, $lrn, $glevel, $course, $studentId);
    
    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Student updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating student: ' . $stmt->error]);
    }
    
    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>