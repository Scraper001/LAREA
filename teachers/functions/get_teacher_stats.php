<?php
include "../../connection/conn.php";

function get_teacher_stats($teacher_id = null) {
    $conn = conn();
    $stats = [];
    
    // For now, we'll show all data since we don't have teacher-student assignments in the current schema
    // In a real implementation, you'd filter by teacher assignments
    
    // Today's attendance summary
    $sql = "SELECT 
                SUM(CASE WHEN attendance = 1 THEN 1 ELSE 0 END) as present_today,
                SUM(CASE WHEN attendance = 0 THEN 1 ELSE 0 END) as absent_today,
                COUNT(*) as total_today
            FROM attendance 
            WHERE DATE(attendance_date) = CURDATE()";
    $result = mysqli_query($conn, $sql);
    $attendance_data = mysqli_fetch_assoc($result);
    $stats['present_today'] = $attendance_data['present_today'] ?? 0;
    $stats['absent_today'] = $attendance_data['absent_today'] ?? 0;
    $stats['total_students_today'] = $attendance_data['total_today'] ?? 0;
    
    // Total assigned students (simplified - all students for now)
    $sql = "SELECT COUNT(*) as total_students FROM students_tbl";
    $result = mysqli_query($conn, $sql);
    $stats['total_assigned_students'] = mysqli_fetch_assoc($result)['total_students'];
    
    // Recent behavior reports (last 7 days)
    $sql = "SELECT COUNT(*) as recent_behavior_reports 
            FROM anecdotal_records_tbl 
            WHERE date_recorded >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $result = mysqli_query($conn, $sql);
    $stats['recent_behavior_reports'] = mysqli_fetch_assoc($result)['recent_behavior_reports'];
    
    // Pending follow-ups
    $sql = "SELECT COUNT(*) as pending_followups 
            FROM anecdotal_records_tbl 
            WHERE follow_up_required = 1 AND status = 'Active'";
    $result = mysqli_query($conn, $sql);
    $stats['pending_followups'] = mysqli_fetch_assoc($result)['pending_followups'];
    
    // Weekly attendance for chart
    $sql = "SELECT 
                DATE(attendance_date) as date,
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
    
    // Recent student activity
    $sql = "SELECT 
                ar.observation_title, 
                ar.date_recorded, 
                ar.severity_level,
                s.fname, 
                s.lname,
                s.grade_level
            FROM anecdotal_records_tbl ar 
            JOIN students_tbl s ON ar.student_id = s.id 
            ORDER BY ar.date_recorded DESC 
            LIMIT 10";
    $result = mysqli_query($conn, $sql);
    $stats['recent_activity'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['recent_activity'][] = $row;
    }
    
    // Class performance overview (simplified - behavior severity distribution)
    $sql = "SELECT 
                severity_level,
                COUNT(*) as count
            FROM anecdotal_records_tbl 
            WHERE date_recorded >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY severity_level";
    $result = mysqli_query($conn, $sql);
    $stats['behavior_severity'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['behavior_severity'][$row['severity_level']] = $row['count'];
    }
    
    // Students needing attention (those with recent high severity incidents)
    $sql = "SELECT DISTINCT
                s.id,
                s.fname,
                s.lname,
                s.grade_level,
                COUNT(ar.record_id) as incident_count,
                MAX(ar.date_recorded) as last_incident
            FROM students_tbl s
            JOIN anecdotal_records_tbl ar ON s.id = ar.student_id
            WHERE ar.severity_level IN ('High', 'Medium') 
            AND ar.date_recorded >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY s.id, s.fname, s.lname, s.grade_level
            ORDER BY incident_count DESC, last_incident DESC
            LIMIT 10";
    $result = mysqli_query($conn, $sql);
    $stats['students_needing_attention'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['students_needing_attention'][] = $row;
    }
    
    mysqli_close($conn);
    return $stats;
}
?>