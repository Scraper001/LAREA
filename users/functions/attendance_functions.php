<?php
function getAttendanceSettings()
{
    global $conn;
    $query = "SELECT * FROM attendance_settings ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

function getAttendanceStats($date = null)
{
    global $conn;

    if (!$date) {
        $date = date('Y-m-d');
    }

    $query = "SELECT 
                COUNT(*) as total_students,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused
              FROM attendance a
              RIGHT JOIN users u ON a.student_id = u.id
              WHERE u.user_type = 'student' AND (a.date = ? OR a.date IS NULL)";

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

    $query = "SELECT u.id, u.Fname, u.Lname, u.Mname, u.GLevel, u.photo_path,
                     a.status, a.time_in, a.time_out, a.remarks
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

    $query = "INSERT INTO attendance (student_id, date, status, time_in, time_out, remarks, recorded_by)
              VALUES (?, ?, ?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE
              status = VALUES(status),
              time_in = VALUES(time_in),
              time_out = VALUES(time_out),
              remarks = VALUES(remarks),
              recorded_by = VALUES(recorded_by),
              updated_at = CURRENT_TIMESTAMP";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssi", $student_id, $date, $status, $time_in, $time_out, $remarks, $recorded_by);

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
?>