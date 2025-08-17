<?php
include "../connection/conn.php";
$conn = conn();

$sql = "SELECT 
            b.behavior_ID_PK as id,
            b.LRN,
            b.behavior_type,
            b.date_entry,
            b.date_entry as behavior_date,
            b.remarks,
            b.severity_level,
            b.behavior_category,
            b.status,
            b.follow_up_required,
            b.follow_up_notes,
            s.Fname,
            s.Lname,
            s.Mname,
            s.photo_path,
            s.id as student_id,
            s.GLevel,
            s.Course
        FROM behavior_tbl b
        LEFT JOIN students_tbl s ON b.LRN = s.LRN
        ORDER BY b.date_entry DESC";

$result = $conn->query($sql);
$row = $result ? $result->fetch_assoc() : null;
?>