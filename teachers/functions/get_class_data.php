<?php
include "../../connection/conn.php";

function get_class_data($teacher_id = null) {
    $conn = conn();
    $data = [];
    
    // Get all students (in a real implementation, this would be filtered by teacher assignment)
    $sql = "SELECT 
                s.id,
                s.fname,
                s.lname,
                s.mname,
                s.grade_level,
                s.course,
                s.LRN,
                COALESCE(recent_attendance.status, 'No Record') as last_attendance_status,
                COALESCE(recent_attendance.attendance_date, 'Never') as last_attendance_date,
                COALESCE(behavior_count.count, 0) as behavior_records_count
            FROM students_tbl s
            LEFT JOIN (
                SELECT 
                    student_id,
                    CASE WHEN attendance = 1 THEN 'Present' ELSE 'Absent' END as status,
                    attendance_date
                FROM attendance a1
                WHERE attendance_date = (
                    SELECT MAX(attendance_date) 
                    FROM attendance a2 
                    WHERE a2.student_id = a1.student_id
                )
            ) recent_attendance ON s.id = recent_attendance.student_id
            LEFT JOIN (
                SELECT 
                    student_id,
                    COUNT(*) as count
                FROM anecdotal_records_tbl
                WHERE date_recorded >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY student_id
            ) behavior_count ON s.id = behavior_count.student_id
            ORDER BY s.grade_level, s.lname, s.fname";
    
    $result = mysqli_query($conn, $sql);
    $data['students'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['students'][] = $row;
    }
    
    // Get upcoming deadlines (simulated data - would come from assignment/grade tables)
    $data['upcoming_deadlines'] = [
        [
            'title' => 'Math Quiz 3',
            'due_date' => date('Y-m-d', strtotime('+3 days')),
            'class' => 'Grade 10 Mathematics',
            'status' => 'pending'
        ],
        [
            'title' => 'Science Project Submission',
            'due_date' => date('Y-m-d', strtotime('+7 days')),
            'class' => 'Grade 9 Science',
            'status' => 'pending'
        ],
        [
            'title' => 'English Essay',
            'due_date' => date('Y-m-d', strtotime('+10 days')),
            'class' => 'Grade 11 English',
            'status' => 'pending'
        ]
    ];
    
    // Get class attendance summary by grade level
    $sql = "SELECT 
                s.grade_level,
                COUNT(DISTINCT s.id) as total_students,
                SUM(CASE WHEN a.attendance = 1 AND DATE(a.attendance_date) = CURDATE() THEN 1 ELSE 0 END) as present_today,
                SUM(CASE WHEN a.attendance = 0 AND DATE(a.attendance_date) = CURDATE() THEN 1 ELSE 0 END) as absent_today
            FROM students_tbl s
            LEFT JOIN attendance a ON s.id = a.student_id AND DATE(a.attendance_date) = CURDATE()
            GROUP BY s.grade_level
            ORDER BY s.grade_level";
    
    $result = mysqli_query($conn, $sql);
    $data['class_attendance'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['class_attendance'][] = $row;
    }
    
    mysqli_close($conn);
    return $data;
}
?>