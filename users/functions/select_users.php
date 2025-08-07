<?php

include "../connection/conn.php";
$conn = conn();

$sql = "SELECT * FROM `students_tbl`";
$result = $conn->query($sql);
$row = $result->fetch_assoc();



?>