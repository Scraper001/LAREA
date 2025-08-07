<?php
session_start();
header('Content-Type: application/json');

include "../../connection/conn.php";
$conn = conn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {

    $userID = mysqli_real_escape_string($conn, trim($_POST['userID']));
    $password = trim($_POST['password']);

    $sql_login = "SELECT * FROM tbl_user WHERE userID_col = ?";
    $prepared = $conn->prepare($sql_login);
    $prepared->bind_param("s", $userID);
    $prepared->execute();
    $result = $prepared->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_col'])) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Login Complete.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Incorrect Credentials.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found.'
        ]);
    }

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}

mysqli_close($conn);
?>