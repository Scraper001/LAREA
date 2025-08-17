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
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['userID'] = $row['userID_col'];
            $_SESSION['user_level'] = $row['userLevel_col'];
            $_SESSION['logged_in'] = true;
            
            // Determine redirect URL based on user level
            $redirect_url = '';
            switch ($row['userLevel_col']) {
                case 1:
                    $redirect_url = '../admin/dashboard.php';
                    break;
                case 2:
                    $redirect_url = '../teachers/dashboard.php';
                    break;
                case 3:
                default:
                    $redirect_url = '../users/index.php';
                    break;
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Login Complete.',
                'redirect_url' => $redirect_url
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