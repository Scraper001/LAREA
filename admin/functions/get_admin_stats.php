<?php
include "../../connection/conn.php";

function get_admin_stats() {
    $conn = conn();
    $stats = [];
    
    // Total users count
    $sql = "SELECT COUNT(*) as total_users FROM tbl_user";
    $result = mysqli_query($conn, $sql);
    $stats['total_users'] = mysqli_fetch_assoc($result)['total_users'];
    
    // Total students count
    $sql = "SELECT COUNT(*) as total_students FROM students_tbl";
    $result = mysqli_query($conn, $sql);
    $stats['total_students'] = mysqli_fetch_assoc($result)['total_students'];
    
    // Today's attendance - Present
    $sql = "SELECT COUNT(*) as present_today FROM attendance WHERE attendance = 1 AND DATE(attendance_date) = CURDATE()";
    $result = mysqli_query($conn, $sql);
    $stats['present_today'] = mysqli_fetch_assoc($result)['present_today'] ?? 0;
    
    // Today's attendance - Absent  
    $sql = "SELECT COUNT(*) as absent_today FROM attendance WHERE attendance = 0 AND DATE(attendance_date) = CURDATE()";
    $result = mysqli_query($conn, $sql);
    $stats['absent_today'] = mysqli_fetch_assoc($result)['absent_today'] ?? 0;
    
    // Total behavior records
    $sql = "SELECT COUNT(*) as total_behavior_records FROM anecdotal_records_tbl";
    $result = mysqli_query($conn, $sql);
    $stats['total_behavior_records'] = mysqli_fetch_assoc($result)['total_behavior_records'];
    
    // Active behavior issues (follow-up required)
    $sql = "SELECT COUNT(*) as active_issues FROM anecdotal_records_tbl WHERE follow_up_required = 1 AND status = 'Active'";
    $result = mysqli_query($conn, $sql);
    $stats['active_issues'] = mysqli_fetch_assoc($result)['active_issues'];
    
    // Users by role
    $sql = "SELECT userLevel_col, COUNT(*) as count FROM tbl_user GROUP BY userLevel_col";
    $result = mysqli_query($conn, $sql);
    $stats['users_by_role'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $role_name = '';
        switch ($row['userLevel_col']) {
            case 1: $role_name = 'Admin'; break;
            case 2: $role_name = 'Teacher'; break;
            case 3: $role_name = 'Student'; break;
            default: $role_name = 'Unknown'; break;
        }
        $stats['users_by_role'][$role_name] = $row['count'];
    }
    
    // Recent activity (last 10 behavior records)
    $sql = "SELECT ar.observation_title, ar.date_recorded, s.fname, s.lname 
            FROM anecdotal_records_tbl ar 
            JOIN students_tbl s ON ar.student_id = s.id 
            ORDER BY ar.date_recorded DESC LIMIT 10";
    $result = mysqli_query($conn, $sql);
    $stats['recent_activity'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['recent_activity'][] = $row;
    }
    
    // Weekly attendance trend
    $sql = "SELECT 
                DAYNAME(attendance_date) as day_name,
                SUM(CASE WHEN attendance = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance = 0 THEN 1 ELSE 0 END) as absent
            FROM attendance 
            WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(attendance_date), DAYNAME(attendance_date)
            ORDER BY attendance_date";
    $result = mysqli_query($conn, $sql);
    $stats['weekly_attendance'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['weekly_attendance'][] = $row;
    }
    
    mysqli_close($conn);
    return $stats;
}
?>