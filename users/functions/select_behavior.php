<?php
include "../connection/conn.php";
$conn = conn();

$sql = "SELECT 
            b.behavior_ID_PK,
            b.LRN,
            b.behavior_type,
            b.date_entry,
            b.remarks,
            s.Fname,
            s.Lname,
            s.Mname,
            s.photo_path
        FROM behavior_tbl b
        LEFT JOIN students_tbl s ON b.LRN = s.LRN
        ORDER BY b.date_entry DESC";

$result = $conn->query($sql);
$row = $result ? $result->fetch_assoc() : null;
?>