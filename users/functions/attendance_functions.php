<?php
// Include database connection
include_once "../connection/conn.php";
$conn = conn();

function getAttendanceSettings()
{
    global $conn;
    $query = "SELECT * FROM attendance_settings ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        // Return default settings if none exist
        return [
            'school_start_time' => '08:00:00',
            'school_end_time' => '17:00:00',
            'late_threshold_minutes' => 15,
            'half_day_hours' => 4.00,
            'full_day_hours' => 8.00,
            'break_duration_minutes' => 60
        ];
    }
}

function calculateHoursWorked($time_in, $time_out, $break_minutes = null)
{
    if (!$time_in || !$time_out) {
        return null;
    }
    
    $start = new DateTime($time_in);
    $end = new DateTime($time_out);
    
    if ($end <= $start) {
        return null; // Invalid time range
    }
    
    $interval = $start->diff($end);
    $hours = $interval->h + ($interval->i / 60);
    
    // Subtract break time if provided
    if ($break_minutes) {
        $hours -= ($break_minutes / 60);
    }
    
    return max(0, round($hours, 2));
}

function getAttendanceStats($date = null)
{
    global $conn;

    if (!$date) {
        $date = date('Y-m-d');
    }

    $query = "SELECT 
                COUNT(u.id) as total_students,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'absent' OR a.status IS NULL THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused,
                AVG(CASE WHEN a.hours_worked IS NOT NULL THEN a.hours_worked ELSE 0 END) as avg_hours
              FROM users u
              LEFT JOIN attendance a ON u.id = a.student_id AND a.date = ?
              WHERE u.user_type = 'student'";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    
    // Ensure absent count is correct
    $stats['absent'] = $stats['total_students'] - $stats['present'] - $stats['late'] - $stats['excused'];
    
    return $stats;
}

function getStudentsForAttendance($search = '', $grade_level = '', $date = null)
{
    global $conn;

    if (!$date) {
        $date = date('Y-m-d');
    }

    $query = "SELECT u.id, u.Fname, u.Lname, u.Mname, u.GLevel, u.photo_path,
                     a.status, a.time_in, a.time_out, a.hours_worked, a.remarks
              FROM users u
              LEFT JOIN attendance a ON u.id = a.student_id AND a.date = ?
              WHERE u.user_type = 'student'";

    $params = [$date];
    $types = "s";

    if (!empty($search)) {
        $query .= " AND (u.Fname LIKE ? OR u.Lname LIKE ? OR u.Mname LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "sss";
    }

    if (!empty($grade_level)) {
        $query .= " AND u.GLevel = ?";
        $params[] = $grade_level;
        $types .= "s";
    }

    $query .= " ORDER BY u.GLevel, u.Lname, u.Fname";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function markAttendance($student_id, $date, $status, $time_in = null, $time_out = null, $remarks = '', $recorded_by = null)
{
    global $conn;

    // Get attendance settings
    $settings = getAttendanceSettings();

    // Auto-determine if late based on time_in
    if ($time_in && $status == 'present') {
        $school_start = $settings['school_start_time'];
        $late_threshold = $settings['late_threshold_minutes'];

        $start_time = new DateTime($school_start);
        $check_time = new DateTime($time_in);
        $late_time = clone $start_time;
        $late_time->add(new DateInterval('PT' . $late_threshold . 'M'));

        if ($check_time > $late_time) {
            $status = 'late';
        }
    }

    // Calculate hours worked if both time_in and time_out are provided
    $hours_worked = null;
    if ($time_in && $time_out) {
        $hours_worked = calculateHoursWorked($time_in, $time_out, $settings['break_duration_minutes']);
    }

    $query = "INSERT INTO attendance (student_id, date, status, time_in, time_out, hours_worked, remarks, recorded_by)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE
              status = VALUES(status),
              time_in = VALUES(time_in),
              time_out = VALUES(time_out),
              hours_worked = VALUES(hours_worked),
              remarks = VALUES(remarks),
              recorded_by = VALUES(recorded_by),
              updated_at = CURRENT_TIMESTAMP";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssisi", $student_id, $date, $status, $time_in, $time_out, $hours_worked, $remarks, $recorded_by);

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
    $query = "SELECT DISTINCT GLevel FROM users WHERE user_type = 'student' ORDER BY GLevel";
    $result = $conn->query($query);
    $levels = [];
    while ($row = $result->fetch_assoc()) {
        $levels[] = $row['GLevel'];
    }
    return $levels;
}

// Additional helper functions for enhanced functionality

function updateAttendanceSettings($settings)
{
    global $conn;
    
    $query = "UPDATE attendance_settings SET 
              school_start_time = ?, 
              school_end_time = ?, 
              late_threshold_minutes = ?, 
              half_day_hours = ?, 
              full_day_hours = ?, 
              break_duration_minutes = ?
              WHERE active = 1";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiddi", 
        $settings['school_start_time'],
        $settings['school_end_time'],
        $settings['late_threshold_minutes'],
        $settings['half_day_hours'],
        $settings['full_day_hours'],
        $settings['break_duration_minutes']
    );
    
    return $stmt->execute();
}

function getAttendanceReport($start_date, $end_date, $grade_level = '')
{
    global $conn;
    
    $query = "SELECT u.id, u.Fname, u.Lname, u.Mname, u.GLevel,
                     COUNT(a.id) as total_days,
                     SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                     SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
                     SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                     SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused_days,
                     AVG(CASE WHEN a.hours_worked IS NOT NULL THEN a.hours_worked ELSE 0 END) as avg_hours
              FROM users u
              LEFT JOIN attendance a ON u.id = a.student_id AND a.date BETWEEN ? AND ?
              WHERE u.user_type = 'student'";
              
    $params = [$start_date, $end_date];
    $types = "ss";
    
    if (!empty($grade_level)) {
        $query .= " AND u.GLevel = ?";
        $params[] = $grade_level;
        $types .= "s";
    }
    
    $query .= " GROUP BY u.id ORDER BY u.GLevel, u.Lname, u.Fname";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function markAllPresent($date, $grade_level = '', $recorded_by = null)
{
    global $conn;
    
    $query = "SELECT id FROM users WHERE user_type = 'student'";
    $params = [];
    $types = "";
    
    if (!empty($grade_level)) {
        $query .= " AND GLevel = ?";
        $params[] = $grade_level;
        $types .= "s";
    }
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $attendanceData = [];
    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = [
            'student_id' => $row['id'],
            'status' => 'present',
            'time_in' => date('H:i'),
            'time_out' => null,
            'remarks' => 'Bulk marked present'
        ];
    }
    
    return bulkMarkAttendance($attendanceData, $date, $recorded_by);
}

function markAllAbsent($date, $grade_level = '', $recorded_by = null)
{
    global $conn;
    
    $query = "SELECT id FROM users WHERE user_type = 'student'";
    $params = [];
    $types = "";
    
    if (!empty($grade_level)) {
        $query .= " AND GLevel = ?";
        $params[] = $grade_level;
        $types .= "s";
    }
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $attendanceData = [];
    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = [
            'student_id' => $row['id'],
            'status' => 'absent',
            'time_in' => null,
            'time_out' => null,
            'remarks' => 'Bulk marked absent'
        ];
    }
    
    return bulkMarkAttendance($attendanceData, $date, $recorded_by);
}
?>