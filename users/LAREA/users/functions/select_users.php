<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to select users
function selectUsers($conn) {
    $sql = "SELECT * FROM users"; // Adjust the query as needed
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result;
    } else {
        return null;
    }
}

// Fetch users
$result = selectUsers($conn);

// Close connection
$conn->close();
?>