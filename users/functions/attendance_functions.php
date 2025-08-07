<?php
include_once "../connection/conn.php";
$conn = conn();

function getAttendanceSettings()
{
    global $conn;
    $query = "SELECT * FROM attendance_settings ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);
    $settings = $result->fetch_assoc();
    
    // Return default settings if none exist
    if (!$settings) {
        return [
            'school_start_time' => '08:00:00',
            'school_end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'late_threshold_minutes' => 15,
            'overtime_threshold_minutes' => 30,
            'half_day_hours' => 4.00,
            'full_day_hours' => 8.00,
            'academic_year' => '2024-2025'
        ];
    }
    
    return $settings;
}

function getAttendanceStats($date = null)
{
    global $conn;

    if (!$date) {
        $date = date('Y-m-d');
    }

    $query = "SELECT 
                COUNT(DISTINCT s.id) as total_students,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'absent' OR a.status IS NULL THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused,
                SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as half_day,
                AVG(CASE WHEN a.total_hours IS NOT NULL THEN a.total_hours ELSE 0 END) as avg_hours,
                SUM(CASE WHEN a.overtime_hours > 0 THEN a.overtime_hours ELSE 0 END) as total_overtime
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getStudentsForAttendance($search = '', $grade_level = '', $date = null)
{
    global $conn;

    if (!$date) {
        $date = date('Y-m-d');
    }

    $query = "SELECT s.id, s.Fname, s.Lname, s.Mname, s.GLevel, s.photo_path, s.LRN,
                     a.status, a.time_in, a.time_out, a.break_start, a.break_end, 
                     a.total_hours, a.overtime_hours, a.remarks
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
              WHERE 1=1";

    $params = [$date];
    $types = "s";

    if (!empty($search)) {
        $query .= " AND (s.Fname LIKE ? OR s.Lname LIKE ? OR s.Mname LIKE ? OR s.LRN LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ssss";
    }

    if (!empty($grade_level)) {
        $query .= " AND s.GLevel LIKE ?";
        $params[] = "%$grade_level%";
        $types .= "s";
    }

    $query .= " ORDER BY s.GLevel, s.Lname, s.Fname";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function calculateWorkingHours($time_in, $time_out, $break_start = null, $break_end = null)
{
    if (!$time_in || !$time_out) {
        return ['total_hours' => 0, 'overtime_hours' => 0];
    }

    $start = new DateTime($time_in);
    $end = new DateTime($time_out);
    
    // Calculate total time
    $total_seconds = $end->getTimestamp() - $start->getTimestamp();
    
    // Subtract break time if provided
    if ($break_start && $break_end) {
        $break_start_time = new DateTime($break_start);
        $break_end_time = new DateTime($break_end);
        $break_seconds = $break_end_time->getTimestamp() - $break_start_time->getTimestamp();
        $total_seconds -= $break_seconds;
    }
    
    $total_hours = round($total_seconds / 3600, 2);
    
    // Calculate overtime (anything over 8 hours)
    $settings = getAttendanceSettings();
    $regular_hours = $settings['full_day_hours'];
    $overtime_hours = max(0, $total_hours - $regular_hours);
    
    return [
        'total_hours' => $total_hours,
        'overtime_hours' => round($overtime_hours, 2)
    ];
}

function isLateArrival($time_in, $status = 'present')
{
    if (!$time_in || $status !== 'present') {
        return false;
    }
    
    $settings = getAttendanceSettings();
    $school_start = new DateTime($settings['school_start_time']);
    $arrival_time = new DateTime($time_in);
    $late_threshold = clone $school_start;
    $late_threshold->add(new DateInterval('PT' . $settings['late_threshold_minutes'] . 'M'));
    
    return $arrival_time > $late_threshold;
}

function markAttendance($student_id, $date, $status, $time_in = null, $time_out = null, $break_start = null, $break_end = null, $remarks = '', $recorded_by = null)
{
    global $conn;

    // Auto-determine if late based on time_in
    if ($time_in && $status == 'present') {
        if (isLateArrival($time_in, $status)) {
            $status = 'late';
        }
    }
    
    // Calculate working hours if both time_in and time_out are provided
    $total_hours = null;
    $overtime_hours = 0;
    
    if ($time_in && $time_out) {
        $hours_calc = calculateWorkingHours($time_in, $time_out, $break_start, $break_end);
        $total_hours = $hours_calc['total_hours'];
        $overtime_hours = $hours_calc['overtime_hours'];
    }

    $query = "INSERT INTO attendance (student_id, date, status, time_in, time_out, break_start, break_end, total_hours, overtime_hours, remarks, recorded_by)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE
              status = VALUES(status),
              time_in = VALUES(time_in),
              time_out = VALUES(time_out),
              break_start = VALUES(break_start),
              break_end = VALUES(break_end),
              total_hours = VALUES(total_hours),
              overtime_hours = VALUES(overtime_hours),
              remarks = VALUES(remarks),
              recorded_by = VALUES(recorded_by),
              updated_at = CURRENT_TIMESTAMP";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssssddsj", $student_id, $date, $status, $time_in, $time_out, $break_start, $break_end, $total_hours, $overtime_hours, $remarks, $recorded_by);

    return $stmt->execute();
}

function bulkMarkAttendance($attendanceData, $date, $recorded_by = null)
{
    global $conn;

    $conn->begin_transaction();

    try {
        foreach ($attendanceData as $data) {
            $result = markAttendance(
                $data['student_id'],
                $date,
                $data['status'],
                $data['time_in'] ?? null,
                $data['time_out'] ?? null,
                $data['break_start'] ?? null,
                $data['break_end'] ?? null,
                $data['remarks'] ?? '',
                $recorded_by
            );

            if (!$result) {
                throw new Exception("Failed to mark attendance for student ID: " . $data['student_id']);
            }
        }

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function getGradeLevels()
{
    global $conn;
    $query = "SELECT DISTINCT GLevel FROM students_tbl ORDER BY GLevel";
    $result = $conn->query($query);
    $levels = [];
    while ($row = $result->fetch_assoc()) {
        $levels[] = $row['GLevel'];
    }
    return $levels;
}

function isHoliday($date)
{
    global $conn;
    $query = "SELECT * FROM holidays WHERE date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAttendanceReport($date_from, $date_to, $grade_filter = '', $student_filter = '')
{
    global $conn;
    
    $query = "SELECT s.id, s.Fname, s.Lname, s.Mname, s.GLevel, s.LRN,
                     COUNT(a.id) as total_days,
                     SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                     SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                     SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
                     SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused_days,
                     SUM(CASE WHEN a.total_hours IS NOT NULL THEN a.total_hours ELSE 0 END) as total_hours,
                     SUM(CASE WHEN a.overtime_hours IS NOT NULL THEN a.overtime_hours ELSE 0 END) as total_overtime,
                     ROUND((SUM(CASE WHEN a.status IN ('present', 'late') THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 2) as attendance_percentage
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date BETWEEN ? AND ?
              WHERE 1=1";
    
    $params = [$date_from, $date_to];
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
    
    $query .= " GROUP BY s.id ORDER BY s.GLevel, s.Lname, s.Fname";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function getDailyAttendanceSummary($date)
{
    global $conn;
    
    $query = "SELECT 
                s.GLevel,
                COUNT(s.id) as total_students,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'absent' OR a.status IS NULL THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused,
                ROUND((SUM(CASE WHEN a.status IN ('present', 'late') THEN 1 ELSE 0 END) / COUNT(s.id)) * 100, 2) as attendance_rate
              FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
              GROUP BY s.GLevel
              ORDER BY s.GLevel";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    return $stmt->get_result();
}
?>