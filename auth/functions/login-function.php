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
            // Set session variables for role-based access
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['userID'] = $row['userID_col'];
            $_SESSION['user_level'] = $row['userLevel_col'];
            $_SESSION['logged_in'] = true;
            
            // Determine redirect URL based on user level
            $redirect_url = '../users/index.php'; // Default for teachers (level 1)
            if ($row['userLevel_col'] == 2) {
                $redirect_url = '../users/admin_dashboard.php'; // Admin
            } elseif ($row['userLevel_col'] == 3) {
                $redirect_url = '../users/parent_dashboard.php'; // Parent
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Login Complete.',
                'redirect_url' => $redirect_url,
                'user_level' => $row['userLevel_col']
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