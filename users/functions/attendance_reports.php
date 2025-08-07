<?php
/**
 * Attendance Reports and Analytics Functions
 * Provides comprehensive reporting capabilities for the attendance system
 */

include_once "../connection/conn.php";
include_once "attendance_functions.php";
include_once "time_calculation_utils.php";

/**
 * Generate daily attendance report
 */
function generateDailyReport($date, $format = 'html')
{
    global $conn;
    
    $report_data = [
        'date' => $date,
        'summary' => getDailyAttendanceSummary($date),
        'students' => [],
        'statistics' => getAttendanceStats($date),
        'holiday' => isHoliday($date)
    ];
    
    // Get detailed student data
    $query = "SELECT s.id, s.Fname, s.Lname, s.Mname, s.GLevel, s.LRN,
                     a.status, a.time_in, a.time_out, a.total_hours, a.overtime_hours, a.remarks
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
              ORDER BY s.GLevel, s.Lname, s.Fname";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $report_data['students'][] = $row;
    }
    
    if ($format === 'json') {
        return json_encode($report_data);
    } elseif ($format === 'csv') {
        return generateCSVReport($report_data, 'daily');
    } else {
        return generateHTMLReport($report_data, 'daily');
    }
}

/**
 * Generate weekly attendance report
 */
function generateWeeklyReport($start_date, $format = 'html')
{
    global $conn;
    
    $end_date = date('Y-m-d', strtotime($start_date . ' +6 days'));
    
    $report_data = [
        'start_date' => $start_date,
        'end_date' => $end_date,
        'students' => [],
        'daily_summaries' => [],
        'weekly_stats' => getWeeklyStats($start_date, $end_date)
    ];
    
    // Get daily summaries for the week
    for ($i = 0; $i < 7; $i++) {
        $current_date = date('Y-m-d', strtotime($start_date . " +$i days"));
        $report_data['daily_summaries'][$current_date] = getDailyAttendanceSummary($current_date);
    }
    
    // Get student weekly data
    $query = "SELECT s.id, s.Fname, s.Lname, s.Mname, s.GLevel, s.LRN,
                     COUNT(CASE WHEN a.status IN ('present', 'late') THEN 1 END) as days_present,
                     COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as days_absent,
                     SUM(a.total_hours) as total_hours,
                     SUM(a.overtime_hours) as total_overtime
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date BETWEEN ? AND ?
              GROUP BY s.id
              ORDER BY s.GLevel, s.Lname, s.Fname";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $row['attendance_rate'] = round(($row['days_present'] / 7) * 100, 2);
        $report_data['students'][] = $row;
    }
    
    if ($format === 'json') {
        return json_encode($report_data);
    } elseif ($format === 'csv') {
        return generateCSVReport($report_data, 'weekly');
    } else {
        return generateHTMLReport($report_data, 'weekly');
    }
}

/**
 * Generate monthly attendance report
 */
function generateMonthlyReport($year, $month, $format = 'html')
{
    global $conn;
    
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date));
    
    $report_data = [
        'year' => $year,
        'month' => $month,
        'month_name' => date('F', strtotime($start_date)),
        'start_date' => $start_date,
        'end_date' => $end_date,
        'students' => [],
        'monthly_stats' => getMonthlyStats($start_date, $end_date),
        'school_days' => getSchoolDaysCount($start_date, $end_date)
    ];
    
    // Get student monthly data
    $query = "SELECT s.id, s.Fname, s.Lname, s.Mname, s.GLevel, s.LRN,
                     COUNT(CASE WHEN a.status IN ('present', 'late') THEN 1 END) as days_present,
                     COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as days_absent,
                     COUNT(CASE WHEN a.status = 'late' THEN 1 END) as days_late,
                     COUNT(CASE WHEN a.status = 'excused' THEN 1 END) as days_excused,
                     SUM(a.total_hours) as total_hours,
                     SUM(a.overtime_hours) as total_overtime,
                     AVG(CASE WHEN a.total_hours > 0 THEN a.total_hours END) as avg_daily_hours
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date BETWEEN ? AND ?
              GROUP BY s.id
              ORDER BY s.GLevel, s.Lname, s.Fname";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $row['attendance_rate'] = round(($row['days_present'] / $report_data['school_days']) * 100, 2);
        $report_data['students'][] = $row;
    }
    
    if ($format === 'json') {
        return json_encode($report_data);
    } elseif ($format === 'csv') {
        return generateCSVReport($report_data, 'monthly');
    } else {
        return generateHTMLReport($report_data, 'monthly');
    }
}

/**
 * Generate custom attendance report
 */
function generateCustomReport($start_date, $end_date, $grade_filter = '', $student_filter = '', $format = 'html')
{
    global $conn;
    
    $report_data = [
        'start_date' => $start_date,
        'end_date' => $end_date,
        'grade_filter' => $grade_filter,
        'student_filter' => $student_filter,
        'students' => [],
        'period_stats' => getPeriodStats($start_date, $end_date, $grade_filter, $student_filter),
        'school_days' => getSchoolDaysCount($start_date, $end_date)
    ];
    
    $students_result = getAttendanceReport($start_date, $end_date, $grade_filter, $student_filter);
    
    while ($row = $students_result->fetch_assoc()) {
        $report_data['students'][] = $row;
    }
    
    if ($format === 'json') {
        return json_encode($report_data);
    } elseif ($format === 'csv') {
        return generateCSVReport($report_data, 'custom');
    } else {
        return generateHTMLReport($report_data, 'custom');
    }
}

/**
 * Get weekly statistics
 */
function getWeeklyStats($start_date, $end_date)
{
    global $conn;
    
    $query = "SELECT 
                COUNT(DISTINCT s.id) as total_students,
                COUNT(DISTINCT a.date) as school_days,
                COUNT(CASE WHEN a.status IN ('present', 'late') THEN 1 END) as total_present,
                COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as total_absent,
                COUNT(CASE WHEN a.status = 'late' THEN 1 END) as total_late,
                SUM(a.total_hours) as total_hours,
                SUM(a.overtime_hours) as total_overtime,
                AVG(CASE WHEN a.status IN ('present', 'late') THEN 1 ELSE 0 END) * 100 as avg_attendance_rate
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date BETWEEN ? AND ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Get monthly statistics
 */
function getMonthlyStats($start_date, $end_date)
{
    global $conn;
    
    $query = "SELECT 
                COUNT(DISTINCT s.id) as total_students,
                COUNT(DISTINCT a.date) as days_with_records,
                COUNT(CASE WHEN a.status IN ('present', 'late') THEN 1 END) as total_present,
                COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as total_absent,
                COUNT(CASE WHEN a.status = 'late' THEN 1 END) as total_late,
                COUNT(CASE WHEN a.status = 'excused' THEN 1 END) as total_excused,
                SUM(a.total_hours) as total_hours,
                SUM(a.overtime_hours) as total_overtime,
                AVG(CASE WHEN a.status IN ('present', 'late') THEN 1 ELSE 0 END) * 100 as avg_attendance_rate,
                MAX(a.total_hours) as max_daily_hours,
                MIN(CASE WHEN a.total_hours > 0 THEN a.total_hours END) as min_daily_hours
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date BETWEEN ? AND ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Get period statistics with filters
 */
function getPeriodStats($start_date, $end_date, $grade_filter = '', $student_filter = '')
{
    global $conn;
    
    $query = "SELECT 
                COUNT(DISTINCT s.id) as total_students,
                COUNT(DISTINCT a.date) as days_with_records,
                COUNT(CASE WHEN a.status IN ('present', 'late') THEN 1 END) as total_present,
                COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as total_absent,
                COUNT(CASE WHEN a.status = 'late' THEN 1 END) as total_late,
                COUNT(CASE WHEN a.status = 'excused' THEN 1 END) as total_excused,
                SUM(a.total_hours) as total_hours,
                SUM(a.overtime_hours) as total_overtime,
                AVG(CASE WHEN a.status IN ('present', 'late') THEN 1 ELSE 0 END) * 100 as avg_attendance_rate
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date BETWEEN ? AND ?
              WHERE 1=1";
              
    $params = [$start_date, $end_date];
    $types = "ss";
    
    if (!empty($grade_filter)) {
        $query .= " AND s.GLevel LIKE ?";
        $params[] = "%$grade_filter%";
        $types .= "s";
    }
    
    if (!empty($student_filter)) {
        $query .= " AND (s.Fname LIKE ? OR s.Lname LIKE ? OR s.Mname LIKE ?)";
        $searchTerm = "%$student_filter%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "sss";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Generate CSV report
 */
function generateCSVReport($data, $type)
{
    $csv = '';
    
    if ($type === 'daily') {
        $csv .= "Daily Attendance Report - " . date('F j, Y', strtotime($data['date'])) . "\n\n";
        $csv .= "LRN,Last Name,First Name,Middle Name,Grade,Status,Time In,Time Out,Total Hours,Overtime,Remarks\n";
        
        foreach ($data['students'] as $student) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $student['LRN'],
                $student['Lname'],
                $student['Fname'],
                $student['Mname'],
                $student['GLevel'],
                $student['status'] ?: 'absent',
                $student['time_in'] ?: '',
                $student['time_out'] ?: '',
                $student['total_hours'] ?: '0',
                $student['overtime_hours'] ?: '0',
                $student['remarks'] ?: ''
            );
        }
    } elseif ($type === 'weekly') {
        $csv .= "Weekly Attendance Report - " . date('F j, Y', strtotime($data['start_date'])) . " to " . date('F j, Y', strtotime($data['end_date'])) . "\n\n";
        $csv .= "LRN,Last Name,First Name,Middle Name,Grade,Days Present,Days Absent,Total Hours,Overtime Hours,Attendance Rate\n";
        
        foreach ($data['students'] as $student) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s%%\n",
                $student['LRN'],
                $student['Lname'],
                $student['Fname'],
                $student['Mname'],
                $student['GLevel'],
                $student['days_present'],
                $student['days_absent'],
                $student['total_hours'] ?: '0',
                $student['total_overtime'] ?: '0',
                $student['attendance_rate']
            );
        }
    }
    
    return $csv;
}

/**
 * Generate HTML report
 */
function generateHTMLReport($data, $type)
{
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>Attendance Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { border-collapse: collapse; width: 100%; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .summary { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            .stats { display: flex; gap: 20px; margin-bottom: 20px; }
            .stat-card { background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 5px; text-align: center; }
            .print-button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; }
            @media print { .print-button { display: none; } }
        </style>
    </head>
    <body>';
    
    $html .= '<button class="print-button" onclick="window.print()">Print Report</button>';
    
    if ($type === 'daily') {
        $html .= '<h1>Daily Attendance Report</h1>';
        $html .= '<p><strong>Date:</strong> ' . date('F j, Y', strtotime($data['date'])) . '</p>';
        
        if ($data['holiday']) {
            $html .= '<div class="summary"><strong>Holiday:</strong> ' . $data['holiday']['name'] . '</div>';
        }
        
        $stats = $data['statistics'];
        $html .= '<div class="stats">
            <div class="stat-card"><h3>' . $stats['present'] . '</h3><p>Present</p></div>
            <div class="stat-card"><h3>' . $stats['absent'] . '</h3><p>Absent</p></div>
            <div class="stat-card"><h3>' . $stats['late'] . '</h3><p>Late</p></div>
            <div class="stat-card"><h3>' . $stats['excused'] . '</h3><p>Excused</p></div>
        </div>';
        
        $html .= '<table>
            <tr>
                <th>LRN</th>
                <th>Student Name</th>
                <th>Grade</th>
                <th>Status</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Total Hours</th>
                <th>Overtime</th>
                <th>Remarks</th>
            </tr>';
            
        foreach ($data['students'] as $student) {
            $html .= '<tr>
                <td>' . $student['LRN'] . '</td>
                <td>' . $student['Lname'] . ', ' . $student['Fname'] . ' ' . $student['Mname'] . '</td>
                <td>' . $student['GLevel'] . '</td>
                <td>' . ($student['status'] ?: 'absent') . '</td>
                <td>' . ($student['time_in'] ?: '-') . '</td>
                <td>' . ($student['time_out'] ?: '-') . '</td>
                <td>' . ($student['total_hours'] ?: '0') . '</td>
                <td>' . ($student['overtime_hours'] ?: '0') . '</td>
                <td>' . ($student['remarks'] ?: '-') . '</td>
            </tr>';
        }
        
        $html .= '</table>';
    }
    
    $html .= '</body></html>';
    
    return $html;
}

/**
 * Save report to file and database
 */
function saveReport($report_name, $report_type, $date_from, $date_to, $grade_filter, $student_filter, $content, $generated_by)
{
    global $conn;
    
    // Create reports directory if it doesn't exist
    $reports_dir = '../reports/';
    if (!is_dir($reports_dir)) {
        mkdir($reports_dir, 0755, true);
    }
    
    // Generate filename
    $filename = $reports_dir . $report_name . '_' . date('Y-m-d_H-i-s') . '.html';
    
    // Save file
    file_put_contents($filename, $content);
    
    // Save to database
    $query = "INSERT INTO attendance_reports (report_name, report_type, date_from, date_to, grade_filter, student_filter, generated_by, file_path)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $report_name, $report_type, $date_from, $date_to, $grade_filter, $student_filter, $generated_by, $filename);
    
    return $stmt->execute() ? $filename : false;
}

/**
 * Get saved reports list
 */
function getSavedReports($limit = 50)
{
    global $conn;
    
    $query = "SELECT * FROM attendance_reports ORDER BY created_at DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}
?>