<?php
// Filepath: c:\xampp\htdocs\LAREA\users\functions\delete_users.php

include "../../includes/db_connection.php"; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['studentId'];

    if (!empty($studentId)) {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $studentId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting student.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid student ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>